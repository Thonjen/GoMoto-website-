<template>
  <AppLayout>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
      <aside class="md:col-span-1 glass-card-dark p-6 h-fit">
        <h2 class="text-xl font-semibold text-white mb-4">Admin Panel</h2>
        <nav class="flex flex-col gap-2">
          <Link href="/admin/users" class="text-white/70 hover:text-white hover:underline transition-colors">User Management</Link>
          <Link href="/admin/vehicles" class="text-white/70 hover:text-white hover:underline transition-colors">Vehicle Listings</Link>
          <Link href="/admin/bookings" class="text-blue-400 font-medium hover:underline">Bookings</Link>
          <Link href="/admin/licenses" class="text-white/70 hover:text-white hover:underline transition-colors">License Verifications</Link>
          <Link href="/admin/disputes" class="text-white/70 hover:text-white hover:underline transition-colors">Disputes</Link>
          <Link href="/admin/reports" class="text-white/70 hover:text-white hover:underline transition-colors">Reports</Link>
        </nav>
      </aside>

      <div class="md:col-span-3 glass-card-dark p-6">
        <h1 class="text-3xl font-bold text-white mb-6">All System Bookings</h1>

        <div class="mb-6">
          <input type="text" v-model="searchQuery" placeholder="Search bookings by vehicle, renter, or owner..."
            class="w-full p-3 bg-white/10 border border-white/20 rounded-md text-white placeholder-white/50 focus:ring-blue-400 focus:border-blue-400 backdrop-blur-sm" />
        </div>

        <div v-if="filteredBookings.length > 0" class="overflow-x-auto">
          <table class="min-w-full glass-card border border-white/20 rounded-lg">
            <thead>
              <tr class="bg-white/10 backdrop-blur-sm text-left text-sm font-semibold text-white">
                <th class="py-3 px-4 border-b border-white/20">Booking ID</th>
                <th class="py-3 px-4 border-b border-white/20">Vehicle</th>
                <th class="py-3 px-4 border-b border-white/20">Renter</th>
                <th class="py-3 px-4 border-b border-white/20">Owner</th>
                <th class="py-3 px-4 border-b border-white/20">Dates</th>
                <th class="py-3 px-4 border-b border-white/20">Total Price</th>
                <th class="py-3 px-4 border-b border-white/20">Status</th>
                <th class="py-3 px-4 border-b border-white/20">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="booking in filteredBookings" :key="booking.id" class="hover:bg-white/5 border-b border-white/10 last:border-b-0 transition-colors">
                <td class="py-3 px-4 text-white/90">{{ booking.id }}</td>
                <td class="py-3 px-4">
                  <div class="font-medium text-white">{{ booking.vehicleName }}</div>
                </td>
                <td class="py-3 px-4 text-white/90">{{ booking.renterName }}</td>
                <td class="py-3 px-4 text-white/90">{{ booking.ownerName }}</td>
                <td class="py-3 px-4 text-white/90">{{ booking.pickupDate }} - {{ booking.returnDate }}</td>
                <td class="py-3 px-4 font-bold text-green-400">₱{{ booking.totalPrice.toLocaleString() }}</td>
                <td class="py-3 px-4">
                  <span :class="['px-3 py-1 rounded-full text-xs font-medium backdrop-blur-sm',
                    booking.status === 'Pending' ? 'bg-yellow-400/20 text-yellow-400' :
                    booking.status === 'Confirmed' ? 'bg-green-400/20 text-green-400' :
                    booking.status === 'Completed' ? 'bg-blue-400/20 text-blue-400' :
                    'bg-red-400/20 text-red-400']">
                    {{ booking.status }}
                  </span>
                </td>
                <td class="py-3 px-4">
                  <button @click="cancelBooking(booking.id)"
                    class="bg-red-500/80 text-white px-3 py-1 rounded-md text-xs font-medium hover:bg-red-600 backdrop-blur-sm transition-colors">
                    Cancel
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <p v-else class="text-white/70 text-center py-8">No bookings found.</p>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const bookings = ref([
  { id: 1, vehicleName: 'Toyota Camry', renterName: 'John Doe', ownerName: 'Jane Smith', pickupDate: '2025-07-20', returnDate: '2025-07-25', totalPrice: 9000, status: 'Confirmed' },
  { id: 2, vehicleName: 'Honda CRV', renterName: 'Maria Santos', ownerName: 'Jane Smith', pickupDate: '2025-08-01', returnDate: '2025-08-05', totalPrice: 10000, status: 'Pending' },
  { id: 3, vehicleName: 'Ford Ranger', renterName: 'Peter Jones', ownerName: 'John Doe', pickupDate: '2025-06-10', returnDate: '2025-06-15', totalPrice: 12500, status: 'Completed' },
  { id: 4, vehicleName: 'Mitsubishi Mirage', renterName: 'Alice Brown', ownerName: 'Peter Jones', pickupDate: '2025-07-01', returnDate: '2025-07-03', totalPrice: 3600, status: 'Cancelled' },
]);

const searchQuery = ref('');

const filteredBookings = computed(() => {
  const query = searchQuery.value.toLowerCase();
  return bookings.value.filter(booking =>
    booking.vehicleName.toLowerCase().includes(query) ||
    booking.renterName.toLowerCase().includes(query) ||
    booking.ownerName.toLowerCase().includes(query)
  );
});

const cancelBooking = (id) => {
  if (confirm(`Are you sure you want to cancel booking #${id}?`)) {
    const booking = bookings.value.find(b => b.id === id);
    if (booking) {
      booking.status = 'Cancelled';
      alert(`Booking #${id} cancelled. (Not actually updated in this demo)`);
      // In a real Inertia app, this would be an Inertia.post or Inertia.put request
    }
  }
};
</script>

<style scoped>
/* Tailwind CSS is used for styling */
</style>
