const userProfileId = document
    .querySelector('meta[name="user-profile-id"]')
    ?.getAttribute("content");
const userLevelName =
    document
        .querySelector('meta[name="user-level-name"]')
        ?.getAttribute("content") || "User";

function getCsrfToken() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    if (!meta) {
        throw new Error(
            'CSRF token not found. Please ensure <meta name="csrf-token"> is included in the HTML.',
        );
    }
    return meta.getAttribute("content");
}

function getGraphqlEndpoint() {
    const meta = document.querySelector('meta[name="graphql-endpoint"]');
    if (meta) return meta.getAttribute("content");
    // fallback to relative path (will resolve relative to current page)
    // Try to build an absolute endpoint based on the current location.
    try {
        const origin = window.location.origin;
        const pathname = window.location.pathname || "";
        const marker = "/public/index.php";
        const idx = pathname.indexOf(marker);
        if (idx !== -1) {
            const prefix = pathname.substring(0, idx); // e.g. '/maxcourse'
            const endpoint = origin + prefix + marker + "/graphql";
            console.debug("Computed GraphQL endpoint:", endpoint);
            return endpoint;
        }
        const fallback = origin + "/public/index.php/graphql";
        console.debug("Fallback GraphQL endpoint:", fallback);
        return fallback;
    } catch (e) {
        return "graphql";
    }
}

function adminManager() {
    return {
        activeTab: "dashboard",
        _graphqlEndpoint: null,
        loading: false,
        stats: {},
        users: [],
        allUsers: [], // Cache untuk search across all pages
        courses: [],
        categories: [],
        enrollments: [],
        badges: [],
        notifications: [],
        grading: {
            submissions: [],
            currentSubmission: null,
            loading: false,
            showModal: false,
            grades: {},
        },
        quizzes: {
            list: [],
            loading: false,
        },
        roles: [],
        payments: [],
        userSearch: "",
        userRoleFilter: "",
        courseSearch: "",
        categorySearch: "",
        enrollmentSearch: "",
        badgeSearch: "",
        notificationSearch: "",
        usersCurrentPage: 0,
        usersPerPage: 10,
        usersPaginatorInfo: null,
        coursesCurrentPage: 0,
        coursesPerPage: 10,
        coursesPaginatorInfo: null,
        searchTimeout: null,
        debounceDelay: 400,
        debouncedUserSearch() {
            // Client-side search doesn't need debouncing since it's instant
            // Just trigger reactive updates by modifying userSearch
            // The filteredUsers computed property will handle filtering
        },
        debouncedCourseSearch() {
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => {
                this.coursesCurrentPage = 0;
                this.loadCourses();
            }, this.debounceDelay);
        },
        showCreateUserModal: false,
        showCreateCourseModal: false,
        showCreateCourseModal: false,
        showEditCourseModal: false,
        showModuleManager: false,
        courseForm: {
            id: null,
            title: "",
            category_id: "",
            short_description: "",
            full_description: "",
            price: 0,
            level: "beginner",
            duration_minutes: 0,
            is_published: false,
        },
        selectedCourse: null,
        modules: [],
        moduleForm: {
            id: null,
            course_id: null,
            title: "",
            description: "",
            position: 0,
        },
        showCreateModuleModal: false,
        showEditModuleModal: false,
        lessons: [],
        lessonForm: {
            id: null,
            module_id: null,
            course_id: null,
            title: "",
            content_type: "video",
            content: "",
            media_url: "",
            duration_seconds: 0,
            is_downloadable: false,
            position: 0,
            sourceType: "upload",
            // Quiz Fields
            quiz_title: "",
            quiz_description: "",
            passing_score: 70,
            time_limit_minutes: 0,
            attempts_allowed: 0,
            questions: [],
        },
        selectedModule: null,
        showLessonManager: false,
        showCreateLessonModal: false,
        showEditLessonModal: false,

        categoryForm: {
            id: null,
            name: "",
            slug: "",
            description: "",
        },
        showCreateCategoryModal: false,
        showEditCategoryModal: false,

        // Enrollment Management
        enrollmentSearch: "",
        enrollmentsCurrentPage: 0,
        enrollmentsPerPage: 10,
        enrollmentsPaginatorInfo: null,
        enrollmentStats: {},
        enrollmentFilters: {
            course_id: "",
            status: "",
            payment_status: "",
        },
        enrollmentForm: {
            id: null,
            student_name: "",
            course_title: "",
            status: "active",
            progress_percent: 0,
            price_paid: 0,
            currency: "IDR",
            expires_at: null,
        },

        // Payments Management
        paymentSearch: "",
        paymentFilters: {
            status: "",
            currency: "",
        },
        payments: [],
        paymentsCurrentPage: 0,
        paymentsPerPage: 10,
        paymentsPaginatorInfo: null,
        enrollmentDetails: {},
        showEditEnrollmentModal: false,
        showEnrollmentDetailsModal: false,

        // Badge Management
        badgeForm: {
            id: null,
            name: "",
            code: "",
            description: "",
        },
        showCreateBadgeModal: false,

        // Notification Management
        notificationSearch: "",
        notificationsCurrentPage: 0,
        notificationsPerPage: 10,
        notificationsPaginatorInfo: null,
        notificationPagination: {
            current_page: 1,
            data: [],
            first_page_url: "",
            from: null,
            last_page: 1,
            last_page_url: "",
            links: [],
            next_page_url: null,
            path: "",
            per_page: 10,
            prev_page_url: null,
            to: null,
            total: 0,
        },
        notificationFilters: {
            is_read: "",
            type: "",
        },
        notificationForm: {
            type: "",
            user_id: "",
            message: "",
        },
        showCreateNotificationModal: false,
        showEditUserModal: false,
        showEditCourseModal: false,
        showEditBadgeModal: false,
        showEditNotificationModal: false,
        editingUser: {},
        editingCourse: {},
        editingBadge: {},
        editingNotification: {},

        init() {
            // Load all data pada awal
            this.loadData();
        },

        // Helper methods untuk call global functions
        showSuccess(message) {
            return showSuccess(message);
        },

        showError(message) {
            return showError(message);
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
                            "/public/index.php/graphql",
                    );
                }
                candidates.push(
                    window.location.origin + "/public/index.php/graphql",
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
                "Could not reach GraphQL endpoint (tried multiple candidates).",
            );
        },

        refreshData() {
            this.loadData();
        },

        async loadData() {
            console.log("loadData() called");
            this.loading = true;
            try {
                // Load essential data first. Use allSettled so a GraphQL failure
                // doesn't block the REST-based data from loading.
                await Promise.allSettled([
                    this.loadStats(),
                    this.loadRoles(),
                    this.loadUsers(),
                ]);

                // Load remaining data in background (tidak blocking)
                Promise.allSettled([
                    this.loadCourses(),
                    this.loadCategories(),
                    this.loadEnrollments(),
                    this.loadEnrollmentStats(),
                    this.loadBadges(),
                    this.loadNotifications(),
                    this.loadPayments(),
                ]).then((results) => {
                    results.forEach((r, i) => {
                        if (r.status === "rejected") {
                            console.error(
                                "Background load error (index " + i + "):",
                                r.reason,
                            );
                        }
                    });
                });
            } finally {
                this.loading = false;
                console.log("loadData() completed - essential data loaded");
            }
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
                // Load paginated users for current page
                let variables = {
                    first: this.usersPerPage,
                    page: this.usersCurrentPage + 1,
                };

                const query = `
                    query GetUsers($first: Int!, $page: Int!) {
                        users(first: $first, page: $page) {
                            data {
                                id
                                name
                                email
                                username
                                avatar_url
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

                const result = await this.doGraphql(query, variables);

                if (result && result.data && result.data.users) {
                    this.users = result.data.users.data || [];
                    this.usersPaginatorInfo = result.data.users.paginatorInfo;

                    // Load all users once for search (when on first page or if allUsers is empty)
                    if (
                        (this.usersCurrentPage === 0 ||
                            this.allUsers.length === 0) &&
                        this.usersPaginatorInfo?.total > 0
                    ) {
                        await this.loadAllUsersForSearch();
                    }
                } else {
                    this.users = [];
                    this.usersPaginatorInfo = null;
                }
            } catch (error) {
                console.error("Error loading users:", error);
                showError("Gagal memuat data users: " + error.message);
            }
        },

        async loadAllUsersForSearch() {
            try {
                // Load ALL users for search functionality
                const query = `
                    query GetAllUsers($first: Int!) {
                        users(first: $first, page: 1) {
                            data {
                                id
                                name
                                email
                                username
                                avatar_url
                                is_active
                                created_at
                                roles {
                                    name
                                    display_name
                                }
                            }
                        }
                    }
                `;

                const variables = {
                    first: 1000, // Load max 1000 users for search
                };

                const result = await this.doGraphql(query, variables);
                if (result && result.data && result.data.users) {
                    this.allUsers = result.data.users.data || [];
                }
            } catch (error) {
                console.error("Error loading all users for search:", error);
            }
        },

        async loadCourses() {
            try {
                const params = new URLSearchParams();
                if (this.courseSearch)
                    params.append("search", this.courseSearch);
                params.append("page", this.coursesCurrentPage + 1);

                const response = await fetch(`/admin/api/courses?${params}`, {
                    headers: {
                        "X-CSRF-TOKEN": getCsrfToken(),
                        Accept: "application/json",
                    },
                });

                if (!response.ok) {
                    console.error(
                        "Failed to load courses:",
                        response.statusText,
                    );
                    return;
                }

                const data = await response.json();
                this.courses = data.data || [];
                this.coursesPaginatorInfo = {
                    currentPage: data.current_page,
                    lastPage: data.last_page,
                    total: data.total,
                };
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
                const params = new URLSearchParams({
                    per_page: this.enrollmentsPerPage,
                    page: this.enrollmentsCurrentPage + 1,
                    search: this.enrollmentSearch,
                    course_id: this.enrollmentFilters.course_id,
                    status: this.enrollmentFilters.status,
                    payment_status: this.enrollmentFilters.payment_status,
                });

                const response = await fetch(
                    `/admin/api/enrollments?${params}`,
                    {
                        headers: {
                            "X-CSRF-TOKEN": getCsrfToken(),
                            Accept: "application/json",
                        },
                    },
                );

                if (!response.ok) {
                    console.error(
                        "Failed to load enrollments:",
                        response.statusText,
                    );
                    return;
                }

                const data = await response.json();

                // Handle both wrapped (pagination with "data" field) and unwrapped (direct array) responses
                if (Array.isArray(data)) {
                    this.enrollments = data;
                    this.enrollmentsPaginatorInfo = {
                        data: data,
                        total: data.length,
                    };
                } else if (Array.isArray(data.data)) {
                    this.enrollments = data.data;
                    this.enrollmentsPaginatorInfo = data;
                } else {
                    this.enrollments = [];
                    this.enrollmentsPaginatorInfo = data || {
                        total: 0,
                        data: [],
                    };
                }
            } catch (error) {
                console.error("Error loading enrollments:", error);
            }
        },

        debouncedEnrollmentSearch() {
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => {
                this.enrollmentsCurrentPage = 0;
                this.loadEnrollments();
            }, this.debounceDelay);
        },

        // Payments
        paymentSearch: "",
        paymentFilters: { status: "", currency: "" },
        payments: [],
        paymentsCurrentPage: 0,
        paymentsPerPage: 10,
        paymentsPaginatorInfo: null,

        debouncedPaymentSearch() {
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => {
                this.paymentsCurrentPage = 0;
                this.loadPayments();
            }, this.debounceDelay);
        },

        async loadPayments() {
            try {
                const params = new URLSearchParams({
                    per_page: this.paymentsPerPage,
                    page: this.paymentsCurrentPage + 1,
                    search: this.paymentSearch,
                    status: this.paymentFilters.status,
                    currency: this.paymentFilters.currency,
                });

                const response = await fetch(`/admin/api/payments?${params}`, {
                    headers: {
                        "X-CSRF-TOKEN": getCsrfToken(),
                        Accept: "application/json",
                    },
                });

                if (!response.ok) {
                    const errorText = await response.text();
                    console.error("Failed to load payments:", response.status);
                    return;
                }

                const data = await response.json();
                this.payments = data.data || [];
                this.paymentsPaginatorInfo = {
                    current_page: data.current_page,
                    last_page: data.last_page,
                    total: data.total,
                    per_page: data.per_page,
                    from: data.from,
                    to: data.to,
                };
            } catch (error) {
                console.error("Error loading payments:", error);
            }
        },

        async viewPaymentDetails(paymentId) {
            try {
                const response = await fetch(
                    `/admin/api/payments/${paymentId}`,
                    {
                        headers: {
                            "X-CSRF-TOKEN": getCsrfToken(),
                            Accept: "application/json",
                        },
                    },
                );
                if (!response.ok) {
                    showError("Gagal memuat pembayaran");
                    return;
                }
                const data = await response.json();
                this.paymentDetails = data;
                showSuccess("Detail pembayaran dimuat");
            } catch (error) {
                console.error("Failed to load payment details:", error);
            }
        },

        async deletePayment(paymentId) {
            if (!confirm("Apakah Anda yakin ingin menghapus pembayaran ini?"))
                return;
            try {
                const response = await fetch(
                    `/admin/api/payments/${paymentId}`,
                    {
                        method: "DELETE",
                        headers: {
                            "X-CSRF-TOKEN": getCsrfToken(),
                            Accept: "application/json",
                        },
                    },
                );
                if (!response.ok) {
                    showError("Gagal menghapus pembayaran");
                    return;
                }
                showSuccess("Pembayaran berhasil dihapus");
                this.loadPayments();
            } catch (error) {
                console.error("Failed to delete payment:", error);
                showError("Gagal menghapus pembayaran");
            }
        },

        // End Payments

        async loadEnrollmentStats() {
            try {
                const response = await fetch(`/admin/api/enrollments/stats`, {
                    headers: {
                        "X-CSRF-TOKEN": getCsrfToken(),
                        Accept: "application/json",
                    },
                });

                if (response.ok) {
                    this.enrollmentStats = await response.json();
                }
            } catch (error) {
                console.error("Error loading enrollment stats:", error);
            }
        },

        openEditEnrollmentModal(enrollment) {
            this.enrollmentForm = {
                id: enrollment.id,
                student_name: enrollment.user?.name,
                course_title: enrollment.course?.title,
                status: enrollment.status,
                progress_percent: enrollment.progress_percent || 0,
                price_paid: enrollment.price_paid || 0,
                currency: enrollment.currency || "IDR",
                expires_at: enrollment.expires_at,
            };
            this.showEditEnrollmentModal = true;
        },

        viewEnrollmentDetails(enrollment) {
            this.enrollmentDetails = enrollment;
            this.showEnrollmentDetailsModal = true;
        },

        async updateEnrollment() {
            try {
                const response = await fetch(
                    `/admin/api/enrollments/${this.enrollmentForm.id}`,
                    {
                        method: "PUT",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": getCsrfToken(),
                        },
                        body: JSON.stringify({
                            status: this.enrollmentForm.status,
                            progress_percent:
                                this.enrollmentForm.progress_percent,
                            price_paid: this.enrollmentForm.price_paid,
                            currency: this.enrollmentForm.currency,
                            expires_at: this.enrollmentForm.expires_at,
                        }),
                    },
                );

                const data = await response.json();

                if (response.ok) {
                    showSuccess("Pendaftaran berhasil diperbarui");
                    this.showEditEnrollmentModal = false;
                    this.loadEnrollments();
                } else {
                    showError(data.message || "Gagal memperbarui pendaftaran");
                }
            } catch (error) {
                console.error("Error updating enrollment:", error);
                showError("Error updating enrollment: " + error.message);
            }
        },

        async deleteEnrollment(enrollmentId) {
            if (
                !confirm("Apakah Anda yakin ingin menghapus pendaftaran ini?")
            ) {
                return;
            }

            try {
                const response = await fetch(
                    `/admin/api/enrollments/${enrollmentId}`,
                    {
                        method: "DELETE",
                        headers: {
                            "X-CSRF-TOKEN": getCsrfToken(),
                        },
                    },
                );

                const data = await response.json();

                if (response.ok) {
                    showSuccess("Pendaftaran berhasil dihapus");
                    this.loadEnrollments();
                } else {
                    showError(data.message || "Gagal menghapus pendaftaran");
                }
            } catch (error) {
                console.error("Error deleting enrollment:", error);
                showError("Gagal menghapus pendaftaran: " + error.message);
            }
        },

        async loadBadges() {
            try {
                const response = await fetch("/admin/api/badges", {
                    headers: {
                        "X-CSRF-TOKEN": getCsrfToken(),
                        Accept: "application/json",
                    },
                });

                if (!response.ok) {
                    console.error(
                        "Failed to load badges:",
                        response.statusText,
                    );
                    return;
                }

                const data = await response.json();
                this.badges = data.data || [];
            } catch (error) {
                console.error("Error loading badges:", error);
            }
        },

        openEditBadgeModal(badge) {
            this.badgeForm = {
                id: badge.id,
                name: badge.name,
                code: badge.code,
                description: badge.description || "",
            };
            this.showCreateBadgeModal = true;
        },

        async saveBadge() {
            try {
                const url = this.badgeForm.id
                    ? `/admin/api/badges/${this.badgeForm.id}`
                    : "/admin/api/badges";

                const method = this.badgeForm.id ? "PUT" : "POST";

                const response = await fetch(url, {
                    method: method,
                    headers: {
                        "X-CSRF-TOKEN": getCsrfToken(),
                        "Content-Type": "application/json",
                        Accept: "application/json",
                    },
                    body: JSON.stringify({
                        name: this.badgeForm.name,
                        code: this.badgeForm.code,
                        description: this.badgeForm.description,
                    }),
                });

                if (response.ok) {
                    this.showCreateBadgeModal = false;
                    this.badgeForm = {
                        id: null,
                        name: "",
                        code: "",
                        description: "",
                    };
                    await this.loadBadges();
                    this.showSuccess(
                        this.badgeForm.id
                            ? "Lencana diperbarui"
                            : "Lencana dibuat",
                    );
                } else {
                    const data = await response.json();
                    this.showError(data.message || "Gagal menyimpan lencana");
                }
            } catch (error) {
                console.error("Error saving badge:", error);
                this.showError("Gagal menyimpan lencana");
            }
        },

        async deleteBadge(id) {
            if (!confirm("Apakah Anda yakin ingin menghapus lencana ini?"))
                return;

            try {
                const response = await fetch(`/admin/api/badges/${id}`, {
                    method: "DELETE",
                    headers: {
                        "X-CSRF-TOKEN": getCsrfToken(),
                        Accept: "application/json",
                    },
                });

                if (response.ok) {
                    await this.loadBadges();
                    this.showSuccess("Lencana dihapus");
                } else {
                    this.showError("Gagal menghapus lencana");
                }
            } catch (error) {
                console.error("Error deleting badge:", error);
                this.showError("Gagal menghapus lencana");
            }
        },

        async loadRoles() {
            try {
                const query = `
                    {
                        roles(first: 10, page: 1) {
                            data {
                                id
                                name
                                display_name
                                description
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
                if (result && result.data && result.data.roles) {
                    this.roles = result.data.roles.data || result.data.roles;
                }
            } catch (error) {
                console.error("Error loading roles:", error);
            }
        },

        async loadNotifications(page = 1) {
            try {
                console.log("loadNotifications() called");
                const params = new URLSearchParams({
                    per_page: this.notificationsPerPage,
                    page: page,
                    search: this.notificationSearch,
                    is_read: this.notificationFilters.is_read,
                    type: this.notificationFilters.type,
                });

                console.log(
                    "Fetching notifications with params:",
                    Object.fromEntries(params),
                );
                const response = await fetch(
                    `/admin/api/notifications?${params}`,
                    {
                        headers: {
                            "X-CSRF-TOKEN": getCsrfToken(),
                            Accept: "application/json",
                        },
                    },
                );

                if (!response.ok) {
                    console.error(
                        "Failed to load notifications:",
                        response.statusText,
                    );
                    return;
                }

                const data = await response.json();
                console.log(
                    "Notifications API response:",
                    data.data?.length,
                    "items",
                );
                // Parse payload JSON strings into objects
                const notifications = (data.data || []).map((notification) => ({
                    ...notification,
                    payload:
                        typeof notification.payload === "string"
                            ? JSON.parse(notification.payload)
                            : notification.payload,
                }));
                this.notifications = notifications;
                this.notificationPagination = data;
                console.log(
                    "Notifications loaded and parsed:",
                    this.notifications.length,
                    "items",
                );
                this.notificationsPaginatorInfo = data;
            } catch (error) {
                console.error("Error loading notifications:", error);
            }
        },

        async sendNotification() {
            try {
                console.log("sendNotification() called");
                const response = await fetch("/admin/api/notifications", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": getCsrfToken(),
                        "Content-Type": "application/json",
                        Accept: "application/json",
                    },
                    body: JSON.stringify({
                        type: this.notificationForm.type,
                        user_id: this.notificationForm.user_id || null,
                        message: this.notificationForm.message,
                    }),
                });

                console.log(
                    "sendNotification() response status:",
                    response.status,
                );

                if (response.ok) {
                    console.log(
                        "sendNotification() success, closing modal and reloading",
                    );
                    this.showCreateNotificationModal = false;
                    this.notificationForm = {
                        type: "",
                        user_id: "",
                        message: "",
                    };
                    // Reset pagination and filters to show new notification
                    this.notificationsCurrentPage = 0;
                    this.notificationSearch = "";
                    this.notificationFilters = { is_read: "", type: "" };

                    console.log("About to call loadNotifications()");
                    await this.loadNotifications();
                    console.log("loadNotifications() completed");
                    this.showSuccess("Notifikasi berhasil dikirim");
                } else {
                    const data = await response.json();
                    console.error("sendNotification() failed:", data);
                    this.showError(data.message || "Gagal mengirim notifikasi");
                }
            } catch (error) {
                console.error("Error sending notification:", error);
                this.showError("Gagal mengirim notifikasi");
            }
        },

        async deleteNotification(id) {
            if (!confirm("Hapus notifikasi ini?")) return;

            try {
                const response = await fetch(`/admin/api/notifications/${id}`, {
                    method: "DELETE",
                    headers: {
                        "X-CSRF-TOKEN": getCsrfToken(),
                        Accept: "application/json",
                    },
                });

                if (response.ok) {
                    await this.loadNotifications();
                    this.showSuccess("Notifikasi dihapus");
                } else {
                    this.showError("Gagal menghapus notifikasi");
                }
            } catch (error) {
                console.error("Error deleting notification:", error);
                this.showError("Gagal menghapus notifikasi");
            }
        },

        get filteredUsers() {
            // Client-side search filtering - search across ALL users (allUsers), not just current page
            if (!this.userSearch || this.userSearch.trim() === "") {
                return this.users.filter((user) => user && user.id);
            }

            const search = this.userSearch.toLowerCase();
            // Use allUsers for search (across all pages), fallback to current page users
            const sourceUsers =
                this.allUsers.length > 0 ? this.allUsers : this.users;

            return sourceUsers.filter((user) => {
                if (!user || !user.id) return false;
                return (
                    user.name.toLowerCase().includes(search) ||
                    user.email.toLowerCase().includes(search) ||
                    (user.username &&
                        user.username.toLowerCase().includes(search))
                );
            });
        },

        get totalFilteredUsers() {
            // Return count of filtered results (across all pages when searching)
            return this.filteredUsers.filter((user) => user && user.id).length;
        },

        get totalUserPages() {
            return Math.ceil(this.totalFilteredUsers / this.usersPerPage);
        },

        get paginatedFilteredUsers() {
            // Show paginated results of filtered users
            const start = this.usersCurrentPage * this.usersPerPage;
            const end = start + this.usersPerPage;
            return this.filteredUsers.slice(start, end);
        },

        get filteredCourses() {
            if (!this.courseSearch) return this.courses;
            const search = this.courseSearch.toLowerCase();
            return this.courses.filter(
                (course) =>
                    course.title.toLowerCase().includes(search) ||
                    course.slug.toLowerCase().includes(search),
            );
        },

        get filteredCategories() {
            if (!this.categorySearch) return this.categories;
            const search = this.categorySearch.toLowerCase();
            return this.categories.filter(
                (category) =>
                    category.name.toLowerCase().includes(search) ||
                    (category.description &&
                        category.description.toLowerCase().includes(search)),
            );
        },

        get filteredEnrollments() {
            if (!this.enrollmentSearch) return this.enrollments;
            const search = this.enrollmentSearch.toLowerCase();
            return this.enrollments.filter(
                (enrollment) =>
                    (enrollment.user?.name &&
                        enrollment.user.name.toLowerCase().includes(search)) ||
                    (enrollment.user?.email &&
                        enrollment.user.email.toLowerCase().includes(search)) ||
                    (enrollment.course?.title &&
                        enrollment.course.title.toLowerCase().includes(search)),
            );
        },

        get filteredBadges() {
            if (!this.badgeSearch) return this.badges;
            const search = this.badgeSearch.toLowerCase();
            return this.badges.filter(
                (badge) =>
                    badge.name.toLowerCase().includes(search) ||
                    badge.code.toLowerCase().includes(search),
            );
        },

        get filteredNotifications() {
            if (!this.notificationSearch) return this.notifications;
            const search = this.notificationSearch.toLowerCase();
            return this.notifications.filter(
                (notification) =>
                    notification.title.toLowerCase().includes(search) ||
                    (notification.message &&
                        notification.message.toLowerCase().includes(search)),
            );
        },

        // Progress Tracking Functions
        expandedCourse: null,
        selectedEnrollment: null,
        showProgressDetailsModal: false,

        getEnrollmentsByCourse(courseId) {
            return this.enrollments.filter((e) => e.course_id === courseId);
        },

        getEnrollmentLessons(courseId) {
            return this.lessons.filter((l) => l.course_id === courseId);
        },

        showProgressModal(enrollment) {
            this.selectedEnrollment = enrollment;
            this.showProgressDetailsModal = true;
        },

        async markLessonComplete(enrollmentId, lessonId) {
            try {
                const response = await fetch(
                    `/admin/api/progress/lesson/complete`,
                    {
                        method: "POST",
                        headers: {
                            "X-CSRF-TOKEN": getCsrfToken(),
                            "Content-Type": "application/json",
                            Accept: "application/json",
                        },
                        body: JSON.stringify({
                            enrollment_id: enrollmentId,
                            lesson_id: lessonId,
                        }),
                    },
                );

                if (response.ok) {
                    await this.loadEnrollments();
                    this.showSuccess("Pelajaran ditandai selesai");
                } else {
                    this.showError("Gagal menandai pelajaran selesai");
                }
            } catch (error) {
                console.error("Error marking lesson complete:", error);
                this.showError("Gagal menandai pelajaran selesai");
            }
        },

        get usersTableContent() {
            const users = this.paginatedFilteredUsers;
            if (users.length === 0) {
                return '<tr><td colspan="6" class="text-center py-4">No users found</td></tr>';
            }

            return users
                .map(
                    (user) => `
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            ${
                                user.avatar_url
                                    ? `<img class="w-8 h-8 rounded-full object-cover" src="/storage/${user.avatar_url}" alt="${user.name}">`
                                    : `<div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center"><span class="text-white text-sm font-bold">${user.name
                                          .charAt(0)
                                          .toUpperCase()}</span></div>`
                            }
                            <div class="ml-3">
                                <div class="text-sm font-medium text-gray-900">${
                                    user.name
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
                                        `<span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">${role.display_name}</span>`,
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
            `,
                )
                .join("");
        },

        get coursesTableContent() {
            const courses = this.filteredCourses;
            if (courses.length === 0) {
                return '<tr><td colspan="8" class="text-center py-4">No courses found</td></tr>';
            }

            return courses
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
            `,
                )
                .join("");
        },

        get categoriesTableContent() {
            const categories = this.filteredCategories;
            if (categories.length === 0) {
                return '<tr><td colspan="5" class="text-center py-4">No categories found</td></tr>';
            }

            return categories
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
            `,
                )
                .join("");
        },

        get enrollmentsTableContent() {
            const enrollments = this.filteredEnrollments;
            if (enrollments.length === 0) {
                return '<tr><td colspan="6" class="text-center py-4">No enrollments found</td></tr>';
            }

            return enrollments
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
            `,
                )
                .join("");
        },

        get badgesTableContent() {
            const badges = this.filteredBadges;
            if (badges.length === 0) {
                return '<tr><td colspan="5" class="text-center py-4">No badges found</td></tr>';
            }

            return badges
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
            `,
                )
                .join("");
        },

        get notificationsTableContent() {
            const notifications = this.filteredNotifications;
            if (notifications.length === 0) {
                return '<tr><td colspan="7" class="text-center py-4">No notifications found</td></tr>';
            }

            return notifications
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
            `,
                )
                .join("");
        },

        // User CRUD operations
        async createUser(userData) {
            try {
                const mutation = `
                    mutation CreateUser($input: CreateUserInput!) {
                        createUser(input: $input) {
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
                    }
                `;

                const variables = {
                    input: {
                        email: userData.email,
                        password: userData.password,
                        name: userData.name,
                        username: userData.username,
                        role_ids: userData.role_ids,
                    },
                };

                const response = await fetch(getGraphqlEndpoint(), {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": getCsrfToken(),
                    },
                    body: JSON.stringify({ query: mutation, variables }),
                });

                const result = await response.json();
                if (result.errors) {
                    throw new Error(
                        result.errors.map((e) => e.message).join(", "),
                    );
                }

                if (result.data) {
                    this.users.push(result.data.createUser);
                    this.showCreateUserModal = false;
                    showSuccess("Pengguna berhasil dibuat");
                    this.loadUsers();
                }
            } catch (error) {
                console.error("Error creating user:", error);
                showError("Gagal membuat pengguna: " + error.message);
            }
        },

        async updateUser(userId, userData) {
            try {
                const mutation = `
                    mutation UpdateUser($id: ID!, $input: UpdateUserInput!) {
                        updateUser(id: $id, input: $input) {
                            id
                            name
                            email
                            username
                            avatar_url
                            is_active
                            created_at
                            roles {
                                name
                                display_name
                            }
                        }
                    }
                `;

                const variables = {
                    id: userId,
                    input: {
                        name: userData.name,
                        email: userData.email,
                        username: userData.username,
                        avatar_url: userData.avatar_url,
                        is_active: userData.is_active,
                    },
                };

                const response = await fetch(getGraphqlEndpoint(), {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": getCsrfToken(),
                    },
                    body: JSON.stringify({ query: mutation, variables }),
                });

                const result = await response.json();
                if (result.errors) {
                    throw new Error(
                        result.errors.map((e) => e.message).join(", "),
                    );
                }

                if (result.data) {
                    const index = this.users.findIndex((u) => u.id == userId);
                    if (index !== -1) {
                        this.users[index] = result.data.updateUser;
                    }
                    this.showEditUserModal = false;
                    showSuccess("Pengguna berhasil diperbarui");
                    this.loadUsers();
                }
            } catch (error) {
                console.error("Error updating user:", error);
                showError("Gagal memperbarui pengguna: " + error.message);
            }
        },

        async deleteUser(userId) {
            if (!confirm("Apakah Anda yakin ingin menghapus pengguna ini?"))
                return;

            try {
                const mutation = `
                    mutation DeleteUser($id: ID!) {
                        deleteUser(id: $id)
                    }
                `;

                const variables = { id: userId };

                const response = await fetch(getGraphqlEndpoint(), {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": getCsrfToken(),
                    },
                    body: JSON.stringify({ query: mutation, variables }),
                });

                const result = await response.json();
                if (result.errors) {
                    throw new Error(
                        result.errors.map((e) => e.message).join(", "),
                    );
                }

                if (result.data) {
                    this.users = this.users.filter(
                        (u) => u.id.toString() !== userId.toString(),
                    );
                    showSuccess("Pengguna berhasil dihapus");
                }
            } catch (error) {
                console.error("Error deleting user:", error);
                showError("Gagal menghapus pengguna: " + error.message);
            }
        },

        // Course CRUD operations
        async createCourse(courseData) {
            try {
                const mutation = `
                    mutation CreateCourse($input: CreateCourseInput!) {
                        createCourse(input: $input) {
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
                            instructor {
                                id
                                name
                            }
                            category {
                                id
                                name
                            }
                        }
                    }
                `;

                const variables = {
                    input: {
                        instructor_id: courseData.instructor_id,
                        category_id: courseData.category_id,
                        title: courseData.title,
                        slug: courseData.slug,
                        short_description: courseData.short_description,
                        full_description: courseData.full_description,
                        price: courseData.price,
                        currency: courseData.currency || "IDR",
                        level: courseData.level,
                        duration_minutes: courseData.duration_minutes,
                        thumbnail_url: courseData.thumbnail_url,
                    },
                };

                const response = await fetch(getGraphqlEndpoint(), {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": getCsrfToken(),
                    },
                    body: JSON.stringify({ query: mutation, variables }),
                });

                const result = await response.json();
                if (result.errors) {
                    throw new Error(
                        result.errors.map((e) => e.message).join(", "),
                    );
                }

                if (result.data) {
                    this.courses.push(result.data.createCourse);
                    this.showCreateCourseModal = false;
                    showSuccess("Kursus berhasil dibuat");
                    this.loadCourses();
                }
            } catch (error) {
                console.error("Error creating course:", error);
                showError("Gagal membuat kursus: " + error.message);
            }
        },

        async updateCourse(courseId, courseData) {
            try {
                const mutation = `
                    mutation UpdateCourse($id: ID!, $input: UpdateCourseInput!) {
                        updateCourse(id: $id, input: $input) {
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
                            instructor {
                                id
                                name
                            }
                            category {
                                id
                                name
                            }
                        }
                    }
                `;

                const variables = {
                    id: courseId,
                    input: {
                        category_id: courseData.category_id,
                        title: courseData.title,
                        slug: courseData.slug,
                        short_description: courseData.short_description,
                        full_description: courseData.full_description,
                        price: courseData.price,
                        currency: courseData.currency,
                        is_published: courseData.is_published,
                        status: courseData.status,
                        level: courseData.level,
                        duration_minutes: courseData.duration_minutes,
                        thumbnail_url: courseData.thumbnail_url,
                    },
                };

                const response = await fetch(getGraphqlEndpoint(), {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": getCsrfToken(),
                    },
                    body: JSON.stringify({ query: mutation, variables }),
                });

                const result = await response.json();
                if (result.errors) {
                    throw new Error(
                        result.errors.map((e) => e.message).join(", "),
                    );
                }

                if (result.data) {
                    const index = this.courses.findIndex(
                        (c) => c.id == courseId,
                    );
                    if (index !== -1) {
                        this.courses[index] = result.data.updateCourse;
                    }
                    showSuccess("Kursus berhasil diperbarui");
                    this.loadCourses();
                }
            } catch (error) {
                console.error("Error updating course:", error);
                showError("Gagal memperbarui kursus: " + error.message);
            }
        },

        async deleteCourse(courseId) {
            if (!confirm("Are you sure you want to delete this course?"))
                return;

            try {
                const mutation = `
                    mutation DeleteCourse($id: ID!) {
                        deleteCourse(id: $id)
                    }
                `;

                const variables = { id: courseId };

                const response = await fetch(getGraphqlEndpoint(), {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": getCsrfToken(),
                    },
                    body: JSON.stringify({ query: mutation, variables }),
                });

                const result = await response.json();
                if (result.errors) {
                    throw new Error(
                        result.errors.map((e) => e.message).join(", "),
                    );
                }

                if (result.data) {
                    this.courses = this.courses.filter((c) => c.id != courseId);
                    showSuccess("Course deleted successfully");
                }
            } catch (error) {
                console.error("Error deleting course:", error);
                showError("Failed to delete course: " + error.message);
            }
        },

        // Edit methods
        async editUser(userId) {
            console.log("editUser called with userId:", userId);
            console.log("Current users:", this.users);
            console.log("Current Alpine instance:", this);

            let user = this.users.find((u) => u.id == userId);
            console.log("Found user:", user);

            if (!user) {
                console.log(
                    "User not in current page, fetching individually...",
                );
                // User not in current page, fetch individually
                try {
                    const query = `
                        query GetUser($id: ID!) {
                            user(id: $id) {
                                id
                                name
                                email
                                username
                                avatar_url
                                is_active
                                created_at
                                roles {
                                    name
                                    display_name
                                }
                            }
                        }
                    `;

                    const result = await this.doGraphql(query, { id: userId });
                    console.log("GraphQL result:", result);
                    if (result.data && result.data.user) {
                        user = result.data.user;
                        console.log("Fetched user:", user);
                    } else {
                        console.error("User not found");
                        return;
                    }
                } catch (error) {
                    console.error("Error loading user:", error);
                    return;
                }
            }

            // Populate edit modal with user data
            this.editingUser = { ...user }; // Clone to avoid modifying the original
            console.log("Setting editingUser:", this.editingUser);
            this.showEditUserModal = true;
            console.log("showEditUserModal set to:", this.showEditUserModal);
        },

        editCourse(courseId) {
            const course = this.courses.find((c) => c.id == courseId);
            if (!course) return;

            // Populate edit modal with course data
            this.editingCourse = { ...course };
            this.courseForm = {
                id: course.id,
                title: course.title,
                short_description: course.short_description,
                full_description: course.full_description,
                category_id: course.category_id,
                price: course.price,
                level: course.level,
                duration_minutes: course.duration_minutes,
                is_published: course.is_published,
            };
            this.showEditCourseModal = true;
        },

        async saveCourse() {
            try {
                const url = this.courseForm.id
                    ? `/admin/api/courses/${this.courseForm.id}`
                    : "/admin/api/courses";

                const method = this.courseForm.id ? "PUT" : "POST";

                const response = await fetch(url, {
                    method,
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": getCsrfToken(),
                    },
                    body: JSON.stringify(this.courseForm),
                });

                const data = await response.json();

                if (response.ok) {
                    showSuccess(
                        this.courseForm.id
                            ? "Course updated successfully"
                            : "Course created successfully",
                    );

                    // Handle thumbnail upload if file selected
                    const refName = this.courseForm.id
                        ? "editThumbnailUpload"
                        : "createThumbnailUpload";

                    // Use Alpine $refs if available, fallback to DOM query
                    const fileInput =
                        this.$refs[refName] ||
                        document.querySelector(`[x-ref="${refName}"]`);

                    if (fileInput && fileInput.files && fileInput.files[0]) {
                        await this.uploadCourseThumbnail(
                            data.course.id,
                            fileInput.files[0],
                        );
                    }

                    this.showCreateCourseModal = false;
                    this.showEditCourseModal = false;
                    this.courseForm = {
                        id: null,
                        title: "",
                        short_description: "",
                        full_description: "",
                        full_description: "",
                        category_id: "",
                        price: 0,
                        level: "beginner",
                        duration_minutes: 0,
                        is_published: false,
                        thumbnail_url: null,
                    };
                    this.loadCourses();
                } else {
                    showError(data.message || "Failed to save course");
                }
            } catch (error) {
                console.error("Error saving course:", error);
                showError("Error saving course: " + error.message);
            }
        },

        async deleteCourse(courseId) {
            if (
                !confirm(
                    "Are you sure you want to delete this course? This action cannot be undone.",
                )
            ) {
                return;
            }

            try {
                const response = await fetch(`/admin/api/courses/${courseId}`, {
                    method: "DELETE",
                    headers: {
                        "X-CSRF-TOKEN": getCsrfToken(),
                    },
                });

                const data = await response.json();

                if (response.ok) {
                    showSuccess("Course deleted successfully");
                    this.loadCourses();
                } else {
                    showError(data.message || "Failed to delete course");
                }
            } catch (error) {
                console.error("Error deleting course:", error);
                showError("Failed to delete course: " + error.message);
            }
        },

        async uploadCourseThumbnail(courseId, file) {
            try {
                const formData = new FormData();
                formData.append("thumbnail", file);

                const response = await fetch(
                    `/admin/api/courses/${courseId}/thumbnail`,
                    {
                        method: "POST",
                        headers: {
                            "X-CSRF-TOKEN": getCsrfToken(),
                            Accept: "application/json",
                        },
                        body: formData,
                    },
                );

                const data = await response.json();

                if (response.ok) {
                    showSuccess("Thumbnail uploaded successfully");
                    return data.thumbnail_url;
                } else {
                    showError(data.message || "Failed to upload thumbnail");
                }
            } catch (error) {
                console.error("Error uploading thumbnail:", error);
                showError("Error uploading thumbnail: " + error.message);
            }
        },

        manageCourseModules(courseId) {
            const course = this.courses.find((c) => c.id == courseId);
            if (!course) return;

            this.selectedCourse = course;
            this.moduleForm.course_id = courseId;
            this.loadModules(courseId);
            this.showModuleManager = true;
        },

        async loadModules(courseId) {
            try {
                const response = await fetch(
                    `/admin/api/courses/${courseId}/modules`,
                    {
                        headers: {
                            "X-CSRF-TOKEN": getCsrfToken(),
                            Accept: "application/json",
                        },
                    },
                );

                if (!response.ok) {
                    console.error(
                        "Failed to load modules:",
                        response.statusText,
                    );
                    return;
                }

                const data = await response.json();
                this.modules = data.data || [];
            } catch (error) {
                console.error("Error loading modules:", error);
            }
        },

        openCreateModuleModal() {
            this.moduleForm = {
                id: null,
                course_id: this.selectedCourse.id,
                title: "",
                description: "",
                position: this.modules.length + 1,
            };
            this.showCreateModuleModal = true;
        },

        openEditModuleModal(module) {
            this.moduleForm = {
                id: module.id,
                course_id: module.course_id,
                title: module.title,
                description: module.description,
                position: module.position,
            };
            this.showEditModuleModal = true;
        },

        async saveModule() {
            try {
                const url = this.moduleForm.id
                    ? `/admin/api/modules/${this.moduleForm.id}`
                    : "/admin/api/modules";

                const method = this.moduleForm.id ? "PUT" : "POST";

                const response = await fetch(url, {
                    method,
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": getCsrfToken(),
                    },
                    body: JSON.stringify(this.moduleForm),
                });

                const data = await response.json();

                if (response.ok) {
                    showSuccess(
                        this.moduleForm.id
                            ? "Modul berhasil diperbarui"
                            : "Modul berhasil dibuat",
                    );
                    this.showCreateModuleModal = false;
                    this.showEditModuleModal = false;
                    this.loadModules(this.selectedCourse.id);
                } else {
                    showError(data.message || "Gagal menyimpan modul");
                }
            } catch (error) {
                console.error("Error saving module:", error);
                showError("Gagal menyimpan modul: " + error.message);
            }
        },

        async deleteModule(moduleId) {
            if (
                !confirm(
                    "Apakah Anda yakin ingin menghapus modul ini beserta semua pelajarannya?",
                )
            ) {
                return;
            }

            try {
                const response = await fetch(`/admin/api/modules/${moduleId}`, {
                    method: "DELETE",
                    headers: {
                        "X-CSRF-TOKEN": getCsrfToken(),
                    },
                });

                const data = await response.json();

                if (response.ok) {
                    showSuccess("Modul berhasil dihapus");
                    this.loadModules(this.selectedCourse.id);
                } else {
                    showError(data.message || "Gagal menghapus modul");
                }
            } catch (error) {
                console.error("Error deleting module:", error);
                showError("Gagal menghapus modul: " + error.message);
            }
        },

        // === LESSON MANAGEMENT ===
        openLessonManager(module) {
            this.selectedModule = module;
            this.lessonForm.module_id = module.id;
            this.lessonForm.course_id = this.selectedCourse.id;
            this.loadLessons(module.id);
            this.showLessonManager = true;
        },

        async loadLessons(moduleId) {
            try {
                const response = await fetch(
                    `/admin/api/modules/${moduleId}/lessons`,
                    {
                        headers: {
                            "X-CSRF-TOKEN": getCsrfToken(),
                            Accept: "application/json",
                        },
                    },
                );

                if (!response.ok) {
                    console.error(
                        "Failed to load lessons:",
                        response.statusText,
                    );
                    return;
                }

                let data;
                const contentType = response.headers.get("content-type");

                if (contentType && contentType.includes("application/json")) {
                    data = await response.json();
                } else {
                    const text = await response.text();
                    console.error("Non-JSON response:", text.substring(0, 500));
                    console.error("Response status:", response.status);
                    return;
                }

                this.lessons = data.data || [];
            } catch (error) {
                console.error("Error loading lessons:", error);
            }
        },

        openCreateLessonModal() {
            this.lessonForm = {
                id: null,
                module_id: this.selectedModule.id,
                course_id: this.selectedCourse.id,
                title: "",
                content_type: "video",
                content: "",
                media_url: "",
                duration_seconds: 0,
                is_downloadable: false,
                position: this.lessons.length + 1,
                sourceType: "upload",
                // Quiz Fields
                quiz_title: "",
                quiz_description: "",
                passing_score: 70,
                time_limit_minutes: 0,
                attempts_allowed: 0,
                questions: [],
            };

            // Clear file inputs
            if (this.$refs.lessonFileUpload)
                this.$refs.lessonFileUpload.value = "";

            this.showCreateLessonModal = true;
        },

        async loadQuizData(lessonId) {
            try {
                const response = await fetch(`/admin/api/quiz/${lessonId}`);
                if (response.ok) {
                    const data = await response.json();
                    if (data.quiz) {
                        this.lessonForm.quiz_title = data.quiz.title;
                        this.lessonForm.quiz_description =
                            data.quiz.description;
                        this.lessonForm.passing_score = data.quiz.passing_score;
                        this.lessonForm.time_limit_minutes = data.quiz
                            .time_limit_seconds
                            ? Math.floor(data.quiz.time_limit_seconds / 60)
                            : 0;
                        this.lessonForm.attempts_allowed =
                            data.quiz.attempts_allowed;
                    }
                    if (data.questions) {
                        this.lessonForm.questions = data.questions.map((q) => ({
                            id: q.id,
                            type: q.type,
                            question: q.question,
                            points: q.points,
                            choices: q.choices
                                ? q.choices.map((c) => ({
                                      id: c.id,
                                      text: c.text,
                                      is_correct: Boolean(c.is_correct),
                                  }))
                                : [],
                        }));
                    }
                }
            } catch (e) {
                console.error("Error loading quiz data", e);
            }
        },

        async openEditLessonModal(lesson) {
            this.lessonForm = {
                id: lesson.id,
                module_id: lesson.module_id,
                course_id: lesson.course_id,
                title: lesson.title,
                content_type: lesson.content_type,
                content: lesson.content,
                media_url: lesson.media_url,
                duration_seconds: lesson.duration_seconds,
                is_downloadable: Boolean(lesson.is_downloadable),
                position: lesson.position,
                sourceType:
                    lesson.media_url && lesson.media_url.includes("/storage/")
                        ? "upload"
                        : "url",
                // Quiz defaults
                quiz_title: lesson.title,
                quiz_description: "",
                passing_score: 70,
                time_limit_minutes: 0,
                attempts_allowed: 0,
                questions: [],
            };

            // Clear file inputs
            if (this.$refs.lessonFileUpload)
                this.$refs.lessonFileUpload.value = "";

            if (lesson.content_type === "quiz") {
                await this.loadQuizData(lesson.id);
            }

            this.showEditLessonModal = true;
        },

        addQuestion() {
            this.lessonForm.questions.push({
                id: null,
                type: "mcq",
                question: "",
                points: 10,
                choices: [
                    { id: null, text: "", is_correct: false },
                    { id: null, text: "", is_correct: false },
                ],
            });
        },

        removeQuestion(index) {
            this.lessonForm.questions.splice(index, 1);
        },

        addChoice(questionIndex) {
            this.lessonForm.questions[questionIndex].choices.push({
                id: null,
                text: "",
                is_correct: false,
            });
        },

        removeChoice(questionIndex, choiceIndex) {
            this.lessonForm.questions[questionIndex].choices.splice(
                choiceIndex,
                1,
            );
        },

        async saveQuizData(lessonId) {
            const payload = {
                title: this.lessonForm.quiz_title,
                description: this.lessonForm.quiz_description,
                passing_score: this.lessonForm.passing_score,
                time_limit_seconds: this.lessonForm.time_limit_minutes * 60,
                attempts_allowed: this.lessonForm.attempts_allowed,
                questions: this.lessonForm.questions,
            };

            const response = await fetch(`/admin/api/quiz/${lessonId}`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": getCsrfToken(),
                },
                body: JSON.stringify(payload),
            });

            if (!response.ok) {
                const errorData = await response
                    .json()
                    .catch((e) => ({ message: "Invalid JSON response" }));
                console.error("Quiz Save Error Details:", errorData);
                throw new Error(
                    errorData.error ||
                        errorData.message ||
                        "Gagal menyimpan data quiz",
                );
            }
        },

        async saveLesson() {
            try {
                const url = this.lessonForm.id
                    ? `/admin/api/lessons/${this.lessonForm.id}`
                    : "/admin/api/lessons";

                const formData = new FormData();
                formData.append("module_id", this.lessonForm.module_id);
                formData.append("course_id", this.lessonForm.course_id);
                formData.append("title", this.lessonForm.title);
                formData.append("content_type", this.lessonForm.content_type);
                formData.append("content", this.lessonForm.content || "");
                formData.append(
                    "duration_seconds",
                    this.lessonForm.duration_seconds || 0,
                );
                formData.append(
                    "is_downloadable",
                    this.lessonForm.is_downloadable ? 1 : 0,
                );
                formData.append("position", this.lessonForm.position);

                // Handle Media (URL vs File)
                if (this.lessonForm.sourceType === "url") {
                    formData.append(
                        "media_url",
                        this.lessonForm.media_url || "",
                    );
                }

                // Handle File Upload
                let fileInput = null;
                if (this.lessonForm.sourceType === "upload") {
                    // Start of selection logic for file input
                    fileInput =
                        this.$refs.lessonFileUpload ||
                        document.querySelector('[x-ref="lessonFileUpload"]');

                    if (fileInput && fileInput.files && fileInput.files[0]) {
                        formData.append("media_file", fileInput.files[0]);
                    }
                }

                // If updating, spoof PUT method
                if (this.lessonForm.id) {
                    formData.append("_method", "PUT");
                }

                const response = await fetch(url, {
                    method: "POST", // Always POST for file uploads with spoofing
                    headers: {
                        "X-CSRF-TOKEN": getCsrfToken(),
                        // Content-Type header excluded to let browser set boundary
                    },
                    body: formData,
                });

                let data;
                const contentType = response.headers.get("content-type");

                if (contentType && contentType.includes("application/json")) {
                    data = await response.json();
                } else {
                    const text = await response.text();
                    console.error("Non-JSON response:", text.substring(0, 500));
                    showError(
                        `Server error (${response.status}): Please check browser console for details`,
                    );
                    return;
                }

                if (response.ok) {
                    const savedLesson =
                        data.lesson || data.data || (data.id ? data : null);

                    if (
                        this.lessonForm.content_type === "quiz" &&
                        savedLesson &&
                        savedLesson.id
                    ) {
                        await this.saveQuizData(savedLesson.id);
                    }

                    showSuccess(
                        this.lessonForm.id
                            ? "Pelajaran berhasil diperbarui"
                            : "Pelajaran berhasil dibuat",
                    );
                    this.showCreateLessonModal = false;
                    this.showEditLessonModal = false;
                    this.loadLessons(this.selectedModule.id);
                } else {
                    showError(data.message || "Gagal menyimpan pelajaran");
                }
            } catch (error) {
                console.error("Error saving lesson:", error);
                showError("Gagal menyimpan pelajaran: " + error.message);
            }
        },

        async deleteLesson(lessonId) {
            if (!confirm("Apakah Anda yakin ingin menghapus pelajaran ini?")) {
                return;
            }

            try {
                const response = await fetch(`/admin/api/lessons/${lessonId}`, {
                    method: "DELETE",
                    headers: {
                        "X-CSRF-TOKEN": getCsrfToken(),
                    },
                });

                let data;
                const contentType = response.headers.get("content-type");

                if (contentType && contentType.includes("application/json")) {
                    data = await response.json();
                } else {
                    const text = await response.text();
                    console.error("Non-JSON response:", text.substring(0, 500));
                    console.error("Response status:", response.status);
                    showError(
                        `Kesalahan server (${response.status}): Silakan periksa konsol browser untuk detailnya`,
                    );
                    return;
                }

                if (response.ok) {
                    showSuccess("Pelajaran berhasil dihapus");
                    this.loadLessons(this.selectedModule.id);
                } else {
                    showError(data.message || "Gagal menghapus pelajaran");
                }
            } catch (error) {
                console.error("Error deleting lesson:", error);
                showError("Gagal menghapus pelajaran: " + error.message);
            }
        },

        // Category CRUD operations
        async loadCategories() {
            try {
                const response = await fetch(
                    `/admin/api/categories?search=${encodeURIComponent(
                        this.categorySearch,
                    )}`,
                    {
                        headers: {
                            "X-CSRF-TOKEN": getCsrfToken(),
                            Accept: "application/json",
                        },
                    },
                );

                if (!response.ok) {
                    console.error(
                        "Failed to load categories:",
                        response.statusText,
                    );
                    return;
                }

                const data = await response.json();
                this.categories = data.data || [];
            } catch (error) {
                console.error("Error loading categories:", error);
            }
        },

        async saveCategory() {
            try {
                const url = this.categoryForm.id
                    ? `/admin/api/categories/${this.categoryForm.id}`
                    : "/admin/api/categories";

                const method = this.categoryForm.id ? "PUT" : "POST";

                const response = await fetch(url, {
                    method,
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": getCsrfToken(),
                    },
                    body: JSON.stringify(this.categoryForm),
                });

                const data = await response.json();

                if (response.ok) {
                    showSuccess(
                        this.categoryForm.id
                            ? "Category updated successfully"
                            : "Category created successfully",
                    );

                    this.showCreateCategoryModal = false;
                    this.showEditCategoryModal = false;
                    this.categoryForm = {
                        id: null,
                        name: "",
                        slug: "",
                        description: "",
                    };
                    this.loadCategories();
                } else {
                    showError(data.message || "Failed to save category");
                }
            } catch (error) {
                console.error("Error saving category:", error);
                showError("Error saving category: " + error.message);
            }
        },

        editCategory(categoryId) {
            const category = this.categories.find((c) => c.id == categoryId);
            if (category) {
                this.categoryForm = { ...category };
                this.showEditCategoryModal = true;
            }
        },

        async deleteCategory(categoryId) {
            if (!confirm("Are you sure you want to delete this category?")) {
                return;
            }

            try {
                const response = await fetch(
                    `/admin/api/categories/${categoryId}`,
                    {
                        method: "DELETE",
                        headers: {
                            "X-CSRF-TOKEN": getCsrfToken(),
                        },
                    },
                );

                const data = await response.json();

                if (response.ok) {
                    showSuccess("Category deleted successfully");
                    this.loadCategories();
                } else {
                    showError(data.message || "Failed to delete category");
                }
            } catch (error) {
                console.error("Error deleting category:", error);
                showError("Error deleting category: " + error.message);
            }
        },

        get filteredCategories() {
            if (!this.categorySearch) {
                return this.categories;
            }
            return this.categories.filter((category) => {
                const search = this.categorySearch.toLowerCase();
                return (
                    category.name.toLowerCase().includes(search) ||
                    (category.description &&
                        category.description.toLowerCase().includes(search))
                );
            });
        },

        formatDate(dateString) {
            if (!dateString) return "-";
            return new Date(dateString).toLocaleDateString("id-ID");
        },

        // Badge CRUD operations
        async createBadge(badgeData) {
            try {
                const mutation = `
                    mutation CreateBadge($input: CreateBadgeInput!) {
                        createBadge(input: $input) {
                            id
                            code
                            name
                            description
                            icon_url
                            created_at
                        }
                    }
                `;

                const variables = {
                    input: {
                        code: badgeData.code,
                        name: badgeData.name,
                        description: badgeData.description,
                        icon_url: badgeData.icon_url,
                    },
                };

                const response = await fetch(getGraphqlEndpoint(), {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": getCsrfToken(),
                    },
                    body: JSON.stringify({ query: mutation, variables }),
                });

                const result = await response.json();
                if (result.errors) {
                    throw new Error(
                        result.errors.map((e) => e.message).join(", "),
                    );
                }

                if (result.data) {
                    this.badges.push(result.data.createBadge);
                    this.showCreateBadgeModal = false;
                    showSuccess("Badge created successfully");
                    this.loadBadges();
                }
            } catch (error) {
                console.error("Error creating badge:", error);
                showError("Failed to create badge: " + error.message);
            }
        },

        async updateBadge(badgeId, badgeData) {
            try {
                const mutation = `
                    mutation UpdateBadge($id: ID!, $input: UpdateBadgeInput!) {
                        updateBadge(id: $id, input: $input) {
                            id
                            code
                            name
                            description
                            icon_url
                        }
                    }
                `;

                const variables = {
                    id: badgeId,
                    input: {
                        code: badgeData.code,
                        name: badgeData.name,
                        description: badgeData.description,
                        icon_url: badgeData.icon_url,
                    },
                };

                const response = await fetch(getGraphqlEndpoint(), {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": getCsrfToken(),
                    },
                    body: JSON.stringify({ query: mutation, variables }),
                });

                const result = await response.json();
                if (result.errors) {
                    throw new Error(
                        result.errors.map((e) => e.message).join(", "),
                    );
                }

                if (result.data) {
                    const index = this.badges.findIndex((b) => b.id == badgeId);
                    if (index !== -1) {
                        this.badges[index] = result.data.updateBadge;
                    }
                    showSuccess("Badge updated successfully");
                    this.loadBadges();
                }
            } catch (error) {
                console.error("Error updating badge:", error);
                showError("Failed to update badge: " + error.message);
            }
        },

        async deleteBadge(badgeId) {
            if (!confirm("Are you sure you want to delete this badge?")) return;

            try {
                const mutation = `
                    mutation DeleteBadge($id: ID!) {
                        deleteBadge(id: $id)
                    }
                `;

                const variables = { id: badgeId };

                const response = await fetch(getGraphqlEndpoint(), {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": getCsrfToken(),
                    },
                    body: JSON.stringify({ query: mutation, variables }),
                });

                const result = await response.json();
                if (result.errors) {
                    throw new Error(
                        result.errors.map((e) => e.message).join(", "),
                    );
                }

                if (result.data) {
                    this.badges = this.badges.filter((b) => b.id != badgeId);
                    showSuccess("Badge deleted successfully");
                }
            } catch (error) {
                console.error("Error deleting badge:", error);
                showError("Failed to delete badge: " + error.message);
            }
        },

        editBadge(badgeId) {
            const badge = this.badges.find((b) => b.id == badgeId);
            if (!badge) return;

            this.editingBadge = badge;
            this.showEditBadgeModal = true;
        },

        // Notification CRUD operations
        async createNotification(notificationData) {
            try {
                const mutation = `
                    mutation CreateNotification($input: CreateNotificationInput!) {
                        createNotification(input: $input) {
                            id
                            user_id
                            type
                            payload
                            is_read
                            sent_at
                            user {
                                id
                                name
                            }
                        }
                    }
                `;

                const variables = {
                    input: {
                        user_id: notificationData.user_id,
                        type: notificationData.type,
                        payload: notificationData.payload,
                    },
                };

                const response = await fetch(getGraphqlEndpoint(), {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": getCsrfToken(),
                    },
                    body: JSON.stringify({ query: mutation, variables }),
                });

                const result = await response.json();
                if (result.errors) {
                    throw new Error(
                        result.errors.map((e) => e.message).join(", "),
                    );
                }

                if (result.data) {
                    this.notifications.push(result.data.createNotification);
                    this.showCreateNotificationModal = false;
                    showSuccess("Notification sent successfully");
                    this.loadNotifications();
                }
            } catch (error) {
                console.error("Error creating notification:", error);
                showError("Failed to send notification: " + error.message);
            }
        },

        async updateNotification(notificationId, notificationData) {
            try {
                const mutation = `
                    mutation UpdateNotification($id: ID!, $input: UpdateNotificationInput!) {
                        updateNotification(id: $id, input: $input) {
                            id
                            is_read
                        }
                    }
                `;

                const variables = {
                    id: notificationId,
                    input: {
                        is_read: notificationData.is_read,
                    },
                };

                const response = await fetch(getGraphqlEndpoint(), {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": getCsrfToken(),
                    },
                    body: JSON.stringify({ query: mutation, variables }),
                });

                const result = await response.json();
                if (result.errors) {
                    throw new Error(
                        result.errors.map((e) => e.message).join(", "),
                    );
                }

                if (result.data) {
                    const index = this.notifications.findIndex(
                        (n) => n.id == notificationId,
                    );
                    if (index !== -1) {
                        this.notifications[index] = {
                            ...this.notifications[index],
                            ...result.data.updateNotification,
                        };
                    }
                    showSuccess("Notification updated successfully");
                    this.loadNotifications();
                }
            } catch (error) {
                console.error("Error updating notification:", error);
                showError("Failed to update notification: " + error.message);
            }
        },

        async deleteNotification(notificationId) {
            if (!confirm("Are you sure you want to delete this notification?"))
                return;

            try {
                const response = await fetch(
                    `/admin/api/notifications/${notificationId}`,
                    {
                        method: "DELETE",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": getCsrfToken(),
                        },
                    },
                );

                if (!response.ok) {
                    const error = await response.text();
                    throw new Error(error || "Failed to delete notification");
                }

                this.notifications = this.notifications.filter(
                    (n) => n.id != notificationId,
                );
                showSuccess("Notification deleted successfully");
            } catch (error) {
                console.error("Error deleting notification:", error);
                showError("Failed to delete notification: " + error.message);
            }
        },

        editNotification(notificationId) {
            const notification = this.notifications.find(
                (n) => n.id == notificationId,
            );
            if (!notification) return;

            this.editingNotification = notification;
            this.showEditNotificationModal = true;
        },

        async loadPendingSubmissions() {
            this.grading.loading = true;
            try {
                const response = await fetch("/admin/api/grading/pending");
                const data = await response.json();
                this.grading.submissions = data.data || [];
            } catch (e) {
                console.error("Error loading pending submissions", e);
            } finally {
                this.grading.loading = false;
            }
        },

        async openGradingModal(submissionId) {
            this.grading.loading = true;
            try {
                const response = await fetch(
                    `/admin/api/grading/${submissionId}`,
                );
                const data = await response.json();
                this.grading.currentSubmission = data;

                this.grading.grades = {};
                if (data.answers) {
                    data.answers.forEach((ans) => {
                        // Use points_earned if available, fallback to points or 0
                        const currentPoints =
                            ans.points_earned !== undefined
                                ? ans.points_earned
                                : ans.points || 0;
                        this.grading.grades[ans.question_id] = currentPoints;
                    });
                }

                this.grading.showModal = true;
            } catch (e) {
                console.error("Error loading submission", e);
            } finally {
                this.grading.loading = false;
            }
        },

        getAnswerText(questionId) {
            if (!this.grading.currentSubmission)
                return "Tidak ada data submission";

            const ans = this.grading.currentSubmission.answers.find(
                (a) => a.question_id === questionId,
            );
            if (!ans)
                return '<span class="text-gray-400 italic">Tidak ada jawaban</span>';

            // Prefer the 'answer' field if it exists and has content
            let displayAnswer = ans.answer;

            // Fallback: reconstruct from user_answer if answer field is empty
            if (!displayAnswer && ans.user_answer) {
                if (ans.type === "mcq" || ans.type === "truefalse") {
                    // Find the choice text from quiz questions
                    const question =
                        this.grading.currentSubmission.quiz.questions.find(
                            (q) => q.id === questionId,
                        );
                    if (question && question.choices) {
                        const choice = question.choices.find(
                            (c) => c.id == ans.user_answer,
                        );
                        if (choice) {
                            displayAnswer = choice.text;
                        }
                    }
                } else if (ans.type === "essay") {
                    // For essay, user_answer might contain the answer
                    displayAnswer = ans.user_answer;
                }
            }

            // If still no answer
            if (!displayAnswer) {
                return '<span class="text-gray-400 italic">Jawaban kosong</span>';
            }

            // For essay, show the full text with formatting
            if (ans.type === "essay") {
                return `<div class="whitespace-pre-wrap">${this.escapeHtml(displayAnswer)}</div>`;
            }

            // For MCQ/TrueFalse, show the answer text
            return this.escapeHtml(displayAnswer);
        },

        escapeHtml(text) {
            if (!text) return "";
            const div = document.createElement("div");
            div.textContent = text;
            return div.innerHTML;
        },

        async submitGrades() {
            if (!this.grading.currentSubmission) return;

            try {
                const response = await fetch(
                    `/admin/api/grading/${this.grading.currentSubmission.id}/grade`,
                    {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": getCsrfToken(),
                        },
                        body: JSON.stringify({ grades: this.grading.grades }),
                    },
                );

                if (response.ok) {
                    showSuccess("Nilai berhasil disimpan");
                    this.grading.showModal = false;
                    this.loadPendingSubmissions();
                } else {
                    showError("Gagal menyimpan nilai");
                }
            } catch (e) {
                showError("Terjadi kesalahan");
            }
        },

        async loadQuizzes() {
            this.quizzes.loading = true;
            try {
                const response = await fetch("/admin/api/quizzes");
                const data = await response.json();
                this.quizzes.list = data || [];
            } catch (e) {
                console.error("Error loading quizzes", e);
            } finally {
                this.quizzes.loading = false;
            }
        },

        editQuizFromList(quiz) {
            // Navigate to course page and open lesson editor
            if (quiz.lesson && quiz.course) {
                window.location.href = `/admin/courses?edit_lesson=${quiz.lesson.id}`;
            }
        },

        async deleteQuiz(quizId) {
            if (!confirm("Apakah Anda yakin ingin menghapus quiz ini?")) return;

            try {
                const response = await fetch(
                    `/admin/api/quiz/${quizId}/delete`,
                    {
                        method: "DELETE",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": getCsrfToken(),
                        },
                    },
                );

                if (response.ok) {
                    showSuccess("Quiz berhasil dihapus");
                    this.loadQuizzes();
                } else {
                    showError("Gagal menghapus quiz");
                }
            } catch (e) {
                showError("Terjadi kesalahan");
            }
        },

        debounceSearch() {
            if (this.searchTimeout) {
                clearTimeout(this.searchTimeout);
            }
            this.searchTimeout = setTimeout(() => {
                this.loadUsers();
            }, 300); // 300ms delay
        },
    };
}

async function loadAdminData() {
    if (userLevelName !== "Admin") {
        showError("Akses ditolak. Halaman ini hanya untuk Admin.");
        return;
    }

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
                users(first: 5, page: 1) {
                    data {
                        id
                        name
                        email
                        created_at
                        roles {
                            name
                        }
                    }
                }
                courses(first: 5, page: 1) {
                    data {
                        id
                        title
                        slug
                        instructor {
                            name
                        }
                        enrollments_count
                        created_at
                    }
                }
            }
        `;

        const response = await fetch(getGraphqlEndpoint(), {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": getCsrfToken(),
            },
            body: JSON.stringify({ query }),
        });

        const result = await response.json();
        console.log("GraphQL response (loadAdminData):", result);
        if (result.errors) {
            throw new Error(result.errors.map((e) => e.message).join(", "));
        }

        if (result.data) {
            renderRecentUsers(result.data);
            renderRecentCourses(result.data);
            renderPendingApprovals(result.data);
        }
    } catch (error) {
        console.error("Error loading admin data:", error);
        showError("Gagal memuat data admin: " + error.message);
    }
}

function renderRecentUsers(data) {
    const container = document.getElementById("recentUsersContainer");
    const users = Array.isArray(data.users)
        ? data.users
        : data.users?.data || [];

    container.innerHTML = users.length
        ? ""
        : '<p class="text-gray-500">Tidak ada pengguna terbaru.</p>';

    users.slice(0, 5).forEach((user) => {
        const userDiv = document.createElement("div");
        userDiv.className =
            "flex items-center justify-between p-3 bg-gray-50 rounded-lg";
        userDiv.innerHTML = `
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                    <span class="text-white text-sm font-bold">${user.name
                        .charAt(0)
                        .toUpperCase()}</span>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-900">${
                        user.name
                    }</p>
                    <p class="text-xs text-gray-500">${user.email}</p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-xs text-gray-500">${formatDate(
                    user.created_at,
                )}</p>
                <div class="flex flex-wrap gap-1 mt-1">
                    ${user.roles
                        .map(
                            (role) =>
                                `<span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">${role.name}</span>`,
                        )
                        .join("")}
                </div>
            </div>
        `;
        container.appendChild(userDiv);
    });
}

function renderRecentCourses(data) {
    const container = document.getElementById("recentCoursesContainer");
    const courses = Array.isArray(data.courses)
        ? data.courses
        : data.courses?.data || [];

    container.innerHTML = courses.length
        ? ""
        : '<p class="text-gray-500">Tidak ada kursus terbaru.</p>';

    courses.slice(0, 5).forEach((course) => {
        const courseDiv = document.createElement("div");
        courseDiv.className =
            "flex items-center justify-between p-3 bg-gray-50 rounded-lg";
        courseDiv.innerHTML = `
            <div class="flex items-center space-x-3">
                <img src="${
                    course.thumbnail_url || "/images/course-placeholder.jpg"
                }" alt="${
                    course.title
                }" class="w-10 h-10 rounded-lg object-cover">
                <div>
                    <p class="text-sm font-medium text-gray-900 line-clamp-1">${
                        course.title
                    }</p>
                    <p class="text-xs text-gray-500">Oleh ${
                        course.instructor?.name || "Unknown"
                    }</p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-700">${
                    course.enrollments_count || 0
                } siswa</p>
                <p class="text-xs text-gray-500">${formatDate(
                    course.created_at,
                )}</p>
            </div>
        `;
        container.appendChild(courseDiv);
    });
}

function renderPendingApprovals(data) {
    const container = document.getElementById("pendingApprovalsContainer");
    if (!container) return; // Safe-guard: admin layout may not include pending approvals section
    const approvals = data.pendingApprovals || [];

    container.innerHTML = approvals.length
        ? ""
        : '<p class="text-gray-500">Tidak ada persetujuan yang menunggu.</p>';

    approvals.forEach((approval) => {
        const approvalDiv = document.createElement("div");
        approvalDiv.className =
            "flex items-center justify-between p-3 bg-yellow-50 border border-yellow-200 rounded-lg";
        approvalDiv.innerHTML = `
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-600"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-900">${
                        approval.title
                    }</p>
                    <p class="text-xs text-gray-500">Oleh ${
                        approval.user?.name || "Unknown"
                    } • ${approval.type}</p>
                </div>
            </div>
            <div class="flex space-x-2">
                <button onclick="approveItem(${approval.id}, '${
                    approval.type
                }')" class="px-3 py-1 text-xs bg-green-600 text-white rounded hover:bg-green-700">
                    Setujui
                </button>
                <button onclick="rejectItem(${approval.id}, '${
                    approval.type
                }')" class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700">
                    Tolak
                </button>
            </div>
        `;
        container.appendChild(approvalDiv);
    });
}

async function approveItem(id, type) {
    try {
        const mutation = `mutation { approve${
            type.charAt(0).toUpperCase() + type.slice(1)
        }(id: ${id}) { id status } }`;
        const response = await fetch(getGraphqlEndpoint(), {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": getCsrfToken(),
            },
            body: JSON.stringify({ query: mutation }),
        });

        const result = await response.json();
        if (result.errors) {
            throw new Error(result.errors.map((e) => e.message).join(", "));
        }

        if (result.data) {
            loadAdminData();
            showSuccess("Item berhasil disetujui.");
        }
    } catch (error) {
        console.error("Error approving item:", error);
        showError("Gagal menyetujui item: " + error.message);
    }
}

async function rejectItem(id, type) {
    try {
        const mutation = `mutation { reject${
            type.charAt(0).toUpperCase() + type.slice(1)
        }(id: ${id}) { id status } }`;
        const response = await fetch(getGraphqlEndpoint(), {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": getCsrfToken(),
            },
            body: JSON.stringify({ query: mutation }),
        });

        const result = await response.json();
        if (result.errors) {
            throw new Error(result.errors.map((e) => e.message).join(", "));
        }

        if (result.data) {
            loadAdminData();
            showSuccess("Item berhasil ditolak.");
        }
    } catch (error) {
        console.error("Error rejecting item:", error);
        showError("Gagal menolak item: " + error.message);
    }
}

function formatDate(dateString) {
    return new Date(dateString).toLocaleDateString("id-ID", {
        day: "2-digit",
        month: "short",
        year: "numeric",
    });
}

function showError(message) {
    const errorDiv = document.createElement("div");
    errorDiv.className =
        "fixed top-4 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded";
    errorDiv.innerHTML = `<span>${message}</span><button onclick="this.parentElement.remove()" class="ml-4 text-red-700">x</button>`;
    document.body.appendChild(errorDiv);
    setTimeout(() => errorDiv.remove(), 5000);
}

function showSuccess(message) {
    const successDiv = document.createElement("div");
    successDiv.className =
        "fixed top-4 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded";
    successDiv.innerHTML = `<span>${message}</span><button onclick="this.parentElement.remove()" class="ml-4 text-green-700">x</button>`;
    document.body.appendChild(successDiv);
    setTimeout(() => successDiv.remove(), 5000);
}

// ============================================================================
// CRITICAL: Expose adminManager factory and proxy to the live Alpine instance
// This must happen before Alpine.js evaluates x-data directives
// ============================================================================
const adminFactory = adminManager; // keep reference to original factory
// Expose a global function `admin` that returns the live Alpine instance
// when available, otherwise falls back to creating a transient instance.
window.admin = function () {
    try {
        const root = document.getElementById("admin-root");
        if (root && root.__x && root.__x.$data) return root.__x.$data;
    } catch (e) {
        // ignore
    }
    return adminFactory();
};

// If Alpine is already loaded, register the component factory immediately so
// Alpine picks it up during its initialization scan. Also listen for Alpine's
// own init event to register in case Alpine loads after this script.
if (window.Alpine) {
    try {
        Alpine.data("admin", adminFactory);
    } catch (e) {
        console.warn("[Admin] Alpine.data registration failed:", e);
    }
}

document.addEventListener("alpine:init", () => {
    try {
        Alpine.data("admin", adminFactory);
    } catch (e) {
        console.warn("[Admin] alpine:init registration failed:", e);
    }
});
document.addEventListener("DOMContentLoaded", () => {
    // Do NOT expose `adminManager` global wrapper — it can cause recursion/confusion.
    // Use `window.admin` (factory) for immediate access to the live instance.

    // When DOM is ready, start loading admin data only if on dashboard page (Alpine will initialize the component
    // itself; registering the factory via alpine:init ensures it is available during
    // Alpine's initialization scan).
    try {
        // Only load admin data if we're on the dashboard page (has recentUsersContainer)
        if (document.getElementById("recentUsersContainer")) {
            loadAdminData();
        }
    } catch (e) {
        // ignore
    }
});
