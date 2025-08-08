<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Booking Management
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="mb-6">
                            <button
                                @click="goBack"
                                class="inline-flex items-center text-blue-600 hover:text-blue-800 font-semibold transition-colors"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5 mr-2"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M15 19l-7-7 7-7"
                                    />
                                </svg>
                                Back to Booking Requests
                            </button>
                        </div>

                        <div class="mb-6">
                            <h1 class="text-3xl font-bold text-gray-800">
                                Booking #{{ booking.id }}
                            </h1>
                            <div class="flex items-center space-x-4 mt-2">
                                <span
                                    :class="getStatusClass(booking.status)"
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
                                >
                                    {{ formatStatus(booking.status) }}
                                </span>
                                <span class="text-sm text-gray-500">
                                    Requested
                                    {{ formatDate(booking.created_at) }}
                                </span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                            <!-- Main Content -->
                            <div class="lg:col-span-2 space-y-6">
                                <!-- Customer Information -->
                                <div class="bg-gray-50 rounded-lg p-6">
                                    <h2 class="text-xl font-semibold mb-4">
                                        Customer Information
                                    </h2>
                                    <div class="flex items-start space-x-4">
                                        <div
                                            class="w-16 h-16 bg-blue-500 rounded-full flex items-center justify-center text-white text-xl font-semibold"
                                        >
                                            {{
                                                booking.user.first_name.charAt(
                                                    0
                                                )
                                            }}{{
                                                booking.user.last_name.charAt(0)
                                            }}
                                        </div>
                                        <div class="flex-1">
                                            <h3 class="text-lg font-semibold">
                                                {{ booking.user.first_name }}
                                                {{ booking.user.last_name }}
                                            </h3>
                                            <div
                                                class="space-y-1 text-sm text-gray-600"
                                            >
                                                <div>
                                                    <span class="font-medium"
                                                        >Email:</span
                                                    >
                                                    <a
                                                        :href="`mailto:${booking.user.email}`"
                                                        class="text-blue-600 hover:text-blue-800"
                                                    >
                                                        {{ booking.user.email }}
                                                    </a>
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
                                </div>

                                <!-- Vehicle Information -->
                                <div class="bg-gray-50 rounded-lg p-6">
                                    <h2 class="text-xl font-semibold mb-4">
                                        Vehicle Information
                                    </h2>
                                    <div class="flex space-x-4">
                                        <img
                                            :src="
                                                booking.vehicle
                                                    .main_photo_url ||
                                                '/images/placeholder-vehicle.jpg'
                                            "
                                            :alt="`${booking.vehicle.brand?.name} ${booking.vehicle.type?.sub_type}`"
                                            class="w-32 h-32 object-cover rounded-lg"
                                        />
                                        <div class="flex-1">
                                            <h3 class="text-lg font-semibold">
                                                {{
                                                    booking.vehicle.brand?.name
                                                }}
                                                {{
                                                    booking.vehicle.type
                                                        ?.sub_type
                                                }}
                                            </h3>
                                            <p class="text-gray-600 mb-3">
                                                {{
                                                    booking.vehicle
                                                        .license_plate
                                                }}
                                            </p>
                                            <div
                                                class="grid grid-cols-2 gap-4 text-sm text-gray-600"
                                            >
                                                <div>
                                                    <span class="font-medium"
                                                        >Year:</span
                                                    >
                                                    {{ booking.vehicle.year }}
                                                </div>
                                                <div>
                                                    <span class="font-medium"
                                                        >Color:</span
                                                    >
                                                    {{ booking.vehicle.color }}
                                                </div>
                                                <div>
                                                    <span class="font-medium"
                                                        >Fuel:</span
                                                    >
                                                    {{
                                                        booking.vehicle
                                                            .fuel_type?.name
                                                    }}
                                                </div>
                                                <div>
                                                    <span class="font-medium"
                                                        >Location:</span
                                                    >
                                                    {{
                                                        booking.vehicle
                                                            .location_name
                                                    }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Booking Details -->
                                <div class="bg-gray-50 rounded-lg p-6">
                                    <h2 class="text-xl font-semibold mb-4">
                                        Booking Details
                                    </h2>
                                    <div class="grid grid-cols-2 gap-6">
                                        <div>
                                            <span class="text-sm text-gray-500"
                                                >Duration</span
                                            >
                                            <div class="font-semibold">
                                                {{
                                                    booking.pricing_tier
                                                        .duration_from
                                                }}
                                                {{
                                                    booking.pricing_tier
                                                        .duration_from === 1
                                                        ? booking.pricing_tier.duration_unit.slice(
                                                              0,
                                                              -1
                                                          )
                                                        : booking.pricing_tier
                                                              .duration_unit
                                                }}
                                            </div>
                                        </div>
                                        <div>
                                            <span class="text-sm text-gray-500"
                                                >Total Amount</span
                                            >
                                            <div
                                                class="text-xl font-bold text-green-600"
                                            >
                                                ₱{{ booking.total_amount }}
                                            </div>
                                        </div>
                                        <div>
                                            <span class="text-sm text-gray-500"
                                                >Pickup Date & Time</span
                                            >
                                            <div class="font-semibold">
                                                {{
                                                    formatDateTime(
                                                        booking.pickup_datetime
                                                    )
                                                }}
                                            </div>
                                        </div>
                                        <div>
                                            <span class="text-sm text-gray-500"
                                                >Estimated Return</span
                                            >
                                            <div class="font-semibold">
                                                {{ estimatedReturn }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Payment Proof (if GCash) -->
                                <div
                                    v-if="booking.payment?.receipt_image"
                                    class="bg-yellow-50 border border-yellow-200 rounded-lg p-6"
                                >
                                    <h2
                                        class="text-xl font-semibold mb-4 text-yellow-800"
                                    >
                                        Payment Proof Submitted
                                    </h2>
                                    <div class="flex items-start space-x-4">
                                        <img
                                            :src="booking.payment.receipt_image"
                                            alt="Payment Receipt"
                                            class="w-48 h-48 object-cover rounded border cursor-pointer"
                                            @click="openReceiptModal"
                                        />
                                        <div class="flex-1">
                                            <p class="text-yellow-800 mb-3">
                                                Customer has uploaded payment
                                                proof. Please verify the payment
                                                before confirming the booking.
                                            </p>
                                            <div
                                                v-if="
                                                    booking.payment
                                                        .reference_number
                                                "
                                                class="text-sm text-yellow-700"
                                            >
                                                <span class="font-medium"
                                                    >Reference Number:</span
                                                >
                                                <span class="font-mono">{{
                                                    booking.payment
                                                        .reference_number
                                                }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Actions Sidebar -->
                            <div class="lg:col-span-1">
                                <div
                                    class="bg-white border rounded-lg p-6 sticky top-6"
                                >
                                    <h2 class="text-xl font-semibold mb-4">
                                        Actions
                                    </h2>

                                    <!-- Payment Status -->
                                    <div class="mb-6">
                                        <span class="text-sm text-gray-500"
                                            >Payment Status</span
                                        >
                                        <div class="mt-1">
                                            <span
                                                :class="
                                                    getPaymentStatusClass(
                                                        booking.payment?.paid_at
                                                    )
                                                "
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                            >
                                                {{
                                                    booking.payment?.paid_at
                                                        ? "Payment Confirmed"
                                                        : "Payment Pending"
                                                }}
                                            </span>
                                        </div>
                                        <div class="mt-2 text-sm text-gray-600">
                                            <span class="font-medium"
                                                >Method:</span
                                            >
                                            {{
                                                booking.payment?.payment_mode
                                                    ?.name || "N/A"
                                            }}
                                        </div>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="space-y-3">
                                        <!-- Actions for pending bookings -->
                                        <div
                                            v-if="booking.status === 'pending'"
                                            class="space-y-3"
                                        >
                                            <button
                                                v-if="
                                                    booking.payment
                                                        ?.receipt_image &&
                                                    !booking.payment.paid_at
                                                "
                                                @click="confirmPayment"
                                                :disabled="processing"
                                                class="w-full py-2 px-4 bg-green-600 hover:bg-green-700 disabled:bg-gray-400 text-white font-semibold rounded-lg transition-colors disabled:cursor-not-allowed"
                                            >
                                                {{
                                                    processing
                                                        ? "Processing..."
                                                        : "Confirm Payment & Booking"
                                                }}
                                            </button>

                                            <button
                                                v-if="
                                                    booking.payment?.type ===
                                                        'cod' ||
                                                    booking.payment?.paid_at
                                                "
                                                @click="confirmBooking"
                                                :disabled="processing"
                                                class="w-full py-2 px-4 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white font-semibold rounded-lg transition-colors disabled:cursor-not-allowed"
                                            >
                                                {{
                                                    processing
                                                        ? "Processing..."
                                                        : "Confirm Booking"
                                                }}
                                            </button>

                                            <button
                                                @click="rejectBooking"
                                                :disabled="processing"
                                                class="w-full py-2 px-4 bg-red-600 hover:bg-red-700 disabled:bg-gray-400 text-white font-semibold rounded-lg transition-colors disabled:cursor-not-allowed"
                                            >
                                                {{
                                                    processing
                                                        ? "Processing..."
                                                        : "Reject Booking"
                                                }}
                                            </button>
                                        </div>

                                        <!-- Complete booking action -->
                                        <button
                                            v-if="
                                                booking.status === 'confirmed'
                                            "
                                            @click="completeBooking"
                                            :disabled="processing"
                                            class="w-full py-2 px-4 bg-purple-600 hover:bg-purple-700 disabled:bg-gray-400 text-white font-semibold rounded-lg transition-colors disabled:cursor-not-allowed"
                                        >
                                            {{
                                                processing
                                                    ? "Processing..."
                                                    : "Mark as Completed"
                                            }}
                                        </button>

                                        <!-- Status info for completed/cancelled -->
                                        <div
                                            v-if="
                                                booking.status === 'completed'
                                            "
                                            class="p-3 bg-green-50 border border-green-200 rounded"
                                        >
                                            <div
                                                class="text-green-800 font-medium text-sm"
                                            >
                                                Booking Completed
                                            </div>
                                            <div
                                                class="text-green-600 text-xs mt-1"
                                            >
                                                This booking has been marked as
                                                completed.
                                            </div>
                                        </div>

                                        <div
                                            v-if="
                                                booking.status === 'cancelled'
                                            "
                                            class="p-3 bg-red-50 border border-red-200 rounded"
                                        >
                                            <div
                                                class="text-red-800 font-medium text-sm"
                                            >
                                                Booking Cancelled
                                            </div>
                                            <div
                                                class="text-red-600 text-xs mt-1"
                                            >
                                                This booking has been cancelled.
                                            </div>
                                        </div>
                                    </div>

                                    <div class="border-t pt-4 mt-6">
                                        <button
                                            @click="goToVehicle"
                                            class="w-full py-2 px-4 border border-gray-300 text-gray-700 hover:bg-gray-50 font-semibold rounded-lg transition-colors"
                                        >
                                            View Vehicle
                                        </button>
                                    </div>
                                </div>
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
                class="bg-white rounded-lg max-w-3xl w-full max-h-[90vh] overflow-auto"
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
                        :src="booking.payment?.receipt_image"
                        alt="Payment Receipt"
                        class="max-w-full h-auto mx-auto rounded border"
                    />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { computed, ref } from "vue";
import { router } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

const props = defineProps({
    booking: Object,
});

const processing = ref(false);
const showReceiptModal = ref(false);

const estimatedReturn = computed(() => {
    const pickup = new Date(props.booking.pickup_datetime);
    const tier = props.booking.pricing_tier;

    let returnDate = new Date(pickup);

    switch (tier.duration_unit) {
        case "minutes":
            returnDate.setMinutes(returnDate.getMinutes() + tier.duration_from);
            break;
        case "hours":
            returnDate.setHours(returnDate.getHours() + tier.duration_from);
            break;
        case "days":
            returnDate.setDate(returnDate.getDate() + tier.duration_from);
            break;
    }

    return returnDate.toLocaleString("en-US", {
        weekday: "short",
        month: "short",
        day: "numeric",
        year: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
});

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
        weekday: "long",
        year: "numeric",
        month: "long",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
}

function formatDate(date) {
    return new Date(date).toLocaleDateString("en-US", {
        year: "numeric",
        month: "long",
        day: "numeric",
    });
}

function openReceiptModal() {
    showReceiptModal.value = true;
}

function closeReceiptModal() {
    showReceiptModal.value = false;
}

function goBack() {
    router.visit(route("owner.bookings.index"));
}

function goToVehicle() {
    router.visit(route("vehicles.show", props.booking.vehicle.id));
}

function confirmPayment() {
    processing.value = true;
    router.post(
        route("owner.bookings.confirmPayment", props.booking.id),
        {},
        {
            onFinish: () => {
                processing.value = false;
            },
        }
    );
}

function confirmBooking() {
    processing.value = true;
    router.post(
        route("owner.bookings.confirm", props.booking.id),
        {},
        {
            onFinish: () => {
                processing.value = false;
            },
        }
    );
}

function rejectBooking() {
    if (
        confirm(
            "Are you sure you want to reject this booking? This action cannot be undone."
        )
    ) {
        processing.value = true;
        router.post(
            route("owner.bookings.reject", props.booking.id),
            {},
            {
                onFinish: () => {
                    processing.value = false;
                },
            }
        );
    }
}

function completeBooking() {
    processing.value = true;
    router.post(
        route("owner.bookings.complete", props.booking.id),
        {},
        {
            onFinish: () => {
                processing.value = false;
            },
        }
    );
}
</script>
