<div x-show="showCreateUserModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" x-transition>
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Create New User</h3>
        <form @submit.prevent="createUser({
            email: $refs.createEmail.value,
            password: $refs.createPassword.value,
            name: $refs.createName.value,
            username: $refs.createUsername.value || null,
            bio: $refs.createBio.value || null,
            avatar_url: $refs.createAvatar.value || null
        })">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Name *</label>
                    <input x-ref="createName" type="text" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Email *</label>
                    <input x-ref="createEmail" type="email" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Password *</label>
                    <input x-ref="createPassword" type="password" required minlength="8" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Username</label>
                    <input x-ref="createUsername" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Bio</label>
                    <textarea x-ref="createBio" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Avatar URL</label>
                    <input x-ref="createAvatar" type="url" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <button type="button" @click="showCreateUserModal = false" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Create User</button>
            </div>
        </form>
    </div>
</div>