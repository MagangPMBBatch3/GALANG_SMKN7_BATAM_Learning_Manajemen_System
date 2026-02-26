function courseMethods() {
    return {
        showCreateCourseModal: false,
        showEditCourseModal: false,
        editingCourse: {},

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
                        result.errors.map((e) => e.message).join(", ")
                    );
                }

                if (result.data) {
                    this.courses.push(result.data.createCourse);
                    this.showCreateCourseModal = false;
                    showSuccess("Course created successfully");
                    this.loadCourses();
                }
            } catch (error) {
                console.error("Error creating course:", error);
                showError("Failed to create course: " + error.message);
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

                const response = await fetch("graphql", {
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
                        result.errors.map((e) => e.message).join(", ")
                    );
                }

                if (result.data) {
                    const index = this.courses.findIndex(
                        (c) => c.id == courseId
                    );
                    if (index !== -1) {
                        this.courses[index] = result.data.updateCourse;
                    }
                    showSuccess("Course updated successfully");
                    this.loadCourses();
                }
            } catch (error) {
                console.error("Error updating course:", error);
                showError("Failed to update course: " + error.message);
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

                const response = await fetch("graphql", {
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
                        result.errors.map((e) => e.message).join(", ")
                    );
                }

                if (result.data) {
                    this.courses = this.courses.filter(
                        (c) => c.id.toString() !== courseId.toString()
                    );
                    showSuccess("Course deleted successfully");
                }
            } catch (error) {
                console.error("Error deleting course:", error);
                showError("Failed to delete course: " + error.message);
            }
        },

        editCourse(courseId) {
            const course = this.courses.find((c) => c.id == courseId);
            if (!course) return;

            // Populate edit modal with course data
            this.editingCourse = course;
            this.showEditCourseModal = true;
            // This would need to be implemented in the view with Alpine.js
        },
    };
}
