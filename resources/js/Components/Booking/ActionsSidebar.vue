<template>
    <div class="glass-card border border-white/20 rounded-lg p-6 bg-white/5 backdrop-blur-sm shadow-glow sticky top-6">
        <h2 class="text-xl font-semibold mb-4 text-white">
            Actions
        </h2>

        <!-- Payment Status -->
        <div class="mb-6">
            <span class="text-sm text-white">Payment Status</span>
            <div class="mt-1">
                <span
                    :class="getPaymentStatusClass(booking.payment?.paid_at)"
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                >
                    {{
                        booking.payment?.paid_at
                            ? "Payment Confirmed"
                            : "Payment Pending"
                    }}
                </span>
            </div>
            <div class="mt-2 text-sm text-white">
                <span class="font-medium">Method:</span>
                {{ booking.payment?.payment_mode?.name || "N/A" }}
            </div>
            
            <!-- Overcharge Status (for completed bookings) -->
            <div v-if="booking.status === 'completed' && hasOvercharges" class="mt-3 pt-2 border-t">
                <span class="text-sm text-gray-500">Overcharge Status</span>
                <div class="mt-1">
                    <span v-if="allOverchargesPaid"
                          class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        ✅ All Overcharges Paid
                    </span>
                    <span v-else
                          class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                        ⏳ {{ unpaidOvercharges.length }} Pending Payment(s)
                    </span>
                </div>
                <div class="mt-1 text-xs text-gray-500">
                    Outstanding: ₱{{ formatCurrency(calculateUnpaidOvercharges) }}
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="space-y-3">
            <!-- Actions for pending bookings -->
            <div v-if="booking.status === 'pending'" class="space-y-3">
                <button
                    v-if="booking.payment?.receipt_image && !booking.payment.paid_at"
                    @click="handleConfirmPayment"
                    :disabled="processing"
                    class="w-full py-2 px-4 bg-green-600 hover:bg-green-700 disabled:bg-gray-400 text-white font-semibold rounded-lg transition-colors disabled:cursor-not-allowed"
                >
                    {{ processing ? "Processing..." : "Confirm Payment & Booking" }}
                </button>

                <button
                    v-if="booking.payment?.type === 'cod' || booking.payment?.paid_at"
                    @click="handleConfirmBooking"
                    :disabled="processing"
                    class="w-full py-2 px-4 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white font-semibold rounded-lg transition-colors disabled:cursor-not-allowed"
                >
                    {{ processing ? "Processing..." : "Confirm Booking" }}
                </button>

                <button
                    @click="handleRejectBooking"
                    :disabled="processing"
                    class="w-full py-2 px-4 bg-red-600 hover:bg-red-700 disabled:bg-gray-400 text-white font-semibold rounded-lg transition-colors disabled:cursor-not-allowed"
                >
                    {{ processing ? "Processing..." : "Reject Booking" }}
                </button>
            </div>

            <!-- Complete booking action -->
            <button
                v-if="booking.status === 'confirmed'"
                @click="handleCompleteBooking"
                :disabled="processing"
                class="w-full py-2 px-4 bg-purple-600 hover:bg-purple-700 disabled:bg-gray-400 text-white font-semibold rounded-lg transition-colors disabled:cursor-not-allowed"
            >
                {{ processing ? "Processing..." : "Mark as Completed" }}
            </button>

            <!-- Status info for completed/cancelled -->
            <div v-if="booking.status === 'completed'" class="p-3 bg-green-500/10 border border-green-500/20 rounded">
                <div class="text-green-300 font-medium text-sm">
                    Booking Completed
                </div>
                <div class="text-green-200 text-xs mt-1">
                    This booking has been marked as completed.
                </div>
            </div>

            <div v-if="booking.status === 'cancelled'" class="p-3 bg-red-500/10 border border-red-500/20 rounded">
                <div class="text-red-300 font-medium text-sm">
                    Booking Cancelled
                </div>
                <div class="text-red-200 text-xs mt-1">
                    This booking has been cancelled.
                </div>
            </div>
        </div>

        <div class="border-t border-gray-600/30 pt-4 mt-6">
            <button
                @click="$emit('goToVehicle')"
                class="w-full py-2 px-4 border border-gray-600/30 text-gray-300 hover:bg-gray-700/40 font-semibold rounded-lg transition-colors"
            >
                View Vehicle
            </button>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import Swal from 'sweetalert2';

const props = defineProps({
    booking: Object,
    processing: Boolean,
});

const emit = defineEmits(['confirmPayment', 'confirmBooking', 'rejectBooking', 'completeBooking', 'goToVehicle']);

const hasOvercharges = computed(() => {
    return props.booking.overcharges && props.booking.overcharges.length > 0;
});

const allOverchargesPaid = computed(() => {
    if (!props.booking.overcharges || props.booking.overcharges.length === 0) return true;
    return props.booking.overcharges.every(o => o.is_paid);
});

const unpaidOvercharges = computed(() => {
    if (!props.booking.overcharges) return [];
    return props.booking.overcharges.filter(o => !o.is_paid);
});

const calculateUnpaidOvercharges = computed(() => {
    if (!props.booking.overcharges || props.booking.overcharges.length === 0) return 0;
    return props.booking.overcharges
        .filter(o => !o.is_paid)
        .reduce((sum, overcharge) => {
            return sum + parseFloat(overcharge.amount || 0);
        }, 0);
});

function getPaymentStatusClass(paidAt) {
    return paidAt
        ? "bg-green-100 text-green-800"
        : "bg-yellow-100 text-yellow-800";
}

function formatCurrency(amount) {
    if (amount === null || amount === undefined || isNaN(amount)) return '0.00';
    return parseFloat(amount).toFixed(2);
}

async function handleConfirmPayment() {
    const result = await Swal.fire({
        title: 'Confirm Payment & Booking',
        text: 'Are you sure you want to confirm this payment and booking?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#16a34a',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, confirm it!',
        cancelButtonText: 'Cancel',
        background: '#1f2937',
        color: '#fff',
        customClass: {
            popup: 'swal-dark-popup'
        }
    });

    if (result.isConfirmed) {
        emit('confirmPayment');
    }
}

async function handleConfirmBooking() {
    const result = await Swal.fire({
        title: 'Confirm Booking',
        text: 'Are you sure you want to confirm this booking?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#2563eb',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, confirm it!',
        cancelButtonText: 'Cancel',
        background: '#1f2937',
        color: '#fff',
        customClass: {
            popup: 'swal-dark-popup'
        }
    });

    if (result.isConfirmed) {
        emit('confirmBooking');
    }
}

async function handleRejectBooking() {
    const result = await Swal.fire({
        title: 'Reject Booking',
        text: 'Are you sure you want to reject this booking? This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, reject it',
        cancelButtonText: 'Cancel',
        background: '#1f2937',
        color: '#fff',
        customClass: {
            popup: 'swal-dark-popup'
        }
    });

    if (result.isConfirmed) {
        emit('rejectBooking');
    }
}

async function handleCompleteBooking() {
    const result = await Swal.fire({
        title: 'Mark as Completed',
        text: 'Are you sure you want to mark this booking as completed?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#9333ea',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, complete it!',
        cancelButtonText: 'Cancel',
        background: '#1f2937',
        color: '#fff',
        customClass: {
            popup: 'swal-dark-popup'
        }
    });

    if (result.isConfirmed) {
        emit('completeBooking');
    }
}
</script>

<style scoped>
:deep(.swal-dark-popup) {
    border: 1px solid rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
}
</style>
