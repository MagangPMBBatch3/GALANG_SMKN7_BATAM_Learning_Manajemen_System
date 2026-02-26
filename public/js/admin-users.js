function userMethods() {
    return {
        showCreateUserModal: false,
        showEditUserModal: false,
        editingUser: {},

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
                    this.users.push(result.data.createUser);
                    this.showCreateUserModal = false;
                    showSuccess("User created successfully");
                    this.loadUsers();
                }
            } catch (error) {
                console.error("Error creating user:", error);
                showError("Failed to create user: " + error.message);
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
                        result.errors.map((e) => e.message).join(", ")
                    );
                }

                if (result.data) {
                    const index = this.users.findIndex((u) => u.id == userId);
                    if (index !== -1) {
                        this.users[index] = result.data.updateUser;
                    }
                    this.showEditUserModal = false;
                    showSuccess("User updated successfully");
                    this.loadUsers();
                }
            } catch (error) {
                console.error("Error updating user:", error);
                showError("Failed to update user: " + error.message);
            }
        },

        async deleteUser(userId) {
            if (!confirm("Are you sure you want to delete this user?")) return;

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
                        result.errors.map((e) => e.message).join(", ")
                    );
                }

                if (result.data) {
                    this.users = this.users.filter((u) => u.id.toString() !== userId.toString());
                    showSuccess("User deleted successfully");
                }
            } catch (error) {
                console.error("Error deleting user:", error);
                showError("Failed to delete user: " + error.message);
            }
        },

        // Edit methods
        async editUser(userId) {
            let user = this.users.find((u) => u.id == userId);
            if (!user) {
                // User not in current page, fetch individually
                try {
                    const query = `
                        query GetUser($id: ID!) {
                            user(id: $id) {
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

                    const result = await this.doGraphql(query, { id: userId });
                    if (result.data && result.data.user) {
                        user = result.data.user;
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
            this.showEditUserModal = true;
        },
    };
}
