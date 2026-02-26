<!-- Notification Create Modal -->
<div x-show="showCreateNotificationModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50" @click.self="showCreateNotificationModal = false">
    <div class="bg-white rounded-lg shadow-lg p-6 max-w-2xl w-full mx-4">
        <h3 class="text-lg font-semibold mb-4">Kirim Notifikasi</h3>
        
        <form @submit.prevent="sendNotification()" class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipe</label>
                    <select x-model="notificationForm.type" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Pilih Tipe</option>
                        <option value="enrollment">Pendaftaran</option>
                        <option value="payment">Pembayaran</option>
                        <option value="system">Sistem</option>
                    </select>
                </div>
                <div x-data="{ userDropdownOpen: false, userSearch: '' }" class="relative">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Pengguna (Opsional)</label>
                    <button type="button" @click="userDropdownOpen = !userDropdownOpen" class="w-full rounded-md border border-gray-300 px-3 py-2 text-left focus:border-blue-500 focus:ring-blue-500 bg-white hover:bg-gray-50 flex justify-between items-center">
                        <span x-text="notificationForm.user_id ? users.find(u => u.id == notificationForm.user_id)?.name || 'Semua Pengguna (Siaran)' : 'Semua Pengguna (Siaran)'"></span>
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                        </svg>
                    </button>
                    
                    <div x-show="userDropdownOpen" @click.outside="userDropdownOpen = false" class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg">
                        <input type="text" x-model="userSearch" placeholder="Cari pengguna..." class="w-full px-3 py-2 border-b border-gray-200 text-sm focus:outline-none">
                        <div class="max-h-64 overflow-y-auto">
                            <button type="button" @click="notificationForm.user_id = ''; userDropdownOpen = false; userSearch = ''" class="w-full text-left px-3 py-2 hover:bg-blue-50 text-sm">
                                <span class="font-medium text-gray-900">Semua Pengguna (Siaran)</span>
                            </button>
                            <template x-for="user in users.filter(u => u.name.toLowerCase().includes(userSearch.toLowerCase()))" :key="user.id">
                                <button type="button" @click="notificationForm.user_id = user.id; userDropdownOpen = false; userSearch = ''" 
                                    :class="notificationForm.user_id == user.id ? 'bg-blue-100 border-l-4 border-blue-500' : 'hover:bg-gray-100'"
                                    class="w-full text-left px-3 py-2 text-sm text-gray-900">
                                    <span x-text="user.name"></span>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Pesan</label>
                <textarea x-model="notificationForm.message" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-blue-500" placeholder="Pesan notifikasi" rows="3" required></textarea>
            </div>

            <div class="flex justify-end gap-2 pt-4">
                <button type="button" @click="showCreateNotificationModal = false" class="px-4 py-2 text-gray-700 bg-gray-200 rounded hover:bg-gray-300">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Kirim</button>
            </div>
        </form>
    </div>
</div>
