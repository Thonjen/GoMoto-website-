<template>
  <AppLayout>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
      <aside class="md:col-span-1 bg-white p-6 rounded-lg shadow-md h-fit">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Admin Panel</h2>
        <nav class="flex flex-col gap-2">
          <Link href="/admin/users" class="text-gray-700 hover:underline">User Management</Link>
          <Link href="/admin/vehicles" class="text-gray-700 hover:underline">Vehicle Listings</Link>
          <Link href="/admin/bookings" class="text-gray-700 hover:underline">Bookings</Link>
          <Link href="/admin/licenses" class="text-gray-700 hover:underline">License Verifications</Link>
          <Link href="/admin/disputes" class="text-gray-700 hover:underline">Disputes</Link>
          <Link href="/admin/reports" class="text-primary-600 font-medium hover:underline">Reports</Link>
        </nav>
      </aside>

      <div class="md:col-span-3 bg-white p-6 rounded-lg shadow-md">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">System Reports</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
          <div class="bg-blue-50 p-6 rounded-lg shadow-md flex items-center gap-4">
            <Users class="h-10 w-10 text-blue-600" />
            <div>
              <p class="text-sm text-gray-600">Total Users</p>
              <p class="text-3xl font-bold text-gray-800">{{ reports.totalUsers }}</p>
            </div>
          </div>
          <div class="bg-green-50 p-6 rounded-lg shadow-md flex items-center gap-4">
            <Car class="h-10 w-10 text-green-600" />
            <div>
              <p class="text-sm text-gray-600">Total Vehicles Listed</p>
              <p class="text-3xl font-bold text-gray-800">{{ reports.totalVehicles }}</p>
            </div>
          </div>
          <div class="bg-yellow-50 p-6 rounded-lg shadow-md flex items-center gap-4">
            <CalendarCheck class="h-10 w-10 text-yellow-600" />
            <div>
              <p class="text-sm text-gray-600">Total Bookings</p>
              <p class="text-3xl font-bold text-gray-800">{{ reports.totalBookings }}</p>
            </div>
          </div>
          <div class="bg-purple-50 p-6 rounded-lg shadow-md flex items-center gap-4">
            <DollarSign class="h-10 w-10 text-purple-600" />
            <div>
              <p class="text-sm text-gray-600">Total Revenue</p>
              <p class="text-3xl font-bold text-gray-800">₱{{ reports.totalRevenue.toLocaleString() }}</p>
            </div>
          </div>
          <div class="bg-red-50 p-6 rounded-lg shadow-md flex items-center gap-4">
            <AlertTriangle class="h-10 w-10 text-red-600" />
            <div>
              <p class="text-sm text-gray-600">Open Disputes</p>
              <p class="text-3xl font-bold text-gray-800">{{ reports.openDisputes }}</p>
            </div>
          </div>
        </div>

        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Recent Activity Log</h2>
        <div v-if="activityLog.length > 0" class="overflow-x-auto">
          <table class="min-w-full bg-white border border-gray-200 rounded-lg">
            <thead>
              <tr class="bg-gray-100 text-left text-sm font-semibold text-gray-700">
                <th class="py-3 px-4 border-b">Timestamp</th>
                <th class="py-3 px-4 border-b">User</th>
                <th class="py-3 px-4 border-b">Action</th>
                <th class="py-3 px-4 border-b">Details</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="log in activityLog" :key="log.id" class="hover:bg-gray-50 border-b border-gray-100 last:border-b-0">
                <td class="py-3 px-4 text-gray-700">{{ log.timestamp }}</td>
                <td class="py-3 px-4 text-gray-700">{{ log.user }}</td>
                <td class="py-3 px-4 text-gray-700">{{ log.action }}</td>
                <td class="py-3 px-4 text-gray-700">{{ log.details }}</td>
              </tr>
            </tbody>
          </table>
        </div>
        <p v-else class="text-gray-600 text-center py-8">No recent activity.</p>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Users, Car, CalendarCheck, DollarSign, AlertTriangle } from 'lucide-vue-next';

const reports = ref({
  totalUsers: 1250,
  totalVehicles: 340,
  totalBookings: 890,
  totalRevenue: 1250000, // PHP
  openDisputes: 7,
});

const activityLog = ref([
  { id: 1, timestamp: '2025-07-18 10:30 AM', user: 'Admin', action: 'Approved License', details: 'License for John Doe (ID: 1)' },
  { id: 2, timestamp: '2025-07-18 09:45 AM', user: 'Jane Smith', action: 'Listed Vehicle', details: 'New vehicle: Honda CRV (ID: 2)' },
  { id: 3, timestamp: '2025-07-17 03:15 PM', user: 'Maria Santos', action: 'Booked Vehicle', details: 'Booking #102 for Honda CRV' },
  { id: 4, timestamp: '2025-07-17 11:00 AM', user: 'Admin', action: 'Resolved Dispute', details: 'Dispute #3 resolved' },
]);

// In a real Inertia app, this data would be passed as props from the controller
</script>

<style scoped>
/* Tailwind CSS is used for styling */
</style>
