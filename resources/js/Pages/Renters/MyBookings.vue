<template>
  <AuthenticatedLayout>
    <div class="min-h-screen py-8">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
          <aside class="md:col-span-1 glass-card p-6 h-fit shadow-glow">
            <h2 class="text-xl font-semibold text-white mb-4">Renter Tools</h2>
            <nav class="flex flex-col gap-3">
              <Link href="/my-bookings" class="text-white font-medium p-3 rounded-lg bg-white/20 border border-white/30">
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
              <h1 class="text-3xl font-bold text-white mb-2">My Bookings</h1>
              <p class="text-white/80">Manage your vehicle reservations</p>
            </div>

            <!-- Bookings Table -->
            <div class="glass-card p-6 shadow-glow">
              <div v-if="bookings.length > 0" class="overflow-x-auto">
                <div class="min-w-full">
                  <div class="grid grid-cols-6 gap-4 text-left text-sm font-semibold text-white/80 border-b border-white/20 pb-3 mb-4">
                    <div>Vehicle</div>
                    <div>Owner</div>
                    <div>Dates</div>
                    <div>Total Price</div>
                    <div>Status</div>
                    <div>Actions</div>
                  </div>
                  <div class="space-y-3">
                    <div v-for="booking in bookings" :key="booking.id" class="grid grid-cols-6 gap-4 py-4 bg-white/5 rounded-lg p-4 hover:bg-white/10 transition-colors border border-white/10">
                      <div>
                        <div class="font-medium text-white">{{ booking.vehicleName }}</div>
                        <div class="text-sm text-white/60">{{ booking.vehicleType }}</div>
                      </div>
                      <div>
                        <div class="font-medium text-white">{{ booking.ownerName }}</div>
                      </div>
                      <div class="text-white/80">{{ booking.pickupDate }} - {{ booking.returnDate }}</div>
                      <div class="font-bold text-primary-300">₱{{ booking.totalPrice.toLocaleString() }}</div>
                      <div>
                        <span :class="['px-3 py-1 rounded-full text-xs font-medium border',
                          booking.status === 'Pending' ? 'bg-yellow-500/20 text-yellow-300 border-yellow-500/30' :
                          booking.status === 'Confirmed' ? 'bg-green-500/20 text-green-300 border-green-500/30' :
                          booking.status === 'Completed' ? 'bg-blue-500/20 text-blue-300 border-blue-500/30' :
                          'bg-red-500/20 text-red-300 border-red-500/30']">
                          {{ booking.status }}
                        </span>
                      </div>
                      <div>
                        <Link :href="`/my-bookings/${booking.id}`"
                          class="btn-glass bg-primary-500/20 border-primary-500/30 text-primary-300 hover:bg-primary-500/30 px-4 py-2 text-sm">
                          View
                        </Link>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div v-else class="text-center py-12">
                <svg class="w-16 h-16 text-white/40 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <p class="text-white/60 text-lg mb-4">You have no bookings yet.</p>
                <p class="text-white/40 mb-6">Start searching for vehicles to make your first reservation!</p>
                <Link href="/" class="btn-primary inline-flex items-center gap-2">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                  </svg>
                  Browse Vehicles
                </Link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

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
