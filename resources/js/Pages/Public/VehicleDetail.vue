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

                                        <!-- Enhanced Owner Information Section -->
<div v-if="vehicle.owner" class="mt-6 mb-6 bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
  <div class="flex flex-col md:flex-row items-start gap-6">
    <!-- Owner Profile Section -->
    <div class="flex items-center gap-4 flex-shrink-0">
      <!-- Avatar -->
      <div class="relative">
        <div v-if="vehicle.owner.profile_picture_url" class="w-16 h-16 rounded-full overflow-hidden shadow-md border-4 border-white ring-2 ring-gray-100">
          <img :src="vehicle.owner.profile_picture_url" :alt="vehicle.owner.name" class="w-full h-full object-cover">
        </div>
        <div v-else class="w-16 h-16 bg-gradient-to-br from-purple-500 to-blue-600 rounded-full flex items-center justify-center text-white text-xl font-bold shadow-md border-4 border-white ring-2 ring-gray-100">
          {{ vehicle.owner.name?.charAt(0)?.toUpperCase() || 'O' }}
        </div>
        <!-- Online Status Indicator -->
        <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-green-500 rounded-full border-2 border-white shadow-sm"></div>
      </div>

      <!-- Owner Details -->
      <div class="flex-1">
        <div class="flex items-center gap-2 mb-1">
          <h3 class="text-lg font-semibold text-gray-900">{{ vehicle.owner.name }}</h3>
          <div v-if="vehicle.owner?.kyc_status === 'approved'" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
            Verified Owner
          </div>
        </div>
        <p class="text-sm text-gray-600 mb-3">Active recently • Member since {{ vehicle.owner.created_at ? new Date(vehicle.owner.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'long' }) : 'Recently' }}</p>
        
        <!-- View Vehicles Button -->
        <button 
          @click="viewOwnerVehicles"
          class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-lg transition-all duration-200 hover:shadow-sm"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
          </svg>
          View Owner's Vehicles
        </button>
      </div>
    </div>

    <!-- Stats Section -->
    <div class="flex-1 md:ml-6">
      <h4 class="text-sm font-medium text-gray-700 mb-3">Owner Statistics</h4>
      <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="bg-gray-50 rounded-lg p-3 text-center">
          <div class="text-lg font-bold text-gray-900">
            {{ ratingStats?.total_ratings || vehicle.ratings_count || '0' }}
          </div>
          <div class="text-xs text-gray-600">Total Ratings</div>
        </div>

        <div class="bg-yellow-50 rounded-lg p-3 text-center">
          <div class="flex items-center justify-center gap-1 text-lg font-bold text-gray-900">
            <span>{{ ratingStats?.average_rating ? parseFloat(ratingStats.average_rating).toFixed(1) : (vehicle.ratings_avg_rating ? parseFloat(vehicle.ratings_avg_rating).toFixed(1) : '0.0') }}</span>
            <span class="text-yellow-500 text-sm">★</span>
          </div>
          <div class="text-xs text-gray-600">Average Rating</div>
        </div>

        <div class="bg-blue-50 rounded-lg p-3 text-center">
          <div class="text-lg font-bold text-gray-900">
            {{ vehicle.owner.vehicles?.length || '1' }}
          </div>
          <div class="text-xs text-gray-600">Vehicles Listed</div>
        </div>
      </div>
    </div>
  </div>
</div>
                </div>
                

                <!-- Vehicle Details Grid -->
                <div class="bg-gray-50 rounded-lg p-6 mb-6">
                                    <h2 class="text-2xl font-semibold text-gray-800 mb-4">
                    Description
                </h2>
                <p class="text-gray-700 leading-relaxed mb-6">
                    {{ vehicle.description }}
                </p>
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

                <!-- Ratings Section -->
                <RatingSection v-if="ratingStats" :rating-stats="ratingStats" class="mt-8" />
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
import RatingSection from "@/Components/Vehicle/RatingSection.vue";

const page = usePage();
const vehicle = page.props.vehicle;
const userActiveBookings = page.props.userActiveBookings || [];
const ratingStats = page.props.ratingStats;

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

function viewOwnerVehicles() {
    router.visit(route('owner.vehicles.public', vehicle.owner.id));
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
