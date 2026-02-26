<div x-show="activeTab === 'dashboard'" x-transition>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                        <i class="fas fa-users text-white"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <dt class="text-sm font-medium text-gray-500 truncate">Total Pengguna</dt>
                    <dd class="text-2xl font-semibold text-gray-900" x-text="stats.totalUsers || 0"></dd>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                        <i class="fas fa-graduation-cap text-white"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <dt class="text-sm font-medium text-gray-500 truncate">Total Kursus</dt>
                    <dd class="text-2xl font-semibold text-gray-900" x-text="stats.totalCourses || 0"></dd>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                        <i class="fas fa-user-check text-white"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <dt class="text-sm font-medium text-gray-500 truncate">Pendaftaran</dt>
                    <dd class="text-2xl font-semibold text-gray-900" x-text="stats.totalEnrollments || 0"></dd>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                        <i class="fas fa-dollar-sign text-white"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <dt class="text-sm font-medium text-gray-500 truncate">Pendapatan</dt>
                    <dd class="text-2xl font-semibold text-gray-900">Rp <span x-text="stats.totalRevenue || 0"></span></dd>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Pengguna Terbaru</h3>
            </div>
            <div class="p-6">
                <div id="recentUsersContainer">
                    <div class="animate-pulse">
                        <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
                        <div class="h-4 bg-gray-200 rounded w-1/2"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Kursus Terbaru</h3>
            </div>
            <div class="p-6">
                <div id="recentCoursesContainer">
                    <div class="animate-pulse">
                        <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
                        <div class="h-4 bg-gray-200 rounded w-1/2"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\maxcourse\resources\views\admin\dashboard.blade.php ENDPATH**/ ?>