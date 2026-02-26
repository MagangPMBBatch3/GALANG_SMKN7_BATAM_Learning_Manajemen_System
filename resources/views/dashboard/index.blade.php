@extends('layouts.main')

@section('title', 'Dashboard')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50">
    <!-- Hero Section -->
    <div class="gradient-bg text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center animate-fade-in">
                <h1 class="text-4xl md:text-6xl font-bold mb-4">
                    Selamat datang kembali, <span class="text-yellow-300">{{ auth()->user()->name }}</span>!
                </h1>
                <p class="text-xl md:text-2xl text-blue-100 mb-8">
                    Siap melanjutkan perjalanan belajar Anda?
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="/courses" class="inline-flex items-center px-8 py-4 bg-white text-blue-600 font-semibold rounded-full hover:bg-gray-100 transition-all duration-300 transform hover:scale-105 shadow-lg">
                        <i class="fas fa-search mr-2"></i>Jelajahi Kursus
                    </a>
                    <a href="/my-courses" class="inline-flex items-center px-8 py-4 bg-yellow-400 text-blue-900 font-semibold rounded-full hover:bg-yellow-300 transition-all duration-300 transform hover:scale-105 shadow-lg">
                        <i class="fas fa-play mr-2"></i>Lanjutkan Belajar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
            <div class="bg-white rounded-2xl shadow-xl p-8 hover-lift border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center mb-4">
                            <i class="fas fa-book text-white text-2xl"></i>
                        </div>
                        <p class="text-sm font-medium text-gray-600 uppercase tracking-wide">Kursus Terdaftar</p>
                        <p class="text-4xl font-bold text-gray-900 mt-2" id="enrolledCourses">0</p>
                    </div>
                    <div class="text-right">
                        <div class="text-6xl text-blue-100">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="flex items-center">
                        <div class="flex-1 bg-gray-200 rounded-full h-2">
                            <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-2 rounded-full" id="enrolledProgress" style="width: 0%"></div>
                        </div>
                        <span class="ml-2 text-sm text-gray-600" id="enrolledProgressText">0%</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-xl p-8 hover-lift border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="w-16 h-16 bg-gradient-to-r from-green-500 to-green-600 rounded-2xl flex items-center justify-center mb-4">
                            <i class="fas fa-check-circle text-white text-2xl"></i>
                        </div>
                        <p class="text-sm font-medium text-gray-600 uppercase tracking-wide">Pelajaran Selesai</p>
                        <p class="text-4xl font-bold text-gray-900 mt-2" id="completedCourses">0</p>
                    </div>
                    <div class="text-right">
                        <div class="text-6xl text-green-100">
                            <i class="fas fa-trophy"></i>
                        </div>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="flex items-center">
                        <div class="flex-1 bg-gray-200 rounded-full h-2">
                            <div class="bg-gradient-to-r from-green-500 to-green-600 h-2 rounded-full" id="completedProgress" style="width: 0%"></div>
                        </div>
                        <span class="ml-2 text-sm text-gray-600" id="completedProgressText">0%</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-xl p-8 hover-lift border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="w-16 h-16 bg-gradient-to-r from-yellow-500 to-orange-500 rounded-2xl flex items-center justify-center mb-4">
                            <i class="fas fa-star text-white text-2xl"></i>
                        </div>
                        <p class="text-sm font-medium text-gray-600 uppercase tracking-wide">Total Jam</p>
                        <p class="text-4xl font-bold text-gray-900 mt-2" id="totalHours">0</p>
                    </div>
                    <div class="text-right">
                        <div class="text-6xl text-yellow-100">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="flex items-center">
                        <div class="flex-1 bg-gray-200 rounded-full h-2">
                            <div class="bg-gradient-to-r from-yellow-500 to-orange-500 h-2 rounded-full" id="hoursProgress" style="width: 0%"></div>
                        </div>
                        <span class="ml-2 text-sm text-gray-600" id="hoursProgressText">0%</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- My Courses -->
            <div class="bg-white rounded-2xl shadow-xl p-8 hover-lift border border-gray-100">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">
                        <i class="fas fa-play-circle text-blue-600 mr-2"></i>Kursus Saya
                    </h2>
                    <a href="/my-courses" class="text-blue-600 hover:text-blue-800 font-medium">
                        Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <div id="myCoursesContainer">
                    <!-- Courses will be loaded here -->
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white rounded-2xl shadow-xl p-8 hover-lift border border-gray-100">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">
                        <i class="fas fa-history text-green-600 mr-2"></i>Aktivitas Terbaru
                    </h2>
                    <a href="/profile" class="text-green-600 hover:text-green-800 font-medium">
                        Lihat Profil <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <div id="recentActivityContainer">
                    <!-- Activity will be loaded here -->
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-6">
            <a href="/forums" class="bg-gradient-to-r from-purple-600 to-pink-600 text-white p-6 rounded-2xl hover-lift text-center group">
                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                    <i class="fas fa-comments text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-2">Gabung Komunitas</h3>
                <p class="text-purple-100">terhubung dengan sesama pelajar</p>
            </a>

            <a href="/certificates" class="bg-gradient-to-r from-green-600 to-teal-600 text-white p-6 rounded-2xl hover-lift text-center group">
                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                    <i class="fas fa-certificate text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-2">Lihat Sertifikat</h3>
                <p class="text-green-100">Pamerkan pencapaian Anda</p>
            </a>

            <a href="/profile" class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white p-6 rounded-2xl hover-lift text-center group">
                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                    <i class="fas fa-user-cog text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-2">Ubah Profil</h3>
                <p class="text-blue-100">Customize your learning experience</p>
            </a>
        </div>
    </div>
</div>

<script src="/js/dashboard.js"></script>
@endsection
