<!-- Module Manager Modal -->
<div x-show="showModuleManager" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" @click.self="showModuleManager = false" x-cloak>
    <div class="bg-white rounded-lg max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="px-6 py-4 border-b border-gray-200 sticky top-0 bg-white flex justify-between items-center">
            <div>
                <h3 class="text-lg font-medium text-gray-900">Kelola Modul Kursus</h3>
                <p class="text-sm text-gray-500 mt-1" x-show="selectedCourse" x-text="`Kursus: ${selectedCourse?.title}`"></p>
            </div>
            <button @click="showModuleManager = false" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <div class="p-6">
            <!-- Add Module Button -->
            <div class="mb-6">
                <button @click="openCreateModuleModal()" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    <i class="fas fa-plus mr-2"></i>Tambah Modul
                </button>
            </div>

            <!-- Modules List -->
            <div class="space-y-4">
                <template x-if="modules.length === 0">
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-inbox text-4xl mb-4"></i>
                        <p>Belum ada modul. Buat satu untuk memulai!</p>
                    </div>
                </template>

                <template x-for="module in modules" :key="module.id">
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-5 hover:shadow-lg transition-shadow dark:bg-gray-800 bg-white">
                        <div class="flex justify-between items-start gap-4">
                            <div class="flex-1 min-w-0">
                                <!-- Header -->
                                <div class="flex items-start gap-3 mb-2">
                                    <span class="px-3 py-1 bg-indigo-100 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-200 rounded-full text-xs font-semibold flex-shrink-0" x-text="`#${module.position}`"></span>
                                    <div class="min-w-0">
                                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white truncate" x-text="module.title"></h4>
                                    </div>
                                </div>
                                
                                <!-- Description -->
                                <template x-if="module.description">
                                    <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2 mb-3" x-text="module.description"></p>
                                </template>
                                
                                <!-- Lessons Counter -->
                                <div class="flex items-center gap-2">
                                    <button @click="openLessonManager(module)" class="inline-flex items-center px-3 py-1.5 bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-200 text-xs font-medium rounded-md hover:bg-green-200 dark:hover:bg-green-800 transition-colors">
                                        <i class="fas fa-book mr-1.5"></i>
                                        <span x-text="`${module.lessons_count || 0} pelajaran`"></span>
                                        <i class="fas fa-chevron-right ml-1 text-xs"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Actions -->
                            <div class="flex gap-1 flex-shrink-0 whitespace-nowrap">
                                <button @click="openEditModuleModal(module)" class="px-3 py-2 text-sm bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-200 rounded hover:bg-blue-200 dark:hover:bg-blue-800 transition-colors" title="Edit module">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button @click="deleteModule(module.id)" class="px-3 py-2 text-sm bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-200 rounded hover:bg-red-200 dark:hover:bg-red-800 transition-colors" title="Delete module">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Module Actions -->
            <div class="mt-6 pt-6 border-t border-gray-200 flex justify-end space-x-3">
                <button @click="showModuleManager = false" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Create Module Modal -->
<div x-show="showCreateModuleModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" @click.self="showCreateModuleModal = false" x-cloak>
    <div class="bg-white rounded-lg max-w-md w-full mx-4">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Buat Modul Baru</h3>
        </div>
        <div class="p-6">
            <form @submit.prevent="saveModule()">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Module Title *</label>
                        <input x-model="moduleForm.title" type="text" required class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea x-model="moduleForm.description" rows="3" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Position</label>
                        <input x-model.number="moduleForm.position" type="number" min="1" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2">
                    </div>
                </div>
                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" @click="showCreateModuleModal = false" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Buat Modul
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Module Modal -->
<div x-show="showEditModuleModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" @click.self="showEditModuleModal = false" x-cloak>
    <div class="bg-white rounded-lg max-w-md w-full mx-4">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Edit Modul</h3>
        </div>
        <div class="p-6">
            <form @submit.prevent="saveModule()">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Module Title *</label>
                        <input x-model="moduleForm.title" type="text" required class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea x-model="moduleForm.description" rows="3" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Position</label>
                        <input x-model.number="moduleForm.position" type="number" min="1" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2">
                    </div>
                </div>
                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" @click="showEditModuleModal = false" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Perbarui Modul
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Lesson Manager Modal -->
<div x-show="showLessonManager" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" @click.self="showLessonManager = false" x-cloak>
    <div class="bg-white dark:bg-gray-900 rounded-lg max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700 sticky top-0 bg-white dark:bg-gray-900 flex justify-between items-start">
            <div class="flex-1">
                <div class="flex items-center gap-2 mb-1">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Manajemen Pelajaran</h3>
                    <span class="px-2.5 py-0.5 bg-purple-100 dark:bg-purple-900 text-purple-700 dark:text-purple-200 text-xs font-medium rounded-full">
                        <i class="fas fa-layer-group mr-1"></i>
                        <span x-text="selectedModule?.title || 'Module'"></span>
                    </span>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400" x-text="`${lessons.length} pelajaran`"></p>
            </div>
            <button @click="showLessonManager = false" class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-400">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <div class="p-6">
            <!-- Add Lesson Button -->
            <div class="mb-6">
                <button @click="openCreateLessonModal()" class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-md hover:from-blue-700 hover:to-blue-800 transition-all shadow-sm hover:shadow-md font-medium">
                    <i class="fas fa-plus mr-2"></i>Buat Pelajaran Baru
                </button>
            </div>

            <!-- Lessons List -->
            <div class="space-y-4">
                <template x-if="lessons.length === 0">
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-file-alt text-4xl mb-4"></i>
                        <p>Belum ada pelajaran. Buat satu untuk memulai!</p>
                    </div>
                </template>

                <template x-for="lesson in lessons" :key="lesson.id">
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:shadow-md transition-shadow dark:bg-gray-800">
                        <div class="flex justify-between items-start gap-3">
                            <div class="flex-1 min-w-0">
                                <!-- Header Row -->
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="px-3 py-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-full text-xs font-medium" x-text="`#${lesson.position}`"></span>
                                    <h4 class="text-base font-semibold text-gray-900 dark:text-white truncate" x-text="lesson.title"></h4>
                                    <span class="px-2 py-1 text-xs font-medium rounded-full flex-shrink-0" :class="{
                                        'bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200': lesson.content_type === 'video',
                                        'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200': lesson.content_type === 'article',
                                        'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200': lesson.content_type === 'pdf',
                                        'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200': lesson.content_type === 'audio',
                                        'bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200': lesson.content_type === 'quiz',
                                        'bg-orange-100 dark:bg-orange-900 text-orange-800 dark:text-orange-200': lesson.content_type === 'other',
                                    }">
                                        <i :class="{
                                            'fas fa-video': lesson.content_type === 'video',
                                            'fas fa-align-left': lesson.content_type === 'article',
                                            'fas fa-file-pdf': lesson.content_type === 'pdf',
                                            'fas fa-music': lesson.content_type === 'audio',
                                            'fas fa-question-circle': lesson.content_type === 'quiz',
                                            'fas fa-file': lesson.content_type === 'other',
                                        }" class="mr-1"></i>
                                        <span x-text="lesson.content_type.charAt(0).toUpperCase() + lesson.content_type.slice(1)"></span>
                                    </span>
                                </div>
                                
                                <!-- Description Preview -->
                                <template x-if="lesson.content">
                                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400 line-clamp-2" x-text="lesson.content.substring(0, 80) + (lesson.content.length > 80 ? '...' : '')"></p>
                                </template>
                                
                                <!-- Metadata -->
                                <div class="mt-3 flex flex-wrap gap-2">
                                    <template x-if="lesson.duration_seconds">
                                        <span class="inline-flex items-center px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-xs font-medium rounded">
                                            <i class="fas fa-clock mr-1"></i>
                                            <span x-text="`${Math.floor(lesson.duration_seconds / 60)}m`"></span>
                                        </span>
                                    </template>
                                    <template x-if="lesson.is_downloadable">
                                        <span class="inline-flex items-center px-2 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 text-xs font-medium rounded">
                                            <i class="fas fa-download mr-1"></i>Dapat Diunduh
                                        </span>
                                    </template>
                                    <template x-if="lesson.media_url">
                                        <span class="inline-flex items-center px-2 py-1 bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200 text-xs font-medium rounded">
                                            <i class="fas fa-link mr-1"></i>Media
                                        </span>
                                    </template>
                                </div>
                            </div>
                            
                            <!-- Actions -->
                            <div class="flex gap-1 flex-shrink-0">
                                <button @click="openEditLessonModal(lesson)" class="px-3 py-2 text-sm bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-200 rounded hover:bg-blue-200 dark:hover:bg-blue-800 transition-colors" title="Edit lesson">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button @click="deleteLesson(lesson.id)" class="px-3 py-2 text-sm bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-200 rounded hover:bg-red-200 dark:hover:bg-red-800 transition-colors" title="Delete lesson">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Lesson Actions -->
            <div class="mt-6 pt-6 border-t border-gray-200 flex justify-end space-x-3">
                <button @click="showLessonManager = false" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Professional Lesson Editor (Create & Edit) -->
<?php echo $__env->make('admin.modals.lesson-editor', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php /**PATH C:\xampp\htdocs\maxcourse\resources\views\admin\modals\module-manager.blade.php ENDPATH**/ ?>