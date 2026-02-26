<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\AuditLog;

class ProfileController extends Controller
{
    public function getAdminStats()
    {
        try {
            /** @var \App\Models\User|null $user */
            $user = Auth::user();
            
            // Check if user is authenticated
            if (!$user) {
                return response()->json(['error' => 'Unauthorized - No user'], 401);
            }
            
            // Check if user is admin using hasRole method
            if (!$user->hasRole('admin')) {
                return response()->json(['error' => 'Unauthorized - Not admin'], 403);
            }

            $stats = [
                'totalUsers' => User::count(),
                'totalCourses' => Course::count(),
                'activeEnrollments' => Enrollment::where('status', 'active')->count(),
                'systemHealth' => 98 // Default health value
            ];

            return response()->json($stats);
        } catch (\Exception $e) {
            Log::error('Error in getAdminStats: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch stats: ' . $e->getMessage()], 500);
        }
    }

    public function getAdminActions()
    {
        try {
            /** @var \App\Models\User|null $user */
            $user = Auth::user();
            
            // Check if user is authenticated
            if (!$user) {
                return response()->json(['error' => 'Unauthorized - No user'], 401);
            }
            
            // Check if user is admin using hasRole method
            if (!$user->hasRole('admin')) {
                return response()->json(['error' => 'Unauthorized - Not admin'], 403);
            }

            $actions = AuditLog::latest()
                ->limit(10)
                ->get()
                ->map(function ($log) {
                    return [
                        'id' => $log->id,
                        'description' => $log->description ?? 'No description',
                        'timestamp' => $log->created_at->diffForHumans(),
                        'statusColor' => 'bg-blue-500'
                    ];
                });

            return response()->json($actions);
        } catch (\Exception $e) {
            Log::error('Error in getAdminActions: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch actions: ' . $e->getMessage()], 500);
        }
    }

    public function uploadPhoto(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // 2MB max
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid file. Please upload a valid image (JPEG, PNG, JPG, GIF) under 2MB.'
            ], 422);
        }

        try {
            /** @var \App\Models\User|null $user */
            $user = Auth::user();

            // Delete old avatar if exists
            if ($user->avatar_url && Storage::disk('public')->exists($user->avatar_url)) {
                Storage::disk('public')->delete($user->avatar_url);
            }

            // Store new photo
            $file = $request->file('photo');
            $filename = 'avatars/' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('avatars', $user->id . '_' . time() . '.' . $file->getClientOriginalExtension(), 'public');

            // Update user avatar_url
            $user->update(['avatar_url' => $path]);

            return response()->json([
                'success' => true,
                'message' => 'Profile photo updated successfully!',
                'avatar_url' => asset('storage/' . $path)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload photo. Please try again.'
            ], 500);
        }
    }

    public function removePhoto(Request $request)
    {
        try {
            /** @var \App\Models\User|null $user */
            $user = Auth::user();

            // Delete avatar file if exists
            if ($user->avatar_url && Storage::disk('public')->exists($user->avatar_url)) {
                Storage::disk('public')->delete($user->avatar_url);
            }

            // Remove avatar_url from user
            $user->update(['avatar_url' => null]);

            return response()->json([
                'success' => true,
                'message' => 'Profile photo removed successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove photo. Please try again.'
            ], 500);
        }
    }

    public function getStudentData()
    {
        try {
            /** @var \App\Models\User|null $user */
            $user = Auth::user();

            // Check if user is authenticated
            if (!$user) {
                return response()->json(['error' => 'Unauthorized - No user'], 401);
            }

            // Reject admin users
            if ($user->hasRole('admin')) {
                return response()->json(['error' => 'Unauthorized - Admin cannot access student profile'], 403);
            }

            // Load student-related data
            $user->load([
                'enrollments.course',
                'certificates.course',
                'badges',
                'points'
            ]);

            // Calculate statistics
            $totalEnrollments = $user->enrollments->count();
            $completedCourses = $user->enrollments->where('status', 'completed')->count();
            $totalCertificates = $user->certificates->count();
            $totalPoints = $user->points->sum('amount') ?? 0;

            // Calculate overall progress
            $overallProgress = $totalEnrollments > 0
                ? round(($completedCourses / $totalEnrollments) * 100)
                : 0;

            $studentData = [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'avatar_url' => $user->avatar_url,
                    'bio' => $user->bio,
                    'location' => $user->location ?? null,
                    'created_at' => $user->created_at,
                    'goals' => $user->goals ?? [],
                    'skills' => $user->skills ?? []
                ],
                'stats' => [
                    'coursesEnrolled' => $totalEnrollments,
                    'coursesCompleted' => $completedCourses,
                    'certificates' => $totalCertificates,
                    'points' => $totalPoints,
                    'overallProgress' => $overallProgress,
                    'studyStreak' => 0,
                    'totalHours' => 0
                ],
                'enrollments' => $user->enrollments->map(function ($enrollment) {
                    return [
                        'id' => $enrollment->id,
                        'course_id' => $enrollment->course_id,
                        'course_title' => $enrollment->course->title ?? null,
                        'status' => $enrollment->status,
                        'progress_percent' => $enrollment->progress_percent ?? 0,
                        'enrolled_at' => $enrollment->enrolled_at,
                        'expires_at' => $enrollment->expires_at
                    ];
                }),
                'certificates' => $user->certificates->map(function ($cert) {
                    return [
                        'id' => $cert->id,
                        'course_title' => $cert->course->title ?? null,
                        'issued_at' => $cert->issued_at,
                        'certificate_number' => $cert->certificate_number ?? null
                    ];
                }),
                'badges' => $user->badges->map(function ($badge) {
                    return [
                        'id' => $badge->id,
                        'name' => $badge->name,
                        'description' => $badge->description,
                        'icon_url' => $badge->icon_url
                    ];
                })
            ];

            return response()->json($studentData);
        } catch (\Exception $e) {
            Log::error('Error in getStudentData: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch student data: ' . $e->getMessage()], 500);
        }
    }

    public function updateBio(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'bio' => 'nullable|string|max:1000',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 422);
            }

            /** @var \App\Models\User|null $user */
            $user = Auth::user();

            if (!$user || $user->hasRole('admin')) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $user->update(['bio' => $request->input('bio')]);

            return response()->json([
                'success' => true,
                'message' => 'Bio updated successfully!',
                'bio' => $user->bio
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating bio: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update bio'
            ], 500);
        }
    }

    public function updateGoals(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'goals' => 'nullable|array',
                'goals.*' => 'string|max:100',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 422);
            }

            /** @var \App\Models\User|null $user */
            $user = Auth::user();

            if (!$user || $user->hasRole('admin')) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $goals = $request->input('goals');
            // Filter out empty goals
            $goals = array_filter($goals, function($goal) {
                return !empty(trim($goal));
            });
            // Reset array keys
            $goals = array_values($goals);

            $user->update(['goals' => $goals ?? []]);

            return response()->json([
                'success' => true,
                'message' => 'Learning goals updated successfully!',
                'goals' => $user->goals ?? []
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating goals: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update goals'
            ], 500);
        }
    }

    public function updateSkills(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'skills' => 'nullable|array',
                'skills.*' => 'string|max:50',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 422);
            }

            /** @var \App\Models\User|null $user */
            $user = Auth::user();

            if (!$user || $user->hasRole('admin')) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $skills = $request->input('skills');
            // Filter out empty skills
            $skills = array_filter($skills, function($skill) {
                return !empty(trim($skill));
            });
            // Reset array keys
            $skills = array_values($skills);

            $user->update(['skills' => $skills ?? []]);

            return response()->json([
                'success' => true,
                'message' => 'Skills updated successfully!',
                'skills' => $user->skills ?? []
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating skills: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update skills'
            ], 500);
        }
    }

    public function getRecentActivities()
    {
        try {
            /** @var \App\Models\User|null $user */
            $user = Auth::user();

            if (!$user || $user->hasRole('admin')) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            // Fetch recent enrollment activities
            $activities = [];

            // Get recently accessed courses (learning activity tracked by updated_at)
            $activeEnrollments = $user->enrollments()
                ->with('course')
                ->orderBy('updated_at', 'desc')
                ->limit(10)
                ->get();

            foreach ($activeEnrollments as $enrollment) {
                if ($enrollment->course) {
                    $activities[] = [
                        'id' => 'learning-' . $enrollment->id,
                        'title' => 'Learning: ' . $enrollment->course->title,
                        'description' => 'Studying course',
                        'time' => $enrollment->updated_at->diffForHumans(),
                        'icon' => 'fas fa-book-open',
                        'iconBg' => 'bg-purple-500'
                    ];
                }
            }

            // Get recent course enrollments
            $enrollments = $user->enrollments()
                ->with('course')
                ->orderBy('enrolled_at', 'desc')
                ->limit(5)
                ->get();

            foreach ($enrollments as $enrollment) {
                if ($enrollment->course) {
                    $activities[] = [
                        'id' => 'enroll-' . $enrollment->id,
                        'title' => 'Enrolled in course: ' . $enrollment->course->title,
                        'description' => 'Started learning',
                        'time' => $enrollment->enrolled_at->diffForHumans(),
                        'icon' => 'fas fa-graduation-cap',
                        'iconBg' => 'bg-blue-500'
                    ];
                }
            }

            // Get recently earned certificates
            $certificates = $user->certificates()
                ->with('course')
                ->orderBy('issued_at', 'desc')
                ->limit(5)
                ->get();

            foreach ($certificates as $cert) {
                if ($cert->course) {
                    $activities[] = [
                        'id' => 'cert-' . $cert->id,
                        'title' => 'Completed course: ' . $cert->course->title,
                        'description' => 'Earned a certificate',
                        'time' => $cert->issued_at->diffForHumans(),
                        'icon' => 'fas fa-certificate',
                        'iconBg' => 'bg-green-500'
                    ];
                }
            }

            // Get recently earned badges
            $badges = $user->badges()
                ->orderBy('user_badges.awarded_at', 'desc')
                ->limit(5)
                ->get();

            foreach ($badges as $badge) {
                $awardedAt = $badge->pivot && $badge->pivot->awarded_at ? $badge->pivot->awarded_at : null;
                if (!$awardedAt) {
                    continue;
                }
                $activities[] = [
                    'id' => 'badge-' . $badge->id,
                    'title' => 'Earned badge: ' . $badge->name,
                    'description' => $badge->description ?? '',
                    'time' => $awardedAt->diffForHumans(),
                    'icon' => 'fas fa-medal',
                    'iconBg' => 'bg-yellow-500'
                ];
            }

            // Sort by time string (simple sort, may not be perfect but works for display)
            // Sort in descending order - most recent first
            usort($activities, function($a, $b) {
                return 0; // Keep original order from database queries
            });

            return response()->json([
                'success' => true,
                'activities' => array_slice($activities, 0, 10) // Return top 10
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching activities: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch activities: ' . $e->getMessage()
            ], 500);
        }
    }
}
