@extends('layouts.main')

@section('title', 'Admin Management')

@push('scripts')
<script src="/js/admin.js"></script>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50" id="admin-root" x-data="admin()" x-cloak>
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
                <h3 class="text-lg font-medium text-gray-900">Manajemen Kategori</h3>
                <button @click="showCreateCategoryModal = true" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    <i class="fas fa-plus mr-2"></i>Tambah Kategori
                </button>
            </div>
            <div class="p-6">
                <div class="mb-4">
                    <input x-model="categorySearch" type="text" placeholder="Cari kategori..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Slug</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dibuat</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <template x-if="filteredCategories.length === 0">
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">Tidak ada kategori ditemukan</td>
                                </tr>
                            </template>
                            <template x-for="category in filteredCategories" :key="category.id">
                                <template x-if="category">
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" x-text="category.name"></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="category.slug"></td>
                                        <td class="px-6 py-4 text-sm text-gray-900" x-text="category.description || 'Tidak ada deskripsi'"></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="category.parent_id ? 'Subkategori' : 'Kategori Utama'"></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="formatDate(category.created_at)"></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <button @click="editCategory(category.id)" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>
                                            <button @click="deleteCategory(category.id)" class="text-red-600 hover:text-red-900">Hapus</button>
                                        </td>
                                    </tr>
                                </template>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @include('admin.modals')

    <div x-show="loading" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 flex items-center space-x-4">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            <span class="text-gray-700">Memuat...</span>
        </div>
    </div>
</div>

@endsection
