<template>
    <OwnerLayout>
        <div
            class="glass-card-dark p-6 rounded-lg shadow-glow max-w-4xl mx-auto"
        >
            <h1 class="text-3xl font-bold text-white mb-6">Edit Vehicle</h1>

            <form @submit.prevent="submit" class="space-y-6">
                <!-- Vehicle Type Display (Read-only) -->
                <div>
                    <label class="block text-sm font-medium text-white mb-1"
                        >Vehicle Category</label
                    >
                    <div
                        class="w-full p-3 bg-white/10 border border-white/20 rounded-md text-white font-medium backdrop-blur-sm"
                    >
                        {{
                            vehicle.type?.category === "car"
                                ? "🚗 Car"
                                : "🏍️ Motorcycle"
                        }}
                    </div>
                    <p class="text-xs text-white/70 mt-1">
                        Vehicle category cannot be changed after creation
                    </p>
                </div>

                <!-- Vehicle Details -->
                <h3 class="text-lg font-semibold text-white pt-2">Vehicle Details</h3>

                <!-- Make and Model Row -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-white mb-1">Make *</label>
                    <select
                    v-model="form.make_id"
                    @change="onMakeChange"
                    class="w-full p-3 rounded-md bg-black/80 text-white border-none focus:outline-none focus:ring-2 focus:ring-blue-400"
                    required
                    >
                    <option value="" class="bg-black/80 text-white">Select Make</option>
                    <option
                        v-for="make in makes"
                        :key="make.id"
                        :value="make.id"
                        class="bg-black/80 text-white hover:bg-white/10 active:bg-white/20"
                    >
                        {{ make.name }}
                    </option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-white mb-1">Model *</label>
                    <select
                    v-model="form.model_id"
                    :disabled="!form.make_id || loadingModels"
                    class="w-full p-3 rounded-md bg-black/80 text-white border-none focus:outline-none focus:ring-2 focus:ring-blue-400 disabled:opacity-60"
                    required
                    >
                    <option value="" class="bg-black/80 text-white">Select Model</option>
                    <option
                        v-for="mdl in models"
                        :key="mdl.id"
                        :value="mdl.id"
                        class="bg-black/80 text-white hover:bg-white/10 active:bg-white/20"
                    >
                        {{ mdl.name }}
                    </option>
                    </select>
                    <p v-if="loadingModels" class="text-xs text-white/60 mt-1">Loading models…</p>
                </div>
                </div>

                <!-- Transmission -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <div>
                    <label class="block text-sm font-medium text-white mb-1">Transmission *</label>
                    <select
                    v-model="form.transmission_id"
                    class="w-full p-3 rounded-md bg-black/80 text-white border-none focus:outline-none focus:ring-2 focus:ring-blue-400"
                    required
                    >
                    <option value="" class="bg-black/80 text-white">Select Transmission</option>
                    <option
                        v-for="trans in transmissions"
                        :key="trans.id"
                        :value="trans.id"
                        class="bg-black/80 text-white hover:bg-white/10 active:bg-white/20"
                    >
                        {{ trans.name }}
                    </option>
                    </select>
                </div>
                </div>

                <!-- Fuel Type -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <div>
                    <label class="block text-sm font-medium text-white mb-1">Fuel Type *</label>
                    <select
                    v-model="form.fuel_type_id"
                    class="w-full p-3 rounded-md bg-black/80 text-white border-none focus:outline-none focus:ring-2 focus:ring-blue-400"
                    required
                    >
                    <option value="" class="bg-black/80 text-white">Select Fuel Type</option>
                    <option
                        v-for="fuel in fuelTypes"
                        :key="fuel.id"
                        :value="fuel.id"
                        class="bg-black/80 text-white hover:bg-white/10 active:bg-white/20"
                    >
                        {{ fuel.name }}
                    </option>
                    </select>
                </div>
                </div>

                <!-- Vehicle Sub-Type -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                <div>
                    <label class="block text-sm font-medium text-white mb-1">Vehicle Sub-Type *</label>
                    <select
                    v-model="form.type_id"
                    class="w-full p-3 rounded-md bg-black/80 text-white border-none focus:outline-none focus:ring-2 focus:ring-blue-400"
                    required
                    >
                    <option value="" class="bg-black/80 text-white">Select Sub-Type</option>
                    <option
                        v-for="type in filteredTypes"
                        :key="type.id"
                        :value="type.id"
                        class="bg-black/80 text-white hover:bg-white/10 active:bg-white/20"
                    >
                        {{ type.sub_type }}
                    </option>
                    </select>
                </div>
                </div>

                <!-- Vehicle Specifications -->
                <h3 class="text-lg font-semibold text-white pt-2">Specifications</h3>
                <div :class="['grid gap-4', (vehicle.type?.category === 'car') ? 'grid-cols-1 md:grid-cols-4' : 'grid-cols-1 md:grid-cols-2']">
                    <!-- Engine Size (optional) -->
                    <div>
                        <label class="block text-sm font-medium text-white mb-1">
                            Engine Size <span class="text-white/60 text-xs">(optional)</span>
                        </label>
                        <input
                            v-model="form.engine_size"
                            type="text"
                            inputmode="decimal"
                            class="w-full p-3 rounded-md bg-black/80 text-white border-none focus:outline-none focus:ring-2 focus:ring-blue-400 placeholder-white/50"
                            :placeholder="(vehicle.type?.category === 'car') ? 'e.g., 2.0L' : 'e.g., 155cc'"
                        />
                    </div>

                    <!-- Horsepower -->
                    <div>
                        <label class="block text-sm font-medium text-white mb-1">Horsepower</label>
                        <input
                            v-model="form.horsepower"
                            type="number"
                            min="0"
                            class="w-full p-3 rounded-md bg-black/80 text-white border-none focus:outline-none focus:ring-2 focus:ring-blue-400 placeholder-white/50"
                            placeholder="e.g., 150"
                        />
                    </div>

                    <!-- Doors (cars only) -->
                    <div v-if="vehicle.type?.category === 'car'">
                        <label class="block text-sm font-medium text-white mb-1">Doors</label>
                        <input
                            v-model="form.doors"
                            type="number"
                            min="1"
                            max="6"
                            class="w-full p-3 rounded-md bg-black/80 text-white border-none focus:outline-none focus:ring-2 focus:ring-blue-400 placeholder-white/50"
                            placeholder="e.g., 4"
                        />
                    </div>

                    <!-- Seats (cars only) -->
                    <div v-if="vehicle.type?.category === 'car'">
                        <label class="block text-sm font-medium text-white mb-1">Seats</label>
                        <input
                            v-model="form.seats"
                            type="number"
                            min="1"
                            max="15"
                            class="w-full p-3 rounded-md bg-black/80 text-white border-none focus:outline-none focus:ring-2 focus:ring-blue-400 placeholder-white/50"
                            placeholder="e.g., 5"
                        />
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-medium text-white mb-1">Description</label>
                    <textarea
                        v-model="form.description"
                        rows="3"
                        class="w-full p-3 rounded-md bg-black/80 text-white border-none focus:outline-none focus:ring-2 focus:ring-blue-400 placeholder-white/50"
                        placeholder="Optional description of your vehicle"
                    ></textarea>
                </div>


                <!-- Availability Toggle -->
                <div class="flex items-center">
                    <input
                        v-model="form.is_available"
                        type="checkbox"
                        id="is_available"
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                    />
                    <label
                        for="is_available"
                        class="ml-2 block text-sm text-white"
                    >
                        Vehicle is available for rental
                    </label>
                </div>

                <!-- Main Photo Section -->
                <div class="main-photo-section border border-white/20 rounded-lg p-6 bg-white/5 backdrop-blur-sm shadow-glow space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-white">Main Vehicle Photo</h3>
                        <span class="text-xs text-white/60 bg-white/10 px-2 py-1 rounded-full">Required</span>
                    </div>

                    <!-- Current Photo Display -->
                    <div v-if="form.main_photo_url" class="current-photo-display">
                        <div class="flex items-start gap-4">
                            <div class="relative group">
                                <img
                                    :src="form.main_photo_url"
                                    alt="Current main photo"
                                    class="w-32 h-24 sm:w-48 sm:h-32 object-cover rounded-lg border border-white/20 shadow-md transition-transform group-hover:scale-105"
                                />
                                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-all duration-300 rounded-lg flex items-center justify-center opacity-0 group-hover:opacity-100">
                                    <span class="text-white text-sm font-medium">Current Photo</span>
                                </div>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-white/90 mb-1">Current Main Photo</p>
                                <p class="text-xs text-white/60 mb-3">This is the main image renters will see first</p>
                                <div class="inline-flex items-center gap-2 text-xs text-green-400">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    Photo uploaded
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 p-3 bg-blue-500/10 border border-blue-400/20 rounded-lg">
                            <p class="text-xs text-blue-200">
                                <span class="font-medium">Replace photo:</span> Upload a new photo below to replace the current main photo. The new photo will be automatically optimized for best performance.
                            </p>
                        </div>
                    </div>

                    <!-- Photo Uploader -->
                    <div class="photo-uploader-wrapper">
                        <CustomImageUploader 
                            :multiple="false" 
                            @file-selected="onMainPhotoChange" 
                        />
                    </div>

                    <!-- Photo Guidelines -->
                    <div class="photo-guidelines bg-white/5 border border-white/10 rounded-lg p-4">
                        <h4 class="text-sm font-medium text-white mb-2">📸 Photo Guidelines</h4>
                        <ul class="text-xs text-white/70 space-y-1">
                            <li>• Use high-quality, well-lit photos</li>
                            <li>• Show the vehicle's exterior clearly</li>
                            <li>• Avoid blurry or dark images</li>
                            <li>• Recommended size: 1200x800px</li>
                            <li>• Supported formats: JPEG, PNG, WebP</li>
                        </ul>
                    </div>
                </div>

                <!-- Location Section -->
                <div
                    class="border border-white/20 rounded-lg p-4 bg-white/5 backdrop-blur-sm shadow-glow space-y-4"
                >
                    <h3 class="text-lg font-semibold text-white">
                        Vehicle Location
                    </h3>
                    <div class="flex items-center justify-between">
                        <p class="text-xs sm:text-sm text-white/70">Tip: Click on the map to set location manually.</p>
                        <button
                            type="button"
                            @click="useCurrentLocation"
                            :disabled="locating"
                            class="inline-flex items-center gap-2 px-3 py-2 rounded-md border border-white/20 bg-white/10 text-white hover:bg-white/15 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ locating ? 'Locating…' : 'Use my current location' }}
                        </button>
                    </div>

                    <!-- Location Search -->
                    <div>
                        <label class="block text-sm font-medium text-white mb-1"
                            >Search Location</label
                        >
                        <input
                            v-model="search"
                            @input="onSearch"
                            type="text"
                            placeholder="Search for a location in Surigao del Norte"
                            class="w-full p-2 border border-white/20 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400 bg-white/10 text-white backdrop-blur-sm placeholder-white/50"
                        />

                        <div
                            v-if="suggestions.length > 0"
                            class="mt-2 bg-white border rounded-md shadow-lg max-h-40 overflow-y-auto"
                        >
                            <div
                                v-for="suggestion in suggestions"
                                :key="suggestion.properties.osm_id"
                                @click="selectSuggestion(suggestion)"
                                class="p-2 hover:bg-gray-100 cursor-pointer border-b last:border-b-0"
                            >
                                <div class="text-sm font-medium">
                                    {{ suggestion.properties.name }}
                                </div>
                                <div class="text-xs text-gray-600">
                                    {{ suggestion.properties.city }},
                                    {{ suggestion.properties.country }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Map -->
                    <div class="h-72 rounded overflow-hidden border shadow">
                        <l-map
                            style="height: 100%"
                            :zoom="16"
                            :center="[form.lat, form.lng]"
                            :useGlobalLeaflet="true"
                            @click="onMapClick"
                        >
                            <l-tile-layer
                                url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
                            />
                            <l-marker :lat-lng="[form.lat, form.lng]" />
                            <l-geo-json
                                :geojson="surigaoGeoJson"
                                :options-style="geoJsonStyle"
                            />
                        </l-map>
                    </div>

                    <!-- Info Box -->
                    <div
                        class="space-y-2 text-sm bg-white p-4 rounded shadow-inner border"
                    >
                        <div>
                            <span class="font-medium text-gray-700">Coordinates:</span>
                            <span class="text-gray-600" :title="`Lat ${form.lat}, Lng ${form.lng}`">Lat {{ formatShort(form.lat) }}, Lng {{ formatShort(form.lng) }}</span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-700"
                                >Location:</span
                            >
                            <span class="text-gray-600">{{
                                form.location_name || "—"
                            }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="font-medium text-gray-700"
                                >Map Link:</span
                            >
                            <a
                                :href="`https://www.google.com/maps?q=${form.lat},${form.lng}`"
                                target="_blank"
                                class="text-blue-600 hover:text-blue-800 underline"
                            >
                                View on Google Maps
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Pricing Tiers -->
                <div class="border-t pt-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg text-white font-semibold">
                            Pricing
                        </h3>
                        <button
                            type="button"
                            @click="loadPricingTiers"
                            :disabled="loadingPricingTiers"
                            class="text-white flex items-center px-3 py-1 text-sm text-blue-600 hover:text-blue-800 border border-blue-300 hover:border-blue-400 rounded-md transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <svg
                                :class="[
                                    'w-4 h-4 mr-1',
                                    { 'animate-spin': loadingPricingTiers },
                                ]"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <circle
                                    class="opacity-25"
                                    cx="12"
                                    cy="12"
                                    r="10"
                                    stroke-width="4"
                                ></circle>
                                <path
                                    class="opacity-75"
                                    fill="currentColor"
                                    d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"
                                ></path>
                            </svg>

                            {{
                                loadingPricingTiers
                                    ? "Refreshing..."
                                    : "Refresh"
                            }}
                        </button>
                    </div>
                    <div v-if="pricingTiers.length > 0">
                        <p class="text-sm text-gray-600 mb-3 text-white">
                            Select pricing tiers for this vehicle:
                        </p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <label
                                v-for="tier in pricingTiers"
                                :key="tier.id"
                                class="flex text-white items-center p-3 border rounded-md cursor-pointer hover:bg-gray-500"
                            >
                                <input
                                    type="checkbox"
                                    :value="tier.id"
                                    v-model="selectedTierIds"
                                    class="mr-3 h-4 w-4 text-blue-600"
                                />
                                <div>
                                    <div class="font-medium text-white">
                                        {{ tier.duration_from }}
                                        {{ tier.duration_unit }}
                                    </div>
                                    <div class="text-sm white">
                                        ₱{{ tier.price }}
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>
                    <div
                        v-else-if="loadingPricingTiers"
                        class="text-sm text-gray-500"
                    >
                        Loading pricing tiers...
                    </div>
                    <div v-else class="text-sm text-gray-500">
                        No pricing tiers available.
                    </div>
                    <p class="text-sm text-gray-300 mt-2">
                        You can manage pricing tiers in the
                        <Link
                            href="/owner/pricing-tiers"
                            class="text-blue-600 underline"
                            >pricing management</Link
                        >
                        section.
                    </p>
                </div>

                <!-- Submit Buttons -->
                <div class="border-t pt-6 flex space-x-4">
                    <button
                        type="submit"
                        :disabled="submitting"
                        class="bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white font-medium py-3 px-6 rounded-md transition-colors"
                    >
                        {{
                            submitting
                                ? "Updating Vehicle..."
                                : "Update Vehicle"
                        }}
                    </button>

                    <Link
                        :href="`/owner/vehicles/${vehicle.id}`"
                        class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-3 px-6 rounded-md transition-colors"
                    >
                        Cancel
                    </Link>
                </div>
            </form>
        </div>
    </OwnerLayout>

    <!-- Out of Range / Geolocation Modal -->
    <div
        v-if="showOutOfRangeModal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4"
        @click.self="showOutOfRangeModal = false"
    >
        <div
            class="glass-card-dark max-w-md w-full rounded-lg border border-white/20 shadow-glow"
        >
            <div
                class="p-5 border-b border-white/10 flex items-center justify-between"
            >
                <h3 class="text-white font-semibold">Location Notice</h3>
                <button
                    class="text-white/70 hover:text-white"
                    @click="showOutOfRangeModal = false"
                >
                    ✕
                </button>
            </div>
            <div class="p-5 text-white/90 space-y-3">
                <p>{{ outOfRangeMessage }}</p>
                <ul
                    class="list-disc list-inside text-white/70 text-sm space-y-1"
                >
                    <li>
                        Ensure location services are enabled and grant
                        permission.
                    </li>
                    <li>
                        If you are outside Surigao del Norte city limits, pick a
                        valid in-city meeting point manually.
                    </li>
                </ul>
            </div>
            <div class="p-5 pt-0 flex justify-end gap-2">
                <button
                    class="px-4 py-2 rounded-md border border-white/20 text-white/90 hover:bg-white/10"
                    @click="showOutOfRangeModal = false"
                >
                    Close
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, onMounted, reactive, ref, watch } from "vue";
import { router, Link } from "@inertiajs/vue3";
import { throttle } from "lodash-es";
import OwnerLayout from "@/Layouts/OwnerLayout.vue";
import { LMap, LTileLayer, LMarker, LGeoJson } from "@vue-leaflet/vue-leaflet";
import L from "@/plugins/leaflet-icon-fix";
import CustomImageUploader from "@/Components/CustomImageUploader.vue";

const props = defineProps({
    vehicle: Object,
    makes: Array,
    models: Array,
    types: Array,
    fuelTypes: Array,
    transmissions: Array,
});

const form = reactive({
    make_id: props.vehicle.make_id,
    model_id: props.vehicle.model_id,
    type_id: props.vehicle.type_id,
    fuel_type_id: props.vehicle.fuel_type_id,
    transmission_id: props.vehicle.transmission_id,
    license_plate: props.vehicle.license_plate,
    year: props.vehicle.year,
    color: props.vehicle.color,
    // New specs
    engine_size: props.vehicle.engine_size ?? '',
    horsepower: props.vehicle.horsepower ?? '',
    doors: props.vehicle.doors ?? '',
    seats: props.vehicle.seats ?? '',
    description: props.vehicle.description,
    is_available: props.vehicle.is_available,
    main_photo_url: props.vehicle.main_photo_url,
    lat: props.vehicle.lat ?? 9.785903415098108,
    lng: props.vehicle.lng ?? 125.49062330098809,
    location_name: props.vehicle.location_name,
});

const makes = ref(props.makes || []);
const models = ref(props.models || []);
const pricingTiers = ref([]);
const selectedTierIds = ref(
    (props.vehicle.pricing_tiers || []).map((t) => t.id)
);
const loadingModels = ref(false);
const submitting = ref(false);
const loadingPricingTiers = ref(false);
const mainPhoto = ref(null);
const mainPhotoPreview = ref(null);
const locating = ref(false);
const showOutOfRangeModal = ref(false);
const outOfRangeMessage = ref('Your current location appears to be outside the supported service area.');

// Autocomplete state
const search = ref("");
const suggestions = ref([]);

// Filter types by the vehicle's category
const filteredTypes = computed(() => {
    const category = props.vehicle?.type?.category;
    if (!category) return props.types || [];
    return (props.types || []).filter((t) => t.category === category);
});

const getLocationName = throttle(async () => {
    if (form.lat && form.lng && !form.location_name) {
        try {
            const response = await fetch(
                `https://nominatim.openstreetmap.org/reverse?format=json&lat=${form.lat}&lon=${form.lng}`
            );
            const data = await response.json();
            form.location_name = data.display_name || "";
        } catch (err) {
            console.error("Failed to get location name:", err);
        }
    }
}, 1000);

let searchTimeout = null;
function onSearch() {
    clearTimeout(searchTimeout);
    if (!search.value) {
        suggestions.value = [];
        return;
    }

    searchTimeout = setTimeout(async () => {
        const bbox = "125.25,9.77,125.50,9.80";
        const url = `https://photon.komoot.io/api/?q=${encodeURIComponent(
            search.value
        )}&limit=5&bbox=${bbox}`;
        try {
            const res = await fetch(url);
            const data = await res.json();
            suggestions.value = data.features || [];
        } catch (err) {
            console.error("Search failed:", err);
        }
    }, 300);
}

function selectSuggestion(s) {
    form.lat = s.geometry.coordinates[1];
    form.lng = s.geometry.coordinates[0];
    search.value =
        s.properties.name +
        (s.properties.city ? ", " + s.properties.city : "") +
        (s.properties.country ? ", " + s.properties.country : "");
    suggestions.value = [];
}

function onMainPhotoChange(file) {
    mainPhoto.value = file;
    // FilePond handles preview automatically, no need for manual preview
}

function onMapClick(e) {
    const clickedLat = e.latlng.lat;
    const clickedLng = e.latlng.lng;

    // Check if the clicked location is within the allowed boundary
    if (!isWithinCity(clickedLat, clickedLng)) {
        outOfRangeMessage.value =
            "You can only place markers within the Surigao del Norte service area. Please click inside the highlighted boundary.";
        showOutOfRangeModal.value = true;
        return;
    }

    form.lat = clickedLat;
    form.lng = clickedLng;
}

// Short formatter for coordinates
function formatShort(val, digits = 4) {
    if (val === null || val === undefined || isNaN(val)) return '—';
    const n = Number(val);
    return n.toFixed(digits);
}

// Determine if a point (lat, lng) is within the Surigao polygon
function isWithinCity(lat, lng) {
    try {
        // For Polygon type, coordinates are [[[lng, lat], [lng, lat], ...]]
        const poly = surigaoGeoJson.features?.[0]?.geometry?.coordinates?.[0]
        if (!poly || !Array.isArray(poly)) return false

        const x = lng // polygon is [lng, lat]
        const y = lat
        let inside = false
        for (let i = 0, j = poly.length - 1; i < poly.length; j = i++) {
            const xi = poly[i][0], yi = poly[i][1]
            const xj = poly[j][0], yj = poly[j][1]
            const intersect = yi > y !== yj > y && x < ((xj - xi) * (y - yi)) / (yj - yi) + xi
            if (intersect) inside = !inside
        }
        return inside
    } catch (e) {
        console.error("Error checking if point is within city:", e);
        return false
    }
}

function useCurrentLocation() {
    if (!('geolocation' in navigator)) {
        outOfRangeMessage.value = 'Geolocation is not supported by your browser. Please set the location manually on the map.'
        showOutOfRangeModal.value = true
        return
    }
    locating.value = true
    navigator.geolocation.getCurrentPosition(
        (pos) => {
            locating.value = false
            const { latitude, longitude } = pos.coords
            if (isWithinCity(latitude, longitude)) {
                form.lat = latitude
                form.lng = longitude
                getLocationName()
            } else {
                outOfRangeMessage.value = 'Your current location is outside Surigao del Norte city bounds. Please pick a valid location within the highlighted area.'
                showOutOfRangeModal.value = true
            }
        },
        (err) => {
            locating.value = false
            if (err.code === 1) {
                outOfRangeMessage.value = 'Permission to access location was denied. You can enable it in your browser settings or set the location manually.'
            } else if (err.code === 2) {
                outOfRangeMessage.value = 'Position unavailable. Please try again or set the location manually.'
            } else if (err.code === 3) {
                outOfRangeMessage.value = 'Location request timed out. Please try again or set the location manually.'
            } else {
                outOfRangeMessage.value = 'Unable to get your current location. Please set the location manually.'
            }
            showOutOfRangeModal.value = true
        },
        { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
    )
}

async function onMakeChange() {
    if (!form.make_id) {
        form.model_id = "";
        models.value = [];
        return;
    }

    loadingModels.value = true;
    try {
        const response = await fetch(
            `/api/vehicle-data/models/${form.make_id}`
        );
        const data = await response.json();
        models.value = data || [];

        // ✅ Only reset if the current model_id isn't in the new models
        if (!models.value.some((m) => m.id === form.model_id)) {
            form.model_id = "";
        }
    } catch (err) {
        console.error("Failed to load models:", err);
        models.value = [];
        form.model_id = "";
    } finally {
        loadingModels.value = false;
    }
}

// Fetch reusable pricing tiers for this owner
async function loadPricingTiers() {
    loadingPricingTiers.value = true;
    try {
        const res = await fetch("/owner/pricing-tiers/list");
        if (res.ok) {
            const data = await res.json();
            pricingTiers.value = data.pricingTiers || [];
        }
    } catch (err) {
        console.error("Failed to load pricing tiers:", err);
    } finally {
        loadingPricingTiers.value = false;
    }
}

onMounted(async () => {
    await loadPricingTiers();

    // Load models if make is selected
    if (form.make_id) {
        await onMakeChange();
    }
});

async function submit() {
    submitting.value = true;

    await getLocationName();

    const data = new FormData();

    // Add all form fields explicitly
    data.append("make_id", form.make_id);
    data.append("model_id", form.model_id);
    data.append("type_id", form.type_id);
    data.append("fuel_type_id", form.fuel_type_id);
    data.append("transmission_id", form.transmission_id);
    data.append("license_plate", form.license_plate || "");
    data.append("year", form.year);
    data.append("color", form.color);
    // New specs
    if (form.engine_size !== undefined && form.engine_size !== null) {
        data.append("engine_size", form.engine_size);
    }
    if (form.horsepower !== undefined && form.horsepower !== null && form.horsepower !== '') {
        data.append("horsepower", form.horsepower);
    }
    if (form.doors !== undefined && form.doors !== null && form.doors !== '') {
        data.append("doors", form.doors);
    }
    if (form.seats !== undefined && form.seats !== null && form.seats !== '') {
        data.append("seats", form.seats);
    }
    data.append("description", form.description || "");
    data.append("is_available", form.is_available ? "1" : "0");
    data.append("lat", form.lat);
    data.append("lng", form.lng);
    data.append("location_name", form.location_name || "");

    if (mainPhoto.value) {
        data.append("main_photo", mainPhoto.value);
    }

    data.append("_method", "PUT");
    data.append("pricing_tier_ids", JSON.stringify(selectedTierIds.value));

    router.post(`/owner/vehicles/${props.vehicle.id}`, data, {
        onFinish: () => {
            submitting.value = false;
        },
        onSuccess: () => {
            // Optionally redirect or show success message
        },
        onError: (errors) => {
            console.error("Update failed:", errors);
        },
    });
}

const surigaoGeoJson = {
  "type": "FeatureCollection",
  "features": [
    {
      "type": "Feature",
      "properties": {},
      "geometry": {
        "coordinates": [
          [
            [
              125.48262478637525,
              9.756059051678463
            ],
            [
              125.43127642703769,
              9.790933650321918
            ],
            [
              125.43368935603019,
              9.824558654805614
            ],
            [
              125.44164551553729,
              9.826558849930976
            ],
            [
              125.50466480111874,
              9.787610886673647
            ],
            [
              125.50694880274585,
              9.76500759737057
            ],
            [
              125.48263246790816,
              9.756027787360921
            ],
            [
              125.48262478637525,
              9.756059051678463
            ]
          ]
        ],
        "type": "Polygon"
      }
    }
  ]
};

const geoJsonStyle = () => ({
    color: "#1976d2",
    weight: 2,
    fill: false,
});

const bounds = [
    [9.770890164325817, 125.47007455081837],
    [9.801207813887503, 125.50083985667139],
];

watch(
    () => [form.lat, form.lng],
    () => {
        getLocationName();
    }
);
</script>

<style>
@import "leaflet/dist/leaflet.css";

/* Main photo section styling */
.main-photo-section {
  position: relative;
  overflow: hidden;
}

.current-photo-display {
  margin-bottom: 1.5rem;
}

.photo-uploader-wrapper {
  position: relative;
  z-index: 1;
}

.photo-guidelines {
  margin-top: 1rem;
}

.photo-guidelines h4 {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

/* Ensure proper spacing and layout */
.main-photo-section .current-photo-display img {
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.main-photo-section .current-photo-display img:hover {
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
}

/* Responsive adjustments */
@media (max-width: 640px) {
  .current-photo-display .flex {
    flex-direction: column;
    gap: 1rem;
  }
  
  .current-photo-display img {
    width: 100%;
    height: auto;
    max-width: 300px;
    margin: 0 auto;
  }
}

/* Animation for photo transitions */
.current-photo-display {
  animation: fadeIn 0.5s ease-in-out;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>
