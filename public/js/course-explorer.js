(function waitForAlpine() {
    if (typeof Alpine === "undefined" || !Alpine.data) {
        setTimeout(waitForAlpine, 500);
        return;
    }

    console.log("Alpine ready, registering courseExplorer component...");

    try {
        Alpine.data("courseExplorer", () => ({
            courses: [],
            categories: [],
            loading: false,
            currentPage: 0,
            perPage: 9,
            totalPages: 0,
            search: "",
            categoryFilter: "",
            statusFilter: "",
            sortBy: "newest",
            enrolledCourseIds: [],
            searchTimeout: null,
            showPaymentModal: false,
            selectedCourse: null,
            paymentLoading: false,
            paymentForm: {
                fullName: "",
                email: "",
                paymentMethod: "credit_card",
                cardNumber: "",
                expiry: "",
                cvv: "",
            },

            async init() {
                await this.loadCategories();
                await this.loadEnrolledCourses();
                await this.loadCourses();
            },

            async loadCategories() {
                try {
                    const response = await fetch("/student/api/categories", {
                        headers: {
                            Accept: "application/json",
                            "X-CSRF-TOKEN":
                                document.querySelector(
                                    'meta[name="csrf-token"]',
                                )?.content || "",
                        },
                    });
                    if (response.ok) {
                        this.categories = await response.json();
                    }
                } catch (error) {
                    console.error("Error loading categories:", error);
                }
            },

            async loadEnrolledCourses() {
                try {
                    const response = await fetch("/student/api/enrollments", {
                        headers: {
                            Accept: "application/json",
                            "X-CSRF-TOKEN":
                                document.querySelector(
                                    'meta[name="csrf-token"]',
                                )?.content || "",
                        },
                    });
                    if (response.ok) {
                        const data = await response.json();
                        this.enrolledCourseIds = data.map((e) => e.course.id);
                    }
                } catch (error) {
                    console.error("Error loading enrolled courses:", error);
                }
            },

            async loadCourses() {
                this.loading = true;
                try {
                    const params = new URLSearchParams({
                        per_page: this.perPage,
                        page: this.currentPage + 1,
                    });

                    if (this.search) params.append("search", this.search);
                    if (this.categoryFilter)
                        params.append("category_id", this.categoryFilter);
                    if (this.statusFilter)
                        params.append("status", this.statusFilter);
                    params.append("sort", this.sortBy);

                    const response = await fetch(
                        `/student/api/courses?${params}`,
                        {
                            headers: {
                                Accept: "application/json",
                                "X-CSRF-TOKEN":
                                    document.querySelector(
                                        'meta[name="csrf-token"]',
                                    )?.content || "",
                            },
                        },
                    );

                    if (response.ok) {
                        const data = await response.json();
                        this.courses = data.data || [];
                        this.totalPages = data.last_page || 1;
                    }
                } catch (error) {
                    console.error("Error loading courses:", error);
                } finally {
                    this.loading = false;
                }
            },

            debouncedSearch() {
                clearTimeout(this.searchTimeout);
                this.searchTimeout = setTimeout(() => {
                    this.currentPage = 0;
                    this.loadCourses();
                }, 300);
            },

            isEnrolled(courseId) {
                return this.enrolledCourseIds.includes(courseId);
            },

            handleEnrollClick(course) {
                if (this.isEnrolled(course.id)) return;

                // If course is free, enroll directly
                if (!course.price || course.price === 0) {
                    this.enrollCourse(course, null);
                } else {
                    // Show payment modal for paid courses
                    this.selectedCourse = course;
                    this.showPaymentModal = true;
                    this.resetPaymentForm();
                }
            },

            resetPaymentForm() {
                this.paymentForm = {
                    fullName: "",
                    email: "",
                    paymentMethod: "credit_card",
                    cardNumber: "",
                    expiry: "",
                    cvv: "",
                };
            },

            closePaymentModal() {
                this.showPaymentModal = false;
                this.selectedCourse = null;
                this.resetPaymentForm();
            },

            async processPayment() {
                const form = this.paymentForm;

                // Simple validation
                if (
                    !form.fullName ||
                    !form.email ||
                    !form.cardNumber ||
                    !form.expiry ||
                    !form.cvv
                ) {
                    alert("Please fill in all payment details");
                    return;
                }

                // Enroll with payment data
                const paymentData = {
                    amount: this.selectedCourse.price,
                    currency: "IDR",
                    method: form.paymentMethod,
                    cardNumber: form.cardNumber,
                    cardHolder: form.fullName,
                    cardExpiry: form.expiry,
                    cardCvv: form.cvv,
                    email: form.email,
                    transaction_ref: "TRX" + Date.now(),
                };

                await this.enrollCourse(this.selectedCourse, paymentData);
            },

            async enrollCourse(course, paymentData) {
                if (this.isEnrolled(course.id)) return;

                this.paymentLoading = true;
                try {
                    const body = {
                        course_id: course.id,
                    };

                    // Add payment data if provided
                    if (paymentData) {
                        body.payment = paymentData;
                    }

                    const response = await fetch("/student/api/enroll", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            Accept: "application/json",
                            "X-CSRF-TOKEN":
                                document.querySelector(
                                    'meta[name="csrf-token"]',
                                )?.content || "",
                        },
                        body: JSON.stringify(body),
                    });

                    if (response.ok) {
                        this.enrolledCourseIds.push(course.id);
                        this.closePaymentModal();
                        alert("Successfully enrolled in " + course.title + "!");
                        setTimeout(() => {
                            window.location.href = "/learn/" + course.slug;
                        }, 800);
                    } else {
                        const error = await response.json();
                        alert(
                            "Error: " + (error.message || "Failed to enroll"),
                        );
                    }
                } catch (error) {
                    console.error("Error enrolling:", error);
                    alert("An error occurred while enrolling");
                } finally {
                    this.paymentLoading = false;
                }
            },

            nextPage() {
                if (this.currentPage + 1 < this.totalPages) {
                    this.currentPage++;
                    this.loadCourses();
                }
            },

            previousPage() {
                if (this.currentPage > 0) {
                    this.currentPage--;
                    this.loadCourses();
                }
            },
        }));

        console.log("courseExplorer component registered successfully");
    } catch (error) {
        console.error("Error registering courseExplorer:", error);
    }
})();
