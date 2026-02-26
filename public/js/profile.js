/**
 * Profile Service for MaxCourse Student Portal
 * Handles GraphQL fetching and data formatting for the profile page
 */

window.ProfileService = {
    getCsrfToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        if (!meta) {
            console.warn('CSRF token not found');
            return '';
        }
        return meta.getAttribute('content');
    },

    async fetchProfileData(userId) {
        try {
            const query = `
                query {
                    userStats(user_id: ${parseInt(userId)}) {
                        total_courses_enrolled
                        total_courses_completed
                        total_quizzes_taken
                        average_quiz_score
                        total_points
                        total_badges
                    }
                    certificates(user_id: ${parseInt(userId)}, first: 10) {
                        data {
                            id
                            course {
                                title
                            }
                            issued_at
                        }
                    }
                    userBadges(user_id: ${parseInt(userId)}) {
                        id
                        badge {
                            name
                            description
                            icon_url
                        }
                        awarded_at
                    }
                    notifications(user_id: ${parseInt(userId)}, first: 10) {
                        data {
                            id
                            type
                            payload
                            sent_at
                        }
                    }
                    pointsPaginated(user_id: ${parseInt(userId)}, first: 20) {
                        data {
                            id
                            amount
                            reason
                            created_at
                        }
                    }
                }
            `;

            const response = await fetch('/graphql', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.getCsrfToken()
                },
                body: JSON.stringify({ query })
            });

            const result = await response.json();
            if (result.errors) {
                throw new Error(result.errors.map(e => e.message).join(', '));
            }
            return result.data;
        } catch (error) {
            console.error('Error fetching profile data:', error);
            throw error;
        }
    },

    formatDate(dateString) {
        if (!dateString) return '';
        const date = new Date(dateString);
        if (isNaN(date.getTime())) return dateString;
        
        const now = new Date();
        const diffTime = Math.abs(now - date);
        const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));

        if (diffDays === 0) return 'Hari ini';
        if (diffDays === 1) return 'Kemarin';
        if (diffDays < 7) return `${diffDays} hari yang lalu`;
        if (diffDays < 30) return `${Math.floor(diffDays / 7)} minggu yang lalu`;
        
        return date.toLocaleDateString('id-ID', { month: 'short', day: 'numeric', year: 'numeric' });
    }
};

function showError(message) {
    const errorDiv = document.createElement('div');
    errorDiv.className = 'fixed top-4 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded z-[100]';
    errorDiv.innerHTML = `<span>${message}</span><button onclick="this.parentElement.remove()" class="ml-4 text-red-700 font-bold">×</button>`;
    document.body.appendChild(errorDiv);
    setTimeout(() => errorDiv.remove(), 5000);
}
