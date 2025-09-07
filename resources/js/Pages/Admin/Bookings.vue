<template>
  <AdminLayout>
    <div class="min-h-screen py-6" style="background: linear-gradient(135deg, #535862 0%, #3a3f4a 100%);">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="mb-8">
        <div class="flex justify-between items-center">
          <div>
            <h1 class="text-3xl font-bold text-white">Booking Management</h1>
            <p class="mt-2 text-white/70">Monitor and manage all bookings in the system</p>
          </div>
          <Link :href="route('admin.dashboard')" class="bg-white/10 hover:bg-white/20 text-white font-bold py-2 px-4 rounded-md backdrop-blur-sm border border-white/20 transition-colors">
            Back to Dashboard
          </Link>
        </div>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-yellow-400/20 shadow-glow rounded-lg backdrop-blur-sm border border-white/20 overflow-hidden">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-yellow-400 rounded-full flex items-center justify-center">
                  <span class="text-white text-sm font-bold">P</span>
                </div>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-white/70 truncate">Pending</dt>
                  <dd class="text-lg font-medium text-white">{{ bookingStats.pending || 0 }}</dd>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-green-400/20 shadow-glow rounded-lg backdrop-blur-sm border border-white/20 overflow-hidden">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-green-400 rounded-full flex items-center justify-center">
                  <span class="text-white text-sm font-bold">C</span>
                </div>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-white/70 truncate">Confirmed</dt>
                  <dd class="text-lg font-medium text-white">{{ bookingStats.confirmed || 0 }}</dd>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-blue-400/20 shadow-glow rounded-lg backdrop-blur-sm border border-white/20 overflow-hidden">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-blue-400 rounded-full flex items-center justify-center">
                  <span class="text-white text-sm font-bold">A</span>
                </div>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-white/70 truncate">Active</dt>
                  <dd class="text-lg font-medium text-white">{{ bookingStats.active || 0 }}</dd>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-purple-400/20 shadow-glow rounded-lg backdrop-blur-sm border border-white/20 overflow-hidden">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-purple-400 rounded-full flex items-center justify-center">
                  <span class="text-white text-sm font-bold">T</span>
                </div>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-white/70 truncate">Total</dt>
                  <dd class="text-lg font-medium text-white">{{ bookings.total || 0 }}</dd>
                </dl>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Filters -->
      <div class="glass-card-dark backdrop-blur-sm border border-white/20 shadow-glow mb-6">
        <div class="px-4 py-5 sm:p-6">
          <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
              <label class="block text-sm font-medium text-white/70 mb-2">Search</label>
              <input 
                type="text" 
                v-model="searchQuery" 
                placeholder="Search by user or vehicle..."
                class="w-full bg-white/10 border border-white/20 rounded-md px-3 py-2 text-white placeholder:text-white/50 focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50 backdrop-blur-sm"
                @input="updateFilters"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-white/70 mb-2">Status</label>
              <select 
                v-model="selectedStatus" 
                class="w-full bg-white/10 border border-white/20 rounded-md px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50 backdrop-blur-sm"
                @change="updateFilters"
              >
                <option value="" class="bg-gray-800">All Status</option>
                <option value="pending" class="bg-gray-800">Pending</option>
                <option value="confirmed" class="bg-gray-800">Confirmed</option>
                <option value="completed" class="bg-gray-800">Completed</option>
                <option value="cancelled" class="bg-gray-800">Cancelled</option>
                <option value="refunded" class="bg-gray-800">Refunded</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-white/70 mb-2">Date From</label>
              <input 
                type="date" 
                v-model="dateFrom" 
                class="w-full bg-white/10 border border-white/20 rounded-md px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50 backdrop-blur-sm"
                @change="updateFilters"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-white/70 mb-2">Date To</label>
              <input 
                type="date" 
                v-model="dateTo" 
                class="w-full bg-white/10 border border-white/20 rounded-md px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50 backdrop-blur-sm"
                @change="updateFilters"
              />
            </div>
            <div class="flex items-end">
              <button 
                @click="clearFilters"
                class="w-full bg-gray-500/80 hover:bg-gray-600/80 text-white font-bold py-2 px-4 rounded backdrop-blur-sm border border-gray-400/30 transition-all duration-200"
              >
                Clear Filters
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Bookings Table -->
      <div class="glass-card-dark backdrop-blur-sm border border-white/20 shadow-glow rounded-lg overflow-hidden">
        <div class="px-4 py-5 sm:p-6">
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-white/20">
              <thead class="bg-white/5 backdrop-blur-sm">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-white/70 uppercase tracking-wider">Booking Details</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-white/70 uppercase tracking-wider">Customer</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-white/70 uppercase tracking-wider">Vehicle</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-white/70 uppercase tracking-wider">Duration</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-white/70 uppercase tracking-wider">Amount</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-white/70 uppercase tracking-wider">Status</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-white/70 uppercase tracking-wider">Actions</th>
                </tr>
              </thead>
              <tbody class="bg-white/5 divide-y divide-white/10 backdrop-blur-sm">
                <tr v-for="booking in bookings.data" :key="booking.id" class="hover:bg-white/10 transition-colors duration-200">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div>
                      <div class="text-sm font-medium text-white">Booking #{{ booking.id }}</div>
                      <div class="text-sm text-white/70">{{ formatDate(booking.created_at) }}</div>
                      <div v-if="booking.overcharges && booking.overcharges.length > 0" class="text-xs text-red-400">
                        {{ booking.overcharges.length }} overcharge(s)
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div>
                      <div class="text-sm font-medium text-white">
                        {{ booking.user.first_name }} {{ booking.user.last_name }}
                      </div>
                      <div class="text-sm text-white/70">{{ booking.user.email }}</div>
                      <div class="text-sm text-white/70">{{ booking.user.phone || 'No phone' }}</div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div>
                      <div class="text-sm font-medium text-white">
                        {{ booking.vehicle.make?.name }} {{ booking.vehicle.model?.name }}
                      </div>
                      <div class="text-sm text-white/70">{{ booking.vehicle.license_plate }}</div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-white/70">
                    <div>{{ formatDate(booking.pickup_datetime) }}</div>
                    <div>to</div>
                    <div>{{ formatDate(booking.return_time) }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-white">₱{{ Number(booking.total_amount).toLocaleString() }}</div>
                    <div v-if="booking.payment" class="text-xs" :class="{
                      'text-green-400': booking.payment.status === 'confirmed',
                      'text-yellow-400': booking.payment.status === 'pending',
                      'text-red-400': booking.payment.status === 'rejected'
                    }">
                      Payment: {{ booking.payment.status }}
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full backdrop-blur-sm border"
                          :class="{
                            'bg-yellow-400/20 text-yellow-300 border-yellow-400/30': booking.status === 'pending',
                            'bg-green-400/20 text-green-300 border-green-400/30': booking.status === 'confirmed',
                            'bg-blue-400/20 text-blue-300 border-blue-400/30': booking.status === 'completed',
                            'bg-red-400/20 text-red-300 border-red-400/30': booking.status === 'cancelled',
                            'bg-purple-400/20 text-purple-300 border-purple-400/30': booking.status === 'refunded'
                          }">
                      {{ booking.status }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <div class="flex flex-col space-y-1">
                      <button 
                        @click="viewBooking(booking.id)"
                        class="text-blue-400 hover:text-blue-300 text-left transition-colors duration-200"
                      >
                        View Details
                      </button>
                      <button 
                        v-if="booking.status === 'confirmed'"
                        @click="cancelBooking(booking.id)"
                        class="text-red-400 hover:text-red-300 text-left transition-colors duration-200"
                      >
                        Cancel
                      </button>
                      <button 
                        v-if="booking.status === 'cancelled' && booking.payment?.status === 'confirmed'"
                        @click="showRefundModal(booking)"
                        class="text-purple-400 hover:text-purple-300 text-left transition-colors duration-200"
                      >
                        Process Refund
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div v-if="bookings.links" class="mt-6">
            <nav class="flex items-center justify-between">
              <div class="flex-1 flex justify-between sm:hidden">
                <Link 
                  v-if="bookings.prev_page_url" 
                  :href="bookings.prev_page_url" 
                  class="relative inline-flex items-center px-4 py-2 bg-white/10 border border-white/20 text-sm font-medium rounded-md text-white hover:bg-white/20 backdrop-blur-sm transition-all duration-200"
                >
                  Previous
                </Link>
                <Link 
                  v-if="bookings.next_page_url" 
                  :href="bookings.next_page_url" 
                  class="ml-3 relative inline-flex items-center px-4 py-2 bg-white/10 border border-white/20 text-sm font-medium rounded-md text-white hover:bg-white/20 backdrop-blur-sm transition-all duration-200"
                >
                  Next
                </Link>
              </div>
              <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                  <p class="text-sm text-white/70">
                    Showing {{ bookings.from }} to {{ bookings.to }} of {{ bookings.total }} results
                  </p>
                </div>
                <div>
                  <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                    <Link 
                      v-for="link in bookings.links" 
                      :key="link.label"
                      :href="link.url || '#'"
                      v-html="link.label"
                      :class="[
                        'relative inline-flex items-center px-4 py-2 border text-sm font-medium backdrop-blur-sm transition-all duration-200',
                        link.active 
                          ? 'z-10 bg-blue-500/20 border-blue-400/50 text-blue-300' 
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

      <!-- Refund Modal -->
      <div v-if="showRefundForm" class="fixed inset-0 bg-black/50 backdrop-blur-sm overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border border-white/20 w-96 shadow-glow rounded-xl glass-card-dark backdrop-blur-lg">
          <div class="mt-3">
            <h3 class="text-lg font-medium text-white mb-4">Process Refund</h3>
            <form @submit.prevent="processRefund">
              <div class="mb-4">
                <label class="block text-sm font-medium text-white/70 mb-2">Refund Amount</label>
                <input 
                  type="number" 
                  step="0.01"
                  v-model="refundForm.amount" 
                  :max="refundForm.maxAmount"
                  class="w-full bg-white/10 border border-white/20 rounded-md px-3 py-2 text-white placeholder:text-white/50 focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50 backdrop-blur-sm"
                  required
                />
                <p class="text-xs text-white/50 mt-1">Maximum: ₱{{ refundForm.maxAmount }}</p>
              </div>
              <div class="mb-4">
                <label class="block text-sm font-medium text-white/70 mb-2">Reason</label>
                <textarea 
                  v-model="refundForm.reason" 
                  rows="3"
                  class="w-full bg-white/10 border border-white/20 rounded-md px-3 py-2 text-white placeholder:text-white/50 focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50 backdrop-blur-sm"
                  placeholder="Reason for refund..."
                  required
                ></textarea>
              </div>
              <div class="flex justify-end space-x-2">
                <button 
                  type="button" 
                  @click="showRefundForm = false"
                  class="px-4 py-2 bg-gray-500/80 text-white rounded-md hover:bg-gray-600/80 backdrop-blur-sm border border-gray-400/30 transition-all duration-200"
                >
                  Cancel
                </button>
                <button 
                  type="submit"
                  class="px-4 py-2 bg-purple-600/80 text-white rounded-md hover:bg-purple-700/80 backdrop-blur-sm border border-purple-500/30 transition-all duration-200"
                >
                  Process Refund
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  </AdminLayout>
</template>

<script setup>
import { ref, reactive, computed } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  bookings: Object,
  filters: Object
})

const searchQuery = ref(props.filters.search || '')
const selectedStatus = ref(props.filters.status || '')
const dateFrom = ref(props.filters.date_from || '')
const dateTo = ref(props.filters.date_to || '')
const showRefundForm = ref(false)
const refundForm = reactive({
  bookingId: null,
  amount: 0,
  maxAmount: 0,
  reason: ''
})

const bookingStats = computed(() => {
  const data = props.bookings.data || []
  return {
    pending: data.filter(b => b.status === 'pending').length,
    confirmed: data.filter(b => b.status === 'confirmed').length,
    active: data.filter(b => b.status === 'confirmed' && !b.return_time).length,
    total: props.bookings.total || 0
  }
})

const updateFilters = () => {
  router.get(route('admin.bookings'), {
    search: searchQuery.value,
    status: selectedStatus.value,
    date_from: dateFrom.value,
    date_to: dateTo.value
  }, {
    preserveState: true,
    replace: true
  })
}

const clearFilters = () => {
  searchQuery.value = ''
  selectedStatus.value = ''
  dateFrom.value = ''
  dateTo.value = ''
  updateFilters()
}

const viewBooking = (bookingId) => {
  // Navigate to booking details page
router.get(route('bookings.show', bookingId))
}

const cancelBooking = (bookingId) => {
  if (confirm('Are you sure you want to cancel this booking?')) {
    router.post(route('admin.bookings.cancel', bookingId), {}, {
      onSuccess: () => {
        // Handle success
      }
    })
  }
}

const showRefundModal = (booking) => {
  refundForm.bookingId = booking.id
  refundForm.amount = booking.total_amount
  refundForm.maxAmount = booking.total_amount
  refundForm.reason = ''
  showRefundForm.value = true
}

const processRefund = () => {
  router.post(route('admin.bookings.refund', refundForm.bookingId), {
    refund_amount: refundForm.amount,
    refund_reason: refundForm.reason
  }, {
    onSuccess: () => {
      showRefundForm.value = false
    }
  })
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
