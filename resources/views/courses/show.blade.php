@extends('layouts.main')

@section('title', 'Course Details')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50">
    <!-- Hero Section -->
    <div class="gradient-bg text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center animate-fade-in">
                <h1 class="text-4xl md:text-6xl font-bold mb-4" id="courseTitle">Memuat Kursus...</h1>
                <p class="text-xl md:text-2xl text-blue-100 mb-8 max-w-3xl mx-auto" id="courseDescription">
                    Memuat deskripsi kursus...
                </p>

                <!-- Course Stats -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8 max-w-4xl mx-auto">
                    <div class="text-center">
                        <div class="text-3xl font-bold mb-2" id="courseRating">0.0</div>
                        <div class="text-blue-100">Rating</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold mb-2" id="courseRatingCount">(0)</div>
                        <div class="text-blue-100">Ulasan</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold mb-2" id="courseStudents">0</div>
                        <div class="text-blue-100">Siswa</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold mb-2" id="courseLevel">Semua Level</div>
                        <div class="text-blue-100">Level</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2">
                <!-- Course Thumbnail -->
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden mb-8">
                    <img id="courseThumbnail" src="/images/course-placeholder.jpg" alt="Course Thumbnail" class="w-full h-64 object-cover">
                </div>

                <!-- Course Description -->
                <div class="bg-white rounded-2xl shadow-xl p-8 mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Tentang Kursus Ini</h2>
                    <div id="courseFullDescription" class="text-gray-700 leading-relaxed">
                        Memuat detail kursus...
                    </div>
                </div>

                <!-- Instructor Info -->
                <div class="bg-white rounded-2xl shadow-xl p-8 mb-8" id="instructorInfo">
                    <!-- Instructor info will be loaded here -->
                </div>

                <!-- Lessons -->
                <div class="bg-white rounded-2xl shadow-xl p-8 mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Konten Kursus</h2>
                    <div id="lessonsContainer">
                        <!-- Lessons will be loaded here -->
                    </div>
                </div>

                <!-- Reviews -->
                <div class="bg-white rounded-2xl shadow-xl p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Ulasan Siswa</h2>
                    <div id="reviewsContainer">
                        <!-- Reviews will be loaded here -->
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <!-- Enrollment Card -->
                <div class="bg-white rounded-2xl shadow-xl p-8 sticky top-8">
                    <div class="text-center mb-6">
                        <div class="text-4xl font-bold gradient-text mb-2" id="coursePrice">$0</div>
                        <div class="text-gray-600" id="courseCategory">Kategori</div>
                    </div>

                    <button id="enrollButton" class="w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white py-4 px-6 rounded-xl font-semibold hover:from-blue-700 hover:to-purple-700 transition-all duration-300 transform hover:scale-105 shadow-lg mb-6">
                        Memuat...
                    </button>

                    <!-- Progress (if enrolled) -->
                    <div id="progressContainer" class="mb-6" style="display: none;">
                        <!-- Progress will be shown here -->
                    </div>

                    <!-- Course Features -->
                    <div class="space-y-4">
                        <div class="flex items-center text-gray-700">
                            <i class="fas fa-clock text-blue-600 mr-3"></i>
                            <span>Belajar mandiri</span>
                        </div>  
                        <div class="flex items-center text-gray-700">
                            <i class="fas fa-certificate text-green-600 mr-3"></i>
                            <span>Sertifikat penyelesaian</span>
                        </div>
                        <div class="flex items-center text-gray-700">
                            <i class="fas fa-mobile-alt text-purple-600 mr-3"></i>
                            <span>Ramah seluler</span>
                        </div>
                        <div class="flex items-center text-gray-700">
                            <i class="fas fa-infinity text-orange-600 mr-3"></i>
                            <span>Akses seumur hidup</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="/js/course-show.js"></script>
@endsection
