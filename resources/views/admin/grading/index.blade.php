@extends('layouts.main')

@section('title', 'Admin - Penilaian')

@push('scripts')
<script src="/js/admin.js"></script>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50" id="admin-root" x-data="admin()" x-init="activeTab = 'grading'; loadPendingSubmissions()" x-cloak>
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
                <h3 class="text-lg font-medium text-gray-900">Penilaian Kuis (Esai Pending)</h3>
            </div>
            <div class="p-6">
                <template x-if="grading.loading">
                     <div class="flex justify-center py-12">
                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
                     </div>
                </template>

                 <template x-if="!grading.loading && grading.submissions.length === 0">
                    <div class="text-center py-12 text-gray-500">
                        Belum ada kuis yang menunggu penilaian.
                    </div>
                </template>

                <div x-show="!grading.loading && grading.submissions.length > 0" class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Siswa</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kuis / Pelajaran</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Submit</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <template x-for="sub in grading.submissions" :key="sub.id">
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="text-sm font-medium text-gray-900" x-text="sub.user.name"></div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900" x-text="sub.quiz.title"></div>
                                        <div class="text-xs text-gray-500" x-text="sub.quiz.lesson.course.title"></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="formatDate(sub.created_at)"></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button @click="openGradingModal(sub.id)" class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition-colors">
                                            Nilai Sekarang
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
     <!-- Grading Modal -->
    <div x-show="grading.showModal" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="grading.showModal = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start whitespace-normal">
                        <div class="w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Penilaian Kuis: <span x-text="grading.currentSubmission?.quiz?.title"></span>
                            </h3>
                            <div class="mt-2 text-sm text-gray-500 mb-6">
                                Siswa: <span class="font-semibold" x-text="grading.currentSubmission?.user?.name"></span>
                            </div>
                            
                            <template x-if="grading.currentSubmission">
                                <div class="space-y-6 max-h-[60vh] overflow-y-auto pr-2">
                                    <template x-for="(question, index) in grading.currentSubmission.quiz.questions" :key="question.id">
                                        <div class="bg-gray-50 p-4 rounded-xl border border-gray-200">
                                            <div class="flex justify-between items-start mb-3">
                                                 <div class="font-medium text-gray-900 pr-4">
                                                     <span class="text-gray-500 mr-1" x-text="(index + 1) + '.'"></span>
                                                     <span x-text="question.question"></span>
                                                 </div>
                                                 <div class="text-xs font-semibold px-2 py-1 rounded bg-gray-200 text-gray-700 whitespace-nowrap" x-text="question.type.toUpperCase()"></div>
                                            </div>

                                            <!-- User Answer Display -->
                                            <div class="mb-4">
                                                <p class="text-xs text-gray-500 mb-1">Jawaban Siswa:</p>
                                                <div class="p-3 bg-white rounded-lg border border-gray-200 text-sm text-gray-800 break-words" 
                                                     x-html="getAnswerText(question.id)"></div>
                                            </div>

                                            <!-- Grading Input -->
                                             <div class="flex items-center gap-4 bg-white p-3 rounded-lg border border-gray-200">
                                                 <div class="flex-1">
                                                     <label class="block text-xs font-medium text-gray-500 mb-1">Nilai (Maks: <span x-text="question.points"></span>)</label>
                                                     <input type="number" x-model.number="grading.grades[question.id]" min="0" :max="question.points" 
                                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                                 </div>
                                                 <div class="pt-5 text-sm text-gray-400">/ <span x-text="question.points"></span></div>
                                             </div>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" @click="submitGrades()" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Simpan Nilai
                    </button>
                    <button type="button" @click="grading.showModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
