{{-- resources/views/auth/register.blade.php --}}
@extends('layouts.main')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-600 py-12 px-4">
    <div class="max-w-7xl w-full mx-auto grid lg:grid-cols-2 gap-0 rounded-3xl shadow-2xl overflow-hidden">

        <!-- Kiri: Form Register (Glassmorphism) -->
        <div class="flex items-center justify-center p-8 lg:p-16 bg-white/10 backdrop-blur-xl">
            <div class="w-full max-w-md space-y-8">
                <div class="text-center">
                    <div class="w-20 h-20 gradient-bg rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg">
                        <i class="fas fa-user-plus text-white text-4xl"></i>
                    </div>
                    <h2 class="text-4xl font-bold text-white">
                        Gabung Sekarang
                    </h2>
                    <p class="mt-3 text-lg text-gray-200">
                        Mulai perjalanan belajarmu hari ini
                    </p>
                </div>

                <form class="mt-8 space-y-6" action="{{ url('/register') }}" method="POST">
                    @csrf

                    <!-- Error Messages -->
                    @if ($errors->any())
                        <div class="bg-red-500/20 border border-red-400/50 text-red-200 px-4 py-3 rounded-xl backdrop-blur">
                            <ul class="list-disc list-inside text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="space-y-5">
                        <!-- Nama Lengkap -->
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-user text-gray-300 text-lg"></i>
                            </div>
                            <input id="name" name="name" type="text" required
                                class="w-full pl-12 pr-5 py-4 bg-white/20 border border-white/30 rounded-xl text-white placeholder-gray-300 focus:outline-none focus:ring-4 focus:ring-white/40 focus:border-transparent transition-all duration-300 backdrop-blur"
                                placeholder="Nama Lengkap"
                                value="{{ old('name') }}">
                        </div>

                        <!-- Email -->
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-envelope text-gray-300 text-lg"></i>
                            </div>
                            <input id="email" name="email" type="email" autocomplete="email" required
                                class="w-full pl-12 pr-5 py-4 bg-white/20 border border-white/30 rounded-xl text-white placeholder-gray-300 focus:outline-none focus:ring-4 focus:ring-white/40 focus:border-transparent transition-all duration-300 backdrop-blur"
                                placeholder="Alamat Email"
                                value="{{ old('email') }}">
                        </div>

                        <!-- Username (opsional) -->
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-at text-gray-300 text-lg"></i>
                            </div>
                            <input id="username" name="username" type="text"
                                class="w-full pl-12 pr-5 py-4 bg-white/20 border border-white/30 rounded-xl text-white placeholder-gray-300 focus:outline-none focus:ring-4 focus:ring-white/40 focus:border-transparent transition-all duration-300 backdrop-blur"
                                placeholder="Username (opsional)"
                                value="{{ old('username') }}">
                        </div>

                        <!-- Password -->
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-300 text-lg"></i>
                            </div>
                            <input id="password" name="password" type="password" required
                                class="w-full pl-12 pr-5 py-4 bg-white/20 border border-white/30 rounded-xl text-white placeholder-gray-300 focus:outline-none focus:ring-4 focus:ring-white/40 focus:border-transparent transition-all duration-300 backdrop-blur"
                                placeholder="Kata Sandi">
                        </div>

                        <!-- Konfirmasi Password -->
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-300 text-lg"></i>
                            </div>
                            <input id="password_confirmation" name="password_confirmation" type="password" required
                                class="w-full pl-12 pr-5 py-4 bg-white/20 border border-white/30 rounded-xl text-white placeholder-gray-300 focus:outline-none focus:ring-4 focus:ring-white/40 focus:border-transparent transition-all duration-300 backdrop-blur"
                                placeholder="Konfirmasi Kata Sandi">
                        </div>
                    </div>

                    <!-- Tombol Daftar -->
                    <button type="submit"
                        class="w-full py-4 px-6 bg-gradient-to-r from-pink-500 to-indigo-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-2xl transform hover:-translate-y-1 focus:outline-none focus:ring-4 focus:ring-white/30 transition-all duration-300">
                        Daftar Gratis
                    </button>

                    <p class="text-center text-gray-300 text-sm">
                        Sudah punya akun?
                        <a href="{{ route('login') }}" class="font-bold text-white hover:underline">
                            Masuk di sini
                        </a>
                    </p>
                </form>
            </div>
        </div>

        <!-- Kanan: Gambar Hero (bisa diganti sesuai brand kamu) -->
        <div class="hidden lg:block relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent z-10"></div>

            <img src="https://images.unsplash.com/photo-1497633762265-9d179a990aa6?ixlib=rb-4.0.3&auto=format&fit=crop&w=1350&q=80"
                 alt="Belajar online bersama teman"
                 class="h-full w-full object-cover">

            <div class="absolute bottom-12 left-12 z-20 text-white max-w-lg">
                <h3 class="text-5xl font-bold mb-4 leading-tight">
                    Mulai Belajar,<br>Capai Impianmu
                </h3>
                <p class="text-xl opacity-90">
                    Bergabung dengan 50.000+ pelajar yang sudah sukses meningkatkan skill bersama kami
                </p>
            </div>

            <!-- Efek blob dekoratif -->
            <div class="absolute top-10 right-20 w-80 h-80 bg-indigo-500/30 rounded-full blur-3xl"></div>
            <div class="absolute bottom-20 left-32 w-96 h-96 bg-purple-500/20 rounded-full blur-3xl"></div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
    .backdrop-blur { backdrop-filter: blur(12px); }
</style>
@endsection