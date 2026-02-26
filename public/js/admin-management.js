document.addEventListener('alpine:init', () => {
    Alpine.data('adminManager', () => ({
        // State management
        activeTab: 'dashboard',
        loading: false,

        // Data arrays
        users: [],
        courses: [],
        categories: [],
        enrollments: [],
        badges: [],
        notifications: [],

        // Stats
        stats: {
            totalUsers: 0,
            totalCourses: 0,
            totalEnrollments: 0,
            totalRevenue: 0
        },

        // Search and filters
        userSearch: '',
        userRoleFilter: '',
        courseSearch: '',

        // Pagination
        usersCurrentPage: 0,
        usersPerPage: 10,

        // Modal states
        showCreateUserModal: false,
        showCreateCourseModal: false,
        showCreateCategoryModal: false,
        showCreateBadgeModal: false,
        showCreateNotificationModal: false,

        // Computed properties
        get filteredUsers() {
            return this.users.filter(user => {
                const matchesSearch = !this.userSearch ||
                    user.name.toLowerCase().includes(this.userSearch.toLowerCase()) ||
                    user.email.toLowerCase().includes(this.userSearch.toLowerCase());
                const matchesRole = !this.userRoleFilter || user.role === this.userRoleFilter;
                return matchesSearch && matchesRole;
            });
        },

        get filteredCourses() {
            return this.courses.filter(course => {
                return !this.courseSearch ||
                    course.title.toLowerCase().includes(this.courseSearch.toLowerCase()) ||
                    course.description.toLowerCase().includes(this.courseSearch.toLowerCase());
            });
        },

        get usersTableContent() {
            const start = this.usersCurrentPage * this.usersPerPage;
            const end = start + this.usersPerPage;
            const paginatedUsers = this.filteredUsers.slice(start, end);

            return paginatedUsers.map(user => `
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10">
                                <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                    <span class="text-sm font-medium text-gray-700">${user.name.charAt(0).toUpperCase()}</span>
                                </div>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">${user.name}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${user.email}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ${this.getRoleBadgeClass(user.role)}">
                            ${user.role}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ${user.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                            ${user.status}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <button @click="editUser(${user.id})" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>
                        <button @click="toggleUserStatus(${user.id})" class="text-${user.status === 'active' ? 'red' : 'green'}-600 hover:text-${user.status === 'active' ? 'red' : 'green'}-900 mr-3">
                            ${user.status === 'active' ? 'Deactivate' : 'Activate'}
                        </button>
                        <button @click="deleteUser(${user.id})" class="text-red-600 hover:text-red-900">Delete</button>
                    </td>
                </tr>
            `).join('');
        },

        get coursesTableContent() {
            return this.filteredCourses.map(course => `
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10">
                                <img class="h-10 w-10 rounded-lg object-cover" src="${course.thumbnail || '/images/course-placeholder.jpg'}" alt="">
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">${course.title}</div>
                                <div class="text-sm text-gray-500">${course.description.substring(0, 50)}...</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${course.instructor_name}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${course.category_name}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ${course.status === 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'}">
                            ${course.status}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${course.enrollment_count}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <button @click="editCourse(${course.id})" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>
                        <button @click="toggleCourseStatus(${course.id})" class="text-${course.status === 'published' ? 'yellow' : 'green'}-600 hover:text-${course.status === 'published' ? 'yellow' : 'green'}-900 mr-3">
                            ${course.status === 'published' ? 'Unpublish' : 'Publish'}
                        </button>
                        <button @click="deleteCourse(${course.id})" class="text-red-600 hover:text-red-900">Delete</button>
                    </td>
                </tr>
            `).join('');
        },

        get categoriesTableContent() {
            return this.categories.map(category => `
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${category.name}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${category.slug}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${category.parent_name || 'None'}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${category.courses_count}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <button @click="editCategory(${category.id})" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>
                        <button @click="deleteCategory(${category.id})" class="text-red-600 hover:text-red-900">Delete</button>
                    </td>
                </tr>
            `).join('');
        },

        get enrollmentsTableContent() {
            return this.enrollments.map(enrollment => `
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${enrollment.user_name}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${enrollment.course_title}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ${this.getEnrollmentStatusClass(enrollment.status)}">
                            ${enrollment.status}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${enrollment.progress}%</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${this.formatDate(enrollment.enrolled_at)}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <button @click="viewEnrollment(${enrollment.id})" class="text-indigo-600 hover:text-indigo-900 mr-3">View</button>
                        <button @click="updateEnrollmentStatus(${enrollment.id}, '${enrollment.status === 'active' ? 'completed' : 'active'}')" class="text-blue-600 hover:text-blue-900">
                            ${enrollment.status === 'active' ? 'Complete' : 'Reactivate'}
                        </button>
                    </td>
                </tr>
            `).join('');
        },

        get badgesTableContent() {
            return this.badges.map(badge => `
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10">
                                <span class="text-2xl">${badge.icon}</span>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">${badge.name}</div>
                                <div class="text-sm text-gray-500">${badge.description}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${badge.code}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${badge.users_count}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <button @click="editBadge(${badge.id})" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>
                        <button @click="deleteBadge(${badge.id})" class="text-red-600 hover:text-red-900">Delete</button>
                    </td>
                </tr>
            `).join('');
        },

        get notificationsTableContent() {
            return this.notifications.map(notification => `
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${notification.user_name}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${notification.type}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${notification.message.substring(0, 50)}...</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ${notification.is_read ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'}">
                            ${notification.is_read ? 'Read' : 'Unread'}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${this.formatDate(notification.created_at)}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <button @click="resendNotification(${notification.id})" class="text-blue-600 hover:text-blue-900 mr-3">Resend</button>
                        <button @click="deleteNotification(${notification.id})" class="text-red-600 hover:text-red-900">Delete</button>
                    </td>
                </tr>
            `).join('');
        },

        // Methods
        async init() {
            await this.refreshData();
        },

        async refreshData() {
            this.loading = true;
            try {
                await Promise.all([
                    this.loadStats(),
                    this.loadUsers(),
                    this.loadCourses(),
                    this.loadCategories(),
                    this.loadEnrollments(),
                    this.loadBadges(),
                    this.loadNotifications()
                ]);
            } catch (error) {
                console.error('Error refreshing data:', error);
                this.showToast('Error loading data', 'error');
            } finally {
                this.loading = false;
            }
        },

        async loadStats() {
            try {
                const response = await fetch('/graphql', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        query: `
                            query {
                                usersCount
                                coursesCount
                                enrollmentsCount
                                totalRevenue
                            }
                        `
                    })
                });
                const result = await response.json();
                if (result.data) {
                    this.stats = {
                        totalUsers: result.data.usersCount,
                        totalCourses: result.data.coursesCount,
                        totalEnrollments: result.data.enrollmentsCount,
                        totalRevenue: result.data.totalRevenue
                    };
                }
            } catch (error) {
                console.error('Error loading stats:', error);
            }
        },

        async loadUsers() {
            try {
                const response = await fetch('/graphql', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        query: `
                            query {
                                users {
                                    id
                                    name
                                    email
                                    role
                                    status
                                }
                            }
                        `
                    })
                });
                const result = await response.json();
                if (result.data) {
                    this.users = result.data.users;
                }
            } catch (error) {
                console.error('Error loading users:', error);
            }
        },

        async loadCourses() {
            try {
                const response = await fetch('/graphql', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        query: `
                            query {
                                courses {
                                    id
                                    title
                                    description
                                    thumbnail
                                    status
                                    instructor {
                                        name
                                    }
                                    category {
                                        name
                                    }
                                    enrollments {
                                        id
                                    }
                                }
                            }
                        `
                    })
                });
                const result = await response.json();
                if (result.data) {
                    this.courses = result.data.courses.map(course => ({
                        ...course,
                        instructor_name: course.instructor?.name || 'Unknown',
                        category_name: course.category?.name || 'Uncategorized',
                        enrollment_count: course.enrollments?.length || 0
                    }));
                }
            } catch (error) {
                console.error('Error loading courses:', error);
            }
        },

        async loadCategories() {
            try {
                const response = await fetch('/graphql', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        query: `
                            query {
                                categories {
                                    id
                                    name
                                    slug
                                    parent {
                                        name
                                    }
                                    courses {
                                        id
                                    }
                                }
                            }
                        `
                    })
                });
                const result = await response.json();
                if (result.data) {
                    this.categories = result.data.categories.map(category => ({
                        ...category,
                        parent_name: category.parent?.name,
                        courses_count: category.courses?.length || 0
                    }));
                }
            } catch (error) {
                console.error('Error loading categories:', error);
            }
        },

        async loadEnrollments() {
            try {
                const response = await fetch('/graphql', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        query: `
                            query {
                                enrollments {
                                    id
                                    status
                                    progress
                                    enrolled_at
                                    user {
                                        name
                                    }
                                    course {
                                        title
                                    }
                                }
                            }
                        `
                    })
                });
                const result = await response.json();
                if (result.data) {
                    this.enrollments = result.data.enrollments.map(enrollment => ({
                        ...enrollment,
                        user_name: enrollment.user?.name || 'Unknown',
                        course_title: enrollment.course?.title || 'Unknown Course'
                    }));
                }
            } catch (error) {
                console.error('Error loading enrollments:', error);
            }
        },

        async loadBadges() {
            try {
                const response = await fetch('/graphql', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        query: `
                            query {
                                badges {
                                    id
                                    name
                                    description
                                    code
                                    icon
                                    userBadges {
                                        id
                                    }
                                }
                            }
                        `
                    })
                });
                const result = await response.json();
                if (result.data) {
                    this.badges = result.data.badges.map(badge => ({
                        ...badge,
                        users_count: badge.userBadges?.length || 0
                    }));
                }
            } catch (error) {
                console.error('Error loading badges:', error);
            }
        },

        async loadNotifications() {
            try {
                const response = await fetch('/graphql', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        query: `
                            query {
                                notifications {
                                    id
                                    type
                                    message
                                    is_read
                                    created_at
                                    user {
                                        name
                                    }
                                }
                            }
                        `
                    })
                });
                const result = await response.json();
                if (result.data) {
                    this.notifications = result.data.notifications.map(notification => ({
                        ...notification,
                        user_name: notification.user?.name || 'System'
                    }));
                }
            } catch (error) {
                console.error('Error loading notifications:', error);
            }
        },

        // Helper methods
        getRoleBadgeClass(role) {
            const classes = {
                admin: 'bg-red-100 text-red-800',
                instructor: 'bg-blue-100 text-blue-800',
                student: 'bg-green-100 text-green-800'
            };
            return classes[role] || 'bg-gray-100 text-gray-800';
        },

        getEnrollmentStatusClass(status) {
            const classes = {
                active: 'bg-green-100 text-green-800',
                completed: 'bg-blue-100 text-blue-800',
                cancelled: 'bg-red-100 text-red-800'
            };
            return classes[status] || 'bg-gray-100 text-gray-800';
        },

        formatDate(dateString) {
            return new Date(dateString).toLocaleDateString();
        },

        showToast(message, type = 'info') {
            // Implement toast notification
            console.log(`${type}: ${message}`);
        },

        // CRUD operations (placeholders - implement based on your GraphQL mutations)
        async editUser(userId) {
            // Implement edit user modal
            console.log('Edit user:', userId);
        },

        async toggleUserStatus(userId) {
            // Implement toggle user status
            console.log('Toggle user status:', userId);
        },

        async deleteUser(userId) {
            if (confirm('Are you sure you want to delete this user?')) {
                // Implement delete user
                console.log('Delete user:', userId);
            }
        },

        async editCourse(courseId) {
            // Implement edit course modal
            console.log('Edit course:', courseId);
        },

        async toggleCourseStatus(courseId) {
            // Implement toggle course status
            console.log('Toggle course status:', courseId);
        },

        async deleteCourse(courseId) {
            if (confirm('Are you sure you want to delete this course?')) {
                // Implement delete course
                console.log('Delete course:', courseId);
            }
        },

        async editCategory(categoryId) {
            // Implement edit category modal
            console.log('Edit category:', categoryId);
        },

        async deleteCategory(categoryId) {
            if (confirm('Are you sure you want to delete this category?')) {
                // Implement delete category
                console.log('Delete category:', categoryId);
            }
        },

        async viewEnrollment(enrollmentId) {
            // Implement view enrollment details
            console.log('View enrollment:', enrollmentId);
        },

        async updateEnrollmentStatus(enrollmentId, status) {
            // Implement update enrollment status
            console.log('Update enrollment status:', enrollmentId, status);
        },

        async editBadge(badgeId) {
            // Implement edit badge modal
            console.log('Edit badge:', badgeId);
        },

        async deleteBadge(badgeId) {
            if (confirm('Are you sure you want to delete this badge?')) {
                // Implement delete badge
                console.log('Delete badge:', badgeId);
            }
        },

        async resendNotification(notificationId) {
            // Implement resend notification
            console.log('Resend notification:', notificationId);
        },

        async deleteNotification(notificationId) {
            if (confirm('Are you sure you want to delete this notification?')) {
                // Implement delete notification
                console.log('Delete notification:', notificationId);
            }
        }
    }));
});
