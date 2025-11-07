<template>
    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="glass-card border border-white/20 rounded-lg shadow-glow">
                    <div class="p-6 text-white">
                        <div class="mb-6">
                            <h1 class="text-3xl font-bold text-white">My Bookings</h1>
                            <p class="text-white/70 mt-2">Manage your vehicle rental bookings</p>
                        </div>

                        <div v-if="bookings.length === 0" class="text-center py-12">
                            <div class="mb-4">
                                <svg class="mx-auto h-12 w-12 text-white/40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-white">No bookings found</h3>
                            <p class="mt-2 text-sm text-white/60">You haven't made any vehicle bookings yet.</p>
                            <div class="mt-6">
                                <a 
                                    :href="route('public.vehicles.index')"
                                    class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-400 hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-400/50 backdrop-blur-sm transition-colors"
                                >
                                    Browse Vehicles
                                </a>
                            </div>
                        </div>

                        <div v-else class="space-y-6">
                            <div
                                v-for="booking in bookings"
                                :key="booking.id"
                                class="glass-card-dark border border-white/20 rounded-lg overflow-hidden shadow-sm hover:bg-white/5 transition-all duration-200 backdrop-blur-sm"
                            >
                                <div class="p-6">
                                    <div class="flex items-start justify-between">
                                        <div class="flex space-x-4">
                                            <img
                                                :src="booking.vehicle.main_photo_url || '/images/placeholder-vehicle.jpg'"
                                                :alt="`${booking.vehicle.brand?.name} ${booking.vehicle.type?.sub_type}`"
                                                class="w-20 h-20 object-cover rounded-lg border border-white/20"
                                            />
                                            
                                            <div class="flex-1">
                                                <h3 class="text-lg font-semibold text-white">
                                                    {{ booking.vehicle.brand?.name }} {{ booking.vehicle.type?.sub_type }}
                                                </h3>
                                                <p class="text-sm text-white/60 mb-2">
                                                    Booking #{{ booking.id }}
                                                </p>
                                                
                                                <div class="grid grid-cols-2 gap-4 text-sm text-white/70">
                                                    <div>
                                                        <span class="font-medium text-white">Duration:</span>
                                                        {{ booking.pricing_tier?.duration_from }} {{ booking.pricing_tier?.duration_unit }}
                                                    </div>
                                                    <div>
                                                        <span class="font-medium text-white">Amount:</span>
                                                        ₱{{ booking.total_amount }}
                                                    </div>
                                                    <div>
                                                        <span class="font-medium text-white">Pickup:</span>
                                                        {{ formatDateTime(booking.pickup_datetime) }}
                                                    </div>
                                                    <div>
                                                        <span class="font-medium text-white">Payment:</span>
                                                        {{ booking.payment?.payment_mode?.name || 'N/A' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="flex flex-col items-end space-y-3">
                                            <!-- Status Badge -->
                                            <span 
                                                :class="getStatusClass(booking.status)"
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border backdrop-blur-sm"
                                            >
                                                {{ formatStatus(booking.status) }}
                                            </span>

                                            <!-- Payment Status -->
                                            <span 
                                                v-if="booking.payment"
                                                :class="getPaymentStatusClass(booking.payment.paid_at)"
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border backdrop-blur-sm"
                                            >
                                                {{ booking.payment.paid_at ? 'Payment Confirmed' : 'Payment Pending' }}
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Actions -->
                                    <div class="mt-4 flex justify-end space-x-3">
                                        <button
                                            @click="viewBooking(booking.id)"
                                            class="inline-flex items-center px-3 py-1.5 border border-white/20 shadow-sm text-xs font-medium rounded text-white/70 bg-white/10 hover:bg-white/20 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-400/50 backdrop-blur-sm transition-colors"
                                        >
                                            View Details
                                        </button>
                                        
                                        <button
                                            v-if="canMakePayment(booking)"
                                            @click="goToPayment(booking.id)"
                                            class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-green-400 hover:bg-green-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400/50 backdrop-blur-sm transition-colors"
                                        >
                                            Complete Payment
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
    bookings: Array,
})

function getStatusClass(status) {
    const classes = {
        'pending': 'bg-yellow-400/20 text-yellow-400 border-yellow-400/30',
        'confirmed': 'bg-blue-400/20 text-blue-400 border-blue-400/30',
        'completed': 'bg-green-400/20 text-green-400 border-green-400/30',
        'cancelled': 'bg-red-400/20 text-red-400 border-red-400/30',
    }
    return classes[status] || 'bg-white/10 text-white/70 border-white/20'
}

function getPaymentStatusClass(paidAt) {
    return paidAt 
        ? 'bg-green-400/20 text-green-400 border-green-400/30' 
        : 'bg-yellow-400/20 text-yellow-400 border-yellow-400/30'
}

function formatStatus(status) {
    const statusMap = {
        'pending': 'Pending',
        'confirmed': 'Confirmed',
        'completed': 'Completed',
        'cancelled': 'Cancelled',
    }
    return statusMap[status] || status
}

function formatDateTime(datetime) {
    return new Date(datetime).toLocaleString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    })
}

function canMakePayment(booking) {
    return booking.status === 'pending' && 
           booking.payment && 
           !booking.payment.paid_at && 
           booking.payment.type === 'gcash'
}

function viewBooking(bookingId) {
    router.visit(route('bookings.show', bookingId))
}

function goToPayment(bookingId) {
    router.visit(route('bookings.payment', bookingId))
}
</script>
