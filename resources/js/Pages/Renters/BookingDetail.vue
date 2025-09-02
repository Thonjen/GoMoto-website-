<template>
  <AppLayout>
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
              <h1 class="text-3xl font-bold text-white mb-2">Booking Details #{{ booking.id }}</h1>
              <p class="text-white/80">Complete information about your rental</p>
            </div>

            <!-- Vehicle Details -->
            <div class="glass-card p-6 shadow-glow">
              <h2 class="text-2xl font-semibold text-white mb-4">Vehicle Details</h2>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <img :src="booking.vehicle.imageUrl" :alt="booking.vehicle.name" class="w-full h-48 object-cover rounded-lg" />
                </div>
                <div>
                  <p class="text-xl font-semibold text-white">{{ booking.vehicle.name }}</p>
                  <p class="text-white/80">{{ booking.vehicle.location }}</p>
                  <p class="text-primary-300 font-bold mt-2 text-lg">${{ booking.vehicle.pricePerDay }}/day</p>
                  <Link :href="`/vehicles/${booking.vehicle.id}`" class="text-sm text-primary-300 hover:text-primary-200 mt-2 inline-block transition-colors">View Vehicle Page</Link>
                </div>
              </div>
            </div>

            <!-- Owner Details -->
            <div class="glass-card p-6 shadow-glow">
              <h2 class="text-2xl font-semibold text-white mb-4">Owner Details</h2>
              <div class="bg-white/5 p-4 rounded-lg backdrop-blur-sm">
                <div class="flex items-center gap-4 mb-4">
                  <img :src="booking.owner.avatarUrl" alt="Owner Avatar" class="w-16 h-16 rounded-full object-cover border-2 border-white/20" />
                  <div>
                    <p class="font-semibold text-white">{{ booking.owner.name }}</p>
                    <p class="text-sm text-white/80">{{ booking.owner.email }}</p>
                  </div>
                </div>
                <p class="text-white/90"><strong class="text-white">Contact:</strong> {{ booking.owner.phone }}</p>
                <p class="text-white/90"><strong class="text-white">Member Since:</strong> {{ booking.owner.memberSince }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Booking Summary -->
        <div class="glass-card p-6 shadow-glow">
          <h2 class="text-2xl font-semibold text-white mb-4">Booking Summary</h2>
          <div class="bg-white/5 p-6 rounded-lg backdrop-blur-sm">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-white/90">
              <div>
                <p><strong class="text-white">Pickup Date:</strong> {{ booking.pickupDate }}</p>
                <p><strong class="text-white">Return Date:</strong> {{ booking.returnDate }}</p>
                <p><strong class="text-white">Total Days:</strong> {{ booking.totalDays }}</p>
              </div>
              <div>
                <p><strong class="text-white">Total Price:</strong> <span class="font-bold text-primary-300">₱{{ booking.totalPrice.toLocaleString() }}</span></p>
                <p><strong class="text-white">Status:</strong>
                  <span :class="['px-3 py-1 rounded-full text-xs font-medium ml-2',
                    booking.status === 'Pending' ? 'bg-yellow-500/20 text-yellow-300 border border-yellow-500/30' :
                    booking.status === 'Confirmed' ? 'bg-green-500/20 text-green-300 border border-green-500/30' :
                    booking.status === 'Completed' ? 'bg-blue-500/20 text-blue-300 border border-blue-500/30' :
                    'bg-red-500/20 text-red-300 border border-red-500/30']">
                    {{ booking.status }}
                  </span>
                </p>
              </div>
            </div>
            <div class="mt-4">
              <p class="font-semibold text-white">Your Message to Owner:</p>
              <p class="text-white/80 italic">"{{ booking.message }}"</p>
            </div>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="glass-card p-6 shadow-glow">
          <div class="flex flex-col sm:flex-row gap-4">
            <Link v-if="booking.status === 'Confirmed'" :href="`/my-bookings/${booking.id}/receipt`"
              class="btn-primary flex items-center justify-center gap-2">
              <UploadCloud class="h-5 w-5" />
              Upload Proof of Payment
            </Link>
            
            <!-- Extend Booking Button -->
            <button v-if="booking.status === 'Confirmed' && canExtendBooking" @click="showExtendModal = true"
              class="btn-glass bg-green-500/20 border-green-500/30 text-green-300 hover:bg-green-500/30 flex items-center justify-center gap-2">
              <Clock class="h-5 w-5" />
              Extend Booking
            </button>
            
            <Link v-if="booking.status === 'Completed' && !booking.hasReviewed" :href="`/my-bookings/${booking.id}/review`"
              class="btn-glass bg-blue-500/20 border-blue-500/30 text-blue-300 hover:bg-blue-500/30 flex items-center justify-center gap-2">
              <Star class="h-5 w-5" />
              Leave Review
            </Link>
            <button v-if="booking.status === 'Pending'" @click="cancelBooking"
              class="btn-glass bg-red-500/20 border-red-500/30 text-red-300 hover:bg-red-500/30 flex items-center justify-center gap-2">
              <XCircle class="h-5 w-5" />
              Cancel Booking
            </button>
          </div>
        </div>
        
        <!-- Extend Booking Modal -->
        <div v-if="showExtendModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50">
          <div class="glass-card p-6 w-full max-w-md mx-4 shadow-glow">
            <div class="flex justify-between items-center mb-4">
              <h3 class="text-lg font-semibold text-white">Extend Your Booking</h3>
              <button @click="showExtendModal = false" class="text-white/60 hover:text-white transition-colors">
                <XCircle class="h-6 w-6" />
              </button>
            </div>
            
            <div class="space-y-4">
              <div>
                <p class="text-sm text-white/80 mb-2">
                  Current return date: <strong class="text-white">{{ booking.returnDate }}</strong>
                </p>
                <p class="text-sm text-white/80 mb-4">
                  Extend your booking to avoid overcharges if you need more time.
                </p>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-white mb-2">
                  Extend by how many hours?
                </label>
                <select v-model="extendHours" class="w-full bg-white/10 border border-white/20 rounded-lg px-3 py-2 text-white placeholder-white/60 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent backdrop-blur-sm">
                  <option value="1" class="bg-dark text-white">1 hour (₱{{ calculateExtensionCost(1) }})</option>
                  <option value="2" class="bg-dark text-white">2 hours (₱{{ calculateExtensionCost(2) }})</option>
                  <option value="4" class="bg-dark text-white">4 hours (₱{{ calculateExtensionCost(4) }})</option>
                  <option value="6" class="bg-dark text-white">6 hours (₱{{ calculateExtensionCost(6) }})</option>
                  <option value="12" class="bg-dark text-white">12 hours (₱{{ calculateExtensionCost(12) }})</option>
                  <option value="24" class="bg-dark text-white">24 hours (₱{{ calculateExtensionCost(24) }})</option>
                </select>
              </div>
              
              <div class="bg-blue-500/20 border border-blue-500/30 rounded-lg p-3 backdrop-blur-sm">
                <p class="text-sm text-blue-300">
                  <strong>Extension Cost:</strong> ₱{{ calculateExtensionCost(extendHours) }}
                </p>
                <p class="text-xs text-blue-400 mt-1">
                  This will be added to your total booking cost.
                </p>
              </div>
            </div>
            
            <div class="flex justify-end space-x-3 mt-6">
              <button @click="showExtendModal = false"
                class="px-4 py-2 text-white/60 hover:text-white font-medium transition-colors">
                Cancel
              </button>
              <button @click="extendBooking" :disabled="extending"
                class="btn-primary disabled:opacity-50">
                <span v-if="extending">Extending...</span>
                <span v-else>Extend Booking</span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AuthenticatedLayout.vue';
import { UploadCloud, Star, XCircle, Clock } from 'lucide-vue-next';

const page = usePage();
const bookingId = page.props.id || 1; // Get ID from route params, default to 1 for demo

// Extend booking modal state
const showExtendModal = ref(false);
const extendHours = ref(2);
const extending = ref(false);

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
  pricePerHour: 75, // ₱75/hour (derived from daily rate)
});

// Check if booking can be extended (within 24 hours of return date)
const canExtendBooking = computed(() => {
  if (booking.value.status !== 'Confirmed') return false;
  
  const returnDate = new Date(booking.value.returnDate);
  const now = new Date();
  const hoursUntilReturn = (returnDate.getTime() - now.getTime()) / (1000 * 60 * 60);
  
  // Can extend if return is within 24 hours or if already past return time
  return hoursUntilReturn <= 24;
});

// Calculate extension cost
const calculateExtensionCost = (hours) => {
  return (hours * booking.value.pricePerHour).toLocaleString();
};

// Extend booking function
const extendBooking = () => {
  extending.value = true;
  
  router.post(`/bookings/${booking.value.id}/extend`, {
    extend_hours: extendHours.value
  }, {
    onSuccess: (response) => {
      showExtendModal.value = false;
      extending.value = false;
      
      // Update booking with new details
      const newReturnDate = new Date(booking.value.returnDate);
      newReturnDate.setHours(newReturnDate.getHours() + parseInt(extendHours.value));
      booking.value.returnDate = newReturnDate.toISOString().split('T')[0];
      booking.value.totalPrice += parseInt(extendHours.value) * booking.value.pricePerHour;
      
      alert(`Booking successfully extended by ${extendHours.value} hours!`);
    },
    onError: (errors) => {
      extending.value = false;
      alert('Failed to extend booking. Please try again.');
      console.error(errors);
    }
  });
};

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
