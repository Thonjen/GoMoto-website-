<template>
    <div class="glass-card border border-white/20 rounded-lg p-6 bg-white/5 backdrop-blur-sm shadow-glow">
        <h2 class="text-xl font-semibold mb-4 text-white">
            Booking Details
        </h2>
        <div class="grid grid-cols-2 gap-6">
            <div>
                <span class="text-sm text-white/70">Duration</span>
                <div class="font-semibold">
                    {{ booking.pricing_tier.duration_from }}
                    {{ booking.pricing_tier.duration_from === 1
                        ? booking.pricing_tier.duration_unit.slice(0, -1)
                        : booking.pricing_tier.duration_unit
                    }}
                </div>
            </div>
            <div>
                <span class="text-sm text-white/70">Base Rental Fee</span>
                <div class="text-xl font-bold text-green-600">
                    ₱{{ formatCurrency(booking.total_amount) }}
                </div>
            </div>
            <div>
                <span class="text-sm text-white/70">Pickup Date & Time</span>
                <div class="font-semibold text-sm">
                    {{ formatDateTime(booking.pickup_datetime) }}
                </div>
            </div>
            <div>
                <span class="text-sm text-white/70">Estimated Return</span>
                <div class="font-semibold">
                    {{ estimatedReturn }}
                </div>
            </div>
        </div>

        <!-- Total Amount Summary -->
        <div class="mt-6 pt-4 border-t border-gray-200">
            <div class="glass-card border border-white/20 rounded-lg p-4 bg-white/5 backdrop-blur-sm shadow-glow" v-if="hasOvercharges">
                <h3 class="text-lg font-semibold text-white mb-4">
                    💰 Applied Overcharges
                </h3>
                <div class="space-y-3">
                    <div v-for="overcharge in booking.overcharges" :key="overcharge.id" 
                         class="bg-red-500/10 border border-red-500/20 rounded-lg p-4">
                        <div class="flex justify-between items-start">
                            <div>
                                <div class="font-medium text-red-300">
                                    {{ getOverchargeTypeName(overcharge.overcharge_type_id) }}
                                </div>
                                <div class="text-sm text-gray-300">
                                    {{ formatOverchargeDetails(overcharge.details) }}
                                </div>
                                <div class="text-xs text-gray-400 mt-1">
                                    Applied on {{ formatDateTime(overcharge.occurred_at) }}
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="font-bold text-red-300">₱{{ overcharge.amount }}</div>
                                <div :class="overcharge.is_paid ? 'text-green-400' : 'text-orange-400'" 
                                     class="text-xs font-medium">
                                    {{ overcharge.is_paid ? '✅ PAID' : '⏳ PENDING' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Overcharge Summary for Completed Bookings -->
                <div class="mt-4 bg-gray-800/60 backdrop-blur-sm border border-gray-600/30 rounded-lg p-4">
                    <div class="grid grid-cols-2 gap-4 text-sm text-gray-300">
                        <div class="flex justify-between">
                            <span>Base Rental:</span>
                            <span class="font-semibold text-white">₱{{ booking.total_amount }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Total Overcharges:</span>
                            <span class="font-semibold text-red-400">₱{{ calculateTotalOvercharges }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Paid Overcharges:</span>
                            <span class="text-green-400">₱{{ calculatePaidOvercharges }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Pending Collection:</span>
                            <span class="text-orange-400">₱{{ calculateUnpaidOvercharges }}</span>
                        </div>
                    </div>
                    <div class="border-t border-gray-600/30 mt-3 pt-3">
                        <div class="flex justify-between font-bold text-lg text-white">
                            <span>Grand Total:</span>
                            <span>₱{{ grandTotal }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    booking: Object,
    estimatedReturn: String,
});

const hasOvercharges = computed(() => {
    return props.booking.overcharges && props.booking.overcharges.length > 0;
});

const calculateTotalOvercharges = computed(() => {
    if (!props.booking.overcharges || props.booking.overcharges.length === 0) return 0;
    return props.booking.overcharges.reduce((sum, overcharge) => {
        return sum + parseFloat(overcharge.amount || 0);
    }, 0);
});

const calculatePaidOvercharges = computed(() => {
    if (!props.booking.overcharges || props.booking.overcharges.length === 0) return 0;
    return props.booking.overcharges
        .filter(o => o.is_paid)
        .reduce((sum, overcharge) => {
            return sum + parseFloat(overcharge.amount || 0);
        }, 0);
});

const calculateUnpaidOvercharges = computed(() => {
    if (!props.booking.overcharges || props.booking.overcharges.length === 0) return 0;
    return props.booking.overcharges
        .filter(o => !o.is_paid)
        .reduce((sum, overcharge) => {
            return sum + parseFloat(overcharge.amount || 0);
        }, 0);
});

const grandTotal = computed(() => {
    const baseAmount = parseFloat(props.booking.total_amount || 0);
    const overchargeAmount = calculateTotalOvercharges.value;
    return baseAmount + overchargeAmount;
});

function formatCurrency(amount) {
    if (amount === null || amount === undefined || isNaN(amount)) return '0.00';
    return parseFloat(amount).toFixed(2);
}

function formatDateTime(datetime) {
    return new Date(datetime).toLocaleString("en-US", {
        weekday: "long",
        year: "numeric",
        month: "long",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
}

function getOverchargeTypeName(typeId) {
    const types = {
        1: '⏰ Late Return',
        2: '🏙️ Out of City Use'
    };
    return types[typeId] || 'Unknown Overcharge';
}

function formatOverchargeDetails(details) {
    // Fix old overcharge details that might have decimal hours
    // Match patterns like "Late return by 0.42222222222222 hours"
    return details.replace(/Late return by ([\d.]+) hours/g, (match, hours) => {
        const hoursNumber = parseFloat(hours);
        const formattedTime = formatTimeFromHours(hoursNumber);
        return `Late return by ${formattedTime}`;
    });
}

function formatTimeFromHours(hours) {
    if (!hours || hours <= 0) return '0 minutes';
    
    if (hours < 1) {
        // Less than 1 hour, show in minutes
        const minutes = Math.round(hours * 60);
        return minutes === 1 ? '1 minute' : `${minutes} minutes`;
    } else {
        // 1 hour or more
        const wholeHours = Math.floor(hours);
        const remainingMinutes = Math.round((hours - wholeHours) * 60);
        
        const hourText = wholeHours === 1 ? '1 hour' : `${wholeHours} hours`;
        
        if (remainingMinutes > 0) {
            const minuteText = remainingMinutes === 1 ? '1 minute' : `${remainingMinutes} minutes`;
            return `${hourText} ${minuteText}`;
        } else {
            return hourText;
        }
    }
}
</script>
