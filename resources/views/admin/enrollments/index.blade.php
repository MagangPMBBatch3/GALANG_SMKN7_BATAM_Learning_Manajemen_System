@extends('layouts.main')

@section('title', 'Admin Management')

@push('scripts')
<script src="/js/admin.js"></script>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50" id="admin-root" x-data="admin()" x-init="(async () => { await loadData(); })().catch(err => console.error(err))" x-cloak>
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Manajemen Admin</h1>
                    <p class="text-gray-600 mt-1">Kelola semua data dan entitas platform</p>
                </div>
                <div class="flex items-center space-x-4">
                    <button @click="refreshData()" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-sync-alt mr-2"></i>Refresh
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @include('admin.partials.navbar')


        <!-- Enrollments Management Section -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-medium text-gray-900">Manajemen Pendaftaran</h3>
                <div class="text-sm text-gray-500">
                    <span class="font-semibold" x-text="enrollmentStats.total || 0"></span> Total Pendaftaran
                </div>
            </div>
            <div class="p-6">
                <!-- Filters -->
                <div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                    <input x-model="enrollmentSearch" @input="debouncedEnrollmentSearch()" type="text" placeholder="Cari nama siswa atau email..." class="rounded-md border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2">
                    
                    <select x-model="enrollmentFilters.course_id" @change="loadEnrollments()" class="rounded-md border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2">
                        <option value="">Semua Kursus</option>
                        <template x-for="course in courses" :key="course.id">
                            <option :value="course.id" x-text="course.title"></option>
                        </template>
                    </select>
                    
                    <select x-model="enrollmentFilters.status" @change="loadEnrollments()" class="rounded-md border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2">
                        <option value="">Semua Status</option>
                        <option value="active">Aktif</option>
                        <option value="completed">Selesai</option>
                        <option value="suspended">Ditangguhkan</option>
                        <option value="expired">Kadaluarsa</option>
                    </select>
                    
                    <select x-model="enrollmentFilters.payment_status" @change="loadEnrollments()" class="rounded-md border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2">
                        <option value="">Semua Status Pembayaran</option>
                        <option value="paid">Dibayar</option>
                        <option value="pending">Menunggu</option>
                        <option value="free">Gratis</option>
                    </select>
                </div>

                <!-- Enrollments Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Siswa</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kursus</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Progres</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pembayaran</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Daftar</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <template x-if="filteredEnrollments.length === 0">
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">Tidak ada pendaftaran ditemukan</td>
                                </tr>
                            </template>
                            <template x-for="enrollment in filteredEnrollments" :key="enrollment.id">
                                <template x-if="enrollment && enrollment.id">
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-gray-900" x-text="enrollment.user?.name || 'N/A'"></div>
                                            <div class="text-sm text-gray-500" x-text="enrollment.user?.email || 'N/A'"></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900" x-text="enrollment.course?.title || 'N/A'"></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="w-16 bg-gray-200 rounded-full h-2">
                                                    <div class="bg-blue-600 h-2 rounded-full" :style="`width: ${enrollment.progress_percent || 0}%`"></div>
                                                </div>
                                                <span class="ml-2 text-sm font-medium text-gray-700" x-text="`${enrollment.progress_percent || 0}%`"></span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span :class="{
                                                'bg-green-100 text-green-800': enrollment.status === 'active',
                                                'bg-blue-100 text-blue-800': enrollment.status === 'completed',
                                                'bg-red-100 text-red-800': enrollment.status === 'suspended',
                                                'bg-yellow-100 text-yellow-800': enrollment.status === 'expired',
                                            }" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" x-text="enrollment.status?.charAt(0).toUpperCase() + enrollment.status?.slice(1)">
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <template x-if="enrollment.price_paid > 0">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800" x-text="`Paid - ${enrollment.currency || 'IDR'} ${enrollment.price_paid}`"></span>
                                            </template>
                                            <template x-else>
                                                <template x-if="enrollment.course?.price > 0">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                                                </template>
                                                <template x-else>
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Free</span>
                                                </template>
                                            </template>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="new Date(enrollment.enrolled_at).toLocaleDateString('id-ID')"></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                            <button @click="viewEnrollmentDetails(enrollment)" class="text-indigo-600 hover:text-indigo-900">Detail</button>
                                            <button @click="openEditEnrollmentModal(enrollment)" class="text-blue-600 hover:text-blue-900">Edit</button>
                                            <button @click="deleteEnrollment(enrollment.id)" class="text-red-600 hover:text-red-900">Hapus</button>
                                        </td>
                                    </tr>
                                </template>
                            </template>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-6 flex items-center justify-between" x-show="enrollmentsPaginatorInfo">
                    <div class="text-sm text-gray-700">
                        Menampilkan <span class="font-medium" x-text="enrollmentsPaginatorInfo ? (enrollmentsCurrentPage * enrollmentsPerPage) + 1 : 0"></span> sampai <span class="font-medium" x-text="enrollmentsPaginatorInfo ? Math.min((enrollmentsCurrentPage + 1) * enrollmentsPerPage, enrollmentsPaginatorInfo.total) : 0"></span> dari <span class="font-medium" x-text="enrollmentsPaginatorInfo ? enrollmentsPaginatorInfo.total : 0"></span> hasil
                    </div>
                    <div class="flex items-center space-x-2">
                        <button @click="if (enrollmentsCurrentPage > 0) { enrollmentsCurrentPage--; loadEnrollments(); }" :disabled="enrollmentsCurrentPage <= 0" class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <span class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md" x-text="enrollmentsCurrentPage + 1"></span>
                        <button @click="if (enrollmentsCurrentPage + 1 < enrollmentsPaginatorInfo?.lastPage) { enrollmentsCurrentPage++; loadEnrollments(); }" :disabled="!enrollmentsPaginatorInfo || (enrollmentsCurrentPage + 1) >= enrollmentsPaginatorInfo.lastPage" class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('admin.modals')
    @include('admin.modals.module-manager')
    @include('admin.modals.enrollment-manager')

    <!-- Loading Overlay -->
    <div x-show="loading" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 flex items-center space-x-4">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            <span class="text-gray-700">Memuat...</span>
        </div>
    </div>
</div>

@endsection