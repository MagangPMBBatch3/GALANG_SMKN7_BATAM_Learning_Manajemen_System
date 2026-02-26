@extends('layouts.main')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-900 dark:to-slate-800">
    <!-- Header -->
    <div class="bg-white dark:bg-slate-800 shadow-sm border-b border-slate-200 dark:border-slate-700">
        <div class="max-w-7xl mx-auto px-4 py-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-slate-900 dark:text-white">Dashboard</h1>
                    <p class="text-slate-600 dark:text-slate-400 mt-1">Selamat datang kembali, {{ Auth::user()->name }}! 👋</p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-slate-600 dark:text-slate-400">Anggota sejak {{ Auth::user()->created_at->format('M Y') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 py-8" x-data="studentDashboard()" x-init="init()">
        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <!-- Courses Enrolled -->
            <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-600 dark:text-slate-400">Kursus Diikuti</p>
                        <p class="text-3xl font-bold text-slate-900 dark:text-white mt-1" x-text="enrolledCoursesCount">0</p>
                    </div>
                    <div class="bg-blue-100 dark:bg-blue-900 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C6.5 6.253 2 10.998 2 16.5S6.5 27 12 27s10-4.745 10-10.5S17.5 6.253 12 6.253z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- In Progress -->
            <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6 border-l-4 border-amber-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-600 dark:text-slate-400">Sedang Berjalan</p>
                        <p class="text-3xl font-bold text-slate-900 dark:text-white mt-1" x-text="inProgressCount">0</p>
                    </div>
                    <div class="bg-amber-100 dark:bg-amber-900 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Completed Courses -->
            <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-600 dark:text-slate-400">Selesai</p>
                        <p class="text-3xl font-bold text-slate-900 dark:text-white mt-1" x-text="completedCoursesCount">0</p>
                    </div>
                    <div class="bg-green-100 dark:bg-green-900 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Badges Earned -->
            <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6 border-l-4 border-purple-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-600 dark:text-slate-400">Lencana Diperoleh</p>
                        <p class="text-3xl font-bold text-slate-900 dark:text-white mt-1" x-text="badgesCount">0</p>
                    </div>
                    <div class="bg-purple-100 dark:bg-purple-900 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Two Column Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content (Left) -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Courses In Progress -->
                <div class="bg-white dark:bg-slate-800 rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between">
                        <h2 class="text-xl font-bold text-slate-900 dark:text-white">Kursus Anda</h2>
                        <a href="/explore-courses" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">Lihat Lebih Banyak →</a>
                    </div>
                    <div class="p-6">
                        <template x-if="enrollments.length === 0">
                            <div class="text-center py-12">
                                <svg class="w-12 h-12 text-slate-300 dark:text-slate-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m0 0h6"></path>
                                </svg>
                                <p class="text-slate-500 dark:text-slate-400 mb-4">Belum ada kursus. Mulai belajar hari ini!</p>
                                <a href="/explore-courses" class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                                    Jelajahi Kursus
                                </a>
                            </div>
                        </template>
                        <template x-if="enrollments.length > 0">
                            <div class="space-y-4">
                                <template x-for="enrollment in enrollments.slice(0, 5)" :key="enrollment.id">
                                    <div class="bg-slate-50 dark:bg-slate-700 rounded-lg p-4 hover:shadow-md transition">
                                        <div class="flex gap-4">
                                            <!-- Course Thumbnail -->
                                            <div class="flex-shrink-0">
                                                <div class="w-20 h-20 bg-gradient-to-br from-blue-400 to-blue-600 rounded-lg flex items-center justify-center text-white text-2xl font-bold">
                                                    <span x-text="enrollment.course.title.charAt(0)"></span>
                                                </div>
                                            </div>
                                            <!-- Course Info -->
                                            <div class="flex-grow">
                                                <h3 class="font-semibold text-slate-900 dark:text-white" x-text="enrollment.course.title"></h3>
                                                <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">
                                                    <span x-text="enrollment.course.modules_count || 0"></span> modul •
                                                    <span x-text="enrollment.course.lessons_count || 0"></span> pelajaran
                                                </p>
                                                <!-- Progress Bar -->
                                                <div class="mt-3">
                                                    <div class="flex items-center justify-between mb-1">
                                                        <span class="text-xs font-medium text-slate-600 dark:text-slate-400">Kemajuan</span>
                                                        <span class="text-xs font-bold text-slate-900 dark:text-white" x-text="Math.round(enrollment.progress_percent || 0) + '%'"></span>
                                                    </div>
                                                    <div class="w-full bg-slate-200 dark:bg-slate-600 rounded-full h-2">
                                                        <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-2 rounded-full transition-all" :style="`width: ${enrollment.progress_percent || 0}%`"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Action -->
                                            <div class="flex items-center">
                                                <template x-if="enrollment.progress_percent >= 100 || enrollment.status === 'completed'">
                                                    <a :href="`/student/api/certificate/generate/${enrollment.id}`" 
                                                       target="_blank"
                                                       class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition flex items-center">
                                                        <i class="fas fa-certificate mr-2"></i> Sertifikat
                                                    </a>
                                                </template>
                                                <template x-if="enrollment.progress_percent < 100 && enrollment.status !== 'completed'">
                                                    <a :href="`/learn/${enrollment.course.slug}`" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                                                        Lanjutkan
                                                    </a>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Sidebar (Right) -->
            <div class="space-y-6">
                <!-- Recent Notifications -->
                <div class="bg-white dark:bg-slate-800 rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700">
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white">Notifikasi</h2>
                    </div>
                    <div class="p-6">
                        <template x-if="notifications.length === 0">
                            <p class="text-sm text-slate-500 dark:text-slate-400 text-center py-4">Belum ada notifikasi</p>
                        </template>
                        <template x-if="notifications.length > 0">
                            <div class="space-y-3 max-h-96 overflow-y-auto">
                                <template x-for="notification in notifications.slice(0, 5)" :key="notification.id">
                                    <div class="text-sm p-3 rounded-lg bg-slate-50 dark:bg-slate-700" :class="{'bg-blue-50 dark:bg-blue-900': !notification.is_read}">
                                        <div class="flex items-start gap-2">
                                            <div class="flex-shrink-0 mt-1">
                                                <span class="inline-block w-2 h-2 bg-blue-500 rounded-full" x-show="!notification.is_read"></span>
                                            </div>
                                            <div class="flex-grow">
                                                <p class="font-medium text-slate-900 dark:text-white" x-text="notification.payload?.message || 'Notifikasi baru'"></p>
                                                <p class="text-xs text-slate-600 dark:text-slate-400 mt-1" x-text="new Date(notification.sent_at).toLocaleDateString('id-ID')"></p>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>
                        <div class="mt-4 pt-4 border-t border-slate-200 dark:border-slate-700">
                            <a href="/notifications" class="inline-flex items-center text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 font-medium transition">
                                <i class="fas fa-arrow-right mr-2"></i>Lihat Semua Notifikasi
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Recent Badges -->
                <div class="bg-white dark:bg-slate-800 rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700">
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white">Lencana</h2>
                    </div>
                    <div class="p-6">
                        <template x-if="badges.length === 0">
                            <p class="text-sm text-slate-500 dark:text-slate-400 text-center py-4">Belum ada lencana yang diperoleh</p>
                        </template>
                        <template x-if="badges.length > 0">
                            <div class="grid grid-cols-3 gap-3">
                                <template x-for="badge in badges.slice(0, 6)" :key="badge.id">
                                    <div class="text-center group">
                                        <div class="bg-gradient-to-br from-amber-400 to-orange-500 rounded-lg p-3 mb-2 text-white text-2xl flex items-center justify-center h-20 group-hover:shadow-lg transition">
                                            ⭐
                                        </div>
                                        <p class="text-xs font-semibold text-slate-900 dark:text-white line-clamp-2" x-text="badge.name"></p>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function studentDashboard() {
    return {
        enrollments: [],
        notifications: [],
        badges: [],
        enrolledCoursesCount: 0,
        completedCoursesCount: 0,
        inProgressCount: 0,
        badgesCount: 0,

        async init() {
            await this.loadEnrollments();
            await this.loadNotifications();
            await this.loadBadges();
            this.calculateStats();
        },

        async loadEnrollments() {
            try {
                const response = await fetch('/student/api/enrollments', {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    }
                });
                if (response.ok) {
                    const data = await response.json();
                    this.enrollments = data || [];
                }
            } catch (error) {
                console.error('Error loading enrollments:', error);
            }
        },

        async loadNotifications() {
            try {
                const response = await fetch('/student/api/notifications', {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    }
                });
                if (response.ok) {
                    const data = await response.json();
                    this.notifications = (Array.isArray(data) ? data : data.data || []).slice(0, 10);
                }
            } catch (error) {
                console.error('Error loading notifications:', error);
            }
        },

        async loadBadges() {
            try {
                const response = await fetch('/student/api/badges', {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    }
                });
                if (response.ok) {
                    const data = await response.json();
                    this.badges = (Array.isArray(data) ? data : data.data || []);
                }
            } catch (error) {
                console.error('Error loading badges:', error);
            }
        },

        calculateStats() {
            this.enrolledCoursesCount = this.enrollments.length;
            this.completedCoursesCount = this.enrollments.filter(e => e.status === 'completed').length;
            this.inProgressCount = this.enrollments.filter(e => e.status === 'active').length;
            this.badgesCount = this.badges.length;
        }
    }
}
</script>
@endsection
