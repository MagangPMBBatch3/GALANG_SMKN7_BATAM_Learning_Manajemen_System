@extends('layouts.main')

@section('title', 'My Certificates')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50">
    <!-- Hero Section -->
    <div class="gradient-bg text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center animate-fade-in">
                <h1 class="text-5xl md:text-6xl font-bold mb-4">
                    My <span class="text-yellow-300">Certificates</span>
                </h1>
                <p class="text-xl md:text-2xl text-blue-100 mb-8 max-w-3xl mx-auto">
                    Showcase your achievements and celebrate your learning milestones
                </p>

                <!-- Quick Stats -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8 max-w-4xl mx-auto">
                    <div class="text-center">
                        <div class="text-3xl font-bold mb-2" id="totalCertificates">0</div>
                        <div class="text-blue-100">Total Certificates</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold mb-2" id="certificatesThisMonth">0</div>
                        <div class="text-blue-100">This Month</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold mb-2" id="uniqueInstructors">0</div>
                        <div class="text-blue-100">Unique Instructors</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold mb-2">4.8★</div>
                        <div class="text-blue-100">Average Rating</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Certificates Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12" id="certificatesContainer">
            <!-- Certificates will be loaded here -->
        </div>

        <!-- Empty State -->
        <div class="text-center py-16" id="emptyState" style="display: none;">
            <div class="w-24 h-24 bg-gradient-to-r from-gray-100 to-gray-200 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-certificate text-4xl text-gray-400"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-3">No certificates yet</h3>
            <p class="text-gray-600 mb-6 max-w-md mx-auto">
                Complete courses to earn certificates and showcase your achievements.
            </p>
            <a href="/courses" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold rounded-xl hover:from-blue-700 hover:to-purple-700 transition-all duration-300 transform hover:scale-105 shadow-lg">
                <i class="fas fa-search mr-2"></i>Browse Courses
            </a>
        </div>

        <!-- Share Section -->
        <div class="bg-white rounded-2xl shadow-xl p-8 hover-lift border border-gray-100 text-center">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">
                <i class="fas fa-share-alt text-blue-600 mr-2"></i>Share Your Achievements
            </h2>
            <p class="text-gray-600 mb-6">
                Proud of your accomplishments? Share your certificates on social media or add them to your LinkedIn profile.
            </p>
            <div class="flex flex-wrap justify-center gap-4">
                <button class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fab fa-linkedin mr-2"></i>LinkedIn
                </button>
                <button class="inline-flex items-center px-4 py-2 bg-blue-400 text-white rounded-lg hover:bg-blue-500 transition-colors">
                    <i class="fab fa-twitter mr-2"></i>Twitter
                </button>
                <button class="inline-flex items-center px-4 py-2 bg-blue-800 text-white rounded-lg hover:bg-blue-900 transition-colors">
                    <i class="fab fa-facebook mr-2"></i>Facebook
                </button>
            </div>
        </div>
    </div>
</div>

<script src="/js/certificates.js"></script>
@endsection
