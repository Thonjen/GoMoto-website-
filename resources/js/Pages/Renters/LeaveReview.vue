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
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Leave a Review for Booking #{{ bookingId }}</h1>

        <div class="bg-blue-50 p-4 rounded-md mb-6">
          <p class="font-semibold text-blue-800">Booking Details:</p>
          <p class="text-blue-700">Vehicle: {{ bookingDetails.vehicleName }}</p>
          <p class="text-blue-700">Owner: {{ bookingDetails.ownerName }}</p>
          <p class="text-blue-700">Dates: {{ bookingDetails.pickupDate }} - {{ bookingDetails.returnDate }}</p>
        </div>

        <p class="text-gray-700 mb-6">Share your experience with the vehicle and its owner to help other renters!</p>

        <form @submit.prevent="submitReview">
          <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Overall Rating</label>
            <div class="flex items-center gap-1">
              <Star v-for="n in 5" :key="n" @click="setRating(n)"
                :class="['h-8 w-8 cursor-pointer transition-colors',
                  n <= reviewForm.rating ? 'text-yellow-400 fill-yellow-400' : 'text-gray-300']" />
            </div>
            <p v-if="reviewForm.rating" class="text-sm text-gray-600 mt-2">You rated: {{ reviewForm.rating }} out of 5 stars</p>
          </div>

          <div class="mb-6">
            <label for="reviewText" class="block text-sm font-medium text-gray-700 mb-2">Your Review</label>
            <textarea id="reviewText" v-model="reviewForm.comment" rows="6" required
              class="w-full p-3 border border-gray-300 rounded-md focus:ring-primary-400 focus:border-primary-400"
              placeholder="Describe your experience with the vehicle and owner..."></textarea>
          </div>

          <button type="submit"
            class="bg-primary-600 text-white px-6 py-3 rounded-md font-semibold hover:bg-primary-700 transition-colors flex items-center justify-center gap-2">
            <Send class="h-5 w-5" />
            Submit Review
          </button>
        </form>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Star, Send } from 'lucide-vue-next';

const page = usePage();
const bookingId = page.props.id || 1; // Get ID from route params, default to 1 for demo

const bookingDetails = ref({
  vehicleName: 'Toyota Camry',
  ownerName: 'Juan Dela Cruz',
  pickupDate: '2025-07-20',
  returnDate: '2025-07-25',
});

const reviewForm = ref({
  rating: 0,
  comment: '',
});

const setRating = (stars) => {
  reviewForm.value.rating = stars;
};

const submitReview = () => {
  if (reviewForm.value.rating === 0) {
    alert('Please provide a star rating.');
    return;
  }
  alert('Review submitted successfully! (Not actually saved in this demo)');
  console.log('Review data:', reviewForm.value);
  // In a real Inertia app, this would be an Inertia.post(`/my-bookings/${bookingId}/review`, reviewForm.value)
};

onMounted(() => {
  // Simulate fetching booking details
  // In a real app, this data would be passed as props from the controller
});
</script>

<style scoped>
/* Tailwind CSS is used for styling */
</style>
