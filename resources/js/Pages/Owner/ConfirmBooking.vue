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
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Review Booking Request #{{ booking.id }}</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
          <div>
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Vehicle Details</h2>
            <div class="bg-gray-50 p-4 rounded-md">
              <img :src="booking.vehicle.imageUrl" :alt="booking.vehicle.name" class="w-full h-48 object-cover rounded-md mb-4" />
              <p class="text-xl font-semibold text-gray-800">{{ booking.vehicle.name }}</p>
              <p class="text-gray-600">{{ booking.vehicle.location }}</p>
              <p class="text-primary-600 font-bold mt-2">${{ booking.vehicle.pricePerDay }}/day</p>
            </div>
          </div>
          <div>
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Renter Details</h2>
            <div class="bg-gray-50 p-4 rounded-md">
              <div class="flex items-center gap-4 mb-4">
                <img :src="booking.renter.avatarUrl" alt="Renter Avatar" class="w-16 h-16 rounded-full object-cover" />
                <div>
                  <p class="font-semibold text-gray-800">{{ booking.renter.name }}</p>
                  <p class="text-sm text-gray-600">{{ booking.renter.email }}</p>
                </div>
              </div>
              <p class="text-gray-700"><strong>Contact:</strong> {{ booking.renter.phone }}</p>
              <p class="text-gray-700"><strong>Member Since:</strong> {{ booking.renter.memberSince }}</p>
            </div>
          </div>
        </div>

        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Booking Details</h2>
        <div class="bg-gray-50 p-6 rounded-md mb-8">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-gray-700">
            <div>
              <p><strong>Pickup Date:</strong> {{ booking.pickupDate }}</p>
              <p><strong>Return Date:</strong> {{ booking.returnDate }}</p>
              <p><strong>Total Days:</strong> {{ booking.totalDays }}</p>
            </div>
            <div>
              <p><strong>Total Price:</strong> <span class="font-bold text-primary-600">₱{{ booking.totalPrice.toLocaleString() }}</span></p>
              <p><strong>Status:</strong>
                <span :class="['px-3 py-1 rounded-full text-xs font-medium ml-2',
                  booking.status === 'Pending' ? 'bg-yellow-100 text-yellow-800' :
                  booking.status === 'Confirmed' ? 'bg-green-100 text-green-800' :
                  'bg-red-100 text-red-800']">
                  {{ booking.status }}
                </span>
              </p>
            </div>
          </div>
          <div class="mt-4">
            <p class="font-semibold text-gray-800">Renter's Message:</p>
            <p class="text-gray-700 italic">"{{ booking.message }}"</p>
          </div>
        </div>

        <div class="flex gap-4">
          <button @click="confirmBooking" :disabled="booking.status !== 'Pending'"
            class="bg-green-600 text-white px-6 py-3 rounded-md font-semibold hover:bg-green-700 transition-colors flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
            <CheckCircle class="h-5 w-5" />
            Confirm Booking
          </button>
          <button @click="rejectBooking" :disabled="booking.status !== 'Pending'"
            class="bg-red-600 text-white px-6 py-3 rounded-md font-semibold hover:bg-red-700 transition-colors flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
            <XCircle class="h-5 w-5" />
            Reject Booking
          </button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { CheckCircle, XCircle } from 'lucide-vue-next';

const page = usePage();
const bookingId = page.props.id || 1; // Get ID from route params, default to 1 for demo

const booking = ref({
  id: bookingId,
  vehicle: {
    name: 'Toyota Fortuner 2021',
    location: 'Manila',
    pricePerDay: 2500,
    imageUrl: '/placeholder.svg?height=192&width=256',
  },
  renter: {
    name: 'Maria Santos',
    email: 'maria.s@example.com',
    phone: '09178889999',
    memberSince: 'March 2024',
    avatarUrl: '/placeholder.svg?height=64&width=64',
  },
  pickupDate: '2025-08-01',
  returnDate: '2025-08-05',
  totalDays: 5,
  totalPrice: 12500, // 2500 * 5
  message: 'Hi, I am interested in renting your Fortuner for a family trip. Is it available for these dates?',
  status: 'Pending', // Can be 'Pending', 'Confirmed', 'Rejected'
});

// In a real Inertia app, this data would be passed as props from the controller

const confirmBooking = () => {
  if (confirm('Are you sure you want to CONFIRM this booking?')) {
    booking.value.status = 'Confirmed';
    alert('Booking confirmed! (Not actually updated in this demo)');
    // In a real Inertia app, this would be an Inertia.post(`/my-vehicles/bookings/${booking.value.id}/confirm`)
  }
};

const rejectBooking = () => {
  if (confirm('Are you sure you want to REJECT this booking?')) {
    booking.value.status = 'Rejected';
    alert('Booking rejected! (Not actually updated in this demo)');
    // In a real Inertia app, this would be an Inertia.post(`/my-vehicles/bookings/${booking.value.id}/reject`)
  }
};
</script>

<style scoped>
/* Tailwind CSS is used for styling */
</style>
