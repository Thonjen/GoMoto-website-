<template>
  <AppLayout>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
      <aside class="md:col-span-1 bg-white p-6 rounded-lg shadow-md h-fit">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Admin Panel</h2>
        <nav class="flex flex-col gap-2">
          <Link href="/admin/users" class="text-primary-600 font-medium hover:underline">User Management</Link>
          <Link href="/admin/vehicles" class="text-gray-700 hover:underline">Vehicle Listings</Link>
          <Link href="/admin/bookings" class="text-gray-700 hover:underline">Bookings</Link>
          <Link href="/admin/licenses" class="text-gray-700 hover:underline">License Verifications</Link>
          <Link href="/admin/disputes" class="text-gray-700 hover:underline">Disputes</Link>
          <Link href="/admin/reports" class="text-gray-700 hover:underline">Reports</Link>
        </nav>
      </aside>

      <div class="md:col-span-3 bg-white p-6 rounded-lg shadow-md">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">User Management</h1>

        <div class="mb-6">
          <input type="text" v-model="searchQuery" placeholder="Search users by name or email..."
            class="w-full p-3 border border-gray-300 rounded-md focus:ring-primary-400 focus:border-primary-400" />
        </div>

        <div v-if="filteredUsers.length > 0" class="overflow-x-auto">
          <table class="min-w-full bg-white border border-gray-200 rounded-lg">
            <thead>
              <tr class="bg-gray-100 text-left text-sm font-semibold text-gray-700">
                <th class="py-3 px-4 border-b">User</th>
                <th class="py-3 px-4 border-b">Email</th>
                <th class="py-3 px-4 border-b">Role</th>
                <th class="py-3 px-4 border-b">Status</th>
                <th class="py-3 px-4 border-b">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="user in filteredUsers" :key="user.id" class="hover:bg-gray-50 border-b border-gray-100 last:border-b-0">
                <td class="py-3 px-4">
                  <div class="font-medium text-gray-800">{{ user.name }}</div>
                  <div class="text-sm text-gray-600">ID: {{ user.id }}</div>
                </td>
                <td class="py-3 px-4 text-gray-700">{{ user.email }}</td>
                <td class="py-3 px-4 text-gray-700 capitalize">{{ user.role }}</td>
                <td class="py-3 px-4">
                  <span :class="['px-3 py-1 rounded-full text-xs font-medium',
                    user.isBanned ? 'bg-red-100 text-red-800' :
                    user.isVerified ? 'bg-green-100 text-green-800' :
                    'bg-yellow-100 text-yellow-800']">
                    {{ user.isBanned ? 'Banned' : (user.isVerified ? 'Verified' : 'Unverified') }}
                  </span>
                </td>
                <td class="py-3 px-4 flex gap-2">
                  <button @click="toggleBan(user)"
                    :class="['px-3 py-1 rounded-md text-xs font-medium',
                      user.isBanned ? 'bg-green-500 text-white hover:bg-green-600' : 'bg-red-500 text-white hover:bg-red-600']">
                    {{ user.isBanned ? 'Unban' : 'Ban' }}
                  </button>
                  <button v-if="!user.isVerified" @click="verifyUser(user)"
                    class="bg-blue-500 text-white px-3 py-1 rounded-md text-xs font-medium hover:bg-blue-600">
                    Verify
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <p v-else class="text-gray-600 text-center py-8">No users found.</p>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const users = ref([
  { id: 1, name: 'John Doe', email: 'john.doe@example.com', role: 'renter', isVerified: true, isBanned: false },
  { id: 2, name: 'Jane Smith', email: 'jane.s@example.com', role: 'owner', isVerified: true, isBanned: false },
  { id: 3, name: 'Peter Jones', email: 'peter.j@example.com', role: 'renter', isVerified: false, isBanned: false },
  { id: 4, name: 'Alice Brown', email: 'alice.b@example.com', role: 'owner', isVerified: true, isBanned: true },
  { id: 5, name: 'Bob White', email: 'bob.w@example.com', role: 'renter', isVerified: false, isBanned: false },
]);

const searchQuery = ref('');

const filteredUsers = computed(() => {
  const query = searchQuery.value.toLowerCase();
  return users.value.filter(user =>
    user.name.toLowerCase().includes(query) ||
    user.email.toLowerCase().includes(query)
  );
});

const toggleBan = (user) => {
  user.isBanned = !user.isBanned;
  alert(`${user.name} has been ${user.isBanned ? 'banned' : 'unbanned'}. (Not actually updated in this demo)`);
  // In a real Inertia app, this would be an Inertia.post or Inertia.put request
};

const verifyUser = (user) => {
  user.isVerified = true;
  alert(`${user.name} has been verified. (Not actually updated in this demo)`);
  // In a real Inertia app, this would be an Inertia.post or Inertia.put request
};
</script>

<style scoped>
/* Tailwind CSS is used for styling */
</style>
