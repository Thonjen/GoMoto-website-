<template>
  <AdminLayout>
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
              <h1 class="text-3xl font-bold text-white">Reports & Analytics</h1>
              <p class="mt-2 text-white/70">Comprehensive platform insights and statistics</p>
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
            <Link :href="route('admin.dashboard')" class="bg-white/10 hover:bg-white/20 text-white font-medium py-2 px-4 rounded-md border border-white/20 transition-all duration-200 backdrop-blur-sm shadow-glow text-center">
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
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
              </div>
              <div class="ml-5">
                <h3 class="text-sm font-medium text-white/60 uppercase tracking-wider">Platform Vehicles</h3>
                <p class="text-3xl font-bold text-white">{{ formatNumber(totalVehicles) }}</p>
                <p class="text-sm text-emerald-400 font-medium">{{ formatNumber(activeVehicles) }} available</p>
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
                <p class="text-3xl font-bold text-white">{{ formatCurrency(totalRevenue) }}</p>
                <p class="text-sm text-emerald-400 font-medium">{{ formatNumber(completedBookings) }} completed</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Total Users -->
        <div class="glass-card-dark group hover:scale-105 transition-all duration-300">
          <div class="p-5">
            <div class="flex items-center">
              <div class="p-4 rounded-2xl bg-gradient-to-br from-purple-500/20 to-purple-600/20 backdrop-blur-sm border border-purple-500/30 group-hover:from-purple-500/30 group-hover:to-purple-600/30 transition-all duration-300">
                <svg class="w-8 h-8 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5 0a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                </svg>
              </div>
              <div class="ml-5">
                <h3 class="text-sm font-medium text-white/60 uppercase tracking-wider">Community</h3>
                <p class="text-3xl font-bold text-white">{{ formatNumber(totalUsers) }}</p>
                <p class="text-sm text-purple-400 font-medium">{{ formatNumber(totalOwners) }} owners</p>
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
                <p class="text-3xl font-bold text-white">{{ formatNumber(totalBookings) }}</p>
                <p class="text-sm text-orange-400 font-medium">{{ formatNumber(pendingBookings) }} pending</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Platform Health & Monthly Trends -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-8 mb-8">
        <!-- Platform Health -->
        <div class="glass-card-dark">
          <div class="p-6 border-b border-white/10">
            <h3 class="text-xl font-bold text-white flex items-center">
              <svg class="w-5 h-5 mr-3 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
              </svg>
              Platform Health
            </h3>
          </div>
          <div class="p-6 space-y-6">
            <div class="text-center p-6 bg-gradient-to-br from-emerald-500/10 to-emerald-600/10 rounded-2xl border border-emerald-500/20">
              <div class="text-3xl font-bold text-emerald-400 mb-2">{{ vehicleAvailability }}%</div>
              <div class="text-sm text-white/60 uppercase tracking-wider">Vehicle Availability</div>
            </div>
            <div class="text-center p-6 bg-gradient-to-br from-blue-500/10 to-blue-600/10 rounded-2xl border border-blue-500/20">
              <div class="text-3xl font-bold text-blue-400 mb-2">{{ completionRate }}%</div>
              <div class="text-sm text-white/60 uppercase tracking-wider">Completion Rate</div>
            </div>
            <div class="text-center p-6 bg-gradient-to-br from-purple-500/10 to-purple-600/10 rounded-2xl border border-purple-500/20">
              <div class="text-3xl font-bold text-purple-400 mb-2">{{ averageBookingValue.toFixed(0) }}</div>
              <div class="text-sm text-white/60 uppercase tracking-wider">Avg. Booking Value</div>
            </div>
          </div>
        </div>

        <!-- Revenue Breakdown -->
        <div class="lg:col-span-2 glass-card-dark">
          <div class="p-6 border-b border-white/10">
            <h3 class="text-xl font-bold text-white flex items-center">
              <svg class="w-5 h-5 mr-3 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
              </svg>
              Revenue Breakdown
            </h3>
          </div>
          <div class="p-6">
            <div v-if="revenueData.length > 0" class="overflow-x-auto">
              <table class="w-full">
                <thead>
                  <tr class="text-left text-sm text-white/60 border-b border-white/10">
                    <th class="pb-3 font-medium">Date</th>
                    <th class="pb-3 font-medium text-right">Revenue</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                  <tr v-for="revenue in revenueData" :key="revenue.date" class="text-white hover:bg-white/5 transition-colors">
                    <td class="py-3">{{ formatDate(revenue.date) }}</td>
                    <td class="py-3 text-right font-medium text-emerald-400">{{ formatCurrency(Number(revenue.total)) }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div v-else class="text-center py-12">
              <svg class="mx-auto h-12 w-12 text-white/40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
              </svg>
              <p class="mt-2 text-sm text-white/70">No revenue data available</p>
              <p class="text-xs text-white/50">There are no bookings for this period</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Top Performing Vehicles -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-8 mb-8">
        <!-- Most Popular Vehicles -->
        <div class="glass-card-dark">
          <div class="p-6 border-b border-white/10">
            <h3 class="text-xl font-bold text-white flex items-center">
              <svg class="w-5 h-5 mr-3 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
              </svg>
              Most Popular Vehicles
            </h3>
          </div>
          <div class="p-6">
            <div v-if="vehicleUtilization.length > 0" class="space-y-4">
              <div v-for="(vehicle, index) in vehicleUtilization.slice(0, 5)" :key="vehicle.id" 
                   class="flex items-center justify-between p-4 bg-white/5 rounded-xl border border-white/10 hover:bg-white/10 transition-all duration-200">
                <div class="flex items-center space-x-4">
                  <div class="flex-shrink-0 h-10 w-10 bg-gradient-to-br from-amber-500/20 to-amber-600/20 rounded-full flex items-center justify-center border border-amber-500/30">
                    <span class="text-sm font-bold text-amber-400">{{ index + 1 }}</span>
                  </div>
                  <div>
                    <p class="text-sm font-medium text-white">{{ vehicle.make?.name }} {{ vehicle.model?.name }}</p>
                    <p class="text-xs text-white/60">{{ vehicle.license_plate }}</p>
                  </div>
                </div>
                <div class="text-right">
                  <p class="text-lg font-bold text-white">{{ vehicle.bookings_count }}</p>
                  <p class="text-xs text-white/50">bookings</p>
                </div>
              </div>
            </div>
            <div v-else class="text-center py-12">
              <svg class="mx-auto h-12 w-12 text-white/40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
              </svg>
              <p class="mt-2 text-sm text-white/70">No vehicle data available</p>
              <p class="text-xs text-white/50">There are no bookings for this period</p>
            </div>
          </div>
        </div>

        <!-- Top Users -->
        <div class="glass-card-dark">
          <div class="p-6 border-b border-white/10">
            <h3 class="text-xl font-bold text-white flex items-center">
              <svg class="w-5 h-5 mr-3 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5 0a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
              </svg>
              Top Users by Bookings
            </h3>
          </div>
          <div class="p-6">
            <div v-if="topUsers.length > 0" class="space-y-4">
              <div v-for="(user, index) in topUsers.slice(0, 5)" :key="user.id" 
                   class="flex items-center justify-between p-4 bg-white/5 rounded-xl border border-white/10 hover:bg-white/10 transition-all duration-200">
                <div class="flex items-center space-x-4">
                  <div class="flex-shrink-0 h-10 w-10 bg-gradient-to-br from-pink-500/20 to-pink-600/20 rounded-full flex items-center justify-center border border-pink-500/30">
                    <span class="text-sm font-bold text-pink-400">{{ index + 1 }}</span>
                  </div>
                  <div>
                    <p class="text-sm font-medium text-white">{{ user.first_name }} {{ user.last_name }}</p>
                    <p class="text-xs text-white/60">{{ user.email }}</p>
                  </div>
                </div>
                <div class="text-right">
                  <p class="text-lg font-bold text-white">{{ user.bookings_count }}</p>
                  <p class="text-xs text-white/50">bookings</p>
                </div>
              </div>
            </div>
            <div v-else class="text-center py-12">
              <svg class="mx-auto h-12 w-12 text-white/40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
              </svg>
              <p class="mt-2 text-sm text-white/70">No user data available</p>
              <p class="text-xs text-white/50">There are no bookings for this period</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Vehicle Categories & Booking Status -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-8 mb-8">
        <!-- Most Popular Vehicle Categories -->
        <div class="glass-card-dark">
          <div class="p-6 border-b border-white/10">
            <h3 class="text-xl font-bold text-white flex items-center">
              <svg class="w-5 h-5 mr-3 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
              </svg>
              Most Popular Vehicle Categories
            </h3>
          </div>
          <div class="p-6">
            <div v-if="vehicleCategories.length > 0" class="space-y-4">
              <div v-for="(category, index) in vehicleCategories.slice(0, 6)" :key="index" 
                   class="p-4 bg-white/5 rounded-xl border border-white/10 hover:bg-white/10 transition-all duration-200">
                <div class="flex items-center justify-between mb-3">
                  <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0 h-10 w-10 bg-gradient-to-br from-indigo-500/20 to-indigo-600/20 rounded-full flex items-center justify-center border border-indigo-500/30">
                      <span class="text-sm font-bold text-indigo-400">{{ index + 1 }}</span>
                    </div>
                    <div>
                      <p class="text-base font-semibold text-white">{{ category.name }}</p>
                      <p class="text-xs text-white/50">{{ category.type }}</p>
                    </div>
                  </div>
                  <div class="text-right">
                    <p class="text-lg font-bold text-white">{{ category.booking_count }}</p>
                    <p class="text-xs text-white/50">bookings</p>
                  </div>
                </div>
                <div class="grid grid-cols-2 gap-3 text-sm">
                  <div class="bg-white/5 rounded-lg p-2 border border-white/10">
                    <p class="text-white/60 text-xs">Vehicles</p>
                    <p class="text-white font-medium">{{ category.vehicle_count }}</p>
                  </div>
                  <div class="bg-white/5 rounded-lg p-2 border border-white/10">
                    <p class="text-white/60 text-xs">Revenue</p>
                    <p class="text-emerald-400 font-medium">{{ formatCurrency(category.total_revenue) }}</p>
                  </div>
                </div>
              </div>
            </div>
            <div v-else class="text-center py-12">
              <svg class="mx-auto h-12 w-12 text-white/40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
              </svg>
              <p class="mt-2 text-sm text-white/70">No category data available</p>
              <p class="text-xs text-white/50">There are no bookings for this period</p>
            </div>
          </div>
        </div>

        <!-- Booking Status Distribution -->
        <div class="glass-card-dark">
          <div class="p-6 border-b border-white/10">
            <h3 class="text-xl font-bold text-white flex items-center">
              <svg class="w-5 h-5 mr-3 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
              </svg>
              Booking Status Distribution
            </h3>
          </div>
          <div class="p-6">
            <div v-if="Object.keys(bookingData).length > 0" class="space-y-4 max-h-80 overflow-y-auto custom-scrollbar">
              <div v-for="(bookings, date) in bookingData" :key="date" class="p-4 bg-white/5 rounded-xl border border-white/10">
                <div class="font-medium text-white mb-3">{{ formatDate(date) }}</div>
                <div class="grid grid-cols-2 gap-3">
                  <div v-for="booking in bookings" :key="booking.status" class="text-center p-3 bg-white/5 rounded-lg border border-white/10">
                    <div class="text-2xl font-bold text-white">{{ booking.count }}</div>
                    <div class="text-xs text-white/60 uppercase tracking-wider mt-1">{{ booking.status }}</div>
                  </div>
                </div>
              </div>
            </div>
            <div v-else class="text-center py-12">
              <svg class="mx-auto h-12 w-12 text-white/40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
              </svg>
              <p class="mt-2 text-sm text-white/70">No booking data available</p>
              <p class="text-xs text-white/50">There are no bookings for this period</p>
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
  vehicleUtilization: Array,
  vehicleCategories: Array,
  platformStats: {
    type: Object,
    default: () => ({
      total_vehicles: 0,
      active_vehicles: 0,
      total_users: 0,
      total_owners: 0,
      total_bookings: 0,
      completed_bookings: 0,
      pending_bookings: 0,
      total_revenue: 0
    })
  }
})

const selectedPeriod = ref(props.period)

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

const totalRevenue = computed(() => {
  return props.revenueData.reduce((sum, item) => sum + Number(item.total), 0)
})

const totalBookings = computed(() => {
  return Object.values(props.bookingData).reduce((sum, bookings) => {
    return sum + bookings.reduce((daySum, booking) => daySum + booking.count, 0)
  }, 0)
})

const totalUsers = computed(() => {
  return props.platformStats?.total_users || 0
})

const totalOwners = computed(() => {
  return props.platformStats?.total_owners || 0
})

const totalVehicles = computed(() => {
  return props.platformStats?.total_vehicles || props.vehicleUtilization.length
})

const activeVehicles = computed(() => {
  return props.platformStats?.active_vehicles || 0
})

const completedBookings = computed(() => {
  return props.platformStats?.completed_bookings || 0
})

const pendingBookings = computed(() => {
  return props.platformStats?.pending_bookings || 0
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

const vehicleAvailability = computed(() => {
  return totalVehicles.value > 0 ? Math.round((activeVehicles.value / totalVehicles.value) * 100) : 0
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

const downloadPDF = () => {
  window.open(route('admin.reports.pdf', { period: selectedPeriod.value }), '_blank')
}
</script>

<style scoped>
/* Glassmorphism Card Style */
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
</style>
