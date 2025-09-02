<template>
  <AuthenticatedLayout>
    <div class="min-h-screen py-8">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
          <aside class="md:col-span-1 glass-card p-6 h-fit shadow-glow">
            <h2 class="text-xl font-semibold text-white mb-4">Renter Tools</h2>
            <nav class="flex flex-col gap-3">
              <Link href="/my-bookings" class="text-white/80 font-medium hover:text-white transition-colors p-3 rounded-lg hover:bg-white/10">
                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                My Bookings
              </Link>
            </nav>
          </aside>

          <div class="md:col-span-3 space-y-6">
            <!-- Header -->
            <div class="glass-card p-6 shadow-glow">
              <h1 class="text-3xl font-bold text-white mb-2">Leave a Review for Booking #{{ bookingId }}</h1>
              <p class="text-white/80">Share your experience to help other renters</p>
            </div>

            <!-- Booking Details -->
            <div class="glass-card p-6 shadow-glow">
              <div class="bg-blue-500/20 border border-blue-500/30 p-4 rounded-lg backdrop-blur-sm">
                <p class="font-semibold text-blue-300 mb-2">Booking Details:</p>
                <p class="text-blue-200">Vehicle: {{ bookingDetails.vehicleName }}</p>
                <p class="text-blue-200">Owner: {{ bookingDetails.ownerName }}</p>
                <p class="text-blue-200">Dates: {{ bookingDetails.pickupDate }} - {{ bookingDetails.returnDate }}</p>
              </div>
            </div>

            <!-- Review Form -->
            <div class="glass-card p-6 shadow-glow">
              <p class="text-white/80 mb-6">Share your experience with the vehicle and its owner to help other renters!</p>

              <form @submit.prevent="submitReview" class="space-y-6">
                <div>
                  <label class="block text-sm font-medium text-white mb-3">Overall Rating</label>
                  <div class="flex items-center gap-1">
                    <Star v-for="n in 5" :key="n" @click="setRating(n)"
                      :class="['h-8 w-8 cursor-pointer transition-all duration-200 hover:scale-110',
                        n <= reviewForm.rating ? 'text-yellow-400 fill-yellow-400' : 'text-white/30 hover:text-white/60']" />
                  </div>
                  <p v-if="reviewForm.rating" class="text-sm text-white/80 mt-2">You rated: {{ reviewForm.rating }} out of 5 stars</p>
                </div>

                <div>
                  <label for="reviewText" class="block text-sm font-medium text-white mb-3">Your Review</label>
                  <textarea id="reviewText" v-model="reviewForm.comment" rows="6" required
                    class="w-full p-4 bg-white/10 border border-white/20 rounded-lg text-white placeholder-white/60 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent backdrop-blur-sm transition-all duration-200"
                    placeholder="Describe your experience with the vehicle and owner..."></textarea>
                </div>

                <button type="submit"
                  class="btn-primary flex items-center justify-center gap-2">
                  <Send class="h-5 w-5" />
                  Submit Review
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
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
