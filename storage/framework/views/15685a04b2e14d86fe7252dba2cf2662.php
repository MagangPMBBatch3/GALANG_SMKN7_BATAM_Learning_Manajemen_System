

<?php $__env->startSection('hero'); ?>
<?php if(auth()->guard()->check()): ?>
    <div class="relative overflow-hidden bg-gradient-to-br from-blue-600 via-purple-600 to-pink-600 py-24 lg:py-32">
        <div class="absolute inset-0 bg-black bg-opacity-20"></div>
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23ffffff" fill-opacity="0.1"%3E%3Ccircle cx="30" cy="30" r="4"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="animate-fade-in">
                <h1 class="text-5xl md:text-6xl font-bold text-white mb-6">
                    Selamat Datang, <span class="block text-6xl md:text-7xl mt-2"><?php echo e(Auth::user()->name); ?>! 👋</span>
                </h1>
                <?php
                    $isAdmin = Auth::user()->hasRole('admin');
                    $role = Auth::user()->hasRole('instructor') ? 'instruktur' : (Auth::user()->hasRole('admin') ? 'admin' : 'pelajar');
                    $dashboardUrl = $isAdmin ? '/admin' : '/dashboard';
                    $dashboardText = $isAdmin ? 'Dashboard Admin' : 'Dashboard Saya';
                    $descriptionText = $isAdmin 
                        ? 'Kelola platform pembelajaran dari dashboard admin anda.' 
                        : 'Lanjutkan perjalanan pembelajaran Anda dengan akses ke semua kursus.';
                ?>
                <p class="text-xl md:text-2xl text-blue-100 mb-8 max-w-3xl mx-auto">
                    Anda login sebagai <span class="font-bold capitalize"><?php echo e($role); ?></span>. <?php echo e($descriptionText); ?>

                </p>
                <a href="<?php echo e($dashboardUrl); ?>" class="inline-flex items-center px-8 py-4 bg-white text-blue-600 font-bold text-lg rounded-full hover:bg-gray-100 transition-all duration-300 transform hover:scale-105 shadow-2xl">
                    <i class="fas fa-tachometer-alt mr-3 text-xl"></i><?php echo e($dashboardText); ?>

                </a>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="relative overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-br from-blue-600 via-purple-600 to-pink-600">
        <div class="absolute inset-0 bg-black bg-opacity-20"></div>
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23ffffff" fill-opacity="0.1"%3E%3Ccircle cx="30" cy="30" r="4"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
    </div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 lg:py-32">
        <div class="text-center">
            <div class="animate-fade-in">
                <h1 class="text-5xl md:text-7xl font-bold text-white mb-6 leading-tight">
                    Kuasai Keterampilan Baru dengan
                    <span class="gradient-text text-6xl md:text-8xl block mt-2">MaxCourse</span>
                </h1>
                <p class="text-xl md:text-2xl text-blue-100 mb-8 max-w-3xl mx-auto leading-relaxed">
                    Buka potensi Anda dengan platform pembelajaran online komprehensif kami.
                    Belajar dari instruktur ahli, dapatkan sertifikat, dan majukan karier Anda.
                </p>

                <div class="flex flex-col sm:flex-row gap-6 justify-center mb-12">
                    <a href="/register" class="inline-flex items-center px-8 py-4 bg-white text-blue-600 font-bold text-lg rounded-full hover:bg-gray-100 transition-all duration-300 transform hover:scale-105 shadow-2xl">
                        <i class="fas fa-rocket mr-3 text-xl"></i>Mulai Belajar Gratis
                    </a>
                    <a href="/courses" class="inline-flex items-center px-8 py-4 bg-transparent border-2 border-white text-white font-bold text-lg rounded-full hover:bg-white hover:text-blue-600 transition-all duration-300 transform hover:scale-105">
                        <i class="fas fa-search mr-3 text-xl"></i>Jelajahi Kursus
                    </a>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-8 max-w-4xl mx-auto">
                    <div class="text-center">
                        <div class="text-4xl font-bold text-white mb-2">10K+</div>
                        <div class="text-blue-100">Pelajar</div>
                    </div>
                    <div class="text-center">
                        <div class="text-4xl font-bold text-white mb-2">500+</div>
                        <div class="text-blue-100">Kursus</div>
                    </div>
                    <div class="text-center">
                        <div class="text-4xl font-bold text-white mb-2">50+</div>
                        <div class="text-blue-100">Instruktur</div>
                    </div>
                    <div class="text-center">
                        <div class="text-4xl font-bold text-white mb-2">95%</div>
                        <div class="text-blue-100">Tingkat Kesuksesan</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="absolute bottom-0 left-0 right-0">
        <svg viewBox="0 0 1440 120" class="w-full h-12 md:h-20">
            <path fill="#ffffff" d="M0,32L48,37.3C96,43,192,53,288,58.7C384,64,480,64,576,58.7C672,53,768,43,864,48C960,53,1056,75,1152,80C1248,85,1344,75,1392,69.3L1440,64L1440,120L1392,120C1344,120,1248,120,1152,120C1056,120,960,120,864,120C768,120,672,120,576,120C480,120,384,120,288,120C192,120,96,120,48,120L0,120Z"></path>
        </svg>
    </div>
</div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php if(auth()->guard()->guest()): ?>
<?php $__env->startSection('content'); ?>
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
                Mengapa Memilih <span class="gradient-text">MaxCourse</span>?
            </h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Temukan fitur-fitur yang membuat pembelajaran bersama kami menjadi pengalaman yang luar biasa
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-8 rounded-2xl hover-lift border border-blue-200">
                <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center mb-6">
                    <i class="fas fa-graduation-cap text-white text-2xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-4">Instruktur Ahli</h3>
                <p class="text-gray-600 leading-relaxed">
                    Belajar dari profesional industri dengan pengalaman bertahun-tahun di bidangnya. Instruktur kami bersemangat dalam mengajar dan berkomitmen untuk kesuksesan Anda.
                </p>
            </div>

            <div class="bg-gradient-to-br from-green-50 to-green-100 p-8 rounded-2xl hover-lift border border-green-200">
                <div class="w-16 h-16 bg-gradient-to-r from-green-500 to-green-600 rounded-2xl flex items-center justify-center mb-6">
                    <i class="fas fa-certificate text-white text-2xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-4">Sertifikat Terakreditasi</h3>
                <p class="text-gray-600 leading-relaxed">
                    Dapatkan sertifikat yang diakui oleh pemberi kerja di seluruh dunia. Tunjukkan keterampilan Anda dan tingkatkan prospek karier dengan program terakreditasi kami.
                </p>
            </div>

            <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-8 rounded-2xl hover-lift border border-purple-200">
                <div class="w-16 h-16 bg-gradient-to-r from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center mb-6">
                    <i class="fas fa-users text-white text-2xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-4">Community Support</h3>
                <p class="text-gray-600 leading-relaxed">
                    Join a vibrant learning community. Connect with fellow students, share knowledge, and get help when you need it through our discussion forums.
                </p>
            </div>

            <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 p-8 rounded-2xl hover-lift border border-yellow-200">
                <div class="w-16 h-16 bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-2xl flex items-center justify-center mb-6">
                    <i class="fas fa-mobile-alt text-white text-2xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-4">Belajar Dimana Saja</h3>
                <p class="text-gray-600 leading-relaxed">
                    Akses kursus Anda di perangkat apa pun, di mana saja, kapan saja. Platform responsif kami memastikan pengalaman pembelajaran yang lancar di semua perangkat.
                </p>
            </div>

            <div class="bg-gradient-to-br from-pink-50 to-pink-100 p-8 rounded-2xl hover-lift border border-pink-200">
                <div class="w-16 h-16 bg-gradient-to-r from-pink-500 to-pink-600 rounded-2xl flex items-center justify-center mb-6">
                    <i class="fas fa-chart-line text-white text-2xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-4">Track Progress</h3>
                <p class="text-gray-600 leading-relaxed">
                    Monitor your learning journey with detailed progress tracking. See your achievements, earned points, and completed milestones.
                </p>
            </div>

            <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 p-8 rounded-2xl hover-lift border border-indigo-200">
                <div class="w-16 h-16 bg-gradient-to-r from-indigo-500 to-indigo-600 rounded-2xl flex items-center justify-center mb-6">
                    <i class="fas fa-gamepad text-white text-2xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-4">Pembelajaran Bermain</h3>
                <p class="text-gray-600 leading-relaxed">
                    Jadikan pembelajaran menyenangkan dengan fitur gamifikasi kami. Dapatkan poin, buka lencana, dan bersaing dengan teman-teman sambil menguasai keterampilan baru.
                </p>
            </div>
        </div>
    </div>
</section>

<section class="py-20 bg-gradient-to-br from-gray-50 to-blue-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
                Kursus <span class="gradient-text">Populer</span>
            </h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Mulai perjalanan pembelajaran Anda dengan kursus-kursus paling populer dan sangat dinilai kami
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
            <div class="bg-white rounded-2xl shadow-xl hover-lift overflow-hidden">
                <div class="h-48 bg-gradient-to-r from-blue-400 to-blue-600 flex items-center justify-center">
                    <i class="fas fa-code text-white text-6xl"></i>
                </div>
                <div class="p-6">
                    <div class="flex items-center justify-between mb-3">
                        <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm font-medium rounded-full">Pemrograman</span>
                        <div class="flex items-center">
                            <i class="fas fa-star text-yellow-400 mr-1"></i>
                            <span class="text-sm text-gray-600">4.8</span>
                        </div>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Bootcamp Pengembangan Web</h3>
                    <p class="text-gray-600 mb-4">Kuasi HTML, CSS, JavaScript, dan framework modern untuk membangun situs web yang menakjubkan.</p>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center mr-2">
                                <span class="text-white text-sm font-medium">J</span>
                            </div>
                            <span class="text-sm text-gray-600">Mas Amba</span>
                        </div>
                        <span class="text-lg font-bold text-blue-600">IDR1,6 jt</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-xl hover-lift overflow-hidden">
                <div class="h-48 bg-gradient-to-r from-green-400 to-green-600 flex items-center justify-center">
                    <i class="fas fa-chart-bar text-white text-6xl"></i>
                </div>
                <div class="p-6">
                    <div class="flex items-center justify-between mb-3">
                        <span class="px-3 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full">Bisnis</span>
                        <div class="flex items-center">
                            <i class="fas fa-star text-yellow-400 mr-1"></i>
                            <span class="text-sm text-gray-600">4.9</span>
                        </div>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Kuasai Pemasaran Digital</h3>
                    <p class="text-gray-600 mb-4">Pelajari SEO, pemasaran media sosial, dan analitik untuk mengembangkan kehadiran online Anda.</p>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-gradient-to-r from-green-500 to-teal-600 rounded-full flex items-center justify-center mr-2">
                                <span class="text-white text-sm font-medium">S</span>
                            </div>
                            <span class="text-sm text-gray-600">Mas Gatot</span>
                        </div>
                        <span class="text-lg font-bold text-green-600">IDR1,3 jt</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-xl hover-lift overflow-hidden">
                <div class="h-48 bg-gradient-to-r from-purple-400 to-purple-600 flex items-center justify-center">
                    <i class="fas fa-palette text-white text-6xl"></i>
                </div>
                <div class="p-6">
                    <div class="flex items-center justify-between mb-3">
                        <span class="px-3 py-1 bg-purple-100 text-purple-800 text-sm font-medium rounded-full">Desain</span>
                        <div class="flex items-center">
                            <i class="fas fa-star text-yellow-400 mr-1"></i>
                            <span class="text-sm text-gray-600">4.7</span>
                        </div>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">UI/UX Design Fundamentals</h3>
                    <p class="text-gray-600 mb-4">Create beautiful and user-friendly interfaces with modern design principles.</p>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-gradient-to-r from-purple-500 to-pink-600 rounded-full flex items-center justify-center mr-2">
                                <span class="text-white text-sm font-medium">M</span>
                            </div>
                            <span class="text-sm text-gray-600">Mas Rahul</span>
                        </div>
                        <span class="text-lg font-bold text-purple-600">IDR1,4 jt</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center">
            <a href="/courses" class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-bold text-lg rounded-full hover:from-blue-700 hover:to-purple-700 transition-all duration-300 transform hover:scale-105 shadow-lg">
                <i class="fas fa-arrow-right mr-3"></i>Lihat Semua Kursus
            </a>
        </div>
    </div>
</section>

<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
                What Our <span class="gradient-text">Students Say</span>
            </h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Dengarkan dari pelajar yang telah mengubah karier mereka dengan MaxCourse
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-gradient-to-br from-blue-50 to-purple-50 p-8 rounded-2xl hover-lift border border-blue-100">
                <div class="flex items-center mb-6">
                    <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center mr-4">
                        <span class="text-white font-bold">A</span>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900">Rizal Keseimbangan</h4>
                        <p class="text-gray-600">Software Developer</p>
                    </div>
                </div>
                <div class="flex text-yellow-400 mb-4">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
                <p class="text-gray-700 italic">
                    "MaxCourse benar-benar mengubah karier saya. Kursusnya terstruktur dengan baik, dan instrukturnya luar biasa. Saya mendapatkan pekerjaan impian saya berkat keterampilan yang saya pelajari di sini!"
                </p>
            </div>

            <div class="bg-gradient-to-br from-green-50 to-teal-50 p-8 rounded-2xl hover-lift border border-green-100">
                <div class="flex items-center mb-6">
                    <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-teal-600 rounded-full flex items-center justify-center mr-4">
                        <span class="text-white font-bold">B</span>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900">Iwan Rotasi</h4>
                        <p class="text-gray-600">Marketing Manager</p>
                    </div>
                </div>
                <div class="flex text-yellow-400 mb-4">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
                <p class="text-gray-700 italic">
                    "Kursus pemasaran digitalnya luar biasa. Saya mempelajari keterampilan praktis yang langsung saya terapkan dalam pekerjaan saya. Dukungan komunitasnya juga luar biasa!"
                </p>
            </div>

            <div class="bg-gradient-to-br from-purple-50 to-pink-50 p-8 rounded-2xl hover-lift border border-purple-100">
                <div class="flex items-center mb-6">
                    <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-pink-600 rounded-full flex items-center justify-center mr-4">
                        <span class="text-white font-bold">C</span>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900">Farhan Mengalir</h4>
                        <p class="text-gray-600">UX Designer</p>
                    </div>
                </div>
                <div class="flex text-yellow-400 mb-4">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
                <p class="text-gray-700 italic">
                    "Sebagai seseorang yang baru mengenal desain, MaxCourse membuat pembelajaran menyenangkan dan mudah diakses. Proyek-proyeknya menantang namun memuaskan. Sangat direkomendasikan!"
                </p>
            </div>
        </div>
    </div>
</section>
<section class="py-20 bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600">
    <div class="max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8">
        <h2 class="text-4xl md:text-5xl font-bold text-white mb-6">
            Siap Memulai Perjalanan Belajar Anda?
        </h2>
        <p class="text-xl text-blue-100 mb-8">
            Bergabunglah dengan ribuan pelajar yang telah mengubah karier mereka dengan MaxCourse
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="/register" class="inline-flex items-center px-8 py-4 bg-white text-blue-600 font-bold text-lg rounded-full hover:bg-gray-100 transition-all duration-300 transform hover:scale-105 shadow-lg">
                <i class="fas fa-user-plus mr-3"></i>gabung sekarang gratis
            </a>
            <a href="/login" class="inline-flex items-center px-8 py-4 bg-transparent border-2 border-white text-white font-bold text-lg rounded-full hover:bg-white hover:text-blue-600 transition-all duration-300 transform hover:scale-105">
                <i class="fas fa-sign-in-alt mr-3"></i>Sign In
            </a>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>
<?php endif; ?>

<?php echo $__env->make('layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\maxcourse\resources\views\welcome.blade.php ENDPATH**/ ?>