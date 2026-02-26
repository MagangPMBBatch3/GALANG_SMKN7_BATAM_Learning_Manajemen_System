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
            'CSRF token not found. Please ensure <meta name="csrf-token"> is included in the HTML.'
        );
    }
    return meta.getAttribute("content");
}

async function loadMyCoursesData() {
    try {
        const query = buildMyCoursesQuery();
        const response = await fetch("/graphql", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": getCsrfToken(),
            },
            body: JSON.stringify({ query }),
        });

        const result = await response.json();
        console.log("GraphQL response (loadMyCoursesData):", result);
        if (result.errors) {
            throw new Error(result.errors.map((e) => e.message).join(", "));
        }

        if (result.data) {
            renderMyCourses(result.data);
            updateMyCoursesStats(result.data);
        }
    } catch (error) {
        console.error("Error loading my courses data:", error);
        showError("Gagal memuat data kursus saya: " + error.message);
    }
}

function buildMyCoursesQuery() {
    if (userLevelName === "Instructor") {
        return `
            query {
                myCourses(user_id: ${parseInt(userProfileId)}) {
                    id
                    title
                    slug
                    short_description
                    thumbnail_url
                    price
                    rating_avg
                    rating_count
                    enrollments_count
                    level
                    category {
                        name
                    }
                    created_at
                    updated_at
                }
            }
        `;
    } else {
        return `
            query {
                myEnrolledCourses(user_id: ${parseInt(userProfileId)}) {
                    id
                    title
                    slug
                    short_description
                    thumbnail_url
                    progress_percentage
                    last_accessed_at
                    course {
                        instructor {
                            name
                        }
                        rating_avg
                        lessons_count
                    }
                }
            }
        `;
    }
}

function renderMyCourses(data) {
    const container = document.getElementById("myCoursesContainer");
    let courses = [];

    if (userLevelName === "Instructor") {
        courses = data.myCourses || [];
    } else {
        courses = data.myEnrolledCourses || [];
    }

    container.innerHTML = courses.length
        ? ""
        : `
        <div class="col-span-full text-center py-8">
            <div class="text-gray-500">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada kursus</h3>
                <p class="mt-1 text-sm text-gray-500">${
                    userLevelName === "Instructor"
                        ? "Anda belum membuat kursus apapun."
                        : "Anda belum mendaftar kursus apapun."
                }</p>
            </div>
        </div>
    `;

    courses.forEach((item) => {
        const card = document.createElement("div");
        card.className =
            "bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-all duration-200";

        if (userLevelName === "Instructor") {
            card.onclick = () =>
                (window.location.href = `/courses/${item.slug}`);
            card.innerHTML = `
                <img src="${
                    item.thumbnail_url || "/images/course-placeholder.jpg"
                }" alt="${item.title}" class="w-full h-48 object-cover">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-2">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">${
                            item.category?.name || "Umum"
                        }</span>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">${
                            item.level || "Semua Tingkat"
                        }</span>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2 line-clamp-2">${
                        item.title
                    }</h3>
                    <p class="text-gray-600 text-sm mb-4 line-clamp-3">${
                        item.short_description
                    }</p>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-users text-gray-500"></i>
                            <span class="text-sm text-gray-700">${
                                item.enrollments_count || 0
                            } siswa</span>
                        </div>
                        <div class="text-right">
                            <div class="flex items-center mb-1">
                                <i class="fas fa-star text-yellow-400 mr-1"></i>
                                <span class="text-sm font-medium">${(
                                    item.rating_avg || 0
                                ).toFixed(1)}</span>
                                <span class="text-xs text-gray-500 ml-1">(${
                                    item.rating_count || 0
                                })</span>
                            </div>
                            <div class="text-lg font-bold text-blue-600">${new Intl.NumberFormat(
                                "id-ID",
                                {
                                    style: "currency",
                                    currency: "IDR",
                                    maximumFractionDigits: 0,
                                }
                            ).format(item.price || 0)}</div>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-200 flex justify-between items-center">
                        <span class="text-xs text-gray-500">Dibuat ${formatDate(
                            item.created_at
                        )}</span>
                        <div class="flex space-x-2">
                            <button onclick="event.stopPropagation(); editCourse(${
                                item.id
                            })" class="text-blue-600 hover:text-blue-800 text-sm">Edit</button>
                            <button onclick="event.stopPropagation(); manageCourse(${
                                item.id
                            })" class="text-green-600 hover:text-green-800 text-sm">Kelola</button>
                        </div>
                    </div>
                </div>
            `;
        } else {
            card.onclick = () =>
                (window.location.href = `/courses/${item.slug}`);
            card.innerHTML = `
                <img src="${
                    item.thumbnail_url || "/images/course-placeholder.jpg"
                }" alt="${item.title}" class="w-full h-48 object-cover">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2 line-clamp-2">${
                        item.title
                    }</h3>
                    <p class="text-gray-600 text-sm mb-4 line-clamp-3">${
                        item.short_description
                    }</p>
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-2">
                            <div class="w-6 h-6 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                <span class="text-white text-xs font-bold">${
                                    item.course?.instructor?.name
                                        ?.charAt(0)
                                        ?.toUpperCase() || "I"
                                }</span>
                            </div>
                            <span class="text-sm text-gray-700">${
                                item.course?.instructor?.name || "Instruktur"
                            }</span>
                        </div>
                        <div class="text-right">
                            <div class="flex items-center mb-1">
                                <i class="fas fa-star text-yellow-400 mr-1"></i>
                                <span class="text-sm font-medium">${(
                                    item.course?.rating_avg || 0
                                ).toFixed(1)}</span>
                            </div>
                            <span class="text-xs text-gray-500">${
                                item.course?.lessons_count || 0
                            } pelajaran</span>
                        </div>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3 mb-2">
                        <div class="bg-gradient-to-r from-blue-500 to-purple-600 h-3 rounded-full transition-all duration-300" style="width: ${
                            item.progress_percentage || 0
                        }%"></div>
                    </div>
                    <div class="flex justify-between items-center text-sm text-gray-600">
                        <span>${item.progress_percentage || 0}% selesai</span>
                        <span>Terakhir diakses: ${formatDate(
                            item.last_accessed_at
                        )}</span>
                    </div>
                </div>
            `;
        }

        container.appendChild(card);
    });
}

function updateMyCoursesStats(data) {
    if (userLevelName === "Instructor") {
        const courses = data.myCourses || [];
        const totalCourses = courses.length;
        const totalStudents = courses.reduce(
            (sum, course) => sum + (course.enrollments_count || 0),
            0
        );
        const totalRevenue = courses.reduce(
            (sum, course) =>
                sum + (course.price || 0) * (course.enrollments_count || 0),
            0
        );
        const averageRating =
            courses.length > 0
                ? courses.reduce(
                      (sum, course) => sum + (course.rating_avg || 0),
                      0
                  ) / courses.length
                : 0;

        document.getElementById("totalCourses").textContent = totalCourses;
        document.getElementById("totalStudents").textContent = totalStudents;
        document.getElementById("totalRevenue").textContent =
            "$" + totalRevenue.toFixed(2);
        document.getElementById("averageRating").textContent =
            averageRating.toFixed(1);
    } else {
        const courses = data.myEnrolledCourses || [];
        const enrolledCourses = courses.length;
        const completedCourses = courses.filter(
            (course) => course.progress_percentage >= 100
        ).length;
        const totalProgress = courses.reduce(
            (sum, course) => sum + (course.progress_percentage || 0),
            0
        );
        const averageProgress =
            courses.length > 0 ? totalProgress / courses.length : 0;

        document.getElementById("enrolledCourses").textContent =
            enrolledCourses;
        document.getElementById("completedCourses").textContent =
            completedCourses;
        document.getElementById("averageProgress").textContent =
            averageProgress.toFixed(1) + "%";
    }
}

function formatDate(dateString) {
    if (!dateString) return "Belum pernah";
    return new Date(dateString).toLocaleDateString("id-ID", {
        day: "2-digit",
        month: "short",
        year: "numeric",
    });
}

async function editCourse(id) {
    // Implement edit course functionality
    console.log("Edit course:", id);
    // Redirect to edit page or open modal
}

async function manageCourse(id) {
    // Implement manage course functionality
    console.log("Manage course:", id);
    // Redirect to course management page
}

function showError(message) {
    const errorDiv = document.createElement("div");
    errorDiv.className =
        "fixed top-4 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded";
    errorDiv.innerHTML = `<span>${message}</span><button onclick="this.parentElement.remove()" class="ml-4 text-red-700">x</button>`;
    document.body.appendChild(errorDiv);
    setTimeout(() => errorDiv.remove(), 5000);
}

document.addEventListener("DOMContentLoaded", () => {
    if (!["Instructor", "User"].includes(userLevelName)) {
        showError("Level pengguna tidak valid untuk halaman ini.");
        return;
    }
    loadMyCoursesData();
});
