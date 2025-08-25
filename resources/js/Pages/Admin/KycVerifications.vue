<template>
  <AdminLayout>
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="mb-8">
        <div class="flex justify-between items-center">
          <div>
            <h1 class="text-3xl font-bold text-gray-900">KYC Verifications</h1>
            <p class="mt-2 text-gray-600">Review and manage user verification requests</p>
          </div>
          <div class="flex space-x-4">
            <Link :href="route('admin.kyc.logs')" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
              View Logs
            </Link>
            <Link :href="route('admin.dashboard')" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
              Back to Dashboard
            </Link>
          </div>
        </div>
      </div>

      <!-- Statistics Cards -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <svg class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Pending Review</dt>
                  <dd class="text-lg font-medium text-gray-900">{{ stats.pending_review }}</dd>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Approved</dt>
                  <dd class="text-lg font-medium text-gray-900">{{ stats.approved }}</dd>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Rejected</dt>
                  <dd class="text-lg font-medium text-gray-900">{{ stats.rejected }}</dd>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Total Submissions</dt>
                  <dd class="text-lg font-medium text-gray-900">{{ stats.total_submissions }}</dd>
                </dl>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Filters -->
      <div class="bg-white shadow rounded-lg mb-6">
        <div class="px-4 py-5 sm:p-6">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">Search Users</label>
              <input
                v-model="searchForm.search"
                @input="debouncedSearch"
                type="text"
                placeholder="Name or email..."
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
              />
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700">Status</label>
              <select
                v-model="searchForm.status"
                @change="performSearch"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
              >
                <option value="">All Statuses</option>
                <option value="under_review">Under Review</option>
                <option value="approved">Approved</option>
                <option value="rejected">Rejected</option>
              </select>
            </div>
            
            <div class="flex items-end">
              <button
                @click="clearFilters"
                class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded"
              >
                Clear Filters
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Users Table -->
      <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submitted</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Documents</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="user in users.data" :key="user.id">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="flex-shrink-0 h-10 w-10">
                        <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                          <span class="text-sm font-medium text-gray-700">
                            {{ user.first_name[0] }}{{ user.last_name[0] }}
                          </span>
                        </div>
                      </div>
                      <div class="ml-4">
                        <div class="text-sm font-medium text-gray-900">
                          {{ user.first_name }} {{ user.last_name }}
                        </div>
                        <div class="text-sm text-gray-500">{{ user.email }}</div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                      {{ user.role?.name || 'N/A' }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="[
                      'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                      user.kyc_status === 'approved' ? 'bg-green-100 text-green-800' :
                      user.kyc_status === 'under_review' ? 'bg-blue-100 text-blue-800' :
                      user.kyc_status === 'rejected' ? 'bg-red-100 text-red-800' :
                      'bg-yellow-100 text-yellow-800'
                    ]">
                      {{ user.kyc_status.replace('_', ' ').toUpperCase() }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ user.license_submitted_at ? formatDate(user.license_submitted_at) : 'Not submitted' }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    <div class="flex space-x-2">
                      <span v-if="user.drivers_license_front" class="text-green-600">✓ Front</span>
                      <span v-else class="text-red-600">✗ Front</span>
                      <span v-if="user.drivers_license_back" class="text-green-600">✓ Back</span>
                      <span v-else class="text-red-600">✗ Back</span>
                      <span v-if="user.bio" class="text-green-600">✓ Bio</span>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <div class="flex space-x-2">
                      <button
                        @click="viewUserDetails(user)"
                        class="text-blue-600 hover:text-blue-900"
                      >
                        View
                      </button>
                      <button
                        v-if="user.kyc_status === 'under_review'"
                        @click="approveUser(user)"
                        class="text-green-600 hover:text-green-900"
                      >
                        Approve
                      </button>
                      <button
                        v-if="user.kyc_status === 'under_review'"
                        @click="rejectUser(user)"
                        class="text-red-600 hover:text-red-900"
                      >
                        Reject
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div class="mt-6">
            <nav class="flex items-center justify-between">
              <div class="flex-1 flex justify-between sm:hidden">
                <Link
                  v-if="users.prev_page_url"
                  :href="users.prev_page_url"
                  class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:text-gray-500"
                >
                  Previous
                </Link>
                <Link
                  v-if="users.next_page_url"
                  :href="users.next_page_url"
                  class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:text-gray-500"
                >
                  Next
                </Link>
              </div>
              <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                  <p class="text-sm text-gray-700">
                    Showing {{ users.from || 0 }} to {{ users.to || 0 }} of {{ users.total }} results
                  </p>
                </div>
                <div class="flex space-x-1">
<template v-for="link in users.links" :key="link.label">
  <Link
    v-if="link.url"
    :href="link.url"
    v-html="link.label"
    :class="[
      'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
      link.active
        ? 'z-10 bg-blue-50 border-blue-500 text-blue-600'
        : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'
    ]"
  />
  <span
    v-else
    v-html="link.label"
    class="relative inline-flex items-center px-4 py-2 border text-sm font-medium cursor-not-allowed opacity-50 bg-white border-gray-300 text-gray-500"
  />
</template>

                </div>
              </div>
            </nav>
          </div>
        </div>
      </div>

      <!-- View Details Modal -->
      <div v-if="selectedUser" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white">
          <div class="mt-3">
            <div class="flex justify-between items-center mb-4">
              <h3 class="text-lg font-medium text-gray-900">
                KYC Details - {{ selectedUser.first_name }} {{ selectedUser.last_name }}
              </h3>
              <button
                @click="selectedUser = null"
                class="text-gray-400 hover:text-gray-600"
              >
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
              <!-- User Information -->
              <div>
                <h4 class="text-md font-medium text-gray-900 mb-3">User Information</h4>
                <div class="space-y-2 text-sm">
                  <div><strong>Name:</strong> {{ selectedUser.first_name }} {{ selectedUser.last_name }}</div>
                  <div><strong>Email:</strong> {{ selectedUser.email }}</div>
                  <div><strong>Phone:</strong> {{ selectedUser.phone || 'Not provided' }}</div>
                  <div><strong>Role:</strong> {{ selectedUser.role?.name || 'N/A' }}</div>
                  <div><strong>Status:</strong> 
                    <span :class="[
                      'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ml-1',
                      selectedUser.kyc_status === 'approved' ? 'bg-green-100 text-green-800' :
                      selectedUser.kyc_status === 'under_review' ? 'bg-blue-100 text-blue-800' :
                      selectedUser.kyc_status === 'rejected' ? 'bg-red-100 text-red-800' :
                      'bg-yellow-100 text-yellow-800'
                    ]">
                      {{ selectedUser.kyc_status.replace('_', ' ').toUpperCase() }}
                    </span>
                  </div>
                  <div v-if="selectedUser.bio"><strong>Bio:</strong> {{ selectedUser.bio }}</div>
                  <div v-if="selectedUser.kyc_rejection_reason" class="text-red-600">
                    <strong>Rejection Reason:</strong> {{ selectedUser.kyc_rejection_reason }}
                  </div>
                </div>
              </div>

              <!-- Documents -->
              <div>
                <h4 class="text-md font-medium text-gray-900 mb-3">Uploaded Documents</h4>
                <div class="space-y-4">
                  <div v-if="selectedUser.drivers_license_front">
                    <p class="text-sm font-medium text-gray-700 mb-2">Driver's License (Front)</p>
                    <img 
                      :src="`/storage/${selectedUser.drivers_license_front}`" 
                      alt="Driver's License Front"
                      class="max-w-full h-48 object-contain border rounded"
                    />
                  </div>
                  <div v-if="selectedUser.drivers_license_back">
                    <p class="text-sm font-medium text-gray-700 mb-2">Driver's License (Back)</p>
                    <img 
                      :src="`/storage/${selectedUser.drivers_license_back}`" 
                      alt="Driver's License Back"
                      class="max-w-full h-48 object-contain border rounded"
                    />
                  </div>
                  <div v-if="!selectedUser.drivers_license_front && !selectedUser.drivers_license_back">
                    <p class="text-gray-500">No documents uploaded</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-6 flex justify-end space-x-3">
              <button
                @click="selectedUser = null"
                class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded"
              >
                Close
              </button>
              <button
                v-if="selectedUser.kyc_status === 'under_review'"
                @click="rejectUser(selectedUser)"
                class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded"
              >
                Reject
              </button>
              <button
                v-if="selectedUser.kyc_status === 'under_review'"
                @click="approveUser(selectedUser)"
                class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded"
              >
                Approve
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Approve Modal -->
      <div v-if="showApproveModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Approve KYC</h3>
          <form @submit.prevent="confirmApproval">
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Approval Notes (Optional)
              </label>
              <textarea
                v-model="approvalForm.reason"
                rows="3"
                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                placeholder="Add any notes about the approval..."
              ></textarea>
            </div>
            <div class="flex justify-end space-x-3">
              <button
                type="button"
                @click="showApproveModal = false"
                class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded"
              >
                Cancel
              </button>
              <button
                type="submit"
                class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded"
              >
                Approve
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Reject Modal -->
      <div v-if="showRejectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Reject KYC</h3>
          <form @submit.prevent="confirmRejection">
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Rejection Reason *
              </label>
              <textarea
                v-model="rejectionForm.reason"
                rows="3"
                required
                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                placeholder="Please provide a clear reason for rejection..."
              ></textarea>
            </div>
            <div class="flex justify-end space-x-3">
              <button
                type="button"
                @click="showRejectModal = false"
                class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded"
              >
                Cancel
              </button>
              <button
                type="submit"
                class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded"
              >
                Reject
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  users: Object,
  stats: Object,
  filters: Object
})

const selectedUser = ref(null)
const showApproveModal = ref(false)
const showRejectModal = ref(false)
const currentUser = ref(null)

const searchForm = reactive({
  search: props.filters.search || '',
  status: props.filters.status || ''
})

const approvalForm = reactive({
  reason: ''
})

const rejectionForm = reactive({
  reason: ''
})

let searchTimeout = null

const debouncedSearch = () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    performSearch()
  }, 300)
}

const performSearch = () => {
  router.get(route('admin.kyc.verifications'), searchForm, {
    preserveState: true,
    replace: true
  })
}

const clearFilters = () => {
  searchForm.search = ''
  searchForm.status = ''
  performSearch()
}

const viewUserDetails = (user) => {
  selectedUser.value = user
}

const approveUser = (user) => {
  currentUser.value = user
  approvalForm.reason = ''
  showApproveModal.value = true
}

const rejectUser = (user) => {
  currentUser.value = user
  rejectionForm.reason = ''
  showRejectModal.value = true
}

const confirmApproval = () => {
  router.post(route('admin.kyc.approve', currentUser.value.id), approvalForm, {
    onSuccess: () => {
      showApproveModal.value = false
      selectedUser.value = null
      currentUser.value = null
    }
  })
}

const confirmRejection = () => {
  router.post(route('admin.kyc.reject', currentUser.value.id), rejectionForm, {
    onSuccess: () => {
      showRejectModal.value = false
      selectedUser.value = null
      currentUser.value = null
    }
  })
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}
</script>
