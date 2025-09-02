<template>
  <AdminLayout>
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="mb-8">
        <div class="flex justify-between items-center">
          <div>
            <h1 class="text-3xl font-bold text-white">Payment Management</h1>
            <p class="mt-2 text-white/70">Monitor and manage all payments in the system</p>
          </div>
          <Link :href="route('admin.dashboard')" class="bg-white/10 hover:bg-white/20 text-white font-medium py-2 px-4 rounded-md border border-white/20 transition-all duration-200 backdrop-blur-sm shadow-glow">
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
                placeholder="Search by reference number or user..."
                class="w-full bg-white/10 border border-white/20 rounded-md px-3 py-2 text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 backdrop-blur-sm"
                @input="updateFilters"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-white mb-2">Status</label>
              <select 
                v-model="selectedStatus" 
                class="w-full bg-white/10 border border-white/20 rounded-md px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 backdrop-blur-sm"
                @change="updateFilters"
              >
                <option value="" class="bg-gray-800 text-white">All Status</option>
                <option value="pending" class="bg-gray-800 text-white">Pending</option>
                <option value="confirmed" class="bg-gray-800 text-white">Confirmed</option>
                <option value="rejected" class="bg-gray-800 text-white">Rejected</option>
                <option value="refunded" class="bg-gray-800 text-white">Refunded</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-white mb-2">Type</label>
              <select 
                v-model="selectedType" 
                class="w-full bg-white/10 border border-white/20 rounded-md px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 backdrop-blur-sm"
                @change="updateFilters"
              >
                <option value="" class="bg-gray-800 text-white">All Types</option>
                <option value="booking" class="bg-gray-800 text-white">Booking Payment</option>
                <option value="overcharge" class="bg-gray-800 text-white">Overcharge Payment</option>
                <option value="extension" class="bg-gray-800 text-white">Extension Payment</option>
                <option value="refund" class="bg-gray-800 text-white">Refund</option>
              </select>
            </div>
            <div class="flex items-end">
              <button 
                @click="clearFilters"
                class="w-full bg-white/10 hover:bg-white/20 text-white font-medium py-2 px-4 rounded-md border border-white/20 transition-all duration-200 backdrop-blur-sm shadow-glow"
              >
                Clear Filters
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Payments Table -->
      <div class="glass-card-dark overflow-hidden">
        <div class="px-4 py-5 sm:p-6">
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-white/20">
              <thead class="bg-white/5">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-white/70 uppercase tracking-wider">Payment Details</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-white/70 uppercase tracking-wider">Booking</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-white/70 uppercase tracking-wider">User</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-white/70 uppercase tracking-wider">Amount</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-white/70 uppercase tracking-wider">Status</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-white/70 uppercase tracking-wider">Date</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-white/70 uppercase tracking-wider">Actions</th>
                </tr>
              </thead>
              <tbody class="bg-transparent divide-y divide-white/10">
                <tr v-for="payment in payments.data" :key="payment.id" class="hover:bg-white/5 transition-colors duration-150">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div>
                      <div class="text-sm font-medium text-white">{{ payment.reference_number || 'N/A' }}</div>
                      <div class="text-sm text-white/70">{{ payment.type }}</div>
                      <div class="text-sm text-white/70">{{ payment.paymentMode?.name || 'Unknown' }}</div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div v-if="payment.booking">
                      <div class="text-sm text-white">Booking #{{ payment.booking.id }}</div>
                      <div class="text-sm text-white/70">{{ payment.booking.vehicle?.license_plate || 'N/A' }}</div>
                    </div>
                    <div v-else class="text-sm text-white/50">No booking</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div v-if="payment.booking?.user">
                      <div class="text-sm font-medium text-white">
                        {{ payment.booking.user.first_name }} {{ payment.booking.user.last_name }}
                      </div>
                      <div class="text-sm text-white/70">{{ payment.booking.user.email }}</div>
                    </div>
                    <div v-else class="text-sm text-white/50">Unknown user</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium" :class="{
                      'text-green-400': payment.amount > 0,
                      'text-red-400': payment.amount < 0
                    }">
                      ₱{{ Math.abs(payment.amount).toLocaleString() }}
                      <span v-if="payment.amount < 0" class="text-xs text-red-400">(Refund)</span>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full border backdrop-blur-sm"
                          :class="{
                            'bg-yellow-400/20 text-yellow-300 border-yellow-400/30': payment.status === 'pending',
                            'bg-green-400/20 text-green-300 border-green-400/30': payment.status === 'confirmed' || payment.status === 'completed',
                            'bg-red-400/20 text-red-300 border-red-400/30': payment.status === 'rejected',
                            'bg-blue-400/20 text-blue-300 border-blue-400/30': payment.status === 'refunded'
                          }">
                      {{ payment.status }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-white/70">
                    <div>{{ formatDate(payment.paid_at || payment.created_at) }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <div class="flex space-x-2">
                      <button 
                        v-if="payment.status === 'pending'"
                        @click="confirmPayment(payment.id)"
                        class="text-green-400 hover:text-green-300 transition-colors duration-150"
                      >
                        Confirm
                      </button>
                      <button 
                        v-if="payment.status === 'pending'"
                        @click="rejectPayment(payment.id)"
                        class="text-red-400 hover:text-red-300 transition-colors duration-150"
                      >
                        Reject
                      </button>
                      <button 
                        v-if="payment.receipt_image"
                        @click="viewReceipt(payment.receipt_image)"
                        class="text-blue-400 hover:text-blue-300 transition-colors duration-150"
                      >
                        View Receipt
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div v-if="payments.links" class="mt-6">
            <nav class="flex items-center justify-between">
              <div class="flex-1 flex justify-between sm:hidden">
                <Link 
                  v-if="payments.prev_page_url" 
                  :href="payments.prev_page_url" 
                  class="relative inline-flex items-center px-4 py-2 border border-white/20 text-sm font-medium rounded-md text-white bg-white/10 hover:bg-white/20 backdrop-blur-sm transition-all duration-200"
                >
                  Previous
                </Link>
                <Link 
                  v-if="payments.next_page_url" 
                  :href="payments.next_page_url" 
                  class="ml-3 relative inline-flex items-center px-4 py-2 border border-white/20 text-sm font-medium rounded-md text-white bg-white/10 hover:bg-white/20 backdrop-blur-sm transition-all duration-200"
                >
                  Next
                </Link>
              </div>
              <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                  <p class="text-sm text-white/70">
                    Showing {{ payments.from }} to {{ payments.to }} of {{ payments.total }} results
                  </p>
                </div>
                <div>
                  <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                    <Link 
                      v-for="link in payments.links" 
                      :key="link.label"
                      :href="link.url || '#'"
                      v-html="link.label"
                      :class="[
                        'relative inline-flex items-center px-4 py-2 border text-sm font-medium rounded backdrop-blur-sm transition-all duration-200',
                        link.active 
                          ? 'z-10 bg-blue-400/20 border-blue-400/30 text-blue-300' 
                          : 'bg-white/10 border-white/20 text-white/70 hover:bg-white/20 hover:text-white',
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

      <!-- Receipt Modal -->
      <div v-if="showReceiptModal" class="fixed inset-0 bg-black/50 overflow-y-auto h-full w-full z-50 backdrop-blur-sm">
        <div class="relative top-20 mx-auto p-5 border border-white/20 max-w-lg shadow-glow rounded-xl glass-card-dark">
          <div class="mt-3">
            <h3 class="text-lg font-medium text-white mb-4">Payment Receipt</h3>
            <div class="text-center">
              <img :src="receiptUrl" alt="Payment Receipt" class="max-w-full h-auto rounded-lg border border-white/20" />
            </div>
            <div class="flex justify-end mt-4">
              <button 
                @click="showReceiptModal = false"
                class="px-4 py-2 bg-white/10 hover:bg-white/20 text-white font-medium rounded-md border border-white/20 transition-all duration-200 backdrop-blur-sm shadow-glow"
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
import { ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  payments: Object,
  filters: Object
})

const searchQuery = ref(props.filters.search || '')
const selectedStatus = ref(props.filters.status || '')
const selectedType = ref(props.filters.type || '')
const showReceiptModal = ref(false)
const receiptUrl = ref('')

const updateFilters = () => {
  router.get(route('admin.payments'), {
    search: searchQuery.value,
    status: selectedStatus.value,
    type: selectedType.value
  }, {
    preserveState: true,
    replace: true
  })
}

const clearFilters = () => {
  searchQuery.value = ''
  selectedStatus.value = ''
  selectedType.value = ''
  updateFilters()
}

const confirmPayment = (paymentId) => {
  if (confirm('Are you sure you want to confirm this payment?')) {
    router.post(route('admin.payments.confirm', paymentId), {}, {
      onSuccess: () => {
        // Handle success
      }
    })
  }
}

const rejectPayment = (paymentId) => {
  if (confirm('Are you sure you want to reject this payment?')) {
    router.post(route('admin.payments.reject', paymentId), {}, {
      onSuccess: () => {
        // Handle success
      }
    })
  }
}

const viewReceipt = (receiptPath) => {
  receiptUrl.value = `/storage/${receiptPath}`
  showReceiptModal.value = true
}

const formatDate = (date) => {
  if (!date) return 'N/A'
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}
</script>
