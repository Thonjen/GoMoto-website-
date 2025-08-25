<template>
  <AdminLayout>
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="mb-8">
        <div class="flex justify-between items-center">
          <div>
            <h1 class="text-3xl font-bold text-gray-900">KYC Verification Logs</h1>
            <p class="mt-2 text-gray-600">Track all KYC verification activities and decisions</p>
          </div>
          <div class="flex space-x-4">
            <Link :href="route('admin.kyc.verifications')" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
              Back to Verifications
            </Link>
            <Link :href="route('admin.dashboard')" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
              Dashboard
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
                <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Total Actions</dt>
                  <dd class="text-lg font-medium text-gray-900">{{ stats.total_actions }}</dd>
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
                  <dt class="text-sm font-medium text-gray-500 truncate">Approvals</dt>
                  <dd class="text-lg font-medium text-gray-900">{{ stats.approvals }}</dd>
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
                  <dt class="text-sm font-medium text-gray-500 truncate">Rejections</dt>
                  <dd class="text-lg font-medium text-gray-900">{{ stats.rejections }}</dd>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <svg class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                </svg>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Submissions</dt>
                  <dd class="text-lg font-medium text-gray-900">{{ stats.submissions }}</dd>
                </dl>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Filters -->
      <div class="bg-white shadow rounded-lg mb-6">
        <div class="px-4 py-5 sm:p-6">
          <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">Admin</label>
              <select
                v-model="searchForm.admin_id"
                @change="performSearch"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
              >
                <option value="">All Admins</option>
                <option v-for="admin in admins" :key="admin.id" :value="admin.id">
                  {{ admin.first_name }} {{ admin.last_name }}
                </option>
              </select>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700">Action</label>
              <select
                v-model="searchForm.action"
                @change="performSearch"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
              >
                <option value="">All Actions</option>
                <option value="submitted">Submitted</option>
                <option value="approved">Approved</option>
                <option value="rejected">Rejected</option>
                <option value="resubmitted">Resubmitted</option>
              </select>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700">Date From</label>
              <input
                v-model="searchForm.date_from"
                @change="performSearch"
                type="date"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
              />
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700">Date To</label>
              <input
                v-model="searchForm.date_to"
                @change="performSearch"
                type="date"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
              />
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

      <!-- Logs Table -->
      <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Timestamp</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Admin</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status Change</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Details</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="log in logs.data" :key="log.id">
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ formatDate(log.created_at) }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">
                      {{ log.user?.first_name }} {{ log.user?.last_name }}
                    </div>
                    <div class="text-sm text-gray-500">{{ log.user?.email }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="[
                      'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                      log.action === 'approved' ? 'bg-green-100 text-green-800' :
                      log.action === 'rejected' ? 'bg-red-100 text-red-800' :
                      log.action === 'submitted' || log.action === 'resubmitted' ? 'bg-blue-100 text-blue-800' :
                      'bg-gray-100 text-gray-800'
                    ]">
                      {{ log.action_label }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    <div v-if="log.admin">
                      {{ log.admin.first_name }} {{ log.admin.last_name }}
                    </div>
                    <div v-else class="text-gray-400">System</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    <div class="flex items-center space-x-2">
                      <span v-if="log.old_status" :class="[
                        'inline-flex items-center px-2 py-0.5 rounded text-xs font-medium',
                        log.old_status === 'approved' ? 'bg-green-100 text-green-800' :
                        log.old_status === 'rejected' ? 'bg-red-100 text-red-800' :
                        log.old_status === 'under_review' ? 'bg-blue-100 text-blue-800' :
                        'bg-yellow-100 text-yellow-800'
                      ]">
                        {{ log.old_status.replace('_', ' ') }}
                      </span>
                      <span v-if="log.old_status">→</span>
                      <span :class="[
                        'inline-flex items-center px-2 py-0.5 rounded text-xs font-medium',
                        log.new_status === 'approved' ? 'bg-green-100 text-green-800' :
                        log.new_status === 'rejected' ? 'bg-red-100 text-red-800' :
                        log.new_status === 'under_review' ? 'bg-blue-100 text-blue-800' :
                        'bg-yellow-100 text-yellow-800'
                      ]">
                        {{ log.new_status.replace('_', ' ') }}
                      </span>
                    </div>
                  </td>
                  <td class="px-6 py-4 text-sm text-gray-500">
                    <button
                      @click="viewLogDetails(log)"
                      class="text-blue-600 hover:text-blue-900"
                    >
                      View Details
                    </button>
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
                  v-if="logs.prev_page_url"
                  :href="logs.prev_page_url"
                  class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:text-gray-500"
                >
                  Previous
                </Link>
                <Link
                  v-if="logs.next_page_url"
                  :href="logs.next_page_url"
                  class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:text-gray-500"
                >
                  Next
                </Link>
              </div>
              <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                  <p class="text-sm text-gray-700">
                    Showing {{ logs.from || 0 }} to {{ logs.to || 0 }} of {{ logs.total }} results
                  </p>
                </div>
                <div class="flex space-x-1">
<template v-for="link in logs.links" :key="link.label">
  <Link
    v-if="link.url"
    :href="link.url"
    v-html="link.label"
    :class="[
      'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
      link.active
        ? 'z-10 bg-blue-50 border-blue-500 text-blue-600'
        : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50 hover:text-gray-700'
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

      <!-- Log Details Modal -->
      <div v-if="selectedLog" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-2xl shadow-lg rounded-md bg-white">
          <div class="mt-3">
            <div class="flex justify-between items-center mb-4">
              <h3 class="text-lg font-medium text-gray-900">
                Log Details - {{ selectedLog.action_label }}
              </h3>
              <button
                @click="selectedLog = null"
                class="text-gray-400 hover:text-gray-600"
              >
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>

            <div class="space-y-4">
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700">Timestamp</label>
                  <p class="mt-1 text-sm text-gray-900">{{ formatDate(selectedLog.created_at) }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">Action</label>
                  <p class="mt-1">
                    <span :class="[
                      'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                      selectedLog.action === 'approved' ? 'bg-green-100 text-green-800' :
                      selectedLog.action === 'rejected' ? 'bg-red-100 text-red-800' :
                      selectedLog.action === 'submitted' || selectedLog.action === 'resubmitted' ? 'bg-blue-100 text-blue-800' :
                      'bg-gray-100 text-gray-800'
                    ]">
                      {{ selectedLog.action_label }}
                    </span>
                  </p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">User</label>
                  <p class="mt-1 text-sm text-gray-900">
                    {{ selectedLog.user?.first_name }} {{ selectedLog.user?.last_name }}
                    <br>
                    <span class="text-gray-500">{{ selectedLog.user?.email }}</span>
                  </p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">Admin</label>
                  <p class="mt-1 text-sm text-gray-900">
                    <span v-if="selectedLog.admin">
                      {{ selectedLog.admin.first_name }} {{ selectedLog.admin.last_name }}
                    </span>
                    <span v-else class="text-gray-500">System/Self-submission</span>
                  </p>
                </div>
              </div>

              <div v-if="selectedLog.old_status">
                <label class="block text-sm font-medium text-gray-700">Status Change</label>
                <div class="mt-1 flex items-center space-x-2">
                  <span :class="[
                    'inline-flex items-center px-2 py-0.5 rounded text-xs font-medium',
                    selectedLog.old_status === 'approved' ? 'bg-green-100 text-green-800' :
                    selectedLog.old_status === 'rejected' ? 'bg-red-100 text-red-800' :
                    selectedLog.old_status === 'under_review' ? 'bg-blue-100 text-blue-800' :
                    'bg-yellow-100 text-yellow-800'
                  ]">
                    {{ selectedLog.old_status.replace('_', ' ') }}
                  </span>
                  <span>→</span>
                  <span :class="[
                    'inline-flex items-center px-2 py-0.5 rounded text-xs font-medium',
                    selectedLog.new_status === 'approved' ? 'bg-green-100 text-green-800' :
                    selectedLog.new_status === 'rejected' ? 'bg-red-100 text-red-800' :
                    selectedLog.new_status === 'under_review' ? 'bg-blue-100 text-blue-800' :
                    'bg-yellow-100 text-yellow-800'
                  ]">
                    {{ selectedLog.new_status.replace('_', ' ') }}
                  </span>
                </div>
              </div>

              <div v-if="selectedLog.documents_checked">
                <label class="block text-sm font-medium text-gray-700">Documents Checked</label>
                <div class="mt-1 space-y-1">
                  <div v-for="(checked, document) in selectedLog.documents_checked" :key="document" class="text-sm">
                    <span :class="checked ? 'text-green-600' : 'text-red-600'">
                      {{ checked ? '✓' : '✗' }}
                    </span>
                    {{ document.replace('_', ' ').replace(/([A-Z])/g, ' $1').toLowerCase() }}
                  </div>
                </div>
              </div>

              <div v-if="selectedLog.reason">
                <label class="block text-sm font-medium text-gray-700">Reason/Notes</label>
                <p class="mt-1 text-sm text-gray-900 bg-gray-50 p-3 rounded">{{ selectedLog.reason }}</p>
              </div>

              <div class="grid grid-cols-2 gap-4 text-xs text-gray-500">
                <div>
                  <label class="block font-medium">IP Address</label>
                  <p>{{ selectedLog.ip_address || 'N/A' }}</p>
                </div>
                <div>
                  <label class="block font-medium">User Agent</label>
                  <p class="truncate">{{ selectedLog.user_agent || 'N/A' }}</p>
                </div>
              </div>
            </div>

            <div class="mt-6 flex justify-end">
              <button
                @click="selectedLog = null"
                class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded"
              >
                Close
              </button>
            </div>
          </div>
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
  logs: Object,
  stats: Object,
  admins: Array,
  filters: Object
})

const selectedLog = ref(null)

const searchForm = reactive({
  admin_id: props.filters.admin_id || '',
  action: props.filters.action || '',
  date_from: props.filters.date_from || '',
  date_to: props.filters.date_to || ''
})

const performSearch = () => {
  router.get(route('admin.kyc.logs'), searchForm, {
    preserveState: true,
    replace: true
  })
}

const clearFilters = () => {
  searchForm.admin_id = ''
  searchForm.action = ''
  searchForm.date_from = ''
  searchForm.date_to = ''
  performSearch()
}

const viewLogDetails = (log) => {
  selectedLog.value = log
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
