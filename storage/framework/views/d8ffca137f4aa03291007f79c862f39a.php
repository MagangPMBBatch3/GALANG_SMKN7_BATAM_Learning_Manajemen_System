<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <!-- Prefer the simple relative GraphQL path; many dev servers expose /graphql -->
    <meta name="graphql-endpoint" content="/graphql">
    <?php if(auth()->guard()->check()): ?>
        <meta name="user-profile-id" content="<?php echo e(auth()->user()->id); ?>">
        <meta name="user-level-name" content="<?php echo e(auth()->user()->hasRole('admin') ? 'Admin' : (auth()->user()->hasRole('instructor') ? 'Instructor' : 'User')); ?>">
    <?php endif; ?>

    <title><?php echo e(config('app.name', 'MaxCourse')); ?> - <?php echo $__env->yieldContent('title', 'Platform Pembelajaran Online'); ?></title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">


    <!-- Tailwind CSS CDN (temporary fix) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <!-- Scripts -->
    

    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .gradient-bg-alt {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        .gradient-text {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .glass-effect {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .hover-lift {
            transition: all 0.3s ease;
        }
        .hover-lift:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
        .animate-fade-in {
            animation: fadeIn 0.6s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .nav-link {
            position: relative;
            transition: all 0.3s ease;
        }
        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -5px;
            left: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transition: all 0.3s ease;
        }
        .nav-link:hover::after {
            width: 100%;
            left: 0;
        }
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>
<body class="antialiased bg-gray-50 font-['Inter']">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="/" class="flex items-center space-x-2">
                        <div class="w-10 h-10 gradient-bg rounded-lg flex items-center justify-center">
                            <i class="fas fa-graduation-cap text-white text-xl"></i>
                        </div>
                        <span class="text-xl font-bold gradient-text">MaxCourse</span>
                    </a>
                </div>

                <div class="hidden md:flex items-center space-x-8">
                    <?php if(auth()->guard()->check()): ?>
                        <?php if (\Illuminate\Support\Facades\Blade::check('hasrole', 'admin')): ?>
                            <a href="/admin" class="nav-link text-purple-600 hover:text-purple-800 px-3 py-2 text-sm font-medium">
                                <i class="fas fa-cog mr-2"></i>Panel Admin
                            </a>
                        <?php else: ?>
                            <a href="/explore-courses" class="nav-link text-gray-700 hover:text-gray-900 px-3 py-2 text-sm font-medium">
                                <i class="fas fa-book mr-2"></i>Jelajahi Kursus
                            </a>
                            <a href="/my-courses" class="nav-link text-gray-700 hover:text-gray-900 px-3 py-2 text-sm font-medium">
                                <i class="fas fa-user-graduate mr-2"></i>Kursus Saya
                            </a>
                            <a href="/forums" class="nav-link text-gray-700 hover:text-gray-900 px-3 py-2 text-sm font-medium">
                                <i class="fas fa-comments mr-2"></i>Forum
                            </a>
                            <a href="/dashboard" class="nav-link text-gray-700 hover:text-gray-900 px-3 py-2 text-sm font-medium">
                                <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                            </a>
                            <a href="/profile" class="nav-link text-gray-700 hover:text-gray-900 px-3 py-2 text-sm font-medium">
                                <i class="fas fa-user mr-2"></i>Profil
                            </a>
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php if(auth()->guard()->guest()): ?>
                        <a href="/explore-courses" class="nav-link text-gray-700 hover:text-gray-900 px-3 py-2 text-sm font-medium">
                            <i class="fas fa-book mr-2"></i>Jelajahi Kursus
                        </a>
                        <a href="/forums" class="nav-link text-gray-700 hover:text-gray-900 px-3 py-2 text-sm font-medium">
                            <i class="fas fa-comments mr-2"></i>Forum
                        </a>
                    <?php endif; ?>
                </div>

                <div class="flex items-center space-x-4">
                    <?php if(auth()->guard()->guest()): ?>
                        <a href="/login" class="text-gray-700 hover:text-gray-900 px-3 py-2 text-sm font-medium">
                            <i class="fas fa-sign-in-alt mr-1"></i>Masuk
                        </a>
                        <a href="/register" class="gradient-bg text-white px-4 py-2 rounded-lg text-sm font-medium hover:shadow-lg transition-all duration-300">
                            <i class="fas fa-user-plus mr-1"></i>Daftar
                        </a>
                    <?php else: ?>
                        <div class="relative">
                            <button id="user-menu-button" class="flex items-center space-x-2 text-gray-700 hover:text-gray-900 focus:outline-none">
                                <?php if(auth()->user()->avatar_url): ?>
                                    <img src="/storage/<?php echo e(auth()->user()->avatar_url); ?>" alt="Profile Picture" class="w-8 h-8 rounded-full object-cover">
                                <?php else: ?>
                                    <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                        <span class="text-white text-sm font-medium"><?php echo e(substr(auth()->user()->name, 0, 1)); ?></span>
                                    </div>
                                <?php endif; ?>
                                <span class="text-sm font-medium"><?php echo e(auth()->user()->name); ?></span>
                                <i class="fas fa-chevron-down text-xs"></i>
                            </button>
                            <div id="user-menu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-1 z-50">
                                <?php if (\Illuminate\Support\Facades\Blade::check('hasrole', 'admin')): ?>
                                    
                                <?php else: ?>
                                    <a href="/profile" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-user mr-2"></i>Profil
                                    </a>
                                    <a href="/notifications" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-bell mr-2"></i>Notifikasi
                                    </a>
                                    <a href="/certificates" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-certificate mr-2"></i>Sertifikat
                                    </a>
                                    <hr class="my-1">
                                <?php endif; ?>
                                <form method="POST" action="/logout" class="block">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                        <i class="fas fa-sign-out-alt mr-2"></i>Keluar
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Mobile menu button -->
                    <button id="mobile-menu-button" class="md:hidden text-gray-700 hover:text-gray-900 focus:outline-none">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile menu -->
        <div id="mobile-menu" class="hidden md:hidden bg-white border-t border-gray-200">
            <div class="px-2 pt-2 pb-3 space-y-1">
                <?php if(auth()->guard()->check()): ?>
                    <?php if (\Illuminate\Support\Facades\Blade::check('hasrole', 'admin')): ?>
                        <a href="/admin" class="block px-3 py-2 text-base font-medium text-purple-600 hover:text-purple-800 hover:bg-gray-50">
                            <i class="fas fa-cog mr-2"></i>Panel Admin
                        </a>
                    <?php else: ?>
                        <a href="/courses" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">
                            <i class="fas fa-book mr-2"></i>Kursus
                        </a>
                        <a href="/my-courses" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">
                            <i class="fas fa-user-graduate mr-2"></i>Kursus Saya
                        </a>
                        <a href="/forums" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">
                            <i class="fas fa-comments mr-2"></i>Forum
                        </a>
                        <a href="/dashboard" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">
                            <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                        </a>
                        <a href="/profile" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">
                            <i class="fas fa-user mr-2"></i>Profil
                        </a>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if(auth()->guard()->guest()): ?>
                    <a href="/courses" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">
                        <i class="fas fa-book mr-2"></i>Kursus
                    </a>
                    <a href="/forums" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">
                        <i class="fas fa-comments mr-2"></i>Forum
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Hero Section (only on home page) -->
    <?php if (! empty(trim($__env->yieldContent('hero')))): ?>
        <?php echo $__env->yieldContent('hero'); ?>
    <?php endif; ?>

    <!-- Main Content -->
    <main class="flex-1">
        <?php echo $__env->yieldContent('content'); ?>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="col-span-1 md:col-span-2">
                    <div class="flex items-center space-x-2 mb-4">
                        <div class="w-10 h-10 gradient-bg rounded-lg flex items-center justify-center">
                            <i class="fas fa-graduation-cap text-white text-xl"></i>
                        </div>
                        <span class="text-xl font-bold">MaxCourse</span>
                    </div>
                    <p class="text-gray-400 mb-4">
                        Memberdayakan pembelajar di seluruh dunia dengan pendidikan online berkualitas. Temukan, belajar, dan berkembang dengan platform kursus komprehensif kami.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-youtube"></i>
                        </a>
                    </div>
                </div>

                <?php if (\Illuminate\Support\Facades\Blade::check('hasrole', 'admin')): ?>
                    
                <?php else: ?>
                <div>
                    <h3 class="text-lg font-semibold mb-4">Tautan Cepat</h3>
                    <ul class="space-y-2">
                        <li><a href="/courses" class="text-gray-400 hover:text-white transition-colors">Jelajahi Kursus</a></li>
                        <li><a href="/forums" class="text-gray-400 hover:text-white transition-colors">Forum Komunitas</a></li>
                        <li><a href="/about" class="text-gray-400 hover:text-white transition-colors">Tentang Kami</a></li>
                        <li><a href="/contact" class="text-gray-400 hover:text-white transition-colors">Kontak</a></li>
                    </ul>
                </div>
                <?php endif; ?>

                <?php if (\Illuminate\Support\Facades\Blade::check('hasrole', 'admin')): ?>
                    
                <?php else: ?>
                <div>
                    <h3 class="text-lg font-semibold mb-4">Dukungan</h3>
                    <ul class="space-y-2">
                        <li><a href="/help" class="text-gray-400 hover:text-white transition-colors">Pusat Bantuan</a></li>
                        <li><a href="/faq" class="text-gray-400 hover:text-white transition-colors">FAQ</a></li>
                        <li><a href="/privacy" class="text-gray-400 hover:text-white transition-colors">Kebijakan Privasi</a></li>
                        <li><a href="/terms" class="text-gray-400 hover:text-white transition-colors">Syarat Layanan</a></li>
                    </ul>
                </div>
                <?php endif; ?>
            </div>

            <div class="border-t border-gray-800 mt-8 pt-8 text-center">
                <p class="text-gray-400">
                    &copy; <?php echo e(date('Y')); ?> MaxCourse. Hak cipta dilindungi undang-undang.
                </p>
            </div>
        </div>
    </footer>

    <!-- Back to Top Button -->
    <button id="back-to-top" class="fixed bottom-6 right-6 bg-gradient-to-r from-blue-500 to-purple-600 text-white p-3 rounded-full shadow-lg opacity-0 transition-all duration-300 transform translate-y-4">
        <i class="fas fa-arrow-up"></i>
    </button>

    <script>
        // Mobile menu toggle
        document.getElementById('mobile-menu-button').addEventListener('click', function() {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        });

        // User menu toggle
        document.getElementById('user-menu-button')?.addEventListener('click', function() {
            document.getElementById('user-menu').classList.toggle('hidden');
        });

        // Close menus when clicking outside
        document.addEventListener('click', function(event) {
            const userMenu = document.getElementById('user-menu');
            const mobileMenu = document.getElementById('mobile-menu');
            const userMenuButton = document.getElementById('user-menu-button');
            const mobileMenuButton = document.getElementById('mobile-menu-button');

            if (userMenu && !userMenu.contains(event.target) && !userMenuButton.contains(event.target)) {
                userMenu.classList.add('hidden');
            }

            if (mobileMenu && !mobileMenu.contains(event.target) && !mobileMenuButton.contains(event.target)) {
                mobileMenu.classList.add('hidden');
            }
        });

        // Back to top button
        window.addEventListener('scroll', function() {
            const backToTop = document.getElementById('back-to-top');
            if (window.pageYOffset > 300) {
                backToTop.classList.remove('opacity-0', 'translate-y-4');
                backToTop.classList.add('opacity-100', 'translate-y-0');
            } else {
                backToTop.classList.add('opacity-0', 'translate-y-4');
                backToTop.classList.remove('opacity-100', 'translate-y-0');
            }
        });

        document.getElementById('back-to-top').addEventListener('click', function() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });

        // Add fade-in animation to content
        document.addEventListener('DOMContentLoaded', function() {
            const content = document.querySelector('main');
            if (content) {
                content.classList.add('animate-fade-in');
            }
        });
    </script>

    <!-- Register Alpine components before Alpine loads -->
    <script>
        window.AlpineComponentsToRegister = {};
        
        // Simple registration function for page-specific components
        window.registerAlpineComponent = function(name, factory) {
            window.AlpineComponentsToRegister[name] = factory;
        };
    </script>

    <!-- Alpine.js - harus di-load sebelum admin.js -->
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <!-- Register components after Alpine loads -->
    <script>
        document.addEventListener('alpine:init', () => {
            Object.entries(window.AlpineComponentsToRegister || {}).forEach(([name, factory]) => {
                Alpine.data(name, factory);
            });
            window.AlpineComponentsToRegister = {};
        });
    </script>


    <!-- Page-specific scripts (admin.js) -->
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\maxcourse\resources\views\layouts\main.blade.php ENDPATH**/ ?>