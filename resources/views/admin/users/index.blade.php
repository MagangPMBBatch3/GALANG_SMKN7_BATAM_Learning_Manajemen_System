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


        <!-- Users Management Section -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-medium text-gray-900">Manajemen Pengguna</h3>
                <button @click="showCreateUserModal = true" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    <i class="fas fa-plus mr-2"></i>Tambah Pengguna
                </button>
            </div>
            <div class="p-6">
                <!-- Search -->
                <div class="mb-4 flex space-x-2">
                    <input x-model="userSearch" @input="debouncedUserSearch()" type="text" placeholder="Cari pengguna..." class="flex-1 rounded-md border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2">
                </div>
                <!-- Users Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Peran</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bergabung</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <template x-if="filteredUsers.length === 0">
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">Tidak ada pengguna ditemukan</td>
                                </tr>
                            </template>
                            <template x-for="user in filteredUsers" :key="user.id">
                                <template x-if="user">
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <template x-if="user.avatar_url">
                                                    <img class="w-8 h-8 rounded-full object-cover" :src="'/storage/' + user.avatar_url" :alt="user.name">
                                                </template>
                                                <template x-if="!user.avatar_url">
                                                    <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                                        <span class="text-white text-sm font-bold" x-text="user.name.charAt(0).toUpperCase()"></span>
                                                    </div>
                                                </template>
                                                <div class="ml-3">
                                                    <div class="text-sm font-medium text-gray-900" x-text="user.name"></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" x-text="user.email"></td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex flex-wrap gap-1">
                                                <template x-for="role in user.roles" :key="role.id">
                                                    <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800" x-text="role.display_name || role.name"></span>
                                                </template>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" :class="user.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" x-text="user.is_active ? 'Aktif' : 'Tidak Aktif'"></span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="formatDate(user.created_at)"></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <button @click="editUser(user.id)" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>
                                            <button @click="deleteUser(user.id)" class="text-red-600 hover:text-red-900">Hapus</button>
                                        </td>
                                    </tr>
                                </template>
                            </template>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-6 flex items-center justify-between" x-show="usersPaginatorInfo">
                    <div class="text-sm text-gray-700">
                        Menampilkan <span class="font-medium" x-text="usersPaginatorInfo ? ((usersPaginatorInfo.currentPage - 1) * usersPerPage) + 1 : 0"></span> sampai <span class="font-medium" x-text="usersPaginatorInfo ? Math.min(usersPaginatorInfo.currentPage * usersPerPage, usersPaginatorInfo.total) : 0"></span> dari <span class="font-medium" x-text="usersPaginatorInfo ? usersPaginatorInfo.total : 0"></span> hasil
                    </div>
                    <div class="flex items-center space-x-2">
                        <button @click="if (usersPaginatorInfo && usersPaginatorInfo.currentPage > 1) { usersCurrentPage--; loadUsers(); }" :disabled="!usersPaginatorInfo || usersPaginatorInfo.currentPage <= 1" class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <span class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md" x-text="usersPaginatorInfo ? usersPaginatorInfo.currentPage : 1"></span>
                        <button @click="if (usersPaginatorInfo && usersPaginatorInfo.currentPage < usersPaginatorInfo.lastPage) { usersCurrentPage++; loadUsers(); }" :disabled="!usersPaginatorInfo || usersPaginatorInfo.currentPage >= usersPaginatorInfo.lastPage" class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('admin.modals')

    <!-- Loading Overlay -->
    <div x-show="loading" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 flex items-center space-x-4">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            <span class="text-gray-700">Memuat...</span>
        </div>
    </div>
</div>

@endsection
