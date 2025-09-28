<template>
  <AdminLayout>
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-white">Admin Dashboard</h1>
        <p class="mt-2 text-white/70">System overview and management</p>
      </div>

      <!-- Statistics Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Users Stats -->
        <div class="bg-blue-400/20 shadow-glow rounded-lg backdrop-blur-sm border border-white/20 overflow-hidden">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <svg class="h-6 w-6 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5 0a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                </svg>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-white/70 truncate">Total Users</dt>
                  <dd class="text-lg font-medium text-white">{{ stats.users.total.toLocaleString() }}</dd>
                </dl>
              </div>
            </div>
          </div>
          <div class="bg-white/10 px-5 py-3 backdrop-blur-sm">
            <div class="text-sm">
              <span class="text-green-400 font-medium">+{{ stats.users.new_this_month }}</span>
              <span class="text-white/70"> this month</span>
            </div>
          </div>
        </div>

        <!-- Vehicles Stats -->
        <div class="bg-green-400/20 shadow-glow rounded-lg backdrop-blur-sm border border-white/20 overflow-hidden">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <svg class="h-6 w-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-white/70 truncate">Total Vehicles</dt>
                  <dd class="text-lg font-medium text-white">{{ stats.vehicles.total.toLocaleString() }}</dd>
                </dl>
              </div>
            </div>
          </div>
          <div class="bg-white/10 px-5 py-3 backdrop-blur-sm">
            <div class="text-sm">
              <span class="text-blue-400 font-medium">{{ stats.vehicles.available }}</span>
              <span class="text-white/70"> available</span>
            </div>
          </div>
        </div>

        <!-- Bookings Stats -->
        <div class="bg-purple-400/20 shadow-glow rounded-lg backdrop-blur-sm border border-white/20 overflow-hidden">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <svg class="h-6 w-6 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-white/70 truncate">Total Bookings</dt>
                  <dd class="text-lg font-medium text-white">{{ stats.bookings.total.toLocaleString() }}</dd>
                </dl>
              </div>
            </div>
          </div>
          <div class="bg-white/10 px-5 py-3 backdrop-blur-sm">
            <div class="text-sm">
              <span class="text-yellow-400 font-medium">{{ stats.bookings.pending }}</span>
              <span class="text-white/70"> pending</span>
            </div>
          </div>
        </div>

        <!-- Revenue Stats -->
        <div class="bg-yellow-400/20 shadow-glow rounded-lg backdrop-blur-sm border border-white/20 overflow-hidden">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <svg class="h-6 w-6 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                </svg>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-white/70 truncate">Total Revenue</dt>
                  <dd class="text-lg font-medium text-white">₱{{ Number(stats.payments.total_revenue).toLocaleString() }}</dd>
                </dl>
              </div>
            </div>
          </div>
          <div class="bg-white/10 px-5 py-3 backdrop-blur-sm">
            <div class="text-sm">
              <span class="text-green-400 font-medium">₱{{ Number(stats.payments.this_month_revenue).toLocaleString() }}</span>
              <span class="text-white/70"> this month</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Quick Actions Panel -->
        <div class="glass-card-dark">
          <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-white mb-4">Quick Actions</h3>
            <div class="grid grid-cols-2 gap-4">
              <Link :href="route('admin.users')" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-400/20 hover:bg-blue-400/30 backdrop-blur-sm border-blue-400/30 transition-colors">
                <svg class="-ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5 0a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                </svg>
                Manage Users
              </Link>
              <Link :href="route('admin.kyc.verifications')" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-yellow-400/20 hover:bg-yellow-400/30 backdrop-blur-sm border-yellow-400/30 transition-colors">
                <svg class="-ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
                KYC Verification
              </Link>
              <Link :href="route('admin.vehicles')" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-400/20 hover:bg-green-400/30 backdrop-blur-sm border-green-400/30 transition-colors">
                <svg class="-ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                Manage Vehicles
              </Link>








  <!-- <button
    type="button"
    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 cursor-not-allowed"
    @click="showOverlay = true"
  >
    <svg class="-ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
    </svg>
    Manage Vehicles
  </button> -->

  <!-- Overlay -->
  <!-- <div
    v-if="showOverlay"
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
  >
    <div class="bg-white p-6 rounded-lg shadow-lg text-center">
      <h2 class="text-lg font-semibold text-gray-900 mb-2">🚧 Not Available Yet</h2>
      <p class="text-gray-600 mb-4">This feature is still under development.</p>
      <button
        @click="showOverlay = false"
        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700"
      >
        Close
      </button>
    </div>
  </div> -->







              <Link :href="route('admin.bookings')" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-purple-400/20 hover:bg-purple-400/30 backdrop-blur-sm border-purple-400/30 transition-colors">
                <svg class="-ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                View Bookings
              </Link>
              <Link :href="route('admin.reports')" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-400/20 hover:bg-red-400/30 backdrop-blur-sm border-red-400/30 transition-colors">
                <svg class="-ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                View Reports
              </Link>
              <Link :href="route('admin.statistics')" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-400/20 hover:bg-indigo-400/30 backdrop-blur-sm border-indigo-400/30 transition-colors">
                <svg class="-ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                Statistics
              </Link>
              <Link :href="route('admin.kyc.logs')" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-400/20 hover:bg-blue-400/30 backdrop-blur-sm border-blue-400/30 transition-colors">
                <svg class="-ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                KYC Logs
              </Link>
            </div>
          </div>
        </div>

        <!-- Pending Items -->
        <div class="glass-card-dark">
          <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-white mb-4">Pending Items</h3>
            <div class="space-y-4">
              <div class="flex items-center justify-between">
                <div class="flex items-center">
                  <div class="flex-shrink-0 h-2 w-2 bg-yellow-400 rounded-full"></div>
                  <p class="ml-3 text-sm text-white/70">KYC Verifications</p>
                </div>
                <span class="text-sm font-medium text-white">{{ stats.kyc?.pending_review || 0 }}</span>
              </div>
              <div class="flex items-center justify-between">
                <div class="flex items-center">
                  <div class="flex-shrink-0 h-2 w-2 bg-purple-400 rounded-full"></div>
                  <p class="ml-3 text-sm text-white/70">Pending Bookings</p>
                </div>
                <span class="text-sm font-medium text-white">{{ stats.bookings.pending }}</span>
              </div>
              <div class="flex items-center justify-between">
                <div class="flex items-center">
                  <div class="flex-shrink-0 h-2 w-2 bg-blue-400 rounded-full"></div>
                  <p class="ml-3 text-sm text-white/70">Pending Vehicle Approvals</p>
                </div>
                <span class="text-sm font-medium text-white">{{ stats.vehicles.pending_approval }}</span>
              </div>
              <div class="flex items-center justify-between">
                <div class="flex items-center">
                  <div class="flex-shrink-0 h-2 w-2 bg-green-400 rounded-full"></div>
                  <p class="ml-3 text-sm text-white/70">Extension Requests</p>
                </div>
                <span class="text-sm font-medium text-white">{{ stats.extensions.pending }}</span>
              </div>
              <div class="flex items-center justify-between">
                <div class="flex items-center">
                  <div class="flex-shrink-0 h-2 w-2 bg-red-400 rounded-full"></div>
                  <p class="ml-3 text-sm text-white/70">Unpaid Overcharges</p>
                </div>
                <span class="text-sm font-medium text-white">₱{{ Number(stats.overcharges.unpaid_amount).toLocaleString() }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Charts and Recent Activity -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Revenue Chart -->
        <div class="glass-card-dark">
          <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-white mb-4">Monthly Revenue</h3>
            <div class="h-64 flex items-center justify-center">
              <p class="text-white/70 text-center">Chart placeholder<br/><small class="text-white/50">Revenue data visualization would go here</small></p>
            </div>
          </div>
        </div>

        <!-- Recent Activities -->
        <div class="glass-card-dark">
          <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-white mb-4">Recent Activities</h3>
            <div class="flow-root max-h-64 overflow-y-auto pr-2">
              <ul class="-mb-8">
                <li v-for="(activity, index) in recentActivities" :key="activity.id">
                  <div class="relative pb-8" v-if="index < recentActivities.length - 1">
                    <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-white/20" aria-hidden="true"></span>
                    <div class="relative flex space-x-3">
                      <div class="h-8 w-8 bg-white/10 rounded-full flex items-center justify-center backdrop-blur-sm border border-white/20 flex-shrink-0">
                        <svg class="h-4 w-4 text-white/70" fill="currentColor" viewBox="0 0 20 20">
                          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                      </div>
                      <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                        <div class="min-w-0 flex-1">
                          <p class="text-sm text-white/90 break-words">{{ activity.description }}</p>
                          <p class="text-xs text-white/60 break-words">{{ activity.user }}</p>
                        </div>
                        <div class="text-right text-sm whitespace-nowrap text-white/70 flex-shrink-0">
                          {{ formatDate(activity.created_at) }}
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="relative flex space-x-3" v-else>
                    <div class="h-8 w-8 bg-white/10 rounded-full flex items-center justify-center backdrop-blur-sm border border-white/20 flex-shrink-0">
                      <svg class="h-4 w-4 text-white/70" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                      </svg>
                    </div>
                    <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                      <div class="min-w-0 flex-1">
                        <p class="text-sm text-white/90 break-words">{{ activity.description }}</p>
                        <p class="text-xs text-white/60 break-words">{{ activity.user }}</p>
                      </div>
                      <div class="text-right text-sm whitespace-nowrap text-white/70 flex-shrink-0">
                        {{ formatDate(activity.created_at) }}
                      </div>
                    </div>
                  </div>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>

      <!-- Top Performing Vehicles -->
      <div class="mt-8">
        <div class="glass-card-dark">
          <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-white mb-4">Top Performing Vehicles</h3>
            <div class="overflow-x-auto">
              <table class="min-w-full glass-card border border-white/20 rounded-lg">
                <thead class="bg-white/10 backdrop-blur-sm">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider border-b border-white/20">Vehicle</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider border-b border-white/20">Owner</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider border-b border-white/20">Bookings</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider border-b border-white/20">Revenue</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider border-b border-white/20">Rating</th>
                  </tr>
                </thead>
                <tbody class="glass-card divide-y divide-white/10">
                  <tr v-for="vehicle in topVehicles" :key="vehicle.id" class="hover:bg-white/5 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm font-medium text-white">{{ vehicle.make }} {{ vehicle.model }}</div>
                      <div class="text-sm text-white/70">{{ vehicle.license_plate }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm text-white/90">{{ vehicle.owner_name }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm font-medium text-white">{{ vehicle.total_bookings }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm font-medium text-white">₱{{ Number(vehicle.total_revenue).toLocaleString() }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="flex items-center">
                        <div class="text-sm font-medium text-white mr-2">{{ vehicle.average_rating?.toFixed(1) || 'N/A' }}</div>
                        <div class="flex text-yellow-400">
                          <svg v-for="star in 5" :key="star" :class="[
                            'h-4 w-4',
                            star <= (vehicle.average_rating || 0) ? 'text-yellow-400' : 'text-white/20'
                          ]" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                          </svg>
                        </div>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { ref } from "vue"

const showOverlay = ref(false)

defineProps({
  stats: Object,
  recentActivities: Array,
  monthlyRevenue: Array,
  topVehicles: Array
})

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
