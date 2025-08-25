<template>
  <AdminLayout>
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="mb-8">
        <div class="flex justify-between items-center">
          <div>
            <h1 class="text-3xl font-bold text-gray-900">Payment Management</h1>
            <p class="mt-2 text-gray-600">Monitor and manage all payments in the system</p>
          </div>
          <Link :href="route('admin.dashboard')" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
            Back to Dashboard
          </Link>
        </div>
      </div>

      <!-- Filters -->
      <div class="bg-white shadow rounded-lg mb-6">
        <div class="px-4 py-5 sm:p-6">
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
              <input 
                type="text" 
                v-model="searchQuery" 
                placeholder="Search by reference number or user..."
                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                @input="updateFilters"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
              <select 
                v-model="selectedStatus" 
                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                @change="updateFilters"
              >
                <option value="">All Status</option>
                <option value="pending">Pending</option>
                <option value="confirmed">Confirmed</option>
                <option value="rejected">Rejected</option>
                <option value="refunded">Refunded</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
              <select 
                v-model="selectedType" 
                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                @change="updateFilters"
              >
                <option value="">All Types</option>
                <option value="booking">Booking Payment</option>
                <option value="overcharge">Overcharge Payment</option>
                <option value="extension">Extension Payment</option>
                <option value="refund">Refund</option>
              </select>
            </div>
            <div class="flex items-end">
              <button 
                @click="clearFilters"
                class="w-full bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded"
              >
                Clear Filters
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Payments Table -->
      <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="px-4 py-5 sm:p-6">
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Details</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Booking</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="payment in payments.data" :key="payment.id" class="hover:bg-gray-50">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div>
                      <div class="text-sm font-medium text-gray-900">{{ payment.reference_number || 'N/A' }}</div>
                      <div class="text-sm text-gray-500">{{ payment.type }}</div>
                      <div class="text-sm text-gray-500">{{ payment.paymentMode?.name || 'Unknown' }}</div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div v-if="payment.booking">
                      <div class="text-sm text-gray-900">Booking #{{ payment.booking.id }}</div>
                      <div class="text-sm text-gray-500">{{ payment.booking.vehicle?.license_plate || 'N/A' }}</div>
                    </div>
                    <div v-else class="text-sm text-gray-500">No booking</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div v-if="payment.booking?.user">
                      <div class="text-sm font-medium text-gray-900">
                        {{ payment.booking.user.first_name }} {{ payment.booking.user.last_name }}
                      </div>
                      <div class="text-sm text-gray-500">{{ payment.booking.user.email }}</div>
                    </div>
                    <div v-else class="text-sm text-gray-500">Unknown user</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium" :class="{
                      'text-green-600': payment.amount > 0,
                      'text-red-600': payment.amount < 0
                    }">
                      ₱{{ Math.abs(payment.amount).toLocaleString() }}
                      <span v-if="payment.amount < 0" class="text-xs text-red-500">(Refund)</span>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                          :class="{
                            'bg-yellow-100 text-yellow-800': payment.status === 'pending',
                            'bg-green-100 text-green-800': payment.status === 'confirmed' || payment.status === 'completed',
                            'bg-red-100 text-red-800': payment.status === 'rejected',
                            'bg-blue-100 text-blue-800': payment.status === 'refunded'
                          }">
                      {{ payment.status }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    <div>{{ formatDate(payment.paid_at || payment.created_at) }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <div class="flex space-x-2">
                      <button 
                        v-if="payment.status === 'pending'"
                        @click="confirmPayment(payment.id)"
                        class="text-green-600 hover:text-green-900"
                      >
                        Confirm
                      </button>
                      <button 
                        v-if="payment.status === 'pending'"
                        @click="rejectPayment(payment.id)"
                        class="text-red-600 hover:text-red-900"
                      >
                        Reject
                      </button>
                      <button 
                        v-if="payment.receipt_image"
                        @click="viewReceipt(payment.receipt_image)"
                        class="text-blue-600 hover:text-blue-900"
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
                  class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                >
                  Previous
                </Link>
                <Link 
                  v-if="payments.next_page_url" 
                  :href="payments.next_page_url" 
                  class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                >
                  Next
                </Link>
              </div>
              <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                  <p class="text-sm text-gray-700">
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
                        'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                        link.active 
                          ? 'z-10 bg-blue-50 border-blue-500 text-blue-600' 
                          : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
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
      <div v-if="showReceiptModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border max-w-lg shadow-lg rounded-md bg-white">
          <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Payment Receipt</h3>
            <div class="text-center">
              <img :src="receiptUrl" alt="Payment Receipt" class="max-w-full h-auto rounded-lg" />
            </div>
            <div class="flex justify-end mt-4">
              <button 
                @click="showReceiptModal = false"
                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400"
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
