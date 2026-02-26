@extends('layouts.main')

@section('title', 'Admin Management')

@push('scripts')
<script src="/js/admin.js"></script>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50" id="admin-root" x-data="admin()" x-cloak>
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


        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-medium text-gray-900">Manajemen Kursus</h3>
                <button @click="showCreateCourseModal = true" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    <i class="fas fa-plus mr-2"></i>Tambah Kursus
                </button>
            </div>
            <div class="p-6">
                <div class="mb-4 flex space-x-2">
                    <input x-model="courseSearch" @input="debouncedCourseSearch()" type="text" placeholder="Cari kursus..." class="flex-1 rounded-md border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2">
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Instruktur</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Siswa</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <template x-if="filteredCourses.length === 0">
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">Tidak ada kursus ditemukan</td>
                                </tr>
                            </template>
                            <template x-for="course in filteredCourses" :key="course?.id">
                                <template x-if="course && course.id">
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center">
                                                <div class="h-10 w-10 flex-shrink-0 mr-3" x-show="course.thumbnail_url">
                                                    <img :src="course.thumbnail_url ? '/storage/' + course.thumbnail_url : ''" class="h-10 w-10 rounded-full object-cover">
                                                </div>
                                                <div class="h-10 w-10 flex-shrink-0 mr-3 bg-gray-100 rounded-full flex items-center justify-center text-gray-400" x-show="!course.thumbnail_url">
                                                    <i class="fas fa-image"></i>
                                                </div>
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900" x-text="course.title"></div>
                                                    <div class="text-sm text-gray-500" x-text="course.short_description"></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="course.instructor ? course.instructor.name : 'N/A'"></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="course.category ? course.category.name : 'N/A'"></td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span :class="course.is_published ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'"
                                                  class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                                  x-text="course.is_published ? 'Dipublikasikan' : 'Draf'">
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="course.enrollments_count || 0"></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" x-text="'IDR ' + (course.price || 0).toLocaleString('id-ID')"></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                            <button @click="editCourse(course.id)" class="text-indigo-600 hover:text-indigo-900">Edit</button>
                                            <button @click="manageCourseModules(course.id)" class="text-blue-600 hover:text-blue-900">Modul</button>
                                            <button @click="deleteCourse(course.id)" class="text-red-600 hover:text-red-900">Hapus</button>
                                        </td>
                                    </tr>
                                </template>
                            </template>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-6 flex items-center justify-between" x-show="coursesPaginatorInfo">
                    <div class="text-sm text-gray-700">
                        Menampilkan <span class="font-medium" x-text="coursesPaginatorInfo ? ((coursesPaginatorInfo.currentPage - 1) * coursesPerPage) + 1 : 0"></span> sampai <span class="font-medium" x-text="coursesPaginatorInfo ? Math.min(coursesPaginatorInfo.currentPage * coursesPerPage, coursesPaginatorInfo.total) : 0"></span> dari <span class="font-medium" x-text="coursesPaginatorInfo ? coursesPaginatorInfo.total : 0"></span> hasil
                    </div>
                    <div class="flex items-center space-x-2">
                        <button @click="if (coursesPaginatorInfo && coursesPaginatorInfo.currentPage > 1) { coursesCurrentPage--; loadCourses(); }" :disabled="!coursesPaginatorInfo || coursesPaginatorInfo.currentPage <= 1" class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <span class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md" x-text="coursesPaginatorInfo ? coursesPaginatorInfo.currentPage : 1"></span>
                        <button @click="if (coursesPaginatorInfo && coursesPaginatorInfo.currentPage < coursesPaginatorInfo.lastPage) { coursesCurrentPage++; loadCourses(); }" :disabled="!coursesPaginatorInfo || coursesPaginatorInfo.currentPage >= coursesPaginatorInfo.lastPage" class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('admin.modals')
    @include('admin.modals.module-manager')

    <!-- Loading Overlay -->
    <div x-show="loading" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 flex items-center space-x-4">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            <span class="text-gray-700">Memuat...</span>
        </div>
    </div>
</div>

@endsection