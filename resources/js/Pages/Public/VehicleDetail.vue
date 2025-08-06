<template>
    <AuthenticatedLayout>
              <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <button
            @click="goBack"
            class="mb-6 inline-flex items-center text-primary-600 hover:text-primary-800 font-semibold transition-colors"
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
            Back
        </button>
        <div v-if="vehicle" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 bg-white p-6 rounded-lg shadow-md">
                <!-- Main photo -->
                <!-- Main photo -->
                <img
                    v-if="vehicle.main_photo_url"
                    :src="vehicle.main_photo_url"
                    :alt="vehicle.license_plate"
                    class="w-full h-96 object-cover rounded-lg mb-6 cursor-pointer"
                    @click="openModal(0)"
                />

                <!-- Additional photos -->
                <div v-if="vehicle.photos?.length" class="flex gap-2 mb-4">
                    <img
                        v-for="(photo, index) in vehicle.photos"
                        :key="photo.id"
                        :src="photo.url"
                        class="w-20 h-20 object-cover rounded cursor-pointer"
                        @click="openModal(index + 1)"
                    />
                </div>

                <!-- Modal Viewer -->
                <!-- Modal Viewer -->
                <div
                    v-if="isModalOpen"
                    class="fixed inset-0 z-50 bg-black bg-opacity-80 flex items-center justify-center"
                    @click.self="closeModal"
                >
                    <div class="relative w-full max-w-3xl mx-auto px-4">
                        <!-- No X button here -->

                        <img
                            :src="allPhotos[currentIndex]"
                            class="max-h-[90vh] mx-auto rounded shadow-lg"
                        />

                        <!-- Navigation buttons -->
                        <button
                            v-if="currentIndex > 0"
                            @click.stop="prevImage"
                            class="absolute left-4 top-1/2 transform -translate-y-1/2 text-white text-3xl font-bold"
                        >
                            ‹
                        </button>
                        <button
                            v-if="currentIndex < allPhotos.length - 1"
                            @click.stop="nextImage"
                            class="absolute right-4 top-1/2 transform -translate-y-1/2 text-white text-3xl font-bold"
                        >
                            ›
                        </button>
                    </div>
                </div>

                <h1 class="text-3xl font-bold text-gray-800 mb-2">
                    {{ vehicle.license_plate }}
                </h1>
                <p class="text-gray-600 text-lg mb-4">
                    {{ vehicle.location_name }}
                </p>

                <div class="grid grid-cols-2 gap-4 mb-6 text-gray-700">
                    <div class="flex items-center gap-2">
                        <Car class="h-5 w-5 text-primary-600" />
                        <span>{{ vehicle.type?.category }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <DollarSign class="h-5 w-5 text-primary-600" />
                        <span>
                            <span v-if="vehicle.pricing_tiers?.length">
                                ₱{{
                                    Math.min(
                                        ...vehicle.pricing_tiers.map(
                                            (t) => t.price
                                        )
                                    )
                                }}/{{
                                    vehicle.pricing_tiers[0].duration_unit.replace(
                                        /s$/,
                                        ""
                                    )
                                }}
                            </span>
                            <span v-else>N/A</span>
                        </span>
                    </div>
                    <div class="flex items-center gap-2">
                        <Users class="h-5 w-5 text-primary-600" />
                        <span>Year: {{ vehicle.year }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <Fuel class="h-5 w-5 text-primary-600" />
                        <span>{{ vehicle.fuel_type?.name }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="font-semibold">Color:</span>
                        <span>{{ vehicle.color }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="font-semibold">Brand:</span>
                        <span>{{ vehicle.brand?.name }}</span>
                    </div>
                </div>

                <h2 class="text-2xl font-semibold text-gray-800 mb-4">
                    Description
                </h2>
                <p class="text-gray-700 leading-relaxed mb-6">
                    {{ vehicle.description }}
                </p>

                <h2 class="text-2xl font-semibold text-gray-800 mb-4">
                    Pricing Tiers
                </h2>
                <ul
                    v-if="vehicle.pricing_tiers?.length"
                    class="list-disc ml-5 text-gray-700 mb-6"
                >
                    <li v-for="tier in vehicle.pricing_tiers" :key="tier.id">
                        {{ tier.duration_from }}
                        {{ tier.duration_unit.replace(/s$/, "")
                        }}<span v-if="tier.duration_from > 1">s</span> : ₱{{
                            tier.price
                        }}
                    </li>
                </ul>
                <div v-else class="mb-6 text-gray-500">
                    No pricing tiers available.
                </div>

                <h2 class="text-2xl font-semibold text-gray-800 mb-4">
                    Owner Information
                </h2>
                <div class="flex items-center gap-4 mb-6">
                    <img
                        v-if="vehicle.owner?.profile_photo_url"
                        :src="vehicle.owner.profile_photo_url"
                        alt="Owner Avatar"
                        class="w-16 h-16 rounded-full object-cover"
                    />
                    <div>
                        <p class="font-semibold text-gray-800">
                            {{ vehicle.owner?.name }}
                        </p>
                        <!-- You can add more owner info here if available -->
                    </div>
                </div>

                <h2 class="text-2xl font-semibold text-gray-800 mb-4">
                    Location Map
                </h2>

                <div class="w-full h-64 bg-gray-200 rounded-lg overflow-hidden">
                    <iframe
                        v-if="vehicle.lat && vehicle.lng"
                        :src="`https://maps.google.com/maps?q=${vehicle.lat},${vehicle.lng}&z=18&t=k&output=embed`"
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
            </div>

            <div
                class="lg:col-span-1 bg-white p-6 rounded-lg shadow-md h-fit sticky top-24"
            >
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">
                    Book This Vehicle
                </h2>
                <form @submit.prevent="submitBooking">
                    <div class="mb-4">
                        <label
                            for="pickupDate"
                            class="block text-sm font-medium text-gray-700 mb-2"
                            >Pickup Date</label
                        >
                        <input
                            type="date"
                            id="pickupDate"
                            v-model="bookingForm.start_datetime"
                            required
                            class="w-full p-2 border border-gray-300 rounded-md focus:ring-primary-400 focus:border-primary-400"
                        />
                    </div>
                    <div class="mb-4">
                        <label
                            for="returnDate"
                            class="block text-sm font-medium text-gray-700 mb-2"
                            >Return Date</label
                        >
                        <input
                            type="date"
                            id="returnDate"
                            v-model="bookingForm.end_datetime"
                            required
                            class="w-full p-2 border border-gray-300 rounded-md focus:ring-primary-400 focus:border-primary-400"
                        />
                    </div>
                    <button
                        type="submit"
                        class="w-full bg-primary-600 text-white px-6 py-3 rounded-md font-semibold hover:bg-primary-700 transition-colors flex items-center justify-center gap-2"
                    >
                        <CalendarCheck class="h-5 w-5" />
                        Request Booking
                    </button>
                </form>
            </div>
        </div>
        <div v-else class="text-center py-12 text-gray-600">
            <p>Loading vehicle details or vehicle not found...</p>
        </div>

        </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from "vue";
import { usePage, router } from "@inertiajs/vue3";
import {
    Car,
    DollarSign,
    Users,
    Fuel,
    MapPin,
    CalendarCheck,
} from "lucide-vue-next";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

const page = usePage();
const vehicle = page.props.vehicle;

const bookingForm = ref({
    start_datetime: "",
    end_datetime: "",
});

function submitBooking() {
    if (!bookingForm.value.start_datetime || !bookingForm.value.end_datetime) {
        alert("Please select both pickup and return dates.");
        return;
    }
    if (
        new Date(bookingForm.value.start_datetime) >=
        new Date(bookingForm.value.end_datetime)
    ) {
        alert("Return date must be after pickup date.");
        return;
    }
    router.post(
        `/vehicles/${vehicle.id}/book`,
        {
            start_datetime: bookingForm.value.start_datetime,
            end_datetime: bookingForm.value.end_datetime,
        },
        {
            onSuccess: () => {
                bookingForm.value.start_datetime = "";
                bookingForm.value.end_datetime = "";
                alert("Booking request sent!");
            },
        }
    );
}

function goBack() {
    window.history.length > 1
        ? window.history.back()
        : router.visit("/vehicles"); // Fallback to /vehicles if no history
}

const isModalOpen = ref(false);
const currentIndex = ref(0);

const allPhotos = [
    vehicle.main_photo_url,
    ...(vehicle.photos?.map((p) => p.url) ?? []),
];

function openModal(index) {
    currentIndex.value = index;
    isModalOpen.value = true;
}

function closeModal() {
    isModalOpen.value = false;
}

function nextImage() {
    if (currentIndex.value < allPhotos.length - 1) {
        currentIndex.value++;
    }
}

function prevImage() {
    if (currentIndex.value > 0) {
        currentIndex.value--;
    }
}
</script>

<style scoped>
/* Tailwind CSS is used for styling */
</style>
