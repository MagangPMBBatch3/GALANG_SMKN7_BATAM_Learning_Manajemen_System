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
                <h3 class="text-lg font-medium text-gray-900">Manajemen Lencana</h3>
                <button @click="showCreateBadgeModal = true" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>Buat Lencana
                </button>
            </div>
            <div class="p-6">
                <div class="mb-6">
                    <input x-model="badgeSearch" type="text" placeholder="Cari nama lencana atau kode..." class="w-full rounded-md border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <template x-if="badges.length === 0">
                        <div class="col-span-3 text-center py-12">
                            <i class="fas fa-medal text-gray-300 text-4xl mb-4"></i>
                            <p class="text-gray-500">Tidak ada lencana ditemukan</p>
                        </div>
                    </template>

                    <template x-for="badge in badges.filter(b => !badgeSearch || b.name.toLowerCase().includes(badgeSearch.toLowerCase()) || b.code.toLowerCase().includes(badgeSearch.toLowerCase()))" :key="badge.id">
                        <div class="border border-gray-200 rounded-lg p-6 hover:shadow-lg transition-shadow">
                            <div class="flex justify-center mb-4">
                                <div class="w-20 h-20 bg-gradient-to-br from-yellow-400 to-amber-500 rounded-full flex items-center justify-center text-white text-3xl shadow-lg">
                                    <i class="fas fa-star"></i>
                                </div>
                            </div>

                            <h4 class="text-lg font-semibold text-gray-900 text-center mb-2" x-text="badge.name"></h4>
                            <p class="text-sm text-gray-600 text-center mb-3"><code class="bg-gray-100 px-2 py-1 rounded" x-text="badge.code"></code></p>
                            <p class="text-sm text-gray-600 text-center mb-4 line-clamp-2" x-text="badge.description || 'Tidak ada deskripsi'"></p>

                            <div class="flex gap-2 justify-center">
                                <button @click="openEditBadgeModal(badge)" class="px-3 py-1 text-sm bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors">
                                    <i class="fas fa-edit mr-1"></i>Edit
                                </button>
                                <button @click="deleteBadge(badge.id)" class="px-3 py-1 text-sm bg-red-500 text-white rounded hover:bg-red-600 transition-colors">
                                    <i class="fas fa-trash mr-1"></i>Hapus
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    @include('admin.modals.badge-manager')
</div>
@endsection