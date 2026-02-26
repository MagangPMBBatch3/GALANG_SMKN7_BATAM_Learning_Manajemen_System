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

// Format number as IDR currency
function formatCurrencyIDR(amount) {
    try {
        if (amount === undefined || amount === null || amount === "")
            return "Rp0";
        const rounded = Math.round(Number(amount));
        return new Intl.NumberFormat("id-ID", {
            style: "currency",
            currency: "IDR",
            maximumFractionDigits: 0,
        }).format(rounded);
    } catch (e) {
        return "Rp0";
    }
}

async function loadCourseData(courseSlug) {
    try {
        const response = await fetch(`/student/api/course/${courseSlug}`, {
            headers: {
                Accept: "application/json",
                "X-CSRF-TOKEN": getCsrfToken(),
            },
        });

        if (!response.ok) {
            const err = await response.json().catch(() => ({}));
            throw new Error(err.message || "Gagal mengambil data kursus");
        }

        const data = await response.json();
        // Normalize shape to previous GraphQL-based renderers
        const payload = {
            course: Object.assign({}, data.course, {
                thumbnail_url:
                    data.course.thumbnail_url || data.course.thumbnail || null,
                long_description:
                    data.course.full_description ||
                    data.course.long_description ||
                    "",
            }),
            userEnrollment: data.enrollment || null,
            courseReviews: data.reviews || [],
        };

        renderCourseDetails(payload);
        renderLessons(payload);
        renderReviews(payload);
        updateEnrollmentStatus(payload);
    } catch (error) {
        console.error("Error loading course data:", error);
        showError("Gagal memuat data kursus: " + (error.message || error));
    }
}

function renderCourseDetails(data) {
    const course = data.course;
    if (!course) return;

    document.getElementById("courseTitle").textContent = course.title;
    document.getElementById("courseDescription").innerHTML =
        course.long_description;
    document.getElementById("coursePrice").textContent =
        course.price > 0 ? formatCurrencyIDR(course.price) : "Gratis";
    document.getElementById("courseRating").textContent = (
        course.rating_avg || 0
    ).toFixed(1);
    document.getElementById("courseRatingCount").textContent = `(${
        course.rating_count || 0
    } ulasan)`;
    document.getElementById("courseStudents").textContent =
        course.enrollments_count || 0;
    document.getElementById("courseLevel").textContent =
        course.level || "Semua Tingkat";
    document.getElementById("courseCategory").textContent =
        course.category?.name || "Umum";

    // Render instructor info
    const instructorContainer = document.getElementById("instructorInfo");
    instructorContainer.innerHTML = `
        <div class="flex items-center space-x-4">
            <img src="${
                course.instructor?.avatar_url ||
                "/images/avatar-placeholder.jpg"
            }" alt="${
        course.instructor?.name
    }" class="w-16 h-16 rounded-full object-cover">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">${
                    course.instructor?.name || "Instruktur"
                }</h3>
                <p class="text-gray-600 text-sm">${
                    course.instructor?.bio ||
                    "Instruktur profesional di MaxCourse"
                }</p>
            </div>
        </div>
    `;

    // Update thumbnail
    const thumbnail = document.getElementById("courseThumbnail");
    if (thumbnail) {
        thumbnail.src =
            course.thumbnail_url || "/images/course-placeholder.jpg";
        thumbnail.alt = course.title;
    }
}

function renderLessons(data) {
    const lessons = data.course?.lessons || [];
    const container = document.getElementById("lessonsContainer");
    const enrollment = data.userEnrollment;

    container.innerHTML = lessons.length
        ? '<h3 class="text-xl font-semibold mb-4">Daftar Pelajaran</h3>'
        : '<p class="text-gray-500">Belum ada pelajaran untuk kursus ini.</p>';

    lessons.forEach((lesson, index) => {
        const lessonDiv = document.createElement("div");
        lessonDiv.className = `flex items-center justify-between p-4 border border-gray-200 rounded-lg mb-2 ${
            enrollment ? "cursor-pointer hover:bg-gray-50" : "opacity-50"
        }`;
        if (enrollment) {
            // Redirect to course player (will let user select lesson there)
            lessonDiv.onclick = () =>
                (window.location.href = `/learn/${data.course.slug}`);
        }

        const duration = lesson.duration
            ? `${Math.floor(lesson.duration / 60)}:${(lesson.duration % 60)
                  .toString()
                  .padStart(2, "0")}`
            : "N/A";

        lessonDiv.innerHTML = `
            <div class="flex items-center space-x-4">
                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center text-sm font-medium text-blue-600">
                    ${index + 1}
                </div>
                <div>
                    <h4 class="font-medium text-gray-900">${lesson.title}</h4>
                    <p class="text-sm text-gray-600">Durasi: ${duration}</p>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                ${
                    enrollment
                        ? '<i class="fas fa-play-circle text-blue-600"></i>'
                        : '<i class="fas fa-lock text-gray-400"></i>'
                }
            </div>
        `;
        container.appendChild(lessonDiv);
    });
}

function renderReviews(data) {
    const reviews = data.courseReviews || [];
    const container = document.getElementById("reviewsContainer");

    container.innerHTML = reviews.length
        ? '<h3 class="text-xl font-semibold mb-4">Ulasan Siswa</h3>'
        : '<p class="text-gray-500">Belum ada ulasan untuk kursus ini.</p>';

    reviews.slice(0, 5).forEach((review) => {
        const reviewDiv = document.createElement("div");
        reviewDiv.className = "bg-gray-50 p-4 rounded-lg mb-4";
        reviewDiv.innerHTML = `
            <div class="flex items-center space-x-3 mb-3">
                <img src="${
                    review.user?.avatar_url || "/images/avatar-placeholder.jpg"
                }" alt="${
            review.user?.name
        }" class="w-10 h-10 rounded-full object-cover">
                <div>
                    <p class="font-medium text-gray-900">${
                        review.user?.name || "Anonim"
                    }</p>
                    <div class="flex items-center">
                        ${renderStars(review.rating)}
                        <span class="text-sm text-gray-600 ml-2">${formatDate(
                            review.created_at
                        )}</span>
                    </div>
                </div>
            </div>
            <p class="text-gray-700">${review.comment}</p>
        `;
        container.appendChild(reviewDiv);
    });
}

function renderStars(rating) {
    let stars = "";
    for (let i = 1; i <= 5; i++) {
        stars += `<i class="fas fa-star ${
            i <= rating ? "text-yellow-400" : "text-gray-300"
        }"></i>`;
    }
    return stars;
}

function updateEnrollmentStatus(data) {
    const enrollment = data.userEnrollment;
    const enrollBtn = document.getElementById("enrollButton");
    const progressContainer = document.getElementById("progressContainer");

    if (enrollment) {
        enrollBtn.textContent = "Lanjutkan Belajar";
        enrollBtn.className =
            "w-full bg-green-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-green-700 transition-colors";
        enrollBtn.onclick = () =>
            (window.location.href = `/learn/${data.course.slug}`);

        const progressPercent =
            enrollment.progress_percent ?? enrollment.progress_percentage ?? 0;

        if (progressContainer) {
            progressContainer.innerHTML = `
                <div class="bg-white p-4 rounded-lg shadow">
                    <h4 class="font-semibold mb-2">Progress Anda</h4>
                    <div class="w-full bg-gray-200 rounded-full h-3 mb-2">
                        <div class="bg-blue-600 h-3 rounded-full" style="width: ${progressPercent}%"></div>
                    </div>
                    <p class="text-sm text-gray-600">${progressPercent}% selesai</p>
                </div>
            `;
        }
    } else {
        const priceLabel =
            data.course.price > 0
                ? `Daftar - ${formatCurrencyIDR(data.course.price)}`
                : "Daftar Gratis";
        enrollBtn.textContent = priceLabel;
        enrollBtn.onclick = () => {
            if (data.course.price > 0) {
                showPaymentModal(data.course);
            } else {
                enrollInCourse(data.course.id);
            }
        };
        if (progressContainer) progressContainer.style.display = "none";
    }
}

async function enrollInCourse(courseId, payment) {
    try {
        const payload = { course_id: courseId };
        if (payment) payload.payment = payment;
        const response = await fetch("/student/api/enroll", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                Accept: "application/json",
                "X-CSRF-TOKEN": getCsrfToken(),
            },
            body: JSON.stringify(payload),
        });

        if (!response.ok) {
            const err = await response.json().catch(() => ({}));
            throw new Error(err.message || "Gagal mendaftar kursus");
        }

        const data = await response.json();
        showSuccess("Berhasil mendaftar kursus!");
        // After enroll, redirect to course player
        setTimeout(() => {
            const slug = document
                .querySelector('meta[name="course-slug"]')
                ?.getAttribute("content");
            if (slug) {
                window.location.href = `/learn/${slug}`;
            } else {
                location.reload();
            }
        }, 800);
    } catch (error) {
        console.error("Error enrolling in course:", error);
        showError("Gagal mendaftar kursus: " + (error.message || error));
    }
}

// Show simple fake payment modal (demo)
function showPaymentModal(course) {
    let modal = document.getElementById("paymentModal");
    if (!modal) {
        modal = document.createElement("div");
        modal.id = "paymentModal";
        modal.className = "fixed inset-0 z-50 flex items-center justify-center";
        modal.innerHTML = `
            <div class="absolute inset-0 bg-black/50"></div>
            <div class="bg-white dark:bg-slate-800 rounded-lg shadow-lg z-10 w-full max-w-md p-6">
                <h3 class="text-xl font-semibold mb-4">Pembayaran (Demo)</h3>
                <p class="text-sm text-gray-600 mb-4">Gunakan kartu uji apa saja. Ini hanya simulasi pembayaran.</p>
                <div class="mb-3">
                    <label class="text-sm">Nama</label>
                    <input id="pm_name" class="w-full px-3 py-2 border rounded mt-1" placeholder="Nama di kartu (Demo)">
                </div>
                <div class="mb-3">
                    <label class="text-sm">Nomor Kartu</label>
                    <input id="pm_number" class="w-full px-3 py-2 border rounded mt-1" placeholder="4242 4242 4242 4242">
                </div>
                <div class="flex gap-3 mb-4">
                    <div class="flex-1">
                        <label class="text-sm">Exp</label>
                        <input id="pm_exp" class="w-full px-3 py-2 border rounded mt-1" placeholder="12/34">
                    </div>
                    <div class="w-24">
                        <label class="text-sm">CVC</label>
                        <input id="pm_cvc" class="w-full px-3 py-2 border rounded mt-1" placeholder="123">
                    </div>
                </div>
                <div class="flex items-center justify-between mb-4">
                    <div class="text-sm">Total:</div>
                    <div class="font-semibold" id="pm_amount">${formatCurrencyIDR(
                        course.price
                    )}</div>
                </div>
                <div class="flex gap-3">
                    <button id="pm_cancel" class="flex-1 px-4 py-2 rounded bg-gray-200">Batal</button>
                    <button id="pm_pay" class="flex-1 px-4 py-2 rounded bg-blue-600 text-white">Bayar</button>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
        modal
            .querySelector("#pm_cancel")
            .addEventListener("click", () => modal.remove());
        modal.querySelector("#pm_pay").addEventListener("click", async () => {
            const name =
                document.getElementById("pm_name").value || "Demo User";
            const number =
                document.getElementById("pm_number").value ||
                "4242424242424242";
            const last4 = number.replace(/\D/g, "").slice(-4);
            const amount = course.price || 0;
            const btn = modal.querySelector("#pm_pay");
            btn.disabled = true;
            btn.textContent = "Memproses...";
            setTimeout(async () => {
                try {
                    await enrollInCourse(course.id, {
                        method: "fake_card",
                        amount: amount,
                        currency: "IDR",
                        card_last4: last4,
                        name: name,
                    });
                    modal.remove();
                } catch (e) {
                    showError("Pembayaran gagal: " + (e.message || e));
                    btn.disabled = false;
                    btn.textContent = "Bayar";
                }
            }, 800);
        });
    } else {
        const amt = modal.querySelector("#pm_amount");
        if (amt) amt.textContent = formatCurrencyIDR(course.price);
        document.body.appendChild(modal);
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

// Initialize course data when page loads
document.addEventListener("DOMContentLoaded", () => {
    const courseSlug = document
        .querySelector('meta[name="course-slug"]')
        ?.getAttribute("content");
    if (courseSlug) {
        loadCourseData(courseSlug);
    }
});
