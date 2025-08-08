<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Booking Details</h2>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="mb-6">
                            <button
                                @click="goBack"
                                class="inline-flex items-center text-blue-600 hover:text-blue-800 font-semibold transition-colors"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                                Back to My Bookings
                            </button>
                        </div>

                        <div class="mb-6">
                            <h1 class="text-3xl font-bold text-gray-800">Booking #{{ booking.id }}</h1>
                            <div class="flex items-center space-x-4 mt-2">
                                <span 
                                    :class="getStatusClass(booking.status)"
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
                                >
                                    {{ formatStatus(booking.status) }}
                                </span>
                                <span v-if="booking.payment" class="text-sm text-gray-500">
                                    Created {{ formatDate(booking.created_at) }}
                                </span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                            <!-- Vehicle Information -->
                            <div class="lg:col-span-2 space-y-6">
                                <div class="bg-gray-50 rounded-lg p-6">
                                    <h2 class="text-xl font-semibold mb-4">Vehicle Details</h2>
                                    <div class="flex space-x-4">
                                        <img
                                            :src="booking.vehicle.main_photo_url || '/images/placeholder-vehicle.jpg'"
                                            :alt="`${booking.vehicle.brand?.name} ${booking.vehicle.type?.sub_type}`"
                                            class="w-32 h-32 object-cover rounded-lg"
                                        />
                                        <div class="flex-1">
                                            <h3 class="text-lg font-semibold">
                                                {{ booking.vehicle.brand?.name }} {{ booking.vehicle.type?.sub_type }}
                                            </h3>
                                            <p class="text-gray-600 mb-3">{{ booking.vehicle.description }}</p>
                                            <div class="grid grid-cols-2 gap-4 text-sm text-gray-600">
                                                <div><span class="font-medium">Year:</span> {{ booking.vehicle.year }}</div>
                                                <div><span class="font-medium">Color:</span> {{ booking.vehicle.color }}</div>
                                                <div><span class="font-medium">Fuel:</span> {{ booking.vehicle.fuel_type?.name }}</div>
                                                <div><span class="font-medium">License:</span> {{ booking.vehicle.license_plate }}</div>
                                            </div>
                                            <div class="mt-3">
                                                <span class="font-medium">Location:</span> {{ booking.vehicle.location_name }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Booking Information -->
                                <div class="bg-gray-50 rounded-lg p-6">
                                    <h2 class="text-xl font-semibold mb-4">Booking Information</h2>
                                    <div class="grid grid-cols-2 gap-6">
                                        <div>
                                            <span class="text-sm text-gray-500">Duration</span>
<div class="font-semibold">
  {{ booking.pricing_tier.duration_from }}
  {{ booking.pricing_tier.duration_from === 1
      ? booking.pricing_tier.duration_unit.slice(0, -1)
      : booking.pricing_tier.duration_unit }}
</div>                                        </div>
                                        <div>
                                            <span class="text-sm text-gray-500">Total Amount</span>
                                            <div class="text-xl font-bold text-green-600">₱{{ booking.total_amount }}</div>
                                        </div>
                                        <div>
                                            <span class="text-sm text-gray-500">Pickup Date & Time</span>
                                            <div class="font-semibold">{{ formatDateTime(booking.pickup_datetime) }}</div>
                                        </div>
                                        <div>
                                            <span class="text-sm text-gray-500">Estimated Return</span>
                                            <div class="font-semibold">{{ estimatedReturn }}</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Owner Information -->
                                <div class="bg-gray-50 rounded-lg p-6">
                                    <h2 class="text-xl font-semibold mb-4">Vehicle Owner</h2>
                                    <div class="flex items-center space-x-4">
                                        <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center text-white font-semibold">
                                            {{ booking.vehicle.owner.first_name.charAt(0) }}{{ booking.vehicle.owner.last_name.charAt(0) }}
                                        </div>
                                        <div>
                                            <div class="font-semibold">{{ booking.vehicle.owner.first_name }} {{ booking.vehicle.owner.last_name }}</div>
                                            <div class="text-sm text-gray-600">{{ booking.vehicle.owner.email }}</div>
                                            <div v-if="booking.vehicle.owner.phone" class="text-sm text-gray-600">
                                                <a :href="`tel:${booking.vehicle.owner.phone}`" class="text-blue-600 hover:text-blue-800">
                                                    {{ booking.vehicle.owner.phone }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Payment & Actions Sidebar -->
                            <div class="lg:col-span-1">
                                <!-- Payment Information -->
                                <div class="bg-white border rounded-lg p-6 mb-6">
                                    <h2 class="text-xl font-semibold mb-4">Payment Information</h2>
                                    <div class="space-y-3">
                                        <div>
                                            <span class="text-sm text-gray-500">Payment Method</span>
                                            <div class="font-semibold">{{ booking.payment?.payment_mode?.name || 'N/A' }}</div>
                                        </div>
                                        <div>
                                            <span class="text-sm text-gray-500">Payment Status</span>
                                            <div>
                                                <span 
                                                    :class="getPaymentStatusClass(booking.payment?.paid_at)"
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                                >
                                                    {{ booking.payment?.paid_at ? 'Payment Confirmed' : 'Payment Pending' }}
                                                </span>
                                            </div>
                                        </div>
                                        <div v-if="booking.payment?.reference_number">
                                            <span class="text-sm text-gray-500">Reference Number</span>
                                            <div class="font-mono text-sm">{{ booking.payment.reference_number }}</div>
                                        </div>
                                        <div v-if="booking.payment?.receipt_image">
                                            <span class="text-sm text-gray-500">Payment Proof</span>
                                            <div class="mt-2">
                                                <img 
                                                    :src="booking.payment.receipt_image"
                                                    alt="Payment Receipt"
                                                    class="w-full rounded border cursor-pointer"
                                                    @click="openReceiptModal"
                                                />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="space-y-3">
                                    <button
                                        v-if="canMakePayment"
                                        @click="goToPayment"
                                        class="w-full py-2 px-4 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-colors"
                                    >
                                        Complete Payment
                                    </button>
                                    
                                    <button
                                        v-if="booking.status === 'pending'"
                                        @click="cancelBooking"
                                        class="w-full py-2 px-4 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition-colors"
                                    >
                                        Cancel Booking
                                    </button>

                                    <button
                                        @click="goToVehicle"
                                        class="w-full py-2 px-4 border border-gray-300 text-gray-700 hover:bg-gray-50 font-semibold rounded-lg transition-colors"
                                    >
                                        View Vehicle
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Receipt Modal -->
        <div v-if="showReceiptModal" @click.self="closeReceiptModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-lg max-w-2xl w-full max-h-[90vh] overflow-auto">
                <div class="p-4 border-b flex justify-between items-center">
                    <h3 class="text-lg font-semibold">Payment Receipt</h3>
                    <button @click="closeReceiptModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="p-4 text-center">
                    <img :src="booking.payment?.receipt_image" alt="Payment Receipt" class="max-w-full h-auto mx-auto rounded border" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { computed, ref } from 'vue'
import { router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
    booking: Object,
})

const showReceiptModal = ref(false)

const canMakePayment = computed(() => {
    return props.booking.status === 'pending' && 
           props.booking.payment && 
           !props.booking.payment.paid_at && 
           props.booking.payment.type === 'gcash'
})

const estimatedReturn = computed(() => {
    const pickup = new Date(props.booking.pickup_datetime)
    const tier = props.booking.pricing_tier
    
    let returnDate = new Date(pickup)
    
    switch (tier.duration_unit) {
        case 'minutes':
            returnDate.setMinutes(returnDate.getMinutes() + tier.duration_from)
            break
        case 'hours':
            returnDate.setHours(returnDate.getHours() + tier.duration_from)
            break
        case 'days':
            returnDate.setDate(returnDate.getDate() + tier.duration_from)
            break
    }
    
    return returnDate.toLocaleString('en-US', {
        weekday: 'short',
        month: 'short',
        day: 'numeric',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    })
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
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    })
}

function formatDate(date) {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    })
}

function openReceiptModal() {
    showReceiptModal.value = true
}

function closeReceiptModal() {
    showReceiptModal.value = false
}

function goBack() {
    router.visit(route('bookings.index'))
}

function goToPayment() {
    router.visit(route('bookings.payment', props.booking.id))
}

function goToVehicle() {
    router.visit(`/vehicles/${props.booking.vehicle.id}`)
}

function cancelBooking() {
    if (confirm('Are you sure you want to cancel this booking?')) {
        router.post(route('bookings.cancel', props.booking.id), {}, {
            onSuccess: () => {
                router.visit(route('bookings.index'))
            }
        })
    }
}
</script>
