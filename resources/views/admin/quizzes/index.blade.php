@extends('layouts.main')

@section('title', 'Admin - Manajemen Quiz')

@push('scripts')
<script src="/js/admin.js"></script>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50" id="admin-root" x-data="admin()" x-init="activeTab = 'quizzes'; loadQuizzes()" x-cloak>
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Manajemen Admin</h1>
                    <p class="text-gray-600 mt-1">Kelola semua data dan entitas platform</p>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @include('admin.partials.navbar')


        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Daftar Quiz</h3>
                <p class="text-sm text-gray-500 mt-1">Kelola semua quiz yang ada di platform</p>
            </div>
            <div class="p-6">
                <template x-if="quizzes.loading">
                     <div class="flex justify-center py-12">
                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
                     </div>
                </template>

                 <template x-if="!quizzes.loading && quizzes.list.length === 0">
                    <div class="text-center py-12 text-gray-500">
                        Belum ada quiz. Buat quiz melalui halaman Kursus → Modul → Lesson.
                    </div>
                </template>

                <div x-show="!quizzes.loading && quizzes.list.length > 0" class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul Quiz</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kursus</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lesson</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pertanyaan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Passing Score</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submissions</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <template x-for="quiz in quizzes.list" :key="quiz.id">
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900" x-text="quiz.title || quiz.lesson?.title || 'Untitled Quiz'"></div>
                                        <div class="text-xs text-gray-500" x-text="quiz.description ? quiz.description.substring(0, 60) + '...' : ''"></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900" x-text="quiz.course?.title || '-'"></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900" x-text="quiz.lesson?.title || '-'"></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800" x-text="quiz.questions_count || 0"></span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <span x-text="quiz.passing_score + '%'"></span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800" x-text="quiz.submissions_count || 0"></span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                       
                                        <button @click="editQuizFromList(quiz)" class="text-indigo-600 hover:text-indigo-900">
                                            <i class="fas fa-edit mr-1"></i>Edit
                                        </button>
                                        <button @click="deleteQuiz(quiz.id)" class="text-red-600 hover:text-red-900">
                                            <i class="fas fa-trash mr-1"></i>Hapus
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <!-- Stats Cards -->
                <div class="mt-8 grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-4 border border-blue-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs font-medium text-blue-600 uppercase">Total Quiz</p>
                                <p class="text-2xl font-bold text-blue-900" x-text="quizzes.list.length"></p>
                            </div>
                            <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center">
                                <i class="fas fa-clipboard-list text-white text-xl"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-4 border border-green-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs font-medium text-green-600 uppercase">Total Pertanyaan</p>
                                <p class="text-2xl font-bold text-green-900" x-text="quizzes.list.reduce((sum, q) => sum + (q.questions_count || 0), 0)"></p>
                            </div>
                            <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center">
                                <i class="fas fa-question-circle text-white text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg p-4 border border-purple-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs font-medium text-purple-600 uppercase">Total Submissions</p>
                                <p class="text-2xl font-bold text-purple-900" x-text="quizzes.list.reduce((sum, q) => sum + (q.submissions_count || 0), 0)"></p>
                            </div>
                            <div class="w-12 h-12 bg-purple-500 rounded-full flex items-center justify-center">
                                <i class="fas fa-paper-plane text-white text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-lg p-4 border border-orange-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs font-medium text-orange-600 uppercase">Avg Passing Score</p>
                                <p class="text-2xl font-bold text-orange-900" x-text="quizzes.list.length > 0 ? Math.round(quizzes.list.reduce((sum, q) => sum + q.passing_score, 0) / quizzes.list.length) + '%' : '0%'"></p>
                            </div>
                            <div class="w-12 h-12 bg-orange-500 rounded-full flex items-center justify-center">
                                <i class="fas fa-chart-line text-white text-xl"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
