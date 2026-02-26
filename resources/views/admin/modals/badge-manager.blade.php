<!-- Badge Create/Edit Modal -->
<div x-show="showCreateBadgeModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50" @click.self="showCreateBadgeModal = false">
    <div class="bg-white rounded-lg shadow-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-semibold mb-4" x-text="badgeForm.id ? 'Edit Lencana' : 'Buat Lencana'"></h3>
        
        <form @submit.prevent="saveBadge()" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                <input x-model="badgeForm.name" type="text" required class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-blue-500" placeholder="Nama lencana">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kode</label>
                <input x-model="badgeForm.code" type="text" required class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-blue-500" placeholder="KODE_LENCANA">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                <textarea x-model="badgeForm.description" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-blue-500" placeholder="Deskripsi lencana" rows="3"></textarea>
            </div>

            <div class="flex justify-end gap-2 pt-4">
                <button type="button" @click="showCreateBadgeModal = false" class="px-4 py-2 text-gray-700 bg-gray-200 rounded hover:bg-gray-300">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Simpan Lencana</button>
            </div>
        </form>
    </div>
</div>
