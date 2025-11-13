<script setup>
import { ref, computed } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
    vehiclePerformance: Array,
    monthlyEarnings: Array,
    bookingStatusStats: Object,
    peakHours: Array,
    ownerStats: Object,
    recentFeedback: Array,
    period: String,
})

const selectedPeriod = ref(props.period || '30_days')

// Format currency
const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP'
    }).format(amount || 0)
}

// Format numbers
const formatNumber = (number) => {
    return new Intl.NumberFormat().format(number || 0)
}

// Function to get star rating display
const getStarRating = (rating) => {
    if (!rating) return 'No ratings'
    return '★'.repeat(Math.floor(rating)) + '☆'.repeat(5 - Math.floor(rating)) + ` (${rating})`
}

// Function to get status color
const getStatusColor = (status) => {
    const colors = {
        pending: 'bg-yellow-500/20 text-yellow-300 border-yellow-500/30',
        confirmed: 'bg-blue-500/20 text-blue-300 border-blue-500/30',
        completed: 'bg-green-500/20 text-green-300 border-green-500/30',
        cancelled: 'bg-red-500/20 text-red-300 border-red-500/30',
        rejected: 'bg-red-500/20 text-red-300 border-red-500/30'
    }
    return colors[status] || 'bg-gray-500/20 text-gray-300 border-gray-500/30'
}

const completionRate = computed(() => {
    return props.ownerStats.total_bookings > 0 
        ? Math.round((props.ownerStats.completed_bookings / props.ownerStats.total_bookings) * 100) 
        : 0
})

const vehicleAvailability = computed(() => {
    return props.ownerStats.total_vehicles > 0 
        ? Math.round((props.ownerStats.active_vehicles / props.ownerStats.total_vehicles) * 100) 
        : 0
})

const averageBookingValue = computed(() => {
    return props.ownerStats.total_bookings > 0 
        ? props.ownerStats.total_earnings / props.ownerStats.total_bookings 
        : 0
})

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric'
    })
}

const updatePeriod = () => {
    router.get(route('owner.statistics'), {
        period: selectedPeriod.value
    }, {
        preserveState: true,
        replace: true
    })
}

const downloadPDF = () => {
    window.open(route('owner.statistics.pdf', { period: selectedPeriod.value }), '_blank')
}
</script>

<template>
  <AuthenticatedLayout>
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="mb-8">
        <div class="flex flex-col space-y-4 sm:flex-row sm:justify-between sm:items-center sm:space-y-0">
          <div class="flex items-center space-x-3">
            <div class="p-2 bg-white/10 rounded-lg backdrop-blur-sm">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
              </svg>
            </div>
            <div>
              <h1 class="text-3xl font-bold text-white">Business Analytics</h1>
              <p class="mt-2 text-white/70">Comprehensive insights into your vehicle rental performance</p>
            </div>
          </div>
          <div class="flex flex-col space-y-2 sm:flex-row sm:space-y-0 sm:space-x-4">
            <select 
              v-model="selectedPeriod" 
              @change="updatePeriod"
              class="bg-white/10 border border-white/20 rounded-md px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 backdrop-blur-sm"
            >
              <option value="7_days" class="bg-gray-800 text-white">Last 7 Days</option>
              <option value="30_days" class="bg-gray-800 text-white">Last 30 Days</option>
              <option value="90_days" class="bg-gray-800 text-white">Last 90 Days</option>
              <option value="1_year" class="bg-gray-800 text-white">Last Year</option>
            </select>
            <button 
              @click="downloadPDF" 
              class="bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white font-medium py-2 px-4 rounded-md border border-emerald-400/30 transition-all duration-200 backdrop-blur-sm shadow-lg flex items-center justify-center space-x-2"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
              <span>Download PDF</span>
            </button>
            <Link :href="route('owner.dashboard')" class="bg-white/10 hover:bg-white/20 text-white font-medium py-2 px-4 rounded-md border border-white/20 transition-all duration-200 backdrop-blur-sm shadow-glow text-center">
              Back to Dashboard
            </Link>
          </div>
        </div>
      </div>

      <!-- Summary Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-8">
        <!-- Total Vehicles -->
        <div class="glass-card-dark group hover:scale-105 transition-all duration-300">
          <div class="p-5">
            <div class="flex items-center">
              <div class="p-4 rounded-2xl bg-gradient-to-br from-blue-500/20 to-blue-600/20 backdrop-blur-sm border border-blue-500/30 group-hover:from-blue-500/30 group-hover:to-blue-600/30 transition-all duration-300">
                <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"></path>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l2.414 2.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0M15 17a2 2 0 104 0"></path>
                </svg>
              </div>
              <div class="ml-5">
                <h3 class="text-sm font-medium text-white/60 uppercase tracking-wider">My Vehicles</h3>
                <p class="text-3xl font-bold text-white">{{ formatNumber(ownerStats.total_vehicles) }}</p>
                <p class="text-sm text-emerald-400 font-medium">{{ formatNumber(ownerStats.active_vehicles) }} available</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Platform Revenue -->
        <div class="glass-card-dark group hover:scale-105 transition-all duration-300">
          <div class="p-5">
            <div class="flex items-center">
              <div class="p-4 rounded-2xl bg-gradient-to-br from-emerald-500/20 to-emerald-600/20 backdrop-blur-sm border border-emerald-500/30 group-hover:from-emerald-500/30 group-hover:to-emerald-600/30 transition-all duration-300">
                <svg class="w-8 h-8 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                </svg>
              </div>
              <div class="ml-5">
                <h3 class="text-sm font-medium text-white/60 uppercase tracking-wider">Total Revenue</h3>
                <p class="text-3xl font-bold text-white">{{ formatCurrency(ownerStats.total_earnings) }}</p>
                <p class="text-sm text-emerald-400 font-medium">{{ formatNumber(ownerStats.completed_bookings) }} completed</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Total Bookings -->
        <div class="glass-card-dark group hover:scale-105 transition-all duration-300">
          <div class="p-5">
            <div class="flex items-center">
              <div class="p-4 rounded-2xl bg-gradient-to-br from-amber-500/20 to-amber-600/20 backdrop-blur-sm border border-amber-500/30 group-hover:from-amber-500/30 group-hover:to-amber-600/30 transition-all duration-300">
                <svg class="w-8 h-8 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
              </div>
              <div class="ml-5">
                <h3 class="text-sm font-medium text-white/60 uppercase tracking-wider">Bookings</h3>
                <p class="text-3xl font-bold text-white">{{ formatNumber(ownerStats.total_bookings) }}</p>
                <p class="text-sm text-orange-400 font-medium">{{ formatNumber(ownerStats.pending_bookings) }} pending</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Average Rating -->
        <div class="glass-card-dark group hover:scale-105 transition-all duration-300">
          <div class="p-5">
            <div class="flex items-center">
              <div class="p-4 rounded-2xl bg-gradient-to-br from-purple-500/20 to-purple-600/20 backdrop-blur-sm border border-purple-500/30 group-hover:from-purple-500/30 group-hover:to-purple-600/30 transition-all duration-300">
                <svg class="w-8 h-8 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                </svg>
              </div>
              <div class="ml-5">
                <h3 class="text-sm font-medium text-white/60 uppercase tracking-wider">Rating</h3>
                <p class="text-3xl font-bold text-white">
                  {{ ownerStats.avg_vehicle_rating ? ownerStats.avg_vehicle_rating.toFixed(1) : 'N/A' }}
                </p>
                <p class="text-sm text-purple-400 font-medium">average score</p>
              </div>
            </div>
          </div>
        </div>
    </div>

                <!-- Monthly Earnings & Performance Grid -->
                <div class="grid grid-cols-1 xl:grid-cols-3 gap-8 mb-8 mt-8">
                    <!-- Monthly Earnings Trend -->
                    <div class="xl:col-span-2 glass-card">
                        <div class="p-6 border-b border-white/10">
                            <h3 class="text-xl font-bold text-white flex items-center">
                                <svg class="w-5 h-5 mr-3 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                </svg>
                                Revenue Performance Trend
                            </h3>
                        </div>
                        <div class="p-6">
                            <div v-if="monthlyEarnings.length > 0" class="space-y-6">
                                <!-- Chart Area -->
                                <div class="relative h-64 flex items-end justify-between space-x-2">
                                    <!-- Grid lines -->
                                    <div class="absolute inset-0 flex flex-col justify-between pointer-events-none">
                                        <div class="border-t border-white/5"></div>
                                        <div class="border-t border-white/5"></div>
                                        <div class="border-t border-white/5"></div>
                                        <div class="border-t border-white/5"></div>
                                        <div class="border-t border-white/10"></div>
                                    </div>
                                    
                                    <!-- Bars -->
                                    <div 
                                        v-for="(month, index) in monthlyEarnings" 
                                        :key="month.month"
                                        class="flex-1 relative group cursor-pointer"
                                        @click="$inertia.visit(route('owner.bookings.index'))"
                                    >
                                        <!-- Bar -->
                                        <div class="relative h-full flex flex-col justify-end">
                                            <div 
                                                class="w-full bg-gradient-to-t from-emerald-500 to-emerald-400 rounded-t-lg transition-all duration-500 ease-out hover:from-emerald-400 hover:to-emerald-300 relative overflow-hidden group-hover:shadow-lg group-hover:shadow-emerald-500/50"
                                                :style="{ 
                                                    height: monthlyEarnings.length > 0 
                                                        ? (month.earnings / Math.max(...monthlyEarnings.map(m => m.earnings)) * 100) + '%' 
                                                        : '0%',
                                                    minHeight: month.earnings > 0 ? '8px' : '0px'
                                                }"
                                            >
                                                <!-- Shimmer effect -->
                                                <div class="absolute inset-0 bg-gradient-to-t from-transparent via-white/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                            </div>
                                        </div>
                                        
                                        <!-- Tooltip on hover -->
                                        <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 opacity-0 group-hover:opacity-100 transition-all duration-200 pointer-events-none z-10">
                                            <div class="bg-gray-900 text-white text-xs rounded-lg py-2 px-3 shadow-xl border border-emerald-500/30 whitespace-nowrap">
                                                <div class="font-bold text-emerald-400">{{ formatCurrency(month.earnings) }}</div>
                                                <div class="text-white/70">{{ formatNumber(month.bookings) }} bookings</div>
                                            </div>
                                            <div class="absolute top-full left-1/2 transform -translate-x-1/2 -mt-1">
                                                <div class="border-4 border-transparent border-t-gray-900"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- X-axis labels -->
                                <div class="flex items-center justify-between space-x-2">
                                    <div 
                                        v-for="month in monthlyEarnings" 
                                        :key="month.month"
                                        class="flex-1 text-center"
                                    >
                                        <div class="text-xs text-white/60 font-medium truncate">{{ month.month }}</div>
                                    </div>
                                </div>
                                
                                <!-- Summary Stats -->
                                <div class="grid grid-cols-3 gap-4 pt-4 border-t border-white/10">
                                    <div class="text-center">
                                        <div class="text-2xl font-bold text-emerald-400">
                                            {{ formatCurrency(monthlyEarnings.reduce((sum, m) => sum + m.earnings, 0)) }}
                                        </div>
                                        <div class="text-xs text-white/60 uppercase tracking-wider mt-1">Total Revenue</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-2xl font-bold text-blue-400">
                                            {{ formatNumber(monthlyEarnings.reduce((sum, m) => sum + m.bookings, 0)) }}
                                        </div>
                                        <div class="text-xs text-white/60 uppercase tracking-wider mt-1">Total Bookings</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-2xl font-bold text-purple-400">
                                            {{ formatCurrency(monthlyEarnings.reduce((sum, m) => sum + m.earnings, 0) / monthlyEarnings.reduce((sum, m) => sum + m.bookings, 0) || 0) }}
                                        </div>
                                        <div class="text-xs text-white/60 uppercase tracking-wider mt-1">Avg per Booking</div>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="text-center py-12">
                                <svg class="mx-auto h-12 w-12 text-white/40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                                <p class="mt-2 text-sm text-white/70">No revenue data available</p>
                                <p class="text-xs text-white/50">Start renting out your vehicles to see performance data</p>
                            </div>
                        </div>
                    </div>

                    <!-- Business Insights -->
                    <div class="glass-card">
                        <div class="p-6 border-b border-white/10">
                            <h3 class="text-xl font-bold text-white flex items-center">
                                <svg class="w-5 h-5 mr-3 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                Key Metrics
                            </h3>
                        </div>
                        <div class="p-6 space-y-6">
                            <div class="text-center p-6 bg-gradient-to-br from-emerald-500/10 to-emerald-600/10 rounded-2xl border border-emerald-500/20">
                                <div class="text-3xl font-bold text-emerald-400 mb-2">
                                    {{ ownerStats.total_bookings > 0 ? Math.round((ownerStats.completed_bookings / ownerStats.total_bookings) * 100) : 0 }}%
                                </div>
                                <div class="text-sm text-white/60 uppercase tracking-wider">Completion Rate</div>
                            </div>
                            <div class="text-center p-6 bg-gradient-to-br from-blue-500/10 to-blue-600/10 rounded-2xl border border-blue-500/20">
                                <div class="text-3xl font-bold text-blue-400 mb-2">
                                    {{ ownerStats.total_vehicles > 0 ? Math.round((ownerStats.active_vehicles / ownerStats.total_vehicles) * 100) : 0 }}%
                                </div>
                                <div class="text-sm text-white/60 uppercase tracking-wider">Vehicle Availability</div>
                            </div>
                            <div class="text-center p-6 bg-gradient-to-br from-purple-500/10 to-purple-600/10 rounded-2xl border border-purple-500/20">
                                <div class="text-2xl font-bold text-purple-400 mb-2">
                                    {{ ownerStats.total_bookings > 0 ? formatCurrency(ownerStats.total_earnings / ownerStats.total_bookings) : formatCurrency(0) }}
                                </div>
                                <div class="text-sm text-white/60 uppercase tracking-wider">Average Booking Value</div>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- Peak Hours & Customer Feedback Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8 mt-8">
                    <!-- Peak Booking Hours -->
                    <div class="glass-card">
                        <div class="p-6 border-b border-white/10">
                            <h3 class="text-xl font-bold text-white flex items-center">
                                <svg class="w-5 h-5 mr-3 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Peak Hours
                            </h3>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                <div v-for="hour in peakHours" :key="hour.hour" class="text-center p-4 bg-gradient-to-br from-indigo-500/10 to-indigo-600/10 rounded-xl border border-indigo-500/20 hover:scale-105 transition-transform duration-200">
                                    <div class="text-2xl font-bold text-indigo-400 mb-1">{{ hour.hour }}</div>
                                    <div class="text-xs text-white/60">{{ formatNumber(hour.count) }} bookings</div>
                                </div>
                                <div v-if="peakHours.length === 0" class="col-span-full text-center py-12 text-white/40">
                                    <svg class="w-12 h-12 mx-auto mb-4 text-white/20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <p>No peak hour data</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Booking Status Distribution -->

                                        <div class="glass-card">
                        <div class="p-6 border-b border-white/10">
                            <h3 class="text-xl font-bold text-white flex items-center">
                                <svg class="w-5 h-5 mr-3 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
                                </svg>
                                Booking Status Distribution
                            </h3>
                        </div>
                        <div class="p-6">
                            <div v-if="Object.keys(bookingStatusStats).length > 0" class="space-y-6">
                                <div v-for="(count, status) in bookingStatusStats" :key="status" class="space-y-2">
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="font-medium capitalize text-white/80">{{ status.replace('_', ' ') }}</span>
                                        <span class="font-bold text-white">{{ formatNumber(count) }}</span>
                                    </div>
                                    <div class="relative h-3 bg-white/5 rounded-full overflow-hidden">
                                        <div 
                                            class="absolute inset-y-0 left-0 rounded-full transition-all duration-500 ease-out"
                                            :class="{
                                                'bg-gradient-to-r from-yellow-500 to-yellow-600': status === 'pending',
                                                'bg-gradient-to-r from-blue-500 to-blue-600': status === 'confirmed',
                                                'bg-gradient-to-r from-green-500 to-green-600': status === 'completed',
                                                'bg-gradient-to-r from-red-500 to-red-600': status === 'cancelled' || status === 'rejected'
                                            }"
                                            :style="{ 
                                                width: Object.values(bookingStatusStats).reduce((a, b) => a + b, 0) > 0 
                                                    ? (count / Object.values(bookingStatusStats).reduce((a, b) => a + b, 0) * 100) + '%' 
                                                    : '0%' 
                                            }"
                                        >
                                            <div class="absolute inset-0 bg-gradient-to-r from-white/20 to-transparent"></div>
                                        </div>
                                    </div>
                                    <div class="text-right text-xs text-white/50">
                                        {{ Object.values(bookingStatusStats).reduce((a, b) => a + b, 0) > 0 
                                            ? ((count / Object.values(bookingStatusStats).reduce((a, b) => a + b, 0)) * 100).toFixed(1) 
                                            : '0' }}%
                                    </div>
                                </div>
                            </div>
                            <div v-else class="text-center py-12 text-white/40">
                                <svg class="w-12 h-12 mx-auto mb-4 text-white/20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                <p>No booking data available</p>
                            </div>
                        </div>
                    </div>
                </div>

                                <!-- Vehicle Performance & Status Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8 mt-8">
                    <!-- Top Performing Vehicles -->
                    <div class="glass-card">
                        <div class="p-6 border-b border-white/10">
                            <h3 class="text-xl font-bold text-white flex items-center">
                                <svg class="w-5 h-5 mr-3 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                                </svg>
                                Vehicle Performance
                            </h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                <Link 
                                    v-for="(vehicle, index) in vehiclePerformance.slice(0, 5)" 
                                    :key="vehicle.id"
                                    :href="route('owner.vehicles.show', vehicle.id)"
                                    class="flex items-center justify-between p-4 bg-white/5 rounded-xl border border-white/10 hover:bg-white/10 transition-all duration-200 cursor-pointer hover:scale-[1.02] group">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500/20 to-blue-600/20 rounded-full flex items-center justify-center border border-blue-500/30 group-hover:from-blue-500/30 group-hover:to-blue-600/30 transition-all duration-300">
                                            <span class="text-sm font-bold text-blue-400">#{{ index + 1 }}</span>
                                        </div>
                                        <div>
                                            <p class="text-white font-medium group-hover:text-blue-300 transition-colors duration-200">{{ vehicle.name }}</p>
                                            <p class="text-xs text-white/50">{{ vehicle.plate_number }}</p>
                                            <div class="flex items-center space-x-2 mt-1">
                                                <span class="text-yellow-400 text-xs">
                                                    {{ vehicle.avg_rating ? '★'.repeat(Math.floor(vehicle.avg_rating)) : 'No ratings' }}
                                                </span>
                                                <span :class="vehicle.is_available ? 'text-emerald-400' : 'text-red-400'" class="text-xs font-medium">
                                                    {{ vehicle.is_available ? '● Available' : '● Unavailable' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-emerald-400 font-bold group-hover:text-emerald-300 transition-colors duration-200">{{ formatCurrency(vehicle.total_earnings) }}</p>
                                        <p class="text-xs text-white/50">{{ formatNumber(vehicle.booking_count) }} rentals</p>
                                        <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-200 mt-1">
                                            <svg class="w-4 h-4 text-blue-400 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </div>
                                    </div>
                                </Link>
                                <div v-if="vehiclePerformance.length === 0" class="text-center py-12 text-white/40">
                                    <svg class="w-12 h-12 mx-auto mb-4 text-white/20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H9m0 0H5m2 0v-2a2 2 0 012-2h2a2 2 0 012 2v2z"></path>
                                    </svg>
                                    <p>No vehicles found</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Customer Feedback -->
                    <div class="glass-card">
                        <div class="p-6 border-b border-white/10">
                            <h3 class="text-xl font-bold text-white flex items-center">
                                <svg class="w-5 h-5 mr-3 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                                Customer Feedback
                            </h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4 max-h-80 overflow-y-auto custom-scrollbar">
                                <div v-for="feedback in recentFeedback" :key="feedback.id" 
                                     class="p-4 bg-white/5 rounded-xl border border-white/10 hover:bg-white/10 transition-all duration-200">
                                    <div class="flex items-start justify-between mb-3">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-8 h-8 bg-gradient-to-br from-pink-500/20 to-pink-600/20 rounded-full flex items-center justify-center border border-pink-500/30">
                                                <span class="text-xs font-bold text-pink-400">{{ feedback.customer_name.charAt(0) }}</span>
                                            </div>
                                            <div>
                                                <span class="font-medium text-white">{{ feedback.customer_name }}</span>
                                                <div class="flex items-center space-x-2 text-xs text-white/50">
                                                    <span>{{ feedback.plate_number }}</span>
                                                    <span>•</span>
                                                    <span>{{ new Date(feedback.created_at).toLocaleDateString() }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-yellow-400 text-sm">
                                            {{ '★'.repeat(Math.floor(feedback.rating)) }}{{ '☆'.repeat(5 - Math.floor(feedback.rating)) }}
                                        </div>
                                    </div>
                                    <p class="text-white/70 text-sm leading-relaxed">{{ feedback.comment }}</p>
                                </div>
                                <div v-if="recentFeedback.length === 0" class="text-center py-12 text-white/40">
                                    <svg class="w-12 h-12 mx-auto mb-4 text-white/20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                    </svg>
                                    <p>No recent feedback</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
    </AuthenticatedLayout>
</template>

<style scoped>
/* Glassmorphism Card Style */
.glass-card {
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 1rem;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
}

.glass-card-dark {
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 1rem;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    transition: all 0.3s ease;
}

.glass-card-dark:hover {
    border-color: rgba(255, 255, 255, 0.2);
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
    transform: translateY(-2px);
}

/* Custom Scrollbar */
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 9999px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.2);
    border-radius: 9999px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.3);
}

/* Hover animations */
.glass-card:hover {
    border-color: rgba(255, 255, 255, 0.2);
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
    transform: translateY(-2px);
}

/* Glow effect for interactive elements */
.glass-card .group:hover {
    box-shadow: 0 0 30px rgba(255, 255, 255, 0.1);
}
</style>
