<template>
  <AdminLayout>
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="mb-8">
        <div class="flex justify-between items-center">
          <div>
            <h1 class="text-3xl font-bold text-gray-900">Reports & Analytics</h1>
            <p class="mt-2 text-gray-600">System performance and analytics dashboard</p>
          </div>
          <div class="flex space-x-4">
            <select 
              v-model="selectedPeriod" 
              @change="updatePeriod"
              class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              <option value="7_days">Last 7 Days</option>
              <option value="30_days">Last 30 Days</option>
              <option value="90_days">Last 90 Days</option>
              <option value="1_year">Last Year</option>
            </select>
            <Link :href="route('admin.dashboard')" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
              Back to Dashboard
            </Link>
          </div>
        </div>
      </div>

      <!-- Summary Cards -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                </svg>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Total Revenue</dt>
                  <dd class="text-lg font-medium text-gray-900">₱{{ totalRevenue.toLocaleString() }}</dd>
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
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Total Bookings</dt>
                  <dd class="text-lg font-medium text-gray-900">{{ totalBookings }}</dd>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <svg class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5 0a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                </svg>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Active Users</dt>
                  <dd class="text-lg font-medium text-gray-900">{{ activeUsers }}</dd>
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
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Total Vehicles</dt>
                  <dd class="text-lg font-medium text-gray-900">{{ totalVehicles }}</dd>
                </dl>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Charts -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Revenue Chart -->
        <div class="bg-white shadow rounded-lg">
          <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Revenue Trend</h3>
            <div class="h-64 flex items-center justify-center">
              <div class="text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                <p class="mt-2 text-sm text-gray-500">Revenue chart placeholder</p>
                <p class="text-xs text-gray-400">Chart.js integration needed</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Booking Status Chart -->
        <div class="bg-white shadow rounded-lg">
          <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Booking Status Distribution</h3>
            <div class="h-64 flex items-center justify-center">
              <div class="text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                </svg>
                <p class="mt-2 text-sm text-gray-500">Pie chart placeholder</p>
                <p class="text-xs text-gray-400">Chart.js integration needed</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Top Users and Vehicles -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Top Users -->
        <div class="bg-white shadow rounded-lg">
          <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Top Users by Bookings</h3>
            <div class="space-y-4">
              <div v-for="(user, index) in topUsers" :key="user.id" class="flex items-center justify-between">
                <div class="flex items-center">
                  <div class="flex-shrink-0 h-8 w-8 bg-gray-300 rounded-full flex items-center justify-center">
                    <span class="text-xs font-medium text-gray-700">{{ index + 1 }}</span>
                  </div>
                  <div class="ml-3">
                    <p class="text-sm font-medium text-gray-900">{{ user.first_name }} {{ user.last_name }}</p>
                    <p class="text-sm text-gray-500">{{ user.email }}</p>
                  </div>
                </div>
                <div class="text-sm font-medium text-gray-900">{{ user.bookings_count }} bookings</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Top Vehicles -->
        <div class="bg-white shadow rounded-lg">
          <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Most Popular Vehicles</h3>
            <div class="space-y-4">
              <div v-for="(vehicle, index) in vehicleUtilization" :key="vehicle.id" class="flex items-center justify-between">
                <div class="flex items-center">
                  <div class="flex-shrink-0 h-8 w-8 bg-gray-300 rounded-full flex items-center justify-center">
                    <span class="text-xs font-medium text-gray-700">{{ index + 1 }}</span>
                  </div>
                  <div class="ml-3">
                    <p class="text-sm font-medium text-gray-900">{{ vehicle.make?.name }} {{ vehicle.model?.name }}</p>
                    <p class="text-sm text-gray-500">{{ vehicle.license_plate }} - {{ vehicle.owner?.first_name }} {{ vehicle.owner?.last_name }}</p>
                  </div>
                </div>
                <div class="text-sm font-medium text-gray-900">{{ vehicle.bookings_count }} bookings</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Raw Data Tables -->
      <div class="mt-8">
        <div class="bg-white shadow rounded-lg">
          <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Period Summary</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
              <div>
                <h4 class="text-md font-medium text-gray-900 mb-2">Revenue Breakdown</h4>
                <div class="space-y-2">
                  <div v-for="revenue in revenueData" :key="revenue.date" class="flex justify-between">
                    <span class="text-sm text-gray-600">{{ formatDate(revenue.date) }}</span>
                    <span class="text-sm font-medium">₱{{ Number(revenue.total).toLocaleString() }}</span>
                  </div>
                </div>
              </div>
              
              <div>
                <h4 class="text-md font-medium text-gray-900 mb-2">Booking Trends</h4>
                <div class="space-y-2">
                  <div v-for="(bookings, date) in bookingData" :key="date" class="text-sm">
                    <div class="font-medium text-gray-900">{{ formatDate(date) }}</div>
                    <div class="grid grid-cols-2 gap-2 text-xs text-gray-600">
                      <div v-for="booking in bookings" :key="booking.status">
                        {{ booking.status }}: {{ booking.count }}
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div>
                <h4 class="text-md font-medium text-gray-900 mb-2">Key Metrics</h4>
                <div class="space-y-2 text-sm">
                  <div class="flex justify-between">
                    <span class="text-gray-600">Avg. Booking Value:</span>
                    <span class="font-medium">₱{{ averageBookingValue.toLocaleString() }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-gray-600">Completion Rate:</span>
                    <span class="font-medium">{{ completionRate }}%</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-gray-600">Utilization Rate:</span>
                    <span class="font-medium">{{ utilizationRate }}%</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  period: String,
  revenueData: Array,
  bookingData: Object,
  topUsers: Array,
  vehicleUtilization: Array
})

const selectedPeriod = ref(props.period)

const totalRevenue = computed(() => {
  return props.revenueData.reduce((sum, item) => sum + Number(item.total), 0)
})

const totalBookings = computed(() => {
  return Object.values(props.bookingData).reduce((sum, bookings) => {
    return sum + bookings.reduce((daySum, booking) => daySum + booking.count, 0)
  }, 0)
})

const activeUsers = computed(() => {
  return props.topUsers.length
})

const totalVehicles = computed(() => {
  return props.vehicleUtilization.length
})

const averageBookingValue = computed(() => {
  return totalBookings.value > 0 ? totalRevenue.value / totalBookings.value : 0
})

const completionRate = computed(() => {
  const completed = Object.values(props.bookingData).reduce((sum, bookings) => {
    const completedBookings = bookings.find(b => b.status === 'completed')
    return sum + (completedBookings ? completedBookings.count : 0)
  }, 0)
  return totalBookings.value > 0 ? Math.round((completed / totalBookings.value) * 100) : 0
})

const utilizationRate = computed(() => {
  // This would need to be calculated based on actual vehicle availability vs bookings
  // Placeholder calculation
  return Math.round(Math.random() * 30 + 60) // 60-90% range
})

const updatePeriod = () => {
  router.get(route('admin.reports'), {
    period: selectedPeriod.value
  }, {
    preserveState: true,
    replace: true
  })
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric'
  })
}
</script>
