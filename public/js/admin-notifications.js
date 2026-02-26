function notificationMethods() {
    return {
        showCreateNotificationModal: false,
        showEditNotificationModal: false,
        editingNotification: {},

        // Notification CRUD operations
        async createNotification(notificationData) {
            try {
                const mutation = `
                    mutation CreateNotification($input: CreateNotificationInput!) {
                        createNotification(input: $input) {
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
                    }
                `;

                const variables = {
                    input: {
                        user_id: notificationData.user_id,
                        title: notificationData.title,
                        message: notificationData.message,
                        type: notificationData.type,
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
                    this.notifications.push(result.data.createNotification);
                    this.showCreateNotificationModal = false;
                    showSuccess("Notification created successfully");
                    this.loadNotifications();
                }
            } catch (error) {
                console.error("Error creating notification:", error);
                showError("Failed to create notification: " + error.message);
            }
        },

        async updateNotification(notificationId, notificationData) {
            try {
                const mutation = `
                    mutation UpdateNotification($id: ID!, $input: UpdateNotificationInput!) {
                        updateNotification(id: $id, input: $input) {
                            id
                            title
                            message
                            type
                            is_read
                            user {
                                id
                                name
                            }
                        }
                    }
                `;

                const variables = {
                    id: notificationId,
                    input: {
                        title: notificationData.title,
                        message: notificationData.message,
                        type: notificationData.type,
                        is_read: notificationData.is_read,
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
                    const index = this.notifications.findIndex(
                        (n) => n.id == notificationId
                    );
                    if (index !== -1) {
                        this.notifications[index] = result.data.updateNotification;
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
                const mutation = `
                    mutation DeleteNotification($id: ID!) {
                        deleteNotification(id: $id)
                    }
                `;

                const variables = { id: notificationId };

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
                    this.notifications = this.notifications.filter(
                        (n) => n.id.toString() !== notificationId.toString()
                    );
                    showSuccess("Notification deleted successfully");
                }
            } catch (error) {
                console.error("Error deleting notification:", error);
                showError("Failed to delete notification: " + error.message);
            }
        },

        editNotification(notificationId) {
            const notification = this.notifications.find(
                (n) => n.id == notificationId
            );
            if (!notification) return;

            this.editingNotification = notification;
            this.showEditNotificationModal = true;
        },
    };
}
