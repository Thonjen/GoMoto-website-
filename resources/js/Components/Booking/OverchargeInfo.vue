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

            <div v-if="booking.return_time" class="flex items-center">
                <div class="flex-shrink-0 w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                    <svg class="w-4 h-4 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-900">Actual Return</p>
                    <p class="text-sm text-gray-500">{{ formatDateTime(booking.return_time) }}</p>
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
                        <p class="text-sm font-bold text-red-800">₱{{ formatCurrency(overcharge.amount) }}</p>
                        <p class="text-xs" :class="overcharge.is_paid ? 'text-green-600' : 'text-red-600'">
                            {{ overcharge.is_paid ? 'Paid' : 'Unpaid' }}
                        </p>
                    </div>
                </div>
            </div>
            <div v-if="booking.total_overcharges > 0" class="mt-3 pt-3 border-t border-red-200">
                <div class="flex justify-between items-center">
                    <span class="text-sm font-medium text-red-800">Total Overcharges:</span>
                    <span class="text-lg font-bold text-red-800">₱{{ formatCurrency(booking.total_overcharges) }}</span>
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
            <h5 class="text-md font-medium text-blue-600 mb-3">Request Booking Extension</h5>
            <p class="text-sm text-gray-600 mb-4">
                Request additional time from the vehicle owner to avoid late return charges.
            </p>
            
            <!-- Show recent extension requests -->
            <div v-if="booking.extension_requests && booking.extension_requests.length > 0" class="mb-4 space-y-3">
                <div v-for="request in booking.extension_requests" :key="request.id" 
                     :class="{
                         'bg-yellow-50 border-yellow-200': request.status === 'pending',
                         'bg-green-50 border-green-200': request.status === 'approved', 
                         'bg-red-50 border-red-200': request.status === 'rejected'
                     }" 
                     class="border rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg v-if="request.status === 'pending'" class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                            </svg>
                            <svg v-else-if="request.status === 'approved'" class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            <svg v-else class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 :class="{
                                'text-yellow-800': request.status === 'pending',
                                'text-green-800': request.status === 'approved',
                                'text-red-800': request.status === 'rejected'
                            }" class="text-sm font-medium">
                                Extension Request {{ request.status === 'pending' ? 'Pending' : (request.status === 'approved' ? 'Approved' : 'Rejected') }}
                            </h3>
                            <div :class="{
                                'text-yellow-700': request.status === 'pending',
                                'text-green-700': request.status === 'approved',
                                'text-red-700': request.status === 'rejected'
                            }" class="mt-2 text-sm">
                                <p>{{ request.requested_hours }} hours extension (₱{{ formatCurrency(request.calculated_cost) }})</p>
                                <p v-if="request.status === 'pending'" class="mt-1">Waiting for owner approval.</p>
                                <p v-else-if="request.status === 'approved'" class="mt-1">Your extension has been approved!</p>
                                <p v-else class="mt-1">Extension request was rejected.</p>
                                <p v-if="request.owner_notes" class="mt-2 font-medium">
                                    Owner notes: {{ request.owner_notes }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <form v-if="!booking.pending_extension_request" @submit.prevent="requestExtension" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">
                        Request extension (hours)
                    </label>
                    <input
                        type="number"
                        min="1"
                        :max="maxExtensionHours"
                        v-model="extensionHours"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        placeholder="4"
                    />
                    <p class="mt-1 text-xs text-gray-500">
                        Maximum {{ maxExtensionHours }} hours allowed for this vehicle
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">
                        Reason for extension (optional)
                    </label>
                    <textarea
                        v-model="extensionReason"
                        rows="3"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        placeholder="Please explain why you need the extension..."
                    ></textarea>
                </div>

                <div v-if="extensionHours > 0" class="bg-blue-50 p-3 rounded-md">
                    <p class="text-sm text-blue-800">
                        Estimated cost: ₱{{ calculateExtensionCost() }}
                    </p>
                    <p class="text-sm text-blue-600">
                        New return time: {{ getNewReturnTime() }}
                    </p>
                    <p class="text-xs text-blue-600 mt-1">
                        *Subject to owner approval
                    </p>
                </div>

                <button
                    type="submit"
                    :disabled="extensionForm.processing || !extensionHours"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50"
                >
                    {{ extensionForm.processing ? 'Submitting Request...' : 'Request Extension' }}
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
const extensionReason = ref('')

const extensionForm = useForm({
    requested_hours: 4,
    reason: ''
})

const isOverdue = computed(() => {
    if (props.booking.return_time) {
        return new Date(props.booking.return_time) > new Date(props.expectedReturnTime)
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


const formatCurrency = (amount) => {
    if (amount === null || amount === undefined || isNaN(amount)) return '0.00';
    return parseFloat(amount).toFixed(2);
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

const maxExtensionHours = computed(() => {
    return props.booking.vehicle?.max_extension_hours || 72
})

const requestExtension = () => {
    extensionForm.requested_hours = extensionHours.value
    extensionForm.reason = extensionReason.value
    
    extensionForm.post(route('bookings.requestExtension', props.booking.id), {
        onSuccess: () => {
            // Show success message
            alert('Extension request submitted successfully! The vehicle owner will review your request.')
            // Reload the page to show the pending request
            location.reload()
        },
        onError: (errors) => {
            console.error('Extension request failed:', errors)
            const errorMessage = Object.values(errors)[0] || 'Failed to submit extension request. Please try again.'
            alert(errorMessage)
        }
    })
}
</script>
