<template>
    <AuthenticatedLayout>
        <div class="min-h-screen py-12" style="background: linear-gradient(135deg, #535862 0%, #3a3f4a 100%);">
            <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
                <div class="glass-card-dark border border-white/20 rounded-lg shadow-glow">
                    <div class="p-6 text-white">
                        <div class="mb-6">
                            <button
                                v-if="$is('renter', 'owner')"
                                @click="goBack"
                                class="inline-flex items-center text-blue-400 hover:text-blue-300 font-semibold transition-colors"
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
                                Back to My Bookings
                            </button>
                            <button
                                v-if="$is('admin')"
                                @click="goBackAdmin"
                                class="inline-flex items-center text-blue-400 hover:text-blue-300 font-semibold transition-colors"
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
                                Back to My Bookings
                            </button>
                        </div>

                        <div class="mb-6">
                            <h1 class="text-3xl font-bold text-white">
                                Booking #{{ booking.id }}
                            </h1>
                            <div class="flex items-center space-x-4 mt-2">
                                <span
                                    :class="getStatusClass(booking.status)"
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium border backdrop-blur-sm"
                                >
                                    {{ formatStatus(booking.status) }}
                                </span>
                                <span
                                    v-if="booking.payment"
                                    class="text-sm text-white/60"
                                >
                                    Created {{ formatDate(booking.created_at) }}
                                </span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                            <!-- Vehicle Information -->
                            <div class="lg:col-span-2 space-y-6">
                                <div class="glass-card-dark border border-white/20 rounded-lg p-6 backdrop-blur-sm">
                                    <h2 class="text-xl font-semibold mb-4 text-white">
                                        Vehicle Details
                                    </h2>
                                    <div class="flex space-x-4">
                                        <img
                                            :src="
                                                booking.vehicle
                                                    .main_photo_url ||
                                                '/images/placeholder-vehicle.jpg'
                                            "
                                            :alt="`${booking.vehicle.make?.name} ${booking.vehicle.model?.name || booking.vehicle.type?.name}`"
                                            class="w-32 h-32 object-cover rounded-lg border border-white/20"
                                        />
                                        <div class="flex-1">
                                            <h3 class="text-lg font-semibold text-white">
                                                {{ booking.vehicle.make?.name }} {{ booking.vehicle.model?.name }}
                                                <span v-if="booking.vehicle.year" class="text-white/60">({{ booking.vehicle.year }})</span>
                                            </h3>
                                            <p class="text-white/70 mb-3">
                                                {{
                                                    booking.vehicle.description
                                                }}
                                            </p>
                                            <div
                                                class="grid grid-cols-2 gap-4 text-sm text-white/70"
                                            >
                                                <div>
                                                    <span class="font-medium"
                                                        >Make:</span
                                                    >
                                                    {{ booking.vehicle.make?.name || 'Unknown' }}
                                                </div>
                                                <div>
                                                    <span class="font-medium"
                                                        >Model:</span
                                                    >
                                                    {{ booking.vehicle.model?.name || 'Unknown' }}
                                                </div>
                                                <div>
                                                    <span class="font-medium"
                                                        >Category:</span
                                                    >
                                                    {{ booking.vehicle.type?.category === 'car' ? '🚗 Car' : '🏍️ Motorcycle' }}
                                                </div>
                                                <div>
                                                    <span class="font-medium"
                                                        >Sub-Type:</span
                                                    >
                                                    {{ booking.vehicle.type?.sub_type || 'N/A' }}
                                                </div>
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
                                                        >Fuel Type:</span
                                                    >
                                                    {{
                                                        booking.vehicle.fuelType?.name || booking.vehicle.fuel_type?.name || 'N/A'
                                                    }}
                                                </div>
                                                <div>
                                                    <span class="font-medium"
                                                        >Transmission:</span
                                                    >
                                                    {{ booking.vehicle.transmission?.name || 'N/A' }}
                                                </div>
                                                <div>
                                                    <span class="font-medium"
                                                        >License Plate:</span
                                                    >
                                                    {{
                                                        booking.vehicle.license_plate || 'Not Set'
                                                    }}
                                                </div>
                                            </div>
                                            <div class="mt-3">
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

                                    <h2 class="text-xl font-semibold mb-4 text-white">
                                        Location Map
                                    </h2>

                                    <div
                                        class="w-5xl h-56 bg-white/10 border border-white/20 rounded-lg overflow-hidden backdrop-blur-sm"
                                    >
                                        <iframe
                                            v-if="
                                                booking.vehicle.lat &&
                                                booking.vehicle.lng
                                            "
                                            :src="`https://maps.google.com/maps?q=${booking.vehicle.lat},${booking.vehicle.lng}&z=18&t=k&output=embed`"
                                            class="w-full h-full border-0"
                                            loading="lazy"
                                            allowfullscreen
                                        >
                                        </iframe>

                                        <div
                                            v-else
                                            class="flex items-center justify-center h-full text-white/70"
                                        >
                                            <MapPin class="h-8 w-8 mr-2" />
                                            <span>Location not available</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Booking Information -->
                                <div class="glass-card-dark border border-white/20 rounded-lg p-6 backdrop-blur-sm">
                                    <h2 class="text-xl font-semibold mb-4 text-white">
                                        Booking Information
                                    </h2>
                                    <div class="grid grid-cols-2 gap-6">
                                        <div>
                                            <span class="text-sm text-white/70"
                                                >Duration</span
                                            >
                                            <div class="font-semibold text-white">
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
                                            <span class="text-sm text-white/70"
                                                >Total Amount</span
                                            >
                                            <div
                                                class="text-xl font-bold text-green-400"
                                            >
                                                ₱{{ booking.total_amount }}
                                            </div>
                                        </div>
                                        <div>
                                            <span class="text-sm text-white/70"
                                                >Pickup Date & Time</span
                                            >
                                            <div class="font-semibold text-white">
                                                {{
                                                    formatDateTime(
                                                        booking.pickup_datetime
                                                    )
                                                }}
                                            </div>
                                        </div>
                                        <div>
                                            <span class="text-sm text-white/70"
                                                >Estimated Return</span
                                            >
                                            <div class="font-semibold text-white">
                                                {{ estimatedReturn }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Owner Information -->
                                <div class="glass-card-dark border border-white/20 rounded-lg p-6 backdrop-blur-sm">
                                    <h2 class="text-xl font-semibold mb-4 text-white">
                                        Vehicle Owner
                                    </h2>
                                    <div class="flex items-center space-x-4">
                                        <div
                                            class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center text-white font-semibold border border-white/20"
                                        >
                                            {{
                                                booking.vehicle.owner.first_name.charAt(
                                                    0
                                                )
                                            }}{{
                                                booking.vehicle.owner.last_name.charAt(
                                                    0
                                                )
                                            }}
                                        </div>
                                        <div>
                                            <div class="font-semibold text-white">
                                                {{
                                                    booking.vehicle.owner
                                                        .first_name
                                                }}
                                                {{
                                                    booking.vehicle.owner
                                                        .last_name
                                                }}
                                            </div>
                                            <div class="text-sm text-white/70">
                                                {{
                                                    booking.vehicle.owner.email
                                                }}
                                            </div>
                                            <div
                                                v-if="
                                                    booking.vehicle.owner.phone
                                                "
                                                class="text-sm text-white/70"
                                            >
                                                <a
                                                    :href="`tel:${booking.vehicle.owner.phone}`"
                                                    class="text-blue-400 hover:text-blue-300"
                                                >
                                                    {{
                                                        booking.vehicle.owner
                                                            .phone
                                                    }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Payment & Actions Sidebar -->
                            <div class="lg:col-span-1">
                                <!-- Payment Information -->
                                <div
                                    class="glass-card-dark border border-white/20 rounded-lg p-6 mb-6 backdrop-blur-sm"
                                >
                                    <h2 class="text-xl font-semibold mb-4 text-white">
                                        Payment Information
                                    </h2>
                                    <div class="space-y-3">
                                        <div>
                                            <span class="text-sm text-white/70"
                                                >Payment Method</span
                                            >
                                            <div class="font-semibold text-white">
                                                {{
                                                    booking.payment
                                                        ?.payment_mode?.name ||
                                                    "N/A"
                                                }}
                                            </div>
                                        </div>
                                        <div>
                                            <span class="text-sm text-white/70"
                                                >Payment Status</span
                                            >
                                            <div>
                                                <span
                                                    :class="
                                                        getPaymentStatusClass(
                                                            booking.payment
                                                                ?.paid_at
                                                        )
                                                    "
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border backdrop-blur-sm"
                                                >
                                                    {{
                                                        booking.payment?.paid_at
                                                            ? "Payment Confirmed"
                                                            : "Payment Pending"
                                                    }}
                                                </span>
                                            </div>
                                        </div>
                                        <div
                                            v-if="
                                                booking.payment
                                                    ?.reference_number
                                            "
                                        >
                                            <span class="text-sm text-white/70"
                                                >Reference Number</span
                                            >
                                            <div class="font-mono text-sm text-white">
                                                {{
                                                    booking.payment
                                                        .reference_number
                                                }}
                                            </div>
                                        </div>
                                        <div
                                            v-if="
                                                booking.payment?.receipt_image
                                            "
                                        >
                                            <span class="text-sm text-white/70"
                                                >Payment Proof</span
                                            >
                                            <div class="mt-2">
                                                <img
                                                    :src="
                                                        booking.payment
                                                            .receipt_image
                                                    "
                                                    alt="Payment Receipt"
                                                    class="w-full rounded border border-white/20 cursor-pointer"
                                                    @click="openReceiptModal"
                                                />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Overcharge Information -->
                                <OverchargeInfo
                                    :booking="booking"
                                    :expectedReturnTime="expectedReturnTime"
                                    :potentialOvercharges="potentialOvercharges"
                                    :canExtend="canExtend"
                                />

                                <!-- Actions -->
                                <div class="space-y-3">
                                    <!-- Rate Experience Button (for completed bookings) -->
                                    <button
                                        v-if="booking.status === 'completed' && !booking.rating && canRate"
                                        @click="goToRating"
                                        class="w-full py-2 px-4 bg-yellow-500 hover:bg-yellow-600 text-white font-semibold rounded-lg transition-colors flex items-center justify-center gap-2"
                                    >
                                        <Star class="h-4 w-4" />
                                        Rate Your Experience
                                    </button>

                                    <!-- Show existing rating -->
                                    <div v-if="booking.rating" class="w-full p-3 bg-green-500/20 border border-green-400/30 rounded-lg backdrop-blur-sm">
                                        <div class="flex items-center justify-between">
                                            <span class="text-green-300 font-medium">You rated this rental</span>
                                            <div class="flex items-center space-x-1">
                                                <Star
                                                    v-for="star in 5"
                                                    :key="star"
                                                    :class="[
                                                        'h-4 w-4',
                                                        star <= booking.rating.rating
                                                            ? 'text-yellow-400 fill-yellow-400'
                                                            : 'text-white/30'
                                                    ]"
                                                />
                                            </div>
                                        </div>
                                        <p v-if="booking.rating.comment" class="text-green-200 text-sm mt-1">
                                            "{{ booking.rating.comment }}"
                                        </p>
                                    </div>

                                    <button
                                        v-if="canMakePayment"
                                        @click="goToPayment"
                                        class="w-full py-2 px-4 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-colors"
                                    >
                                        Complete Payment
                                    </button>

                                    <button
                                        v-if="booking.status === 'pending'"
                                        @click="cancelBooking"
                                        class="w-full py-2 px-4 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition-colors"
                                    >
                                        Cancel Booking
                                    </button>

                                    <button
                                        @click="goToVehicle"
                                        class="w-full py-2 px-4 border border-white/20 text-white hover:bg-white/10 font-semibold rounded-lg transition-colors backdrop-blur-sm"
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

        <!-- Receipt Modal -->
        <div
            v-if="showReceiptModal"
            @click.self="closeReceiptModal"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
        >
            <div
                class="glass-card-dark border border-white/20 rounded-lg max-w-2xl w-full max-h-[90vh] overflow-auto shadow-glow"
            >
                <div class="p-4 border-b border-white/20 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-white">Payment Receipt</h3>
                    <button
                        @click="closeReceiptModal"
                        class="text-white/60 hover:text-white transition-colors"
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
                        class="max-w-full h-auto mx-auto rounded border border-white/20"
                    />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { computed, ref } from "vue";
import { router } from "@inertiajs/vue3";
import { MapPin, Star } from "lucide-vue-next";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import OverchargeInfo from "@/Components/Booking/OverchargeInfo.vue";

const props = defineProps({
    booking: Object,
    expectedReturnTime: String,
    potentialOvercharges: Array,
});

const showReceiptModal = ref(false);

const canMakePayment = computed(() => {
    return (
        props.booking.status === "pending" &&
        props.booking.payment &&
        !props.booking.payment.paid_at &&
        props.booking.payment.type === "gcash"
    );
});

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

const canExtend = computed(() => {
    // Allow extension only for confirmed bookings that haven't been returned yet
    // and the vehicle allows extensions
    return (
        props.booking.status === "confirmed" &&
        !props.booking.return_time &&
        props.booking.vehicle?.allow_extensions !== false
    );
});

const canRate = computed(() => {
    // Allow rating only for completed bookings that haven't been rated yet
    return (
        props.booking.status === "completed" &&
        props.booking.return_time &&
        !props.booking.rating
    );
});

function getStatusClass(status) {
    const classes = {
        pending: "bg-yellow-400/20 text-yellow-400 border-yellow-400/30",
        confirmed: "bg-blue-400/20 text-blue-400 border-blue-400/30",
        completed: "bg-green-400/20 text-green-400 border-green-400/30",
        cancelled: "bg-red-400/20 text-red-400 border-red-400/30",
    };
    return classes[status] || "bg-white/10 text-white/70 border-white/20";
}

function getPaymentStatusClass(paidAt) {
    return paidAt
        ? "bg-green-400/20 text-green-400 border-green-400/30"
        : "bg-yellow-400/20 text-yellow-400 border-yellow-400/30";
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
    router.visit(route("bookings.index"));
}

function goBackAdmin() {
    router.visit(route("admin.bookings"));
}
function goToPayment() {
    router.visit(route("bookings.payment", props.booking.id));
}

function goToVehicle() {
    router.visit(`/vehicles/${props.booking.vehicle.id}`);
}

function goToRating() {
    router.visit(route('ratings.create', props.booking.id));
}

function cancelBooking() {
    if (confirm("Are you sure you want to cancel this booking?")) {
        router.post(
            route("bookings.cancel", props.booking.id),
            {},
            {
                onSuccess: () => {
                    router.visit(route("bookings.index"));
                },
            }
        );
    }
}
</script>
