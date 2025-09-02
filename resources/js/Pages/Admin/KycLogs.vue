<template>
  <AdminLayout>
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="mb-8">
        <div class="flex justify-between items-center">
          <div>
            <h1 class="text-3xl font-bold text-white">KYC Verification Logs</h1>
            <p class="mt-2 text-white/70">Track all KYC verification activities and decisions</p>
          </div>
          <div class="flex space-x-4">
            <Link :href="route('admin.kyc.verifications')" class="bg-blue-400/20 hover:bg-blue-400/30 text-white font-bold py-2 px-4 rounded-md backdrop-blur-sm border border-blue-400/30 transition-colors">
              Back to Verifications
            </Link>
            <Link :href="route('admin.dashboard')" class="bg-white/10 hover:bg-white/20 text-white font-bold py-2 px-4 rounded-md backdrop-blur-sm border border-white/20 transition-colors">
              Dashboard
            </Link>
          </div>
        </div>
      </div>

      <!-- Statistics Cards -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-blue-400/20 shadow-glow rounded-lg backdrop-blur-sm border border-white/20 overflow-hidden">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <svg class="h-6 w-6 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-white/70 truncate">Total Actions</dt>
                  <dd class="text-lg font-medium text-white">{{ stats.total_actions }}</dd>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-green-400/20 shadow-glow rounded-lg backdrop-blur-sm border border-white/20 overflow-hidden">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <svg class="h-6 w-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-white/70 truncate">Approvals</dt>
                  <dd class="text-lg font-medium text-white">{{ stats.approvals }}</dd>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-red-400/20 shadow-glow rounded-lg backdrop-blur-sm border border-white/20 overflow-hidden">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <svg class="h-6 w-6 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-white/70 truncate">Rejections</dt>
                  <dd class="text-lg font-medium text-white">{{ stats.rejections }}</dd>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-yellow-400/20 shadow-glow rounded-lg backdrop-blur-sm border border-white/20 overflow-hidden">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <svg class="h-6 w-6 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                </svg>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-white/70 truncate">Submissions</dt>
                  <dd class="text-lg font-medium text-white">{{ stats.submissions }}</dd>
                </dl>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Filters -->
      <div class="glass-card-dark mb-6">
        <div class="px-4 py-5 sm:p-6">
          <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
              <label class="block text-sm font-medium text-white">Admin</label>
              <select
                v-model="searchForm.admin_id"
                @change="performSearch"
                class="mt-1 block w-full bg-white/10 border border-white/20 rounded-md px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 backdrop-blur-sm"
              >
                <option value="" class="bg-gray-800 text-white">All Admins</option>
                <option v-for="admin in admins" :key="admin.id" :value="admin.id" class="bg-gray-800 text-white">
                  {{ admin.first_name }} {{ admin.last_name }}
                </option>
              </select>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-white">Action</label>
              <select
                v-model="searchForm.action"
                @change="performSearch"
                class="mt-1 block w-full bg-white/10 border border-white/20 rounded-md px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 backdrop-blur-sm"
              >
                <option value="" class="bg-gray-800 text-white">All Actions</option>
                <option value="submitted" class="bg-gray-800 text-white">Submitted</option>
                <option value="approved" class="bg-gray-800 text-white">Approved</option>
                <option value="rejected" class="bg-gray-800 text-white">Rejected</option>
                <option value="resubmitted" class="bg-gray-800 text-white">Resubmitted</option>
              </select>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-white">Date From</label>
              <input
                v-model="searchForm.date_from"
                @change="performSearch"
                type="date"
                class="mt-1 block w-full bg-white/10 border border-white/20 rounded-md px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 backdrop-blur-sm"
              />
            </div>
            
            <div>
              <label class="block text-sm font-medium text-white">Date To</label>
              <input
                v-model="searchForm.date_to"
                @change="performSearch"
                type="date"
                class="mt-1 block w-full bg-white/10 border border-white/20 rounded-md px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 backdrop-blur-sm"
              />
            </div>
            
            <div class="flex items-end">
              <button
                @click="clearFilters"
                class="bg-white/10 hover:bg-white/20 text-white font-medium py-2 px-4 rounded-md border border-white/20 transition-all duration-200 backdrop-blur-sm shadow-glow"
              >
                Clear Filters
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Logs Table -->
      <div class="glass-card-dark">
        <div class="px-4 py-5 sm:p-6">
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-white/20">
              <thead class="bg-white/5">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-white/70 uppercase tracking-wider">Timestamp</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-white/70 uppercase tracking-wider">User</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-white/70 uppercase tracking-wider">Action</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-white/70 uppercase tracking-wider">Admin</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-white/70 uppercase tracking-wider">Status Change</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-white/70 uppercase tracking-wider">Details</th>
                </tr>
              </thead>
              <tbody class="bg-transparent divide-y divide-white/10">
                <tr v-for="log in logs.data" :key="log.id" class="hover:bg-white/5 transition-colors duration-150">
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-white/70">
                    {{ formatDate(log.created_at) }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-white">
                      {{ log.user?.first_name }} {{ log.user?.last_name }}
                    </div>
                    <div class="text-sm text-white/70">{{ log.user?.email }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="[
                      'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border backdrop-blur-sm',
                      log.action === 'approved' ? 'bg-green-400/20 text-green-300 border-green-400/30' :
                      log.action === 'rejected' ? 'bg-red-400/20 text-red-300 border-red-400/30' :
                      log.action === 'submitted' || log.action === 'resubmitted' ? 'bg-blue-400/20 text-blue-300 border-blue-400/30' :
                      'bg-gray-400/20 text-gray-300 border-gray-400/30'
                    ]">
                      {{ log.action_label }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-white/70">
                    <div v-if="log.admin">
                      {{ log.admin.first_name }} {{ log.admin.last_name }}
                    </div>
                    <div v-else class="text-white/50">System</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-white/70">
                    <div class="flex items-center space-x-2">
                      <span v-if="log.old_status" :class="[
                        'inline-flex items-center px-2 py-0.5 rounded text-xs font-medium border backdrop-blur-sm',
                        log.old_status === 'approved' ? 'bg-green-400/20 text-green-300 border-green-400/30' :
                        log.old_status === 'rejected' ? 'bg-red-400/20 text-red-300 border-red-400/30' :
                        log.old_status === 'under_review' ? 'bg-blue-400/20 text-blue-300 border-blue-400/30' :
                        'bg-yellow-400/20 text-yellow-300 border-yellow-400/30'
                      ]">
                        {{ log.old_status.replace('_', ' ') }}
                      </span>
                      <span v-if="log.old_status" class="text-white/50">→</span>
                      <span :class="[
                        'inline-flex items-center px-2 py-0.5 rounded text-xs font-medium border backdrop-blur-sm',
                        log.new_status === 'approved' ? 'bg-green-400/20 text-green-300 border-green-400/30' :
                        log.new_status === 'rejected' ? 'bg-red-400/20 text-red-300 border-red-400/30' :
                        log.new_status === 'under_review' ? 'bg-blue-400/20 text-blue-300 border-blue-400/30' :
                        'bg-yellow-400/20 text-yellow-300 border-yellow-400/30'
                      ]">
                        {{ log.new_status.replace('_', ' ') }}
                      </span>
                    </div>
                  </td>
                  <td class="px-6 py-4 text-sm text-white/70">
                    <button
                      @click="viewLogDetails(log)"
                      class="text-blue-400 hover:text-blue-300 transition-colors duration-150"
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
                  class="relative inline-flex items-center px-4 py-2 border border-white/20 text-sm font-medium rounded-md text-white bg-white/10 hover:bg-white/20 backdrop-blur-sm transition-all duration-200"
                >
                  Previous
                </Link>
                <Link
                  v-if="logs.next_page_url"
                  :href="logs.next_page_url"
                  class="ml-3 relative inline-flex items-center px-4 py-2 border border-white/20 text-sm font-medium rounded-md text-white bg-white/10 hover:bg-white/20 backdrop-blur-sm transition-all duration-200"
                >
                  Next
                </Link>
              </div>
              <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                  <p class="text-sm text-white/70">
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
                        'relative inline-flex items-center px-4 py-2 border text-sm font-medium rounded backdrop-blur-sm transition-all duration-200',
                        link.active
                          ? 'z-10 bg-blue-400/20 border-blue-400/30 text-blue-300'
                          : 'bg-white/10 border-white/20 text-white/70 hover:bg-white/20 hover:text-white'
                      ]"
                    />
                    <span
                      v-else
                      v-html="link.label"
                      class="relative inline-flex items-center px-4 py-2 border text-sm font-medium cursor-not-allowed opacity-50 bg-white/5 border-white/10 text-white/50 rounded backdrop-blur-sm"
                    />
                  </template>
                </div>
              </div>
            </nav>
          </div>
        </div>
      </div>

      <!-- Log Details Modal -->
      <div v-if="selectedLog" class="fixed inset-0 bg-black/50 overflow-y-auto h-full w-full z-50 backdrop-blur-sm">
        <div class="relative top-20 mx-auto p-5 border border-white/20 w-11/12 max-w-2xl shadow-glow rounded-xl glass-card-dark">
          <div class="mt-3">
            <div class="flex justify-between items-center mb-4">
              <h3 class="text-lg font-medium text-white">
                Log Details - {{ selectedLog.action_label }}
              </h3>
              <button
                @click="selectedLog = null"
                class="text-white/70 hover:text-white transition-colors duration-150"
              >
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>

            <div class="space-y-4">
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-white/70">Timestamp</label>
                  <p class="mt-1 text-sm text-white">{{ formatDate(selectedLog.created_at) }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-white/70">Action</label>
                  <p class="mt-1">
                    <span :class="[
                      'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border backdrop-blur-sm',
                      selectedLog.action === 'approved' ? 'bg-green-400/20 text-green-300 border-green-400/30' :
                      selectedLog.action === 'rejected' ? 'bg-red-400/20 text-red-300 border-red-400/30' :
                      selectedLog.action === 'submitted' || selectedLog.action === 'resubmitted' ? 'bg-blue-400/20 text-blue-300 border-blue-400/30' :
                      'bg-gray-400/20 text-gray-300 border-gray-400/30'
                    ]">
                      {{ selectedLog.action_label }}
                    </span>
                  </p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-white/70">User</label>
                  <p class="mt-1 text-sm text-white">
                    {{ selectedLog.user?.first_name }} {{ selectedLog.user?.last_name }}
                    <br>
                    <span class="text-white/70">{{ selectedLog.user?.email }}</span>
                  </p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-white/70">Admin</label>
                  <p class="mt-1 text-sm text-white">
                    <span v-if="selectedLog.admin">
                      {{ selectedLog.admin.first_name }} {{ selectedLog.admin.last_name }}
                    </span>
                    <span v-else class="text-white/50">System/Self-submission</span>
                  </p>
                </div>
              </div>

              <div v-if="selectedLog.old_status">
                <label class="block text-sm font-medium text-white/70">Status Change</label>
                <div class="mt-1 flex items-center space-x-2">
                  <span :class="[
                    'inline-flex items-center px-2 py-0.5 rounded text-xs font-medium border backdrop-blur-sm',
                    selectedLog.old_status === 'approved' ? 'bg-green-400/20 text-green-300 border-green-400/30' :
                    selectedLog.old_status === 'rejected' ? 'bg-red-400/20 text-red-300 border-red-400/30' :
                    selectedLog.old_status === 'under_review' ? 'bg-blue-400/20 text-blue-300 border-blue-400/30' :
                    'bg-yellow-400/20 text-yellow-300 border-yellow-400/30'
                  ]">
                    {{ selectedLog.old_status.replace('_', ' ') }}
                  </span>
                  <span class="text-white/50">→</span>
                  <span :class="[
                    'inline-flex items-center px-2 py-0.5 rounded text-xs font-medium border backdrop-blur-sm',
                    selectedLog.new_status === 'approved' ? 'bg-green-400/20 text-green-300 border-green-400/30' :
                    selectedLog.new_status === 'rejected' ? 'bg-red-400/20 text-red-300 border-red-400/30' :
                    selectedLog.new_status === 'under_review' ? 'bg-blue-400/20 text-blue-300 border-blue-400/30' :
                    'bg-yellow-400/20 text-yellow-300 border-yellow-400/30'
                  ]">
                    {{ selectedLog.new_status.replace('_', ' ') }}
                  </span>
                </div>
              </div>

              <div v-if="selectedLog.documents_checked">
                <label class="block text-sm font-medium text-white/70">Documents Checked</label>
                <div class="mt-1 space-y-1">
                  <div v-for="(checked, document) in selectedLog.documents_checked" :key="document" class="text-sm">
                    <span :class="checked ? 'text-green-400' : 'text-red-400'">
                      {{ checked ? '✓' : '✗' }}
                    </span>
                    <span class="text-white/70 ml-1">{{ document.replace('_', ' ').replace(/([A-Z])/g, ' $1').toLowerCase() }}</span>
                  </div>
                </div>
              </div>

              <div v-if="selectedLog.reason">
                <label class="block text-sm font-medium text-white/70">Reason/Notes</label>
                <p class="mt-1 text-sm text-white bg-white/10 p-3 rounded border border-white/20 backdrop-blur-sm">{{ selectedLog.reason }}</p>
              </div>

              <div class="grid grid-cols-2 gap-4 text-xs text-white/50">
                <div>
                  <label class="block font-medium text-white/70">IP Address</label>
                  <p>{{ selectedLog.ip_address || 'N/A' }}</p>
                </div>
                <div>
                  <label class="block font-medium text-white/70">User Agent</label>
                  <p class="truncate">{{ selectedLog.user_agent || 'N/A' }}</p>
                </div>
              </div>
            </div>

            <div class="mt-6 flex justify-end">
              <button
                @click="selectedLog = null"
                class="bg-white/10 hover:bg-white/20 text-white font-medium py-2 px-4 rounded-md border border-white/20 transition-all duration-200 backdrop-blur-sm shadow-glow"
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
