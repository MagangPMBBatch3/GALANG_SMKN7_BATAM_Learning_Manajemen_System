const userProfileId = document.querySelector('meta[name="user-profile-id"]')?.getAttribute('content');
const userLevelName = document.querySelector('meta[name="user-level-name"]')?.getAttribute('content') || 'User';

function getCsrfToken() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    if (!meta) {
        throw new Error('CSRF token not found. Please ensure <meta name="csrf-token"> is included in the HTML.');
    }
    return meta.getAttribute('content');
}

async function loadDashboardData() {
    try {
        const query = buildDashboardQuery();
        const response = await fetch('/graphql', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken()
            },
            body: JSON.stringify({ query })
        });

        const result = await response.json();
        console.log('GraphQL response (loadDashboardData):', result);
        if (result.errors) {
            throw new Error(result.errors.map(e => e.message).join(', '));
        }

        if (result.data) {
            updateDashboardStats(result.data);
            renderRecentActivity(result.data);
            renderMyCourses(result.data);
        }
    } catch (error) {
        console.error('Error loading dashboard data:', error);
        showError('Gagal memuat data dashboard: ' + error.message);
    }
}

function buildDashboardQuery() {
    if (userLevelName === 'Admin') {
        return `
            query {
                dashboardStats {
                    totalUsers
                    totalCourses
                    totalEnrollments
                    totalRevenue
                }
                recentActivity {
                    id
                    type
                    description
                    created_at
                    user {
                        name
                    }
                }
                recentCourses {
                    id
                    title
                    slug
                    thumbnail_url
                    enrollments_count
                    created_at
                }
            }
        `;
    } else if (userLevelName === 'Instructor') {
        return `
            query {
                instructorStats(user_id: ${parseInt(userProfileId)}) {
                    totalCourses
                    totalStudents
                    totalRevenue
                    averageRating
                }
                myCourses(user_id: ${parseInt(userProfileId)}) {
                    id
                    title
                    slug
                    thumbnail_url
                    enrollments_count
                    rating_avg
                    created_at
                }
                recentEnrollments(user_id: ${parseInt(userProfileId)}) {
                    id
                    course {
                        title
                    }
                    user {
                        name
                    }
                    created_at
                }
            }
        `;
    } else {
        return `
            query {
                userStats(user_id: ${parseInt(userProfileId)}) {
                    total_courses_enrolled
                    total_courses_completed
                    total_quizzes_taken
                    average_quiz_score
                    total_points
                    total_badges
                }
                enrollments(user_id: ${parseInt(userProfileId)}, first: 10) {
                    data {
                        id
                        course {
                            id
                            title
                            slug
                            thumbnail_url
                        }
                        progress_percent
                        enrolled_at
                    }
                }
                notifications(user_id: ${parseInt(userProfileId)}, first: 5) {
                    data {
                        id
                        type
                        payload
                        sent_at
                    }
                }
            }
        `;
    }
}

function updateDashboardStats(data) {
    if (userLevelName === 'Admin') {
        document.getElementById('totalUsers').textContent = data.dashboardStats?.totalUsers || 0;
        document.getElementById('totalCourses').textContent = data.dashboardStats?.totalCourses || 0;
        document.getElementById('totalEnrollments').textContent = data.dashboardStats?.totalEnrollments || 0;
        document.getElementById('totalRevenue').textContent = '$' + (data.dashboardStats?.totalRevenue || 0);
    } else if (userLevelName === 'Instructor') {
        document.getElementById('totalCourses').textContent = data.instructorStats?.totalCourses || 0;
        document.getElementById('totalStudents').textContent = data.instructorStats?.totalStudents || 0;
        document.getElementById('totalRevenue').textContent = '$' + (data.instructorStats?.totalRevenue || 0);
        document.getElementById('averageRating').textContent = (data.instructorStats?.averageRating || 0).toFixed(1);
    } else {
        document.getElementById('enrolledCourses').textContent = data.userStats?.total_courses_enrolled || 0;
        document.getElementById('completedCourses').textContent = data.userStats?.total_courses_completed || 0;
        document.getElementById('totalHours').textContent = data.userStats?.total_quizzes_taken || 0;
        document.getElementById('certificatesEarned').textContent = data.userStats?.total_badges || 0;
    }
}

function renderRecentActivity(data) {
    const container = document.getElementById('recentActivityContainer');
    let activities = [];

    if (userLevelName === 'Admin') {
        activities = data.recentActivity || [];
    } else if (userLevelName === 'Instructor') {
        activities = data.recentEnrollments?.map(enrollment => ({
            id: enrollment.id,
            type: 'enrollment',
            description: `${enrollment.user.name} mendaftar kursus ${enrollment.course.title}`,
            created_at: enrollment.created_at
        })) || [];
    } else {
        activities = data.notifications?.data?.map(notification => ({
            id: notification.id,
            type: notification.type,
            description: notification.payload || 'Notifikasi baru',
            created_at: notification.sent_at
        })) || [];
    }

    container.innerHTML = activities.length ? '' : '<p class="text-gray-500">Tidak ada aktivitas terbaru.</p>';

    activities.slice(0, 5).forEach(activity => {
        const activityDiv = document.createElement('div');
        activityDiv.className = 'flex items-start space-x-3 p-3 bg-gray-50 rounded-lg';
        activityDiv.innerHTML = `
            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                <i class="fas fa-${getActivityIcon(activity.type)} text-blue-600"></i>
            </div>
            <div class="flex-1">
                <p class="text-sm text-gray-900">${activity.description}</p>
                <p class="text-xs text-gray-500">${formatDate(activity.created_at)}</p>
            </div>
        `;
        container.appendChild(activityDiv);
    });
}

function renderMyCourses(data) {
    const container = document.getElementById('myCoursesContainer');
    let courses = [];

    if (userLevelName === 'Admin') {
        courses = data.recentCourses || [];
    } else if (userLevelName === 'Instructor') {
        courses = data.myCourses || [];
    } else {
        courses = data.enrollments?.data?.map(enrollment => ({
            id: enrollment.course.id,
            title: enrollment.course.title,
            slug: enrollment.course.slug,
            thumbnail_url: enrollment.course.thumbnail_url,
            progress_percentage: enrollment.progress_percent,
            last_accessed_at: enrollment.enrolled_at
        })) || [];
    }

    container.innerHTML = courses.length ? '' : '<p class="text-gray-500">Tidak ada kursus.</p>';

    courses.slice(0, 3).forEach(course => {
        const courseDiv = document.createElement('div');
        courseDiv.className = 'bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-all duration-200 cursor-pointer';
        courseDiv.onclick = () => window.location.href = `/courses/${course.slug}`;
        courseDiv.innerHTML = `
            <img src="${course.thumbnail_url || '/images/course-placeholder.jpg'}" alt="${course.title}" class="w-full h-32 object-cover">
            <div class="p-4">
                <h3 class="font-semibold text-gray-900 mb-2 line-clamp-2">${course.title}</h3>
                ${userLevelName === 'User' ? `
                    <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: ${course.progress_percentage || 0}%"></div>
                    </div>
                    <p class="text-xs text-gray-600">${course.progress_percentage || 0}% selesai</p>
                ` : `
                    <div class="flex justify-between items-center text-sm text-gray-600">
                        <span>${course.enrollments_count || 0} siswa</span>
                        <span>${(course.rating_avg || 0).toFixed(1)} ⭐</span>
                    </div>
                `}
            </div>
        `;
        container.appendChild(courseDiv);
    });
}

function getActivityIcon(type) {
    const icons = {
        'enrollment': 'user-plus',
        'completion': 'check-circle',
        'review': 'star',
        'course_created': 'plus-circle'
    };
    return icons[type] || 'circle';
}

function formatDate(dateString) {
    return new Date(dateString).toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric'
    });
}

function showError(message) {
    const errorDiv = document.createElement('div');
    errorDiv.className = 'fixed top-4 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded';
    errorDiv.innerHTML = `<span>${message}</span><button onclick="this.parentElement.remove()" class="ml-4 text-red-700">x</button>`;
    document.body.appendChild(errorDiv);
    setTimeout(() => errorDiv.remove(), 5000);
}

document.addEventListener('DOMContentLoaded', () => {
    if (!['Admin', 'Instructor', 'User'].includes(userLevelName)) {
        showError('Level pengguna tidak valid.');
        return;
    }
    loadDashboardData();
});
