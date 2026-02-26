@extends('layouts.admin')

@section('title', 'Forum Management')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="forumAdminComponent()">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Forum Management</h1>
            <p class="text-gray-600 mt-2">Manage community forum threads and moderation</p>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search Threads</label>
                    <input type="text" x-model="searchQuery" @input="loadThreads()" placeholder="Search by title, body..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select x-model="filterLocked" @change="loadThreads()"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">All Status</option>
                        <option value="false">Open</option>
                        <option value="true">Locked</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Course</label>
                    <select x-model="filterCourse" @change="loadThreads()"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">All Courses</option>
                        <!-- Courses will be loaded here -->
                    </select>
                </div>
            </div>
        </div>

        <!-- Threads Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Thread</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Author</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Course</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Posts</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Created</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <template x-for="thread in threads" :key="thread.id">
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <p class="text-sm font-medium text-gray-900" x-text="thread.title"></p>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm text-gray-600" x-text="thread.author"></p>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm text-gray-600" x-text="thread.course || 'General'"></p>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm text-gray-600" x-text="thread.posts_count"></p>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <span x-show="thread.is_locked"
                                            class="px-2 py-1 bg-red-100 text-red-800 text-xs font-semibold rounded">Locked</span>
                                        <span x-show="!thread.is_locked"
                                            class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded">Open</span>
                                        <span x-show="thread.is_sticky"
                                            class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded">Sticky</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm text-gray-600" x-text="thread.created_at"></p>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <button @click="toggleLock(thread.id, thread.is_locked)"
                                            :class="thread.is_locked ? 'text-blue-600' : 'text-orange-600'"
                                            class="text-sm font-medium hover:underline">
                                            <i class="fas" :class="thread.is_locked ? 'fa-lock-open' : 'fa-lock'"></i>
                                            <span x-text="thread.is_locked ? 'Unlock' : 'Lock'"></span>
                                        </button>
                                        <button @click="deleteThread(thread.id)"
                                            class="text-sm font-medium text-red-600 hover:underline">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <!-- Empty State -->
            <div class="text-center py-12" x-show="threads.length === 0">
                <i class="fas fa-inbox text-4xl text-gray-300 mb-4 block"></i>
                <p class="text-gray-500">No forum threads found</p>
            </div>
        </div>

        <!-- Loading State -->
        <div class="text-center py-12" x-show="loading">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
            <p class="text-gray-600">Loading threads...</p>
        </div>
    </div>
</div>

<script>
function forumAdminComponent() {
    return {
        threads: [],
        loading: true,
        searchQuery: '',
        filterLocked: '',
        filterCourse: '',

        init() {
            this.loadThreads();
        },

        async loadThreads() {
            this.loading = true;
            try {
                const params = new URLSearchParams();
                if (this.searchQuery) params.append('search', this.searchQuery);
                if (this.filterLocked !== '') params.append('locked', this.filterLocked);
                if (this.filterCourse) params.append('course_id', this.filterCourse);

                const response = await fetch(`/admin/api/forums/threads?${params}`);
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

        async toggleLock(threadId, currentLocked) {
            try {
                const response = await fetch(`/admin/api/forums/threads/${threadId}/status`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        is_locked: !currentLocked
                    })
                });

                const data = await response.json();
                if (data.success) {
                    await this.loadThreads();
                } else {
                    alert('Error: ' + data.message);
                }
            } catch (error) {
                console.error('Error toggling lock:', error);
                alert('Failed to update thread');
            }
        },

        async deleteThread(threadId) {
            if (!confirm('Are you sure you want to delete this thread?')) {
                return;
            }

            try {
                const response = await fetch(`/admin/api/forums/threads/${threadId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const data = await response.json();
                if (data.success) {
                    await this.loadThreads();
                    alert('Thread deleted successfully');
                } else {
                    alert('Error: ' + data.message);
                }
            } catch (error) {
                console.error('Error deleting thread:', error);
                alert('Failed to delete thread');
            }
        }
    }
}
</script>
@endsection
