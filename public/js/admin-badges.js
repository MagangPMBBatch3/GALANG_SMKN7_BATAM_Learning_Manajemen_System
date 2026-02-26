function badgeMethods() {
    return {
        showCreateBadgeModal: false,
        showEditBadgeModal: false,
        editingBadge: {},

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
                    this.badges = this.badges.filter((b) => b.id.toString() !== badgeId.toString());
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
    };
}
