<template>
    <OwnerLayout>
        <div class="glass-card-dark shadow-glow overflow-hidden">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h1 class="text-3xl font-bold text-white">
                            Booking Requests
                        </h1>
                        <p class="text-white/70 mt-2">
                            Manage bookings for your vehicles
                        </p>
                    </div>
                    
                    <!-- View Toggle -->
                    <div class="flex bg-white/10 rounded-lg p-1 backdrop-blur-sm border border-white/20">
                        <Link
                            :href="route('owner.bookings.index')"
                            class="px-3 py-2 rounded-md text-sm font-medium transition-all bg-white/20 backdrop-blur-sm text-white border border-white/30"
                        >
                            List View
                        </Link>
                        <Link
                            :href="route('owner.bookings.calendar')"
                            class="px-3 py-2 rounded-md text-sm font-medium transition-all hover:bg-white/20 text-white/70 hover:text-white"
                        >
                            Calendar View
                        </Link>
                    </div>
                </div>

                <div v-if="bookings.length === 0" class="text-center py-12">
                    <div class="mb-4">
                        <svg
                            class="mx-auto h-12 w-12 text-white/40"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
                            />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-white">
                        No booking requests
                    </h3>
                    <p class="mt-2 text-sm text-white/60">
                        You don't have any booking requests yet.
                    </p>
                </div>

                <div v-else class="space-y-6">
                    <div
                        v-for="booking in bookings"
                        :key="booking.id"
                        class="glass-card-dark border border-white/20 rounded-lg overflow-hidden shadow-sm hover:bg-white/5 transition-all duration-200 backdrop-blur-sm"
                    >
                        <div class="p-6">
                            <div class="flex items-start justify-between">
                                <div class="flex space-x-4">
                                    <img
                                        :src="
                                            booking.vehicle.main_photo_url ||
                                            '/images/placeholder-vehicle.jpg'
                                        "
                                        :alt="`${booking.vehicle.brand?.name} ${booking.vehicle.type?.sub_type}`"
                                        class="w-20 h-20 object-cover rounded-lg border border-white/20"
                                    />

                                    <div class="flex-1">
                                        <h3
                                            class="text-lg font-semibold text-white"
                                        >
                                            {{ booking.vehicle.brand?.name }}
                                            {{ booking.vehicle.type?.sub_type }}
                                        </h3>
                                        <p class="text-sm text-white/60 mb-2">
                                            Booking #{{ booking.id }} by
                                            {{ booking.user.first_name }}
                                            {{ booking.user.last_name }}
                                        </p>

                                        <div
                                            class="grid grid-cols-2 gap-4 text-sm text-white/70"
                                        >
                                            <div>
                                                <span class="font-medium text-white"
                                                    >Duration:</span
                                                >
                                                {{
                                                    booking.pricing_tier
                                                        ?.duration_from
                                                }}
                                                {{
                                                    booking.pricing_tier
                                                        ?.duration_from === 1
                                                        ? booking.pricing_tier?.duration_unit?.slice(
                                                              0,
                                                              -1
                                                          )
                                                        : booking.pricing_tier
                                                              ?.duration_unit
                                                }}
                                            </div>
                                            <div>
                                                <span class="font-medium"
                                                    >Amount:</span
                                                >
                                                ₱{{ booking.total_amount }}
                                            </div>
                                            <div>
                                                <span class="font-medium"
                                                    >Pickup:</span
                                                >
                                                {{
                                                    formatDateTime(
                                                        booking.pickup_datetime
                                                    )
                                                }}
                                            </div>
                                            <div>
                                                <span class="font-medium"
                                                    >Payment:</span
                                                >
                                                {{
                                                    booking.payment
                                                        ?.payment_mode?.name ||
                                                    "N/A"
                                                }}
                                            </div>
                                        </div>

                                        <!-- Customer Contact -->
                                        <div
                                            class="mt-3 flex items-center space-x-4 text-sm text-white/70"
                                        >
                                            <div>
                                                <span class="font-medium"
                                                    >Customer:</span
                                                >
                                                {{ booking.user.email }}
                                            </div>
                                            <div v-if="booking.user.phone">
                                                <span class="font-medium"
                                                    >Phone:</span
                                                >
                                                <a
                                                    :href="`tel:${booking.user.phone}`"
                                                    class="text-blue-600 hover:text-blue-800"
                                                >
                                                    {{ booking.user.phone }}
                                                </a>
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
                                        :class="
                                            getPaymentStatusClass(
                                                booking.payment.paid_at
                                            )
                                        "
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                    >
                                        {{
                                            booking.payment.paid_at
                                                ? "Payment Confirmed"
                                                : "Payment Pending"
                                        }}
                                    </span>

                                    <!-- Overcharge Alert for Active Bookings -->
                                    <div v-if="booking.status === 'confirmed' && isBookingOverdue(booking)" 
                                         class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-500/20 text-red-300 border border-red-400/30">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                        </svg>
                                        ⚠️ Potential Overcharges
                                    </div>
                                    
                                    <div v-else-if="booking.status === 'confirmed'" 
                                         class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-500/20 text-green-300 border border-green-400/30">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        🟢 Active Rental
                                    </div>
                                </div>
                            </div>

                            <!-- Payment Receipt -->
                            <div
                                v-if="
                                    booking.payment?.receipt_image &&
                                    !booking.payment.paid_at
                                "
                                class="mt-4 p-4 bg-yellow-500/20 border border-yellow-400/30 rounded-lg backdrop-blur-sm"
                            >
                                <h4
                                    class="text-sm font-medium text-yellow-300 mb-2"
                                >
                                    Payment Proof Submitted
                                </h4>
                                <div class="flex items-start space-x-3">
                                    <img
                                        :src="booking.payment.receipt_image"
                                        alt="Payment Receipt"
                                        class="w-20 h-20 object-cover rounded border border-white/20 cursor-pointer"
                                        @click="
                                            openReceiptModal(
                                                booking.payment.receipt_image
                                            )
                                        "
                                    />
                                    <p class="text-sm text-yellow-200">
                                        Customer uploaded payment proof. Please
                                        verify and confirm the payment.
                                    </p>
                                </div>
                            </div>

                            <!-- Driver's License Verification Section -->
                            <div v-if="booking.status === 'pending'" class="mt-4 p-4 bg-blue-50/20 border border-black-400/30 rounded-lg backdrop-blur-sm">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="text-sm font-medium text-blue-300">
                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V4a2 2 0 012-2h2a2 2 0 012 2v2m-4 0a2 2 0 012 2v2a2 2 0 01-2 2H9a2 2 0 01-2-2V8a2 2 0 012-2h2z"></path>
                                        </svg>
                                        Renter's Driver License Verification
                                    </h4>
                                    <span 
                                        :class="booking.user.kyc_status === 'approved' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'"
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                    >
                                        {{ booking.user.kyc_status === 'approved' ? '✓ Verified' : '⚠ Pending' }}
                                    </span>
                                </div>
                                
                                <div v-if="booking.user.drivers_license_front_url || booking.user.drivers_license_back_url" 
                                     class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <div v-if="booking.user.drivers_license_front_url" class="text-center">
                                        <p class="text-xs text-blue-200 mb-2 font-medium">Front Side</p>
                                        <img 
                                            :src="booking.user.drivers_license_front_url" 
                                            alt="Driver's License Front"
                                            class="w-full h-24 object-contain bg-white/10 rounded border border-white/20 cursor-pointer hover:bg-white/20 transition-colors"
                                            @click="openReceiptModal(booking.user.drivers_license_front_url)"
                                        />
                                    </div>
                                    <div v-if="booking.user.drivers_license_back_url" class="text-center">
                                        <p class="text-xs text-blue-200 mb-2 font-medium">Back Side</p>
                                        <img 
                                            :src="booking.user.drivers_license_back_url" 
                                            alt="Driver's License Back"
                                            class="w-full h-24 object-contain bg-white/10 rounded border border-white/20 cursor-pointer hover:bg-white/20 transition-colors"
                                            @click="openReceiptModal(booking.user.drivers_license_back_url)"
                                        />
                                    </div>
                                </div>
                                
                                <div v-else class="text-center py-4">
                                    <svg class="w-8 h-8 text-white/30 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                    </svg>
                                    <p class="text-sm text-white/50">No driver's license uploaded</p>
                                    <p class="text-xs text-white/40 mt-1">Renter needs to complete Driver's License verification</p>
                                </div>
                                
                                <div v-if="booking.user.kyc_status !== 'approved'" class="mt-3 p-2 bg-yellow-500/20 border border-yellow-500/30 rounded text-center">
                                    <p class="text-xs text-yellow-200">
                                        ⚠️ Consider requiring Driver's License Verification before confirming this booking
                                    </p>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="mt-4 flex justify-end space-x-3">
                                <button
                                    @click="viewBooking(booking.id)"
                                    class="inline-flex items-center px-3 py-1.5 border border-white/30 shadow-sm text-xs font-medium rounded text-white bg-white/10 hover:bg-white/20 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 backdrop-blur-sm transition-colors"
                                >
                                    View Details
                                </button>

                                <!-- Actions for pending bookings -->
                                <div
                                    v-if="booking.status === 'pending'"
                                    class="flex space-x-2"
                                >
                                    <button
                                        v-if="
                                            booking.payment?.receipt_image &&
                                            !booking.payment.paid_at
                                        "
                                        @click="confirmPayment(booking.id)"
                                        :disabled="processing[booking.id]"
                                        class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50 transition-colors"
                                    >
                                        {{
                                            processing[booking.id]
                                                ? "Processing..."
                                                : "Confirm Payment"
                                        }}
                                    </button>

                                    <button
                                        v-if="
                                            booking.payment?.type === 'cod' ||
                                            booking.payment?.paid_at
                                        "
                                        @click="confirmBooking(booking.id)"
                                        :disabled="processing[booking.id]"
                                        class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 transition-colors"
                                    >
                                        {{
                                            processing[booking.id]
                                                ? "Processing..."
                                                : "Confirm Booking"
                                        }}
                                    </button>

                                    <button
                                        @click="rejectBooking(booking.id)"
                                        :disabled="processing[booking.id]"
                                        class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50 transition-colors"
                                    >
                                        {{
                                            processing[booking.id]
                                                ? "Processing..."
                                                : "Reject"
                                        }}
                                    </button>
                                </div>

                                <!-- Complete booking action -->
                                <button
                                    v-if="booking.status === 'confirmed'"
                                    @click="completeBooking(booking.id)"
                                    :disabled="processing[booking.id]"
                                    class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 disabled:opacity-50 transition-colors"
                                >
                                    {{
                                        processing[booking.id]
                                            ? "Processing..."
                                            : "Mark Complete"
                                    }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Receipt Modal -->
        <div
            v-if="showReceiptModal"
            @click.self="closeReceiptModal"
            class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4"
        >
            <div
                class="glass-card-dark rounded-lg max-w-2xl w-full max-h-[90vh] overflow-auto border border-white/20 shadow-glow"
            >
                <div class="p-4 border-b border-white/20 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-white">Payment Receipt</h3>
                    <button
                        @click="closeReceiptModal"
                        class="text-white/70 hover:text-white transition-colors"
                    >
                        <svg
                            class="w-6 h-6"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"
                            ></path>
                        </svg>
                    </button>
                </div>
                <div class="p-4 text-center bg-white/5">
                    <img
                        :src="selectedReceiptUrl"
                        alt="Payment Receipt"
                        class="max-w-full h-auto mx-auto rounded border border-white/20"
                    />
                </div>
            </div>
        </div>
    </OwnerLayout>
</template>

<script setup>
import { ref, reactive } from "vue";
import { router, Link } from "@inertiajs/vue3";
import OwnerLayout from "@/Layouts/OwnerLayout.vue";

const props = defineProps({
    bookings: Array,
});

const processing = reactive({});
const showReceiptModal = ref(false);
const selectedReceiptUrl = ref("");

function getStatusClass(status) {
    const classes = {
        pending: "bg-yellow-100 text-yellow-800",
        confirmed: "bg-blue-100 text-blue-800",
        completed: "bg-green-100 text-green-800",
        cancelled: "bg-red-100 text-red-800",
    };
    return classes[status] || "bg-white/20 text-white";
}

function getPaymentStatusClass(paidAt) {
    return paidAt
        ? "bg-green-100 text-green-800"
        : "bg-yellow-100 text-yellow-800";
}

function formatStatus(status) {
    const statusMap = {
        pending: "Pending",
        confirmed: "Confirmed",
        completed: "Completed",
        cancelled: "Cancelled",
    };
    return statusMap[status] || status;
}

function formatDateTime(datetime) {
    return new Date(datetime).toLocaleString("en-US", {
        month: "short",
        day: "numeric",
        year: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
}

function openReceiptModal(receiptUrl) {
    selectedReceiptUrl.value = receiptUrl;
    showReceiptModal.value = true;
}

function closeReceiptModal() {
    showReceiptModal.value = false;
    selectedReceiptUrl.value = "";
}

function viewBooking(bookingId) {
    router.visit(route("owner.bookings.show", bookingId));
}

function confirmPayment(bookingId) {
    processing[bookingId] = true;
    router.post(
        route("owner.bookings.confirmPayment", bookingId),
        {},
        {
            onFinish: () => {
                processing[bookingId] = false;
            },
        }
    );
}

function confirmBooking(bookingId) {
    processing[bookingId] = true;
    router.post(
        route("owner.bookings.confirm", bookingId),
        {},
        {
            onFinish: () => {
                processing[bookingId] = false;
            },
        }
    );
}

function rejectBooking(bookingId) {
    if (confirm("Are you sure you want to reject this booking?")) {
        processing[bookingId] = true;
        router.post(
            route("owner.bookings.reject", bookingId),
            {},
            {
                onFinish: () => {
                    processing[bookingId] = false;
                },
            }
        );
    }
}

function completeBooking(bookingId) {
    processing[bookingId] = true;
    router.post(
        route("owner.bookings.complete", bookingId),
        {},
        {
            onFinish: () => {
                processing[bookingId] = false;
            },
        }
    );
}

function isBookingOverdue(booking) {
    if (booking.status !== 'confirmed' || booking.return_time) {
        return false; // Not active or already returned
    }
    
    const now = new Date();
    const pickup = new Date(booking.pickup_datetime);
    const tier = booking.pricing_tier;
    
    // Calculate expected return time
    let expectedReturn = new Date(pickup);
    switch (tier.duration_unit) {
        case 'minutes':
            expectedReturn.setMinutes(expectedReturn.getMinutes() + tier.duration_from);
            break;
        case 'hours':
            expectedReturn.setHours(expectedReturn.getHours() + tier.duration_from);
            break;
        case 'days':
            expectedReturn.setDate(expectedReturn.getDate() + tier.duration_from);
            break;
        default:
            expectedReturn.setDate(expectedReturn.getDate() + 1);
    }
    
    // Consider extended time if booking was extended
    if (booking.is_extended && booking.extended_until) {
        expectedReturn = new Date(booking.extended_until);
    }
    
    return now > expectedReturn;
}
</script>
