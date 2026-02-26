@extends('layouts.main')

@section('title', 'Jelajahi Kursus')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50">
    <!-- Hero Section -->
    <div class="gradient-bg text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center animate-fade-in">
                <h1 class="text-5xl md:text-6xl font-bold mb-4">
                    Temukan <span class="text-yellow-300">Skill</span> Anda Berikutnya
                </h1>
                <p class="text-xl md:text-2xl text-blue-100 mb-8 max-w-3xl mx-auto">
                    Jelajahi koleksi kursus komprehensif kami yang dirancang untuk membantu Anda menguasai teknologimu baru dan memajukan karier Anda
                </p>

                <!-- Search Bar -->
                <div class="max-w-2xl mx-auto mb-8">
                    <div class="relative">
                        <input type="text" id="searchInput"
                               placeholder="Cari kursus, topik, atau instruktur..."
                               class="w-full px-6 py-4 text-lg rounded-full border-0 shadow-2xl focus:ring-4 focus:ring-blue-300 focus:outline-none bg-white text-gray-900">
                        <div class="absolute right-4 top-4">
                            <i class="fas fa-search text-gray-400 text-xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8 max-w-4xl mx-auto">
                    <div class="text-center">
                        <div class="text-3xl font-bold mb-2" id="totalCourses">500+</div>
                        <div class="text-blue-100">Kursus Berkualitas</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold mb-2" id="totalEnrollments">50K+</div>
                        <div class="text-blue-100">Pelajar Puas</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold mb-2" id="totalInstructors">100+</div>
                        <div class="text-blue-100">Instruktur Ahli</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold mb-2" id="averageRating">4.8★</div>
                        <div class="text-blue-100">Rata - Rata Nilai</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Filters and Sort -->
        <div class="bg-white rounded-2xl shadow-xl p-6 mb-8 hover-lift">
            <div class="flex flex-col lg:flex-row gap-6 items-center">
                <!-- Search Input -->
                <div class="flex-1 w-full lg:w-auto">
                    <div class="relative">
                        <input type="text" id="filterSearch"
                               placeholder="Cari kursus..."
                               class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <i class="fas fa-search absolute left-4 top-4 text-gray-400"></i>
                    </div>
                </div>

                <!-- Category Filter -->
                <div class="w-full lg:w-48">
                    <select id="filterCategory" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white">
                        <option value="">Semua Kategori</option>
                        <option value="programming">Pemrograman</option>
                        <option value="business">Bisnis</option>
                        <option value="design">Desain</option>
                        <option value="marketing">Pemasaran</option>
                        <option value="data-science">Data Science</option>
                    </select>
                </div>

                <!-- Level Filter -->
                <div class="w-full lg:w-48">
                    <select id="filterLevel" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white">
                        <option value="">Semua Level</option>
                        <option value="beginner">Pemula</option>
                        <option value="intermediate">Menengah</option>
                        <option value="advanced">Lanjutan</option>
                    </select>
                </div>

                <!-- Sort -->
                <div class="w-full lg:w-48">
                    <select id="filterSort" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white">
                        <option value="popular">Paling Populer</option>
                        <option value="newest">Terbaru</option>
                        <option value="price-low">Harga: Rendah ke Tinggi</option>
                        <option value="price-high">Harga: Tinggi ke Rendah</option>
                        <option value="rating">Rating Tertinggi</option>
                    </select>
                </div>

                <!-- Filter Button -->
                <button onclick="filterCourses()" class="px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl hover:from-blue-700 hover:to-purple-700 transition-colors font-medium">
                    <i class="fas fa-filter mr-2"></i>Filter
                </button>
            </div>
        </div>

        <!-- Course Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8 mb-12" id="courseCardsContainer">
            <!-- Course cards will be loaded here -->
        </div>

        <!-- Pagination -->
        <div class="flex justify-center">
            <nav class="flex items-center space-x-2" id="paginationLinks">
                <!-- Pagination links will be loaded here -->
            </nav>
        </div>

        <!-- Results Summary -->
        <div class="text-center mt-8">
            <p class="text-gray-600" id="paginationInfo">
                <!-- Pagination info will be loaded here -->
            </p>
        </div>
    </div>
</div>

<script src="/js/courses.js"></script>
@endsection
