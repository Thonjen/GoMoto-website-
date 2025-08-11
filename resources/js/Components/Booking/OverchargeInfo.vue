<template>
    <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm">
        <h4 class="text-lg font-medium text-gray-900 mb-4">
            Booking Timeline & Overcharges
        </h4>

        <!-- Booking Timeline -->
        <div class="space-y-4 mb-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                    <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-900">Pickup Time</p>
                    <p class="text-sm text-gray-500">{{ formatDateTime(booking.pickup_datetime) }}</p>
                </div>
            </div>

            <div class="flex items-center">
                <div :class="[
                    'flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center',
                    isOverdue ? 'bg-red-100' : 'bg-yellow-100'
                ]">
                    <svg class="w-4 h-4" :class="isOverdue ? 'text-red-600' : 'text-yellow-600'" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium" :class="isOverdue ? 'text-red-600' : 'text-gray-900'">
                        Expected Return
                    </p>
                    <p class="text-sm" :class="isOverdue ? 'text-red-500' : 'text-gray-500'">
                        {{ formatDateTime(expectedReturnTime) }}
                    </p>
                    <p v-if="isOverdue" class="text-xs text-red-600 font-medium">
                        {{ getOverdueText() }}
                    </p>
                </div>
            </div>

            <div v-if="booking.actual_return_time" class="flex items-center">
                <div class="flex-shrink-0 w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                    <svg class="w-4 h-4 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-900">Actual Return</p>
                    <p class="text-sm text-gray-500">{{ formatDateTime(booking.actual_return_time) }}</p>
                </div>
            </div>
        </div>

        <!-- Overcharge Information -->
        <div v-if="booking.overcharges && booking.overcharges.length > 0" class="border-t pt-4">
            <h5 class="text-md font-medium text-red-600 mb-3">Applied Overcharges</h5>
            <div class="space-y-2">
                <div v-for="overcharge in booking.overcharges" :key="overcharge.id" 
                     class="flex justify-between items-center p-3 bg-red-50 rounded-md">
                    <div>
                        <p class="text-sm font-medium text-red-800">
                            {{ overcharge.overcharge_type.description }}
                        </p>
                        <p class="text-xs text-red-600">{{ overcharge.details }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-bold text-red-800">₱{{ overcharge.amount }}</p>
                        <p class="text-xs" :class="overcharge.is_paid ? 'text-green-600' : 'text-red-600'">
                            {{ overcharge.is_paid ? 'Paid' : 'Unpaid' }}
                        </p>
                    </div>
                </div>
            </div>
            <div v-if="booking.total_overcharges > 0" class="mt-3 pt-3 border-t border-red-200">
                <div class="flex justify-between items-center">
                    <span class="text-sm font-medium text-red-800">Total Overcharges:</span>
                    <span class="text-lg font-bold text-red-800">₱{{ booking.total_overcharges }}</span>
                </div>
            </div>
        </div>

        <!-- Potential Overcharges (for active bookings) -->
        <div v-else-if="potentialOvercharges && potentialOvercharges.length > 0" class="border-t pt-4">
            <h5 class="text-md font-medium text-yellow-600 mb-3">Potential Overcharges</h5>
            <div class="space-y-2">
                <div v-for="overcharge in potentialOvercharges" :key="`potential-${overcharge.overcharge_type_id}`" 
                     class="flex justify-between items-center p-3 bg-yellow-50 rounded-md">
                    <div>
                        <p class="text-sm font-medium text-yellow-800">
                            {{ getOverchargeTypeName(overcharge.overcharge_type_id) }}
                        </p>
                        <p class="text-xs text-yellow-600">{{ overcharge.details }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-bold text-yellow-800">₱{{ overcharge.amount }}</p>
                        <p class="text-xs text-yellow-600">If charged now</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Extension Option (for renters on active bookings) -->
        <div v-if="canExtend" class="border-t pt-4 mt-4">
            <h5 class="text-md font-medium text-blue-600 mb-3">Extend Booking</h5>
            <p class="text-sm text-gray-600 mb-4">
                Avoid late return charges by extending your booking time.
            </p>
            
            <form @submit.prevent="extendBooking" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">
                        Extend by (hours)
                    </label>
                    <input
                        type="number"
                        min="1"
                        max="72"
                        v-model="extensionHours"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        placeholder="4"
                    />
                </div>

                <div v-if="extensionHours > 0" class="bg-blue-50 p-3 rounded-md">
                    <p class="text-sm text-blue-800">
                        Extension cost: ₱{{ calculateExtensionCost() }}
                    </p>
                    <p class="text-sm text-blue-600">
                        New return time: {{ getNewReturnTime() }}
                    </p>
                </div>

                <button
                    type="submit"
                    :disabled="extensionForm.processing || !extensionHours"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50"
                >
                    {{ extensionForm.processing ? 'Extending...' : 'Extend Booking' }}
                </button>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useForm } from '@inertiajs/vue3'

const props = defineProps({
    booking: Object,
    expectedReturnTime: String,
    potentialOvercharges: Array,
    canExtend: {
        type: Boolean,
        default: false
    }
})

const extensionHours = ref(4)

const extensionForm = useForm({
    extend_hours: 4
})

const isOverdue = computed(() => {
    if (props.booking.actual_return_time) {
        return new Date(props.booking.actual_return_time) > new Date(props.expectedReturnTime)
    }
    return new Date() > new Date(props.expectedReturnTime)
})

const formatDateTime = (dateString) => {
    if (!dateString) return 'N/A'
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    })
}

const getOverdueText = () => {
    const now = new Date()
    const expected = new Date(props.expectedReturnTime)
    const diffHours = Math.floor((now - expected) / (1000 * 60 * 60))
    
    if (diffHours < 1) return 'Overdue by less than an hour'
    return `Overdue by ${diffHours} hour${diffHours > 1 ? 's' : ''}`
}

const getOverchargeTypeName = (typeId) => {
    // This would typically come from a prop or be fetched
    const types = {
        1: 'Late Return',
        2: 'Out of City Use'
    }
    return types[typeId] || 'Unknown'
}

const calculateExtensionCost = () => {
    if (!extensionHours.value || !props.booking.pricing_tier) return 0
    
    let hourlyRate = props.booking.pricing_tier.price
    
    // Convert rate to hourly if needed
    if (props.booking.pricing_tier.duration_unit === 'days') {
        hourlyRate = props.booking.pricing_tier.price / 24
    } else if (props.booking.pricing_tier.duration_unit === 'minutes') {
        hourlyRate = props.booking.pricing_tier.price * 60
    }
    
    return (extensionHours.value * hourlyRate).toFixed(2)
}

const getNewReturnTime = () => {
    if (!extensionHours.value) return 'N/A'
    
    const newTime = new Date(props.expectedReturnTime)
    newTime.setHours(newTime.getHours() + parseInt(extensionHours.value))
    
    return formatDateTime(newTime.toISOString())
}

const extendBooking = () => {
    extensionForm.extend_hours = extensionHours.value
    
    extensionForm.post(route('bookings.extend', props.booking.id), {
        onSuccess: (response) => {
            // Show success message
            alert('Booking successfully extended! Your new return time has been updated.')
            // The parent component should handle refreshing the data
            location.reload()
        },
        onError: (errors) => {
            console.error('Extension failed:', errors)
            const errorMessage = Object.values(errors)[0] || 'Failed to extend booking. Please try again.'
            alert(errorMessage)
        }
    })
}
</script>
