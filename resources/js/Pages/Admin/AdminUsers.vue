<template>
  <AppLayout>
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="mb-8">
        <div class="flex justify-between items-center">
          <div>
            <h1 class="text-3xl font-bold text-white">User Management</h1>
            <p class="mt-2 text-white/70">Manage all users in the system</p>
          </div>
          <Link :href="route('admin.dashboard')" class="bg-white/10 hover:bg-white/20 text-white font-bold py-2 px-4 rounded-md backdrop-blur-sm border border-white/20 transition-colors">
            Back to Dashboard
          </Link>
        </div>
      </div>

      <!-- Filters -->
      <div class="glass-card-dark mb-6">
        <div class="px-4 py-5 sm:p-6">
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
              <label class="block text-sm font-medium text-white mb-2">Search</label>
              <input 
                type="text" 
                v-model="searchQuery" 
                placeholder="Search by name, email, or phone..."
                class="w-full bg-white/10 border border-white/20 rounded-md px-3 py-2 text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 backdrop-blur-sm"
                @input="updateFilters"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-white mb-2">Role</label>
              <select 
                v-model="selectedRole" 
                class="w-full bg-white/10 border border-white/20 rounded-md px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 backdrop-blur-sm"
                @change="updateFilters"
              >
                <option value="" class="bg-gray-800 text-white">All Roles</option>
                <option v-for="role in roles" :key="role.id" :value="role.name" class="bg-gray-800 text-white">{{ role.name }}</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-white mb-2">Status</label>
              <select 
                v-model="selectedStatus" 
                class="w-full bg-white/10 border border-white/20 rounded-md px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 backdrop-blur-sm"
                @change="updateFilters"
              >
                <option value="" class="bg-gray-800 text-white">All Status</option>
                <option value="active" class="bg-gray-800 text-white">Active</option>
                <option value="banned" class="bg-gray-800 text-white">Banned</option>
                <option value="suspended" class="bg-gray-800 text-white">Suspended</option>
              </select>
            </div>
            <div class="flex items-end">
              <button 
                @click="clearFilters"
                class="w-full bg-white/10 hover:bg-white/20 text-white font-bold py-2 px-4 rounded-md backdrop-blur-sm border border-white/20 transition-colors"
              >
                Clear Filters
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Users Table -->
      <div class="glass-card-dark overflow-hidden">
        <div class="px-4 py-5 sm:p-6">
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-white/20">
              <thead class="bg-white/10 backdrop-blur-sm">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">User</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Contact</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Role</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Statistics</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Status</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Actions</th>
                </tr>
              </thead>
              <tbody class="glass-card divide-y divide-white/10">
                <tr v-for="user in users.data" :key="user.id" class="hover:bg-white/5 transition-colors">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="flex-shrink-0 h-10 w-10">
                        <div class="h-10 w-10 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center border border-white/20">
                          <span class="text-sm font-medium text-white">
                            {{ user.first_name?.[0] }}{{ user.last_name?.[0] }}
                          </span>
                        </div>
                      </div>
                      <div class="ml-4">
                        <div class="text-sm font-medium text-white">
                          {{ user.first_name }} {{ user.last_name }}
                        </div>
                        <div class="text-sm text-white/70">ID: {{ user.id }}</div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-white">{{ user.email }}</div>
                    <div class="text-sm text-white/70">{{ user.phone || 'No phone' }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full backdrop-blur-sm"
                          :class="{
                            'bg-blue-400/20 text-white': user.role?.name === 'customer',
                            'bg-green-400/20 text-green-400': user.role?.name === 'owner',
                            'bg-purple-400/20 text-purple-400': user.role?.name === 'admin'
                          }">
                      {{ user.role?.name || 'No role' }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-white/90">
                    <div>{{ user.vehicles_count || 0 }} vehicles</div>
                    <div>{{ user.bookings_count || 0 }} bookings</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full backdrop-blur-sm"
                          :class="{
                            'bg-green-400/20 text-green-400': user.status === 'active' || !user.status,
                            'bg-red-400/20 text-red-400': user.status === 'banned',
                            'bg-yellow-400/20 text-yellow-400': user.status === 'suspended'
                          }">
                      {{ user.status || 'active' }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <div class="flex space-x-2">
                      <button 
                        @click="editUser(user)"
                        class="text-blue-400 hover:text-blue-300 transition-colors"
                      >
                        Edit
                      </button>
                      <button 
                        v-if="user.status !== 'banned'"
                        @click="banUser(user.id)"
                        class="text-red-400 hover:text-red-300 transition-colors"
                      >
                        Ban
                      </button>
                      <button 
                        v-else
                        @click="unbanUser(user.id)"
                        class="text-green-400 hover:text-green-300 transition-colors"
                      >
                        Unban
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div v-if="users.links" class="mt-6">
            <nav class="flex items-center justify-between">
              <div class="flex-1 flex justify-between sm:hidden">
                <Link 
                  v-if="users.prev_page_url" 
                  :href="users.prev_page_url" 
                  class="relative inline-flex items-center px-4 py-2 border border-white/20 text-sm font-medium rounded-md text-white bg-white/10 hover:bg-white/20 backdrop-blur-sm transition-colors"
                >
                  Previous
                </Link>
                <Link 
                  v-if="users.next_page_url" 
                  :href="users.next_page_url" 
                  class="ml-3 relative inline-flex items-center px-4 py-2 border border-white/20 text-sm font-medium rounded-md text-white bg-white/10 hover:bg-white/20 backdrop-blur-sm transition-colors"
                >
                  Next
                </Link>
              </div>
              <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                  <p class="text-sm text-white/70">
                    Showing {{ users.from }} to {{ users.to }} of {{ users.total }} results
                  </p>
                </div>
                <div>
                  <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                    <Link 
                      v-for="link in users.links" 
                      :key="link.label"
                      :href="link.url || '#'"
                      v-html="link.label"
                      :class="[
                        'relative inline-flex items-center px-4 py-2 border text-sm font-medium backdrop-blur-sm',
                        link.active 
                          ? 'z-10 bg-blue-400/20 border-blue-400 text-blue-400' 
                          : 'bg-white/10 border-white/20 text-white/70 hover:bg-white/20',
                        !link.url ? 'cursor-not-allowed opacity-50' : 'cursor-pointer'
                      ]"
                    />
                  </nav>
                </div>
              </div>
            </nav>
          </div>
        </div>
      </div>

      <!-- Edit User Modal -->
      <div v-if="showEditModal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border border-white/20 w-96 shadow-xl rounded-md glass-card-dark">
          <div class="mt-3">
            <h3 class="text-lg font-medium text-white mb-4">Edit User</h3>
            <form @submit.prevent="updateUser">
              <div class="mb-4">
                <label class="block text-sm font-medium text-white mb-2">Role</label>
                <select 
                  v-model="editingUser.role_id" 
                  class="w-full bg-white/10 border border-white/20 rounded-md px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-blue-400 backdrop-blur-sm"
                >
                  <option v-for="role in roles" :key="role.id" :value="role.id" class="bg-gray-800 text-white">{{ role.name }}</option>
                </select>
              </div>
              <div class="flex justify-end space-x-2">
                <button 
                  type="button" 
                  @click="showEditModal = false"
                  class="px-4 py-2 bg-white/10 text-white rounded-md hover:bg-white/20 backdrop-blur-sm border border-white/20 transition-colors"
                >
                  Cancel
                </button>
                <button 
                  type="submit"
                  class="px-4 py-2 bg-blue-500/80 text-white rounded-md hover:bg-blue-600 backdrop-blur-sm transition-colors"
                >
                  Update
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  users: Object,
  roles: Array,
  filters: Object
})

const searchQuery = ref(props.filters.search || '')
const selectedRole = ref(props.filters.role || '')
const selectedStatus = ref(props.filters.status || '')
const showEditModal = ref(false)
const editingUser = reactive({})

const updateFilters = () => {
  router.get(route('admin.users'), {
    search: searchQuery.value,
    role: selectedRole.value,
    status: selectedStatus.value
  }, {
    preserveState: true,
    replace: true
  })
}

const clearFilters = () => {
  searchQuery.value = ''
  selectedRole.value = ''
  selectedStatus.value = ''
  updateFilters()
}

const banUser = (userId) => {
  if (confirm('Are you sure you want to ban this user?')) {
    router.post(route('admin.users.ban', userId), {}, {
      onSuccess: () => {
        // Handle success
      }
    })
  }
}

const unbanUser = (userId) => {
  router.post(route('admin.users.unban', userId), {}, {
    onSuccess: () => {
      // Handle success
    }
  })
}

const editUser = (user) => {
  Object.assign(editingUser, user)
  showEditModal.value = true
}

const updateUser = () => {
  router.post(route('admin.users.update-role', editingUser.id), {
    role_id: editingUser.role_id
  }, {
    onSuccess: () => {
      showEditModal.value = false
    }
  })
}
</script>
        