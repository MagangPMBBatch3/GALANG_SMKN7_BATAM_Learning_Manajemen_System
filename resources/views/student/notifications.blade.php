@extends('layouts.main')

@section('title', 'Notifikasi - Student Portal')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-900 dark:to-slate-800">
    <!-- Header -->
    <div class="bg-white dark:bg-slate-800 shadow-sm border-b border-slate-200 dark:border-slate-700">
        <div class="max-w-6xl mx-auto px-4 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-slate-900 dark:text-white">Notifikasi</h1>
                    <p class="text-slate-600 dark:text-slate-400 mt-1">Kelola dan lihat semua notifikasi Anda</p>
                </div>
                <a href="/dashboard" class="text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-6xl mx-auto px-4 py-8" x-data="studentNotifications()" x-init="init()">
        <!-- Filters and Search -->
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Search -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Cari</label>
                    <input 
                        type="text" 
                        x-model="search" 
                        @keyup="filterNotifications()"
                        placeholder="Cari notifikasi..." 
                        class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                </div>

                <!-- Filter Status -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Status</label>
                    <select 
                        x-model="filterStatus" 
                        @change="filterNotifications()"
                        class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                        <option value="">Semua</option>
                        <option value="unread">Belum Dibaca</option>
                        <option value="read">Sudah Dibaca</option>
                    </select>
                </div>

                <!-- Filter Type -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Tipe</label>
                    <select 
                        x-model="filterType" 
                        @change="filterNotifications()"
                        class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                        <option value="">Semua Tipe</option>
                        <option value="grading">Penilaian Essay</option>
                        <option value="course">Kursus</option>
                        <option value="system">Sistem</option>
                        <option value="badge">Lencana</option>
                    </select>
                </div>

                <!-- Actions -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Aksi</label>
                    <div class="flex gap-2">
                        <button 
                            @click="markAllAsRead()" 
                            class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition font-medium text-sm"
                        >
                            <i class="fas fa-check-double mr-1"></i>Semua Dibaca
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-4 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-600 dark:text-slate-400">Total Notifikasi</p>
                        <p class="text-2xl font-bold text-slate-900 dark:text-white" x-text="allNotifications.length">0</p>
                    </div>
                    <div class="bg-blue-100 dark:bg-blue-900 p-3 rounded-lg">
                        <i class="fas fa-bell text-blue-600 dark:text-blue-400 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-4 border-l-4 border-yellow-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-600 dark:text-slate-400">Belum Dibaca</p>
                        <p class="text-2xl font-bold text-slate-900 dark:text-white" x-text="unreadCount">0</p>
                    </div>
                    <div class="bg-yellow-100 dark:bg-yellow-900 p-3 rounded-lg">
                        <i class="fas fa-exclamation-circle text-yellow-600 dark:text-yellow-400 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-4 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-600 dark:text-slate-400">Sudah Dibaca</p>
                        <p class="text-2xl font-bold text-slate-900 dark:text-white" x-text="readCount">0</p>
                    </div>
                    <div class="bg-green-100 dark:bg-green-900 p-3 rounded-lg">
                        <i class="fas fa-check-circle text-green-600 dark:text-green-400 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notifications List -->
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow overflow-hidden">
            <!-- Loading State -->
            <template x-if="loading">
                <div class="p-12 text-center">
                    <div class="inline-block">
                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
                    </div>
                    <p class="mt-4 text-slate-600 dark:text-slate-400">Memuat notifikasi...</p>
                </div>
            </template>

            <!-- Empty State -->
            <template x-if="!loading && filteredNotifications.length === 0">
                <div class="p-12 text-center">
                    <div class="text-6xl mb-4">📭</div>
                    <p class="text-slate-600 dark:text-slate-400 text-lg">Tidak ada notifikasi</p>
                    <p class="text-slate-500 dark:text-slate-500 text-sm mt-1">Anda tidak memiliki notifikasi yang sesuai dengan filter saat ini</p>
                </div>
            </template>

            <!-- Notifications -->
            <template x-if="!loading && filteredNotifications.length > 0">
                <div class="divide-y divide-slate-200 dark:divide-slate-700">
                    <template x-for="notification in filteredNotifications" :key="notification.id">
                        <div class="p-6 hover:bg-slate-50 dark:hover:bg-slate-700 transition" :class="{'bg-blue-50 dark:bg-blue-900/20': !notification.is_read}">
                            <div class="flex items-start gap-4">
                                <!-- Icon -->
                                <div class="flex-shrink-0 mt-1">
                                    <div 
                                        class="w-12 h-12 rounded-lg flex items-center justify-center text-lg"
                                        :class="{
                                            'bg-purple-100 text-purple-600 dark:bg-purple-900 dark:text-purple-400': notification.type === 'grading',
                                            'bg-blue-100 text-blue-600 dark:bg-blue-900 dark:text-blue-400': notification.type === 'course',
                                            'bg-green-100 text-green-600 dark:bg-green-900 dark:text-green-400': notification.type === 'badge',
                                            'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400': notification.type === 'system'
                                        }"
                                    >
                                        <template x-if="notification.type === 'grading'">
                                            <i class="fas fa-pen-fancy"></i>
                                        </template>
                                        <template x-if="notification.type === 'course'">
                                            <i class="fas fa-graduation-cap"></i>
                                        </template>
                                        <template x-if="notification.type === 'badge'">
                                            <i class="fas fa-medal"></i>
                                        </template>
                                        <template x-if="notification.type === 'system'">
                                            <i class="fas fa-cog"></i>
                                        </template>
                                    </div>
                                </div>

                                <!-- Content -->
                                <div class="flex-grow min-w-0">
                                    <div class="flex items-start justift-between gap-4">
                                        <div class="flex-grow">
                                            <div class="flex items-center gap-2 mb-1">
                                                <h3 class="font-semibold text-slate-900 dark:text-white" x-text="notification.payload?.title || 'Notifikasi Baru'"></h3>
                                                <template x-if="!notification.is_read">
                                                    <span class="inline-block w-2 h-2 bg-blue-500 rounded-full"></span>
                                                </template>
                            <span class="text-xs px-2 py-1 rounded-full font-medium" :class="{
                                'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200': notification.type === 'grading',
                                'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200': notification.type === 'course',
                                'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200': notification.type === 'badge',
                                'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200': notification.type === 'system'
                            }" x-text="formatType(notification.type)"></span>
                                            </div>
                                            <p class="text-slate-700 dark:text-slate-300" x-text="notification.payload?.message || 'Tidak ada pesan'"></p>
                                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-2" x-text="formatDate(notification.sent_at)"></p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="flex-shrink-0 flex gap-2">
                                    <button 
                                        @click="toggleRead(notification.id, notification.is_read)" 
                                        :class="notification.is_read ? 'text-slate-400 hover:text-slate-600' : 'text-blue-600 hover:text-blue-700'"
                                        class="transition"
                                        :title="notification.is_read ? 'Tandai sebagai belum dibaca' : 'Tandai sebagai dibaca'"
                                    >
                                        <template x-if="notification.is_read">
                                            <i class="fas fa-envelope text-lg"></i>
                                        </template>
                                        <template x-if="!notification.is_read">
                                            <i class="fas fa-envelope-open text-lg"></i>
                                        </template>
                                    </button>
                                    <button 
                                        @click="deleteNotification(notification.id)" 
                                        class="text-red-600 hover:text-red-700 transition"
                                        title="Hapus notifikasi"
                                    >
                                        <i class="fas fa-trash text-lg"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </template>
        </div>

        <!-- Pagination -->
        <template x-if="!loading && filteredNotifications.length > 0 && pagination.last_page > 1">
            <div class="mt-6 flex items-center justify-center gap-2">
                <button 
                    @click="currentPage > 1 ? currentPage-- : null" 
                    :disabled="currentPage === 1"
                    class="px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 disabled:opacity-50 disabled:cursor-not-allowed transition"
                >
                    <i class="fas fa-chevron-left"></i>
                </button>
                
                <div class="flex gap-1">
                    <template x-for="page in Array.from({length: pagination.last_page}, (_, i) => i + 1)" :key="page">
                        <button 
                            @click="currentPage = page" 
                            :class="currentPage === page ? 'bg-blue-600 text-white' : 'border border-slate-300 dark:border-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700'"
                            class="px-4 py-2 rounded-lg transition"
                            x-text="page"
                        ></button>
                    </template>
                </div>

                <button 
                    @click="currentPage < pagination.last_page ? currentPage++ : null" 
                    :disabled="currentPage === pagination.last_page"
                    class="px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 disabled:opacity-50 disabled:cursor-not-allowed transition"
                >
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </template>
    </div>
</div>

<script>
function studentNotifications() {
    return {
        allNotifications: [],
        filteredNotifications: [],
        loading: false,
        search: '',
        filterStatus: '',
        filterType: '',
        currentPage: 1,
        pagination: {
            current_page: 1,
            last_page: 1,
            total: 0,
            per_page: 10
        },

        async init() {
            await this.loadNotifications();
        },

        async loadNotifications() {
            this.loading = true;
            try {
                const response = await fetch('/student/api/notifications?per_page=15', {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    }
                });
                if (response.ok) {
                    const data = await response.json();
                    this.allNotifications = data.data || data;
                    this.pagination = data.pagination || data;
                    this.filterNotifications();
                }
            } catch (error) {
                console.error('Error loading notifications:', error);
            } finally {
                this.loading = false;
            }
        },

        filterNotifications() {
            this.filteredNotifications = this.allNotifications.filter(notif => {
                // Search filter
                const searchMatch = !this.search || 
                    notif.payload?.message?.toLowerCase().includes(this.search.toLowerCase()) ||
                    notif.payload?.title?.toLowerCase().includes(this.search.toLowerCase());

                // Status filter
                const statusMatch = !this.filterStatus || 
                    (this.filterStatus === 'unread' && !notif.is_read) ||
                    (this.filterStatus === 'read' && notif.is_read);

                // Type filter
                const typeMatch = !this.filterType || notif.type === this.filterType;

                return searchMatch && statusMatch && typeMatch;
            });
        },

        async toggleRead(notificationId, currentStatus) {
            try {
                const response = await fetch(`/student/api/notifications/${notificationId}/toggle-read`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    },
                    body: JSON.stringify({ is_read: !currentStatus })
                });

                if (response.ok) {
                    await this.loadNotifications();
                }
            } catch (error) {
                console.error('Error toggling read status:', error);
            }
        },

        async deleteNotification(notificationId) {
            if (!confirm('Apakah Anda yakin ingin menghapus notifikasi ini?')) return;

            try {
                const response = await fetch(`/student/api/notifications/${notificationId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    }
                });

                if (response.ok) {
                    await this.loadNotifications();
                }
            } catch (error) {
                console.error('Error deleting notification:', error);
            }
        },

        async markAllAsRead() {
            try {
                const response = await fetch('/student/api/notifications/mark-all-read', {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    }
                });

                if (response.ok) {
                    await this.loadNotifications();
                }
            } catch (error) {
                console.error('Error marking all as read:', error);
            }
        },

        formatType(type) {
            const types = {
                'grading': 'Penilaian',
                'course': 'Kursus',
                'badge': 'Lencana',
                'system': 'Sistem'
            };
            return types[type] || 'Notifikasi';
        },

        formatDate(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const diffMs = now - date;
            const diffMins = Math.floor(diffMs / 60000);
            const diffHours = Math.floor(diffMs / 3600000);
            const diffDays = Math.floor(diffMs / 86400000);

            if (diffMins < 1) return 'Baru saja';
            if (diffMins < 60) return `${diffMins} menit lalu`;
            if (diffHours < 24) return `${diffHours} jam lalu`;
            if (diffDays < 7) return `${diffDays} hari lalu`;
            
            return date.toLocaleDateString('id-ID', { 
                year: 'numeric', 
                month: 'short', 
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        },

        get unreadCount() {
            return this.allNotifications.filter(n => !n.is_read).length;
        },

        get readCount() {
            return this.allNotifications.filter(n => n.is_read).length;
        }
    };
}
</script>
@endsection
