@extends('layouts.main')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50" x-data="forumComponent()">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
                Forum <span class="text-blue-600">Komunitas</span>
            </h1>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Berhubungan dengan sesama pembelajar, ajukan pertanyaan, dan bagikan pengetahuan Anda di komunitas belajar yang aktif ini.
            </p>
        </div>

        <!-- Course Selector & Create Button -->
        <div class="bg-white rounded-2xl shadow-xl p-8 mb-8 hover-lift border border-gray-100">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Filter berdasarkan Kursus</label>
                    <select x-model="filters.course_id" @change="loadThreads()" class="w-full md:w-64 px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Semua Kursus</option>
                        <template x-for="course in courses" :key="course.id">
                            <option :value="course.id" x-text="course.title"></option>
                        </template>
                    </select>
                </div>
                <div class="flex gap-4">
                    <button @click="openCreateThreadModal()" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold rounded-xl hover:from-blue-700 hover:to-purple-700 transition-all duration-300 transform hover:scale-105 shadow-lg">
                        <i class="fas fa-plus mr-2"></i>Buat Thread Baru
                    </button>
                </div>
            </div>
        </div>

        <!-- Threads List -->
        <div class="bg-white rounded-2xl shadow-xl hover-lift border border-gray-100">
            <div class="p-8 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-2xl font-bold text-gray-900">
                        <i class="fas fa-comments text-blue-600 mr-2"></i>Thread Forum
                    </h2>
                    <div class="flex items-center gap-4">
                        <select x-model="filters.sort" @change="loadThreads()" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="latest">Terbaru</option>
                            <option value="popular">Paling Populer</option>
                            <option value="unanswered">Belum Dijawab</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="divide-y divide-gray-200">
                <template x-for="thread in threads" :key="thread.id">
                    <div class="p-6 hover:bg-blue-50 cursor-pointer transition-colors" @click="viewThread(thread.id)">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <h3 class="text-lg font-bold text-gray-900" x-text="thread.title"></h3>
                                    <span x-show="thread.is_sticky" class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded">Sticky</span>
                                </div>
                                <p class="text-gray-600 text-sm mb-3" x-text="thread.body"></p>
                                <div class="flex items-center gap-4 text-xs text-gray-500">
                                    <span><i class="fas fa-user mr-1"></i><span x-text="thread.author"></span></span>
                                    <span><i class="fas fa-tag mr-1"></i><span x-text="thread.course"></span></span>
                                    <span><i class="fas fa-clock mr-1"></i><span x-text="thread.created_at"></span></span>
                                    <span><i class="fas fa-reply mr-1"></i><span x-text="thread.replies + ' balasan'"></span></span>
                                </div>
                            </div>
                            <div class="ml-4 text-right">
                                <div class="text-2xl font-bold text-blue-600" x-text="thread.replies"></div>
                                <div class="text-xs text-gray-500">Balasan</div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Empty State -->
            <div class="text-center py-16" x-show="threads.length === 0 && !loading">
                <div class="w-24 h-24 bg-gradient-to-r from-gray-100 to-gray-200 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-comments text-4xl text-gray-400"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-3">Belum ada thread</h3>
                <p class="text-gray-600 mb-6 max-w-md mx-auto">
                    Jadilah yang pertama memulai diskusi! Bagikan pemikiran Anda, ajukan pertanyaan, atau bantu pengguna lain di komunitas.
                </p>
                <button @click="openCreateThreadModal()" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold rounded-xl hover:from-blue-700 hover:to-purple-700 transition-all duration-300 transform hover:scale-105 shadow-lg">
                    <i class="fas fa-plus mr-2"></i>Mulai Percakapan
                </button>
            </div>

            <!-- Loading State -->
            <div class="text-center py-16" x-show="loading">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
                <p class="text-gray-600">Memuat diskusi...</p>
            </div>
        </div>

        <!-- Community Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-12">
            <div class="bg-white rounded-2xl shadow-xl p-8 hover-lift border border-gray-100 text-center">
                <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-users text-white text-2xl"></i>
                </div>
                <div class="text-3xl font-bold text-gray-900 mb-2" x-text="stats.members || 0"></div>
                <div class="text-gray-600">Anggota Aktif</div>
            </div>
            <div class="bg-white rounded-2xl shadow-xl p-8 hover-lift border border-gray-100 text-center">
                <div class="w-16 h-16 bg-gradient-to-r from-green-500 to-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-comments text-white text-2xl"></i>
                </div>
                <div class="text-3xl font-bold text-gray-900 mb-2" x-text="stats.threads || 0"></div>
                <div class="text-gray-600">Total Thread</div>
            </div>
            <div class="bg-white rounded-2xl shadow-xl p-8 hover-lift border border-gray-100 text-center">
                <div class="w-16 h-16 bg-gradient-to-r from-purple-500 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-reply text-white text-2xl"></i>
                </div>
                <div class="text-3xl font-bold text-gray-900 mb-2" x-text="stats.replies || 0"></div>
                <div class="text-gray-600">Total Balasan</div>
            </div>
        </div>
    </div>

    <!-- Create Thread Modal -->
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" x-show="showCreateModal" @click.self="showCreateModal = false">
        <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-2xl w-full mx-4">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Buat Thread Baru</h2>
            <form @submit.prevent="createThread()" class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Kursus</label>
                    <select x-model="newThread.course_id" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                        <option value="">Pilih Kursus</option>
                        <template x-for="course in courses" :key="course.id">
                            <option :value="course.id" x-text="course.title"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Judul</label>
                    <input x-model="newThread.title" type="text" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Judul thread..." required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Isi Pesan</label>
                    <textarea x-model="newThread.body" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent" rows="6" placeholder="Tulis pesan Anda..." required></textarea>
                </div>
                <div class="flex gap-3 justify-end">
                    <button type="button" @click="showCreateModal = false" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50">Batal</button>
                    <button type="submit" :disabled="creating" class="px-6 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 disabled:opacity-50">
                        <span x-show="!creating">Buat Thread</span>
                        <span x-show="creating"><i class="fas fa-spinner fa-spin mr-2"></i>Membuat...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Error/Success Modal -->
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" x-show="showModal" @click.self="showModal = false">
        <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-md w-full mx-4 transform transition-all" :class="modalType === 'success' ? 'border-l-4 border-green-500' : 'border-l-4 border-red-500'">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div :class="modalType === 'success' ? 'w-12 h-12 bg-green-100 rounded-full flex items-center justify-center' : 'w-12 h-12 bg-red-100 rounded-full flex items-center justify-center'">
                        <i :class="modalType === 'success' ? 'fas fa-check text-green-600 text-xl' : 'fas fa-exclamation text-red-600 text-xl'"></i>
                    </div>
                    <h3 class="text-lg font-bold" :class="modalType === 'success' ? 'text-green-700' : 'text-red-700'" x-text="modalTitle"></h3>
                </div>
                <button @click="showModal = false" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <p class="text-gray-700 mb-6" x-text="modalMessage"></p>
            <template x-if="Object.keys(modalErrors).length > 0">
                <div class="bg-red-50 rounded-lg p-4 mb-6 border border-red-200">
                    <p class=\"text-sm font-semibold text-red-700 mb-3\">Detail Kesalahan Validasi:</p>
                    <ul class="space-y-2">
                        <template x-for="(msgs, field) in modalErrors" :key="field">
                            <li class="text-sm text-red-600">
                                <span class="font-semibold" x-text="field"></span>:
                                <span x-text="Array.isArray(msgs) ? msgs[0] : msgs"></span>
                            </li>
                        </template>
                    </ul>
                </div>
            </template>
            <div class="flex gap-3 justify-end">
                <button @click="showModal = false" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-medium">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
function forumComponent() {
    return {
        threads: [],
        courses: [],
        stats: {},
        loading: true,
        creating: false,
        showCreateModal: false,
        showModal: false,
        modalType: 'error',
        modalTitle: '',
        modalMessage: '',
        modalErrors: {},
        filters: {
            course_id: '',
            sort: 'latest'
        },
        newThread: {
            course_id: '',
            title: '',
            body: ''
        },

        async init() {
            await this.loadCourses();
            await this.loadThreads();
            await this.loadStats();
        },

        async loadCourses() {
            try {
                const response = await fetch('/forum/api/courses');
                const data = await response.json();
                if (data.success) {
                    this.courses = data.courses;
                }
            } catch (error) {
                console.error('Error loading courses:', error);
            }
        },

        async loadThreads() {
            this.loading = true;
            try {
                const params = new URLSearchParams();
                if (this.filters.course_id) params.append('course_id', this.filters.course_id);
                params.append('sort', this.filters.sort);

                const response = await fetch(`/forum/api/threads?${params}`);
                const data = await response.json();
                if (data.success) {
                    this.threads = data.threads;
                }
            } catch (error) {
                console.error('Error loading threads:', error);
            } finally {
                this.loading = false;
            }
        },

        async loadStats() {
            try {
                const response = await fetch('/forum/api/stats');
                const data = await response.json();
                if (data.success) {
                    this.stats = data.stats;
                }
            } catch (error) {
                console.error('Error loading stats:', error);
            }
        },

        openCreateThreadModal() {
            if (!@json(Auth::check())) {
                window.location.href = '/login';
                return;
            }
            this.showCreateModal = true;
        },

        async createThread() {
            this.creating = true;
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                
                console.log('Creating thread with data:', this.newThread);
                console.log('CSRF Token present:', !!csrfToken);
                
                const response = await fetch('/forum/api/threads', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken || '',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(this.newThread)
                });

                const data = await response.json();
                console.log('Response status:', response.status, 'Data:', data);
                
                if (response.ok) {
                    this.showCreateModal = false;
                    this.newThread = { course_id: '', title: '', body: '' };
                    await this.loadThreads();
                    this.showSuccessModal('Sukses!', 'Thread berhasil dibuat');
                } else {
                    const errorMsg = data.message || 'Gagal membuat thread';
                    const errors = data.errors || {};
                    console.error('Full API response:', data);
                    this.showErrorModal('Validasi Gagal', errorMsg, errors);
                }
            } catch (error) {
                console.error('Error creating thread:', error);
                this.showErrorModal('Error', 'Terjadi kesalahan saat membuat thread: ' + error.message, {});
            } finally {
                this.creating = false;
            }
        },

        showSuccessModal(title, message) {
            this.modalType = 'success';
            this.modalTitle = title;
            this.modalMessage = message;
            this.modalErrors = {};
            this.showModal = true;
        },

        showErrorModal(title, message, errors = {}) {
            this.modalType = 'error';
            this.modalTitle = title;
            this.modalMessage = message;
            this.modalErrors = errors;
            this.showModal = true;
        },

        viewThread(threadId) {
            window.location.href = `/forums/${threadId}`;
        }
    }
}
</script>
@endsection