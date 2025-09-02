<template>
    <AuthenticatedLayout>
        <div class="min-h-screen py-8">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="glass-card border border-white/20 rounded-lg shadow-glow backdrop-blur-sm">
                    <div class="p-6">
                        <div class="mb-6">
                            <button
                                @click="goBack"
                                class="inline-flex items-center text-white hover:text-white/80 font-semibold transition-colors bg-[#00000040] px-4 py-2 rounded-lg backdrop-blur-sm border border-white/20"
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
                                Back to Vehicle
                            </button>
                        </div>

                        <h1 class="text-3xl font-bold text-white mb-8">
                            Book Vehicle
                        </h1>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            <!-- Vehicle Info -->
                            <div>
                                <div class="glass-card p-6 rounded-lg mb-6 border border-white/20 backdrop-blur-sm">
                                    <h2 class="text-xl font-semibold mb-4 text-white">
                                        Vehicle Details
                                    </h2>
                                    <img
                                        :src="
                                            vehicle.main_photo_url ||
                                            '/images/placeholder-vehicle.jpg'
                                        "
                                        :alt="`${vehicle.make?.name} ${vehicle.model?.name || vehicle.type?.sub_type}`"
                                        class="w-full h-48 object-cover rounded-lg mb-4"
                                    />
                                    <h3 class="text-lg font-semibold text-white">
                                        {{ vehicle.make?.name }}
                                        {{ vehicle.model?.name || vehicle.type?.sub_type }}
                                    </h3>
                                    <p class="text-white/80">
                                        {{ vehicle.description }}
                                    </p>
                                    <div
                                        class="mt-4 grid grid-cols-2 gap-4 text-sm text-white/90"
                                    >
                                        <div>
                                            <strong>Year:</strong>
                                            {{ vehicle.year }}
                                        </div>
                                        <div>
                                            <strong>Color:</strong>
                                            {{ vehicle.color }}
                                        </div>
                                        <div>
                                            <strong>Fuel:</strong>
                                            {{ vehicle.fuelTypes?.name || vehicle.fuel_type?.name }}
                                        </div>
                                        <div>
                                            <strong>Transmission:</strong>
                                            {{ vehicle.transmission?.name || 'Manual' }}
                                        </div>
                                        <div>
                                            <strong>Location:</strong>
                                            {{ vehicle.location_name }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Booking Form -->
                            <div>
                                <!-- Error Messages -->
                                <div
                                    v-if="hasBookingErrors"
                                    class="mb-6 p-4 bg-red-500/20 border border-red-400/30 rounded-lg text-red-300 backdrop-blur-sm"
                                >
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg
                                                class="h-5 w-5 text-red-400"
                                                viewBox="0 0 20 20"
                                                fill="currentColor"
                                            >
                                                <path
                                                    fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                    clip-rule="evenodd"
                                                />
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <h3 class="text-sm font-medium text-red-300">
                                                Booking Error
                                            </h3>
                                            <div class="mt-2 text-sm">
                                                <ul class="list-disc list-inside space-y-1">
                                                    <li
                                                        v-if="errors.booking_conflict"
                                                    >
                                                        {{ errors.booking_conflict }}
                                                    </li>
                                                    <li
                                                        v-if="errors.vehicle_unavailable"
                                                    >
                                                        {{ errors.vehicle_unavailable }}
                                                    </li>
                                                    <li v-if="errors.rate_limit">
                                                        {{ errors.rate_limit }}
                                                    </li>
                                                    <li v-if="errors.user_booking_conflict">
                                                        {{ errors.user_booking_conflict }}
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Active Booking Warning -->
                                <div
                                    v-if="hasActiveBookings"
                                    class="mb-6 p-4 bg-yellow-500/20 border border-yellow-400/30 rounded-lg text-yellow-300 backdrop-blur-sm"
                                >
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg
                                                class="h-5 w-5 text-yellow-400"
                                                viewBox="0 0 20 20"
                                                fill="currentColor"
                                            >
                                                <path
                                                    fill-rule="evenodd"
                                                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                                    clip-rule="evenodd"
                                                />
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <h3 class="text-sm font-medium text-yellow-300">
                                                Active Booking Found
                                            </h3>
                                            <div class="mt-2 text-sm">
                                                <p class="mb-2">You already have active bookings for this vehicle:</p>
                                                <ul class="list-disc list-inside space-y-1">
                                                    <li
                                                        v-for="booking in userActiveBookings"
                                                        :key="booking.id"
                                                    >
                                                        {{ new Date(booking.pickup_datetime).toLocaleString() }} 
                                                        ({{ booking.pricing_tier?.duration_from }} {{ booking.pricing_tier?.duration_unit }}) 
                                                        - Status: {{ booking.status }}
                                                    </li>
                                                </ul>
                                                <p class="mt-2">
                                                    Please <Link :href="route('bookings.index')" class="text-blue-400 hover:text-blue-300 underline">manage your existing bookings</Link> 
                                                    or wait until they are completed before booking again.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <form
                                    @submit.prevent="submitBooking"
                                    class="space-y-6"
                                >
                                    <fieldset :disabled="hasActiveBookings" class="space-y-6"
                                              :class="{ 'opacity-60': hasActiveBookings }"
                                    >
                                    
                                    <!-- Pricing Tiers -->
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-white mb-3"
                                            >Select Rental Duration</label
                                        >
                                        <div class="space-y-3">
                                            <div
                                                v-for="tier in vehicle.pricing_tiers"
                                                :key="tier.id"
                                                class="border rounded-lg p-4 cursor-pointer transition-all backdrop-blur-sm"
                                                :class="
                                                    form.pricing_tier_id ==
                                                    tier.id
                                                        ? 'border-blue-400 bg-blue-400/20'
                                                        : 'border-white/20 hover:border-white/30 hover:bg-white/5'
                                                "
                                                @click="
                                                    form.pricing_tier_id =
                                                        tier.id
                                                "
                                            >
                                                <div
                                                    class="flex items-center justify-between"
                                                >
                                                    <div
                                                        class="flex items-center"
                                                    >
                                                        <input
                                                            type="radio"
                                                            :id="`tier-${tier.id}`"
                                                            :value="tier.id"
                                                            v-model="
                                                                form.pricing_tier_id
                                                            "
                                                            class="h-4 w-4 text-blue-400 focus:ring-blue-400/50 border-white/30 bg-white/10"
                                                        />
                                                        <div class="ml-3">
                                                            <label
                                                                :for="`tier-${tier.id}`"
                                                                class="text-sm font-medium text-white cursor-pointer"
                                                            >
                                                                {{
                                                                    tier.duration_from
                                                                }}
                                                                {{
                                                                    tier.duration_unit
                                                                }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div
                                                        class="text-lg font-bold text-green-400"
                                                    >
                                                        ₱{{ tier.price }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div
                                            v-if="errors.pricing_tier_id"
                                            class="mt-2 text-sm text-red-400"
                                        >
                                            {{ errors.pricing_tier_id }}
                                        </div>
                                    </div>

<!-- Pickup Date & Time -->
<div class="grid grid-cols-2 gap-4">
    <div>
        <label for="pickup_date" class="block text-sm font-medium text-white">Pickup Date</label>
        <input
            type="date"
            id="pickup_date"
            v-model="form.pickup_date"
            :min="today"
            @change="updateMinTime"
            class="mt-1 block w-full bg-white/10 border-white/20 text-white placeholder-white/50 rounded-md shadow-sm focus:ring-blue-400 focus:border-blue-400 sm:text-sm backdrop-blur-sm"
            required
        />
        <div v-if="errors.pickup_date" class="mt-2 text-sm text-red-400">
            {{ errors.pickup_date }}
        </div>
    </div>

    <div>
        <label for="pickup_time" class="block text-sm font-medium text-white">Pickup Time</label>
        <input
            type="time"
            id="pickup_time"
            v-model="form.pickup_time"
            :min="minPickupTime"
            class="mt-1 block w-full bg-white/10 border-white/20 text-white placeholder-white/50 rounded-md shadow-sm focus:ring-blue-400 focus:border-blue-400 sm:text-sm backdrop-blur-sm"
            required
        />
        <div v-if="errors.pickup_time" class="mt-2 text-sm text-red-400">
            {{ errors.pickup_time }}
        </div>
    </div>
</div>


                                    <!-- Payment Method -->
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-white mb-3"
                                            >Payment Method</label
                                        >
                                        <div class="space-y-3">
                                            <div
                                                v-for="method in availablePaymentMethods"
                                                :key="method.id"
                                                class="border rounded-lg p-4 cursor-pointer transition-all backdrop-blur-sm"
                                                :class="
                                                    form.payment_mode_id ==
                                                    method.id
                                                        ? 'border-blue-400 bg-blue-400/20'
                                                        : 'border-white/20 hover:border-white/30 hover:bg-white/5'
                                                "
                                                @click="
                                                    form.payment_mode_id =
                                                        method.id
                                                "
                                            >
                                                <div class="flex items-center">
                                                    <input
                                                        type="radio"
                                                        :id="`mode-${method.id}`"
                                                        :value="method.id"
                                                        v-model="
                                                            form.payment_mode_id
                                                        "
                                                        class="h-4 w-4 text-blue-400 focus:ring-blue-400/50 border-white/30 bg-white/10"
                                                    />
                                                    <div class="ml-3">
                                                        <label
                                                            :for="`mode-${method.id}`"
                                                            class="text-sm font-medium text-white cursor-pointer"
                                                        >
                                                            {{ method.name }}
                                                        </label>
                                                        <p
                                                            v-if="
                                                                method.type ===
                                                                'cod'
                                                            "
                                                            class="text-xs text-white/60"
                                                        >
                                                            Pay when you pick up
                                                            the vehicle
                                                        </p>
                                                        <p
                                                            v-else-if="
                                                                method.type ===
                                                                'gcash'
                                                            "
                                                            class="text-xs text-white/60"
                                                        >
                                                            Pay via GCash using
                                                            owner's QR code
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Show message if no payment methods available -->
                                            <div
                                                v-if="
                                                    !availablePaymentMethods ||
                                                    availablePaymentMethods.length ===
                                                        0
                                                "
                                                class="text-center py-8 text-white/60 border border-white/20 rounded-lg backdrop-blur-sm"
                                            >
                                                <p class="font-medium text-white">
                                                    No payment methods available
                                                </p>
                                                <p class="text-sm">
                                                    The owner needs to configure
                                                    their payment settings.
                                                </p>
                                            </div>
                                        </div>
                                        <div
                                            v-if="errors.payment_mode_id"
                                            class="mt-2 text-sm text-red-400"
                                        >
                                            {{ errors.payment_mode_id }}
                                        </div>
                                    </div>

                                    <!-- GCash Reference (optional) -->
                                    <div v-if="isGCashSelected">
                                        <label
                                            for="reference_number"
                                            class="block text-sm font-medium text-white"
                                            >GCash Reference Number
                                            (Optional)</label
                                        >
                                        <input
                                            type="text"
                                            id="reference_number"
                                            v-model="form.reference_number"
                                            placeholder="Enter reference number if available"
                                            class="mt-1 block w-full bg-white/10 border-white/20 text-white placeholder-white/50 rounded-md shadow-sm focus:ring-blue-400 focus:border-blue-400 sm:text-sm backdrop-blur-sm"
                                        />
                                        <div
                                            v-if="errors.reference_number"
                                            class="mt-2 text-sm text-red-400"
                                        >
                                            {{ errors.reference_number }}
                                        </div>
                                    </div>

                                    <!-- Total Amount -->
                                    <div
                                        v-if="selectedTier"
                                        class="bg-green-400/20 border border-green-400/30 rounded-lg p-4 backdrop-blur-sm"
                                    >
                                        <div
                                            class="flex justify-between items-center"
                                        >
                                            <span
                                                class="text-lg font-semibold text-white"
                                                >Total Amount:</span
                                            >
                                            <span
                                                class="text-2xl font-bold text-green-400"
                                                >₱{{ selectedTier.price }}</span
                                            >
                                        </div>
                                    </div>

                                    <!-- Submit Button -->
                                    <button
                                        type="submit"
                                        :disabled="
                                            !canBook ||
                                            !form.pricing_tier_id ||
                                            !form.payment_mode_id
                                        "
                                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white disabled:opacity-50 disabled:cursor-not-allowed backdrop-blur-sm transition-colors"
                                        :class="canBook ? 'bg-blue-400 hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-400/50' : 'bg-white/20'"
                                    >
                                        <svg
                                            v-if="processing"
                                            class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                                            xmlns="http://www.w3.org/2000/svg"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                        >
                                            <circle
                                                class="opacity-25"
                                                cx="12"
                                                cy="12"
                                                r="10"
                                                stroke="currentColor"
                                                stroke-width="4"
                                            ></circle>
                                            <path
                                                class="opacity-75"
                                                fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                            ></path>
                                        </svg>
                                        {{
                                            hasActiveBookings
                                                ? "Booking Disabled - Active Booking Exists"
                                                : processing
                                                ? "Creating Booking..."
                                                : "Create Booking"
                                        }}
                                    </button>
                                    </fieldset>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { computed, ref } from "vue";
import { router, useForm, Link } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

const props = defineProps({
    vehicle: Object,
    availablePaymentMethods: Array,
    errors: Object,
    userActiveBookings: Array,
});

const processing = ref(false);

const today = new Date().toISOString().split("T")[0];
const now = new Date();
const pickup_time = now.toTimeString().slice(0, 5); // "HH:MM" format
const form = useForm({
    pricing_tier_id: null,
    pickup_date: today,
    pickup_time: pickup_time,
    payment_mode_id: null,
    reference_number: "",
});

const selectedTier = computed(() => {
    if (!form.pricing_tier_id) return null;
    return props.vehicle.pricing_tiers.find(
        (tier) => tier.id == form.pricing_tier_id
    );
});

const isGCashSelected = computed(() => {
    if (!form.payment_mode_id) return false;
    const selectedMethod = props.availablePaymentMethods.find(
        (method) => method.id == form.payment_mode_id
    );
    return selectedMethod?.type === "gcash";
});

const hasBookingErrors = computed(() => {
    return props.errors && (
        props.errors.booking_conflict ||
        props.errors.vehicle_unavailable ||
        props.errors.rate_limit ||
        props.errors.user_booking_conflict
    );
});


const minPickupTime = ref(pickup_time); // default: now

function updateMinTime() {
    const todayStr = today; // "YYYY-MM-DD"
    if (form.pickup_date === todayStr) {
        // If booking is today, min time is now
        const nowDate = new Date();
        minPickupTime.value = nowDate.toTimeString().slice(0, 5); // HH:MM
        // If user selected an earlier time, reset it to minPickupTime
        if (form.pickup_time < minPickupTime.value) {
            form.pickup_time = minPickupTime.value;
        }
    } else {
        // If booking is future date, allow any time
        minPickupTime.value = "00:00";
    }
}
const hasActiveBookings = computed(() => {
    return props.userActiveBookings && props.userActiveBookings.length > 0;
});

const canBook = computed(() => {
    return !hasActiveBookings.value && !processing.value;
});

function submitBooking() {
    // Prevent double submission
    if (processing.value) {
        return;
    }
    
    processing.value = true;

    form.post(route("bookings.store", props.vehicle.id), {
        onSuccess: () => {
            // Redirect will be handled by the controller
        },
        onError: (errors) => {
            processing.value = false;
            console.log('Booking errors:', errors);
        },
        onFinish: () => {
            processing.value = false;
        },
    });
}

function goBack() {
    window.history.length > 1
        ? window.history.back()
        : router.visit(`/vehicles/${props.vehicle.id}`);
}
</script>
