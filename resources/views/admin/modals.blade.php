<div x-show="showCreateUserModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" x-cloak>
    <div class="bg-white rounded-lg max-w-md w-full mx-4">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Buat Pengguna Baru</h3>
        </div>
        <div class="p-6">
            <form @submit.prevent="createUser({
                name: $refs.createUserName.value,
                email: $refs.createUserEmail.value,
                username: $refs.createUserUsername.value,
                password: $refs.createUserPassword.value,
                role_ids: [$refs.createUserRole.value]
            })">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nama</label>
                        <input x-ref="createUserName" type="text" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <input x-ref="createUserEmail" type="email" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nama Pengguna</label>
                        <input x-ref="createUserUsername" type="text" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Kata Sandi</label>
                        <input x-ref="createUserPassword" type="password" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Peran</label>
                        <select x-ref="createUserRole" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Pilih Peran</option>
                            <template x-for="role in roles" :key="role.id">
                                <option :value="role.id" x-text="role.display_name || role.name"></option>
                            </template>
                        </select>
                    </div>
                </div>
                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" @click="showCreateUserModal = false" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Buat Pengguna</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div x-show="showEditUserModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" x-cloak>
    <div class="bg-white rounded-lg max-w-md w-full mx-4">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Edit Pengguna</h3>
        </div>
        <div class="p-6">
            <form @submit.prevent="updateUser(editingUser.id, { name: editingUser.name, email: editingUser.email, username: editingUser.username, avatar_url: editingUser.avatar_url, is_active: editingUser.is_active })">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nama</label>
                        <input x-model="editingUser.name" type="text" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <input x-model="editingUser.email" type="email" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nama Pengguna</label>
                        <input x-model="editingUser.username" type="text" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">URL Avatar</label>
                        <input x-model="editingUser.avatar_url" type="url" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="flex items-center">
                            <input x-model="editingUser.is_active" type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-700">Aktif</span>
                        </label>
                    </div>
                </div>
                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" @click="showEditUserModal = false" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Perbarui Pengguna</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div x-show="showCreateCourseModal" class="fixed inset-0 bg-black bg-opacity-60 backdrop-blur-sm flex items-center justify-center z-50 transition-opacity" @click.self="showCreateCourseModal = false" x-cloak>
    <div class="bg-white dark:bg-slate-900 rounded-xl max-w-3xl w-full mx-4 max-h-[90vh] overflow-y-auto shadow-2xl transform transition-all">
        <div class="px-8 py-6 border-b border-gray-100 dark:border-gray-700 sticky top-0 bg-white dark:bg-slate-900 z-10 flex justify-between items-center">
            <div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white">Buat Kursus Baru</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Isi detail untuk meluncurkan perjalanan belajar baru.</p>
            </div>
            <button @click="showCreateCourseModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <div class="p-8">
            <form @submit.prevent="saveCourse()">
                <div class="mb-8">
                    <h4 class="text-sm uppercase tracking-wide text-blue-600 font-bold mb-4 border-b border-blue-100 pb-2">Informasi Dasar</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Judul Kursus *</label>
                            <input x-model="courseForm.title" type="text" required placeholder="Contoh: Menguasai Laravel 10: Dari Nol ke Ahli" 
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 transition-shadow shadow-sm py-2.5 px-3 dark:bg-slate-800 dark:border-slate-700 dark:text-white">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Kategori *</label>
                            <div class="relative">
                                <select x-model="courseForm.category_id" required 
                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm py-2.5 px-3 appearance-none dark:bg-slate-800 dark:border-slate-700 dark:text-white">
                                    <option value="">Pilih Kategori</option>
                                    <template x-for="category in categories" :key="category.id">
                                        <option :value="category.id" x-text="category.name"></option>
                                    </template>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500">
                                    <i class="fas fa-chevron-down text-xs"></i>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Tingkat Kesulitan</label>
                            <div class="relative">
                                <select x-model="courseForm.level" 
                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm py-2.5 px-3 appearance-none dark:bg-slate-800 dark:border-slate-700 dark:text-white">
                                    <option value="beginner">Pemula</option>
                                    <option value="intermediate">Menengah</option>
                                    <option value="advanced">Lanjutan</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500">
                                    <i class="fas fa-signal text-xs"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-8">
                    <h4 class="text-sm uppercase tracking-wide text-blue-600 font-bold mb-4 border-b border-blue-100 pb-2">Detail Kursus</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Harga (IDR)</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm font-bold">Rp</span>
                                </div>
                                <input x-model.number="courseForm.price" type="number" step="1" min="0" placeholder="0"
                                    class="w-full pl-10 rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm py-2.5 px-3 dark:bg-slate-800 dark:border-slate-700 dark:text-white">
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Set ke 0 untuk kursus gratis.</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Total Durasi</label>
                            <div class="relative">
                                <input x-model.number="courseForm.duration_minutes" type="number" step="1" min="0" placeholder="Contoh: 120"
                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm py-2.5 px-3 dark:bg-slate-800 dark:border-slate-700 dark:text-white">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-xs">Menit</span>
                                </div>
                            </div>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Deskripsi Singkat</label>
                            <textarea x-model="courseForm.short_description" rows="2" placeholder="Kalimat singkat menarik tentang kursus..."
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm py-2 px-3 dark:bg-slate-800 dark:border-slate-700 dark:text-white"></textarea>
                        </div>
                        
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Deskripsi Lengkap</label>
                            <textarea x-model="courseForm.full_description" rows="5" placeholder="Kurikulum mendetail dan hasil pembelajaran..."
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm py-2 px-3 dark:bg-slate-800 dark:border-slate-700 dark:text-white"></textarea>
                        </div>
                    </div>
                </div>

                <div class="mb-8">
                    <h4 class="text-sm uppercase tracking-wide text-blue-600 font-bold mb-4 border-b border-blue-100 pb-2">Aset & Pengaturan</h4>
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Gambar Thumbnail</label>
                            
                            <div x-data="{ thumbnailPreview: null, thumbnailName: null }" class="mt-1">
                                <div x-show="!thumbnailPreview" class="flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-blue-500 transition-colors bg-gray-50 dark:bg-slate-800 dark:border-slate-600 relative group">
                                    <input x-ref="createThumbnailUpload" type="file" accept="image/*" 
                                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                        @change="
                                            const file = $refs.createThumbnailUpload.files[0]; 
                                            thumbnailName = file ? file.name : null; 
                                            if(file) { 
                                                const reader = new FileReader(); 
                                                reader.onload = (e) => thumbnailPreview = e.target.result; 
                                                reader.readAsDataURL(file); 
                                            } else { 
                                                thumbnailPreview = null; 
                                            }
                                        ">
                                    <div class="space-y-1 text-center">
                                        <i class="fas fa-image text-gray-400 text-3xl mb-2 group-hover:text-blue-500 transition-colors"></i>
                                        <div class="flex text-sm text-gray-600 dark:text-gray-400 justify-center">
                                            <span class="text-blue-600 font-medium">Unggah file</span>
                                            <p class="pl-1">atau tarik dan lepas</p>
                                        </div>
                                        <p class="text-xs text-gray-500">PNG, JPG, GIF hingga 5MB</p>
                                    </div>
                                </div>

                                <div x-show="thumbnailPreview" class="relative rounded-lg overflow-hidden border border-gray-200 shadow-sm" style="display: none;">
                                    <img :src="thumbnailPreview" class="w-full h-48 object-cover">
                                    <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity">
                                        <button type="button" @click="thumbnailPreview = null; thumbnailName = null; $refs.createThumbnailUpload.value = ''" 
                                            class="bg-red-600 text-white px-3 py-1.5 rounded-md text-sm font-medium hover:bg-red-700 shadow-sm transform hover:scale-105 transition-all">
                                            <i class="fas fa-trash-alt mr-1"></i> Hapus
                                        </button>
                                    </div>
                                    <div class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-60 text-white text-xs py-1 px-2 truncate" x-text="thumbnailName"></div>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center">
                            <div class="flex items-center h-5">
                                <input x-model="courseForm.is_published" id="create_is_published" type="checkbox" 
                                    class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded cursor-pointer">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="create_is_published" class="font-medium text-gray-700 dark:text-gray-300 cursor-pointer">Terbitkan Segera</label>
                                <p class="text-gray-500 dark:text-gray-400">Buat kursus ini terlihat oleh siswa segera setelah dibuat.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex justify-end gap-3 pt-6 border-t border-gray-100 dark:border-gray-700">
                    <button type="button" @click="showCreateCourseModal = false" 
                        class="px-5 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors focus:ring-2 focus:ring-offset-2 focus:ring-gray-200">
                        Batal
                    </button>
                    <button type="submit" 
                        class="px-5 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all shadow-md hover:shadow-lg font-medium focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Buat Kursus
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div x-show="showEditCourseModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" @click.self="showEditCourseModal = false" x-cloak>
    <div class="bg-white rounded-lg max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="px-6 py-4 border-b border-gray-200 sticky top-0 bg-white">
            <h3 class="text-lg font-medium text-gray-900">Edit Kursus</h3>
        </div>
        <div class="p-6">
            <form @submit.prevent="saveCourse()">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Title *</label>
                        <input x-model="courseForm.title" type="text" required class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Category</label>
                        <select x-model="courseForm.category_id" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2">
                            <option value="">Select Category</option>
                            <template x-for="category in categories" :key="category.id">
                                <option :value="category.id" x-text="category.name"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Level</label>
                        <select x-model="courseForm.level" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2">
                            <option value="beginner">Beginner</option>
                            <option value="intermediate">Intermediate</option>
                            <option value="advanced">Advanced</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Price (IDR)</label>
                        <input x-model.number="courseForm.price" type="number" step="1" min="0" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Duration (minutes)</label>
                        <input x-model.number="courseForm.duration_minutes" type="number" step="1" min="0" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Short Description</label>
                        <textarea x-model="courseForm.short_description" rows="2" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2"></textarea>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Full Description</label>
                        <textarea x-model="courseForm.full_description" rows="4" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2"></textarea>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Course Thumbnail</label>
                        
                        <div x-data="{ thumbnailPreview: null, thumbnailName: null }" class="mt-1">
                            <div x-show="!thumbnailPreview && courseForm.thumbnail_url" class="mb-3 relative rounded-lg overflow-hidden border border-gray-200 shadow-sm">
                                <img :src="courseForm.thumbnail_url ? '/storage/' + courseForm.thumbnail_url : ''" class="w-full h-48 object-cover">
                                <div class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-60 text-white text-xs py-1 px-2">Current Thumbnail</div>
                            </div>

                            <div x-show="!thumbnailPreview" class="flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-blue-500 transition-colors bg-gray-50 dark:bg-slate-800 dark:border-slate-600 relative group">
                                <input x-ref="editThumbnailUpload" type="file" accept="image/*" 
                                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                    @change="
                                        const file = $refs.editThumbnailUpload.files[0]; 
                                        thumbnailName = file ? file.name : null; 
                                        if(file) { 
                                            const reader = new FileReader(); 
                                            reader.onload = (e) => thumbnailPreview = e.target.result; 
                                            reader.readAsDataURL(file); 
                                        } else { 
                                            thumbnailPreview = null; 
                                        }
                                    ">
                                <div class="space-y-1 text-center">
                                    <i class="fas fa-image text-gray-400 text-3xl mb-2 group-hover:text-blue-500 transition-colors"></i>
                                    <div class="flex text-sm text-gray-600 dark:text-gray-400 justify-center">
                                        <span class="text-blue-600 font-medium">Replace Thumbnail</span>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500">PNG, JPG, GIF up to 5MB</p>
                                </div>
                            </div>

                            <div x-show="thumbnailPreview" class="relative rounded-lg overflow-hidden border border-gray-200 shadow-sm" style="display: none;">
                                <img :src="thumbnailPreview" class="w-full h-48 object-cover">
                                <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity">
                                    <button type="button" @click="thumbnailPreview = null; thumbnailName = null; $refs.editThumbnailUpload.value = ''" 
                                        class="bg-red-600 text-white px-3 py-1.5 rounded-md text-sm font-medium hover:bg-red-700 shadow-sm transform hover:scale-105 transition-all">
                                        <i class="fas fa-trash-alt mr-1"></i> Cancel Change
                                    </button>
                                </div>
                                <div class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-60 text-white text-xs py-1 px-2 truncate" x-text="'New: ' + thumbnailName"></div>
                            </div>
                        </div>
                    </div>
                    <div class="md:col-span-2">
                        <label class="flex items-center">
                            <input x-model="courseForm.is_published" type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-700">Publish This Course</span>
                        </label>
                    </div>
                </div>
                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" @click="showEditCourseModal = false" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Perbarui Kursus</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div x-show="showCreateCategoryModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" x-cloak>
    <div class="bg-white rounded-lg max-w-md w-full mx-4">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Buat Kategori Baru</h3>
        </div>
        <div class="p-6">
            <form @submit.prevent="saveCategory()">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nama Kategori</label>
                        <input x-model="categoryForm.name" @input="if (!categoryForm.id) { categoryForm.slug = categoryForm.name.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, ''); }" type="text" required class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Slug</label>
                        <input x-model="categoryForm.slug" type="text" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2">
                        <p class="text-xs text-gray-500 mt-1">Biarkan kosong untuk generate otomatis dari nama</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
                        <textarea x-model="categoryForm.description" rows="3" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2"></textarea>
                    </div>
                </div>
                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" @click="showCreateCategoryModal = false" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Buat Kategori</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div x-show="showEditCategoryModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" x-cloak>
    <div class="bg-white rounded-lg max-w-md w-full mx-4">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Edit Kategori</h3>
        </div>
        <div class="p-6">
            <form @submit.prevent="saveCategory()">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nama Kategori</label>
                        <input x-model="categoryForm.name" type="text" required class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Slug</label>
                        <input x-model="categoryForm.slug" type="text" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
                        <textarea x-model="categoryForm.description" rows="3" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2"></textarea>
                    </div>
                </div>
                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" @click="showEditCategoryModal = false" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Perbarui Kategori</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div x-show="loading" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 flex items-center space-x-4">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
        <span class="text-gray-700">Memuat...</span>
    </div>
</div>
