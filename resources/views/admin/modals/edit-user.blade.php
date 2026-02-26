<div x-show="showEditUserModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" x-transition>
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Edit User</h3>
        <form @submit.prevent="updateUser(editingUser.id, {
            name: $refs.editName.value,
            email: $refs.editEmail.value,
            username: $refs.editUsername.value || null,
            bio: $refs.editBio.value || null,
            avatar_url: $refs.editAvatar.value || null,
            is_active: $refs.editActive.checked
        })">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Name</label>
                    <input x-ref="editName" type="text" :value="editingUser.name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Email</label>
                    <input x-ref="editEmail" type="email" :value="editingUser.email" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Username</label>
                    <input x-ref="editUsername" type="text" :value="editingUser.username || ''" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Status</label>
                    <input x-ref="editActive" type="checkbox" :checked="editingUser.is_active" class="mt-1 rounded text-blue-600">
                    <span class="ml-2 text-sm text-gray-700">Active</span>
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <button type="button" @click="showEditUserModal = false" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Update User</button>
            </div>
        </form>
    </div>
</div>