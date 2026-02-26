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

        @include('admin.dashboard')
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
