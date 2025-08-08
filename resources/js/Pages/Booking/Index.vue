<template>
    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="mb-6">
                            <h1 class="text-3xl font-bold text-gray-800">My Bookings</h1>
                            <p class="text-gray-600 mt-2">Manage your vehicle rental bookings</p>
                        </div>

                        <div v-if="bookings.length === 0" class="text-center py-12">
                            <div class="mb-4">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900">No bookings found</h3>
                            <p class="mt-2 text-sm text-gray-500">You haven't made any vehicle bookings yet.</p>
                            <div class="mt-6">
                                <a 
                                    :href="route('public.vehicles.index')"
                                    class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                >
                                    Browse Vehicles
                                </a>
                            </div>
                        </div>

                        <div v-else class="space-y-6">
                            <div
                                v-for="booking in bookings"
                                :key="booking.id"
                                class="border rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-200"
                            >
                                <div class="p-6">
                                    <div class="flex items-start justify-between">
                                        <div class="flex space-x-4">
                                            <img
                                                :src="booking.vehicle.main_photo_url || '/images/placeholder-vehicle.jpg'"
                                                :alt="`${booking.vehicle.brand?.name} ${booking.vehicle.type?.sub_type}`"
                                                class="w-20 h-20 object-cover rounded-lg"
                                            />
                                            
                                            <div class="flex-1">
                                                <h3 class="text-lg font-semibold text-gray-900">
                                                    {{ booking.vehicle.brand?.name }} {{ booking.vehicle.type?.sub_type }}
                                                </h3>
                                                <p class="text-sm text-gray-500 mb-2">
                                                    Booking #{{ booking.id }}
                                                </p>
                                                
                                                <div class="grid grid-cols-2 gap-4 text-sm text-gray-600">
                                                    <div>
                                                        <span class="font-medium">Duration:</span>
                                                        {{ booking.pricing_tier?.duration_from }} {{ booking.pricing_tier?.duration_unit }}
                                                    </div>
                                                    <div>
                                                        <span class="font-medium">Amount:</span>
                                                        ₱{{ booking.total_amount }}
                                                    </div>
                                                    <div>
                                                        <span class="font-medium">Pickup:</span>
                                                        {{ formatDateTime(booking.pickup_datetime) }}
                                                    </div>
                                                    <div>
                                                        <span class="font-medium">Payment:</span>
                                                        {{ booking.payment?.payment_mode?.name || 'N/A' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="flex flex-col items-end space-y-3">
                                            <!-- Status Badge -->
                                            <span 
                                                :class="getStatusClass(booking.status)"
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                            >
                                                {{ formatStatus(booking.status) }}
                                            </span>

                                            <!-- Payment Status -->
                                            <span 
                                                v-if="booking.payment"
                                                :class="getPaymentStatusClass(booking.payment.paid_at)"
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                            >
                                                {{ booking.payment.paid_at ? 'Payment Confirmed' : 'Payment Pending' }}
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Actions -->
                                    <div class="mt-4 flex justify-end space-x-3">
                                        <button
                                            @click="viewBooking(booking.id)"
                                            class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                        >
                                            View Details
                                        </button>
                                        
                                        <button
                                            v-if="canMakePayment(booking)"
                                            @click="goToPayment(booking.id)"
                                            class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
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
        'pending': 'bg-yellow-100 text-yellow-800',
        'confirmed': 'bg-blue-100 text-blue-800',
        'completed': 'bg-green-100 text-green-800',
        'cancelled': 'bg-red-100 text-red-800',
    }
    return classes[status] || 'bg-gray-100 text-gray-800'
}

function getPaymentStatusClass(paidAt) {
    return paidAt 
        ? 'bg-green-100 text-green-800' 
        : 'bg-yellow-100 text-yellow-800'
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
