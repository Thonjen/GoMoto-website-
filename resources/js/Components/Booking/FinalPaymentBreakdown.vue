<template>
    <!-- Final Payment Breakdown for completed bookings -->
    <div v-if="booking.status === 'completed'" class="glass-card border border-white/20 rounded-lg p-6 bg-white/5 backdrop-blur-sm shadow-glow">
        <h2 class="text-xl font-semibold mb-4 text-white">
            💰 Final Payment Breakdown
        </h2>
        
        <!-- Base Rental Charges -->
        <div class="bg-gray-800/60 backdrop-blur-sm border border-gray-600/30 rounded-lg p-4 mb-4">
            <h3 class="text-lg font-medium text-white mb-3 flex items-center">
                🎯 Base Rental Fee
            </h3>
            <div class="space-y-2 text-sm text-gray-300">
                <div class="flex justify-between">
                    <span>Duration:</span>
                    <span class="text-white">{{ booking.pricing_tier.duration_from }} {{ booking.pricing_tier.duration_unit }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Rate:</span>
                    <span class="text-white">₱{{ booking.pricing_tier.price }}</span>
                </div>
                <div class="flex justify-between font-semibold text-green-400 border-t border-gray-600/30 pt-2">
                    <span>Base Total:</span>
                    <span>₱{{ booking.total_amount }}</span>
                </div>
            </div>
        </div>

        <!-- Overcharge Fees (if any) -->
        <div v-if="booking.overcharges && booking.overcharges.length > 0" class="bg-red-500/10 border border-red-500/20 rounded-lg p-4 mb-4">
            <h3 class="text-lg font-medium text-red-300 mb-3 flex items-center">
                ⚠️ Additional Overcharge Fees
            </h3>
            <div class="space-y-3">
                <div v-for="overcharge in booking.overcharges" :key="overcharge.id" class="bg-gray-800/60 backdrop-blur-sm border border-gray-600/30 rounded p-3">
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="font-medium text-red-300">
                                {{ getOverchargeTypeName(overcharge.overcharge_type_id) }}
                            </div>
                            <div class="text-sm text-gray-300">
                                {{ formatOverchargeDetails(overcharge.details) }}
                            </div>
                            <div class="text-xs text-gray-400 mt-1">
                                {{ overcharge.units }} {{ overcharge.overcharge_type_id === 1 ? 'hours' : 'km' }} 
                                × ₱{{ overcharge.rate_applied }}
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="font-semibold text-red-600">₱{{ overcharge.amount }}</div>
                            <div :class="overcharge.is_paid ? 'text-green-600' : 'text-orange-600'" class="text-xs font-medium">
                                {{ overcharge.is_paid ? '✅ PAID' : '⏳ PENDING' }}
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Overcharge Summary -->
                <div class="border-t pt-3">
                    <div class="flex justify-between font-semibold text-red-600">
                        <span>Total Overcharges:</span>
                        <span>₱{{ calculateTotalOvercharges }}</span>
                    </div>
                    <div class="flex justify-between text-sm text-gray-600">
                        <span>Paid:</span>
                        <span class="text-green-600">₱{{ calculatePaidOvercharges }}</span>
                    </div>
                    <div class="flex justify-between text-sm text-gray-600">
                        <span>Pending:</span>
                        <span class="text-orange-600">₱{{ calculateUnpaidOvercharges }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- No Overcharges Clean Rental -->
        <div v-else class="bg-green-500/10 border border-green-500/20 rounded-lg p-4 mb-4">
            <div class="flex items-center">
                <div class="text-green-400 mr-3">✅</div>
                <div>
                    <div class="font-semibold text-green-300">Clean Rental - No Overcharges</div>
                    <div class="text-sm text-green-200">
                        Rental completed without any violations. Total earned: ₱{{ formatCurrency(booking.total_amount) }}
                    </div>
                </div>
            </div>
            
            <!-- Debug Information -->
            <div class="mt-3 pt-3 border-t border-green-500/20 text-xs text-gray-400">
                <div>Expected Return: {{ expectedReturnTime || estimatedReturn }}</div>
                <div>Actual Return: {{ booking.return_time ? formatDateTime(booking.return_time) : 'Not set' }}</div>
                <div>Overcharges Array: {{ booking.overcharges ? booking.overcharges.length : 'null' }} items</div>
                <div>Has Overcharges Flag: {{ booking.has_overcharges ? 'true' : 'false' }}</div>
                <div>Total Overcharges Amount: ₱{{ formatCurrency(booking.total_overcharges || 0) }}</div>
            </div>
            
            <!-- Manual Overcharge Calculation -->
            <div class="mt-3 pt-3 border-t border-green-500/20">
                <button @click="$emit('recalculateOvercharges')" 
                        :disabled="processing"
                        class="text-sm bg-yellow-600 hover:bg-yellow-700 disabled:bg-gray-400 text-white font-medium py-2 px-3 rounded transition-colors disabled:cursor-not-allowed">
                    🔄 Recalculate Overcharges
                </button>
                <div class="text-xs text-gray-400 mt-1">
                    Click if this booking should have overcharges but doesn't show any
                </div>
            </div>
            
            <!-- Manual Overcharge Addition -->
            <div class="mt-3 pt-3 border-t border-green-200">
                <button @click="$emit('toggleManualOverchargeForm')" 
                        class="text-sm bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-3 rounded transition-colors">
                    ➕ Add Manual Overcharge
                </button>
            </div>
        </div>

        <!-- Grand Total -->
        <div class="bg-gray-800 text-white rounded-lg p-4">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-semibold">Total Amount Due</h3>
                    <p class="text-sm text-gray-300">Base rental + overcharges</p>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold">₱{{ grandTotal }}</div>
                    <div class="text-sm text-gray-300">
                        {{ allOverchargesPaid ? '✅ Fully Paid' : '⏳ Partial Payment' }}
                    </div>
                </div>
            </div>
            
            <!-- Payment Actions for Overcharges -->
            <div v-if="unpaidOvercharges.length > 0" class="mt-4 pt-4 border-t border-gray-600">
                <h4 class="text-sm font-medium mb-2">Overcharge Collection Actions:</h4>
                <div class="space-y-2">
                    <button
                        v-for="overcharge in unpaidOvercharges"
                        :key="overcharge.id"
                        @click="$emit('markOverchargeAsPaid', overcharge.id)"
                        :disabled="processing"
                        class="text-xs bg-green-600 hover:bg-green-700 disabled:bg-gray-400 text-white font-medium py-1 px-2 rounded transition-colors disabled:cursor-not-allowed">
                        Mark {{ getOverchargeTypeName(overcharge.overcharge_type_id) }} as Paid (₱{{ formatCurrency(overcharge.amount) }})
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    booking: Object,
    expectedReturnTime: String,
    estimatedReturn: String,
    processing: Boolean,
});

defineEmits(['recalculateOvercharges', 'toggleManualOverchargeForm', 'markOverchargeAsPaid']);

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

const allOverchargesPaid = computed(() => {
    if (!props.booking.overcharges || props.booking.overcharges.length === 0) return true;
    return props.booking.overcharges.every(o => o.is_paid);
});

const unpaidOvercharges = computed(() => {
    if (!props.booking.overcharges) return [];
    return props.booking.overcharges.filter(o => !o.is_paid);
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
    return details.replace(/Late return by ([\d.]+) hours/g, (match, hours) => {
        const hoursNumber = parseFloat(hours);
        const formattedTime = formatTimeFromHours(hoursNumber);
        return `Late return by ${formattedTime}`;
    });
}

function formatTimeFromHours(hours) {
    if (!hours || hours <= 0) return '0 minutes';
    
    if (hours < 1) {
        const minutes = Math.round(hours * 60);
        return minutes === 1 ? '1 minute' : `${minutes} minutes`;
    } else {
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
