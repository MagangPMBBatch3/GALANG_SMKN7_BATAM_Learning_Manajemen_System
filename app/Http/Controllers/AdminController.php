<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Role;

class AdminController extends Controller
{
    // === COURSE MANAGEMENT ===
    public function getCourses(Request $request)
    {
        $query = \App\Models\Course::with(['instructor', 'category'])
            ->withCount('enrollments');

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('short_description', 'LIKE', "%{$search}%")
                  ->orWhereHas('instructor', function($q) use ($search) {
                      $q->where('name', 'LIKE', "%{$search}%");
                  })
                  ->orWhereHas('category', function($q) use ($search) {
                      $q->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }

        if ($request->has('category_id') && $request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('status') && $request->status !== '') {
            $query->where('is_published', $request->status === 'published');
        }

        if ($request->has('instructor_id') && $request->instructor_id) {
            $query->where('instructor_id', $request->instructor_id);
        }

        $courses = $query->orderByDesc('created_at')->paginate(10);

        return response()->json($courses);
    }

    public function createCourse(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'short_description' => 'nullable|string|max:500',
            'full_description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'price' => 'nullable|numeric|min:0',
            'level' => 'nullable|string|in:beginner,intermediate,advanced',
            'duration_minutes' => 'nullable|integer|min:1',
            'is_published' => 'boolean',
        ]);

        $course = \App\Models\Course::create([
            'instructor_id' => Auth::id(),
            'title' => $request->title,
            'short_description' => $request->short_description,
            'full_description' => $request->full_description,
            'category_id' => $request->category_id,
            'price' => $request->price ?? 0,
            'level' => $request->level ?? 'beginner',
            'duration_minutes' => $request->duration_minutes ?? 0,
            'is_published' => $request->is_published ?? false,
            'slug' => \Illuminate\Support\Str::slug($request->title),
        ]);

        return response()->json(['message' => 'Course created successfully', 'course' => $course->load(['instructor', 'category'])], 201);
    }

    public function updateCourse(Request $request, $id)
    {
        $course = \App\Models\Course::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'short_description' => 'nullable|string|max:500',
            'full_description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'price' => 'nullable|numeric|min:0',
            'level' => 'nullable|string|in:beginner,intermediate,advanced',
            'duration_minutes' => 'nullable|integer|min:1',
            'is_published' => 'boolean',
        ]);

        $course->update([
            'title' => $request->title,
            'short_description' => $request->short_description,
            'full_description' => $request->full_description,
            'category_id' => $request->category_id,
            'price' => $request->price ?? $course->price,
            'level' => $request->level ?? $course->level,
            'duration_minutes' => $request->duration_minutes ?? $course->duration_minutes,
            'is_published' => $request->is_published ?? $course->is_published,
            'slug' => \Illuminate\Support\Str::slug($request->title),
        ]);

        return response()->json(['message' => 'Course updated successfully', 'course' => $course->load(['instructor', 'category'])]);
    }

    public function deleteCourse($id)
    {
        $course = \App\Models\Course::findOrFail($id);

        // Hapus semua modules dan lessons terkait
        $course->modules()->each(function($module) {
            $module->lessons()->delete();
            $module->delete();
        });

        $course->delete();

        return response()->json(['message' => 'Course deleted successfully']);
    }

    public function uploadCourseThumbnail(Request $request, $id)
    {
        $request->validate([
            'thumbnail' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB max
        ]);

        $course = \App\Models\Course::findOrFail($id);

        // Delete old thumbnail if exists
        if ($course->thumbnail_url && \Illuminate\Support\Facades\Storage::exists($course->thumbnail_url)) {
            \Illuminate\Support\Facades\Storage::delete($course->thumbnail_url);
        }

        // Store new thumbnail
        $path = $request->file('thumbnail')->store('courses/thumbnails', 'public');
        
        $course->update(['thumbnail_url' => $path]);

        return response()->json([
            'message' => 'Thumbnail uploaded successfully',
            'thumbnail_url' => \Illuminate\Support\Facades\Storage::url($path),
            'course' => $course
        ]);
    }

    public function deleteCourseThumbnail($id)
    {
        $course = \App\Models\Course::findOrFail($id);

        if ($course->thumbnail_url && \Illuminate\Support\Facades\Storage::exists($course->thumbnail_url)) {
            \Illuminate\Support\Facades\Storage::delete($course->thumbnail_url);
        }

        $course->update(['thumbnail_url' => null]);

        return response()->json(['message' => 'Thumbnail deleted successfully']);
    }

        // === MODULE MANAGEMENT ===
        public function getModules(Request $request, $courseId)
        {
            $course = \App\Models\Course::findOrFail($courseId);
            
            $modules = $course->modules()
                ->withCount('lessons')
                ->orderBy('position')
                ->get();

            return response()->json(['data' => $modules]);
        }

        public function createModule(Request $request)
        {
            $request->validate([
                'course_id' => 'required|exists:courses,id',
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'position' => 'nullable|integer',
            ]);

            // Get next position if not provided
            $position = $request->position;
            if (!$position) {
                $maxPosition = \App\Models\CourseModule::where('course_id', $request->course_id)
                    ->max('position');
                $position = ($maxPosition ?? 0) + 1;
            }

            $module = \App\Models\CourseModule::create([
                'course_id' => $request->course_id,
                'title' => $request->title,
                'description' => $request->description,
                'position' => $position,
            ]);

            return response()->json(['message' => 'Module created successfully', 'module' => $module->loadCount('lessons')], 201);
        }

        public function updateModule(Request $request, $id)
        {
            $module = \App\Models\CourseModule::findOrFail($id);

            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'position' => 'nullable|integer',
            ]);

            $module->update([
                'title' => $request->title,
                'description' => $request->description,
                'position' => $request->position ?? $module->position,
            ]);

            return response()->json(['message' => 'Module updated successfully', 'module' => $module->loadCount('lessons')]);
        }

        public function deleteModule($id)
        {
            $module = \App\Models\CourseModule::findOrFail($id);
            
            // Delete all lessons in this module
            $module->lessons()->delete();
            $module->delete();

            return response()->json(['message' => 'Module deleted successfully']);
        }

        // === LESSON MANAGEMENT ===
        public function getLessons(Request $request, $moduleId)
        {
            $module = \App\Models\CourseModule::findOrFail($moduleId);
            
            $lessons = $module->lessons()
                ->orderBy('position')
                ->get();

            return response()->json(['data' => $lessons]);
        }

        public function createLesson(Request $request)
        {
            $request->validate([
                'module_id' => 'required|exists:course_modules,id',
                'course_id' => 'required|exists:courses,id',
                'title' => 'required|string|max:255',
                'content_type' => 'required|string|in:video,article,pdf,audio,quiz,other',
                'content' => 'nullable|string',
                'media_url' => 'nullable|string', 
                'media_file' => 'nullable|file|max:512000', // 500MB limit
                'duration_seconds' => 'nullable|integer|min:0',
                'is_downloadable' => 'boolean',
                'position' => 'nullable|integer|min:1',
            ]);

            // Handle File Upload
            $mediaUrl = $request->media_url;
            if ($request->hasFile('media_file')) {
                $path = $request->file('media_file')->store('courses/lessons', 'public');
                $mediaUrl = \Illuminate\Support\Facades\Storage::url($path);
            }

            // Get next position if not provided
            $position = $request->position;
            if (!$position) {
                $maxPosition = \App\Models\Lesson::where('module_id', $request->module_id)
                    ->max('position');
                $position = ($maxPosition ?? 0) + 1;
            }

            $lesson = \App\Models\Lesson::create([
                'module_id' => $request->module_id,
                'course_id' => $request->course_id,
                'title' => $request->title,
                'content_type' => $request->content_type,
                'content' => $request->content,
                'media_url' => $mediaUrl,
                'duration_seconds' => $request->duration_seconds ?? 0,
                'is_downloadable' => $request->is_downloadable ?? false,
                'position' => $position,
            ]);

            return response()->json(['message' => 'Lesson created successfully', 'lesson' => $lesson], 201);
        }

        public function updateLesson(Request $request, $id)
        {
            $lesson = \App\Models\Lesson::findOrFail($id);

            $request->validate([
                'title' => 'required|string|max:255',
                'content_type' => 'required|string|in:video,article,pdf,audio,quiz,other',
                'content' => 'nullable|string',
                'media_url' => 'nullable|string',
                'media_file' => 'nullable|file|max:512000', // 500MB limit
                'duration_seconds' => 'nullable|integer|min:0',
                'is_downloadable' => 'boolean',
                'position' => 'nullable|integer|min:1',
            ]);

            // Handle File Upload
            $mediaUrl = $request->media_url ?? $lesson->media_url; // Default to existing or new text URL
            if ($request->hasFile('media_file')) {
                // Delete old file if strictly local (optional, but good practice)
                // We assume if it contains 'storage/courses/lessons' it is ours.
                if ($lesson->media_url && strpos($lesson->media_url, 'storage/courses/lessons') !== false) {
                    $oldPath = str_replace('/storage/', '', parse_url($lesson->media_url, PHP_URL_PATH));
                    if (\Illuminate\Support\Facades\Storage::disk('public')->exists($oldPath)) {
                        \Illuminate\Support\Facades\Storage::disk('public')->delete($oldPath);
                    }
                }

                $path = $request->file('media_file')->store('courses/lessons', 'public');
                $mediaUrl = \Illuminate\Support\Facades\Storage::url($path);
            }

            $lesson->update([
                'title' => $request->title,
                'content_type' => $request->content_type,
                'content' => $request->content,
                'media_url' => $mediaUrl,
                'duration_seconds' => $request->duration_seconds ?? $lesson->duration_seconds,
                'is_downloadable' => $request->is_downloadable ?? $lesson->is_downloadable,
                'position' => $request->position ?? $lesson->position,
            ]);

            return response()->json(['message' => 'Lesson updated successfully', 'lesson' => $lesson]);
        }

        public function deleteLesson($id)
        {
            $lesson = \App\Models\Lesson::findOrFail($id);
            $lesson->delete();

            return response()->json(['message' => 'Lesson deleted successfully']);
        }

    // Category Management Methods
    public function getCategories(Request $request)
    {
        $query = \App\Models\Category::query();

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        $categories = $query->paginate(10);

        return response()->json($categories);
    }

    public function createCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories',
            'description' => 'nullable|string|max:1000',
            'slug' => 'nullable|string|max:255|unique:categories',
        ]);

        $category = \App\Models\Category::create([
            'name' => $request->name,
            'slug' => $request->slug ?? \Illuminate\Support\Str::slug($request->name),
            'description' => $request->description,
        ]);

        return response()->json(['message' => 'Category created successfully', 'category' => $category], 201);
    }

    public function updateCategory(Request $request, $id)
    {
        $category = \App\Models\Category::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $id,
            'description' => 'nullable|string|max:1000',
            'slug' => 'nullable|string|max:255|unique:categories,slug,' . $id,
        ]);

        $category->update([
            'name' => $request->name ?? $category->name,
            'slug' => $request->slug ?? ($request->has('name') ? \Illuminate\Support\Str::slug($request->name) : $category->slug),
            'description' => $request->description ?? $category->description,
        ]);

        return response()->json(['message' => 'Category updated successfully', 'category' => $category]);
    }

    public function deleteCategory($id)
    {
        $category = \App\Models\Category::findOrFail($id);

        // Check if category is used by any courses
        if ($category->courses()->count() > 0) {
            return response()->json(['message' => 'Cannot delete category that has courses'], 403);
        }

        $category->delete();

        return response()->json(['message' => 'Category deleted successfully']);
    }

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!Auth::user()->isAdmin()) {
                if ($request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                    return response()->json(['message' => 'Unauthorized. Admin access required.'], 403);
                }
                abort(403, 'Unauthorized');
            }
            return $next($request);
        });
    }


    public function index()
    {
        return view('admin.index');
    }

    public function users()
    {
        return view('admin.users.index');
    }

    public function courses()
    {
        return view('admin.courses.index');
    }

    public function categories()
    {
        return view('admin.categories.index');
    }

    public function enrollments()
    {
        return view('admin.enrollments.index');
    }

    public function badges()
    {
        return view('admin.badges.index');
    }

    public function notifications()
    {
        return view('admin.notifications.index');
    }

    public function progress()
    {
        return view('admin.progress.index');
    }

    public function grading()
    {
        return view('admin.grading.index');
    }

    public function quizzes()
    {
        return view('admin.quizzes.index');
    }

    // User Management Methods
    public function getUsers(Request $request)
    {
        $query = User::with('roles');

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('username', 'LIKE', "%{$search}%");
            });
        }

        // Role filter
        if ($request->has('role') && !empty($request->role)) {
            $query->whereHas('roles', function($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        // Status filter
        if ($request->has('status') && $request->status !== '') {
            $query->where('is_active', $request->status === 'active');
        }

        $users = $query->paginate(10);

        return response()->json($users);
    }

    public function createUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'username' => 'nullable|string|max:255|unique:users',
            'role' => 'required|string|exists:roles,name',
        ]);

        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
                'username' => $request->username,
                'is_active' => true,
            ]);

            // Assign role
            $role = Role::where('name', $request->role)->first();
            $user->roles()->attach($role->id, [
                'assigned_by' => Auth::id(),
                'assigned_at' => now(),
            ]);

            DB::commit();
            return response()->json(['message' => 'User created successfully', 'user' => $user->load('roles')]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Failed to create user', 'error' => $e->getMessage()], 500);
        }
    }

    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'username' => 'nullable|string|max:255|unique:users,username,' . $user->id,
            'role' => 'required|string|exists:roles,name',
            'is_active' => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'username' => $request->username,
                'is_active' => $request->is_active ?? $user->is_active,
            ]);

            // Update role if changed
            $currentRole = $user->roles()->first();
            if ($currentRole && $currentRole->name !== $request->role) {
                $user->roles()->detach();
                $newRole = Role::where('name', $request->role)->first();
                $user->roles()->attach($newRole->id, [
                    'assigned_by' => Auth::id(),
                    'assigned_at' => now(),
                ]);
            }

            DB::commit();
            return response()->json(['message' => 'User updated successfully', 'user' => $user->load('roles')]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Failed to update user', 'error' => $e->getMessage()], 500);
        }
    }

    public function toggleUserStatus(User $user)
    {
        $user->update(['is_active' => !$user->is_active]);

        return response()->json([
            'message' => 'User status updated successfully',
            'user' => $user
        ]);
    }

    public function deleteUser(User $user)
    {
        // Prevent deleting admin users or self
        if ($user->isAdmin() || $user->id === Auth::id()) {
            return response()->json(['message' => 'Cannot delete this user'], 403);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }

    // Role Management Methods
    public function getRoles()
    {
        $roles = Role::all();
        return response()->json($roles);
    }

    // === ENROLLMENT MANAGEMENT ===
    public function getEnrollments(Request $request)
    {
        \Log::info("AdminController::getEnrollments called", ['user_id' => Auth::id(), 'params' => $request->all()]);
        
        $baseQuery = \App\Models\Enrollment::with(['user', 'course' => function ($q) {
            $q->with(['instructor', 'category']);
        }])
        ->orderBy('created_at', 'desc');

        \Log::info("Query count before filters", ['count' => $baseQuery->count()]);
        
        $query = $baseQuery;

        // Search by student name or email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        // Filter by course
        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by payment status
        if ($request->filled('payment_status')) {
            if ($request->payment_status === 'paid') {
                $query->where('price_paid', '>', 0);
            } elseif ($request->payment_status === 'pending') {
                $query->where('price_paid', 0)
                      ->whereHas('course', function ($q) {
                          $q->where('price', '>', 0);
                      });
            } elseif ($request->payment_status === 'free') {
                $query->where(function ($q) {
                    $q->where('price_paid', 0)
                      ->whereHas('course', function ($c) {
                          $c->where('price', '=', 0);
                      });
                });
            }
        }

        $perPage = $request->get('per_page', 10);
        $enrollments = $query->paginate($perPage);

        \Log::info('getEnrollments pagination object', [
            'total' => $enrollments->total(),
            'count' => count($enrollments->items()),
            'has_data' => isset($enrollments['data']),
            'data_keys' => array_keys($enrollments->toArray())
        ]);

        $responseJson = response()->json($enrollments);
        \Log::info('getEnrollments response status', [
            'status' => $responseJson->status(),
            'content_length' => strlen($responseJson->getContent()),
            'content_sample' => substr($responseJson->getContent(), 0, 200)
        ]);

        return $responseJson;
    }

    public function updateEnrollment(Request $request, $id)
    {
        $enrollment = \App\Models\Enrollment::findOrFail($id);

        $request->validate([
            'status' => 'required|string|in:active,completed,suspended,expired',
            'progress_percent' => 'nullable|numeric|min:0|max:100',
            'price_paid' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|max:10',
            'expires_at' => 'nullable|datetime',
        ]);

        $enrollment->update([
            'status' => $request->status,
            'progress_percent' => $request->progress_percent ?? $enrollment->progress_percent,
            'price_paid' => $request->price_paid ?? $enrollment->price_paid,
            'currency' => $request->currency ?? $enrollment->currency,
            'expires_at' => $request->expires_at ?? $enrollment->expires_at,
        ]);

        return response()->json(['message' => 'Enrollment updated successfully', 'enrollment' => $enrollment->load('user', 'course')]);
    }

    public function deleteEnrollment($id)
    {
        $enrollment = \App\Models\Enrollment::findOrFail($id);
        $enrollment->delete();

        return response()->json(['message' => 'Enrollment deleted successfully']);
    }

    public function getEnrollmentStats()
    {
        $stats = [
            'total' => \App\Models\Enrollment::count(),
            'active' => \App\Models\Enrollment::where('status', 'active')->count(),
            'completed' => \App\Models\Enrollment::where('status', 'completed')->count(),
            'suspended' => \App\Models\Enrollment::where('status', 'suspended')->count(),
            'expired' => \App\Models\Enrollment::where('status', 'expired')->count(),
            'paid' => \App\Models\Enrollment::where('price_paid', '>', 0)->count(),
            'pending' => \App\Models\Enrollment::where('price_paid', 0)->whereHas('course', function ($q) {
                $q->where('price', '>', 0);
            })->count(),
        ];

        return response()->json($stats);
    }

    // Statistics Methods
    public function getStats()
    {
        $stats = [
            'totalUsers' => User::count(),
            'totalCourses' => \App\Models\Course::count(),
            'totalEnrollments' => \App\Models\Enrollment::count(),
            'totalRevenue' => \App\Models\Payment::where('status', 'paid')->sum('amount'),
        ];

        return response()->json($stats);
    }

    public function getBadges(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $search = $request->input('search', '');
            
            $query = \App\Models\Badge::query();
            
            if ($search) {
                $query->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('code', 'LIKE', "%{$search}%");
            }
            
            $badges = $query->orderBy('name')->get();
            
            return response()->json(['data' => $badges]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // === PAYMENTS MANAGEMENT ===
    public function payments()
    {
        return view('admin.payments.index');
    }

    public function getPayments(Request $request)
    {
        $query = \App\Models\Payment::with(['user', 'course', 'enrollment'])
            ->orderBy('created_at', 'desc');

        // Search by user name, email, or transaction reference
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($qu) use ($search) {
                    $qu->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('email', 'LIKE', "%{$search}%");
                })->orWhere('transaction_ref', 'LIKE', "%{$search}%");
            });
        }

        // Filter by payment status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by currency
        if ($request->filled('currency')) {
            $query->where('currency', $request->currency);
        }

        $perPage = $request->get('per_page', 10);
        $payments = $query->paginate($perPage);

        return response()->json($payments);
    }

    public function getPayment($id)
    {
        $payment = \App\Models\Payment::with(['user', 'course', 'enrollment'])->findOrFail($id);
        return response()->json($payment);
    }

    public function deletePayment($id)
    {
        $payment = \App\Models\Payment::findOrFail($id);
        $payment->delete();
        return response()->json(['message' => 'Payment deleted successfully']);
    }

    public function createBadge(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:badges',
                'code' => 'required|string|max:100|unique:badges',
                'description' => 'nullable|string|max:1000',
            ]);
            
            $badge = \App\Models\Badge::create($validated);
            
            return response()->json(['data' => $badge, 'message' => 'Badge created successfully']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function updateBadge(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        try {
            $badge = \App\Models\Badge::findOrFail($id);
            
            $validated = $request->validate([
                'name' => 'sometimes|string|max:255|unique:badges,name,' . $id,
                'code' => 'sometimes|string|max:100|unique:badges,code,' . $id,
                'description' => 'nullable|string|max:1000',
            ]);
            
            $badge->update($validated);
            
            return response()->json(['data' => $badge, 'message' => 'Badge updated successfully']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function deleteBadge($id): \Illuminate\Http\JsonResponse
    {
        try {
            $badge = \App\Models\Badge::findOrFail($id);
            $badge->delete();
            
            return response()->json(['message' => 'Badge deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getNotifications(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $perPage = $request->input('per_page', 10);
            $search = $request->input('search', '');
            
            $query = \App\Models\Notification::with('user')->orderBy('sent_at', 'desc');
            
            if ($search) {
                $query->whereJsonContains('payload->message', $search);
            }
            
            if ($request->input('is_read') !== '' && $request->input('is_read') !== null) {
                $query->where('is_read', $request->input('is_read'));
            }
            
            if ($request->input('type')) {
                $query->where('type', $request->input('type'));
            }
            
            $notifications = $query->paginate($perPage);
            
            return response()->json($notifications);
        } catch (\Exception $e) {
            \Log::error('getNotifications() error', ['error' => $e->getMessage()]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function sendNotification(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $validated = $request->validate([
                'type' => 'required|string|max:100',
                'message' => 'required|string|max:1000',
                'user_id' => 'nullable|integer|exists:users,id',
            ]);
            
            $payload = ['message' => $validated['message']];
            
            if ($validated['user_id']) {
                // Send to specific user
                \App\Models\Notification::create([
                    'user_id' => $validated['user_id'],
                    'type' => $validated['type'],
                    'payload' => $payload,
                    'is_read' => false,
                    'sent_at' => now(),
                ]);
            } else {
                // Broadcast to all users
                $users = \App\Models\User::pluck('id');
                foreach ($users as $userId) {
                    \App\Models\Notification::create([
                        'user_id' => $userId,
                        'type' => $validated['type'],
                        'payload' => $payload,
                        'is_read' => false,
                        'sent_at' => now(),
                    ]);
                }
            }
            
            return response()->json(['message' => 'Notification sent successfully']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function deleteNotification($id): \Illuminate\Http\JsonResponse
    {
        try {
            $notification = \App\Models\Notification::findOrFail($id);
            $notification->delete();
            
            return response()->json(['message' => 'Notification deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function markLessonComplete(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $validated = $request->validate([
                'enrollment_id' => 'required|integer|exists:enrollments,id',
                'lesson_id' => 'required|integer|exists:lessons,id',
            ]);

            // Create or update lesson progress
            \App\Models\LessonProgress::updateOrCreate(
                [
                    'user_id' => $request->user()->id,
                    'lesson_id' => $validated['lesson_id'],
                ],
                [
                    'course_id' => \App\Models\Lesson::find($validated['lesson_id'])->course_id,
                    'is_completed' => true,
                    'completed_at' => now(),
                ]
            );

            // Update enrollment progress
            $enrollment = \App\Models\Enrollment::find($validated['enrollment_id']);
            $totalLessons = \App\Models\Lesson::where('course_id', $enrollment->course_id)->count();
            $completedLessons = \App\Models\LessonProgress::where('course_id', $enrollment->course_id)
                ->where('user_id', $enrollment->user_id)
                ->where('is_completed', true)
                ->count();

            $progressPercent = $totalLessons > 0 ? ($completedLessons / $totalLessons) * 100 : 0;
            $enrollment->update(['progress_percent' => $progressPercent]);

            return response()->json(['message' => 'Lesson marked as complete', 'progress' => $progressPercent]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    // === QUIZ MANAGEMENT ===
    public function getQuiz($lessonId)
    {
        $lesson = \App\Models\Lesson::findOrFail($lessonId);
        
        if ($lesson->content_type !== 'quiz') {
            return response()->json(['message' => 'This lesson is not a quiz'], 400);
        }

        $quiz = \App\Models\Quiz::with(['questions.choices'])->firstOrCreate(
            ['lesson_id' => $lessonId],
            [
                'course_id' => $lesson->course_id,
                'title' => $lesson->title,
                'description' => 'Default quiz description',
                'passing_score' => 70,
                'time_limit_seconds' => 0,
                'attempts_allowed' => 0
            ]
        );

        return response()->json(['quiz' => $quiz]);
    }

    public function updateQuiz(Request $request, $lessonId)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'passing_score' => 'required|integer|min:0|max:100',
            'time_limit_seconds' => 'nullable|integer|min:0',
            'attempts_allowed' => 'nullable|integer|min:0',
            'questions' => 'array',
            'questions.*.question' => 'nullable|string',
            'questions.*.question_text' => 'nullable|string',
            'questions.*.type' => 'required|string',
            'questions.*.points' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();
            
            // 1. Update or Create Quiz
            $lesson = \App\Models\Lesson::findOrFail($lessonId);
            
            $quiz = \App\Models\Quiz::updateOrCreate(
                ['lesson_id' => $lessonId],
                [
                    'course_id' => $lesson->course_id,
                    'title' => $request->title,
                    'description' => $request->description,
                    'passing_score' => $request->passing_score,
                    'time_limit_seconds' => $request->time_limit_seconds ?? 0,
                    'attempts_allowed' => $request->attempts_allowed ?? 0,
                ]
            );

            // 2. Sync Questions
            // Delete old questions (Cascade will delete choices)
            $quiz->questions()->delete();

            if ($request->has('questions') && is_array($request->questions)) {
                foreach ($request->questions as $index => $qData) {
                    // Handle both 'question' and 'question_text' field names
                    $questionText = $qData['question'] ?? $qData['question_text'] ?? '';
                    
                    $question = $quiz->questions()->create([
                        'type' => $qData['type'] ?? 'mcq',
                        'question_text' => $questionText,
                        'points' => $qData['points'] ?? 1,
                        'position' => $index,
                    ]);

                    // 3. Create Choices for MCQ/TrueFalse
                    if (in_array($qData['type'], ['mcq', 'truefalse', 'multi_select'])) {
                        if (isset($qData['choices']) && is_array($qData['choices'])) {
                            foreach ($qData['choices'] as $cIndex => $cData) {
                                $question->choices()->create([
                                    'text' => $cData['text'] ?? '',
                                    'is_correct' => $cData['is_correct'] ?? false,
                                    'position' => $cIndex,
                                ]);
                            }
                        }
                    }
                }
            }

            DB::commit();
            return response()->json([
                'message' => 'Quiz saved successfully', 
                'quiz' => $quiz->load('questions.choices')
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            
            // EMERGENCY DEBUGGING
            try {
                file_put_contents(public_path('debug_quiz_error.txt'), date('Y-m-d H:i:s') . " - Error: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            } catch (\Exception $writeErr) {
                // Ignore write error
            }

            // DEBUG: Return error directly to see in network tab
            return response()->json([
                'message' => 'Failed to save quiz',
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }
    // === GRADING MANAGEMENT ===

    public function getPendingSubmissions(Request $request) {
        $user = Auth::user();
        
        $query = \App\Models\QuizSubmission::whereIn('status', ['pending_review', 'completed'])
            ->with(['user', 'quiz.lesson.course', 'quiz.questions'])
            ->orderBy('created_at', 'asc');
        
        // If instructor, only show their course submissions
        if ($user->hasRole('instructor')) {
            $query->whereHas('quiz.lesson.course', function($q) use ($user) {
                $q->where('instructor_id', $user->id);
            });
        }
        
        $submissions = $query->paginate(10);
        
        // Add essay question indicators to each submission
        foreach ($submissions as $submission) {
            $essayQuestions = $submission->quiz->questions->filter(function($q) {
                return $q->type === 'essay';
            });
            $submission->essay_count = $essayQuestions->count();
            $submission->has_essays = $essayQuestions->count() > 0;
        }
        
        return response()->json($submissions);
    }

    public function getSubmission($id) {
         $submission = \App\Models\QuizSubmission::with([
             'user', 
             'quiz.questions.choices', 
             'quiz.lesson.course'
         ])->findOrFail($id);
         
         // Check authorization
         $user = Auth::user();
         if ($user->hasRole('instructor') && $submission->quiz->lesson->course->instructor_id !== $user->id) {
             abort(403, 'Tidak memiliki akses untuk submission ini');
         }
         
         return response()->json($submission);
    }

    public function gradeSubmission(Request $request, $id) {
         // Validate input
         $validated = $request->validate([
             'grades' => 'required|array',
             'grades.*' => 'numeric|min:0',
         ]);
         
         $submission = \App\Models\QuizSubmission::with([
             'quiz.questions',
             'quiz.lesson.course'
         ])->findOrFail($id);
         
         // Check authorization
         $user = Auth::user();
         if ($user->hasRole('instructor') && $submission->quiz->lesson->course->instructor_id !== $user->id) {
             abort(403, 'Tidak memiliki akses untuk grading submission ini');
         }
         
         $grades = $validated['grades']; // Array of { question_id: SCORE }

         $answers = $submission->answers;
         $totalScore = 0;
         $maxScore = 0;

         foreach ($submission->quiz->questions as $question) {
             $maxScore += $question->points;
         }

         // Update answers with scores
         $updatedAnswers = [];
         $earnedScore = 0;

         foreach ($answers as $ans) {
             $qId = $ans['question_id'];
             
             // If this question was graded in this request
             // Handle both string and integer keys
             if (isset($grades[$qId]) || isset($grades[(string)$qId])) {
                 $awardedPoints = $grades[$qId] ?? $grades[(string)$qId];
                 $ans['points_earned'] = floatval($awardedPoints);
                 $ans['is_correct'] = ($awardedPoints > 0); 
             }
             
             // Sum earned points
             if (isset($ans['points_earned'])) {
                 $earnedScore += floatval($ans['points_earned']);
             }
             
             $updatedAnswers[] = $ans;
         }
         
         $submission->answers = $updatedAnswers;
         $submission->score = $earnedScore;
         
         // Recalculate status
         $percentage = ($maxScore > 0) ? ($earnedScore / $maxScore) * 100 : 0;
         $submission->status = $percentage >= $submission->quiz->passing_score ? 'passed' : 'failed';
         
         $submission->save();

         // Mark lesson as complete if passed
         if ($submission->status === 'passed') {
             \App\Models\LessonProgress::updateOrCreate(
                ['user_id' => $submission->user_id, 'lesson_id' => $submission->quiz->lesson_id],
                ['is_completed' => true, 'completed_at' => now()]
             );
         }

         // Create notification for student
         $statusText = $submission->status === 'passed' ? 'Lulus' : 'Tidak Lulus';
         \App\Models\Notification::create([
             'user_id' => $submission->user_id,
             'type' => 'grading',
             'payload' => [
                 'message' => "Pekerjaan Anda di kuis \"{$submission->quiz->title}\" telah dinilai dengan skor {$earnedScore}/{$maxScore} ({$percentage}%). Anda {$statusText}.",
                 'title' => "Kuis Dinilai: {$submission->quiz->title}",
                 'quiz_id' => $submission->quiz_id,
                 'score' => $earnedScore,
                 'max_score' => $maxScore,
                 'status' => $submission->status,
             ],
             'is_read' => false,
             'sent_at' => now(),
         ]);

         return response()->json([
             'message' => 'Submission graded successfully', 
             'submission' => $submission
         ]);
    }

    public function getQuizzes(Request $request)
    {
        $quizzes = \App\Models\Quiz::with(['course', 'lesson'])
            ->withCount(['questions', 'submissions'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($quizzes);
    }

    /**
     * Show forum management page
     */
    public function forums()
    {
        return view('admin.forums');
    }

    /**
     * Get all forum threads for admin
     */
    public function getForumThreads(Request $request)
    {
        try {
            $query = \App\Models\ForumThread::with(['user', 'course', 'posts']);

            if ($request->filled('search')) {
                $search = $request->input('search');
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('body', 'like', "%{$search}%");
            }

            if ($request->filled('course_id')) {
                $query->where('course_id', $request->input('course_id'));
            }

            if ($request->filled('locked')) {
                $locked = $request->input('locked') === 'true';
                $query->where('is_locked', $locked);
            }

            $threads = $query->orderBy('created_at', 'desc')->paginate(20);

            return response()->json([
                'success' => true,
                'threads' => $threads->map(function($thread) {
                    return [
                        'id' => $thread->id,
                        'title' => $thread->title,
                        'author' => $thread->user->name,
                        'course' => $thread->course?->title,
                        'posts_count' => $thread->posts()->count(),
                        'is_locked' => $thread->is_locked,
                        'is_sticky' => $thread->is_sticky,
                        'created_at' => $thread->created_at->toDateString(),
                    ];
                }),
                'total' => $threads->total(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch threads',
            ], 500);
        }
    }

    /**
     * Lock/unlock a forum thread
     */
    public function updateThreadStatus(Request $request, $threadId)
    {
        try {
            $thread = \App\Models\ForumThread::findOrFail($threadId);

            if ($request->filled('is_locked')) {
                $thread->is_locked = $request->input('is_locked') === 'true';
            }

            if ($request->filled('is_sticky')) {
                $thread->is_sticky = $request->input('is_sticky') === 'true';
            }

            $thread->save();

            return response()->json([
                'success' => true,
                'message' => 'Thread updated successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update thread',
            ], 500);
        }
    }

    /**
     * Delete a forum thread
     */
    public function deleteThread($threadId)
    {
        try {
            $thread = \App\Models\ForumThread::findOrFail($threadId);
            $thread->posts()->delete();
            $thread->delete();

            return response()->json([
                'success' => true,
                'message' => 'Thread deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete thread',
            ], 500);
        }
    }
}

