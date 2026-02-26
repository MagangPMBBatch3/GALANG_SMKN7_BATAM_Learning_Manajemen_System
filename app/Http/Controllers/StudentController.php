<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\StreamedResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Enrollment;
use App\Models\Notification;
use App\Models\Badge;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\LessonProgress;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Certificate;
use App\Models\UserBadge;
use App\Models\Quiz;
use App\Models\QuizSubmission;
use App\Models\Question;
use App\Models\Choice;

class StudentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Get student dashboard view
     */
    public function dashboard()
    {
        return view('dashboard.student');
    }

    /**
     * Get course exploration view
     */
    public function exploreCourses()
    {
        return view('student.courses.index');
    }

    /**
     * Get student's enrolled courses
     */
    public function getEnrollments(Request $request)
    {
        $userId = Auth::id();

        $enrollments = Enrollment::where('user_id', $userId)
            ->with(['course' => function ($q) {
                $q->with(['modules', 'lessons'])->withCount('modules', 'lessons');
            }])
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($enrollment) use ($userId) {
                // Self-healing: If progress is 100% but no certificate, generate it
                try {
                    if (($enrollment->progress_percent >= 100 || $enrollment->status === 'completed') && 
                        !Certificate::where('enrollment_id', $enrollment->id)->exists()) {
                        $this->generateRewards($userId, $enrollment->course_id, $enrollment);
                    }
                } catch (\Exception $e) {
                    // Ignore self-healing errors to prevent blocking dashboard
                    Log::error("Self-healing failed for enrollment {$enrollment->id}: " . $e->getMessage());
                }

                // Get certificate ID if exists
                $certificateId = null;
                if ($enrollment->progress_percent >= 100 || $enrollment->status === 'completed') {
                    $certificateId = Certificate::where('enrollment_id', $enrollment->id)->value('id');
                }

                return [
                    'id' => $enrollment->id,
                    'status' => $enrollment->status,
                    'progress_percent' => $enrollment->progress_percent ?? 0,
                    'price_paid' => $enrollment->price_paid,
                    'currency' => $enrollment->currency,
                    'enrolled_at' => $enrollment->created_at,
                    'certificate_id' => $certificateId,
                    'course' => [
                        'id' => $enrollment->course->id,
                        'title' => $enrollment->course->title,
                        'slug' => $enrollment->course->slug,
                        'short_description' => $enrollment->course->short_description,
                        'thumbnail_url' => $enrollment->course->thumbnail_url ?? null,
                        'duration_minutes' => $enrollment->course->duration_minutes ?? null,
                        'modules_count' => $enrollment->course->modules_count,
                        'lessons_count' => $enrollment->course->lessons_count,
                    ],
                ];
            });

        return response()->json($enrollments);
    }

    /**
     * Helper to generate certificate and badge
     */
    private function generateRewards($userId, $courseId, $enrollment)
    {
        try {
            // Ensure course exists
            $course = $enrollment->course ?? Course::find($courseId);
            if (!$course) {
                Log::warning("Cannot generate rewards for missing course ID: $courseId");
                return null;
            }

            // 1. Generate Certificate if not exists
            Certificate::firstOrCreate(
                ['enrollment_id' => $enrollment->id],
                [
                    'user_id' => $userId,
                    'course_id' => $courseId,
                    'cert_number' => 'CERT-' . date('Ym') . '-' . strtoupper(Str::random(8)),
                    'issued_at' => now(),
                    'digital_signature' => 'SIG-' . hash('sha256', $userId . $courseId . now()),
                ]
            );

            // 2. Award Badge
            $badgeCode = 'COURSE_COMPLETED_' . $courseId;
            
            $badge = Badge::firstOrCreate(
                ['code' => $badgeCode],
                [
                    'name' => 'Lulus: ' . Str::limit($course->title, 20),
                    'description' => 'Penghargaan atas penyelesaian kursus ' . $course->title,
                    'icon_url' => null, 
                ]
            );

            if (!UserBadge::where('user_id', $userId)->where('badge_id', $badge->id)->exists()) {
                UserBadge::create([
                    'user_id' => $userId,
                    'badge_id' => $badge->id,
                    'awarded_at' => now(),
                ]);
                return $badge;
            }
        } catch (\Exception $e) {
            Log::error("Error generating rewards: " . $e->getMessage());
        }
        return null;
    }

    /**
     * Get student's certificates (API)
     */
    public function getCertificates(Request $request)
    {
        $userId = Auth::id();
        $certificates = Certificate::where('user_id', $userId)
            ->with(['course' => function($q) {
                $q->select('id', 'title', 'slug', 'thumbnail_url', 'instructor_id');
                $q->with('instructor:id,name');
            }])
            ->orderByDesc('issued_at')
            ->get()
            ->map(function($cert) {
                return [
                    'id' => $cert->id,
                    'certificate_number' => $cert->cert_number,
                    'issued_at' => $cert->issued_at,
                    'course' => [
                        'id' => $cert->course->id,
                        'title' => $cert->course->title,
                        'slug' => $cert->course->slug,
                        'thumbnail_url' => $cert->course->thumbnail_url,
                        'instructor' => [
                            'name' => $cert->course->instructor->name ?? 'Instructor'
                        ]
                    ]
                ];
            });
            
        return response()->json(['userCertificates' => $certificates]);
    }

    /**
     * Get student's earned badges
     */
    public function getBadges(Request $request)
    {
        $userId = Auth::id();

        // Return only badges that belong to this user via user_badges pivot table
        $badges = Badge::whereHas('users', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->get()
            ->map(function ($badge) {
                return [
                    'id' => $badge->id,
                    'code' => $badge->code,
                    'name' => $badge->name,
                    'description' => $badge->description,
                    'icon_url' => $badge->icon_url,
                ];
            });

        return response()->json($badges);
    }

    /**
     * Get student stats
     */
    public function getStats(Request $request)
    {
        $userId = Auth::id();

        $enrollments = Enrollment::where('user_id', $userId)->get();
        $totalEnrolled = $enrollments->count();
        $completed = $enrollments->where('status', 'completed')->count();
        $inProgress = $enrollments->where('status', 'active')->count();
        $averageProgress = $enrollments->avg('progress_percent') ?? 0;

        return response()->json([
            'total_enrolled' => $totalEnrolled,
            'completed' => $completed,
            'in_progress' => $inProgress,
            'average_progress' => round($averageProgress, 2),
        ]);
    }

    /**
     * Get all categories
     */
    public function getCategories(Request $request)
    {
        $categories = \App\Models\Category::orderBy('name')
            ->get(['id', 'name', 'description'])
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'description' => $category->description,
                ];
            });

        return response()->json($categories);
    }

    /**
     * Get courses for catalog
     */
    public function getCourses(Request $request)
    {
        $query = \App\Models\Course::with(['instructor', 'category'])
            ->withCount('enrollments', 'modules', 'lessons')
            ->where('is_published', true);

        // Search
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('short_description', 'LIKE', "%{$search}%");
            });
        }

        // Category filter
        if ($request->has('category_id') && $request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        // Sort
        $sort = $request->get('sort', 'newest');
        switch ($sort) {
            case 'popular':
                $query->orderByDesc('enrollments_count');
                break;
            case 'rating':
                $query->orderByDesc('rating');
                break;
            case 'newest':
            default:
                $query->orderByDesc('created_at');
        }

        $courses = $query->paginate(9);

        return response()->json($courses);
    }

    /**
     * Enroll student in course
     */
    public function enrollCourse(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
        ]);

        $userId = Auth::id();
        $courseId = $request->course_id;

        // Check if already enrolled
        $existing = Enrollment::where('user_id', $userId)
            ->where('course_id', $courseId)
            ->first();

        if ($existing) {
            return response()->json([
                'message' => 'Already enrolled in this course',
            ], 422);
        }

        $course = \App\Models\Course::find($courseId);

        // If course is paid, payment data is required (we accept simulated payment for demo)
        $payment = $request->input('payment');
        if (($course->price ?? 0) > 0 && !$payment) {
            return response()->json(['message' => 'Payment required'], 402);
        }

        $pricePaid = $course->price ?? 0;
        $currency = 'IDR';

        if ($payment && is_array($payment)) {
            $pricePaid = $payment['amount'] ?? $pricePaid;
            $currency = $payment['currency'] ?? $currency;
        }

        // Persist enrollment and payment (if any)
        DB::beginTransaction();
        try {
            $enrollment = Enrollment::create([
                'user_id' => $userId,
                'course_id' => $courseId,
                'status' => 'active',
                'progress_percent' => 0,
                'price_paid' => $pricePaid,
                'currency' => $currency,
            ]);

            if ($payment && is_array($payment)) {
                \App\Models\Payment::create([
                    'enrollment_id' => $enrollment->id,
                    'user_id' => $userId,
                    'course_id' => $courseId,
                    'amount' => $payment['amount'] ?? $pricePaid,
                    'currency' => $payment['currency'] ?? $currency,
                    'method' => $payment['method'] ?? 'fake_card',
                    'status' => 'paid',
                    'transaction_ref' => $payment['transaction_ref'] ?? null,
                    'payment_data' => $payment,
                    'paid_at' => now(),
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Successfully enrolled in course',
                'enrollment' => $enrollment,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to enroll: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Show course player view
     */
    public function showCourse($slug)
    {
        return view('student.courses.learn', ['slug' => $slug]);
    }

    /**
     * Get course data with modules and lessons
     */
    public function getCourseData(Request $request, $slug)
    {
        $userId = Auth::id();
        $course = Course::where('slug', $slug)->firstOrFail();

        // Check enrollment
        $enrollment = Enrollment::where('user_id', $userId)
            ->where('course_id', $course->id)
            ->first();

        if (!$enrollment) {
            return response()->json(['message' => 'Not enrolled in this course'], 403);
        }

        // Track activity - update enrollment timestamp
        $enrollment->touch();

        // Get modules with lessons
        $modules = $course->modules()
            ->with('lessons')
            ->orderBy('position')
            ->get()
            ->map(function ($module) {
                return [
                    'id' => $module->id,
                    'title' => $module->title,
                    'description' => $module->description,
                    'position' => $module->position,
                ];
            });

        $lessons = Lesson::whereHas('module', function ($q) use ($course) {
            $q->where('course_id', $course->id);
        })
            ->with('module')
            ->orderBy('position')
            ->get()
            ->map(function ($lesson) {
                // Format content URL properly
                $contentUrl = null;
                $useStreamingUrl = false;
                
                if ($lesson->media_url) {
                    // Check if it's an external URL (starts with http:// or https://)
                    if (preg_match('/^https?:\/\//', $lesson->media_url)) {
                        $contentUrl = $lesson->media_url;
                    } 
                    // For local files, use streaming URL to control headers
                    else {
                        // Use streaming route for proper header control
                        // Use relative URL to ensure it matches current domain (localhost vs 127.0.0.1)
                        $contentUrl = '/student/api/media/' . $lesson->id;
                        $useStreamingUrl = true;
                    }
                }

                // Auto-detect proper content type from extension
                if ($useStreamingUrl) {
                    // For streaming URLs, detect from media_url
                    $extension = strtolower(pathinfo($lesson->media_url, PATHINFO_EXTENSION));
                } else {
                    $pathOnly = parse_url($contentUrl, PHP_URL_PATH);
                    $extension = strtolower(pathinfo($pathOnly, PATHINFO_EXTENSION));
                }
                
                $derivedType = $lesson->content_type;
                
                if (in_array($extension, ['mp4', 'webm', 'ogg', 'mov', 'avi', 'mkv', 'wmv'])) {
                    $derivedType = 'video';
                } elseif ($extension === 'pdf') {
                    $derivedType = 'pdf';
                } elseif (in_array($extension, ['mp3', 'wav', 'ogg'])) {
                    $derivedType = 'audio';
                }

                return [
                    'id' => $lesson->id,
                    'title' => $lesson->title,
                    'module_id' => $lesson->module_id,
                    'content_type' => $derivedType,
                    'content_url' => $contentUrl,
                    'content' => $lesson->content,
                    'duration' => $lesson->duration_seconds,
                    'position' => $lesson->position,
                    'is_downloadable' => $lesson->is_downloadable ?? false,
                ];
            });

        // Get completed lessons
        $completedLessons = LessonProgress::where('user_id', $userId)
            ->where('course_id', $course->id)
            ->where('is_completed', true)
            ->pluck('lesson_id')
            ->toArray();

        return response()->json([
            'course' => [
                'id' => $course->id,
                'title' => $course->title,
                'slug' => $course->slug,
            ],
            'enrollment' => [
                'id' => $enrollment->id,
                'status' => $enrollment->status,
                'progress_percent' => $enrollment->progress_percent ?? 0,
            ],
            'modules' => $modules,
            'lessons' => $lessons,
            'completed_lessons' => $completedLessons,
        ]);
    }

    /**
     * Mark lesson as complete
     */
    public function completeLesson(Request $request)
    {
        $request->validate([
            'lesson_id' => 'required|exists:lessons,id',
            'course_id' => 'required|exists:courses,id',
        ]);

        $userId = Auth::id();
        $lessonId = $request->lesson_id;
        $courseId = $request->course_id;

        // Verify enrollment
        $enrollment = Enrollment::where('user_id', $userId)
            ->where('course_id', $courseId)
            ->firstOrFail();

        // Create or update lesson progress
        LessonProgress::updateOrCreate(
            ['user_id' => $userId, 'lesson_id' => $lessonId],
            [
                'course_id' => $courseId,
                'is_completed' => true,
                'completed_at' => now(),
            ]
        );

        // Calculate progress
        $totalLessons = Lesson::whereHas('module', function ($q) use ($courseId) {
            $q->where('course_id', $courseId);
        })->count();

        $completedLessons = LessonProgress::where('user_id', $userId)
            ->where('course_id', $courseId)
            ->where('is_completed', true)
            ->count();

        $progressPercent = ($completedLessons / $totalLessons) * 100;

        // Update enrollment progress
        $enrollment->update(['progress_percent' => $progressPercent]);
        
        // Check if completed (>= 100%)
        $isCompleted = $progressPercent >= 100;
        if ($isCompleted) {
             $enrollment->update(['status' => 'completed']);
        }

        $certificateId = null;
        $earnedBadge = null;

        // Always generate rewards when course is completed
        // generateRewards handles duplicates internally (firstOrCreate / exists check)
        if ($isCompleted) {
            $earnedBadge = $this->generateRewards($userId, $courseId, $enrollment);
            $certificateId = Certificate::where('enrollment_id', $enrollment->id)->value('id');
        }

        return response()->json([
            'message' => 'Lesson completed successfully',
            'progress_percent' => round($progressPercent, 2),
            'is_completed' => $isCompleted,
            'certificate_id' => $certificateId,
            'earned_badge' => $earnedBadge,
        ]);
    }

    /**
     * Stream media file (PDF, video, etc.) with proper headers for inline viewing
     */
    public function streamMedia($lessonId)
    {
        try {
            $userId = Auth::id();
            
            // Get lesson with course
            $lesson = Lesson::with('module.course')->findOrFail($lessonId);
            $course = $lesson->module->course;
            
            // Verify enrollment (but allow admins to access all)
            if (!Auth::user()->isAdmin()) {
                $enrollment = Enrollment::where('user_id', $userId)
                    ->where('course_id', $course->id)
                    ->first();
                    
                if (!$enrollment) {
                    abort(403, 'You are not enrolled in this course');
                }
            }
            
            // Get file path from media_url
            $mediaUrl = $lesson->media_url;
            
            if (empty($mediaUrl)) {
                abort(404, 'No media file associated with this lesson');
            }
            
            // Clean path - handle various formats
            $path = $mediaUrl;

            // Remove domain if present
            $path = preg_replace('/^https?:\/\/[^\/]+/', '', $path);

            // Remove leading slash if present
            $path = ltrim($path, '/');

            // Get full path - files are stored in public/storage via Laravel's storage:link
            $fullPath = public_path($path);
            
            // Check if file exists
            if (!file_exists($fullPath)) {
                Log::error("File not found: {$fullPath}");
                abort(404, 'File not found');
            }
            
            // Determine MIME type
            $extension = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
            $mimeTypes = [
                'pdf'  => 'application/pdf',
                'mp4'  => 'video/mp4',
                'webm' => 'video/webm',
                'ogg'  => 'video/ogg',
                'jpg'  => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'png'  => 'image/png',
                'gif'  => 'image/gif',
                'webp' => 'image/webp',
                'mp3'  => 'audio/mpeg',
                'wav'  => 'audio/wav',
            ];
            
            $mimeType = $mimeTypes[$extension] ?? mime_content_type($fullPath) ?? 'application/octet-stream';
            
            Log::info("Serving media file via response()->file()", [
                'lesson_id' => $lessonId,
                'path' => $fullPath,
                'mime' => $mimeType
            ]);

            // Use Laravel's file response helper which handles everything correctly
            return response()->file($fullPath, [
                'Content-Type' => $mimeType,
                'Cache-Control' => 'public, max-age=31536000',
            ]);
            
        } catch (\Exception $e) {
            Log::error("Error streaming media for lesson {$lessonId}: " . $e->getMessage());
            abort(404, 'Media file not accessible');
        }
    }


    /**
     * Get Quiz Data for Student
     */
    public function getQuizData($lessonId)
    {
        $lesson = Lesson::findOrFail($lessonId);
        if ($lesson->content_type !== 'quiz') {
            return response()->json(['error' => 'Not a quiz lesson'], 404);
        }

        $quiz = Quiz::where('lesson_id', $lessonId)->first();
        if (!$quiz) {
            return response()->json([
                'error' => 'Quiz not set up', 
                'message' => 'Quiz belum dikonfigurasi untuk lesson ini. Silakan hubungi instruktur.',
                'lesson_id' => $lessonId
            ], 404);
        }

        // Check attempts
        $userId = Auth::id();
        $attempts = QuizSubmission::where('quiz_id', $quiz->id)
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
            
        // Hide correct answers from choices
        $questions = $quiz->questions()->with(['choices' => function($q) {
            $q->select('id', 'question_id', 'text', 'position'); // Exclude is_correct
        }])->orderBy('position')->get();

        return response()->json([
            'quiz' => $quiz,
            'questions' => $questions,
            'attempts' => $attempts,
            'max_attempts' => $quiz->attempts_allowed,
            'time_limit' => $quiz->time_limit_seconds,
        ]);
    }

    /**
     * Submit Quiz
     */
    public function submitQuiz(Request $request, $quizId)
    {
        try {
            $userId = Auth::id();
            $quiz = Quiz::with('questions.choices')->findOrFail($quizId);

            $attemptsCount = QuizSubmission::where('quiz_id', $quizId)->where('user_id', $userId)->count();
            if ($quiz->attempts_allowed > 0 && $attemptsCount >= $quiz->attempts_allowed) {
                 return response()->json(['message' => 'Max attempts reached'], 403);
            }

            $answers = $request->input('answers', []);
            
            $score = 0;
            $totalPoints = 0;
            $essayPending = false;
            $submissionAnswers = [];

            foreach ($quiz->questions as $question) {
                $totalPoints += $question->points;
                $userAnswer = $answers[$question->id] ?? null;
                
                $isCorrect = false;
                $pointsEarned = 0;

                if ($question->type === 'essay') {
                    $essayPending = true;
                    // Essay points are 0 until graded
                } elseif ($question->type === 'mcq' || $question->type === 'truefalse') {
                    $correctChoice = $question->choices->where('is_correct', true)->first();
                    if ($correctChoice && $userAnswer == $correctChoice->id) {
                        $isCorrect = true;
                        $pointsEarned = $question->points;
                    }
                }

                $score += $pointsEarned;
                
                // Get display text for the answer
                $answerText = $userAnswer;
                if ($question->type === 'mcq' || $question->type === 'truefalse') {
                    // For MCQ/TrueFalse, try to get the choice text
                    $choice = $question->choices->firstWhere('id', $userAnswer);
                    if ($choice) {
                        $answerText = $choice->text;
                    }
                }
                
                $submissionAnswers[] = [
                    'question_id' => $question->id,
                    'type' => $question->type,
                    'answer' => $answerText,
                    'user_answer' => $userAnswer,
                    'is_correct' => $isCorrect,
                    'points_earned' => $pointsEarned,
                ];
            }

            // Calculate percentage
            $percentage = $totalPoints > 0 ? ($score / $totalPoints) * 100 : 0;
            $passed = !$essayPending && $percentage >= $quiz->passing_score;
           
            $startedAt = $request->input('started_at') ? \Carbon\Carbon::parse($request->input('started_at')) : now();

            // Determine status based on essay pending and pass/fail
            $status = 'passed';
            if ($essayPending) {
                $status = 'pending_review';
            } elseif (!$passed) {
                $status = 'failed';
            }

            $submission = QuizSubmission::create([
                'quiz_id' => $quiz->id,
                'user_id' => $userId,
                'started_at' => $startedAt,
                'finished_at' => now(),
                'score' => $score,
                'status' => $status,
                'answers' => $submissionAnswers, 
            ]);

            if ($passed) {
                 // Mark lesson complete
                 LessonProgress::updateOrCreate(
                    ['user_id' => $userId, 'lesson_id' => $quiz->lesson_id],
                    ['is_completed' => true, 'completed_at' => now()]
                );
            }

            // Create submission notification
            if ($essayPending) {
                // Essay pending review
                Notification::create([
                    'user_id' => $userId,
                    'type' => 'grading',
                    'payload' => [
                        'message' => "Kuis \"{$quiz->title}\" Anda telah disubmit. Jawaban essay sedang menunggu penilaian dari instruktur.",
                        'title' => "Kuis Disubmit: {$quiz->title}",
                        'quiz_id' => $quiz->id,
                        'status' => 'pending_review',
                    ],
                    'is_read' => false,
                    'sent_at' => now(),
                ]);
            } elseif ($passed) {
                // Quiz passed
                Notification::create([
                    'user_id' => $userId,
                    'type' => 'grading',
                    'payload' => [
                        'message' => "Selamat! Anda telah lulus kuis \"{$quiz->title}\" dengan skor {$score}/{$totalPoints} ({$percentage}%).",
                        'title' => "Kuis Lulus: {$quiz->title}",
                        'quiz_id' => $quiz->id,
                        'score' => $score,
                        'max_score' => $totalPoints,
                        'status' => 'passed',
                    ],
                    'is_read' => false,
                    'sent_at' => now(),
                ]);
            } else {
                // Quiz failed
                Notification::create([
                    'user_id' => $userId,
                    'type' => 'grading',
                    'payload' => [
                        'message' => "Sayang sekali, Anda belum lulus kuis \"{$quiz->title}\". Skor Anda: {$score}/{$totalPoints} ({$percentage}%). Silakan coba lagi.",
                        'title' => "Kuis Tidak Lulus: {$quiz->title}",
                        'quiz_id' => $quiz->id,
                        'score' => $score,
                        'max_score' => $totalPoints,
                        'status' => 'failed',
                    ],
                    'is_read' => false,
                    'sent_at' => now(),
                ]);
            }

            return response()->json([
                'passed' => $passed,
                'score' => $score,
                'percentage' => round($percentage, 1),
                'total_points' => $totalPoints,
                'review_needed' => $essayPending,
                'submission_id' => $submission->id
            ]);
        } catch (\Exception $e) {
            Log::error("Submit Quiz Error: " . $e->getMessage());
             // EMERGENCY DEBUGGING
            try {
                file_put_contents(public_path('debug_submit_quiz_error.txt'), date('Y-m-d H:i:s') . " - Error: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            } catch (\Exception $writeErr) {
                // Ignore write error
            }
            return response()->json([
                'error' => 'Gagal mengirim jawaban quiz', 
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function generateAndDownloadCertificate($enrollmentId)
    {
        $userId = Auth::id();
        $enrollment = Enrollment::where('id', $enrollmentId)
            ->where('user_id', $userId)
            ->firstOrFail();

        if ($enrollment->progress_percent < 100 && $enrollment->status !== 'completed') {
            abort(403, 'Course not completed');
        }

        $certificate = Certificate::firstOrCreate(
            ['enrollment_id' => $enrollment->id],
            [
                'user_id' => $userId,
                'course_id' => $enrollment->course_id,
                'cert_number' => 'CERT-' . date('Ym') . '-' . strtoupper(Str::random(8)),
                'issued_at' => now(),
                'digital_signature' => 'SIG-' . hash('sha256', $userId . $enrollment->course_id . now()),
            ]
        );

        // Ensure badge exists too (best effort)
        try {
            $badgeCode = 'COURSE_COMPLETED_' . $enrollment->course_id;
            $course = $enrollment->course ?? Course::find($enrollment->course_id);
            $badge = Badge::firstOrCreate(
                ['code' => $badgeCode],
                [
                    'name' => 'Lulus: ' . Str::limit($course->title, 20),
                    'description' => 'Penghargaan atas penyelesaian kursus ' . $course->title,
                    'icon_url' => null, 
                ]
            );
            
            if (!UserBadge::where('user_id', $userId)->where('badge_id', $badge->id)->exists()) {
                UserBadge::create([
                    'user_id' => $userId,
                    'badge_id' => $badge->id,
                    'awarded_at' => now(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error("Badge generation failed: " . $e->getMessage());
        }

        return redirect()->route('student.certificate.view', $certificate->id);
    }

    /**
     * View Certificate
     */
    public function viewCertificate(Request $request, $id)
    {
        $certificate = Certificate::with(['user', 'course'])->findOrFail($id);

        if (Auth::id() !== $certificate->user_id && !Auth::user()->isAdmin()) {
            abort(403);
        }

        return view('student.certificates.view', compact('certificate'));
    }

    /**
     * Student Notifications Page
     */
    public function notificationsPage()
    {
        return view('student.notifications');
    }

    /**
     * Get student's notifications with pagination
     */
    public function getNotifications(Request $request)
    {
        $userId = Auth::id();
        $perPage = $request->input('per_page', 15);

        if ($request->wantsJson()) {
            // API endpoint with pagination
            $notifications = Notification::where('user_id', $userId)
                ->orderByDesc('sent_at')
                ->paginate($perPage)
                ->through(function ($notification) {
                    return [
                        'id' => $notification->id,
                        'type' => $notification->type,
                        'payload' => $notification->payload ?? [],
                        'is_read' => (bool) $notification->is_read,
                        'sent_at' => $notification->sent_at,
                    ];
                });

            return response()->json([
                'data' => $notifications->items(),
                'pagination' => [
                    'current_page' => $notifications->currentPage(),
                    'per_page' => $notifications->perPage(),
                    'total' => $notifications->total(),
                    'last_page' => $notifications->lastPage(),
                ]
            ]);
        }

        // Non-JSON response (for dashboard sidebar)
        $notifications = Notification::where('user_id', $userId)
            ->orderByDesc('sent_at')
            ->limit(20)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'payload' => $notification->payload ?? [],
                    'is_read' => (bool) $notification->is_read,
                    'sent_at' => $notification->sent_at,
                ];
            });

        return response()->json($notifications);
    }

    /**
     * Toggle notification read status
     */
    public function toggleNotificationRead($notificationId, Request $request)
    {
        $userId = Auth::id();
        $notification = Notification::where('id', $notificationId)
            ->where('user_id', $userId)
            ->firstOrFail();

        $isRead = $request->input('is_read', !$notification->is_read);
        $notification->update(['is_read' => $isRead]);

        return response()->json([
            'message' => 'Notification status updated',
            'notification' => [
                'id' => $notification->id,
                'is_read' => (bool) $notification->is_read,
            ]
        ]);
    }

    /**
     * Delete a notification
     */
    public function deleteNotification($notificationId)
    {
        $userId = Auth::id();
        $notification = Notification::where('id', $notificationId)
            ->where('user_id', $userId)
            ->firstOrFail();

        $notification->delete();

        return response()->json([
            'message' => 'Notification deleted successfully'
        ]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllNotificationsAsRead()
    {
        $userId = Auth::id();
        Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'message' => 'All notifications marked as read'
        ]);
    }

    /**
     * Download Certificate (Redirects to view for printing)

     */
    public function downloadCertificate(Request $request, $id)
    {
        return redirect()->route('student.certificate.view', $id);
    }

    public function getQuizReview($submissionId)
    {
        $userId = Auth::id();
        $submission = QuizSubmission::where('id', $submissionId)
            ->where('user_id', $userId)
            ->with(['quiz.questions.choices'])
            ->firstOrFail();

        return response()->json([
            'submission' => $submission,
            'quiz' => $submission->quiz->load('questions.choices'), 
        ]);
    }
}
