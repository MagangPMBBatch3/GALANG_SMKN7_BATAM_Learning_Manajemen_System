@extends('layouts.main')

@section('title', 'Profil Saya')

@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="user-profile-id" content="{{ $user->id }}">
@endsection

@section('scripts')
<script src="{{ asset('js/profile.js') }}"></script>
@endsection

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50" x-data="profileComponent()">
    <!-- Profile Header -->
    <div class="gradient-bg text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="flex flex-col lg:flex-row items-center lg:items-start gap-8">
                <!-- Profile Picture and Basic Info -->
                <div class="flex flex-col items-center lg:items-start">
                    <div class="relative">
                        <div class="w-32 h-32 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-4xl font-bold text-white shadow-2xl overflow-hidden"
                             x-show="!user.avatar_url">
                            <span x-text="getInitials(user.name)"></span>
                        </div>
                        <img x-show="user.avatar_url"
                             :src="user.avatar_url && user.avatar_url.startsWith('http') ? user.avatar_url : '/storage/' + user.avatar_url"
                             alt="Profile Picture"
                             class="w-32 h-32 rounded-full object-cover shadow-2xl">
                        <button @click="openPhotoModal()"
                                class="absolute bottom-0 right-0 w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-lg hover:shadow-xl transition-shadow">
                            <i class="fas fa-camera text-gray-600"></i>
                        </button>
                    </div>
                    <div class="text-center lg:text-left mt-6">
                        <h1 class="text-3xl md:text-4xl font-bold mb-2" x-text="user.name"></h1>
                        <p class="text-xl text-blue-100 mb-2" x-text="user.email"></p>
                        <div class="flex items-center justify-center lg:justify-start gap-4 text-sm text-blue-100">
                            
                            <span><i class="fas fa-calendar mr-1"></i>Joined <span x-text="formatDate(user.created_at)"></span></span>
                        </div>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="flex-1 grid grid-cols-2 lg:grid-cols-4 gap-6 w-full lg:w-auto">
                    <div class="bg-white bg-opacity-10 backdrop-blur-sm rounded-2xl p-6 text-center hover-lift">
                        <div class="text-3xl font-bold mb-2" x-text="stats.coursesEnrolled"></div>
                        <div class="text-blue-100">Kursus yang Didaftarkan</div>
                    </div>
                    <div class="bg-white bg-opacity-10 backdrop-blur-sm rounded-2xl p-6 text-center hover-lift">
                        <div class="text-3xl font-bold mb-2" x-text="stats.coursesCompleted"></div>
                        <div class="text-blue-100">Kursus Selesai</div>
                    </div>
                    <div class="bg-white bg-opacity-10 backdrop-blur-sm rounded-2xl p-6 text-center hover-lift">
                        <div class="text-3xl font-bold mb-2" x-text="stats.certificates"></div>
                        <div class="text-blue-100">Sertifikat</div>
                    </div>
                    <div class="bg-white bg-opacity-10 backdrop-blur-sm rounded-2xl p-6 text-center hover-lift">
                        <div class="text-3xl font-bold mb-2" x-text="stats.points"></div>
                        <div class="text-blue-100">Poin Didapatkan</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-8">
                <!-- About Section -->
                <div class="bg-white rounded-2xl shadow-xl p-8 hover-lift">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">
                            <i class="fas fa-user text-blue-600 mr-2"></i> Tentang Saya
                        </h2>
                        <button @click="openBioModal()" class="text-blue-600 hover:text-blue-800 font-medium">
                            <i class="fas fa-edit mr-1"></i>Edit
                        </button>
                    </div>
                    <div class="prose max-w-none">
                        <p class="text-gray-600 leading-relaxed" x-text="user.bio || 'No bio added yet. Tell others about yourself, your interests, and your learning goals.'"></p>
                    </div>
                </div>

                <!-- Learning Goals -->
                <div class="bg-white rounded-2xl shadow-xl p-8 hover-lift">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">
                            <i class="fas fa-bullseye text-green-600 mr-2"></i>Goal belajar
                        </h2>
                        <button @click="openGoalsModal()" class="text-green-600 hover:text-green-800 font-medium">
                            <i class="fas fa-edit mr-1"></i>Edit
                        </button>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <template x-for="goal in user.goals" :key="goal">
                            <div class="flex items-center p-4 bg-green-50 rounded-xl border border-green-100">
                                <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-check text-white text-sm"></i>
                                </div>
                                <span class="text-gray-700" x-text="goal"></span>
                            </div>
                        </template>
                        <div x-show="!user.goals || user.goals.length === 0" class="col-span-full text-center py-8">
                            <i class="fas fa-target text-4xl text-gray-300 mb-4"></i>
                            <p class="text-gray-500">Belum diatur.</p>
                        </div>
                    </div>
                </div>

                <!-- Aktivitas -->
                <div class="bg-white rounded-2xl shadow-xl p-8 hover-lift">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">
                        <i class="fas fa-history text-purple-600 mr-2"></i>Aktivitas
                    </h2>
                    <div class="space-y-4">
                        <template x-for="activity in recentActivities" :key="activity.title">
                            <div class="flex items-start p-4 bg-gray-50 rounded-xl">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center mr-4" :class="activity.iconBg">
                                    <i :class="activity.icon" class="text-white text-sm"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-gray-900 font-medium" x-text="activity.title"></p>
                                    <p class="text-gray-600 text-sm" x-text="activity.description"></p>
                                    <p class="text-gray-500 text-xs mt-1" x-text="activity.time"></p>
                                </div>
                            </div>
                        </template>
                        <div x-show="!recentActivities || recentActivities.length === 0" class="text-center py-8">
                            <i class="fas fa-inbox text-4xl text-gray-300 mb-4"></i>
                            <p class="text-gray-500">Tidak Ada Aktivitas.</p>
                        </div>
                    </div>
                </div>

                <!-- Badges / Achievements -->
                <div class="bg-white rounded-2xl shadow-xl p-8 hover-lift">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">
                            <i class="fas fa-trophy text-yellow-600 mr-2"></i>Badge Saya
                        </h2>
                    </div>
                    @if($user->badges->count() > 0)
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @foreach($user->badges as $badge)
                            <div class="text-center p-4 bg-gradient-to-br from-yellow-50 to-orange-50 rounded-xl border border-yellow-100 transition hover:shadow-md">
                                <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-3 shadow-sm border border-yellow-100">
                                    @if($badge->icon_url)
                                        <img src="{{ asset($badge->icon_url) }}" alt="{{ $badge->name }}" class="w-10 h-10 object-contain">
                                    @else
                                        <i class="fas fa-medal text-yellow-500 text-2xl"></i>
                                    @endif
                                </div>
                                <h3 class="font-bold text-gray-900 text-sm mb-1">{{ $badge->name }}</h3>
                                <p class="text-gray-600 text-xs">{{ $badge->description }}</p>
                            </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-award text-gray-400 text-2xl"></i>
                        </div>
                        <p class="text-gray-500">Belum ada badge yang didapatkan.</p>
                        <p class="text-gray-400 text-sm mt-1">Selesaikan kursus untuk mendapatkan badge!</p>
                    </div>
                    @endif
                </div>

                <!-- Certificates -->
                <div class="bg-white rounded-2xl shadow-xl p-8 hover-lift">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">
                            <i class="fas fa-certificate text-blue-600 mr-2"></i>Sertifikat Saya
                        </h2>
                    </div>
                    @if($user->certificates->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($user->certificates as $cert)
                            <div class="flex items-center p-4 bg-blue-50 rounded-xl border border-blue-100 hover:shadow-md transition group">
                                <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center mr-4 shadow-sm text-blue-600">
                                    <i class="fas fa-file-contract text-lg"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-bold text-gray-900 truncate">{{ $cert->course->title }}</h4>
                                    <p class="text-xs text-gray-500">Issued: {{ $cert->issued_at->format('d M Y') }}</p>
                                </div>
                                <a href="{{ route('student.certificate.view', $cert->id) }}" target="_blank" class="ml-2 p-2 text-blue-600 hover:bg-blue-100 rounded-lg transition" title="Lihat Sertifikat">
                                    <i class="fas fa-external-link-alt"></i>
                                </a>
                            </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-file-alt text-gray-400 text-2xl"></i>
                        </div>
                        <p class="text-gray-500">Belum ada sertifikat.</p>
                        <p class="text-gray-400 text-sm mt-1">Selesaikan kursus 100% untuk mendapatkan sertifikat.</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-8">
                <!-- Learning Progress -->
                <div class="bg-white rounded-2xl shadow-xl p-6 hover-lift">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">
                        <i class="fas fa-chart-line text-indigo-600 mr-2"></i>Progress Belajar
                    </h3>
                    <div class="space-y-4">
                        <div>
                            <div class="flex justify-between text-sm text-gray-600 mb-1">
                                <span>Semua Progres</span>
                                <span x-text="stats.overallProgress + '%'"></span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3">
                                <div class="bg-gradient-to-r from-indigo-500 to-purple-600 h-3 rounded-full transition-all duration-500"
                                     :style="'width: ' + stats.overallProgress + '%'"></div>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-100">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-indigo-600" x-text="stats.studyStreak"></div>
                                <div class="text-xs text-gray-600">Day Streak</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-purple-600" x-text="stats.totalHours"></div>
                                <div class="text-xs text-gray-600">Jam Dipelajari</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activities -->
                <div class="bg-white rounded-2xl shadow-xl p-6 hover-lift">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">
                        <i class="fas fa-history text-orange-600 mr-2"></i>Aktivitas Terbaru
                    </h3>
                    <div class="space-y-2 max-h-48 overflow-y-auto pr-2">
                        <template x-for="(activity, index) in recentActivities.slice(0, 3)" :key="activity.id">
                            <div class="flex items-start gap-2 pb-2" :class="{'border-b border-gray-100': index < Math.min(3, recentActivities.length) - 1}">
                                <div :class="activity.iconBg" class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0">
                                    <i :class="activity.icon" class="text-white text-sm"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900" x-text="activity.title"></p>
                                    <p class="text-xs text-gray-500 mt-1" x-text="activity.time"></p>
                                </div>
                            </div>
                        </template>
                        <div x-show="recentActivities.length === 0" class="text-center py-6">
                            <i class="fas fa-inbox text-gray-300 text-3xl mb-2 block"></i>
                            <p class="text-gray-500 text-sm">Belum ada aktivitas.</p>
                        </div>
                    </div>
                    <div x-show="recentActivities.length > 3" class="mt-2 pt-2 border-t border-gray-200">
                        <a href="#" class="text-sm text-orange-600 hover:text-orange-700 font-medium flex items-center justify-center">
                            <i class="fas fa-arrow-right mr-1"></i>Lihat Semua Aktivitas
                        </a>
                    </div>
                </div>

                <!-- Skills -->
                <div class="bg-white rounded-2xl shadow-xl p-6 hover-lift">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-bold text-gray-900">
                            <i class="fas fa-code text-teal-600 mr-2"></i>Skills & Interests
                        </h3>
                        <button @click="openSkillsModal()" class="text-teal-600 hover:text-teal-800 text-sm font-medium">
                            <i class="fas fa-edit mr-1"></i>Edit
                        </button>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <template x-for="skill in user.skills" :key="skill">
                            <span class="px-3 py-1 bg-teal-100 text-teal-800 text-sm rounded-full" x-text="skill"></span>
                        </template>
                        <span x-show="!user.skills || user.skills.length === 0" class="text-gray-500 text-sm">Belum Ada.</span>
                    </div>
                </div>

                <!-- Social Links -->
                <div class="bg-white rounded-2xl shadow-xl p-6 hover-lift">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">
                        <i class="fas fa-share-alt text-blue-600 mr-2"></i>Koneksi Denganku
                    </h3>
                    <div class="grid grid-cols-2 gap-3">
                        <a href="#" class="flex items-center justify-center p-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors">
                            <i class="fab fa-linkedin text-lg"></i>
                        </a>
                        <a href="#" class="flex items-center justify-center p-3 bg-blue-400 text-white rounded-xl hover:bg-blue-500 transition-colors">
                            <i class="fab fa-twitter text-lg"></i>
                        </a>
                        <a href="#" class="flex items-center justify-center p-3 bg-pink-600 text-white rounded-xl hover:bg-pink-700 transition-colors">
                            <i class="fab fa-instagram text-lg"></i>
                        </a>
                        <a href="#" class="flex items-center justify-center p-3 bg-gray-800 text-white rounded-xl hover:bg-gray-900 transition-colors">
                            <i class="fab fa-github text-lg"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bio Edit Modal -->
    <div x-show="showBioModal"
         x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
         @click.self="closeBioModal()">
        <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-900">Edit Tentang Saya</h3>
                <button @click="closeBioModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <textarea x-model="bioEdit"
                      placeholder="Ceritakan tentang diri Anda..."
                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 mb-4"
                      rows="6"></textarea>
            <div x-show="bioUpdateError" class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                <p class="text-red-700 text-sm" x-text="bioUpdateError"></p>
            </div>
            <div class="flex gap-3">
                <button @click="saveBio()" :disabled="bioUpdating" class="flex-1 bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 disabled:opacity-50">
                    <span x-show="!bioUpdating">Simpan</span>
                    <span x-show="bioUpdating" class="flex items-center justify-center">
                        <i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...
                    </span>
                </button>
                <button @click="closeBioModal()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                    Batal
                </button>
            </div>
        </div>
    </div>

    <!-- Goals Edit Modal -->
    <div x-show="showGoalsModal"
         x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
         @click.self="closeGoalsModal()">
        <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-900">Edit Goal Belajar</h3>
                <button @click="closeGoalsModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="space-y-3 mb-4" x-data="{goalsArray: $root.goalsEdit}">
                <template x-for="(goal, index) in goalsEdit" :key="index">
                    <div class="flex gap-2">
                        <input x-model="goalsEdit[index]"
                               placeholder="Masukkan goal pembelajaran..."
                               class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                        <button @click="removeGoal(index)" class="px-3 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </template>
            </div>
            <button @click="addGoal()" class="w-full py-2 px-4 border-2 border-dashed border-green-300 text-green-600 rounded-lg hover:bg-green-50 mb-4">
                <i class="fas fa-plus mr-2"></i>Tambah Goal
            </button>
            <div x-show="goalsUpdateError" class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                <p class="text-red-700 text-sm" x-text="goalsUpdateError"></p>
            </div>
            <div class="flex gap-3">
                <button @click="saveGoals()" :disabled="goalsUpdating" class="flex-1 bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700 disabled:opacity-50">
                    <span x-show="!goalsUpdating">Simpan</span>
                    <span x-show="goalsUpdating" class="flex items-center justify-center">
                        <i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...
                    </span>
                </button>
                <button @click="closeGoalsModal()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                    Batal
                </button>
            </div>
        </div>
    </div>

    <!-- Skills Edit Modal -->
    <div x-show="showSkillsModal"
         x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
         @click.self="closeSkillsModal()">
        <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-900">Edit Skills & Interests</h3>
                <button @click="closeSkillsModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="space-y-3 mb-4">
                <template x-for="(skill, index) in skillsEdit" :key="index">
                    <div class="flex gap-2">
                        <input x-model="skillsEdit[index]"
                               placeholder="Masukkan skill..."
                               class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500">
                        <button @click="removeSkill(index)" class="px-3 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </template>
            </div>
            <button @click="addSkill()" class="w-full py-2 px-4 border-2 border-dashed border-teal-300 text-teal-600 rounded-lg hover:bg-teal-50 mb-4">
                <i class="fas fa-plus mr-2"></i>Tambah Skill
            </button>
            <div x-show="skillsUpdateError" class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                <p class="text-red-700 text-sm" x-text="skillsUpdateError"></p>
            </div>
            <div class="flex gap-3">
                <button @click="saveSkills()" :disabled="skillsUpdating" class="flex-1 bg-teal-600 text-white py-2 px-4 rounded-lg hover:bg-teal-700 disabled:opacity-50">
                    <span x-show="!skillsUpdating">Simpan</span>
                    <span x-show="skillsUpdating" class="flex items-center justify-center">
                        <i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...
                    </span>
                </button>
                <button @click="closeSkillsModal()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                    Batal
                </button>
            </div>
        </div>
    </div>

    <!-- Photo Upload Modal -->
    <div x-show="showPhotoModal"
         x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
         @click.self="closePhotoModal()">
        <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95">

            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-900">Ubah Foto Profil</h3>
                <button @click="closePhotoModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Current Photo Preview -->
            <div class="text-center mb-6">
                <div class="w-32 h-32 mx-auto mb-4 rounded-full overflow-hidden border-4 border-gray-200">
                    <img x-show="previewUrl"
                         :src="previewUrl"
                         alt="Preview"
                         class="w-full h-full object-cover">
                    <img x-show="!previewUrl && user.avatar_url"
                         :src="user.avatar_url && user.avatar_url.startsWith('http') ? user.avatar_url : '/storage/' + user.avatar_url"
                         alt="Current Photo"
                         class="w-full h-full object-cover">
                    <div x-show="!previewUrl && !user.avatar_url"
                         class="w-full h-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center text-4xl font-bold text-white">
                        <span x-text="getInitials(user.name)"></span>
                    </div>
                </div>
            </div>

            <!-- File Input -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Foto Baru</label>
                <input type="file"
                       @change="handleFileSelect($event)"
                       accept="image/*"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <p class="text-xs text-gray-500 mt-1">Format: JPEG, PNG, GIF. Maksimal 2MB.</p>
            </div>

            <!-- Error Message -->
            <div x-show="uploadError" class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                <p class="text-red-700 text-sm" x-text="uploadError"></p>
            </div>

            <!-- Success Message -->
            <div x-show="uploadSuccess" class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg">
                <p class="text-green-700 text-sm" x-text="uploadSuccess"></p>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-3">
                <button @click="uploadPhoto()"
                        :disabled="!selectedFile || uploading"
                        class="flex-1 bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                    <span x-show="!uploading">Unggah</span>
                    <span x-show="uploading" class="flex items-center justify-center">
                        <i class="fas fa-spinner fa-spin mr-2"></i>Mengunggah...
                    </span>
                </button>

                <button x-show="user.avatar_url"
                        @click="removePhoto()"
                        :disabled="uploading"
                        class="px-4 py-2 border border-red-300 text-red-600 rounded-lg hover:bg-red-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                    Hapus
                </button>

                <button @click="closePhotoModal()"
                        class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function profileComponent() {
    return {
        // Photo modal state
        showPhotoModal: false,
        selectedFile: null,
        previewUrl: null,
        uploading: false,
        uploadError: '',
        uploadSuccess: '',

        // Bio modal state
        showBioModal: false,
        bioEdit: '',
        bioUpdating: false,
        bioUpdateError: '',

        // Goals modal state
        showGoalsModal: false,
        goalsEdit: [],
        goalsUpdating: false,
        goalsUpdateError: '',

        // Skills modal state
        showSkillsModal: false,
        skillsEdit: [],
        skillsUpdating: false,
        skillsUpdateError: '',

        user: {
            ...@json($user),
            goals: @json($user->goals ?? []),
            skills: @json($user->skills ?? []),
            location: @json($user->location ?? null)
        },
        stats: {
            coursesEnrolled: {{ $user->enrollments->count() }},
            coursesCompleted: {{ $user->enrollments->where('status', 'completed')->count() }},
            certificates: {{ $user->certificates->count() }},
            points: {{ $user->points->sum('amount') ?? 0 }},
            overallProgress: {{ $overallProgress }},
            studyStreak: {{ $studyStreak }},
            totalHours: {{ $totalHours }}
        },
        achievements: [],
        recentActivities: [],

        async init() {
            await this.loadActivities();
        },

        // Bio Modal Methods
        openBioModal() {
            this.bioEdit = this.user.bio || '';
            this.showBioModal = true;
            this.bioUpdateError = '';
        },

        closeBioModal() {
            this.showBioModal = false;
            this.bioEdit = '';
            this.bioUpdateError = '';
        },

        async saveBio() {
            this.bioUpdating = true;
            this.bioUpdateError = '';
            try {
                const response = await fetch('/profile/update-bio', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({bio: this.bioEdit})
                });
                const result = await response.json();
                if (result.success) {
                    this.user.bio = result.bio;
                    setTimeout(() => this.closeBioModal(), 1000);
                } else {
                    this.bioUpdateError = result.message || 'Failed to update bio';
                }
            } catch (error) {
                this.bioUpdateError = 'Error: ' + error.message;
            } finally {
                this.bioUpdating = false;
            }
        },

        // Goals Modal Methods
        openGoalsModal() {
            this.goalsEdit = JSON.parse(JSON.stringify(this.user.goals || []));
            this.showGoalsModal = true;
            this.goalsUpdateError = '';
        },

        closeGoalsModal() {
            this.showGoalsModal = false;
            this.goalsEdit = [];
            this.goalsUpdateError = '';
        },

        addGoal() {
            this.goalsEdit.push('');
        },

        removeGoal(index) {
            this.goalsEdit.splice(index, 1);
        },

        async saveGoals() {
            this.goalsUpdating = true;
            this.goalsUpdateError = '';
            try {
                const response = await fetch('/profile/update-goals', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({goals: this.goalsEdit})
                });
                const result = await response.json();
                if (result.success) {
                    this.user.goals = result.goals;
                    setTimeout(() => this.closeGoalsModal(), 1000);
                } else {
                    this.goalsUpdateError = result.message || 'Failed to update goals';
                }
            } catch (error) {
                this.goalsUpdateError = 'Error: ' + error.message;
            } finally {
                this.goalsUpdating = false;
            }
        },

        // Skills Modal Methods
        openSkillsModal() {
            this.skillsEdit = JSON.parse(JSON.stringify(this.user.skills || []));
            this.showSkillsModal = true;
            this.skillsUpdateError = '';
        },

        closeSkillsModal() {
            this.showSkillsModal = false;
            this.skillsEdit = [];
            this.skillsUpdateError = '';
        },

        addSkill() {
            this.skillsEdit.push('');
        },

        removeSkill(index) {
            this.skillsEdit.splice(index, 1);
        },

        async saveSkills() {
            this.skillsUpdating = true;
            this.skillsUpdateError = '';
            try {
                const response = await fetch('/profile/update-skills', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({skills: this.skillsEdit})
                });
                const result = await response.json();
                if (result.success) {
                    this.user.skills = result.skills;
                    setTimeout(() => this.closeSkillsModal(), 1000);
                } else {
                    this.skillsUpdateError = result.message || 'Failed to update skills';
                }
            } catch (error) {
                this.skillsUpdateError = 'Error: ' + error.message;
            } finally {
                this.skillsUpdating = false;
            }
        },

        // Load Activities
        async loadActivities() {
            try {
                const response = await fetch('/profile/recent-activities', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    credentials: 'same-origin'
                });
                const result = await response.json();
                if (result.success) {
                    this.recentActivities = result.activities || [];
                }
            } catch (error) {
                console.error('Error loading activities:', error);
            }
        },

        // Photo Modal Methods
        openPhotoModal() {
            this.showPhotoModal = true;
            this.selectedFile = null;
            this.previewUrl = null;
            this.uploadError = '';
            this.uploadSuccess = '';
        },

        closePhotoModal() {
            this.showPhotoModal = false;
            this.selectedFile = null;
            this.previewUrl = null;
            this.uploadError = '';
            this.uploadSuccess = '';
        },

        handleFileSelect(event) {
            const file = event.target.files[0];
            if (file) {
                const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                if (!allowedTypes.includes(file.type)) {
                    this.uploadError = 'Format file tidak didukung. Gunakan JPEG, PNG, atau GIF.';
                    this.selectedFile = null;
                    this.previewUrl = null;
                    return;
                }

                if (file.size > 2 * 1024 * 1024) {
                    this.uploadError = 'Ukuran file terlalu besar. Maksimal 2MB.';
                    this.selectedFile = null;
                    this.previewUrl = null;
                    return;
                }

                this.selectedFile = file;
                this.uploadError = '';

                const reader = new FileReader();
                reader.onload = (e) => {
                    this.previewUrl = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        },

        async uploadPhoto() {
            if (!this.selectedFile) return;

            this.uploading = true;
            this.uploadError = '';
            this.uploadSuccess = '';

            try {
                const formData = new FormData();
                formData.append('photo', this.selectedFile);
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

                const response = await fetch('/profile/upload-photo', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const result = await response.json();

                if (result.success) {
                    this.uploadSuccess = result.message;
                    this.user.avatar_url = result.avatar_url;

                    setTimeout(() => {
                        this.closePhotoModal();
                    }, 2000);
                } else {
                    this.uploadError = result.message;
                }
            } catch (error) {
                this.uploadError = 'Terjadi kesalahan saat mengunggah foto. Silakan coba lagi.';
            } finally {
                this.uploading = false;
            }
        },

        async removePhoto() {
            if (!confirm('Apakah Anda yakin ingin menghapus foto profil?')) return;

            this.uploading = true;
            this.uploadError = '';
            this.uploadSuccess = '';

            try {
                const response = await fetch('/profile/remove-photo', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const result = await response.json();

                if (result.success) {
                    this.uploadSuccess = result.message;
                    this.user.avatar_url = null;

                    setTimeout(() => {
                        this.closePhotoModal();
                    }, 2000);
                } else {
                    this.uploadError = result.message;
                }
            } catch (error) {
                this.uploadError = 'Terjadi kesalahan saat menghapus foto. Silakan coba lagi.';
            } finally {
                this.uploading = false;
            }
        },

        getInitials(name) {
            return name.split(' ').map(n => n[0]).join('').toUpperCase();
        },

        formatDate(dateString) {
            return new Date(dateString).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'long'
            });
        },
    }
}

</script>
@endsection