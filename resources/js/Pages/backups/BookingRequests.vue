<template>
  <AppLayout>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
      <aside class="md:col-span-1 bg-white p-6 rounded-lg shadow-md h-fit">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Owner Tools</h2>
        <nav class="flex flex-col gap-2">
          <Link href="/my-vehicles" class="text-gray-700 hover:underline">My Vehicles</Link>
          <Link href="/my-vehicles/create" class="text-gray-700 hover:underline">Add New Vehicle</Link>
          <Link href="/my-vehicles/bookings" class="text-primary-600 font-medium hover:underline">Booking Requests</Link>
          <Link href="/my-payments" class="text-gray-700 hover:underline">My Payments</Link>
        </nav>
      </aside>

      <div class="md:col-span-3 bg-white p-6 rounded-lg shadow-md">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Incoming Booking Requests</h1>
        <div v-if="bookingRequests.length > 0" class="overflow-x-auto">
          <table class="min-w-full bg-white border border-gray-200 rounded-lg">
            <thead>
              <tr class="bg-gray-100 text-left text-sm font-semibold text-gray-700">
                <th class="py-3 px-4 border-b">Vehicle</th>
                <th class="py-3 px-4 border-b">Renter</th>
                <th class="py-3 px-4 border-b">Dates</th>
                <th class="py-3 px-4 border-b">Status</th>
                <th class="py-3 px-4 border-b">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="request in bookingRequests" :key="request.id" class="hover:bg-gray-50 border-b border-gray-100 last:border-b-0">
                <td class="py-3 px-4">
                  <div class="font-medium text-gray-800">{{ request.vehicle.license_plate }}</div>
                  <div class="text-sm text-gray-600">{{ request.vehicle.type?.category }}</div>
                </td>
                <td class="py-3 px-4">
                  <div class="font-medium text-gray-800">{{ request.user.first_name }} {{ request.user.last_name }}</div>
                  <div class="text-sm text-gray-600">{{ request.user.email }}</div>
                </td>
                <td class="py-3 px-4 text-gray-700">
                  <div>{{ request.pickup_datetime }}</div>
                  <div class="text-sm text-gray-500">{{ request.pricing_tier?.duration_label || 'N/A' }}</div>
                </td>
                <td class="py-3 px-4">
                  <span :class="['px-3 py-1 rounded-full text-xs font-medium',
                    request.status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                    request.status === 'confirmed' ? 'bg-green-100 text-green-800' :
                    'bg-red-100 text-red-800']">
                    {{ request.status.charAt(0).toUpperCase() + request.status.slice(1) }}
                  </span>
                </td>
                <td class="py-3 px-4">
                  <Link :href="route('owner.bookings.show', request.id)"
                    class="bg-primary-600 text-white px-4 py-2 rounded-md text-sm hover:bg-primary-700 transition-colors mr-2">
                    Review
                  </Link>
                  <button
                    v-if="request.status === 'pending'"
                    @click="confirmBooking(request.id)"
                    class="bg-green-600 text-white px-3 py-1 rounded mr-2"
                  >Confirm</button>
                  <button
                    v-if="request.status === 'pending'"
                    @click="rejectBooking(request.id)"
                    class="bg-red-600 text-white px-3 py-1 rounded"
                  >Reject</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <p v-else class="text-gray-600 text-center py-8">No incoming booking requests at the moment.</p>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
  bookingRequests: Array
});

function confirmBooking(id) {
  if (confirm('Confirm this booking?')) {
    router.post(route('owner.bookings.confirm', id));
  }
}
function rejectBooking(id) {
  if (confirm('Reject this booking?')) {
    router.post(route('owner.bookings.reject', id));
  }
}
</script>
