<template>
    <AuthenticatedLayout>
        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h1 class="text-3xl font-bold text-gray-800 mb-8">
                            Payment
                        </h1>

                        <!-- Booking Summary -->
                        <div class="bg-gray-50 rounded-lg p-6 mb-8">
                            <h2 class="text-xl font-semibold mb-4">
                                Booking Summary
                            </h2>
                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <img
                                        :src="
                                            booking.vehicle.main_photo_url ||
                                            '/images/placeholder-vehicle.jpg'
                                        "
                                        :alt="`${booking.vehicle.brand?.name} ${booking.vehicle.type?.sub_type}`"
                                        class="w-full h-32 object-cover rounded-lg mb-3"
                                    />
                                    <h3 class="font-semibold">
                                        {{ booking.vehicle.brand?.name }}
                                        {{ booking.vehicle.type?.sub_type }}
                                    </h3>

                                    <h2
                                        class="text-2xl font-semibold text-gray-800 mb-4"
                                    >
                                        Location:
                                    </h2>

                                    <div
                                        class="w-full h-40 bg-gray-200 rounded-lg overflow-hidden"
                                    >
                                        <iframe
                                            v-if="booking.vehicle.lat && booking.vehicle.lng"
                                            :src="`https://maps.google.com/maps?q=${booking.vehicle.lat},${booking.vehicle.lng}&z=18&t=k&output=embed`"
                                            class="w-full h-full border-0"
                                            loading="lazy"
                                            allowfullscreen
                                        >
                                        </iframe>

                                        <div
                                            v-else
                                            class="flex items-center justify-center h-full text-gray-500"
                                        >
                                            <MapPin class="h-8 w-8 mr-2" />
                                            <span>Location not available</span>
                                        </div>
                                    </div>

                                    <p class="text-xs text-gray-600">
                                        Location:
                                        {{ booking.vehicle.location_name }}
                                    </p>

                                </div>
                                <div class="space-y-3">
                                    <div>
                                        <span class="text-sm text-gray-500"
                                            >Duration:</span
                                        >
                                        <span class="block font-medium"
                                            >{{
                                                booking.pricing_tier
                                                    .duration_from
                                            }}
                                            {{
                                                booking.pricing_tier
                                                    .duration_unit
                                            }}</span
                                        >
                                    </div>
                                    <div>
                                        <span class="text-sm text-gray-500"
                                            >Pickup Date & Time:</span
                                        >
                                        <span class="block font-medium">{{
                                            formatDateTime(
                                                booking.pickup_datetime
                                            )
                                        }}</span>
                                    </div>
                                    <div>
                                        <span class="text-sm text-gray-500"
                                            >Payment Method:</span
                                        >
                                        <span class="block font-medium">{{
                                            booking.payment.payment_mode.name
                                        }}</span>
                                    </div>
                                    <div class="pt-3 border-t">
                                        <span class="text-sm text-gray-500"
                                            >Total Amount:</span
                                        >
                                        <span
                                            class="block text-2xl font-bold text-green-600"
                                            >₱{{ booking.total_amount }}</span
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Instructions -->
                        <div
                            v-if="booking.payment.type === 'gcash'"
                            class="mb-8"
                        >
                            <h2 class="text-xl font-semibold mb-4">
                                GCash Payment
                            </h2>

                            <div
                                v-if="booking.vehicle.owner.gcash_qr"
                                class="grid grid-cols-1 md:grid-cols-2 gap-8"
                            >
                                <!-- QR Code -->
                                <div class="text-center">
                                    <h3 class="text-lg font-medium mb-4">
                                        Scan QR Code to Pay
                                    </h3>
                                    <div
                                        class="bg-white p-4 rounded-lg border inline-block"
                                    >
                                        <img
                                            :src="
                                                '/storage/' +
                                                booking.vehicle.owner.gcash_qr
                                            "
                                            alt="GCash QR Code"
                                            class="w-64 h-64 object-contain"
                                        />
                                    </div>
                                    <p class="text-sm text-gray-600 mt-2">
                                        Amount:
                                        <strong
                                            >₱{{ booking.total_amount }}</strong
                                        >
                                    </p>
                                </div>

                                <!-- Upload Receipt -->
                                <div>
                                    <h3 class="text-lg font-medium mb-4">
                                        Upload Payment Proof
                                    </h3>

                                    <div v-if="!booking.payment.receipt_image">
                                        <form
                                            @submit.prevent="uploadReceipt"
                                            class="space-y-4"
                                        >
                                            <div>
                                                <label
                                                    for="receipt"
                                                    class="block text-sm font-medium text-gray-700"
                                                >
                                                    Upload Receipt/Screenshot
                                                </label>
                                                <input
                                                    type="file"
                                                    id="receipt"
                                                    @change="handleFileChange"
                                                    accept="image/*"
                                                    class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                                    required
                                                />
                                                <p
                                                    class="text-xs text-gray-500 mt-1"
                                                >
                                                    Upload a clear image of your
                                                    payment confirmation
                                                </p>
                                                <div
                                                    v-if="errors.receipt_image"
                                                    class="mt-2 text-sm text-red-600"
                                                >
                                                    {{ errors.receipt_image }}
                                                </div>
                                            </div>

                                            <button
                                                type="submit"
                                                :disabled="
                                                    !selectedFile || uploading
                                                "
                                                class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50 disabled:cursor-not-allowed"
                                            >
                                                {{
                                                    uploading
                                                        ? "Uploading..."
                                                        : "Upload Receipt"
                                                }}
                                            </button>
                                        </form>
                                    </div>

                                    <div v-else class="text-center">
                                        <div
                                            class="bg-green-50 border border-green-200 rounded-lg p-4"
                                        >
                                            <h4
                                                class="text-lg font-medium text-green-800"
                                            >
                                                Receipt Uploaded!
                                            </h4>
                                            <p class="text-green-700 mt-2">
                                                Your payment proof has been
                                                submitted and is awaiting
                                                verification.
                                            </p>
                                            <img
                                                :src="
                                                    booking.payment
                                                        .receipt_image
                                                "
                                                alt="Payment Receipt"
                                                class="mt-4 mx-auto max-w-xs rounded-lg border"
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div
                                v-else
                                class="bg-red-50 border border-red-200 rounded-lg p-4"
                            >
                                <p class="text-red-800">
                                    <strong>Notice:</strong> The vehicle owner
                                    has not uploaded their GCash QR code yet.
                                    Please contact the owner directly for
                                    payment instructions.
                                </p>
                            </div>
                        </div>

                        <!-- COD Instructions -->
                        <div v-else class="mb-8">
                            <h2 class="text-xl font-semibold mb-4">
                                Cash on Delivery
                            </h2>
                            <div
                                class="bg-blue-50 border border-blue-200 rounded-lg p-6"
                            >
                                <div class="flex items-start">
                                    <div>
                                        <h3
                                            class="text-lg font-medium text-blue-800"
                                        >
                                            Payment Instructions
                                        </h3>
                                        <div class="mt-2 text-blue-700">
                                            <p class="mb-2">
                                                You have selected Cash on
                                                Delivery payment method.
                                            </p>
                                            <ul
                                                class="list-disc list-inside space-y-1"
                                            >
                                                <li>
                                                    Prepare the exact amount:
                                                    <strong
                                                        >₱{{
                                                            booking.total_amount
                                                        }}</strong
                                                    >
                                                </li>
                                                <li>
                                                    Payment will be collected
                                                    when you pick up the vehicle
                                                </li>
                                                <li>
                                                    Make sure to bring a valid
                                                    ID for verification
                                                </li>
                                                <li>
                                                    Contact the owner if you
                                                    need to change the pickup
                                                    time
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div class="bg-gray-50 rounded-lg p-6 mb-8">
                            <h3 class="text-lg font-semibold mb-3">
                                Need Help?
                            </h3>
                            <p class="text-gray-600 mb-4">
                                If you have any questions about your booking or
                                payment, please contact the vehicle owner:
                            </p>
                            <div class="flex items-center space-x-4">
                                <div>
                                    <span class="text-sm text-gray-500"
                                        >Owner:</span
                                    >
                                    <span class="block font-medium"
                                        >{{ booking.vehicle.owner.first_name }}
                                        {{
                                            booking.vehicle.owner.last_name
                                        }}</span
                                    >
                                </div>
                                <div v-if="booking.vehicle.owner.phone">
                                    <span class="text-sm text-gray-500"
                                        >Phone:</span
                                    >
                                    <a
                                        :href="`tel:${booking.vehicle.owner.phone}`"
                                        class="block font-medium text-blue-600 hover:text-blue-800"
                                    >
                                        {{ booking.vehicle.owner.phone }}
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex justify-between">
                            <button
                                @click="goToBookings"
                                class="px-6 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                            >
                                View My Bookings
                            </button>

                            <button
                                @click="goToVehicle"
                                class="px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                            >
                                Back to Vehicle
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from "vue";
import { router, useForm } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

const props = defineProps({
    booking: Object,
    errors: Object,
});

const selectedFile = ref(null);
const uploading = ref(false);

const uploadForm = useForm({
    receipt_image: null,
});

function handleFileChange(event) {
    selectedFile.value = event.target.files[0];
    uploadForm.receipt_image = event.target.files[0];
}

function uploadReceipt() {
    if (!selectedFile.value) return;

    uploading.value = true;

    uploadForm.post(route("bookings.uploadReceipt", props.booking.id), {
        onSuccess: () => {
            selectedFile.value = null;
        },
        onError: () => {
            uploading.value = false;
        },
        onFinish: () => {
            uploading.value = false;
        },
    });
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

function goToBookings() {
    router.visit(route("bookings.index"));
}

function goToVehicle() {
    router.visit(`/vehicles/${props.booking.vehicle.id}`);
}
</script>
