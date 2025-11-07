<template>
    <div class="glass-card border border-white/20 rounded-lg p-6 bg-white/5 backdrop-blur-sm shadow-glow">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-white flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Driver's License Verification
            </h2>
            <span 
                :class="booking.user.kyc_status === 'approved' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'"
                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
            >
                {{ booking.user.kyc_status === 'approved' ? '✓ Verified' : '⚠ ' + booking.user.kyc_status }}
            </span>
        </div>
        
        <div v-if="booking.user.drivers_license_front_url || booking.user.drivers_license_back_url" 
             class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div v-if="booking.user.drivers_license_front_url" class="text-center">
                <p class="text-sm text-white/70 mb-3 font-medium">Front Side</p>
                <div class="bg-white/10 p-4 rounded-lg border border-white/20">
                    <img
                        :src="booking.user.drivers_license_front_url"
                        alt="Driver's License Front"
                        class="w-full h-48 object-cover rounded border border-gray-600/30 cursor-pointer hover:opacity-75 transition-opacity"
                        @click="$emit('open-license-modal', 'front', booking.user.drivers_license_front_url)"
                    />
                </div>
            </div>
            <div v-if="booking.user.drivers_license_back_url" class="text-center">
                <p class="text-sm text-white/70 mb-3 font-medium">Back Side</p>
                <div class="bg-white/10 p-4 rounded-lg border border-white/20">
                    <img
                        :src="booking.user.drivers_license_back_url"
                        alt="Driver's License Back"
                        class="w-full h-48 object-cover rounded border border-gray-600/30 cursor-pointer hover:opacity-75 transition-opacity"
                        @click="$emit('open-license-modal', 'back', booking.user.drivers_license_back_url)"
                    />
                </div>
            </div>
        </div>
        
        <div v-else class="text-center py-8">
            <svg class="w-12 h-12 text-white/30 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <p class="text-white/60 font-medium">No driver's license uploaded</p>
            <p class="text-sm text-white/40 mt-1">This renter has not completed Driver's License Verification</p>
        </div>
        
        <div v-if="booking.user.kyc_status !== 'approved'" class="mt-4 p-3 bg-yellow-500/20 border border-yellow-500/30 rounded-lg">
            <div class="flex items-start space-x-2">
                <div class="text-yellow-300">⚠️</div>
                <div class="text-sm text-yellow-200">
                    This renter's identity verification is {{ booking.user.kyc_status }}. 
                    Please verify their documents before confirming the booking.
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
const props = defineProps({
    booking: Object,
});

defineEmits(['open-license-modal']);
</script>
