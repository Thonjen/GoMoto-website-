<template>
  <AppLayout>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
      <aside class="md:col-span-1 bg-white p-6 rounded-lg shadow-md h-fit">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Renter Tools</h2>
        <nav class="flex flex-col gap-2">
          <Link href="/my-bookings" class="text-primary-600 font-medium hover:underline">My Bookings</Link>
        </nav>
      </aside>

      <div class="md:col-span-3 bg-white p-6 rounded-lg shadow-md">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">My Bookings</h1>

        <div v-if="bookings.length > 0" class="overflow-x-auto">
          <table class="min-w-full bg-white border border-gray-200 rounded-lg">
            <thead>
              <tr class="bg-gray-100 text-left text-sm font-semibold text-gray-700">
                <th class="py-3 px-4 border-b">Vehicle</th>
                <th class="py-3 px-4 border-b">Owner</th>
                <th class="py-3 px-4 border-b">Dates</th>
                <th class="py-3 px-4 border-b">Total Price</th>
                <th class="py-3 px-4 border-b">Status</th>
                <th class="py-3 px-4 border-b">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="booking in bookings" :key="booking.id" class="hover:bg-gray-50 border-b border-gray-100 last:border-b-0">
                <td class="py-3 px-4">
                  <div class="font-medium text-gray-800">{{ booking.vehicleName }}</div>
                  <div class="text-sm text-gray-600">{{ booking.vehicleType }}</div>
                </td>
                <td class="py-3 px-4">
                  <div class="font-medium text-gray-800">{{ booking.ownerName }}</div>
                </td>
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
                  <Link :href="`/my-bookings/${booking.id}`"
                    class="bg-primary-600 text-white px-4 py-2 rounded-md text-sm hover:bg-primary-700 transition-colors">
                    View
                  </Link>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <p v-else class="text-gray-600 text-center py-8">You have no bookings yet. Start searching for vehicles!</p>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const bookings = ref([
  {
    id: 1,
    vehicleName: 'Toyota Camry',
    vehicleType: 'Sedan',
    ownerName: 'Juan Dela Cruz',
    pickupDate: '2025-07-20',
    returnDate: '2025-07-25',
    totalPrice: 9000,
    status: 'Confirmed',
  },
  {
    id: 2,
    vehicleName: 'Honda CRV',
    vehicleType: 'SUV',
    ownerName: 'Maria Santos',
    pickupDate: '2025-08-01',
    returnDate: '2025-08-05',
    totalPrice: 10000,
    status: 'Pending',
  },
  {
    id: 3,
    vehicleName: 'Ford Ranger',
    vehicleType: 'SUV',
    ownerName: 'Pedro Reyes',
    pickupDate: '2025-06-10',
    returnDate: '2025-06-15',
    totalPrice: 12500,
    status: 'Completed',
  },
]);

// In a real Inertia app, this data would be passed as props from the controller
</script>

<style scoped>
/* Tailwind CSS is used for styling */
</style>
