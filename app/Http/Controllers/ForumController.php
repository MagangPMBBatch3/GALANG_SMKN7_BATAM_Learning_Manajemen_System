<?php

namespace App\Http\Controllers;

use App\Models\ForumThread;
use App\Models\ForumPost;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForumController extends Controller
{
    /**
     * Display the forum index page
     */
    public function index()
    {
        $courses = Course::all();
        return view('forums.index', compact('courses'));
    }

    /**
     * Get all forum threads with filtering and sorting
     */
    public function getThreads(Request $request)
    {
        try {
            $courseId = $request->input('course_id');
            $sort = $request->input('sort', 'latest');

            $query = ForumThread::with(['user', 'course', 'posts.user'])
                ->where('is_locked', false);

            if ($request->filled('course_id')) {
                $query->where('course_id', $courseId);
            }

            // Apply sorting
            switch ($sort) {
                case 'popular':
                    $query->withCount('posts')
                        ->orderBy('posts_count', 'desc')
                        ->orderBy('created_at', 'desc');
                    break;
                case 'unanswered':
                    $query->whereHas('posts', function($q) {
                        $q->whereNull('parent_post_id');
                    }, '<', 2)
                        ->orderBy('created_at', 'desc');
                    break;
                default:
                    $query->orderBy('created_at', 'desc');
            }

            $threads = $query->paginate(10);

            return response()->json([
                'success' => true,
                'threads' => $threads->map(function($thread) {
                    return [
                        'id' => $thread->id,
                        'title' => $thread->title,
                        'body' => substr($thread->body, 0, 150) . '...',
                        'author' => $thread->user->name,
                        'course' => $thread->course?->title,
                        'replies' => $thread->posts()->count(),
                        'views' => rand(10, 500),
                        'created_at' => $thread->created_at->diffForHumans(),
                        'is_sticky' => $thread->is_sticky,
                    ];
                }),
                'total' => $threads->total(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch threads: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get forum statistics
     */
    public function getStats()
    {
        try {
            $totalThreads = ForumThread::count();
            $totalPosts = ForumPost::count();
            $totalMembers = \App\Models\User::whereHas('forumThreads')->orWhereHas('forumPosts')->count();

            return response()->json([
                'success' => true,
                'stats' => [
                    'threads' => $totalThreads,
                    'replies' => $totalPosts,
                    'members' => $totalMembers,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch stats',
            ], 500);
        }
    }

    /**
     * Create a new forum thread
     */
    public function storeThread(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'body' => 'required|string|min:10',
                'course_id' => 'required|integer|exists:courses,id',
            ]);

            $thread = ForumThread::create([
                'user_id' => Auth::id(),
                'course_id' => $validated['course_id'],
                'title' => $validated['title'],
                'body' => $validated['body'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Thread created successfully',
                'data' => [
                    'id' => $thread->id,
                    'title' => $thread->title,
                    'body' => $thread->body,
                    'user_id' => $thread->user_id,
                    'course_id' => $thread->course_id,
                    'created_at' => $thread->created_at,
                    'updated_at' => $thread->updated_at,
                ]
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create thread: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get a single thread with all posts
     */
    public function getThread($id)
    {
        try {
            $thread = ForumThread::with([
                'user:id,name,email',
                'course:id,title',
                'posts' => function($q) {
                    $q->with(['user:id,name,email', 'likes:id,post_id,user_id'])->orderBy('created_at', 'asc');
                },
                'likes:id,thread_id,user_id'
            ])->findOrFail($id);

            // Load all posts together with likes
            $allPosts = $thread->posts()->with(['user', 'likes'])->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $thread->id,
                    'title' => $thread->title,
                    'body' => $thread->body,
                    'user' => $thread->user,
                    'course' => $thread->course,
                    'created_at' => $thread->created_at,
                    'is_locked' => $thread->is_locked,
                    'is_sticky' => $thread->is_sticky,
                    'views' => 0,
                    'likes_count' => $thread->likes()->count(),
                    'posts' => $allPosts->map(function($post) {
                        return [
                            'id' => $post->id,
                            'thread_id' => $post->thread_id,
                            'user_id' => $post->user_id,
                            'parent_post_id' => $post->parent_post_id,
                            'body' => $post->body,
                            'user' => $post->user,
                            'created_at' => $post->created_at,
                            'updated_at' => $post->updated_at,
                            'likes_count' => $post->likes()->count(),
                        ];
                    }),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Thread not found: ' . $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Add a reply to a thread
     */
    public function storePost(Request $request, $threadId)
    {
        try {
            $thread = ForumThread::findOrFail($threadId);

            if ($thread->is_locked) {
                return response()->json([
                    'success' => false,
                    'message' => 'This thread is locked',
                ], 403);
            }

            $validated = $request->validate([
                'body' => 'required|string|min:3|max:2000',
                'parent_post_id' => 'nullable|integer|exists:forum_posts,id',
            ]);

            $post = ForumPost::create([
                'thread_id' => $threadId,
                'user_id' => Auth::id(),
                'parent_post_id' => $request->input('parent_post_id'),
                'body' => $validated['body'],
            ]);

            // Reload with relationships
            $post->load(['user:id,name,email', 'likes']);

            return response()->json([
                'success' => true,
                'message' => 'Reply posted successfully',
                'data' => [
                    'id' => $post->id,
                    'thread_id' => $post->thread_id,
                    'user_id' => $post->user_id,
                    'parent_post_id' => $post->parent_post_id,
                    'body' => $post->body,
                    'user' => $post->user,
                    'created_at' => $post->created_at,
                    'updated_at' => $post->updated_at,
                    'likes_count' => $post->likes()->count(),
                ],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to post reply: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get courses for dropdown
     */
    public function getCourses()
    {
        try {
            $courses = Course::select('id', 'title')->get();

            return response()->json([
                'success' => true,
                'courses' => $courses,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch courses',
            ], 500);
        }
    }

    /**
     * Like or unlike a thread
     */
    public function likeThread(Request $request, $threadId)
    {
        try {
            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 401);
            }

            $thread = ForumThread::findOrFail($threadId);
            $userId = Auth::id();

            // Toggle like (use updateOrCreate to toggle)
            $like = $thread->likes()->where('user_id', $userId)->first();

            if ($like) {
                $like->delete();
            } else {
                $thread->likes()->create(['user_id' => $userId]);
            }

            return response()->json([
                'success' => true,
                'likes_count' => $thread->likes()->count(),
                'liked' => !$like,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to like thread',
            ], 500);
        }
    }

    /**
     * Like or unlike a post
     */
    public function likePost(Request $request, $postId)
    {
        try {
            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 401);
            }

            $post = ForumPost::findOrFail($postId);
            $userId = Auth::id();

            // Toggle like
            $like = $post->likes()->where('user_id', $userId)->first();

            if ($like) {
                $like->delete();
            } else {
                $post->likes()->create(['user_id' => $userId]);
            }

            return response()->json([
                'success' => true,
                'likes_count' => $post->likes()->count(),
                'liked' => !$like,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to like post',
            ], 500);
        }
    }

    /**
     * Delete a post
     */
    public function deletePost(Request $request, $postId)
    {
        try {
            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 401);
            }

            $post = ForumPost::findOrFail($postId);

            // Check authorization: only post owner or admin can delete
            if (Auth::id() !== $post->user_id && !Auth::user()->is_admin) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            // Delete nested replies first
            $post->replies()->delete();
            $post->likes()->delete();
            $post->delete();

            return response()->json([
                'success' => true,
                'message' => 'Post deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete post: ' . $e->getMessage(),
            ], 500);
        }
    }
}
