<!-- Edit Enrollment Modal -->
<div x-show="showEditEnrollmentModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" @click.self="showEditEnrollmentModal = false" x-cloak>
    <div class="bg-white rounded-lg max-w-md w-full mx-4">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Edit Pendaftaran</h3>
        </div>
        <div class="p-6">
            <form @submit.prevent="updateEnrollment()">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Siswa</label>
                        <div class="mt-1 block w-full rounded-md border border-gray-300 bg-gray-50 px-3 py-2 text-gray-700" x-text="enrollmentForm.student_name || 'N/A'"></div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Kursus</label>
                        <div class="mt-1 block w-full rounded-md border border-gray-300 bg-gray-50 px-3 py-2 text-gray-700" x-text="enrollmentForm.course_title || 'N/A'"></div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status *</label>
                        <select x-model="enrollmentForm.status" required class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2">
                            <option value="active">Aktif</option>
                            <option value="completed">Selesai</option>
                            <option value="suspended">Ditangguhkan</option>
                            <option value="expired">Kadaluarsa</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Progres % (0-100)</label>
                        <input x-model.number="enrollmentForm.progress_percent" type="number" min="0" max="100" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Harga Dibayar</label>
                        <input x-model.number="enrollmentForm.price_paid" type="number" min="0" step="0.01" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Mata Uang</label>
                        <input x-model="enrollmentForm.currency" type="text" placeholder="IDR, USD, dll." class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Kadaluarsa Pada</label>
                        <input x-model="enrollmentForm.expires_at" type="datetime-local" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2">
                    </div>
                </div>
                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" @click="showEditEnrollmentModal = false" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Perbarui Pendaftaran
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Enrollment Details Modal -->
<div x-show="showEnrollmentDetailsModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" @click.self="showEnrollmentDetailsModal = false" x-cloak>
    <div class="bg-white rounded-lg max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-medium text-gray-900">Detail Pendaftaran</h3>
            <button @click="showEnrollmentDetailsModal = false" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-2 gap-6">
                <!-- Student Info -->
                <div>
                    <h4 class="text-sm font-semibold text-gray-900 mb-4">Informasi Siswa</h4>
                    <div class="space-y-3">
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wide">Nama</p>
                            <p class="text-sm font-medium text-gray-900" x-text="enrollmentDetails.user?.name || 'N/A'"></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wide">Email</p>
                            <p class="text-sm font-medium text-gray-900" x-text="enrollmentDetails.user?.email || 'N/A'"></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wide">Telepon</p>
                            <p class="text-sm font-medium text-gray-900" x-text="enrollmentDetails.user?.phone || 'N/A'"></p>
                        </div>
                    </div>
                </div>

                <!-- Course Info -->
                <!-- Course Info -->
                <div>
                    <h4 class="text-sm font-semibold text-gray-900 mb-4">Informasi Kursus</h4>
                    <div class="space-y-3">
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wide">Judul Kursus</p>
                            <p class="text-sm font-medium text-gray-900" x-text="enrollmentDetails.course?.title || 'N/A'"></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wide">Instruktur</p>
                            <p class="text-sm font-medium text-gray-900" x-text="enrollmentDetails.course?.instructor?.name || 'N/A'"></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wide">Kategori</p>
                            <p class="text-sm font-medium text-gray-900" x-text="enrollmentDetails.course?.category?.name || 'N/A'"></p>
                        </div>
                    </div>
                </div>

                <!-- Enrollment Status -->
                <div class="col-span-2">
                    <h4 class="text-sm font-semibold text-gray-900 mb-4">Status Pendaftaran</h4>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wide">Status</p>
                            <span :class="{
                                'bg-green-100 text-green-800': enrollmentDetails.status === 'active',
                                'bg-blue-100 text-blue-800': enrollmentDetails.status === 'completed',
                                'bg-red-100 text-red-800': enrollmentDetails.status === 'suspended',
                                'bg-yellow-100 text-yellow-800': enrollmentDetails.status === 'expired',
                            }" class="inline-block px-2 py-1 text-xs font-semibold rounded-full" x-text="enrollmentDetails.status?.toUpperCase()"></span>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wide">Progres</p>
                            <p class="text-sm font-medium text-gray-900" x-text="`${enrollmentDetails.progress_percent || 0}%`"></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wide">Tanggal Daftar</p>
                            <p class="text-sm font-medium text-gray-900" x-text="enrollmentDetails.enrolled_at ? new Date(enrollmentDetails.enrolled_at).toLocaleDateString('id-ID') : 'N/A'"></p>
                        </div>
                    </div>
                </div>

                <!-- Payment Info -->
                <div class="col-span-2">
                    <h4 class="text-sm font-semibold text-gray-900 mb-4">Informasi Pembayaran</h4>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wide">Harga Dibayar</p>
                            <p class="text-sm font-medium text-gray-900" x-text="`${enrollmentDetails.currency || 'IDR'} ${enrollmentDetails.price_paid || 0}`"></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wide">Harga Kursus</p>
                            <p class="text-sm font-medium text-gray-900" x-text="`IDR ${enrollmentDetails.course?.price || 0}`"></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wide">Status Pembayaran</p>
                            <template x-if="enrollmentDetails.price_paid > 0">
                                <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">LUNAS</span>
                            </template>
                            <template x-else>
                                <template x-if="enrollmentDetails.course?.price > 0">
                                    <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">MENUNGGU</span>
                                </template>
                                <template x-else>
                                    <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">GRATIS</span>
                                </template>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Expiry Info -->
                <div class="col-span-2">
                    <h4 class="text-sm font-semibold text-gray-900 mb-4">Informasi Kadaluarsa</h4>
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide">Kadaluarsa Pada</p>
                        <p class="text-sm font-medium text-gray-900" x-text="enrollmentDetails.expires_at ? new Date(enrollmentDetails.expires_at).toLocaleDateString('id-ID') : 'Tidak Ada Kadaluarsa'"></p>
                    </div>
                </div>
            </div>

            <div class="mt-6 pt-6 border-t border-gray-200 flex justify-end space-x-3">
                <button @click="showEnrollmentDetailsModal = false" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>
