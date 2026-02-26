let currentPage = 1;
let currentFilters = {};
const ITEMS_PER_PAGE = 10;

const userProfileId = document
    .querySelector('meta[name="user-profile-id"]')
    ?.getAttribute("content");
const userLevelName =
    document
        .querySelector('meta[name="user-level-name"]')
        ?.getAttribute("content") || "User";

function translateLevel(level) {
    const levels = {
        'beginner': 'Pemula',
        'intermediate': 'Menengah',
        'advanced': 'Lanjutan',
        'all': 'Semua Tingkat'
    };
    return levels[level?.toLowerCase()] || level;
}

function getCsrfToken() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    if (!meta) {
        throw new Error(
            'CSRF token not found. Please ensure <meta name="csrf-token"> is included in the HTML.'
        );
    }
    return meta.getAttribute("content");
}

async function loadCoursesData(page = 1, filters = {}) {
    try {
        const query = buildQuery(page, filters);
        const response = await fetch("/graphql", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": getCsrfToken(),
            },
            body: JSON.stringify({ query }),
        });

        const result = await response.json();
        console.log("GraphQL response (loadCoursesData):", result);
        if (result.errors) {
            throw new Error(result.errors.map((e) => e.message).join(", "));
        }

        if (result.data) {
            let data =
                userLevelName === "Admin"
                    ? result.data.allCourses?.data || []
                    : result.data.courses?.data || [];
            let paginatorInfo =
                userLevelName === "Admin"
                    ? result.data.allCourses?.paginatorInfo
                    : result.data.courses?.paginatorInfo;
            data = applyClientSideFilters(data, filters);
            renderCourseCards(data);
            updateSummary(data);
            updatePagination(paginatorInfo.total, page);
        }
    } catch (error) {
        console.error("Error loading courses data:", error);
        showError("Gagal memuat data kursus: " + error.message);
    }
}

function applyClientSideFilters(data, filters) {
    return data.filter((item) => {
        let matches = true;
        if (filters.category && item.category?.name !== filters.category) {
            matches = false;
        }
        if (filters.level && item.level !== filters.level) {
            matches = false;
        }
        if (
            filters.search &&
            !item.title.toLowerCase().includes(filters.search.toLowerCase())
        ) {
            matches = false;
        }
        return matches;
    });
}

function buildQuery(page, filters) {
    const first = ITEMS_PER_PAGE;
    const pageNum = page;

    if (userLevelName === "Admin") {
        return `
            query {
                allCourses(first: ${first}, page: ${pageNum}) {
                    data {
                        id
                        title
                        slug
                        short_description
                        price
                        thumbnail_url
                        rating_avg
                        rating_count
                        level
                        category {
                            name
                        }
                        instructor {
                            name
                        }
                        enrollments {
                            count
                        }
                        created_at
                    }
                    paginatorInfo {
                        total
                        currentPage
                        lastPage
                    }
                }
            }
        `;
    } else {
        return `
            query {
                courses(first: ${first}, page: ${pageNum}) {
                    data {
                        id
                        title
                        slug
                        short_description
                        price
                        thumbnail_url
                        rating_avg
                        rating_count
                        level
                        category {
                            name
                        }
                        instructor {
                            name
                        }
                        enrollments_count
                        created_at
                    }
                    paginatorInfo {
                        total
                        currentPage
                        lastPage
                    }
                }
            }
        `;
    }
}

function renderCourseCards(data) {
    const container = document.getElementById("courseCardsContainer");
    const isAdmin = userLevelName === "Admin";
    const isInstructor = userLevelName === "Instructor";
    container.innerHTML = data.length
        ? ""
        : `
        <div class="col-span-full text-center py-8">
            <div class="text-gray-500">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada kursus tersedia</h3>
                <p class="mt-1 text-sm text-gray-500">Belum ada kursus yang tersedia.</p>
            </div>
        </div>
    `;

    data.forEach((item) => {
        const card = document.createElement("div");
        card.className =
            "bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-all duration-200 cursor-pointer";
        card.onclick = () => (window.location.href = `/courses/${item.slug}`);
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
                        translateLevel(item.level) || "Semua Tingkat"
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
                        <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                            <span class="text-white text-xs font-bold">${
                                item.instructor?.name
                                    ?.charAt(0)
                                    ?.toUpperCase() || "I"
                            }</span>
                        </div>
                        <span class="text-sm text-gray-700">${
                            item.instructor?.name || "Instruktur"
                        }</span>
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
                ${
                    isAdmin || isInstructor
                        ? `
                    <div class="mt-4 pt-4 border-t border-gray-200 flex justify-between items-center">
                        <span class="text-xs text-gray-500">${
                            item.enrollments?.count || 0
                        } siswa terdaftar</span>
                        <div class="flex space-x-2">
                            ${
                                isAdmin
                                    ? `<button onclick="event.stopPropagation(); editCourse(${item.id})" class="text-blue-600 hover:text-blue-800 text-sm">Edit</button>`
                                    : ""
                            }
                            ${
                                isAdmin
                                    ? `<button onclick="event.stopPropagation(); deleteCourse(${item.id})" class="text-red-600 hover:text-red-800 text-sm">Hapus</button>`
                                    : ""
                            }
                        </div>
                    </div>
                `
                        : ""
                }
            </div>
        `;
        container.appendChild(card);
    });
}

function updateSummary(data) {
    const totalCourses = data.length;
    const totalEnrollments = data.reduce(
        (sum, item) => sum + (item.enrollments_count || 0),
        0
    );
    const averageRating =
        data.length > 0
            ? data.reduce((sum, item) => sum + (item.rating_avg || 0), 0) /
              data.length
            : 0;

    document.getElementById("totalCourses").textContent = totalCourses;
    document.getElementById("totalEnrollments").textContent = totalEnrollments;
    document.getElementById("averageRating").textContent =
        averageRating.toFixed(1);
}

function updatePagination(total, page) {
    const info = document.getElementById("paginationInfo");
    const links = document.getElementById("paginationLinks");
    const totalPages = Math.ceil(total / ITEMS_PER_PAGE);
    info.textContent = `Menampilkan ${Math.min(
        total,
        ITEMS_PER_PAGE
    )} dari ${total} kursus (Halaman ${page})`;

    const prevDisabled =
        page === 1
            ? "disabled opacity-50 cursor-not-allowed"
            : "hover:bg-gray-300";
    const nextDisabled =
        page >= totalPages
            ? "disabled opacity-50 cursor-not-allowed"
            : "hover:bg-gray-300";

    links.innerHTML = `
        <button onclick="changePage(${page - 1})" ${
        page === 1 ? "disabled" : ""
    } class="flex items-center justify-center w-8 h-8 sm:w-10 sm:h-10 bg-gray-200 rounded-full transition-colors ${prevDisabled}" title="Halaman Sebelumnya">
            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </button>
        <span class="flex items-center justify-center w-8 h-8 sm:w-10 sm:h-10 bg-blue-500 text-white rounded-full text-xs sm:text-sm font-medium">${page}</span>
        <button onclick="changePage(${page + 1})" ${
        page >= totalPages ? "disabled" : ""
    } class="flex items-center justify-center w-8 h-8 sm:w-10 sm:h-10 bg-gray-200 rounded-full transition-colors ${nextDisabled}" title="Halaman Berikutnya">
            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
        </button>
    `;
}

function changePage(page) {
    if (page < 1) return;
    currentPage = page;
    loadCoursesData(currentPage, currentFilters);
}

function filterCourses() {
    const category = document.getElementById("filterCategory").value;
    const level = document.getElementById("filterLevel").value;
    const search = document.getElementById("searchInput").value;
    currentFilters = {
        category: category || null,
        level: level || null,
        search: search || null,
    };
    console.log("Applying filters:", currentFilters);
    currentPage = 1;
    loadCoursesData(currentPage, currentFilters);
}

async function editCourse(id) {
    // Implement edit course functionality
    console.log("Edit course:", id);
    // Redirect to edit page or open modal
}

async function deleteCourse(id) {
    if (!confirm("Apakah Anda yakin ingin menghapus kursus ini?")) return;

    try {
        const mutation = `mutation { deleteCourse(id: ${id}) { id } }`;
        const response = await fetch("/graphql", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": getCsrfToken(),
            },
            body: JSON.stringify({ query: mutation }),
        });

        const result = await response.json();
        console.log("GraphQL response (deleteCourse):", result);
        if (result.errors) {
            throw new Error(result.errors.map((e) => e.message).join(", "));
        }

        if (result.data) {
            loadCoursesData(currentPage, currentFilters);
        }
    } catch (error) {
        console.error("Error deleting course:", error);
        showError("Gagal menghapus kursus: " + error.message);
    }
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
    if (!["Admin", "Instructor", "User"].includes(userLevelName)) {
        showError(
            "Level pengguna tidak valid. Harus admin, instructor, atau user."
        );
        return;
    }
    loadCoursesData();
});
