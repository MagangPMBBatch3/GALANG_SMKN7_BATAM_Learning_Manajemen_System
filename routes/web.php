<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\LessonProgress;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test', function () {
    return view('test');
});

// TEMP DEBUG - remove after fixing
Route::get('/debug-payments', function () {
    $count  = \App\Models\Payment::count();
    $result = \App\Models\Payment::orderBy('created_at', 'desc')->paginate(10);
    return response()->json([
        'raw_count' => $count,
        'paginator_total' => $result->total(),
        'data_count' => $result->count(),
        'first_item' => $result->first(),
        'db_name' => \DB::connection()->getDatabaseName(),
    ]);
});


Auth::routes();

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\StudentController::class, 'dashboard'])->name('student.dashboard');
    Route::get('/student/dashboard', [App\Http\Controllers\StudentController::class, 'dashboard'])->name('student.dashboard.alt');

    // Student course pages
    Route::get('/explore-courses', [App\Http\Controllers\StudentController::class, 'exploreCourses'])->name('student.explore');
    Route::get('/learn/{slug}', [App\Http\Controllers\StudentController::class, 'showCourse'])->name('student.learn');
    Route::get('/my-courses', function () {
        return view('my-courses.index');
    });

    // Old routes (for legacy support)
    Route::get('/courses', function () {
        return redirect('/explore-courses');
    });

    Route::get('/courses/{slug}', function ($slug) {
        return view('courses.show', ['slug' => $slug]);
    });

    Route::get('/my-courses', function () {
        return view('my-courses.index');
    });

    Route::get('/profile', function () {
        $user = Auth::user();

        // Reject admin users - profile is only for students
        if ($user->hasRole('admin')) {
            abort(403, 'Admin users cannot access student profile');
        }

        // Load additional data for profile
        $user->load(['certificates.course', 'badges', 'enrollments']);

        // Calculate overall progress based on enrollments
        $enrollments = $user->enrollments;
        $totalProgress = 0;
        if ($enrollments->count() > 0) {
            $totalProgress = round($enrollments->avg('progress_percent'));
        }

        // Calculate day streak based on lesson completion
        $latestProgress = \App\Models\LessonProgress::where('user_id', $user->id)
            ->orderBy('completed_at', 'desc')
            ->first();

        $dayStreak = 0;
        if ($latestProgress) {
            $today = now()->startOfDay();
            $latestDay = $latestProgress->completed_at?->startOfDay();
            
            if ($latestDay) {
                $currentDate = $latestDay;
                $dayStreak = 1;

                // Count consecutive days backwards
                while (true) {
                    $previousDate = $currentDate->copy()->subDay();
                    $hasActivityOnDay = \App\Models\LessonProgress::where('user_id', $user->id)
                        ->whereDate('completed_at', $previousDate->toDateString())
                        ->exists();

                    if ($hasActivityOnDay) {
                        $dayStreak++;
                        $currentDate = $previousDate;
                    } else {
                        break;
                    }
                }
            }
        }

        // Calculate total hours studied
        $totalMinutes = \App\Models\LessonProgress::where('user_id', $user->id)
            ->where('is_completed', true)
            ->join('lessons', 'lesson_progress.lesson_id', '=', 'lessons.id')
            ->sum(\DB::raw('lessons.duration_seconds / 60'));

        $totalHours = round($totalMinutes / 60, 1);

        return view('profile.index', [
            'user' => $user,
            'overallProgress' => $totalProgress,
            'studyStreak' => $dayStreak,
            'totalHours' => $totalHours,
        ]);
    })->middleware('auth');

    Route::post('/profile/upload-photo', [App\Http\Controllers\ProfileController::class, 'uploadPhoto'])->middleware('auth');
    Route::delete('/profile/remove-photo', [App\Http\Controllers\ProfileController::class, 'removePhoto'])->middleware('auth');
    Route::put('/profile/update-bio', [App\Http\Controllers\ProfileController::class, 'updateBio'])->middleware('auth');
    Route::put('/profile/update-goals', [App\Http\Controllers\ProfileController::class, 'updateGoals'])->middleware('auth');
    Route::put('/profile/update-skills', [App\Http\Controllers\ProfileController::class, 'updateSkills'])->middleware('auth');
    Route::get('/profile/recent-activities', [App\Http\Controllers\ProfileController::class, 'getRecentActivities'])->middleware('auth');
    
    Route::get('/api/admin/stats', [App\Http\Controllers\ProfileController::class, 'getAdminStats'])->middleware('auth');
    Route::get('/api/admin/actions', [App\Http\Controllers\ProfileController::class, 'getAdminActions'])->middleware('auth');

    Route::get('/lessons', function () {
        return view('lessons.show');
    });

    Route::get('/quizzes', function () {
        return view('quizzes.show');
    });

    Route::get('/forums', [App\Http\Controllers\ForumController::class, 'index'])->name('forums.index');
    Route::get('/forums/{id}', function ($id) {
        return view('forums.show', ['id' => $id]);
    });

    // Forum API routes
    Route::prefix('forum/api')->group(function () {
        Route::get('/threads', [App\Http\Controllers\ForumController::class, 'getThreads']);
        Route::get('/stats', [App\Http\Controllers\ForumController::class, 'getStats']);
        Route::get('/courses', [App\Http\Controllers\ForumController::class, 'getCourses']);
        Route::post('/threads', [App\Http\Controllers\ForumController::class, 'storeThread'])->middleware('auth');
        Route::get('/threads/{id}', [App\Http\Controllers\ForumController::class, 'getThread']);
        Route::post('/threads/{id}/posts', [App\Http\Controllers\ForumController::class, 'storePost'])->middleware('auth');
        Route::post('/threads/{id}/like', [App\Http\Controllers\ForumController::class, 'likeThread'])->middleware('auth');
        Route::post('/posts/{id}/like', [App\Http\Controllers\ForumController::class, 'likePost'])->middleware('auth');
        Route::delete('/posts/{id}', [App\Http\Controllers\ForumController::class, 'deletePost'])->middleware('auth');
    });

    Route::get('/certificates', function () {
        return view('certificates.show');
    });

    Route::get('/notifications', [App\Http\Controllers\StudentController::class, 'notificationsPage'])->name('student.notifications');

    // Student API routes
    Route::prefix('student/api')->group(function () {
        Route::get('/enrollments', [App\Http\Controllers\StudentController::class, 'getEnrollments']);
        Route::get('/notifications', [App\Http\Controllers\StudentController::class, 'getNotifications']);
        Route::patch('/notifications/{notificationId}/toggle-read', [App\Http\Controllers\StudentController::class, 'toggleNotificationRead']);
        Route::delete('/notifications/{notificationId}', [App\Http\Controllers\StudentController::class, 'deleteNotification']);
        Route::patch('/notifications/mark-all-read', [App\Http\Controllers\StudentController::class, 'markAllNotificationsAsRead']);
        Route::get('/badges', [App\Http\Controllers\StudentController::class, 'getBadges']);
        Route::get('/certificates', [App\Http\Controllers\StudentController::class, 'getCertificates'])->name('api.certificates');
        Route::get('/stats', [App\Http\Controllers\StudentController::class, 'getStats']);
        Route::get('/categories', [App\Http\Controllers\StudentController::class, 'getCategories']);
        Route::get('/courses', [App\Http\Controllers\StudentController::class, 'getCourses']);
        Route::post('/enroll', [App\Http\Controllers\StudentController::class, 'enrollCourse']);
        Route::get('/course/{slug}', [App\Http\Controllers\StudentController::class, 'getCourseData']);
        Route::post('/lesson/complete', [App\Http\Controllers\StudentController::class, 'completeLesson']);
        Route::get('/media/{lessonId}', [App\Http\Controllers\StudentController::class, 'streamMedia']);
        Route::get('/certificate/generate/{enrollmentId}', [App\Http\Controllers\StudentController::class, 'generateAndDownloadCertificate'])->name('student.certificate.generate');
        Route::get('/certificate/{id}', [App\Http\Controllers\StudentController::class, 'viewCertificate'])->name('student.certificate.view');
        Route::get('/certificate/{id}/download', [App\Http\Controllers\StudentController::class, 'downloadCertificate'])->name('student.certificate.download');
        
        // Quiz Routes
        Route::get('/quiz/{lessonId}', [App\Http\Controllers\StudentController::class, 'getQuizData']);
        Route::post('/quiz/{quizId}/submit', [App\Http\Controllers\StudentController::class, 'submitQuiz']);
        Route::get('/quiz/review/{submissionId}', [App\Http\Controllers\StudentController::class, 'getQuizReview']);
    });

    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::get('/', [App\Http\Controllers\AdminController::class, 'index'])->name('admin.dashboard');
        Route::get('/users', [App\Http\Controllers\AdminController::class, 'users'])->name('admin.users');
        Route::get('/courses', [App\Http\Controllers\AdminController::class, 'courses'])->name('admin.courses');
        Route::get('/categories', [App\Http\Controllers\AdminController::class, 'categories'])->name('admin.categories');
        Route::get('/enrollments', [App\Http\Controllers\AdminController::class, 'enrollments'])->name('admin.enrollments');
        Route::get('/payments', [App\Http\Controllers\AdminController::class, 'payments'])->name('admin.payments');
        Route::get('/badges', [App\Http\Controllers\AdminController::class, 'badges'])->name('admin.badges');
        Route::get('/notifications', [App\Http\Controllers\AdminController::class, 'notifications'])->name('admin.notifications');
        Route::get('/progress', [App\Http\Controllers\AdminController::class, 'progress'])->name('admin.progress');
        Route::get('/grading', [App\Http\Controllers\AdminController::class, 'grading'])->middleware('instructor_or_admin')->name('admin.grading');
        Route::get('/quizzes', [App\Http\Controllers\AdminController::class, 'quizzes'])->name('admin.quizzes');
        Route::get('/forums', [App\Http\Controllers\AdminController::class, 'forums'])->name('admin.forums');

        // Admin API routes
        Route::prefix('api')->group(function () {

            // TEMP DEBUG ROUTE - remove after fixing
            Route::get('/debug-auth-payments', function () {
                return response()->json([
                    'user_id'        => Auth::id(),
                    'user_name'      => Auth::user()?->name,
                    'is_admin'       => Auth::user()?->isAdmin(),
                    'payment_count'  => \App\Models\Payment::count(),
                    'payment_data'   => \App\Models\Payment::select('id','status','amount')->limit(3)->get(),
                    'enrollment_count' => \App\Models\Enrollment::count(),
                ]);
            });

            Route::prefix('admin')->group(function () {
                Route::get('/users', [App\Http\Controllers\AdminController::class, 'getUsers']);
                Route::post('/users', [App\Http\Controllers\AdminController::class, 'createUser']);
                Route::put('/users/{user}', [App\Http\Controllers\AdminController::class, 'updateUser']);
                Route::patch('/users/{user}/status', [App\Http\Controllers\AdminController::class, 'toggleUserStatus']);
                Route::delete('/users/{user}', [App\Http\Controllers\AdminController::class, 'deleteUser']);
                Route::get('/roles', [App\Http\Controllers\AdminController::class, 'getRoles']);
                Route::get('/stats', [App\Http\Controllers\AdminController::class, 'getStats']);
            });
            
            // Courses API
            Route::get('/courses', [App\Http\Controllers\AdminController::class, 'getCourses']);
            Route::post('/courses', [App\Http\Controllers\AdminController::class, 'createCourse']);
            Route::put('/courses/{id}', [App\Http\Controllers\AdminController::class, 'updateCourse']);
            Route::delete('/courses/{id}', [App\Http\Controllers\AdminController::class, 'deleteCourse']);
            Route::post('/courses/{id}/thumbnail', [App\Http\Controllers\AdminController::class, 'uploadCourseThumbnail']);
            Route::delete('/courses/{id}/thumbnail', [App\Http\Controllers\AdminController::class, 'deleteCourseThumbnail']);

            // Modules API
            Route::get('/courses/{courseId}/modules', [App\Http\Controllers\AdminController::class, 'getModules']);
            Route::post('/modules', [App\Http\Controllers\AdminController::class, 'createModule']);
            Route::put('/modules/{id}', [App\Http\Controllers\AdminController::class, 'updateModule']);
            Route::delete('/modules/{id}', [App\Http\Controllers\AdminController::class, 'deleteModule']);

            // Lessons API
            Route::get('/modules/{moduleId}/lessons', [App\Http\Controllers\AdminController::class, 'getLessons']);
            Route::post('/lessons', [App\Http\Controllers\AdminController::class, 'createLesson']);
            Route::put('/lessons/{id}', [App\Http\Controllers\AdminController::class, 'updateLesson']);
            Route::delete('/lessons/{id}', [App\Http\Controllers\AdminController::class, 'deleteLesson']);

            // Categories API
            Route::get('/categories', [App\Http\Controllers\AdminController::class, 'getCategories']);
            Route::post('/categories', [App\Http\Controllers\AdminController::class, 'createCategory']);
            Route::put('/categories/{id}', [App\Http\Controllers\AdminController::class, 'updateCategory']);
            Route::delete('/categories/{id}', [App\Http\Controllers\AdminController::class, 'deleteCategory']);

            // Enrollments API
            Route::get('/enrollments', [App\Http\Controllers\AdminController::class, 'getEnrollments']);
            Route::put('/enrollments/{id}', [App\Http\Controllers\AdminController::class, 'updateEnrollment']);
            Route::delete('/enrollments/{id}', [App\Http\Controllers\AdminController::class, 'deleteEnrollment']);
            Route::get('/enrollments/stats', [App\Http\Controllers\AdminController::class, 'getEnrollmentStats']);

            // Payments API
            Route::get('/payments', [App\Http\Controllers\AdminController::class, 'getPayments']);
            Route::get('/payments/{id}', [App\Http\Controllers\AdminController::class, 'getPayment']);
            Route::delete('/payments/{id}', [App\Http\Controllers\AdminController::class, 'deletePayment']);

            // Badges API
            Route::get('/badges', [App\Http\Controllers\AdminController::class, 'getBadges']);
            Route::post('/badges', [App\Http\Controllers\AdminController::class, 'createBadge']);
            Route::put('/badges/{id}', [App\Http\Controllers\AdminController::class, 'updateBadge']);
            Route::delete('/badges/{id}', [App\Http\Controllers\AdminController::class, 'deleteBadge']);

            // Notifications API
            Route::get('/notifications', [App\Http\Controllers\AdminController::class, 'getNotifications']);
            Route::post('/notifications', [App\Http\Controllers\AdminController::class, 'sendNotification']);
            Route::delete('/notifications/{id}', [App\Http\Controllers\AdminController::class, 'deleteNotification']);

            // Progress API
            Route::post('/progress/lesson/complete', [App\Http\Controllers\AdminController::class, 'markLessonComplete']);
            
            // Quiz Management API
            Route::get('/quiz/{lessonId}', [App\Http\Controllers\AdminController::class, 'getQuiz']);
            Route::post('/quiz/{lessonId}', [App\Http\Controllers\AdminController::class, 'updateQuiz']);

            // Grading API
            Route::middleware('instructor_or_admin')->group(function () {
                Route::get('/grading/pending', [App\Http\Controllers\AdminController::class, 'getPendingSubmissions']);
                Route::get('/grading/{id}', [App\Http\Controllers\AdminController::class, 'getSubmission']);
                Route::post('/grading/{id}/grade', [App\Http\Controllers\AdminController::class, 'gradeSubmission']);
            });

            // Quiz List API
            Route::get('/quizzes', [App\Http\Controllers\AdminController::class, 'getQuizzes']);

            // Forums API
            Route::get('/forums/threads', [App\Http\Controllers\AdminController::class, 'getForumThreads']);
            Route::patch('/forums/threads/{id}/status', [App\Http\Controllers\AdminController::class, 'updateThreadStatus']);
            Route::delete('/forums/threads/{id}', [App\Http\Controllers\AdminController::class, 'deleteThread']);
        });
    });
});

