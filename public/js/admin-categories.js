function categoryMethods() {
    return {
        showCreateCategoryModal: false,

        // Category CRUD operations
        async createCategory(categoryData) {
            try {
                const mutation = `
                    mutation CreateCategory($input: CreateCategoryInput!) {
                        createCategory(input: $input) {
                            id
                            name
                            slug
                            description
                            created_at
                        }
                    }
                `;

                const variables = {
                    input: {
                        name: categoryData.name,
                        slug: categoryData.slug,
                        description: categoryData.description,
                        parent_id: categoryData.parent_id,
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
                    this.categories.push(result.data.createCategory);
                    this.showCreateCategoryModal = false;
                    showSuccess("Category created successfully");
                    this.loadCategories();
                }
            } catch (error) {
                console.error("Error creating category:", error);
                showError("Failed to create category: " + error.message);
            }
        },

        async updateCategory(categoryId, categoryData) {
            try {
                const mutation = `
                    mutation UpdateCategory($id: ID!, $input: UpdateCategoryInput!) {
                        updateCategory(id: $id, input: $input) {
                            id
                            name
                            slug
                            description
                        }
                    }
                `;

                const variables = {
                    id: categoryId,
                    input: {
                        name: categoryData.name,
                        slug: categoryData.slug,
                        description: categoryData.description,
                        parent_id: categoryData.parent_id,
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
                    const index = this.categories.findIndex(
                        (c) => c.id == categoryId
                    );
                    if (index !== -1) {
                        this.categories[index] = result.data.updateCategory;
                    }
                    showSuccess("Category updated successfully");
                    this.loadCategories();
                }
            } catch (error) {
                console.error("Error updating category:", error);
                showError("Failed to update category: " + error.message);
            }
        },

        async deleteCategory(categoryId) {
            if (!confirm("Are you sure you want to delete this category?"))
                return;

            try {
                const mutation = `
                    mutation DeleteCategory($id: ID!) {
                        deleteCategory(id: $id)
                    }
                `;

                const variables = { id: categoryId };

                const response = await fetch("/graphql", {
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
                    this.categories = this.categories.filter(
                        (c) => c.id.toString() !== categoryId.toString()
                    );
                    showSuccess("Category deleted successfully");
                }
            } catch (error) {
                console.error("Error deleting category:", error);
                showError("Failed to delete category: " + error.message);
            }
        },
    };
}
