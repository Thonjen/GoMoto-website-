<template>
    <OwnerLayout>
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="mb-6 flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-800">
                            Booking Requests
                        </h1>
                        <p class="text-gray-600 mt-2">
                            Manage bookings for your vehicles
                        </p>
                    </div>
                    
                    <!-- View Toggle -->
                    <div class="flex bg-gray-100 rounded-lg p-1">
                        <Link
                            :href="route('owner.bookings.index')"
                            class="px-3 py-2 rounded-md text-sm font-medium transition-all bg-white shadow"
                        >
                            List View
                        </Link>
                        <Link
                            :href="route('owner.bookings.calendar')"
                            class="px-3 py-2 rounded-md text-sm font-medium transition-all hover:bg-white hover:shadow"
                        >
                            Calendar View
                        </Link>
                    </div>
                </div>

                <div v-if="bookings.length === 0" class="text-center py-12">
                    <div class="mb-4">
                        <svg
                            class="mx-auto h-12 w-12 text-gray-400"
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
                    <h3 class="text-lg font-medium text-gray-900">
                        No booking requests
                    </h3>
                    <p class="mt-2 text-sm text-gray-500">
                        You don't have any booking requests yet.
                    </p>
                </div>

                <div v-else class="space-y-6">
                    <div
                        v-for="booking in bookings"
                        :key="booking.id"
                        class="border rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-200"
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
                                        class="w-20 h-20 object-cover rounded-lg"
                                    />

                                    <div class="flex-1">
                                        <h3
                                            class="text-lg font-semibold text-gray-900"
                                        >
                                            {{ booking.vehicle.brand?.name }}
                                            {{ booking.vehicle.type?.sub_type }}
                                        </h3>
                                        <p class="text-sm text-gray-500 mb-2">
                                            Booking #{{ booking.id }} by
                                            {{ booking.user.first_name }}
                                            {{ booking.user.last_name }}
                                        </p>

                                        <div
                                            class="grid grid-cols-2 gap-4 text-sm text-gray-600"
                                        >
                                            <div>
                                                <span class="font-medium"
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
                                            class="mt-3 flex items-center space-x-4 text-sm text-gray-600"
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
                                         class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                        </svg>
                                        ⚠️ Potential Overcharges
                                    </div>
                                    
                                    <div v-else-if="booking.status === 'confirmed'" 
                                         class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
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
                                class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg"
                            >
                                <h4
                                    class="text-sm font-medium text-yellow-800 mb-2"
                                >
                                    Payment Proof Submitted
                                </h4>
                                <div class="flex items-start space-x-3">
                                    <img
                                        :src="booking.payment.receipt_image"
                                        alt="Payment Receipt"
                                        class="w-20 h-20 object-cover rounded border cursor-pointer"
                                        @click="
                                            openReceiptModal(
                                                booking.payment.receipt_image
                                            )
                                        "
                                    />
                                    <p class="text-sm text-yellow-700">
                                        Customer uploaded payment proof. Please
                                        verify and confirm the payment.
                                    </p>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="mt-4 flex justify-end space-x-3">
                                <button
                                    @click="viewBooking(booking.id)"
                                    class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
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
                                        class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50"
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
                                        class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50"
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
                                        class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50"
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
                                    class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 disabled:opacity-50"
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
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
        >
            <div
                class="bg-white rounded-lg max-w-2xl w-full max-h-[90vh] overflow-auto"
            >
                <div class="p-4 border-b flex justify-between items-center">
                    <h3 class="text-lg font-semibold">Payment Receipt</h3>
                    <button
                        @click="closeReceiptModal"
                        class="text-gray-400 hover:text-gray-600"
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
                <div class="p-4 text-center">
                    <img
                        :src="selectedReceiptUrl"
                        alt="Payment Receipt"
                        class="max-w-full h-auto mx-auto rounded border"
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
    return classes[status] || "bg-gray-100 text-gray-800";
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
