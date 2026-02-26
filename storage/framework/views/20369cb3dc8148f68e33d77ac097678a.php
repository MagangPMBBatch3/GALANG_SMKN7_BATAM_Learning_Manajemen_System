<!-- Professional Lesson Editor Modal -->
<div x-show="showCreateLessonModal || showEditLessonModal" 
     class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4" 
     @click.self="showCreateLessonModal = false; showEditLessonModal = false" 
     x-cloak
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100">
    
    <div class="bg-white dark:bg-slate-900 rounded-2xl w-full max-w-6xl max-h-[95vh] overflow-hidden shadow-2xl"
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100">
        
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-8 py-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold flex items-center gap-3">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                        <span x-text="showEditLessonModal ? 'Edit Pelajaran' : 'Buat Pelajaran Baru'"></span>
                    </h2>
                    <p class="text-blue-100 text-sm mt-1">Tambahkan konten menarik ke modul kursus Anda</p>
                </div>
                <button @click="showCreateLessonModal = false; showEditLessonModal = false" 
                        class="text-white/80 hover:text-white transition-colors p-2 hover:bg-white/10 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

        <form @submit.prevent="saveLesson()">
            <!-- Two Column Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-0 max-h-[calc(95vh-180px)] overflow-y-auto">
                
                <!-- Left Column: Main Content (2/3) -->
                <div class="lg:col-span-2 p-8 space-y-6 border-r border-slate-200 dark:border-slate-700">
                    
                    <!-- Basic Information Section -->
                    <div class="space-y-4">
                        <div class="flex items-center gap-2 text-slate-900 dark:text-white mb-4">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <h3 class="text-lg font-semibold">Informasi Dasar</h3>
                        </div>

                        <!-- Lesson Title -->
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                                Judul Pelajaran <span class="text-red-500">*</span>
                            </label>
                            <input 
                                x-model="lessonForm.title" 
                                type="text" 
                                required 
                                placeholder="Contoh: Pengenalan React Hooks"
                                class="w-full px-4 py-3 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                            >
                            <p class="mt-1.5 text-xs text-slate-500 dark:text-slate-400">Pilih judul yang jelas dan deskriptif untuk pelajaran Anda</p>
                        </div>

                        <!-- Content Type -->
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                                Tipe Konten <span class="text-red-500">*</span>
                            </label>
                            <div class="grid grid-cols-3 gap-3">
                                <label class="relative cursor-pointer">
                                    <input type="radio" x-model="lessonForm.content_type" value="video" class="peer sr-only" required>
                                    <div class="p-4 rounded-lg border-2 border-slate-200 dark:border-slate-700 peer-checked:border-blue-600 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20 hover:border-slate-300 transition-all text-center">
                                        <svg class="w-8 h-8 mx-auto mb-2 text-slate-400 peer-checked:text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                        </svg>
                                        <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Video</span>
                                    </div>
                                </label>
                                <label class="relative cursor-pointer">
                                    <input type="radio" x-model="lessonForm.content_type" value="article" class="peer sr-only">
                                    <div class="p-4 rounded-lg border-2 border-slate-200 dark:border-slate-700 peer-checked:border-green-600 peer-checked:bg-green-50 dark:peer-checked:bg-green-900/20 hover:border-slate-300 transition-all text-center">
                                        <svg class="w-8 h-8 mx-auto mb-2 text-slate-400 peer-checked:text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Article</span>
                                    </div>
                                </label>
                                <label class="relative cursor-pointer">
                                    <input type="radio" x-model="lessonForm.content_type" value="pdf" class="peer sr-only">
                                    <div class="p-4 rounded-lg border-2 border-slate-200 dark:border-slate-700 peer-checked:border-red-600 peer-checked:bg-red-50 dark:peer-checked:bg-red-900/20 hover:border-slate-300 transition-all text-center">
                                        <svg class="w-8 h-8 mx-auto mb-2 text-slate-400 peer-checked:text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                        </svg>
                                        <span class="text-sm font-medium text-slate-700 dark:text-slate-300">PDF</span>
                                    </div>
                                </label>
                                <label class="relative cursor-pointer">
                                    <input type="radio" x-model="lessonForm.content_type" value="quiz" class="peer sr-only">
                                    <div class="p-4 rounded-lg border-2 border-slate-200 dark:border-slate-700 peer-checked:border-purple-600 peer-checked:bg-purple-50 dark:peer-checked:bg-purple-900/20 hover:border-slate-300 transition-all text-center">
                                        <svg class="w-8 h-8 mx-auto mb-2 text-slate-400 peer-checked:text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                        </svg>
                                        <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Quiz</span>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Media Upload Section -->
                    <div x-show="['video', 'pdf', 'audio'].includes(lessonForm.content_type)" 
                         class="space-y-4 p-6 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-200 dark:border-slate-700">
                        
                        <div class="flex items-center gap-2 text-slate-900 dark:text-white mb-4">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                            <h3 class="text-lg font-semibold">Konten Media</h3>
                        </div>

                        <!-- Source Type Toggle -->
                        <div class="flex gap-3 p-1 bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-700">
                            <button type="button" 
                                    @click="lessonForm.sourceType = 'upload'"
                                    :class="lessonForm.sourceType === 'upload' ? 'bg-blue-600 text-white shadow-md' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800'"
                                    class="flex-1 px-4 py-2.5 rounded-md font-medium text-sm transition-all">
                                <svg class="w-4 h-4 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                                Unggah File
                            </button>
                            <button type="button" 
                                    @click="lessonForm.sourceType = 'url'"
                                    :class="lessonForm.sourceType === 'url' ? 'bg-blue-600 text-white shadow-md' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800'"
                                    class="flex-1 px-4 py-2.5 rounded-md font-medium text-sm transition-all">
                                <svg class="w-4 h-4 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                </svg>
                                URL Eksternal
                            </button>
                        </div>

                        <!-- File Upload -->
                        <div x-show="lessonForm.sourceType === 'upload'" x-data="{ fileName: null, fileSize: null }">
                            <div x-show="!fileName" 
                                 class="relative border-2 border-dashed border-slate-300 dark:border-slate-600 rounded-xl p-8 text-center hover:border-blue-500 dark:hover:border-blue-500 transition-all bg-white dark:bg-slate-900 group cursor-pointer">
                                <input 
                                    x-ref="lessonFileUpload" 
                                    type="file" 
                                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                    @change="
                                        if ($refs.lessonFileUpload.files[0]) {
                                            fileName = $refs.lessonFileUpload.files[0].name;
                                            fileSize = ($refs.lessonFileUpload.files[0].size / 1024 / 1024).toFixed(2);
                                        }
                                    "
                                >
                                <svg class="w-16 h-16 mx-auto mb-4 text-slate-400 group-hover:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                                <p class="text-lg font-medium text-slate-700 dark:text-slate-300 mb-1">
                                    Drop your file here, or <span class="text-blue-600">browse</span>
                                </p>
                                <p class="text-sm text-slate-500 dark:text-slate-400" x-text="lessonForm.content_type === 'video' ? 'MP4, WebM, MOV up to 500MB' : (lessonForm.content_type === 'pdf' ? 'PDF up to 50MB' : 'MP3, WAV up to 50MB')"></p>
                            </div>

                            <!-- File Preview -->
                            <div x-show="fileName" 
                                 class="flex items-center gap-4 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl">
                                <div class="flex-shrink-0 w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-slate-900 dark:text-white truncate" x-text="fileName"></p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400" x-text="fileSize + ' MB'"></p>
                                </div>
                                <button type="button" 
                                        @click="fileName = null; fileSize = null; $refs.lessonFileUpload.value = ''"
                                        class="flex-shrink-0 p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- URL Input -->
                        <div x-show="lessonForm.sourceType === 'url'">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                                URL Media
                            </label>
                            <input 
                                x-model="lessonForm.media_url" 
                                type="url" 
                                autocomplete="off"
                                placeholder="https://youtube.com/watch?v=... atau https://drive.google.com/..."
                                class="w-full px-4 py-3 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                            >
                            <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">
                                <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Mendukung YouTube, Vimeo, Google Drive, dan URL file langsung
                            </p>
                        </div>
                    </div>

                    <!-- Article Content Editor -->
                    <div x-show="lessonForm.content_type === 'article' || lessonForm.content_type === 'video'" 
                         class="space-y-4">
                        <div class="flex items-center gap-2 text-slate-900 dark:text-white">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            <h3 class="text-lg font-semibold" x-text="lessonForm.content_type === 'article' ? 'Konten Artikel' : 'Deskripsi (Opsional)'"></h3>
                        </div>
                        <textarea 
                            x-model="lessonForm.content" 
                            rows="8" 
                            placeholder="Tulis konten pelajaran Anda di sini... Anda dapat menggunakan HTML untuk pemformatan."
                            class="w-full px-4 py-3 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all font-mono text-sm"
                        ></textarea>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            HTML supported: &lt;p&gt;, &lt;h1-h6&gt;, &lt;strong&gt;, &lt;em&gt;, &lt;ul&gt;, &lt;ol&gt;, &lt;li&gt;, &lt;a&gt;
                        </p>
                    </div>

                    <!-- Quiz Editor -->
                    <div x-show="lessonForm.content_type === 'quiz'" class="space-y-6">
                        
                        <!-- Quiz Metadata -->
                        <div class="bg-purple-50 dark:bg-purple-900/20 p-6 rounded-xl border border-purple-100 dark:border-purple-800 space-y-4">
                            <h3 class="text-lg font-semibold text-purple-900 dark:text-purple-100 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                Pengaturan Kuis
                            </h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Passing Score (%)</label>
                                    <input type="number" x-model.number="lessonForm.passing_score" min="0" max="100" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-800 text-slate-900 dark:text-white">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Batas Waktu (Menit)</label>
                                    <input type="number" x-model.number="lessonForm.time_limit_minutes" min="0" placeholder="0 = Tidak terbatas" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-800 text-slate-900 dark:text-white">
                                    <p class="text-xs text-slate-500 mt-1">0 untuk tidak ada batas waktu</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Batas Percobaan</label>
                                    <input type="number" x-model.number="lessonForm.attempts_allowed" min="0" placeholder="0 = Tidak terbatas" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-800 text-slate-900 dark:text-white">
                                    <p class="text-xs text-slate-500 mt-1">0 untuk percobaan tidak terbatas</p>
                                </div>
                            </div>
                             <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Deskripsi Kuis</label>
                                <textarea x-model="lessonForm.quiz_description" rows="3" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-800 text-slate-900 dark:text-white"></textarea>
                            </div>
                        </div>

                        <!-- Questions List -->
                         <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-semibold text-slate-900 dark:text-white flex items-center gap-2">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Daftar Pertanyaan
                                </h3>
                                <button type="button" @click="addQuestion()" class="px-3 py-1.5 text-sm bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors font-medium">
                                    + Tambah Pertanyaan
                                </button>
                            </div>

                            <template x-for="(question, qIndex) in lessonForm.questions" :key="qIndex">
                                <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden shadow-sm">
                                    <div class="p-4 bg-slate-50 dark:bg-slate-700/50 border-b border-slate-200 dark:border-slate-700 flex justify-between items-start">
                                        <div class="flex-1 mr-4">
                                            <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                                                <div class="md:col-span-8">
                                                    <input type="text" x-model="question.question" placeholder="Tulis pertanyaan di sini..." class="w-full font-medium bg-transparent border-0 border-b border-slate-300 dark:border-slate-600 focus:ring-0 focus:border-blue-500 px-0 py-1 text-slate-900 dark:text-white">
                                                </div>
                                                <div class="md:col-span-2">
                                                     <select x-model="question.type" class="w-full text-sm rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-800 text-slate-900 dark:text-white">
                                                        <option value="mcq">Pilihan Ganda</option>
                                                        <option value="truefalse">Benar/Salah</option>
                                                        <option value="essay">Esai</option>
                                                    </select>
                                                </div>
                                                 <div class="md:col-span-2">
                                                    <div class="flex items-center gap-2">
                                                        <span class="text-xs text-slate-500">Poin:</span>
                                                        <input type="number" x-model.number="question.points" class="w-full text-sm rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-800 py-1 text-slate-900 dark:text-white">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex flex-col gap-1 ml-2">
                                            <button type="button" @click="if(qIndex > 0) { var t = lessonForm.questions.splice(qIndex, 1)[0]; lessonForm.questions.splice(qIndex-1, 0, t); }" class="text-slate-400 hover:text-blue-500" :disabled="qIndex === 0" :class="{'opacity-30 cursor-not-allowed': qIndex === 0}">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                                            </button>
                                            <button type="button" @click="if(qIndex < lessonForm.questions.length-1) { var t = lessonForm.questions.splice(qIndex, 1)[0]; lessonForm.questions.splice(qIndex+1, 0, t); }" class="text-slate-400 hover:text-blue-500" :disabled="qIndex === lessonForm.questions.length-1" :class="{'opacity-30 cursor-not-allowed': qIndex === lessonForm.questions.length-1}">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                            </button>
                                            <button type="button" @click="removeQuestion(qIndex)" class="text-slate-400 hover:text-red-500 transition-colors mt-1">
                                               <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <!-- Choices Section (for MCQ/TrueFalse) -->
                                    <div class="p-4" x-show="['mcq', 'truefalse'].includes(question.type)">
                                        <div class="space-y-2">
                                            <template x-for="(choice, cIndex) in question.choices" :key="cIndex">
                                                <div class="flex items-center gap-3">
                                                    <input type="radio" :name="'correct_answer_'+qIndex" :checked="choice.is_correct" @change="question.choices.forEach(c => c.is_correct = false); choice.is_correct = true" class="text-blue-600 focus:ring-blue-500">
                                                    <input type="text" x-model="choice.text" placeholder="Tulis pilihan jawaban..." class="flex-1 text-sm rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-800 text-slate-900 dark:text-white">
                                                    <button type="button" @click="question.choices.splice(cIndex, 1)" class="text-slate-300 hover:text-red-500" x-show="question.type !== 'truefalse'">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                    </button>
                                                </div>
                                            </template>
                                        </div>
                                        <button type="button" @click="question.choices.push({id:null, text:'', is_correct:false})" class="mt-3 text-sm text-blue-600 hover:text-blue-700 font-medium flex items-center gap-1" x-show="question.type !== 'truefalse'">
                                            + Tambah Pilihan
                                        </button>
                                    </div>
                                    <div class="p-4" x-show="question.type === 'essay'">
                                        <p class="text-sm text-slate-500 italic">Jawaban esai akan dinilai secara manual oleh instruktur.</p>
                                    </div>
                                </div>
                            </template>
                             <div x-show="lessonForm.questions.length === 0" class="text-center py-8 text-slate-500 bg-slate-50 dark:bg-slate-800/50 rounded-lg border border-dashed border-slate-300 dark:border-slate-700">
                                Belum ada pertanyaan. Klik "Tambah Pertanyaan" untuk memulai.
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Right Column: Settings (1/3) -->
                <div class="lg:col-span-1 p-8 bg-slate-50 dark:bg-slate-800/30 space-y-6">
                    
                    <div class="flex items-center gap-2 text-slate-900 dark:text-white mb-4">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <h3 class="text-lg font-semibold">Lesson Settings</h3>
                    </div>

                    <!-- Position -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            Posisi dalam Modul
                        </label>
                        <input 
                            x-model.number="lessonForm.position" 
                            type="number" 
                            min="1" 
                            placeholder="1"
                            class="w-full px-4 py-2.5 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                        >
                        <p class="mt-1.5 text-xs text-slate-500 dark:text-slate-400">Urutan pelajaran ini dalam modul</p>
                    </div>

                    <!-- Duration -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            Durasi (menit)
                        </label>
                        <div class="relative">
                            <input 
                                x-model.number="lessonForm.duration_seconds" 
                                type="number" 
                                min="0" 
                                placeholder="15"
                                class="w-full px-4 py-2.5 pr-16 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                @input="lessonForm.duration_seconds = $event.target.value * 60"
                                :value="lessonForm.duration_seconds ? Math.floor(lessonForm.duration_seconds / 60) : ''"
                            >
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 text-sm text-slate-400">min</span>
                        </div>
                        <p class="mt-1.5 text-xs text-slate-500 dark:text-slate-400">Perkiraan waktu untuk menyelesaikan</p>
                    </div>

                    <!-- Toggles -->
                    <div class="space-y-4 pt-4 border-t border-slate-200 dark:border-slate-700">
                        <label class="flex items-center justify-between cursor-pointer group">
                            <div class="flex items-center gap-3">
                                <div class="p-2 rounded-lg bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 group-hover:bg-blue-200 dark:group-hover:bg-blue-900/50 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-slate-900 dark:text-white">Izinkan Unduhan</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">Biarkan siswa mengunduh konten ini</p>
                                </div>
                            </div>
                            <input 
                                x-model="lessonForm.is_downloadable" 
                                type="checkbox" 
                                class="w-5 h-5 text-blue-600 border-slate-300 rounded focus:ring-blue-500 focus:ring-offset-0 transition-all"
                            >
                        </label>

                        <label class="flex items-center justify-between cursor-pointer group">
                            <div class="flex items-center gap-3">
                                <div class="p-2 rounded-lg bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 group-hover:bg-green-200 dark:group-hover:bg-green-900/50 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-slate-900 dark:text-white">Pratinjau Gratis</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">Terlihat oleh pengguna yang belum terdaftar</p>
                                </div>
                            </div>
                            <input 
                                x-model="lessonForm.is_preview" 
                                type="checkbox" 
                                class="w-5 h-5 text-green-600 border-slate-300 rounded focus:ring-green-500 focus:ring-offset-0 transition-all"
                            >
                        </label>
                    </div>

                    <!-- Save Status Indicator -->
                    <div class="pt-6 border-t border-slate-200 dark:border-slate-700">
                        <div class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>Semua perubahan disimpan secara otomatis</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Actions -->
            <div class="px-8 py-5 bg-slate-50 dark:bg-slate-800 border-t border-slate-200 dark:border-slate-700 flex items-center justify-between">
                <button 
                    type="button" 
                    @click="showCreateLessonModal = false; showEditLessonModal = false"
                    class="px-6 py-2.5 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-lg font-medium transition-colors">
                    Batal
                </button>
                <div class="flex gap-3">
                    <button 
                        type="button"
                        class="px-6 py-2.5 border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 hover:bg-white dark:hover:bg-slate-800 rounded-lg font-medium transition-colors">
                        Simpan sebagai Draf
                    </button>
                    <button 
                        type="submit"
                        class="px-8 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-lg font-medium shadow-lg shadow-blue-500/30 hover:shadow-xl hover:shadow-blue-500/40 transition-all flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span x-text="showEditLessonModal ? 'Perbarui Pelajaran' : 'Buat Pelajaran'"></span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\maxcourse\resources\views\admin\modals\lesson-editor.blade.php ENDPATH**/ ?>