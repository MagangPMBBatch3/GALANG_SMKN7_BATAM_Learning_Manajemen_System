@extends('layouts.main')

@section('title', 'Diskusi Forum')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="threadComponent()" x-init="init()">
        <!-- Loading State -->
        <div x-show="loading" class="text-center py-12">
            <div class="inline-block">
                <div class="w-12 h-12 border-4 border-blue-200 border-t-blue-600 rounded-full animate-spin"></div>
            </div>
            <p class="mt-4 text-gray-600 dark:text-gray-300">Memuat thread...</p>
        </div>

        <!-- Thread Content -->
        <template x-if="!loading && thread">
            <div>
                <!-- Thread Header -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 mb-8 border border-gray-100 dark:border-gray-700">
                    <div class="flex items-start justify-between gap-4 mb-6">
                        <div class="flex-1">
                            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-4" x-text="thread.title"></h1>
                            
                            <!-- Thread Meta Info -->
                            <div class="flex items-center space-x-6 text-sm text-gray-600 dark:text-gray-400 flex-wrap gap-4">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-user text-blue-500"></i>
                                    <span x-text="`Oleh ${thread.user?.name || 'Anonim'}`"></span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-calendar text-green-500"></i>
                                    <span x-text="formatDate(thread.created_at)"></span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-reply text-purple-500"></i>
                                    <span x-text="`${thread.posts?.length || 0} balasan`"></span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-eye text-orange-500"></i>
                                    <span x-text="`${thread.views || 0} dilihat`"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Thread Status Badge -->
                        <div class="text-right">
                            <template x-if="thread.is_locked">
                                <span class="inline-block px-3 py-1 bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 text-sm font-medium rounded-full">
                                    <i class="fas fa-lock mr-1"></i>Terkunci
                                </span>
                            </template>
                            <template x-if="thread.is_sticky">
                                <span class="inline-block px-3 py-1 bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200 text-sm font-medium rounded-full">
                                    <i class="fas fa-thumbtack mr-1"></i>Penting
                                </span>
                            </template>
                        </div>
                    </div>

                    <!-- Thread Course Tag -->
                    <div class="flex items-center space-x-2 mb-6">
                        <span class="px-3 py-1 bg-gradient-to-r from-blue-100 to-purple-100 dark:from-blue-900 dark:to-purple-900 text-blue-800 dark:text-blue-200 text-sm font-medium rounded-full">
                            <i class="fas fa-book mr-1"></i>
                            <span x-text="thread.course?.name || 'Forum Umum'"></span>
                        </span>
                    </div>

                    <!-- Thread Body -->
                    <div class="mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
                        <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap leading-relaxed" x-text="thread.body"></p>
                    </div>

                    <!-- Thread Actions -->
                    <div class="flex items-center space-x-3">
                        <button @click="likeThread()" :class="threadLiked ? 'text-red-500' : 'text-gray-400'" class="inline-flex items-center space-x-1 px-4 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <i :class="threadLiked ? 'fas fa-heart' : 'far fa-heart'" class="text-lg"></i>
                            <span x-text="threadLikes" class="text-sm font-medium"></span>
                        </button>
                        <button @click="scrollToReplyForm()" class="inline-flex items-center space-x-2 px-6 py-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold rounded-xl hover:from-blue-700 hover:to-purple-700 transition-all duration-300">
                            <i class="fas fa-reply"></i>
                            <span>Balas</span>
                        </button>
                    </div>
                </div>

                <!-- Posts (Replies) -->
                <div class="space-y-6 mb-8">
                    <template x-for="post in thread.posts" :key="post.id">
                        <template x-if="!post.parent_post_id">
                            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 border border-gray-100 dark:border-gray-700 hover:shadow-lg transition-shadow">
                                <!-- Post Header -->
                                <div class="flex items-start justify-between mb-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold text-sm">
                                            <span x-text="post.user?.name?.charAt(0) || '?'"></span>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-900 dark:text-white" x-text="post.user?.name || 'Anonim'"></p>
                                            <p class="text-sm text-gray-500 dark:text-gray-400" x-text="formatDate(post.created_at)"></p>
                                        </div>
                                    </div>
                                    <template x-if="currentUserId === post.user_id">
                                        <div class="flex items-center space-x-2">
                                            <button @click="deletePost(post.id)" class="text-red-500 hover:text-red-700 text-sm">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </template>
                                </div>

                                <!-- Post Content -->
                                <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap mb-4 leading-relaxed" x-text="post.body"></p>

                                <!-- Post Actions -->
                                <div class="flex items-center space-x-4">
                                    <button @click="togglePostLike(post.id)" :class="isPostLiked(post.id) ? 'text-red-500' : 'text-gray-400'" class="inline-flex items-center space-x-1 text-sm hover:text-red-500 transition-colors">
                                        <i :class="isPostLiked(post.id) ? 'fas fa-heart' : 'far fa-heart'"></i>
                                        <span x-text="getPostLikesCount(post.id)"></span>
                                    </button>
                                    <button @click="setReplyingToPost(post.id)" :disabled="thread.is_locked" class="inline-flex items-center space-x-1 text-sm text-gray-500 hover:text-blue-600 dark:hover:text-blue-400 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                        <i class="fas fa-reply"></i>
                                        <span>Balas</span>
                                    </button>
                                </div>

                                <!-- Nested Replies -->
                                <div class="ml-8 mt-4 space-y-4 border-l-2 border-gray-200 dark:border-gray-700 pl-4">
                                    <template x-for="reply in thread.posts.filter(p => p.parent_post_id === post.id)" :key="reply.id">
                                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                            <!-- Reply Header -->
                                            <div class="flex items-start justify-between mb-2">
                                                <div class="flex items-center space-x-2">
                                                    <div class="w-8 h-8 bg-gradient-to-r from-green-500 to-teal-600 rounded-full flex items-center justify-center text-white font-bold text-xs">
                                                        <span x-text="reply.user?.name?.charAt(0) || '?'"></span>
                                                    </div>
                                                    <div>
                                                        <p class="font-semibold text-sm text-gray-900 dark:text-white" x-text="reply.user?.name || 'Anonim'"></p>
                                                        <p class="text-xs text-gray-500 dark:text-gray-400" x-text="formatDate(reply.created_at)"></p>
                                                    </div>
                                                </div>
                                                <template x-if="currentUserId === reply.user_id">
                                                    <button @click="deletePost(reply.id)" class="text-red-500 hover:text-red-700 text-xs">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </template>
                                            </div>

                                            <!-- Reply Content -->
                                            <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap mb-2 leading-relaxed" x-text="reply.body"></p>

                                            <!-- Reply Actions -->
                                            <div class="flex items-center space-x-3 text-xs">
                                                <button @click="togglePostLike(reply.id)" :class="isPostLiked(reply.id) ? 'text-red-500' : 'text-gray-400'" class="inline-flex items-center space-x-1 hover:text-red-500 transition-colors">
                                                    <i :class="isPostLiked(reply.id) ? 'fas fa-heart' : 'far fa-heart'"></i>
                                                    <span x-text="getPostLikesCount(reply.id)"></span>
                                                </button>
                                            </div>
                                        </div>
                                    </template>
                                </div>

                                <!-- Quick Reply Form for this post -->
                                <template x-if="replyingToPostId === post.id && !thread.is_locked">
                                    <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                                        <form @submit.prevent="submitReply(post.id)">
                                            <textarea 
                                                x-model="replyContent" 
                                                rows="3" 
                                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none" 
                                                placeholder="Tulis balasan Anda..."
                                                @input="replyContent = $event.target.value.slice(0, 500)">
                                            </textarea>
                                            <div class="mt-2 flex items-center justify-between">
                                                <span class="text-xs text-gray-500 dark:text-gray-400" x-text="`${replyContent.length}/500`"></span>
                                                <div class="flex items-center space-x-2">
                                                    <button type="button" @click="cancelReply()" class="px-3 py-1 text-sm border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                                        Batal
                                                    </button>
                                                    <button type="submit" :disabled="replySubmitting || !replyContent.trim()" class="px-4 py-1 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                                                        <span x-show="!replySubmitting">Kirim</span>
                                                        <span x-show="replySubmitting"><i class="fas fa-spinner animate-spin"></i></span>
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </template>

                    <!-- No Replies Message -->
                    <template x-if="thread.posts.length === 0">
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-12 text-center border border-gray-100 dark:border-gray-700">
                            <i class="fas fa-comments text-4xl text-gray-300 dark:text-gray-600 mb-4"></i>
                            <p class="text-gray-500 dark:text-gray-400">Belum ada balasan. Jadilah yang pertama untuk membalas!</p>
                        </div>
                    </template>
                </div>

                <!-- Main Reply Form -->
                <template x-if="!thread.is_locked">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 border border-gray-100 dark:border-gray-700">
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center">
                            <i class="fas fa-reply text-blue-600 mr-3"></i>Kirim Balasan
                        </h3>

                        <form @submit.prevent="submitMainReply()">
                            <div class="mb-6">
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Balasan Anda</label>
                                <textarea 
                                    x-model="mainReplyContent" 
                                    rows="6" 
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-vertical" 
                                    placeholder="Bagikan pemikiran Anda, ajukan pertanyaan, atau bantu orang lain dalam diskusi ini..."
                                    @input="mainReplyContent = $event.target.value">
                                </textarea>
                                <div class="mt-2 text-sm text-gray-500 dark:text-gray-400" x-text="`${mainReplyContent.length}/2000`"></div>
                            </div>

                            <div class="flex items-center justify-between">
                                <div></div>
                                <div class="flex items-center space-x-3">
                                    <button type="button" @click="mainReplyContent = ''" class="px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-200">
                                        Batal
                                    </button>
                                    <button type="submit" :disabled="replySubmitting || !mainReplyContent.trim()" class="px-8 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold rounded-xl hover:from-blue-700 hover:to-purple-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-300">
                                        <span x-show="!replySubmitting"><i class="fas fa-paper-plane mr-2"></i>Kirim Balasan</span>
                                        <span x-show="replySubmitting"><i class="fas fa-spinner animate-spin mr-2"></i>Mengirim...</span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </template>

                <!-- Thread Locked Message -->
                <template x-if="thread.is_locked">
                    <div class="bg-red-50 dark:bg-red-900 rounded-2xl shadow-md p-8 text-center border border-red-200 dark:border-red-700">
                        <i class="fas fa-lock text-4xl text-red-500 mb-4"></i>
                        <p class="text-red-800 dark:text-red-200 font-semibold">Thread ini telah dikunci dan tidak dapat dibalas lagi.</p>
                    </div>
                </template>

                <!-- Error/Success Modal -->
                <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" x-show="showModal" @click.self="showModal = false">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-8 max-w-md w-full mx-4 transform transition-all" :class="modalType === 'success' ? 'border-l-4 border-green-500' : 'border-l-4 border-red-500'">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <div :class="modalType === 'success' ? 'w-12 h-12 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center' : 'w-12 h-12 bg-red-100 dark:bg-red-900 rounded-full flex items-center justify-center'">
                                    <i :class="modalType === 'success' ? 'fas fa-check text-green-600 text-xl' : 'fas fa-exclamation text-red-600 text-xl'"></i>
                                </div>
                                <h3 class="text-lg font-bold" :class="modalType === 'success' ? 'text-green-700 dark:text-green-300' : 'text-red-700 dark:text-red-300'" x-text="modalTitle"></h3>
                            </div>
                            <button @click="showModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <i class="fas fa-times text-xl"></i>
                            </button>
                        </div>
                        <p class="text-gray-700 dark:text-gray-300 mb-6" x-text="modalMessage"></p>
                        <template x-if="Object.keys(modalErrors).length > 0">
                            <div class="bg-red-50 dark:bg-red-900 rounded-lg p-4 mb-6 border border-red-200 dark:border-red-700">
                                <p class="text-sm font-semibold text-red-700 dark:text-red-300 mb-3">Detail Kesalahan Validasi:</p>
                                <ul class="space-y-2">
                                    <template x-for="(msgs, field) in modalErrors" :key="field">
                                        <li class="text-sm text-red-600 dark:text-red-300">
                                            <span class="font-semibold" x-text="field"></span>:
                                            <span x-text="Array.isArray(msgs) ? msgs[0] : msgs"></span>
                                        </li>
                                    </template>
                                </ul>
                            </div>
                        </template>
                        <div class="flex gap-3 justify-end">
                            <button @click="showModal = false" class="px-6 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 font-medium">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
</div>

<script>
// Simple inline function definition for Alpine
function threadComponent() {
    return {
        threadId: {{ $id }},
        currentUserId: {{ auth()->id() ?? 'null' }},
        
        thread: null,
        loading: true,
        replySubmitting: false,
        showModal: false,
        modalType: 'error',
        modalTitle: '',
        modalMessage: '',
        modalErrors: {},
        
        mainReplyContent: '',
        replyContent: '',
        replyingToPostId: null,
        
        threadLikes: 0,
        threadLiked: false,
        postLikes: {},
        
        formatDate(dateStr) {
            const date = new Date(dateStr);
            const today = new Date();
            const yesterday = new Date(today);
            yesterday.setDate(yesterday.getDate() - 1);
            
            if (date.toDateString() === today.toDateString()) {
                return 'Hari ini ' + date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
            } else if (date.toDateString() === yesterday.toDateString()) {
                return 'Kemarin ' + date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
            } else {
                return date.toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' });
            }
        },
        
        async init() {
            try {
                const response = await fetch(`/forum/api/threads/${this.threadId}`);
                const data = await response.json();
                this.thread = data.data;
                
                // Initialize post likes counts
                if (this.thread.posts) {
                    this.thread.posts.forEach(post => {
                        this.postLikes[post.id] = post.likes_count || 0;
                    });
                }
                
                this.threadLikes = this.thread.likes_count || 0;
                this.loading = false;
            } catch (error) {
                console.error('Error loading thread:', error);
                this.loading = false;
            }
        },
        
        async likeThread() {
            if (!this.currentUserId) {
                alert('Silakan login untuk memberikan like');
                return;
            }
            
            try {
                const response = await fetch(`/forum/api/threads/${this.threadId}/like`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    }
                });
                
                if (response.ok) {
                    const result = await response.json();
                    this.threadLiked = !this.threadLiked;
                    this.threadLikes = result.likes_count;
                }
            } catch (error) {
                console.error('Error liking thread:', error);
            }
        },
        
        async togglePostLike(postId) {
            if (!this.currentUserId) {
                alert('Silakan login untuk memberikan like');
                return;
            }
            
            try {
                const response = await fetch(`/forum/api/posts/${postId}/like`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    }
                });
                
                if (response.ok) {
                    const result = await response.json();
                    this.postLikes[postId] = result.likes_count;
                }
            } catch (error) {
                console.error('Error liking post:', error);
            }
        },
        
        isPostLiked(postId) {
            return this.postLikes[postId] > 0;
        },
        
        getPostLikesCount(postId) {
            return this.postLikes[postId] || 0;
        },
        
        setReplyingToPost(postId) {
            this.replyingToPostId = postId;
            this.replyContent = '';
        },
        
        cancelReply() {
            this.replyingToPostId = null;
            this.replyContent = '';
        },
        
        scrollToReplyForm() {
            document.querySelector('textarea').focus();
        },
        
        async submitReply(parentPostId) {
            if (!this.replyContent.trim()) return;
            if (!this.currentUserId) {
                this.showErrorModal('Perhatian', 'Silakan login untuk membalas', {});
                return;
            }
            
            this.replySubmitting = true;
            
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                const payload = {
                    body: this.replyContent,
                    parent_post_id: parentPostId
                };

                console.log('Submitting reply with payload:', payload);
                console.log('CSRF Token:', csrfToken ? 'Present' : 'Missing');
                
                const response = await fetch(`/forum/api/threads/${this.threadId}/posts`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken || '',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });
                
                const responseData = await response.json();
                console.log('Response status:', response.status, 'Data:', responseData);
                
                if (response.ok) {
                    if (responseData.data && responseData.data.id) {
                        this.thread.posts.push(responseData.data);
                        this.postLikes[responseData.data.id] = responseData.data.likes_count || 0;
                        this.replyContent = '';
                        this.replyingToPostId = null;
                        this.showSuccessModal('Sukses!', 'Balasan Anda telah dikirim');
                    } else {
                        console.error('Invalid response structure:', responseData);
                        this.showErrorModal('Error', 'Respons server tidak valid', {});
                    }
                } else {
                    const errorMsg = responseData.message || 'Gagal mengirim balasan';
                    const errors = responseData.errors || {};
                    console.error('Full API response:', responseData);
                    this.showErrorModal('Validasi Gagal', errorMsg, errors);
                }
            } catch (error) {
                console.error('Error submitting reply:', error);
                this.showErrorModal('Error', 'Terjadi kesalahan saat mengirim balasan: ' + error.message, {});
            } finally {
                this.replySubmitting = false;
            }
        },
        
        async submitMainReply() {
            if (!this.mainReplyContent.trim()) return;
            if (!this.currentUserId) {
                this.showErrorModal('Perhatian', 'Silakan login untuk membalas', {});
                return;
            }
            
            this.replySubmitting = true;
            
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                const payload = {
                    body: this.mainReplyContent
                };

                console.log('Submitting main reply with payload:', payload);
                console.log('CSRF Token:', csrfToken ? 'Present' : 'Missing');
                
                const response = await fetch(`/forum/api/threads/${this.threadId}/posts`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken || '',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });
                
                const responseData = await response.json();
                console.log('Response status:', response.status, 'Data:', responseData);
                
                if (response.ok) {
                    if (responseData.data && responseData.data.id) {
                        this.thread.posts.push(responseData.data);
                        this.postLikes[responseData.data.id] = responseData.data.likes_count || 0;
                        this.mainReplyContent = '';
                        this.showSuccessModal('Sukses!', 'Balasan Anda telah dikirim');
                    } else {
                        console.error('Invalid response structure:', responseData);
                        this.showErrorModal('Error', 'Respons server tidak valid', {});
                    }
                } else {
                    const errorMsg = responseData.message || 'Gagal mengirim balasan';
                    const errors = responseData.errors || {};
                    console.error('Full API response:', responseData);
                    this.showErrorModal('Validasi Gagal', errorMsg, errors);
                }
            } catch (error) {
                console.error('Error submitting reply:', error);
                this.showErrorModal('Error', 'Terjadi kesalahan saat mengirim balasan: ' + error.message, {});
            } finally {
                this.replySubmitting = false;
            }
        },
        
        async deletePost(postId) {
            if (!confirm('Apakah Anda yakin ingin menghapus balasan ini?')) return;
            
            try {
                const response = await fetch(`/forum/api/posts/${postId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    }
                });
                
                if (response.ok) {
                    this.thread.posts = this.thread.posts.filter(p => p.id !== postId);
                    this.showSuccessModal('Sukses!', 'Balasan telah dihapus');
                } else {
                    this.showErrorModal('Error', 'Gagal menghapus balasan', {});
                }
            } catch (error) {
                console.error('Error deleting post:', error);
                this.showErrorModal('Error', 'Terjadi kesalahan saat menghapus balasan', {});
            }
        },

        showSuccessModal(title, message) {
            this.modalType = 'success';
            this.modalTitle = title;
            this.modalMessage = message;
            this.modalErrors = {};
            this.showModal = true;
        },

        showErrorModal(title, message, errors = {}) {
            this.modalType = 'error';
            this.modalTitle = title;
            this.modalMessage = message;
            this.modalErrors = errors;
            this.showModal = true;
        }
    };
}
</script>
@endsection