{{-- resources/views/auth/login.blade.php --}}
@extends('layouts.main')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-600 py-12 px-4">
    <div class="max-w-7xl w-full mx-auto grid lg:grid-cols-2 gap-0 rounded-3xl shadow-2xl overflow-hidden">
        
        <!-- Bagian Kiri: Form Login (Glassmorphism) -->
        <div class="flex items-center justify-center p-8 lg:p-16 bg-white/10 backdrop-blur-xl">
            <div class="w-full max-w-md space-y-8">
                <div class="text-center">
                    <div class="w-20 h-20 gradient-bg rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg">
                        <i class="fas fa-graduation-cap text-white text-4xl"></i>
                    </div>
                    <h2 class="text-4xl font-bold text-white">
                        Selamat Datang Kembali!
                    </h2>
                    <p class="mt-3 text-lg text-gray-200">
                        Masuk untuk melanjutkan perjalanan belajarmu
                    </p>
                </div>

                <form class="mt-8 space-y-6" action="{{ route('login') }}" method="POST">
                    @csrf

                    <!-- Alert Error -->
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="list-disc list-inside text-sm text-red-300">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="space-y-5">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-envelope text-gray-300 text-lg"></i>
                            </div>
                            <input id="email" name="email" type="email" autocomplete="email" required
                                class="w-full pl-12 pr-5 py-4 bg-white/20 border border-white/30 rounded-xl text-white placeholder-gray-300 focus:outline-none focus:ring-4 focus:ring-white/40 focus:border-transparent transition-all duration-300 backdrop-blur"
                                placeholder="Email address"
                                value="{{ old('email') }}">
                        </div>

                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-300 text-lg"></i>
                            </div>
                            <input id="password" name="password" type="password" autocomplete="current-password" required
                                class="w-full pl-12 pr-5 py-4 bg-white/20 border border-white/30 rounded-xl text-white placeholder-gray-300 focus:outline-none focus:ring-4 focus:ring-white/40 focus:border-transparent transition-all duration-300 backdrop-blur"
                                placeholder="Password">
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-sm">
                        <label class="flex items-center text-gray-200">
                            <input type="checkbox" name="remember" class="w-4 h-4 text-indigo-300 rounded focus:ring-indigo-500">
                            <span class="ml-2">Ingat saya</span>
                        </label>

                        <a href="{{ route('password.request') }}" class="text-indigo-200 hover:text-white font-medium transition">
                            Lupa password?
                        </a>
                    </div>

                    <button type="submit"
                        class="w-full py-4 px-6 bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-1 focus:outline-none focus:ring-4 focus:ring-white/30 transition-all duration-300">
                        Masuk Sekarang
                    </button>

                    <p class="text-center text-gray-300 text-sm">
                        Belum punya akun?
                        <a href="{{ url('/register') }}" class="font-bold text-white hover:underline">
                            Daftar gratis sekarang
                        </a>
                    </p>
                </form>
            </div>
        </div>

        <!-- Bagian Kanan: Gambar Hero Modern -->
        <div class="hidden lg:block relative overflow-hidden">
            <!-- Background gradient overlay -->
            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent z-10"></div>
            
            <!-- Gambar (ganti dengan gambar kamu sendiri atau gunakan Unsplash) -->
            <img src="storage/abundant-collection-antique-books-wooden-shelves-generated-by-ai.jpg" 
                 alt="Students learning" 
                 class="h-full w-full object-cover">

            <!-- Teks overlay di pojok kiri bawah -->
            <div class="absolute bottom-12 left-12 z-20 text-white">
                <h3 class="text-4xl font-bold mb-4">Belajar Tanpa Batas</h3>
                <p class="text-xl opacity-90">Ribuan kursus berkualitas dari instruktur terbaik</p>
            </div>

            <!-- Dekorasi floating shapes (opsional) -->
            <div class="absolute top-20 right-10 w-72 h-72 bg-purple-500/30 rounded-full blur-3xl"></div>
            <div class="absolute bottom-32 left-20 w-96 h-96 bg-pink-500/20 rounded-full blur-3xl"></div>
        </div>
    </div>
</div>
@endsection

{{-- Tambahkan ini di <head> layouts.main jika belum ada Font Awesome --}}
@section('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
    .transition-blur {
        transition: all 0.3s ease;
    }
    .backdrop-blur {
        backdrop-filter: blur(12px);
    }
</style>

<script>
// Refresh CSRF token every 10 minutes or when tab becomes active
let csrfRefreshInterval;
let pageHidden = false;

// Update the hidden state of the page
document.addEventListener('visibilitychange', function() {
    pageHidden = document.hidden;
    
    if (!pageHidden && csrfRefreshInterval) {
        // Tab became visible, refresh CSRF token immediately
        refreshCsrfToken();
    }
});

// Refresh when tab gets focus
window.addEventListener('focus', function() {
    if (csrfRefreshInterval) {
        refreshCsrfToken();
    }
});

function refreshCsrfToken() {
    fetch(window.location.href, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.text())
    .then(html => {
        // Extract the new CSRF token from the response
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const newToken = doc.querySelector('input[name="_token"]')?.value;
        
        // Update the CSRF token in the form
        if (newToken) {
            const tokenInput = document.querySelector('input[name="_token"]');
            if (tokenInput) {
                tokenInput.value = newToken;
            }
        }
    })
    .catch(err => console.log('CSRF token refresh failed (non-critical):', err));
}

// Start the refresh interval when page loads
window.addEventListener('load', function() {
    // Refresh CSRF token every 10 minutes (600000ms)
    csrfRefreshInterval = setInterval(refreshCsrfToken, 600000);
    
    // Also refresh after 2 minutes of inactivity
    let inactivityTimer;
    const resetInactivityTimer = () => {
        clearTimeout(inactivityTimer);
        inactivityTimer = setTimeout(() => {
            if (!pageHidden) {
                refreshCsrfToken();
            }
        }, 120000); // 2 minutes
    };
    
    document.addEventListener('mousemove', resetInactivityTimer);
    document.addEventListener('keypress', resetInactivityTimer);
    resetInactivityTimer(); // Initialize timer
});
</script>
@endsection