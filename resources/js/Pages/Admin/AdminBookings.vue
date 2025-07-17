<template>
  <AppLayout>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
      <aside class="md:col-span-1 bg-white p-6 rounded-lg shadow-md h-fit">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Admin Panel</h2>
        <nav class="flex flex-col gap-2">
          <Link href="/admin/users" class="text-gray-700 hover:underline">User Management</Link>
          <Link href="/admin/vehicles" class="text-gray-700 hover:underline">Vehicle Listings</Link>
          <Link href="/admin/bookings" class="text-primary-600 font-medium hover:underline">Bookings</Link>
          <Link href="/admin/licenses" class="text-gray-700 hover:underline">License Verifications</Link>
          <Link href="/admin/disputes" class="text-gray-700 hover:underline">Disputes</Link>
          <Link href="/admin/reports" class="text-gray-700 hover:underline">Reports</Link>
        </nav>
      </aside>

      <div class="md:col-span-3 bg-white p-6 rounded-lg shadow-md">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">All System Bookings</h1>

        <div class="mb-6">
          <input type="text" v-model="searchQuery" placeholder="Search bookings by vehicle, renter, or owner..."
            class="w-full p-3 border border-gray-300 rounded-md focus:ring-primary-400 focus:border-primary-400" />
        </div>

        <div v-if="filteredBookings.length > 0" class="overflow-x-auto">
          <table class="min-w-full bg-white border border-gray-200 rounded-lg">
            <thead>
              <tr class="bg-gray-100 text-left text-sm font-semibold text-gray-700">
                <th class="py-3 px-4 border-b">Booking ID</th>
                <th class="py-3 px-4 border-b">Vehicle</th>
                <th class="py-3 px-4 border-b">Renter</th>
                <th class="py-3 px-4 border-b">Owner</th>
                <th class="py-3 px-4 border-b">Dates</th>
                <th class="py-3 px-4 border-b">Total Price</th>
                <th class="py-3 px-4 border-b">Status</th>
                <th class="py-3 px-4 border-b">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="booking in filteredBookings" :key="booking.id" class="hover:bg-gray-50 border-b border-gray-100 last:border-b-0">
                <td class="py-3 px-4 text-gray-700">{{ booking.id }}</td>
                <td class="py-3 px-4">
                  <div class="font-medium text-gray-800">{{ booking.vehicleName }}</div>
                </td>
                <td class="py-3 px-4 text-gray-700">{{ booking.renterName }}</td>
                <td class="py-3 px-4 text-gray-700">{{ booking.ownerName }}</td>
                <td class="py-3 px-4 text-gray-700">{{ booking.pickupDate }} - {{ booking.returnDate }}</td>
                <td class="py-3 px-4 font-bold text-primary-600">₱{{ booking.totalPrice.toLocaleString() }}</td>
                <td class="py-3 px-4">
                  <span :class="['px-3 py-1 rounded-full text-xs font-medium',
                    booking.status === 'Pending' ? 'bg-yellow-100 text-yellow-800' :
                    booking.status === 'Confirmed' ? 'bg-green-100 text-green-800' :
                    booking.status === 'Completed' ? 'bg-blue-100 text-blue-800' :
                    'bg-red-100 text-red-800']">
                    {{ booking.status }}
                  </span>
                </td>
                <td class="py-3 px-4">
                  <button @click="cancelBooking(booking.id)"
                    class="bg-red-500 text-white px-3 py-1 rounded-md text-xs font-medium hover:bg-red-600">
                    Cancel
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <p v-else class="text-gray-600 text-center py-8">No bookings found.</p>
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
