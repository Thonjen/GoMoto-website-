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
                <img
                    v-if="vehicle.main_photo_url"
                    :src="vehicle.main_photo_url"
                    :alt="`${vehicle.make?.name || 'Vehicle'} ${vehicle.model?.name || ''} ${vehicle.year || ''}`"
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

                <div class="mb-6">
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">
                        {{ vehicle.make?.name }} {{ vehicle.model?.name }}
                        <span v-if="vehicle.year" class="text-2xl text-gray-600">({{ vehicle.year }})</span>
                    </h1>
                    <div class="flex items-center gap-4 text-gray-600">
                        <span v-if="vehicle.license_plate" class="bg-gray-100 px-3 py-1 rounded-lg font-mono text-sm">
                            {{ vehicle.license_plate }}
                        </span>
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            {{ vehicle.location_name || 'Surigao del Norte' }}
                        </span>
                    </div>
                </div>

                <!-- Vehicle Details Grid -->
                <div class="bg-gray-50 rounded-lg p-6 mb-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Vehicle Details</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 text-gray-700">
                        <!-- Vehicle Category -->
                        <div class="flex items-center gap-2">
                            <Car class="h-5 w-5 text-primary-600" />
                            <div>
                                <span class="text-sm text-gray-500">Category:</span>
                                <p class="font-medium">{{ vehicle.type?.category === 'car' ? '🚗 Car' : '🏍️ Motorcycle' }}</p>
                            </div>
                        </div>

                        <!-- Make -->
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-gray-500">Make:</span>
                            <span class="font-medium">{{ vehicle.make?.name || 'Unknown' }}</span>
                        </div>

                        <!-- Model -->
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-gray-500">Model:</span>
                            <span class="font-medium">{{ vehicle.model?.name || 'Unknown' }}</span>
                        </div>

                        <!-- Sub-Type -->
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-gray-500">Sub-Type:</span>
                            <span class="font-medium">{{ vehicle.type?.sub_type || 'N/A' }}</span>
                        </div>

                        <!-- Year -->
                        <div class="flex items-center gap-2">
                            <Users class="h-5 w-5 text-primary-600" />
                            <div>
                                <span class="text-sm text-gray-500">Year:</span>
                                <p class="font-medium">{{ vehicle.year }}</p>
                            </div>
                        </div>

                        <!-- Fuel Type -->
                        <div class="flex items-center gap-2">
                            <Fuel class="h-5 w-5 text-primary-600" />
                            <div>
                                <span class="text-sm text-gray-500">Fuel Type:</span>
                                <p class="font-medium">{{ vehicle.fuelType?.name || vehicle.fuel_type?.name || 'Unknown' }}</p>
                            </div>
                        </div>

                        <!-- Transmission -->
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-gray-500">Transmission:</span>
                            <span class="font-medium">{{ vehicle.transmission?.name || 'Unknown' }}</span>
                        </div>

                        <!-- Color -->
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-gray-500">Color:</span>
                            <span class="font-medium">{{ vehicle.color }}</span>
                        </div>

                        <!-- License Plate -->
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-gray-500">License Plate:</span>
                            <span class="font-medium">{{ vehicle.license_plate || 'Not Set' }}</span>
                        </div>

                        <!-- Starting Price -->
                        <div class="flex items-center gap-2">
                            <DollarSign class="h-5 w-5 text-primary-600" />
                            <div>
                                <span class="text-sm text-gray-500">Starting at:</span>
                                <p class="font-medium text-green-600">
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
                                </p>
                            </div>
                        </div>
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
                <div class="mb-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Rental Options</h2>
                    <div v-if="vehicle.pricing_tiers?.length" class="space-y-3">
                        <div 
                            v-for="tier in vehicle.pricing_tiers" 
                            :key="tier.id"
                            class="flex justify-between items-center p-3 border rounded-lg"
                        >
                            <div>
                                <span class="font-medium">{{ tier.duration_from }} {{ tier.duration_unit }}</span>
                            </div>
                            <div class="text-right">
                                <span class="text-lg font-bold text-green-600">₱{{ tier.price }}</span>
                            </div>
                        </div>
                        <div class="text-sm text-gray-500 mt-2">
                            Starting from ₱{{ Math.min(...vehicle.pricing_tiers.map(t => parseFloat(t.price))) }}
                        </div>
                    </div>
                    <div v-else class="text-gray-500">
                        No pricing information available
                    </div>
                </div>

                <!-- Book Now Button -->
                <button
                    @click="bookNow"
                    :disabled="!canBook"
                    class="w-full py-3 px-4 font-semibold rounded-lg transition-colors disabled:cursor-not-allowed flex items-center justify-center gap-2"
                    :class="canBook 
                        ? 'bg-blue-600 hover:bg-blue-700 text-white' 
                        : 'bg-gray-400 text-gray-100'"
                >
                    <CalendarCheck class="h-5 w-5" />
                    {{ 
                        hasActiveBookings 
                        ? 'Active Booking Exists' 
                        : !vehicle.is_available 
                        ? 'Not Available' 
                        : 'Book Now' 
                    }}
                </button>

                <!-- Status Message -->
                <div v-if="hasActiveBookings" class="mt-3 p-3 bg-yellow-100 border border-yellow-300 rounded-lg">
                    <p class="text-xs text-yellow-700 text-center">
                        You have {{ userActiveBookings.length }} active booking(s) for this vehicle.
                        <br>
                        <span class="underline cursor-pointer" @click="router.visit(route('bookings.index'))">
                            View your bookings
                        </span>
                    </p>
                </div>
                <p v-else class="text-xs text-gray-500 mt-3 text-center">
                    Select pickup time and payment method on the next page
                </p>
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
import { ref, computed } from "vue";
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
const userActiveBookings = page.props.userActiveBookings || [];

const hasActiveBookings = computed(() => {
    return userActiveBookings.length > 0;
});

const canBook = computed(() => {
    return vehicle.is_available && !hasActiveBookings.value;
});

function bookNow() {
    if (!canBook.value) {
        if (hasActiveBookings.value) {
            alert('You already have an active booking for this vehicle. Please manage your existing bookings first.');
        } else {
            alert('This vehicle is not available for booking.');
        }
        return;
    }
    
    router.visit(route('bookings.create', vehicle.id));
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
