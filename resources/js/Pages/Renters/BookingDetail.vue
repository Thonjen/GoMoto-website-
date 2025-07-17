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
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Booking Details #{{ booking.id }}</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
          <div>
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Vehicle Details</h2>
            <div class="bg-gray-50 p-4 rounded-md">
              <img :src="booking.vehicle.imageUrl" :alt="booking.vehicle.name" class="w-full h-48 object-cover rounded-md mb-4" />
              <p class="text-xl font-semibold text-gray-800">{{ booking.vehicle.name }}</p>
              <p class="text-gray-600">{{ booking.vehicle.location }}</p>
              <p class="text-primary-600 font-bold mt-2">${{ booking.vehicle.pricePerDay }}/day</p>
              <Link :href="`/vehicles/${booking.vehicle.id}`" class="text-sm text-primary-600 hover:underline mt-2 inline-block">View Vehicle Page</Link>
            </div>
          </div>
          <div>
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Owner Details</h2>
            <div class="bg-gray-50 p-4 rounded-md">
              <div class="flex items-center gap-4 mb-4">
                <img :src="booking.owner.avatarUrl" alt="Owner Avatar" class="w-16 h-16 rounded-full object-cover" />
                <div>
                  <p class="font-semibold text-gray-800">{{ booking.owner.name }}</p>
                  <p class="text-sm text-gray-600">{{ booking.owner.email }}</p>
                </div>
              </div>
              <p class="text-gray-700"><strong>Contact:</strong> {{ booking.owner.phone }}</p>
              <p class="text-gray-700"><strong>Member Since:</strong> {{ booking.owner.memberSince }}</p>
            </div>
          </div>
        </div>

        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Booking Summary</h2>
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
                  booking.status === 'Completed' ? 'bg-blue-100 text-blue-800' :
                  'bg-red-100 text-red-800']">
                  {{ booking.status }}
                </span>
              </p>
            </div>
          </div>
          <div class="mt-4">
            <p class="font-semibold text-gray-800">Your Message to Owner:</p>
            <p class="text-gray-700 italic">"{{ booking.message }}"</p>
          </div>
        </div>

        <div class="flex flex-col sm:flex-row gap-4">
          <Link v-if="booking.status === 'Confirmed'" :href="`/my-bookings/${booking.id}/receipt`"
            class="bg-primary-600 text-white px-6 py-3 rounded-md font-semibold hover:bg-primary-700 transition-colors flex items-center justify-center gap-2">
            <UploadCloud class="h-5 w-5" />
            Upload Proof of Payment
          </Link>
          <Link v-if="booking.status === 'Completed' && !booking.hasReviewed" :href="`/my-bookings/${booking.id}/review`"
            class="bg-blue-600 text-white px-6 py-3 rounded-md font-semibold hover:bg-blue-700 transition-colors flex items-center justify-center gap-2">
            <Star class="h-5 w-5" />
            Leave Review
          </Link>
          <button v-if="booking.status === 'Pending'" @click="cancelBooking"
            class="bg-red-600 text-white px-6 py-3 rounded-md font-semibold hover:bg-red-700 transition-colors flex items-center justify-center gap-2">
            <XCircle class="h-5 w-5" />
            Cancel Booking
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
import { UploadCloud, Star, XCircle } from 'lucide-vue-next';

const page = usePage();
const bookingId = page.props.id || 1; // Get ID from route params, default to 1 for demo

const booking = ref({
  id: bookingId,
  vehicle: {
    id: 101,
    name: 'Toyota Camry 2022',
    location: 'Manila',
    pricePerDay: 1800,
    imageUrl: '/placeholder.svg?height=192&width=256',
  },
  owner: {
    name: 'Juan Dela Cruz',
    email: 'juan.d@example.com',
    phone: '09171234567',
    memberSince: 'January 2023',
    avatarUrl: '/placeholder.svg?height=64&width=64',
  },
  pickupDate: '2025-07-20',
  returnDate: '2025-07-25',
  totalDays: 5,
  totalPrice: 9000,
  message: 'Looking forward to renting your car for my trip next week!',
  status: 'Confirmed', // Can be 'Pending', 'Confirmed', 'Completed', 'Rejected'
  hasReviewed: false, // Simulate if a review has been left
});

// In a real Inertia app, this data would be passed as props from the controller

const cancelBooking = () => {
  if (confirm('Are you sure you want to cancel this booking? This action cannot be undone.')) {
    booking.value.status = 'Cancelled';
    alert('Booking cancelled! (Not actually updated in this demo)');
    // In a real Inertia app, this would be an Inertia.post(`/my-bookings/${booking.value.id}/cancel`)
  }
};
</script>

<style scoped>
/* Tailwind CSS is used for styling */
</style>
