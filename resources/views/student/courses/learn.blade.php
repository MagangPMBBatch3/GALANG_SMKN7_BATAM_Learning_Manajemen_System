@extends('layouts.main')

@section('content')
<div class="min-h-screen bg-slate-50 dark:bg-slate-900" x-data="coursePlayer()">
    
    <div class="bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 sticky top-0 z-40">
        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-4 flex-1 min-w-0">
                    <a href="{{ url('/dashboard') }}" class="text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                    </a>
                    <div class="flex-1 min-w-0">
                        <h1 class="text-lg font-bold text-slate-900 dark:text-white truncate" x-text="course?.title || 'Loading...'"></h1>
                        <div class="flex items-center gap-2 text-xs text-slate-600 dark:text-slate-300 font-medium">
                            <span x-text="Math.round(enrollment?.progress_percent || 0)"></span>% Selesai
                            <span class="text-slate-400">•</span>
                            <span x-text="completedLessons.length"></span>/<span x-text="lessons.length"></span> Pelajaran
                        </div>
                    </div>
                </div>

                <div class="hidden md:flex items-center gap-4">
                    <div class="w-48">
                        <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-2">
                            <div class="bg-green-600 h-2 rounded-full transition-all" :style="`width: ${enrollment?.progress_percent || 0}%`"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="flex h-[calc(100vh-4rem)]">
        <div class="w-80 bg-white dark:bg-slate-800 border-r border-slate-200 dark:border-slate-700 overflow-y-auto flex-shrink-0">
            <div class="p-4">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wide">Konten Kursus</h2>
                    <button @click="expandAll = !expandAll" class="text-xs text-blue-600 hover:text-blue-700">
                        <span x-text="expandAll ? 'Tutup Semua' : 'Buka Semua'"></span>
                    </button>
                </div>
                
                <template x-if="loading">
                    <div class="space-y-4 animate-pulse">
                        <div class="h-12 bg-slate-200 dark:bg-slate-700 rounded-lg"></div>
                        <div class="space-y-2 pl-4">
                            <div class="h-8 bg-slate-100 dark:bg-slate-700 rounded w-3/4"></div>
                            <div class="h-8 bg-slate-100 dark:bg-slate-700 rounded w-full"></div>
                        </div>
                    </div>
                </template>

                <template x-if="!loading">
                    <div class="space-y-2">
                        <template x-for="module in modules" :key="module.id">
                            <div class="border border-slate-200 dark:border-slate-700 rounded-lg overflow-hidden">
                                <button 
                                    @click="toggleModule(module.id)"
                                    class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-700/50 hover:bg-slate-100 dark:hover:bg-slate-600 transition flex items-center justify-between text-left"
                                >
                                    <div class="flex-1">
                                        <p class="font-semibold text-slate-900 dark:text-white text-sm" x-text="module.title"></p>
                                        <p class="text-xs text-slate-600 dark:text-slate-300 mt-1 font-medium">
                                            <span x-text="getModuleLessons(module.id).filter(l => isLessonCompleted(l.id)).length"></span>/<span x-text="getModuleLessons(module.id).length"></span> selesai
                                        </p>
                                    </div>
                                    <svg class="w-5 h-5 text-slate-500 transition-transform duration-200" :class="{'rotate-180': expandedModule === module.id}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>

                                <template x-if="expandedModule === module.id">
                                    <div class="bg-white dark:bg-slate-800">
                                        <template x-for="lesson in getModuleLessons(module.id)" :key="lesson.id">
                                            <button 
                                                @click="selectLesson(lesson)"
                                                :class="{
                                                    'bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-600': selectedLesson?.id === lesson.id,
                                                    'hover:bg-slate-50 dark:hover:bg-slate-700/30 border-l-4 border-transparent': selectedLesson?.id !== lesson.id
                                                }"
                                                class="w-full px-4 py-3 text-left border-b border-slate-100 dark:border-slate-700 transition flex items-start gap-3"
                                            >
                                                <!-- Status Icon -->
                                                <div class="flex-shrink-0 mt-0.5">
                                                    <template x-if="isLessonCompleted(lesson.id)">
                                                        <div class="w-5 h-5 rounded-full bg-green-600 flex items-center justify-center">
                                                            <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                            </svg>
                                                        </div>
                                                    </template>
                                                    <template x-if="!isLessonCompleted(lesson.id) && selectedLesson?.id === lesson.id">
                                                        <div class="w-5 h-5 rounded-full border-2 border-blue-600 flex items-center justify-center">
                                                            <div class="w-2 h-2 rounded-full bg-blue-600"></div>
                                                        </div>
                                                    </template>
                                                    <template x-if="!isLessonCompleted(lesson.id) && selectedLesson?.id !== lesson.id">
                                                        <div class="w-5 h-5 rounded-full border-2 border-slate-300 dark:border-slate-600"></div>
                                                    </template>
                                                </div>

                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-medium text-slate-900 dark:text-white truncate" x-text="lesson.title"></p>
                                                    <div class="flex items-center gap-2 mt-1">
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold"
                                                            :class="{
                                                                'bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-200': lesson.content_type === 'video',
                                                                'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-200': lesson.content_type === 'article',
                                                                'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-200': lesson.content_type === 'pdf',
                                                                'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-200': lesson.content_type === 'audio',
                                                                'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-200': lesson.content_type === 'quiz'
                                                            }"
                                                            x-text="lesson.content_type">
                                                        </span>
                                                        <template x-if="lesson.duration">
                                                            <span class="text-xs text-slate-600 dark:text-slate-300 font-medium" x-text="Math.floor(lesson.duration / 60) + ' min'"></span>
                                                        </template>
                                                    </div>
                                                </div>
                                            </button>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                </template>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col overflow-hidden bg-slate-50 dark:bg-slate-900">
            <template x-if="selectedLesson">
                <div class="h-full flex flex-col">
                    <!-- Lesson Header -->
                    <div class="bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 px-6 py-4">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-2" x-text="selectedLesson.title"></h2>
                                <div class="flex items-center gap-4 text-sm text-slate-700 dark:text-slate-300 font-medium">
                                    <span class="inline-flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                        </svg>
                                        <span x-text="selectedLesson.content_type"></span>
                                    </span>
                                    <template x-if="selectedLesson.duration">
                                        <span class="inline-flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <span x-text="Math.floor(selectedLesson.duration / 60) + ' menit'"></span>
                                        </span>
                                    </template>
                                </div>
                            </div>
                            <template x-if="isLessonCompleted(selectedLesson.id)">
                                <div class="flex items-center gap-2 px-4 py-2 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 rounded-lg">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="font-medium">Selesai</span>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="flex-1 overflow-y-auto">
                        <template x-if="selectedLesson.content_type === 'video'">
                            <div class="h-full flex flex-col bg-black">
                                <div class="flex-1 flex items-center justify-center p-4">
                                    <div class="w-full max-w-5xl">
                                        <template x-if="isExternalVideo(selectedLesson.content_url)">
                                            <div class="aspect-video">
                                                <iframe 
                                                    :src="getEmbedUrl(selectedLesson.content_url)" 
                                                    class="w-full h-full rounded-lg" 
                                                    frameborder="0" 
                                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                                                    allowfullscreen>
                                                </iframe>
                                            </div>
                                        </template>

                                        <template x-if="!isExternalVideo(selectedLesson.content_url)">
                                            <video 
                                                x-ref="videoPlayer"
                                                :key="selectedLesson.content_url"
                                                :src="selectedLesson.content_url"
                                                controls
                                                controlsList="nodownload"
                                                preload="metadata"
                                                class="w-full rounded-lg shadow-2xl"
                                                @ended="markLessonComplete()"
                                                @@error="handleMediaError($event)"
                                            >
                                                <p class="text-white p-8 text-center">Browser Anda tidak mendukung tag video.</p>
                                            </video>
                                        </template>
                                    </div>
                                </div>
                                
                                <!-- Video Description (if any) -->
                                <template x-if="selectedLesson.content">
                                    <div class="bg-white dark:bg-slate-800 border-t border-slate-200 dark:border-slate-700 p-6">
                                        <div class="max-w-5xl mx-auto">
                                            <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-3">Tentang pelajaran ini</h3>
                                            <div class="max-w-none text-slate-800 dark:text-slate-200 leading-relaxed
                                                [&_p]:mb-6
                                                [&_h1]:text-3xl [&_h1]:font-extrabold [&_h1]:tracking-tight [&_h1]:mt-10 [&_h1]:mb-6 [&_h1]:text-slate-900 dark:[&_h1]:text-white
                                                [&_h2]:text-2xl [&_h2]:font-bold [&_h2]:tracking-tight [&_h2]:mt-8 [&_h2]:mb-4 [&_h2]:text-slate-900 dark:[&_h2]:text-white
                                                [&_h3]:text-xl [&_h3]:font-bold [&_h3]:mt-6 [&_h3]:mb-3 [&_h3]:text-slate-900 dark:[&_h3]:text-white
                                                [&_h4]:text-lg [&_h4]:font-semibold [&_h4]:mt-6 [&_h4]:mb-3 [&_h4]:text-slate-900 dark:[&_h4]:text-white
                                                [&_ul]:list-disc [&_ul]:pl-6 [&_ul]:mb-6 [&_ul]:space-y-2
                                                [&_ol]:list-decimal [&_ol]:pl-6 [&_ol]:mb-6 [&_ol]:space-y-2
                                                [&_li]:pl-1
                                                [&_a]:text-blue-600 [&_a]:underline [&_a]:underline-offset-2 hover:[&_a]:text-blue-800 dark:[&_a]:text-blue-400 dark:hover:[&_a]:text-blue-300
                                                [&_strong]:font-bold [&_strong]:text-slate-900 dark:[&_strong]:text-white
                                                [&_em]:italic
                                                [&_blockquote]:border-l-4 [&_blockquote]:border-slate-200 [&_blockquote]:pl-4 [&_blockquote]:italic [&_blockquote]:my-6 [&_blockquote]:text-slate-600 dark:[&_blockquote]:border-slate-700 dark:[&_blockquote]:text-slate-400
                                                [&_code]:bg-slate-100 [&_code]:px-1.5 [&_code]:py-0.5 [&_code]:rounded [&_code]:text-sm [&_code]:font-mono [&_code]:text-pink-600 dark:[&_code]:bg-slate-800 dark:[&_code]:text-pink-400" 
                                                x-html="selectedLesson.content">
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>

                        <!-- Article Content -->
                        <template x-if="selectedLesson.content_type === 'article'">
                            <div class="h-full overflow-y-auto bg-white dark:bg-slate-800">
                                <div class="max-w-4xl mx-auto px-6 py-8">
                                    <div class="max-w-none text-slate-800 dark:text-slate-200 leading-relaxed text-lg
                                        [&_p]:mb-6
                                        [&_h1]:text-4xl [&_h1]:font-extrabold [&_h1]:tracking-tight [&_h1]:mt-12 [&_h1]:mb-6 [&_h1]:text-slate-900 dark:[&_h1]:text-white
                                        [&_h2]:text-3xl [&_h2]:font-bold [&_h2]:tracking-tight [&_h2]:mt-10 [&_h2]:mb-4 [&_h2]:text-slate-900 dark:[&_h2]:text-white
                                        [&_h3]:text-2xl [&_h3]:font-bold [&_h3]:mt-8 [&_h3]:mb-4 [&_h3]:text-slate-900 dark:[&_h3]:text-white
                                        [&_h4]:text-xl [&_h4]:font-semibold [&_h4]:mt-6 [&_h4]:mb-3 [&_h4]:text-slate-900 dark:[&_h4]:text-white
                                        [&_ul]:list-disc [&_ul]:pl-6 [&_ul]:mb-6 [&_ul]:space-y-2
                                        [&_ol]:list-decimal [&_ol]:pl-6 [&_ol]:mb-6 [&_ol]:space-y-2
                                        [&_li]:pl-1
                                        [&_a]:text-blue-600 [&_a]:underline [&_a]:underline-offset-2 hover:[&_a]:text-blue-800 dark:[&_a]:text-blue-400 dark:hover:[&_a]:text-blue-300
                                        [&_strong]:font-bold [&_strong]:text-slate-900 dark:[&_strong]:text-white
                                        [&_em]:italic
                                        [&_blockquote]:border-l-4 [&_blockquote]:border-slate-200 [&_blockquote]:pl-4 [&_blockquote]:italic [&_blockquote]:my-6 [&_blockquote]:text-slate-600 dark:[&_blockquote]:border-slate-700 dark:[&_blockquote]:text-slate-400
                                        [&_code]:bg-slate-100 [&_code]:px-1.5 [&_code]:py-0.5 [&_code]:rounded [&_code]:text-sm [&_code]:font-mono [&_code]:text-pink-600 dark:[&_code]:bg-slate-800 dark:[&_code]:text-pink-400" 
                                        x-html="selectedLesson.content || '<p class=&quot;text-gray-500&quot;>No content available</p>'">
                                    </div>
                                </div>
                            </div>
                        </template>

                        <!-- PDF Content -->
                        <template x-if="selectedLesson.content_type === 'pdf' && selectedLesson.content_url">
                            <div class="h-full flex flex-col bg-slate-100 dark:bg-slate-900">
                                <div class="bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 px-6 py-4">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-4">
                                            <div class="flex items-center gap-2">
                                                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                                </svg>
                                                <h3 class="text-lg font-semibold text-slate-900 dark:text-white" x-text="selectedLesson.title"></h3>
                                            </div>
                                        </div>

                                        <!-- Action Buttons -->
                                        <div class="flex items-center gap-2">
                                            <a :href="selectedLesson.content_url"
                                               target="_blank"
                                               class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition shadow-sm">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                                </svg>
                                                Buka di Tab Baru
                                            </a>
                                            <a :href="selectedLesson.content_url"
                                               download
                                               class="p-2 rounded-lg border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition"
                                               title="Unduh PDF">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                                </svg>
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex-1 relative bg-slate-200 dark:bg-slate-900">
                                    <iframe
                                        :src="selectedLesson.content_url + '#toolbar=1&navpanes=1&scrollbar=1'"
                                        class="absolute inset-0 w-full h-full border-0"
                                        @load="markLessonComplete()"
                                        title="PDF Viewer">
                                    </iframe>
                                </div>
                            </div>
                        </template>

                        <template x-if="selectedLesson.content_type === 'audio' && selectedLesson.content_url">
                            <div class="h-full flex items-center justify-center bg-gradient-to-br from-purple-50 to-blue-50 dark:from-slate-900 dark:to-slate-800">
                                <div class="w-full max-w-2xl mx-auto px-6">
                                    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl p-8 border border-slate-200 dark:border-slate-700">
                                        <div class="text-center mb-8">
                                            <div class="w-24 h-24 bg-gradient-to-br from-purple-500 to-blue-500 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                                                <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path>
                                                </svg>
                                            </div>
                                            <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-2" x-text="selectedLesson.title"></h3>
                                            <p class="text-slate-500 dark:text-slate-400">Pelajaran Audio</p>
                                        </div>
                                        
                                        <div class="bg-slate-50 dark:bg-slate-700/50 rounded-xl p-6 mb-6">
                                            <audio 
                                                :src="selectedLesson.content_url"
                                                controls
                                                class="w-full"
                                                @ended="markLessonComplete()"
                                                @@error="handleMediaError($event)"
                                            >
                                                Your browser does not support the audio tag.
                                            </audio>
                                        </div>

                                        <template x-if="selectedLesson.content">
                                            <div class="prose dark:prose-invert max-w-none" x-html="selectedLesson.content"></div>
                                        </template>

                                        <template x-if="selectedLesson.is_downloadable">
                                            <div class="text-center mt-6">
                                                <a :href="selectedLesson.content_url" download class="inline-flex items-center gap-2 text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                                    </svg>
                                                    Unduh Audio
                                                </a>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <!-- Quiz Content -->
                        <template x-if="selectedLesson.content_type === 'quiz'">
                            <div class="h-full bg-slate-50 dark:bg-slate-900 overflow-y-auto">
                                <!-- Loading State -->
                                <template x-if="!quiz && quizQuestions.length === 0 && quizAttempts.length === 0">
                                    <div class="flex items-center justify-center h-full">
                                        <div class="text-center max-w-md bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-slate-200 dark:border-slate-700 p-8">
                                            <div class="w-16 h-16 bg-yellow-100 dark:bg-yellow-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                                                <svg class="w-8 h-8 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                                </svg>
                                            </div>
                                            <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-2">Quiz Belum Dikonfigurasi</h3>
                                            <p class="text-slate-500 dark:text-slate-400 mb-6">Quiz untuk lesson ini belum diatur oleh instruktur. Silakan hubungi instruktur atau coba lagi nanti.</p>
                                            <button @click="selectLesson(lessons[0])" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition font-medium">
                                                Kembali ke Lesson Pertama
                                            </button>
                                        </div>
                                    </div>
                                </template>

                                <template x-if="quiz">
                                    <div class="max-w-3xl mx-auto px-6 py-8">
                                        
                                        <!-- INTRO / HISTORY VIEW -->
                                        <template x-if="quizStatus === 'intro'">
                                            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-8">
                                                <div class="text-center mb-8">
                                                    <div class="w-16 h-16 bg-yellow-100 dark:bg-yellow-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                                                        <svg class="w-8 h-8 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                                        </svg>
                                                    </div>
                                                    <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-2" x-text="quiz.title || selectedLesson.title"></h2>
                                                    <p class="text-slate-600 dark:text-slate-400 mb-6" x-text="quiz.description || 'Selesaikan kuis ini untuk melanjutkan.'"></p>
                                                    
                                                    <div class="flex justify-center gap-6 text-sm mb-8">
                                                        <div class="bg-slate-50 dark:bg-slate-700 px-4 py-2 rounded-lg">
                                                            <span class="block text-slate-500 dark:text-slate-400 text-xs uppercase tracking-wider">KKM</span>
                                                            <span class="font-bold text-slate-900 dark:text-white" x-text="quiz.passing_score"></span>
                                                        </div>
                                                        <div class="bg-slate-50 dark:bg-slate-700 px-4 py-2 rounded-lg">
                                                            <span class="block text-slate-500 dark:text-slate-400 text-xs uppercase tracking-wider">Durasi</span>
                                                            <span class="font-bold text-slate-900 dark:text-white" x-text="quiz.time_limit_seconds > 0 ? formatTime(quiz.time_limit_seconds) : 'Tanpa Batas'"></span>
                                                        </div>
                                                        <div class="bg-slate-50 dark:bg-slate-700 px-4 py-2 rounded-lg">
                                                            <span class="block text-slate-500 dark:text-slate-400 text-xs uppercase tracking-wider">Percobaan</span>
                                                            <span class="font-bold text-slate-900 dark:text-white" x-text="(quizAttempts.length) + '/' + (quiz.attempts_allowed > 0 ? quiz.attempts_allowed : '∞')"></span>
                                                        </div>
                                                    </div>

                                                    <button 
                                                        @click="startQuiz()"
                                                        :disabled="quiz.attempts_allowed > 0 && quizAttempts.length >= quiz.attempts_allowed"
                                                        class="bg-blue-600 hover:bg-blue-700 disabled:bg-slate-300 disabled:cursor-not-allowed text-white px-8 py-3 rounded-lg font-bold shadow-lg shadow-blue-500/30 transition transform hover:-translate-y-0.5"
                                                    >
                                                        Mulai Kuis
                                                    </button>
                                                </div>

                                                <!-- History -->
                                                <template x-if="quizAttempts.length > 0">
                                                    <div class="border-t border-slate-200 dark:border-slate-700 pt-6">
                                                        <h3 class="font-bold text-slate-900 dark:text-white mb-4">Riwayat Pengerjaan</h3>
                                                        <div class="space-y-3">
                                                            <template x-for="(attempt, index) in quizAttempts" :key="attempt.id">
                                                                <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-700/50 rounded-lg">
                                                                    <div>
                                                                        <span class="text-sm font-medium text-slate-900 dark:text-white" x-text="'Percobaan ' + (quizAttempts.length - index)"></span>
                                                                        <p class="text-xs text-slate-500" x-text="new Date(attempt.created_at).toLocaleDateString()"></p>
                                                                    </div>
                                                                    <div class="text-right">
                                                                        <div class="font-bold" :class="attempt.status === 'graded' ? (attempt.score >= quiz.passing_score ? 'text-green-600' : 'text-red-600') : 'text-slate-600'">
                                                                            <span x-text="Math.round(attempt.score)"></span> Nilai
                                                                        </div>
                                                                        <span class="text-xs px-2 py-0.5 rounded-full" 
                                                                            :class="attempt.status === 'graded' ? (attempt.score >= quiz.passing_score ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700') : 'bg-blue-100 text-blue-700'"
                                                                            x-text="attempt.status === 'graded' ? (attempt.score >= quiz.passing_score ? 'Lulus' : 'Gagal') : 'Menunggu Review'">
                                                                        </span>
                                                                        <button @click="openReview(attempt.id)" class="text-xs text-blue-600 hover:text-blue-800 underline mt-1 block w-full text-right">
                                                                            Review
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </template>
                                                        </div>
                                                    </div>
                                                </template>
                                            </div>
                                        </template>

                                        <!-- ACTIVE QUIZ VIEW -->
                                        <template x-if="quizStatus === 'active'">
                                            <div class="space-y-6">
                                                <!-- Header / Timer -->
                                                <div class="flex items-center justify-between bg-white dark:bg-slate-800 p-4 rounded-lg shadow-sm sticky top-0 z-20 border border-slate-200 dark:border-slate-700">
                                                    <span class="font-medium text-slate-600 dark:text-slate-300">
                                                        Soal <span x-text="currentQuestionIndex + 1"></span> dari <span x-text="quizQuestions.length"></span>
                                                    </span>
                                                    <template x-if="quiz.time_limit_seconds > 0">
                                                        <div class="flex items-center gap-2 text-orange-600 font-bold font-mono text-lg bg-orange-50 dark:bg-orange-900/20 px-3 py-1 rounded">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                            </svg>
                                                            <span x-text="formatTime(quizTimeRemaining)"></span>
                                                        </div>
                                                    </template>
                                                </div>

                                                <!-- Question Card -->
                                                <div class="bg-white dark:bg-slate-800 rounded-xl shadow p-8 border border-slate-200 dark:border-slate-700">
                                                    <template x-if="quizQuestions && quizQuestions[currentQuestionIndex]">
                                                        <div>
                                                            <h3 class="text-lg font-medium text-slate-900 dark:text-white mb-6" x-text="quizQuestions[currentQuestionIndex].question || quizQuestions[currentQuestionIndex].question_text"></h3>
                                                            
                                                            <!-- MCQ / TrueFalse -->
                                                            <template x-if="['mcq', 'truefalse'].includes(quizQuestions[currentQuestionIndex].type)">
                                                                <div class="space-y-3">
                                                                    <template x-for="choice in quizQuestions[currentQuestionIndex].choices" :key="choice.id">
                                                                        <label class="flex items-start p-4 rounded-lg border cursor-pointer transition-all hover:bg-slate-50 dark:hover:bg-slate-700"
                                                                            :class="quizAnswers[quizQuestions[currentQuestionIndex].id] == choice.id ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20 ring-1 ring-blue-500' : 'border-slate-200 dark:border-slate-700'">
                                                                            <div class="flex items-center h-5">
                                                                                <input type="radio" 
                                                                                    :name="'q_' + quizQuestions[currentQuestionIndex].id"
                                                                                    :value="choice.id"
                                                                                    x-model="quizAnswers[quizQuestions[currentQuestionIndex].id]"
                                                                                    class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300">
                                                                            </div>
                                                                            <div class="ml-3 text-sm">
                                                                                <span class="font-medium text-slate-900 dark:text-white" x-text="choice.text"></span>
                                                                            </div>
                                                                        </label>
                                                                    </template>
                                                                </div>
                                                            </template>

                                                            <!-- Essay -->
                                                            <template x-if="quizQuestions[currentQuestionIndex].type === 'essay'">
                                                                <div class="space-y-2">
                                                                    <textarea 
                                                                        rows="6"
                                                                        x-model="quizAnswers[quizQuestions[currentQuestionIndex].id]"
                                                                        class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                                                        placeholder="Tulis jawaban Anda di sini..."></textarea>
                                                                </div>
                                                            </template>
                                                        </div>
                                                    </template>
                                                    <template x-if="!quizQuestions || !quizQuestions[currentQuestionIndex]">
                                                        <div class="text-center py-8 text-slate-500">
                                                            Memuat pertanyaan... (atau terjadi kesalahan)
                                                        </div>
                                                    </template>
                                                </div>

                                                <!-- Navigation -->
                                                <div class="flex items-center justify-between mt-8">
                                                    <button 
                                                        @click="prevQuestion()"
                                                        :disabled="currentQuestionIndex === 0"
                                                        class="px-6 py-2 rounded-lg border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 font-medium hover:bg-slate-50 dark:hover:bg-slate-700 disabled:opacity-50 disabled:cursor-not-allowed">
                                                        Sebelumnya
                                                    </button>
                                                    
                                                    <template x-if="!isLastQuestion()">
                                                        <button 
                                                            @click="nextQuestion()"
                                                            class="px-6 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-medium shadow-sm">
                                                            Selanjutnya
                                                        </button>
                                                    </template>

                                                    <template x-if="isLastQuestion()">
                                                        <button 
                                                            @click="submitQuiz()"
                                                            :disabled="quizSubmitting"
                                                            class="px-8 py-2 rounded-lg bg-green-600 hover:bg-green-700 text-white font-bold shadow-lg shadow-green-500/30 flex items-center gap-2">
                                                            <template x-if="quizSubmitting">
                                                                <svg class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                                </svg>
                                                            </template>
                                                            <span x-text="quizSubmitting ? 'Mengirim...' : 'Kumpulkan'"></span>
                                                        </button>
                                                    </template>
                                                </div>
                                            </div>
                                        </template>

                                        <!-- RESULT VIEW -->
                                        <template x-if="quizStatus === 'result' && quizResult">
                                            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-slate-200 dark:border-slate-700 p-8 text-center max-w-2xl mx-auto">
                                                <div class="mb-6">
                                                    <div class="w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4"
                                                        :class="quizResult.passed ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600'">
                                                        <template x-if="quizResult.passed">
                                                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                        </template>
                                                        <template x-if="!quizResult.passed">
                                                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                        </template>
                                                    </div>
                                                    
                                                    <h2 class="text-3xl font-bold text-slate-900 dark:text-white mb-2" x-text="quizResult.passed ? 'Selamat! Anda Lulus' : 'Belum Lulus'"></h2>
                                                    
                                                    <template x-if="quizResult.review_needed">
                                                        <p class="text-yellow-600 bg-yellow-50 px-4 py-2 rounded-lg inline-block text-sm font-medium mb-4">Jawaban esai Anda sedang menunggu penilaian instruktur.</p>
                                                    </template>
                                                </div>

                                                <div class="grid grid-cols-2 gap-4 mb-8">
                                                    <div class="bg-slate-50 dark:bg-slate-700 p-4 rounded-lg">
                                                        <span class="block text-slate-500 text-xs uppercase font-bold">Skor Anda</span>
                                                        <span class="text-3xl font-extrabold text-blue-600" x-text="Math.round(quizResult.score)"></span>
                                                    </div>
                                                    <div class="bg-slate-50 dark:bg-slate-700 p-4 rounded-lg">
                                                        <span class="block text-slate-500 text-xs uppercase font-bold">Persentase</span>
                                                        <span class="text-3xl font-extrabold" :class="quizResult.passed ? 'text-green-600' : 'text-red-500'" x-text="quizResult.percentage + '%'"></span>
                                                    </div>
                                                </div>

                                                <div class="flex justify-center gap-4">
                                                    <button @click="quizStatus = 'intro'" class="px-6 py-2 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-700 dark:text-white hover:bg-slate-50 dark:hover:bg-slate-700 transition font-medium">
                                                        Kembali
                                                    </button>
                                                    <button @click="openReview(quizResult.submission_id)" class="px-6 py-2 border border-blue-300 rounded-lg text-blue-700 bg-blue-50 hover:bg-blue-100 transition font-medium">
                                                        Lihat Jawaban
                                                    </button>
                                                    <template x-if="!quizResult.passed">
                                                        <button @click="startQuiz()" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition font-bold shadow">
                                                            Coba Lagi
                                                        </button>
                                                    </template>
                                                    <template x-if="quizResult.passed && !isLastLesson()">
                                                        <button @click="nextLesson()" class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition font-bold shadow flex items-center gap-2">
                                                            Lanjut Belajar
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                                                        </button>
                                                    </template>
                                                </div>
                                            </div>
                                        </template>

                                        <!-- REVIEW VIEW -->
                                        <template x-if="quizStatus === 'review' && reviewData">
                                            <div class="space-y-6">
                                                <div class="flex items-center justify-between">
                                                    <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Review Jawaban</h2>
                                                    <button @click="closeReview()" class="px-4 py-2 text-slate-500 hover:text-slate-700 dark:text-slate-400">
                                                        Tutup Review
                                                    </button>
                                                </div>

                                                <div class="bg-white dark:bg-slate-800 rounded-xl shadow border border-slate-200 dark:border-slate-700 overflow-hidden">
                                                    <div class="p-6 space-y-8">
                                                        <template x-for="(question, index) in reviewData.quiz.questions" :key="question.id">
                                                            <div class="border-b border-slate-200 dark:border-slate-700 last:border-0 pb-6 last:pb-0">
                                                                <div class="flex items-start gap-4">
                                                                    <span class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-full bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 font-bold text-sm" x-text="index + 1"></span>
                                                                    <div class="flex-1">
                                                                        <h3 class="text-lg font-medium text-slate-900 dark:text-white mb-4" x-text="question.question"></h3>
                                                                        
                                                                        <!-- Choices -->
                                                                         <template x-if="['mcq', 'truefalse'].includes(question.type)">
                                                                            <div class="space-y-2">
                                                                                <template x-for="choice in question.choices" :key="choice.id">
                                                                                    <div class="flex items-center gap-3 p-3 rounded-lg border"
                                                                                         :class="{
                                                                                            'bg-green-50 border-green-200 text-green-800': choice.is_correct,
                                                                                            'bg-red-50 border-red-200 text-red-800': !choice.is_correct && isUserChoice(question.id, choice.id),
                                                                                            'bg-slate-50 border-slate-200 opacity-50': !choice.is_correct && !isUserChoice(question.id, choice.id)
                                                                                         }">
                                                                                        <div class="flex-shrink-0">
                                                                                            <template x-if="choice.is_correct">
                                                                                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                                                            </template>
                                                                                            <template x-if="!choice.is_correct && isUserChoice(question.id, choice.id)">
                                                                                                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                                                            </template>
                                                                                            <template x-if="!choice.is_correct && !isUserChoice(question.id, choice.id)">
                                                                                                <div class="w-5 h-5"></div>
                                                                                            </template>
                                                                                        </div>
                                                                                        <span x-text="choice.text"></span>
                                                                                        <template x-if="choice.is_correct">
                                                                                            <span class="ml-auto text-xs font-bold text-green-600 uppercase">Jawaban Benar</span>
                                                                                        </template>
                                                                                         <template x-if="!choice.is_correct && isUserChoice(question.id, choice.id)">
                                                                                            <span class="ml-auto text-xs font-bold text-red-600 uppercase">Jawaban Anda</span>
                                                                                        </template>
                                                                                    </div>
                                                                                </template>
                                                                            </div>
                                                                         </template>

                                                                         <!-- Essay -->
                                                                         <template x-if="question.type === 'essay'">
                                                                             <div class="space-y-4">
                                                                                <div class="bg-slate-50 dark:bg-slate-900 p-4 rounded-lg border border-slate-200 dark:border-slate-700">
                                                                                    <p class="text-xs text-slate-500 uppercase font-bold mb-2">Jawaban Anda:</p>
                                                                                    <p class="text-slate-800 dark:text-slate-200 whitespace-pre-wrap" x-text="getUserAnswerText(question.id)"></p>
                                                                                </div>
                                                                                <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                                                                                    <div class="flex justify-between items-center mb-2">
                                                                                        <p class="text-xs text-blue-600 dark:text-blue-400 uppercase font-bold">Nilai:</p>
                                                                                        <span class="font-  bold text-blue-700 dark:text-blue-300" x-text="getUserScore(question.id) + ' / ' + question.points"></span>
                                                                                    </div>
                                                                                </div>
                                                                             </div>
                                                                         </template>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </template>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>

                                    </div>
                                </template>
                            </div>
                        </template>

                        <!-- No Content / Fallback -->
                        <template x-if="!selectedLesson.content_url && selectedLesson.content_type !== 'article' && selectedLesson.content_type !== 'quiz'">
                            <div class="h-full flex items-center justify-center bg-slate-50 dark:bg-slate-900 p-8">
                                <div class="text-center max-w-md bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-slate-200 dark:border-slate-700 p-8">
                                    <div class="w-16 h-16 bg-slate-100 dark:bg-slate-700 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-2">Konten Tidak Tersedia</h3>
                                    <p class="text-slate-500 dark:text-slate-400 mb-6">Pelajaran ini belum memiliki konten media.</p>
                                    <button 
                                        @click="markLessonComplete()"
                                        :disabled="isLessonCompleted(selectedLesson.id)"
                                        class="w-full bg-blue-600 hover:bg-blue-700 disabled:bg-slate-300 disabled:text-slate-500 disabled:cursor-not-allowed text-white px-6 py-3 rounded-lg font-medium transition"
                                    >
                                        <span x-show="!isLessonCompleted(selectedLesson.id)">Tandai Selesai</span>
                                        <span x-show="isLessonCompleted(selectedLesson.id)">Selesai</span>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div class="bg-white dark:bg-slate-800 border-t border-slate-200 dark:border-slate-700 px-6 py-5 mt-auto">
                        <div class="flex items-center justify-between gap-4">
                            <button 
                                @click="previousLesson()"
                                :disabled="!hasPreviousLesson()"
                                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg border-2 border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 disabled:opacity-50 disabled:cursor-not-allowed hover:bg-slate-50 dark:hover:bg-slate-700 hover:border-slate-400 dark:hover:border-slate-500 transition-all font-medium"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                </svg>
                                Sebelumnya
                            </button>

                            <button 
                                @click="markLessonComplete()"
                                :disabled="isLessonCompleted(selectedLesson.id) || markingComplete"
                                :class="{
                                    'bg-green-600 hover:bg-green-700 text-white shadow-lg shadow-green-500/30 hover:shadow-xl': !isLessonCompleted(selectedLesson.id) && !markingComplete,
                                    'bg-green-500 text-white cursor-wait': markingComplete,
                                    'bg-slate-200 dark:bg-slate-700 text-slate-500 dark:text-slate-400 cursor-not-allowed': isLessonCompleted(selectedLesson.id)
                                }"
                                class="inline-flex items-center gap-2 px-8 py-3 rounded-lg font-semibold transition-all text-base"
                            >
                                <template x-if="markingComplete">
                                    <svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                </template>
                                <template x-if="!markingComplete && !isLessonCompleted(selectedLesson.id)">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </template>
                                <template x-if="isLessonCompleted(selectedLesson.id)">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                </template>
                                <span x-text="markingComplete ? 'Menyimpan...' : (isLessonCompleted(selectedLesson.id) ? 'Selesai' : 'Tandai Selesai')"></span>
                            </button>

                                <button 
                                @click="nextLesson()"
                                :disabled="!hasNextLesson()"
                                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white disabled:opacity-50 disabled:cursor-not-allowed transition-all font-medium shadow-md hover:shadow-lg"
                            >
                                Selanjutnya
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                                
                            </button>
                        </div>
                    </div>
                </div>
            </template>

            <template x-if="!selectedLesson && !loading">
                <div class="h-full flex items-center justify-center">
                    <div class="text-center">
                        <div class="w-24 h-24 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-12 h-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-slate-900 dark:text-white mb-2">Pilih pelajaran untuk memulai</h3>
                        <p class="text-slate-500 dark:text-slate-400">Pilih pelajaran dari sidebar untuk mulai belajar</p>
                    </div>
                </div>
            </template>

            <template x-if="loading">
                <div class="h-full flex items-center justify-center">
                    <div class="text-center">
                        <div class="animate-spin rounded-full h-16 w-16 border-b-2 border-blue-600 mx-auto mb-4"></div>
                        <p class="text-slate-600 dark:text-slate-400">Memuat konten kursus...</p>
                    </div>
                </div>
            </template>
        </div>
    </div>
    <!-- Completion Modal -->
    <div x-show="showCompletionModal" 
         style="display: none;"
         class="fixed inset-0 z-50 overflow-y-auto" 
         aria-labelledby="modal-title" 
         role="dialog" 
         aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showCompletionModal" 
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0" 
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100" 
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
                 aria-hidden="true"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="showCompletionModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                <div class="bg-white dark:bg-slate-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 dark:bg-green-900/30 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-bold text-gray-900 dark:text-white" id="modal-title">
                                Selamat! Kursus Selesai
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500 dark:text-gray-300">
                                    Anda telah menyelesaikan semua materi dalam kursus ini. Sertifikat kompetensi dan lencana penghargaan telah diterbitkan.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-slate-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                    <a :href="certificateUrl" target="_blank" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Unduh Sertifikat
                    </a>
                    <button type="button" @click="showCompletionModal = false" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 dark:border-slate-500 shadow-sm px-4 py-2 bg-white dark:bg-slate-600 text-base font-medium text-gray-700 dark:text-white hover:bg-gray-50 dark:hover:bg-slate-500 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function coursePlayer() {
    return {
        courseSlug: '{{ $slug }}',
        course: null,
        enrollment: null,
        modules: [],
        lessons: [],
        selectedLesson: null,
        expandedModule: null,
        expandAll: false,
        completedLessons: [],
        loading: true,
        lessonProgress: 0,
        lessonProgress: 0,
        markingComplete: false,
        showCompletionModal: false,
        markingComplete: false,
        showCompletionModal: false,
        certificateUrl: '',
        
        // Quiz State
        quiz: null,
        quizQuestions: [],
        quizAttempts: [],
        quizStatus: 'intro', // intro, active, result
        currentQuestionIndex: 0,
        quizAnswers: {},
        quizTimer: null,
        quizTimeRemaining: 0,
        quizSubmitting: false,
        quizResult: null,

        async init() {
            console.log('[CoursePlayer] Initializing for slug:', this.courseSlug);
            await this.loadCourseData();
            if (this.modules.length > 0) {
                this.expandedModule = this.modules[0].id;
                const firstLesson = this.getModuleLessons(this.modules[0].id)[0];
                if (firstLesson) {
                    this.selectLesson(firstLesson);
                }
            }
        },
        
        async selectLesson(lesson) {
            this.selectedLesson = lesson;
            // Clear quiz state when changing lesson
            this.quiz = null;
            this.quizStatus = 'intro';
            
            if (lesson.content_type === 'quiz') {
                await this.loadQuizData(lesson.id);
            }
        },

        async loadQuizData(lessonId) {
            try {
                const response = await fetch(`/student/api/quiz/${lessonId}`);
                if (response.ok) {
                    const data = await response.json();
                    this.quiz = data.quiz;
                    this.quizQuestions = data.questions;
                    this.quizAttempts = data.attempts;
                    
                    // If max attempts reached, show result of best attempt? 
                    // Or just show attempts list.
                } else {
                    // Quiz not found or not set up
                    const errorData = await response.json().catch(() => ({}));
                    console.error('Quiz not available:', errorData);
                    
                    // Set quiz to null so UI shows "Quiz belum dikonfigurasi"
                    this.quiz = null;
                    this.quizQuestions = [];
                    this.quizAttempts = [];
                }
            } catch (e) {
                console.error('Error loading quiz', e);
                this.quiz = null;
                this.quizQuestions = [];
                this.quizAttempts = [];
            }
        },

        startQuiz() {
            this.quizStatus = 'active';
            this.currentQuestionIndex = 0;
            this.quizAnswers = {};
            this.quizResult = null;
            
            if (this.quiz.time_limit_seconds > 0) {
                this.quizTimeRemaining = this.quiz.time_limit_seconds;
                this.startTimer();
            }
        },

        startTimer() {
            if (this.quizTimer) clearInterval(this.quizTimer);
            this.quizTimer = setInterval(() => {
                this.quizTimeRemaining--;
                if (this.quizTimeRemaining <= 0) {
                    this.submitQuiz(true); // Auto submit
                }
            }, 1000);
        },

        formatTime(seconds) {
            const minutes = Math.floor(seconds / 60);
            const remainingSeconds = seconds % 60;
            return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`;
        },

        isLastQuestion() {
            return this.currentQuestionIndex === this.quizQuestions.length - 1;
        },

        nextQuestion() {
            if (!this.isLastQuestion()) {
                this.currentQuestionIndex++;
            }
        },

        prevQuestion() {
            if (this.currentQuestionIndex > 0) {
                this.currentQuestionIndex--;
            }
        },

        async submitQuiz(auto = false) {
             if (!auto && !confirm('Apakah Anda yakin ingin mengumpulkan jawaban?')) return;
             
             clearInterval(this.quizTimer);
             this.quizSubmitting = true;
             
             try {
                 const response = await fetch(`/student/api/quiz/${this.quiz.id}/submit`, {
                     method: 'POST',
                     headers: {
                         'Content-Type': 'application/json',
                         'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                     },
                     body: JSON.stringify({
                         answers: this.quizAnswers,
                         started_at: new Date().toISOString()
                     })
                 });
                 
                 const text = await response.text();
                 let result;
                 try {
                     result = JSON.parse(text);
                 } catch (e) {
                      console.error('Non-JSON response:', text);
                      throw new Error('Server error: Invalid response format');
                 }

                 if (!response.ok) {
                     throw new Error(result.error || result.message || 'Gagal menyimpan jawaban.');
                 }
                 
                 this.quizResult = result;
                 this.quizStatus = 'result';
                 
                 if (result.passed) {
                     this.completedLessons.push(this.selectedLesson.id);
                     if (this.allLessonsCompleted()) {
                         setTimeout(() => this.checkCompletion(), 1000);
                     }
                 }
                 
                 // Refresh attempts
                 this.loadQuizData(this.selectedLesson.id);

             } catch (e) {
                 console.error(e);
                 alert('Error: ' + e.message);
             } finally {
                 this.quizSubmitting = false;
             }
        },
        
        allLessonsCompleted() {
            return this.lessons.every(l => this.isLessonCompleted(l.id) || (this.selectedLesson.id === l.id && this.quizResult?.passed));
        },

        async loadCourseData() {
            try {
                console.log('[CoursePlayer] Loading course data...');
                const response = await fetch(`/student/api/course/${this.courseSlug}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    }
                });
                
                if (response.ok) {
                    const data = await response.json();
                    console.log('[CoursePlayer] Course data loaded:', data);
                    
                    this.course = data.course;
                    this.enrollment = data.enrollment;
                    this.modules = data.modules || [];
                    this.lessons = data.lessons || [];
                    this.completedLessons = data.completed_lessons || [];
                    
                    console.log('[CoursePlayer] Lessons:', this.lessons.length);
                    console.log('[CoursePlayer] Completed:', this.completedLessons.length);
                } else {
                    console.error('[CoursePlayer] Failed to load course:', response.status);
                }
            } catch (error) {
                console.error('[CoursePlayer] Error loading course:', error);
            } finally {
                this.loading = false;
            }
        },

        toggleModule(moduleId) {
            this.expandedModule = this.expandedModule === moduleId ? null : moduleId;
        },

        getModuleLessons(moduleId) {
            return this.lessons.filter(l => l.module_id === moduleId);
        },

        async selectLesson(lesson) {
            if (!lesson) {
                console.warn('[CoursePlayer] No lesson to select');
                return;
            }
            
            console.log('[CoursePlayer] Selecting lesson:', lesson.title);
            
            this.selectedLesson = lesson;
            this.lessonProgress = this.isLessonCompleted(lesson.id) ? 100 : 0;
            this.expandedModule = lesson.module_id;

            // Quiz Logic
            this.quiz = null;
            this.quizStatus = 'intro';
            this.reviewData = null; // New state
            if (lesson.content_type === 'quiz') {
                await this.loadQuizData(lesson.id);
            }
        },

        async openReview(submissionId) {
             try {
                 const response = await fetch(`/student/api/quiz/review/${submissionId}`);
                 if (response.ok) {
                     const data = await response.json();
                     this.reviewData = data;
                     this.quizStatus = 'review';
                 }
             } catch (e) {
                 console.error("Error loading review", e);
             }
        },

        isLessonCompleted(lessonId) {
            return this.completedLessons.includes(lessonId);
        },

        handleMediaError(event) {
            console.error('[CoursePlayer] Media load error:', event);
            console.error('[CoursePlayer] Failed URL:', event.target.src);
        },

        async markLessonComplete() {
            if (!this.selectedLesson || this.isLessonCompleted(this.selectedLesson.id) || this.markingComplete) {
                return;
            }

            console.log('[CoursePlayer] Marking lesson complete:', this.selectedLesson.id);
            
            this.markingComplete = true;

            this.completedLessons.push(this.selectedLesson.id);
            this.lessonProgress = 100;

            try {
                const response = await fetch('/student/api/lesson/complete', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    },
                    body: JSON.stringify({
                        lesson_id: this.selectedLesson.id,
                        course_id: this.course.id,
                    })
                });

                if (response.ok) {
                    const data = await response.json();
                    console.log('[CoursePlayer] ✓ Lesson marked complete:', data);
                    
                    if (this.enrollment) {
                        this.enrollment.progress_percent = data.progress_percent || 0;
                    }

                    this.markingComplete = false;

                    if (data.is_completed) {
                        this.certificateUrl = `/student/certificate/${data.certificate_id}/download`;
                        this.showCompletionModal = true;
                        
                        // Fire confetti if possible, or just play completion sound
                        // this.playSuccessSound();
                    } else if (this.hasNextLesson()) {
                        setTimeout(() => this.nextLesson(), 1500);
                    }
                } else {
                    console.error('[CoursePlayer] Failed to save progress:', response.status);
                    const errorData = await response.json().catch(() => ({}));
                    console.error('[CoursePlayer] Error details:', errorData);
                    
                    this.completedLessons = this.completedLessons.filter(id => id !== this.selectedLesson.id);
                    this.lessonProgress = 0;
                    this.markingComplete = false;
                    
                    alert('Failed to save progress. Please try again.');
                }
            } catch (error) {
                console.error('[CoursePlayer] Error marking lesson complete:', error);
                
                this.completedLessons = this.completedLessons.filter(id => id !== this.selectedLesson.id);
                this.lessonProgress = 0;
                this.markingComplete = false;
                
                alert('Network error. Please check your connection and try again.');
            }
        },

        hasNextLesson() {
            if (!this.selectedLesson) return false;
            const currentIndex = this.lessons.findIndex(l => l.id === this.selectedLesson.id);
            return currentIndex < this.lessons.length - 1;
        },

        hasPreviousLesson() {
            if (!this.selectedLesson) return false;
            const currentIndex = this.lessons.findIndex(l => l.id === this.selectedLesson.id);
            return currentIndex > 0;
        },

        nextLesson() {
            if (!this.hasNextLesson()) return;
            const currentIndex = this.lessons.findIndex(l => l.id === this.selectedLesson.id);
            this.selectLesson(this.lessons[currentIndex + 1]);
        },

        previousLesson() {
            if (!this.hasPreviousLesson()) return;
            const currentIndex = this.lessons.findIndex(l => l.id === this.selectedLesson.id);
            this.selectLesson(this.lessons[currentIndex - 1]);
        },

        isExternalVideo(url) {
            if (!url) return false;
            return url.includes('youtube.com') || 
                   url.includes('youtu.be') || 
                   url.includes('drive.google.com') ||
                   url.includes('vimeo.com');
        },

        getEmbedUrl(url) {
            if (!url) return '';
            
            if (url.includes('youtube.com/watch?v=')) {
                return url.replace('watch?v=', 'embed/');
            }
            if (url.includes('youtu.be/')) {
                return url.replace('youtu.be/', 'www.youtube.com/embed/');
            }

            if (url.includes('drive.google.com') && url.includes('/view')) {
                return url.replace('/view', '/preview');
            }

            // Vimeo
            if (url.includes('vimeo.com/')) {
                const videoId = url.split('vimeo.com/')[1];
                return `https://player.vimeo.com/video/${videoId}`;
            }

            return url;
        },

        isUserChoice(questionId, choiceId) {
            if (!this.reviewData) return false;
            const ans = this.reviewData.submission.answers.find(a => a.question_id === questionId);
            if (!ans) return false;
            return parseInt(ans.answer) === parseInt(choiceId);
        },
        getUserAnswerText(questionId) {
             if (!this.reviewData) return '-';
             const ans = this.reviewData.submission.answers.find(a => a.question_id === questionId);
             return ans ? ans.answer : '-';
        },
        getUserScore(questionId) {
             if (!this.reviewData) return 0;
             const ans = this.reviewData.submission.answers.find(a => a.question_id === questionId);
             return ans ? ans.points : 0;
        },
        closeReview() {
            this.quizStatus = 'intro';
        }
    }
}
</script>
@endsection
