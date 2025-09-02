<template>
    <!-- Live Overcharge Tracking (for confirmed bookings) -->
    <div v-if="booking.status === 'confirmed'" class="glass-card border border-white/20 rounded-lg p-6 bg-white/5 backdrop-blur-sm shadow-glow">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">
                ⚠️ Live Overcharge Monitoring
            </h3>
            <button 
                @click="$emit('refreshOverchargeStatus')"
                class="text-blue-600 hover:text-blue-800 text-sm font-medium transition-colors"
            >
                🔄 Refresh Status
            </button>
        </div>

        <!-- Time Status -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div class="bg-gray-800/60 backdrop-blur-sm border border-gray-600/30 rounded-lg p-3">
                <div class="text-sm text-gray-300">Current Status</div>
                <div :class="timeRemaining.isOverdue ? 'text-red-400 font-bold' : 'text-green-400 font-semibold'">
                    {{ timeRemaining.text }}
                </div>
            </div>
            <div class="bg-gray-800/60 backdrop-blur-sm border border-gray-600/30 rounded-lg p-3">
                <div class="text-sm text-gray-300">Expected Return</div>
                <div class="font-semibold text-white">
                    {{ formatDateTime(expectedReturnTime || estimatedReturn) }}
                </div>
            </div>
        </div>

        <!-- Potential Overcharges -->
        <div v-if="potentialOvercharges && potentialOvercharges.length > 0" class="bg-yellow-500/10 border border-yellow-500/20 rounded-lg p-4">
            <h4 class="font-semibold text-yellow-300 mb-3">
                🚨 Active Overcharges Detected
            </h4>
            <div class="space-y-3">
                <div v-for="overcharge in potentialOvercharges" :key="overcharge.overcharge_type_id" 
                     class="bg-gray-800/60 backdrop-blur-sm border border-gray-600/30 rounded p-3 border-l-4 border-yellow-400">
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="font-medium text-yellow-300">
                                {{ getOverchargeTypeName(overcharge.overcharge_type_id) }}
                            </div>
                            <div class="text-sm text-gray-300">
                                {{ formatOverchargeDetails(overcharge.details) }}
                            </div>
                            <div class="text-xs text-gray-400 mt-1">
                                {{ overcharge.overcharge_type_id === 1 ? formatTimeFromHours(overcharge.units) : `${overcharge.units} km` }} 
                                × ₱{{ overcharge.rate_applied }} per {{ overcharge.overcharge_type_id === 1 ? 'hour' : 'km' }}
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="font-bold text-yellow-300">₱{{ overcharge.amount }}</div>
                            <div class="text-xs text-yellow-400">Will be applied at completion</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Contact Renter Button -->
            <div class="mt-4 pt-3 border-t border-yellow-500/20">
                <button 
                    @click="$emit('contactRenter')"
                    class="w-full bg-yellow-600 hover:bg-yellow-700 text-white font-medium py-2 px-4 rounded-lg transition-colors"
                >
                    📞 Contact Renter About Overcharges
                </button>
            </div>
        </div>

        <!-- Show manual overcharge detection when overdue but no automatic overcharges -->
        <div v-else-if="booking.status === 'confirmed' && timeRemaining.isOverdue" class="bg-red-500/10 border border-red-500/20 rounded-lg p-4">
            <div class="flex items-start">
                <div class="text-red-400 mr-3">⚠️</div>
                <div class="flex-1">
                    <div class="font-semibold text-red-300">Vehicle is Overdue - Potential Overcharges</div>
                    <div class="text-sm text-red-200 mt-1">
                        This rental is {{ timeRemaining.text }}. Overcharges may apply when the booking is completed.
                    </div>
                    <div class="text-xs text-red-300 mt-2">
                        Automatic overcharge calculation will occur when the booking is marked as completed.
                        Contact the renter to discuss return or extension options.
                    </div>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-red-500/20">
                <button 
                    @click="$emit('contactRenter')"
                    class="w-full bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition-colors"
                >
                    📞 Contact Renter - Vehicle Overdue
                </button>
            </div>
        </div>

        <!-- No Overcharges -->
        <div v-else-if="booking.status === 'confirmed'" class="bg-green-500/10 border border-green-500/20 rounded-lg p-4">
            <div class="flex items-center">
                <div class="text-green-400 mr-3">✅</div>
                <div>
                    <div class="font-semibold text-green-300">No Overcharges Detected</div>
                    <div class="text-sm text-green-200">Rental is proceeding normally within guidelines</div>
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
    potentialOvercharges: Array,
});

defineEmits(['refreshOverchargeStatus', 'contactRenter']);

const timeRemaining = computed(() => {
    const now = new Date();
    const expected = new Date(props.expectedReturnTime || props.estimatedReturn);
    const diffMs = expected.getTime() - now.getTime();
    const diffHours = Math.floor(diffMs / (1000 * 60 * 60));
    const diffMinutes = Math.floor((diffMs % (1000 * 60 * 60)) / (1000 * 60));
    
    if (diffMs <= 0) {
        const overdueHours = Math.floor(Math.abs(diffMs) / (1000 * 60 * 60));
        const overdueMinutes = Math.floor((Math.abs(diffMs) % (1000 * 60 * 60)) / (1000 * 60));
        
        return {
            isOverdue: true,
            text: overdueHours > 0 
                ? `${overdueHours}h ${overdueMinutes}m overdue` 
                : `${overdueMinutes}m overdue`
        };
    }
    
    return {
        isOverdue: false,
        text: diffHours > 0 
            ? `${diffHours}h ${diffMinutes}m remaining` 
            : `${diffMinutes}m remaining`
    };
});

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
