

<?php $__env->startSection('title', 'My Learning Journey'); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50">
    <!-- Hero Section -->
    <div class="gradient-bg text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center animate-fade-in">
                <h1 class="text-5xl md:text-6xl font-bold mb-4">
                    <span class="text-yellow-300">Perjalanan Belajar</span> Saya
                </h1>
                <p class="text-xl md:text-2xl text-blue-100 mb-8 max-w-3xl mx-auto">
                    Lacak kemajuan Anda, lanjutkan belajar, dan capai tujuan Anda dengan dasbor pribadi kami
                </p>

                <!-- Quick Stats -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8 max-w-4xl mx-auto">
                    <div class="text-center">
                        <div class="text-3xl font-bold mb-2" x-data="statsComponent()" x-text="stats.enrolled"></div>
                        <div class="text-blue-100">Kursus Terdaftar</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold mb-2" x-text="stats.completed"></div>
                        <div class="text-blue-100">Selesai</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold mb-2" x-text="stats.inProgress"></div>
                        <div class="text-blue-100">Sedang Berjalan</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold mb-2" x-text="stats.certificates"></div>
                        <div class="text-blue-100">Sertifikat</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Filters and Actions -->
        <div class="bg-white rounded-2xl shadow-xl p-6 mb-8 hover-lift">
            <div class="flex flex-col lg:flex-row gap-6 items-center justify-between">
                <div class="flex flex-col lg:flex-row gap-4 items-center">
                    <!-- Status Filter -->
                    <div class="w-full lg:w-48">
                        <select class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white"
                                x-model="filters.status">
                            <option value="">Semua Kursus</option>
                            <option value="in-progress">Sedang Berjalan</option>
                            <option value="completed">Selesai</option>
                            <option value="not-started">Belum Dimulai</option>
                        </select>
                    </div>

                    <!-- Sort -->
                    <div class="w-full lg:w-48">
                        <select class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white"
                                x-model="filters.sort">
                            <option value="recent">Baru Diakses</option>
                            <option value="progress">Kemajuan</option>
                            <option value="name">Nama Kursus</option>
                            <option value="enrolled">Tanggal Daftar</option>
                        </select>
                    </div>
                </div>

                <div class="flex gap-4">
                    <a href="/explore-courses" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold rounded-xl hover:from-blue-700 hover:to-purple-700 transition-all duration-300 transform hover:scale-105 shadow-lg">
                        <i class="fas fa-plus mr-2"></i>Jelajahi Kursus
                    </a>
                    <a href="/certificates" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-600 to-teal-600 text-white font-semibold rounded-xl hover:from-green-700 hover:to-teal-700 transition-all duration-300 transform hover:scale-105 shadow-lg">
                        <i class="fas fa-certificate mr-2"></i>Sertifikat Saya
                    </a>
                </div>
            </div>
        </div>

        <!-- Course Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12"
             x-data="myCoursesComponent()"
             x-init="init()">

            <!-- Loading State -->
            <template x-if="loading">
                <template x-for="n in 6" :key="n">
                    <div class="bg-white rounded-2xl shadow-xl overflow-hidden animate-pulse">
                    <div class="h-48 bg-gradient-to-r from-gray-200 to-gray-300"></div>
                    <div class="p-6">
                        <div class="h-6 bg-gray-200 rounded mb-3"></div>
                        <div class="h-4 bg-gray-200 rounded mb-2"></div>
                        <div class="h-4 bg-gray-200 rounded w-3/4 mb-4"></div>
                        <div class="flex justify-between items-center">
                            <div class="h-8 bg-gray-200 rounded w-16"></div>
                            <div class="h-10 bg-gray-200 rounded-lg w-24"></div>
                        </div>
                    </div>
                    </div>
                </template>
            </template>

            <!-- Course Cards -->
            <template x-for="enrollment in filteredEnrollments" :key="enrollment.id">
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden hover-lift group">
                    <!-- Course Image -->
                    <div class="relative overflow-hidden">
                        <img :src="enrollment.course.thumbnail_url ? '/storage/' + enrollment.course.thumbnail_url : '/images/course-placeholder.jpg'"
                             :alt="enrollment.course.title"
                             class="w-full h-48 object-cover group-hover:scale-110 transition-transform duration-500">
                        <div class="absolute top-4 right-4">
                            <span class="px-3 py-1 bg-white bg-opacity-90 text-gray-800 text-xs font-semibold rounded-full"
                                  :class="getStatusColor(enrollment.progress_percent)"
                                  x-text="getStatusText(enrollment.progress_percent)"></span>
                        </div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black via-transparent to-transparent opacity-0 group-hover:opacity-60 transition-opacity duration-300"></div>
                        <div class="absolute bottom-4 left-4 right-4 transform translate-y-full group-hover:translate-y-0 transition-transform duration-300">
                            <div class="flex items-center space-x-2 text-white">
                                <i class="fas fa-play-circle text-xl"></i>
                                <span class="text-sm font-medium">Lanjutkan Belajar</span>
                            </div>
                        </div>
                    </div>

                    <!-- Course Content -->
                    <div class="p-6">
                        <!-- Title -->
                        <h3 class="text-xl font-bold text-gray-900 mb-3 line-clamp-2 group-hover:text-blue-700 transition-colors"
                            x-text="enrollment.course.title"></h3>

                        <!-- Description -->
                        <p class="text-gray-600 text-sm mb-4 line-clamp-2" x-text="enrollment.course.short_description"></p>

                        <!-- Progress -->
                        <div class="mb-4">
                            <div class="flex justify-between text-sm text-gray-600 mb-2">
                                <span>Kemajuan</span>
                                <span x-text="Math.round(enrollment.progress_percent) + '%'"></span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3">
                                <div class="bg-gradient-to-r from-blue-500 to-purple-600 h-3 rounded-full transition-all duration-500"
                                     :style="'width: ' + enrollment.progress_percent + '%'"></div>
                            </div>
                        </div>

                        <!-- Enrollment Info -->
                        <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
                            <span x-text="'Terdaftar: ' + formatDate(enrollment.enrolled_at)"></span>
                            <span x-text="enrollment.course.duration_minutes ? enrollment.course.duration_minutes + ' menit' : 'Mandiri'"></span>
                        </div>

                        <!-- Actions -->
                        <div class="flex gap-3">
                            <a :href="'/learn/' + enrollment.course.slug"
                               class="flex-1 inline-flex items-center justify-center px-4 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold rounded-xl hover:from-blue-700 hover:to-purple-700 transition-all duration-300 transform hover:scale-105 shadow-lg">
                                <i class="fas fa-play mr-2"></i>
                                <span x-text="enrollment.progress_percent > 0 ? 'Lanjutkan' : 'Mulai'"></span>
                            </a>
                            <button class="p-3 bg-gray-100 text-gray-600 rounded-xl hover:bg-gray-200 transition-colors"
                                    @click="toggleBookmark(enrollment.course.id)">
                                <i class="fas fa-bookmark" :class="isBookmarked(enrollment.course.id) ? 'text-yellow-500' : ''"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Empty State -->
            <template x-if="!loading && filteredEnrollments.length === 0 && enrollments.length > 0">
                <div class="col-span-full text-center py-16">
                    <div class="w-24 h-24 bg-gradient-to-r from-gray-100 to-gray-200 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-filter text-4xl text-gray-400"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">Tidak ada kursus yang sesuai filter</h3>
                    <p class="text-gray-600 mb-6 max-w-md mx-auto">
                        Coba sesuaikan filter Anda untuk melihat lebih banyak kursus.
                    </p>
                    <button class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold rounded-xl hover:from-blue-700 hover:to-purple-700 transition-all duration-300"
                            @click="clearFilters">
                        <i class="fas fa-times mr-2"></i>Hapus Filter
                    </button>
                </div>
            </template>

            <!-- No Courses State -->
            <template x-if="!loading && enrollments.length === 0">
                <div class="col-span-full text-center py-20">
                    <div class="w-32 h-32 bg-gradient-to-r from-blue-100 to-purple-100 rounded-full flex items-center justify-center mx-auto mb-8">
                        <i class="fas fa-graduation-cap text-6xl text-blue-600"></i>
                    </div>
                    <h3 class="text-3xl font-bold text-gray-900 mb-4">Start Your Learning Journey</h3>
                    <p class="text-xl text-gray-600 mb-8 max-w-2xl mx-auto">
                        Anda belum mendaftar kursus apa pun. Jelajahi koleksi kursus lengkap kami dan mulailah perjalanan Anda menuju kesuksesan.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="/courses" class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-bold text-lg rounded-full hover:from-blue-700 hover:to-purple-700 transition-all duration-300 transform hover:scale-105 shadow-lg">
                            <i class="fas fa-search mr-3"></i>Cari Kursus
                        </a>
                        <a href="/categories" class="inline-flex items-center px-8 py-4 bg-transparent border-2 border-blue-600 text-blue-600 font-bold text-lg rounded-full hover:bg-blue-600 hover:text-white transition-all duration-300 transform hover:scale-105">
                            <i class="fas fa-tags mr-3"></i>Jelajahi Kategori
                        </a>
                    </div>
                </div>
            </template>
        </div>

        <!-- Learning Insights -->
        <template x-if="!loading && enrollments.length > 0">
            <div class="bg-white rounded-2xl shadow-xl p-8 hover-lift">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">
                        <i class="fas fa-chart-line text-blue-600 mr-2"></i>Wawasan Belajar
                    </h2>
                    <a href="/analytics" class="text-blue-600 hover:text-blue-800 font-medium">
                        Lihat Analisis Detail <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Study Streak -->
                    <div class="bg-gradient-to-br from-orange-50 to-red-50 p-6 rounded-xl border border-orange-100">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-gradient-to-r from-orange-500 to-red-500 rounded-xl flex items-center justify-center">
                                <i class="fas fa-fire text-white text-xl"></i>
                            </div>
                            <span class="text-2xl font-bold text-orange-600" x-text="stats.streak"></span>
                        </div>
                        <h3 class="font-semibold text-gray-900 mb-1">Runtutan Hari</h3>
                        <p class="text-sm text-gray-600">Pertahankan! 🔥</p>
                    </div>

                    <!-- Weekly Goal -->
                    <div class="bg-gradient-to-br from-green-50 to-emerald-50 p-6 rounded-xl border border-green-100">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-emerald-500 rounded-xl flex items-center justify-center">
                                <i class="fas fa-target text-white text-xl"></i>
                            </div>
                            <span class="text-2xl font-bold text-green-600" x-text="stats.weeklyProgress + '%'"></span>
                        </div>
                        <h3 class="font-semibold text-gray-900 mb-1">Target Mingguan</h3>
                        <p class="text-sm text-gray-600">Selesaikan 5 pelajaran minggu ini</p>
                    </div>

                    <!-- Total Time -->
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 p-6 rounded-xl border border-blue-100">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-xl flex items-center justify-center">
                                <i class="fas fa-clock text-white text-xl"></i>
                            </div>
                            <span class="text-2xl font-bold text-blue-600" x-text="stats.totalTime"></span>
                        </div>
                        <h3 class="font-semibold text-gray-900 mb-1">Waktu Belajar</h3>
                        <p class="text-sm text-gray-600">Bulan ini</p>
                    </div>
                </div>
            </div>
        </template>
    </div>
</div>

<script>
function myCoursesComponent() {
    return {
        enrollments: [],
        filteredEnrollments: [],
        loading: true,
        filters: {
            status: '',
            sort: 'recent'
        },
        bookmarks: [],

        async init() {
            await this.fetchEnrollments();
            this.applyFilters();
        },

        async fetchEnrollments() {
            this.loading = true;
            try {
                const response = await fetch('/student/api/enrollments', {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    }
                });

                if (!response.ok) {
                    throw new Error('Failed to fetch enrollments');
                }

                const data = await response.json();
                this.enrollments = data;
                this.filteredEnrollments = [...this.enrollments];
            } catch (error) {
                console.error('Error fetching enrollments:', error);
            } finally {
                this.loading = false;
            }
        },


        applyFilters() {
            let filtered = [...this.enrollments];

            // Status filter
            if (this.filters.status) {
                switch (this.filters.status) {
                    case 'completed':
                        filtered = filtered.filter(e => e.progress_percent >= 100);
                        break;
                    case 'in-progress':
                        filtered = filtered.filter(e => e.progress_percent > 0 && e.progress_percent < 100);
                        break;
                    case 'not-started':
                        filtered = filtered.filter(e => e.progress_percent === 0);
                        break;
                }
            }

            // Sort
            switch (this.filters.sort) {
                case 'progress':
                    filtered.sort((a, b) => b.progress_percent - a.progress_percent);
                    break;
                case 'name':
                    filtered.sort((a, b) => a.course.title.localeCompare(b.course.title));
                    break;
                case 'enrolled':
                    filtered.sort((a, b) => new Date(b.enrolled_at) - new Date(a.enrolled_at));
                    break;
                case 'recent':
                default:
                    // Already sorted by recent access (assuming API returns in this order)
                    break;
            }

            this.filteredEnrollments = filtered;
        },

        getStatusText(progress) {
            if (progress >= 100) return 'Selesai';
            if (progress > 0) return 'Sedang Berjalan';
            return 'Belum Dimulai';
        },

        getStatusColor(progress) {
            if (progress >= 100) return 'bg-green-100 text-green-800';
            if (progress > 0) return 'bg-blue-100 text-blue-800';
            return 'bg-gray-100 text-gray-800';
        },

        formatDate(dateString) {
            return new Date(dateString).toLocaleDateString();
        },

        toggleBookmark(courseId) {
            const index = this.bookmarks.indexOf(courseId);
            if (index > -1) {
                this.bookmarks.splice(index, 1);
            } else {
                this.bookmarks.push(courseId);
            }
        },

        isBookmarked(courseId) {
            return this.bookmarks.includes(courseId);
        },

        clearFilters() {
            this.filters = {
                status: '',
                sort: 'recent'
            };
            this.applyFilters();
        }
    }
}

function statsComponent() {
    return {
        stats: {
            enrolled: 0,
            completed: 0,
            inProgress: 0,
            certificates: 0,
            streak: 0,
            weeklyProgress: 0,
            totalTime: '0h'
        },

        init() {
            // Mock stats - in real app, fetch from API
            this.stats = {
                enrolled: 12,
                completed: 8,
                inProgress: 4,
                certificates: 6,
                streak: 7,
                weeklyProgress: 75,
                totalTime: '24h'
            };
        }
    }
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\maxcourse\resources\views\my-courses\index.blade.php ENDPATH**/ ?>