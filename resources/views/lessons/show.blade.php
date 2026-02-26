@extends('layouts.main')

@section('title', 'Lesson')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Lesson Header -->
        <div class="bg-white rounded-2xl shadow-xl p-8 mb-8 hover-lift border border-gray-100">
            <div class="flex items-center justify-between">
                <button id="backButton" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Course
                </button>
                <div class="flex items-center space-x-4">
                    <button id="prevLesson" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-colors">
                        <i class="fas fa-chevron-left mr-2"></i>Previous
                    </button>
                    <button id="nextLesson" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl hover:from-blue-700 hover:to-purple-700 transition-colors">
                        Next <i class="fas fa-chevron-right ml-2"></i>
                    </button>
                </div>
            </div>
            <div class="mt-6">
                <h1 class="text-3xl font-bold text-gray-900 mb-2" id="lessonTitle">Loading Lesson...</h1>
                <p class="text-gray-600" id="courseTitle">Course Title</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2">
                <!-- Video Player -->
                <div class="bg-white rounded-2xl shadow-xl p-8 mb-8 hover-lift border border-gray-100">
                    <div class="aspect-video bg-gray-900 rounded-xl overflow-hidden mb-6">
                        <div id="videoPlayer" class="w-full h-full flex items-center justify-center">
                            <!-- Video player will be loaded here -->
                            <div class="text-white text-center">
                                <i class="fas fa-play-circle text-6xl mb-4"></i>
                                <p>Loading video...</p>
                            </div>
                        </div>
                    </div>

                    <!-- Lesson Actions -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <button id="likeButton" class="inline-flex items-center px-4 py-2 rounded-xl hover:bg-red-50 transition-colors">
                                <i class="fas fa-heart mr-2 text-gray-400"></i>
                                <span class="text-gray-600">Like</span>
                            </button>
                            <button id="bookmarkButton" class="inline-flex items-center px-4 py-2 rounded-xl hover:bg-yellow-50 transition-colors">
                                <i class="fas fa-bookmark mr-2 text-gray-400"></i>
                                <span class="text-gray-600">Bookmark</span>
                            </button>
                            <button id="shareButton" class="inline-flex items-center px-4 py-2 rounded-xl hover:bg-blue-50 transition-colors">
                                <i class="fas fa-share mr-2 text-gray-400"></i>
                                <span class="text-gray-600">Share</span>
                            </button>
                        </div>
                        <div class="text-sm text-gray-500">
                            <span id="lessonDuration">0:00</span> / <span id="totalDuration">0:00</span>
                        </div>
                    </div>
                </div>

                <!-- Lesson Content -->
                <div class="bg-white rounded-2xl shadow-xl p-8 mb-8 hover-lift border border-gray-100">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Lesson Content</h2>
                    <div id="lessonContent" class="prose prose-lg max-w-none">
                        <!-- Lesson content will be loaded here -->
                        <p>Loading lesson content...</p>
                    </div>
                </div>

                <!-- Resources -->
                <div class="bg-white rounded-2xl shadow-xl p-8 mb-8 hover-lift border border-gray-100" id="resourcesSection">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Resources</h2>
                    <div id="resourcesContainer">
                        <!-- Resources will be loaded here -->
                    </div>
                </div>

                <!-- Comments -->
                <div class="bg-white rounded-2xl shadow-xl p-8 hover-lift border border-gray-100">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Discussion</h2>
                    <div id="commentsContainer">
                        <!-- Comments will be loaded here -->
                    </div>

                    <!-- Add Comment Form -->
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <form id="commentForm">
                            <div class="mb-4">
                                <textarea id="commentContent" rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-vertical" placeholder="Share your thoughts about this lesson..."></textarea>
                            </div>
                            <div class="flex justify-end">
                                <button type="submit" class="px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold rounded-xl hover:from-blue-700 hover:to-purple-700 transition-all duration-300 transform hover:scale-105 shadow-lg">
                                    <i class="fas fa-paper-plane mr-2"></i>Post Comment
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <!-- Course Progress -->
                <div class="bg-white rounded-2xl shadow-xl p-6 mb-8 hover-lift border border-gray-100 sticky top-8">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Course Progress</h3>
                    <div class="mb-4">
                        <div class="flex justify-between text-sm text-gray-600 mb-2">
                            <span>Overall Progress</span>
                            <span id="overallProgress">0%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="bg-gradient-to-r from-blue-500 to-purple-600 h-3 rounded-full transition-all duration-500" id="progressBar" style="width: 0%"></div>
                        </div>
                    </div>

                    <!-- Lesson List -->
                    <div class="space-y-2" id="lessonList">
                        <!-- Lessons will be loaded here -->
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white rounded-2xl shadow-xl p-6 hover-lift border border-gray-100">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Quick Actions</h3>
                    <div class="space-y-3">
                        <button id="takeNotes" class="w-full text-left px-4 py-3 rounded-xl hover:bg-gray-50 transition-colors flex items-center">
                            <i class="fas fa-sticky-note mr-3 text-blue-600"></i>
                            <span>Take Notes</span>
                        </button>
                        <button id="downloadTranscript" class="w-full text-left px-4 py-3 rounded-xl hover:bg-gray-50 transition-colors flex items-center">
                            <i class="fas fa-download mr-3 text-green-600"></i>
                            <span>Download Transcript</span>
                        </button>
                        <button id="reportIssue" class="w-full text-left px-4 py-3 rounded-xl hover:bg-gray-50 transition-colors flex items-center">
                            <i class="fas fa-flag mr-3 text-red-600"></i>
                            <span>Report Issue</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="/js/lessons.js"></script>
@endsection
