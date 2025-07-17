<template>
  <AppLayout>
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Dashboard</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
      <div class="bg-white p-6 rounded-lg shadow-md flex items-center gap-4">
        <Car class="h-10 w-10 text-primary-600" />
        <div>
          <p class="text-sm text-gray-600">Total Vehicles Listed</p>
          <p class="text-2xl font-bold text-gray-800">{{ dashboardData.totalVehicles }}</p>
        </div>
      </div>
      <div class="bg-white p-6 rounded-lg shadow-md flex items-center gap-4">
        <ShoppingCart class="h-10 w-10 text-green-600" />
        <div>
          <p class="text-sm text-gray-600">Active Bookings (Renter)</p>
          <p class="text-2xl font-bold text-gray-800">{{ dashboardData.activeRenterBookings }}</p>
        </div>
      </div>
      <div class="bg-white p-6 rounded-lg shadow-md flex items-center gap-4">
        <CalendarCheck class="h-10 w-10 text-blue-600" />
        <div>
          <p class="text-sm text-gray-600">Pending Booking Requests (Owner)</p>
          <p class="text-2xl font-bold text-gray-800">{{ dashboardData.pendingOwnerRequests }}</p>
        </div>
      </div>
      <div class="bg-white p-6 rounded-lg shadow-md flex items-center gap-4">
        <DollarSign class="h-10 w-10 text-yellow-600" />
        <div>
          <p class="text-sm text-gray-600">Total Earnings (Owner)</p>
          <p class="text-2xl font-bold text-gray-800">₱{{ dashboardData.totalEarnings.toLocaleString() }}</p>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Recent Bookings (Renter)</h2>
        <ul v-if="dashboardData.recentRenterBookings.length > 0" class="space-y-4">
          <li v-for="booking in dashboardData.recentRenterBookings" :key="booking.id"
            class="border-b border-gray-200 pb-4 last:border-b-0 last:pb-0">
            <div class="flex justify-between items-center">
              <div>
                <p class="font-semibold text-gray-800">{{ booking.vehicleName }}</p>
                <p class="text-sm text-gray-600">{{ booking.dates }}</p>
              </div>
              <span :class="['px-3 py-1 rounded-full text-xs font-medium',
                booking.status === 'Confirmed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800']">
                {{ booking.status }}
              </span>
            </div>
            <Link :href="`/my-bookings/${booking.id}`" class="text-sm text-primary-600 hover:underline mt-2 block">View Details</Link>
          </li>
        </ul>
        <p v-else class="text-gray-600">No recent bookings as a renter.</p>
        <Link href="/my-bookings" class="mt-4 inline-block text-primary-600 hover:underline">View All My Bookings</Link>
      </div>

      <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Recent Booking Requests (Owner)</h2>
        <ul v-if="dashboardData.recentOwnerRequests.length > 0" class="space-y-4">
          <li v-for="request in dashboardData.recentOwnerRequests" :key="request.id"
            class="border-b border-gray-200 pb-4 last:border-b-0 last:pb-0">
            <div class="flex justify-between items-center">
              <div>
                <p class="font-semibold text-gray-800">{{ request.vehicleName }}</p>
                <p class="text-sm text-gray-600">From: {{ request.renterName }}</p>
                <p class="text-sm text-gray-600">{{ request.dates }}</p>
              </div>
              <Link :href="`/my-vehicles/bookings/${request.id}`"
                class="bg-primary-600 text-white px-4 py-2 rounded-md text-sm hover:bg-primary-700 transition-colors">
                Review
              </Link>
            </div>
          </li>
        </ul>
        <p v-else class="text-gray-600">No recent booking requests as an owner.</p>
        <Link href="/my-vehicles/bookings" class="mt-4 inline-block text-primary-600 hover:underline">View All Booking Requests</Link>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Car, ShoppingCart, CalendarCheck, DollarSign } from 'lucide-vue-next';

const dashboardData = ref({
  totalVehicles: 3,
  activeRenterBookings: 1,
  pendingOwnerRequests: 2,
  totalEarnings: 15000,
  recentRenterBookings: [
    { id: 101, vehicleName: 'Honda Civic', dates: '2025-07-20 - 2025-07-25', status: 'Confirmed' },
    { id: 102, vehicleName: 'Toyota Vios', dates: '2025-07-10 - 2025-07-12', status: 'Completed' },
  ],
  recentOwnerRequests: [
    { id: 201, vehicleName: 'Ford Everest', renterName: 'Maria Santos', dates: '2025-08-01 - 2025-08-05' },
    { id: 202, vehicleName: 'Suzuki Swift', renterName: 'Pedro Reyes', dates: '2025-07-28 - 2025-07-30' },
  ],
});

// In a real Inertia app, this data would be passed as props from the controller
</script>

<style scoped>
/* Tailwind CSS is used for styling */
</style>
