@extends('layouts.main')

@section('title', 'Pelacakan Progres')

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
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @include('admin.partials.navbar')

        <!-- Content -->
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Pelacakan Progres Siswa</h2>
            <p class="text-gray-600 mt-1">Pantau progres belajar siswa di semua kursus</p>
        </div>

        <!-- Course List -->
        <div class="grid grid-cols-1 gap-6">
            <template x-for="course in courses" :key="course.id">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <!-- Course Header -->
                    <div class="flex items-start justify-between mb-6">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900" x-text="course.title"></h3>
                            <p class="text-sm text-gray-500 mt-1">
                                <span x-text="course.modules_count || 0"></span> modul • 
                                <span x-text="course.lessons_count || 0"></span> pelajaran
                            </p>
                        </div>
                        <button @click="expandedCourse = expandedCourse === course.id ? null : course.id" class="px-4 py-2 text-blue-600 hover:text-blue-700">
                            <template x-if="expandedCourse !== course.id">
                                <span><i class="fas fa-chevron-down mr-2"></i>Buka</span>
                            </template>
                            <template x-else>
                                <span><i class="fas fa-chevron-up mr-2"></i>Tutup</span>
                            </template>
                        </button>
                    </div>

                    <!-- Expanded Content -->
                    <template x-if="expandedCourse === course.id">
                        <div class="space-y-4">
                            <!-- Enrollments List -->
                            <div>
                                <h4 class="font-semibold text-gray-900 mb-3">Pendaftaran Siswa</h4>
                                <div class="space-y-3">
                                    <template x-for="enrollment in getEnrollmentsByCourse(course.id)" :key="enrollment.id">
                                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                            <!-- Student Info -->
                                            <div class="flex items-center justify-between mb-3">
                                                <div>
                                                    <p class="font-medium text-gray-900" x-text="enrollment.user?.name"></p>
                                                    <p class="text-sm text-gray-500" x-text="enrollment.user?.email"></p>
                                                </div>
                                                <span class="px-3 py-1 rounded-full text-sm font-semibold" :class="enrollment.status === 'active' ? 'bg-green-100 text-green-800' : enrollment.status === 'completed' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800'" x-text="enrollment.status"></span>
                                            </div>

                                            <!-- Progress Bar -->
                                            <div class="mb-3">
                                                <div class="flex justify-between items-center mb-2">
                                                    <span class="text-sm text-gray-600">Progres Keseluruhan</span>
                                                    <span class="text-sm font-medium text-gray-900" x-text="`${enrollment.progress_percent}%`"></span>
                                                </div>
                                                <div class="w-full bg-gray-200 rounded-full h-2">
                                                    <div class="bg-blue-600 h-2 rounded-full" :style="`width: ${enrollment.progress_percent}%`"></div>
                                                </div>
                                            </div>

                                            <!-- View Details Button -->
                                            <button @click="showProgressModal(enrollment)" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                                                <i class="fas fa-eye mr-1"></i>Lihat Progres Pelajaran
                                            </button>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </template>

            <template x-if="courses.length === 0">
                <div class="text-center py-12">
                    <i class="fas fa-graduation-cap text-gray-300 text-4xl mb-4"></i>
                    <p class="text-gray-500">Tidak ada kursus tersedia</p>
                </div>
            </template>
        </div>
    </div>

    <!-- Progress Details Modal -->
    <div x-show="showProgressDetailsModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50" @click.self="showProgressDetailsModal = false">
        <div class="bg-white rounded-lg shadow-lg p-6 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-900">Progres Pelajaran</h3>
                <button @click="showProgressDetailsModal = false" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Student & Course Info -->
            <div class="bg-gray-50 rounded p-4 mb-4">
                <p class="text-sm text-gray-600">Siswa: <span class="font-semibold text-gray-900" x-text="selectedEnrollment?.user?.name"></span></p>
                <p class="text-sm text-gray-600">Kursus: <span class="font-semibold text-gray-900" x-text="selectedEnrollment?.course?.title"></span></p>
            </div>

            <!-- Lessons List -->
            <div class="space-y-3">
                <h4 class="font-semibold text-gray-900 mb-3">Penyelesaian Pelajaran</h4>
                <template x-for="lesson in getEnrollmentLessons(selectedEnrollment?.course_id)" :key="lesson.id">
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded border border-gray-200">
                        <div class="flex-1">
                            <p class="font-medium text-gray-900" x-text="lesson.title"></p>
                            <p class="text-sm text-gray-500" x-text="lesson.content_type"></p>
                        </div>
                        <button @click="markLessonComplete(selectedEnrollment?.id, lesson.id)" class="px-3 py-1 text-sm bg-green-500 text-white rounded hover:bg-green-600">
                            Tandai Selesai
                        </button>
                    </div>
                </template>
            </div>

            <div class="flex justify-end gap-2 mt-6">
                <button @click="showProgressDetailsModal = false" class="px-4 py-2 text-gray-700 bg-gray-200 rounded hover:bg-gray-300">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

