

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-900 dark:to-slate-800">
    <!-- Header -->
    <div class="bg-white dark:bg-slate-800 shadow-sm border-b border-slate-200 dark:border-slate-700">
        <div class="max-w-7xl mx-auto px-4 py-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-slate-900 dark:text-white">Jelajahi Kursus</h1>
                    <p class="text-slate-600 dark:text-slate-400 mt-1">Temukan dan ikuti kursus untuk meningkatkan keahlian Anda</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div id="courseExplorer" x-data="courseExplorer()" x-init="init()" x-cloak class="max-w-7xl mx-auto px-4 py-8">
        <!-- Filters -->
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Search -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Cari</label>
                    <input 
                        type="text" 
                        x-model="search"
                        @input="debouncedSearch()"
                        placeholder="Judul kursus atau deskripsi..."
                        class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                </div>

                <!-- Category Filter -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Kategori</label>
                    <select 
                        x-model="categoryFilter"
                        @change="loadCourses()"
                        class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        <option value="">Semua Kategori</option>
                        <template x-for="category in categories" :key="category.id">
                            <option :value="category.id" x-text="category.name"></option>
                        </template>
                    </select>
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Status</label>
                    <select 
                        x-model="statusFilter"
                        @change="loadCourses()"
                        class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        <option value="">Semua Status</option>
                        <option value="published">Dipublikasikan</option>
                        <option value="draft">Draft</option>
                    </select>
                </div>

                <!-- Sort -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Urutkan</label>
                    <select 
                        x-model="sortBy"
                        @change="loadCourses()"
                        class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        <option value="newest">Terbaru</option>
                        <option value="popular">Terpopuler</option>
                        <option value="rating">Rating Tertinggi</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Courses Grid -->
        <template x-if="loading">
            <div class="text-center py-12">
                <div class="inline-block">
                    <svg class="animate-spin h-8 w-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
            </div>
        </template>

        <template x-if="!loading && courses.length === 0">
            <div class="text-center py-12">
                <svg class="w-12 h-12 text-slate-300 dark:text-slate-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m0 0h6"></path>
                </svg>
                <p class="text-slate-500 dark:text-slate-400 mb-4">Tidak ada kursus yang sesuai dengan kriteria Anda.</p>
            </div>
        </template>

        <template x-if="!loading && courses.length > 0">
            <div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                    <template x-for="course in courses" :key="course.id">
                        <div class="bg-white dark:bg-slate-800 rounded-lg shadow hover:shadow-lg transition overflow-hidden">
                            <!-- Thumbnail -->
                            <div class="w-full h-40 bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white text-4xl font-bold overflow-hidden relative">
                                <template x-if="course.thumbnail_url">
                                    <img :src="course.thumbnail_url ? '/storage/' + course.thumbnail_url : ''" class="absolute inset-0 w-full h-full object-cover">
                                </template>
                                <template x-if="!course.thumbnail_url">
                                    <span x-text="course.title.charAt(0)"></span>
                                </template>
                            </div>

                            <!-- Content -->
                            <div class="p-5">
                                <!-- Category Badge -->
                                <div class="mb-3">
                                    <span class="inline-block px-2 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 text-xs font-semibold rounded">
                                        <span x-text="course.category?.name || 'Tanpa Kategori'"></span>
                                    </span>
                                </div>

                                <!-- Title -->
                                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2 line-clamp-2" x-text="course.title"></h3>

                                <!-- Description -->
                                <p class="text-sm text-slate-600 dark:text-slate-400 line-clamp-2 mb-4" x-text="course.short_description"></p>

                                <!-- Instructor -->
                                <p class="text-xs text-slate-500 dark:text-slate-500 mb-3">
                                    oleh <span class="font-medium" x-text="course.instructor?.name || 'Tidak Diketahui'"></span>
                                </p>

                                <!-- Stats -->
                                <div class="flex items-center justify-between text-xs text-slate-600 dark:text-slate-400 mb-4">
                                    <span><span x-text="course.modules_count || 0"></span> modul</span>
                                    <span><span x-text="course.lessons_count || 0"></span> pelajaran</span>
                                    <span><span x-text="course.enrollments_count || 0"></span> siswa</span>
                                </div>

                                <!-- Price & Button -->
                                <div class="flex items-center justify-between pt-4 border-t border-slate-200 dark:border-slate-700">
                                    <div>
                                        <template x-if="course.price > 0">
                                            <p class="text-lg font-bold text-slate-900 dark:text-white">
                                                <span x-text="new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(course.price)"></span>
                                            </p>
                                        </template>
                                        <template x-if="course.price === 0">
                                            <p class="text-lg font-bold text-green-600 dark:text-green-400">Gratis</p>
                                        </template>
                                    </div>
                                    <button 
                                        @click="handleEnrollClick(course)"
                                        :disabled="isEnrolled(course.id)"
                                        class="px-4 py-2 rounded-lg font-medium text-sm transition"
                                        :class="isEnrolled(course.id) 
                                            ? 'bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-400 cursor-not-allowed' 
                                            : 'bg-blue-600 hover:bg-blue-700 text-white'"
                                    >
                                        <span x-text="isEnrolled(course.id) ? 'Terdaftar' : 'Daftar'"></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Pagination -->
                <template x-if="totalPages > 1">
                    <div class="flex items-center justify-center gap-2">
                        <button 
                            @click="previousPage()"
                            :disabled="currentPage === 0"
                            class="px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-600 text-slate-900 dark:text-white disabled:opacity-50 disabled:cursor-not-allowed hover:bg-slate-100 dark:hover:bg-slate-700"
                        >
                            Sebelumnya
                        </button>
                        <div class="text-sm text-slate-600 dark:text-slate-400">
                            Hal <span x-text="currentPage + 1"></span> dari <span x-text="totalPages"></span>
                        </div>
                        <button 
                            @click="nextPage()"
                            :disabled="currentPage + 1 >= totalPages"
                            class="px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-600 text-slate-900 dark:text-white disabled:opacity-50 disabled:cursor-not-allowed hover:bg-slate-100 dark:hover:bg-slate-700"
                        >
                            Selanjutnya
                        </button>
                    </div>
                </template>
            </div>
        </template>

    <!-- Payment Modal -->
    <div x-show="isPaymentModalVisible" x-cloak class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[100]" @click.self="closePaymentModal()">
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow-xl p-8 w-full max-w-md">
            <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-2">Selesaikan Pembayaran</h3>
            <p class="text-slate-600 dark:text-slate-400 mb-6">
                Daftar di <strong x-text="selectedCourse?.title"></strong>
            </p>

            <!-- Price Info -->
            <div class="bg-slate-100 dark:bg-slate-700 p-4 rounded-lg mb-6">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-slate-600 dark:text-slate-400">Harga Kursus:</span>
                    <span class="font-bold text-slate-900 dark:text-white" x-text="selectedCourse?.price ? new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(selectedCourse.price) : 'Gratis'"></span>
                </div>
                <div class="border-t border-slate-300 dark:border-slate-600 pt-2">
                    <div class="flex justify-between items-center">
                        <span class="font-semibold text-slate-900 dark:text-white">Total:</span>
                        <span class="text-lg font-bold text-blue-600 dark:text-blue-400" x-text="selectedCourse?.price ? new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(selectedCourse.price) : 'Gratis'"></span>
                    </div>
                </div>
            </div>

            <!-- Payment Form -->
            <div class="space-y-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Metode Pembayaran</label>
                    <select 
                        x-model="paymentForm.paymentMethod"
                        class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        <option value="credit_card">Kartu Kredit</option>
                        <option value="debit_card">Kartu Debit</option>
                        <option value="e_wallet">E-Wallet</option>
                        <option value="bank_transfer">Transfer Bank</option>
                        <option value="paypal">PayPal</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Nama Lengkap</label>
                    <input 
                        type="text" 
                        x-model="paymentForm.fullName"
                        placeholder="John Doe"
                        class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Email</label>
                    <input 
                        type="email" 
                        x-model="paymentForm.email"
                        placeholder="john@example.com"
                        class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Nomor Kartu</label>
                    <input 
                        type="text" 
                        x-model="paymentForm.cardNumber"
                        placeholder="4111 1111 1111 1111"
                        class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Demo: 4111 1111 1111 1111 (Ini adalah data simulasi untuk demo)</p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Kedaluwarsa</label>
                        <input 
                            type="text" 
                            x-model="paymentForm.expiry"
                            placeholder="MM/YY"
                            class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">CVV</label>
                        <input 
                            type="text" 
                            x-model="paymentForm.cvv"
                            placeholder="123"
                            class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        />
                    </div>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex gap-3">
                <button 
                    @click="closePaymentModal()"
                    class="flex-1 px-4 py-2 border border-slate-300 dark:border-slate-600 text-slate-900 dark:text-white rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition"
                >
                    Batal
                </button>
                <button 
                    @click="processPayment()"
                    :disabled="paymentLoading"
                    class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                >
                    <span x-show="!paymentLoading">Bayar Sekarang</span>
                    <svg x-show="paymentLoading" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
(function() {
    const courseExplorerFactory = () => ({
        courses: [],
        categories: [],
        loading: false,
        currentPage: 0,
        perPage: 9,
        totalPages: 0,
        search: '',
        categoryFilter: '',
        statusFilter: '',
        sortBy: 'newest',
        enrolledCourseIds: [],
        searchTimeout: null,
        isPaymentModalVisible: false,
        selectedCourse: null,
        paymentLoading: false,
        paymentForm: {
            fullName: '',
            email: '',
            paymentMethod: 'credit_card',
            cardNumber: '',
            expiry: '',
            cvv: ''
        },

        async init() {
            this.isPaymentModalVisible = false;
            await this.loadCategories();
            await this.loadEnrolledCourses();
            await this.loadCourses();
        },

        async loadCategories() {
            try {
                const response = await fetch('/student/api/categories', {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    }
                });
                if (response.ok) {
                    this.categories = await response.json();
                }
            } catch (error) {
                console.error('[courseExplorer] Error loading categories:', error);
            }
        },

        async loadEnrolledCourses() {
            try {
                const response = await fetch('/student/api/enrollments', {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    }
                });
                if (response.ok) {
                    const data = await response.json();
                    this.enrolledCourseIds = data.map(e => e.course.id);
                } else {
                    console.error('Error loading enrolled courses:', response.status);
                }
            } catch (error) {
                console.error('Error loading enrolled courses:', error);
            }
        },

        async loadCourses() {
            this.loading = true;
            try {
                const params = new URLSearchParams({
                    per_page: this.perPage,
                    page: this.currentPage + 1,
                });

                if (this.search) params.append('search', this.search);
                if (this.categoryFilter) params.append('category_id', this.categoryFilter);
                if (this.statusFilter) params.append('status', this.statusFilter);
                params.append('sort', this.sortBy);

                const response = await fetch(`/student/api/courses?${params}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    this.courses = data.data || [];
                    this.totalPages = data.last_page || 1;
                }
            } catch (error) {
                console.error('[courseExplorer] Error loading courses:', error);
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
            if (this.isEnrolled(course.id)) {
                return;
            }

            if (!course.price || course.price === 0) {
                this.enrollCourse(course, null);
            } else {
                this.selectedCourse = course;
                this.isPaymentModalVisible = true;
                this.resetPaymentForm();
            }
        },

        resetPaymentForm() {
            this.paymentForm = {
                fullName: '',
                email: '',
                paymentMethod: 'credit_card',
                cardNumber: '',
                expiry: '',
                cvv: ''
            };
        },

        closePaymentModal() {
            this.isPaymentModalVisible = false;
            this.selectedCourse = null;
            this.resetPaymentForm();
        },

        async processPayment() {
            const form = this.paymentForm;

            if (!form.fullName || !form.email || !form.cardNumber || !form.expiry || !form.cvv) {
                alert('Mohon isi semua detail pembayaran');
                return;
            }

            const paymentData = {
                amount: this.selectedCourse.price,
                currency: 'IDR',
                method: form.paymentMethod,
                cardNumber: form.cardNumber,
                cardHolder: form.fullName,
                cardExpiry: form.expiry,
                cardCvv: form.cvv,
                email: form.email,
                transaction_ref: 'TRX' + Date.now()
            };

            await this.enrollCourse(this.selectedCourse, paymentData);
        },

        async enrollCourse(course, paymentData) {
            if (this.isEnrolled(course.id)) {
                return;
            }

            this.paymentLoading = true;
            try {
                const body = {
                    course_id: course.id,
                };

                if (paymentData) {
                    body.payment = paymentData;
                }

                const response = await fetch('/student/api/enroll', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    },
                    body: JSON.stringify(body)
                });

                if (response.ok) {
                    const data = await response.json();
                    this.enrolledCourseIds.push(course.id);
                    this.closePaymentModal();
                    alert('Berhasil mendaftar di ' + course.title + '!');
                    setTimeout(() => {
                        window.location.href = '/learn/' + course.slug;
                    }, 800);
                } else {
                    const error = await response.json();
                    alert('Error: ' + (error.message || 'Gagal mendaftar'));
                }
            } catch (error) {
                console.error('Error enrolling:', error);
                alert('Terjadi kesalahan saat mendaftar');
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
        }
    });

    // Register component using the main layout's registration function
    if (typeof registerAlpineComponent === 'function') {
        registerAlpineComponent('courseExplorer', courseExplorerFactory);
    } else {
        // Fallback: direct Alpine registration if layout function not available
        const registerComponent = () => {
            if (typeof Alpine === 'undefined' || !Alpine.data) {
                setTimeout(registerComponent, 50);
                return;
            }
            Alpine.data('courseExplorer', courseExplorerFactory);
        };
        registerComponent();
    }
})();
</script>
<?php $__env->stopPush(); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\maxcourse\resources\views\student\courses\index.blade.php ENDPATH**/ ?>