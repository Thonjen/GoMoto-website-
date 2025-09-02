<template>
    <!-- Payment Proof (if GCash) -->
    <div v-if="booking.payment?.receipt_image" class="glass-card border border-white/20 rounded-lg p-6 bg-white/5 backdrop-blur-sm shadow-glow">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-white flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Payment Proof Submitted
            </h2>
            <span 
                :class="booking.payment?.paid_at ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'"
                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
            >
                {{ booking.payment?.paid_at ? '✓ Verified' : '⚠ Pending Verification' }}
            </span>
        </div>

        <div class="bg-gray-800/40 rounded-lg p-4 border border-gray-600/30">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Payment Receipt Image -->
                <div class="lg:col-span-1">
                    <div class="bg-white/5 p-4 rounded-lg border border-white/20">
                        <p class="text-sm text-white/70 mb-3 font-medium text-center">Payment Receipt</p>
                        <img
                            :src="booking.payment.receipt_image"
                            alt="Payment Receipt"
                            class="w-full h-64 object-cover rounded border border-gray-600/30 cursor-pointer hover:opacity-75 transition-opacity"
                            @click="$emit('open-receipt-modal', booking.payment.receipt_image)"
                        />
                    </div>
                </div>

                <!-- Payment Details -->
                <div class="lg:col-span-2 space-y-4">
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <!-- Row 1: Two items -->
    <div class="bg-gray-700/30 p-4 rounded-lg">
        <p class="text-sm text-white/70 mb-1">Payment Method</p>
        <p class="font-semibold text-white">{{ booking.payment?.payment_mode?.name || "N/A" }}</p>
    </div>

    <div class="bg-gray-700/30 p-4 rounded-lg">
        <p class="text-sm text-white/70 mb-1">Amount Paid</p>
        <p class="font-semibold text-green-400 text-lg">₱{{ formatCurrency(booking.total_amount) }}</p>
    </div>

    <!-- Row 2: Single item centered -->
    <div v-if="booking.payment?.reference_number" class="bg-gray-700/30 p-4 rounded-lg md:col-span-2 md:justify-self-center">
        <p class="text-sm text-white/70 mb-1">Reference Number</p>
        <p class="font-mono text-white text-sm break-all">{{ booking.payment.reference_number }}</p>
    </div>

    <!-- Optional Submitted date -->
    <div class="bg-gray-700/30 p-4 rounded-lg md:col-span-2 md:justify-self-center">
        <p class="text-sm text-white/70 mb-1">Submitted</p>
        <p class="font-semibold text-white">{{ formatDateTime(booking.payment.created_at) }}</p>
    </div>
</div>


                    <!-- Verification Instructions -->
                    <div class="bg-blue-500/10 border border-blue-500/20 rounded-lg p-4">
                        <div class="flex items-start space-x-3">
                            <div class="text-blue-400">ℹ️</div>
                            <div class="flex-1">
                                <h4 class="font-semibold text-blue-300 mb-2">Verification Instructions</h4>
                                <ul class="text-sm text-blue-200 space-y-1">
                                    <li>• Verify the payment amount matches the booking total</li>
                                    <li>• Check the reference number against your payment records</li>
                                    <li>• Confirm the payment timestamp is reasonable</li>
                                    <li>• Contact the customer if any discrepancies are found</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
const props = defineProps({
    booking: Object,
});

defineEmits(['open-receipt-modal']);

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
</script>
