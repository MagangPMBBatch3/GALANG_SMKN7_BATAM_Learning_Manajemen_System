function dashboardMethods() {
    return {
        activeTab: "dashboard",
        _graphqlEndpoint: null,
        loading: false,
        stats: {},
        users: [],
        courses: [],
        categories: [],
        enrollments: [],
        badges: [],
        notifications: [],
        userSearch: "",
        userRoleFilter: "",
        courseSearch: "",
        usersCurrentPage: 0,
        usersPerPage: 10,

        init() {
            this.loadData();
        },

        async doGraphql(query, variables = null) {
            // cached working endpoint
            const tryHeaders = {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": getCsrfToken(),
            };

            const meta = document
                .querySelector('meta[name="graphql-endpoint"]')
                ?.getAttribute("content");
            const candidates = [];
            if (meta) candidates.push(meta);
            candidates.push("/graphql");
            candidates.push("/public/index.php/graphql");
            candidates.push("/index.php/graphql");

            // try building from current path (handles subfolder like /maxcourse)
            try {
                const parts = window.location.pathname
                    .split("/")
                    .filter(Boolean);
                if (parts.length > 0) {
                    const prefix = "/" + parts[0];
                    candidates.push(
                        window.location.origin +
                            prefix +
                            "/public/index.php/graphql"
                    );
                }
                candidates.push(
                    window.location.origin + "/public/index.php/graphql"
                );
                candidates.push(window.location.origin + "/graphql");
            } catch (e) {
                // ignore
            }

            // ensure uniqueness and preserve order
            const seen = new Set();
            const uniq = candidates.filter((c) => {
                if (!c) return false;
                if (seen.has(c)) return false;
                seen.add(c);
                return true;
            });

            // if cached endpoint exists, try it first
            if (this._graphqlEndpoint) uniq.unshift(this._graphqlEndpoint);

            for (const endpoint of uniq) {
                try {
                    const resp = await fetch(endpoint, {
                        method: "POST",
                        headers: tryHeaders,
                        body: JSON.stringify({ query, variables }),
                    });

                    // if 404, try next candidate
                    if (resp.status === 404) continue;

                    const text = await resp.text();
                    try {
                        const json = JSON.parse(text);
                        // cache working endpoint
                        this._graphqlEndpoint = endpoint;
                        console.debug("GraphQL endpoint detected:", endpoint);
                        console.debug("GraphQL response:", json);
                        return json;
                    } catch (err) {
                        // not JSON (probably HTML error page) - try next
                        continue;
                    }
                } catch (err) {
                    // network/CORS error - try next
                    continue;
                }
            }

            throw new Error(
                "Could not reach GraphQL endpoint (tried multiple candidates)."
            );
        },

        refreshData() {
            this.loadData();
        },

        loadData() {
            this.loading = true;
            this.loadStats();
            this.loadUsers();
            this.loadCourses();
            this.loadCategories();
            this.loadEnrollments();
            this.loadBadges();
            this.loadNotifications();
            this.loading = false;
        },

        async loadStats() {
            try {
                const query = `
                    query {
                        systemStats {
                            total_users
                            total_courses
                            total_enrollments
                            total_revenue
                            active_users_last_30_days
                        }
                    }
                `;

                const result = await this.doGraphql(query, null);
                if (result.data) {
                    this.stats = {
                        totalUsers: result.data.systemStats.total_users,
                        totalCourses: result.data.systemStats.total_courses,
                        totalEnrollments:
                            result.data.systemStats.total_enrollments,
                        totalRevenue: result.data.systemStats.total_revenue,
                        activeUsers:
                            result.data.systemStats.active_users_last_30_days,
                    };
                }
            } catch (error) {
                console.error("Error loading stats:", error);
            }
        },

        async loadUsers() {
            try {
                // Use hardcoded parameters instead of variables (GraphQL schema issue with variables)
                const first = this.usersPerPage;
                const page = this.usersCurrentPage + 1;
                const query = `
                    {
                        users(first: ${first}, page: ${page}) {
                            data {
                                id
                                name
                                email
                                username
                                is_active
                                created_at
                                roles {
                                    name
                                    display_name
                                }
                            }
                            paginatorInfo {
                                currentPage
                                lastPage
                                total
                            }
                        }
                    }
                `;

                const result = await this.doGraphql(query, null);
                console.debug("loadUsers result:", result);
                if (result && result.data) {
                    // handle both paginated { data: [...] } and non-paginated arrays
                    if (Array.isArray(result.data.users)) {
                        this.users = result.data.users;
                    } else if (
                        result.data.users &&
                        Array.isArray(result.data.users.data)
                    ) {
                        this.users = result.data.users.data;
                    } else {
                        this.users = [];
                    }
                }
            } catch (error) {
                console.error("Error loading users:", error);
            }
        },

        async loadCourses() {
            try {
                const query = `
                    {
                        courses(first: 10, page: 1) {
                            data {
                                id
                                title
                                slug
                                short_description
                                price
                                currency
                                is_published
                                status
                                level
                                duration_minutes
                                rating_avg
                                rating_count
                                enrollments_count
                                created_at
                                instructor {
                                    id
                                    name
                                }
                                category {
                                    id
                                    name
                                }
                            }
                            paginatorInfo {
                                currentPage
                                lastPage
                                total
                            }
                        }
                    }
                `;

                const result = await this.doGraphql(query, null);
                if (result && result.data && result.data.courses) {
                    this.courses =
                        result.data.courses.data || result.data.courses;
                }
            } catch (error) {
                console.error("Error loading courses:", error);
            }
        },

        async loadCategories() {
            try {
                const query = `
                    {
                        categories(first: 10, page: 1) {
                            data {
                                id
                                name
                                slug
                                description
                                parent_id
                                created_at
                            }
                            paginatorInfo {
                                currentPage
                                lastPage
                                total
                            }
                        }
                    }
                `;

                const result = await this.doGraphql(query, null);
                if (result && result.data && result.data.categories) {
                    this.categories =
                        result.data.categories.data || result.data.categories;
                }
            } catch (error) {
                console.error("Error loading categories:", error);
            }
        },

        async loadEnrollments() {
            try {
                const query = `
                    {
                        enrollments(first: 10, page: 1) {
                            data {
                                id
                                user_id
                                course_id
                                enrolled_at
                                status
                                progress_percent
                                price_paid
                                currency
                                user {
                                    id
                                    name
                                    email
                                }
                                course {
                                    id
                                    title
                                    slug
                                }
                            }
                            paginatorInfo {
                                currentPage
                                lastPage
                                total
                            }
                        }
                    }
                `;

                const result = await this.doGraphql(query, null);
                if (result && result.data && result.data.enrollments) {
                    this.enrollments =
                        result.data.enrollments.data || result.data.enrollments;
                }
            } catch (error) {
                console.error("Error loading enrollments:", error);
            }
        },

        async loadBadges() {
            try {
                const query = `
                    {
                        badges(first: 10, page: 1) {
                            data {
                                id
                                code
                                name
                                description
                                icon_url
                                created_at
                            }
                            paginatorInfo {
                                currentPage
                                lastPage
                                total
                            }
                        }
                    }
                `;

                const result = await this.doGraphql(query, null);
                if (result && result.data && result.data.badges) {
                    this.badges = result.data.badges.data || result.data.badges;
                }
            } catch (error) {
                console.error("Error loading badges:", error);
            }
        },

        async loadNotifications() {
            try {
                const query = `
                    {
                        notifications(first: 10, page: 1) {
                            data {
                                id
                                title
                                message
                                type
                                is_read
                                created_at
                                user {
                                    id
                                    name
                                }
                            }
                            paginatorInfo {
                                currentPage
                                lastPage
                                total
                            }
                        }
                    }
                `;

                const result = await this.doGraphql(query, null);
                if (result && result.data && result.data.notifications) {
                    this.notifications =
                        result.data.notifications.data ||
                        result.data.notifications;
                }
            } catch (error) {
                console.error("Error loading notifications:", error);
            }
        },

        get usersTableContent() {
            if (this.users.length === 0) {
                return '<tr><td colspan="6" class="text-center py-4">No users found</td></tr>';
            }

            return this.users
                .map(
                    (user) => `
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                <span class="text-white text-sm font-bold">${user.name
                                    .charAt(0)
                                    .toUpperCase()}</span>
                            </div>
                            <div class="ml-3">
                                <div class="text-sm font-medium text-gray-900">${
                                    user.name
                                }</div>
                                <div class="text-sm text-gray-500">${
                                    user.username
                                }</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${
                        user.email
                    }</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex flex-wrap gap-1">
                            ${user.roles
                                .map(
                                    (role) =>
                                        `<span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">${role.display_name}</span>`
                                )
                                .join("")}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${
                            user.is_active
                                ? "bg-green-100 text-green-800"
                                : "bg-red-100 text-red-800"
                        }">
                            ${user.is_active ? "Active" : "Inactive"}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        ${formatDate(user.created_at)}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <button onclick="window.admin().editUser(${
                            user.id
                        })" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>
                        <button onclick="window.admin().deleteUser(${
                            user.id
                        })" class="text-red-600 hover:text-red-900">Delete</button>
                    </td>
                </tr>
            `
                )
                .join("");
        },

        get coursesTableContent() {
            if (this.courses.length === 0) {
                return '<tr><td colspan="8" class="text-center py-4">No courses found</td></tr>';
            }

            return this.courses
                .map(
                    (course) => `
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <img class="h-10 w-10 rounded-lg object-cover" src="${
                                course.thumbnail_url ||
                                "/images/course-placeholder.jpg"
                            }" alt="${course.title}">
                            <div class="ml-3">
                                <div class="text-sm font-medium text-gray-900">${
                                    course.title
                                }</div>
                                <div class="text-sm text-gray-500">${
                                    course.slug
                                }</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${
                        course.instructor?.name || "Unknown"
                    }</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${
                        course.category?.name || "Uncategorized"
                    }</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${
                        course.price
                    } ${course.currency}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${
                            course.is_published
                                ? "bg-green-100 text-green-800"
                                : "bg-yellow-100 text-yellow-800"
                        }">
                            ${course.is_published ? "Published" : "Draft"}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${
                        course.enrollments_count || 0
                    }</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        ${formatDate(course.created_at)}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <button onclick="window.admin().editCourse(${
                            course.id
                        })" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>
                        <button onclick="window.admin().deleteCourse(${
                            course.id
                        })" class="text-red-600 hover:text-red-900">Delete</button>
                    </td>
                </tr>
            `
                )
                .join("");
        },

        get categoriesTableContent() {
            if (this.categories.length === 0) {
                return '<tr><td colspan="5" class="text-center py-4">No categories found</td></tr>';
            }

            return this.categories
                .map(
                    (category) => `
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${
                        category.name
                    }</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${
                        category.slug
                    }</td>
                    <td class="px-6 py-4 text-sm text-gray-900">${
                        category.description || "No description"
                    }</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${
                        category.parent_id ? "Subcategory" : "Main Category"
                    }</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        ${formatDate(category.created_at)}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <button onclick="window.admin().editCategory(${
                            category.id
                        })" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>
                        <button onclick="window.admin().deleteCategory(${
                            category.id
                        })" class="text-red-600 hover:text-red-900">Delete</button>
                    </td>
                </tr>
            `
                )
                .join("");
        },

        get enrollmentsTableContent() {
            if (this.enrollments.length === 0) {
                return '<tr><td colspan="6" class="text-center py-4">No enrollments found</td></tr>';
            }

            return this.enrollments
                .map(
                    (enrollment) => `
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${
                        enrollment.user?.name || "Unknown"
                    }</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${
                        enrollment.user?.email || "N/A"
                    }</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${
                        enrollment.course?.title || "Unknown Course"
                    }</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${
                            enrollment.status === "completed"
                                ? "bg-green-100 text-green-800"
                                : enrollment.status === "in_progress"
                                ? "bg-blue-100 text-blue-800"
                                : "bg-yellow-100 text-yellow-800"
                        }">
                            ${enrollment.status || "Unknown"}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${
                        enrollment.progress_percent || 0
                    }%</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        ${formatDate(enrollment.enrolled_at)}
                    </td>
                </tr>
            `
                )
                .join("");
        },

        get badgesTableContent() {
            if (this.badges.length === 0) {
                return '<tr><td colspan="5" class="text-center py-4">No badges found</td></tr>';
            }

            return this.badges
                .map(
                    (badge) => `
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${
                        badge.name
                    }</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${
                        badge.code
                    }</td>
                    <td class="px-6 py-4 text-sm text-gray-900">${
                        badge.description || "No description"
                    }</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        ${formatDate(badge.created_at)}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <button onclick="window.admin().editBadge(${
                            badge.id
                        })" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>
                        <button onclick="window.admin().deleteBadge(${
                            badge.id
                        })" class="text-red-600 hover:text-red-900">Delete</button>
                    </td>
                </tr>
            `
                )
                .join("");
        },

        get notificationsTableContent() {
            if (this.notifications.length === 0) {
                return '<tr><td colspan="7" class="text-center py-4">No notifications found</td></tr>';
            }

            return this.notifications
                .map(
                    (notification) => `
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${
                        notification.title
                    }</td>
                    <td class="px-6 py-4 text-sm text-gray-900">${
                        notification.message || "No message"
                    }</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${
                        notification.type
                    }</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${
                            notification.is_read
                                ? "bg-green-100 text-green-800"
                                : "bg-yellow-100 text-yellow-800"
                        }">
                            ${notification.is_read ? "Read" : "Unread"}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${
                        notification.user?.name || "System"
                    }</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        ${formatDate(notification.created_at)}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <button onclick="window.admin().editNotification(${
                            notification.id
                        })" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>
                        <button onclick="window.admin().deleteNotification(${
                            notification.id
                        })" class="text-red-600 hover:text-red-900">Delete</button>
                    </td>
                </tr>
            `
                )
                .join("");
        },
    };
}
