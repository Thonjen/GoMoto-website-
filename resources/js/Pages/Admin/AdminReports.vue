<template>
  <AppLayout>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
      <aside class="md:col-span-1 glass-card-dark p-6 h-fit">
        <h2 class="text-xl font-semibold text-white mb-4">Admin Panel</h2>
        <nav class="flex flex-col gap-2">
          <Link href="/admin/users" class="text-white/70 hover:text-white hover:underline transition-colors">User Management</Link>
          <Link href="/admin/vehicles" class="text-white/70 hover:text-white hover:underline transition-colors">Vehicle Listings</Link>
          <Link href="/admin/bookings" class="text-white/70 hover:text-white hover:underline transition-colors">Bookings</Link>
          <Link href="/admin/licenses" class="text-white/70 hover:text-white hover:underline transition-colors">License Verifications</Link>
          <Link href="/admin/disputes" class="text-white/70 hover:text-white hover:underline transition-colors">Disputes</Link>
          <Link href="/admin/reports" class="text-blue-400 font-medium hover:underline">Reports</Link>
        </nav>
      </aside>

      <div class="md:col-span-3 glass-card-dark p-6">
        <h1 class="text-3xl font-bold text-white mb-6">System Reports</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
          <div class="bg-blue-400/20 p-6 rounded-lg shadow-glow backdrop-blur-sm border border-white/20 flex items-center gap-4">
            <Users class="h-10 w-10 text-blue-400" />
            <div>
              <p class="text-sm text-white/70">Total Users</p>
              <p class="text-3xl font-bold text-white">{{ reports.totalUsers }}</p>
            </div>
          </div>
          <div class="bg-green-400/20 p-6 rounded-lg shadow-glow backdrop-blur-sm border border-white/20 flex items-center gap-4">
            <Car class="h-10 w-10 text-green-400" />
            <div>
              <p class="text-sm text-white/70">Total Vehicles Listed</p>
              <p class="text-3xl font-bold text-white">{{ reports.totalVehicles }}</p>
            </div>
          </div>
          <div class="bg-yellow-400/20 p-6 rounded-lg shadow-glow backdrop-blur-sm border border-white/20 flex items-center gap-4">
            <CalendarCheck class="h-10 w-10 text-yellow-400" />
            <div>
              <p class="text-sm text-white/70">Total Bookings</p>
              <p class="text-3xl font-bold text-white">{{ reports.totalBookings }}</p>
            </div>
          </div>
          <div class="bg-purple-400/20 p-6 rounded-lg shadow-glow backdrop-blur-sm border border-white/20 flex items-center gap-4">
            <DollarSign class="h-10 w-10 text-purple-400" />
            <div>
              <p class="text-sm text-white/70">Total Revenue</p>
              <p class="text-3xl font-bold text-white">₱{{ reports.totalRevenue.toLocaleString() }}</p>
            </div>
          </div>
          <div class="bg-red-400/20 p-6 rounded-lg shadow-glow backdrop-blur-sm border border-white/20 flex items-center gap-4">
            <AlertTriangle class="h-10 w-10 text-red-400" />
            <div>
              <p class="text-sm text-white/70">Open Disputes</p>
              <p class="text-3xl font-bold text-white">{{ reports.openDisputes }}</p>
            </div>
          </div>
        </div>

        <h2 class="text-2xl font-semibold text-white mb-4">Recent Activity Log</h2>
        <div v-if="activityLog.length > 0" class="overflow-x-auto">
          <table class="min-w-full glass-card border border-white/20 rounded-lg">
            <thead>
              <tr class="bg-white/10 backdrop-blur-sm text-left text-sm font-semibold text-white">
                <th class="py-3 px-4 border-b border-white/20">Timestamp</th>
                <th class="py-3 px-4 border-b border-white/20">User</th>
                <th class="py-3 px-4 border-b border-white/20">Action</th>
                <th class="py-3 px-4 border-b border-white/20">Details</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="log in activityLog" :key="log.id" class="hover:bg-white/5 border-b border-white/10 last:border-b-0 transition-colors">
                <td class="py-3 px-4 text-white/90">{{ log.timestamp }}</td>
                <td class="py-3 px-4 text-white/90">{{ log.user }}</td>
                <td class="py-3 px-4 text-white/90">{{ log.action }}</td>
                <td class="py-3 px-4 text-white/90">{{ log.details }}</td>
              </tr>
            </tbody>
          </table>
        </div>
        <p v-else class="text-white/70 text-center py-8">No recent activity.</p>
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
