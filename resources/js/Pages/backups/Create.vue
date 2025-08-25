<template>
    <OwnerLayout>
        <div class="md:col-span-3 bg-white p-6 rounded-lg shadow-md">
            <h1 class="text-3xl font-bold text-gray-800 mb-6">
                Add New Vehicle
            </h1>

            <form
                @submit.prevent="submit"
                enctype="multipart/form-data"
                class="space-y-6"
            >
                <!-- License Plate -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1"
                        >License Plate</label
                    >
                    <input
                        v-model="form.license_plate"
                        class="w-full p-2 border rounded-md focus:outline-none focus:ring focus:border-blue-400"
                        required
                    />
                </div>

                <!-- Make and Model -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Make</label>
                        <select
                            v-model="form.make_id"
                            @change="onMakeChange"
                            class="w-full p-2 border rounded-md focus:outline-none focus:ring focus:border-blue-400"
                            required
                        >
                            <option value="">Select Make</option>
                            <option v-for="make in makes" :key="make.id" :value="make.id">
                                {{ make.name }}
                            </option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Model</label>
                        <select
                            v-model="form.model_id"
                            class="w-full p-2 border rounded-md focus:outline-none focus:ring focus:border-blue-400"
                            required
                            :disabled="!form.make_id"
                        >
                            <option value="">Select Model</option>
                            <option v-for="model in models" :key="model.id" :value="model.id">
                                {{ model.name }}
                            </option>
                        </select>
                    </div>
                </div>

                <!-- Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1"
                        >Type</label
                    >
                    <select
                        v-model="form.type_id"
                        class="w-full p-2 border rounded-md focus:outline-none focus:ring focus:border-blue-400"
                        required
                    >
                        <option v-for="t in types" :key="t.id" :value="t.id">
                            {{ t.category }}
                        </option>
                    </select>
                </div>

                <!-- Fuel Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1"
                        >Fuel Type</label
                    >
                    <select
                        v-model="form.fuel_type_id"
                        class="w-full p-2 border rounded-md focus:outline-none focus:ring focus:border-blue-400"
                        required
                    >
                        <option
                            v-for="f in fuelTypes"
                            :key="f.id"
                            :value="f.id"
                        >
                            {{ f.name }}
                        </option>
                    </select>
                </div>

                <!-- Year -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1"
                        >Year</label
                    >
                    <input
                        v-model="form.year"
                        type="number"
                        class="w-full p-2 border rounded-md focus:outline-none focus:ring focus:border-blue-400"
                        required
                    />
                </div>

                <!-- Color -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1"
                        >Color</label
                    >
                    <input
                        v-model="form.color"
                        class="w-full p-2 border rounded-md focus:outline-none focus:ring focus:border-blue-400"
                        required
                    />
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1"
                        >Description</label
                    >
                    <textarea
                        v-model="form.description"
                        class="w-full p-2 border rounded-md focus:outline-none focus:ring focus:border-blue-400"
                        rows="4"
                    ></textarea>
                </div>

                <!-- Photo Upload -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1"
                        >Main Photo</label
                    >
                    <FilePondUploader
                        :allow-multiple="false"
                        :accepted-file-types="['image/*']"
                        @file-added="onMainPhotoAdded"
                    />
                    <div v-if="mainPhotoPreview" class="mt-3">
                        <img
                            :src="mainPhotoPreview"
                            class="w-40 h-28 object-cover rounded shadow"
                        />
                    </div>
                </div>

                <!-- Location Section -->
                <div
                    class="border rounded-lg p-4 bg-gray-50 shadow-sm space-y-4"
                >
                    <!-- Autocomplete Input -->
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 mb-1"
                            >Search Location</label
                        >
                        <input
                            v-model="search"
                            @input="onSearch"
                            type="text"
                            placeholder="Enter location..."
                            class="w-full p-2 border rounded-md focus:outline-none focus:ring focus:border-blue-400"
                        />
                        <ul
                            v-if="suggestions.length"
                            class="border mt-2 bg-white max-h-48 overflow-y-auto rounded shadow"
                        >
                            <li
                                v-for="(s, i) in suggestions"
                                :key="i"
                                @click="selectSuggestion(s)"
                                class="p-2 hover:bg-gray-100 cursor-pointer text-sm"
                            >
                                {{ s.properties.name }}
                                <span v-if="s.properties.city"
                                    >, {{ s.properties.city }}</span
                                >
                                <span v-if="s.properties.country"
                                    >, {{ s.properties.country }}</span
                                >
                            </li>
                        </ul>
                    </div>

                    <!-- Map -->
                    <div class="h-72 rounded overflow-hidden border shadow">
                        <l-map
                            style="height: 100%"
                            :zoom="20"
                            :center="[form.lat, form.lng]"
                            :maxBounds="bounds"
                            :minZoom="15"
                            :maxZoom="18"
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
                            <span class="font-medium text-gray-700"
                                >Coordinates:</span
                            >
                            <span class="text-gray-600"
                                >Lat {{ form.lat }}, Lng {{ form.lng }}</span
                            >
                        </div>
                        <div>
                            <span class="font-medium text-gray-700"
                                >Location:</span
                            >
                            <span class="text-gray-600">{{
                                form.location_name
                            }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="font-medium text-gray-700"
                                >Map Link:</span
                            >
                            <a
                                :href="`https://www.google.com/maps?q=${form.lat},${form.lng}`"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="inline-flex items-center text-blue-600 hover:text-blue-800 underline transition"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-4 w-4 mr-1"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 20l-5.447-2.724A2 2 0 013 15.382V5a2 2 0 012-2h14a2 2 0 012 2v10.382a2 2 0 01-1.553 1.894L15 20l-3-1.5L9 20z"
                                    />
                                </svg>
                                <span>View on Google Maps</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Pricing Section -->
                <div class="mt-8">
                    <h2 class="text-xl font-bold mb-2">Pricing</h2>
                    <div>
                        <label class="block font-medium mb-1"
                            >Select Pricing Tiers:</label
                        >
                        <select
                            v-model="selectedTierIds"
                            multiple
                            class="border p-2 rounded w-full max-w-lg"
                        >
                            <option
                                v-for="tier in pricingTiers"
                                :key="tier.id"
                                :value="tier.id"
                            >
                                {{ tier.duration_from }}
                                {{ tier.duration_unit }} - ₱{{ tier.price }}
                            </option>
                        </select>
                        <div class="text-xs text-gray-500 mt-1">
                            Manage your pricing tiers
                            <a
                                href="/owner/pricing-tiers"
                                class="text-blue-600 underline"
                                >here</a
                            >.
                        </div>
                    </div>
                </div>

                <!-- Submit -->
                <div class="pt-2">
                    <button
                        type="submit"
                        class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md transition"
                    >
                        Save Vehicle
                    </button>
                </div>
            </form>
        </div>
    </OwnerLayout>
</template>

<script setup>
import { Link } from "@inertiajs/vue3";
import OwnerLayout from "@/Layouts/OwnerLayout.vue";
import { PlusCircle, X } from "lucide-vue-next";
import { computed, onMounted, reactive, onUnmounted, ref, watch } from "vue";
import { throttle } from "lodash-es";
import { router } from "@inertiajs/vue3";
import { LMap, LTileLayer, LMarker, LGeoJson } from "@vue-leaflet/vue-leaflet";
import L from "@/plugins/leaflet-icon-fix";
import FilePondUploader from "@/Components/FilePondUploader.vue";
const locationName = ref("Fetching location...");

// Makes and models data
const makes = ref([]);
const models = ref([]);

const props = defineProps({ 
    makes: Array, 
    types: Array, 
    fuelTypes: Array, 
    transmissions: Array 
});

const form = reactive({
    license_plate: "",
    make_id: "",
    model_id: "",
    type_id: "",
    fuel_type_id: "",
    transmission_id: "",
    year: "",
    color: "",
    description: "",
    is_available: true,
    lat: 9.785903415098108,
    lng: 125.49062330098809,
    location_name: "",
});

const getLocationName = throttle(async () => {
    if (form.lat && form.lng) {
        try {
            const res = await fetch(
                `https://nominatim.openstreetmap.org/reverse?lat=${form.lat}&lon=${form.lng}&format=json`
            );
            const data = await res.json();
            form.location_name = data.display_name;
        } catch (err) {
            locationName.value = "Unknown location";
            form.location_name = "";
        }
    }
}, 1000);
const mainPhoto = ref(null);
const mainPhotoPreview = ref(null);

// Autocomplete state
const search = ref("");
const suggestions = ref([]);

let searchTimeout = null;
function onSearch() {
    clearTimeout(searchTimeout);
    if (!search.value) {
        suggestions.value = [];
        return;
    }
    const bbox = "125.25,9.77,125.50,9.80";
    searchTimeout = setTimeout(async () => {
        const url = `https://photon.komoot.io/api/?q=${encodeURIComponent(
            search.value
        )}&limit=5&bbox=${bbox}`;
        const res = await fetch(url);
        const data = await res.json();
        suggestions.value = data.features || [];
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

function onMainPhotoAdded(file) {
    mainPhoto.value = file;
    if (file) {
        mainPhotoPreview.value = URL.createObjectURL(file);
    } else {
        mainPhotoPreview.value = null;
    }
}

function onMapClick(e) {
    form.lat = e.latlng.lat;
    form.lng = e.latlng.lng;
}

const pricingTiers = ref([]);
const selectedTierIds = ref([]);

// Fetch reusable pricing tiers for this owner
onMounted(async () => {
    const res = await fetch("/owner/pricing-tiers/list");
    if (res.ok) {
        const data = await res.json();
        pricingTiers.value = data.pricingTiers || [];
    }
    
    // Load makes
    await loadMakes();
});

//...existing code...

// Load models when make changes
async function onMakeChange() {
    if (!form.make_id) {
        models.value = [];
        form.model_id = '';
        return;
    }
    
    try {
        const response = await fetch(`/api/vehicle-data/models/${form.make_id}`);
        if (response.ok) {
            models.value = await response.json();
        } else {
            models.value = [];
        }
    } catch (error) {
        console.error('Error loading models:', error);
        models.value = [];
    }
}

function submit() {
    const data = new FormData();
    Object.entries(form).forEach(([k, v]) => {
        if (k === "is_available") {
            data.append("is_available", v ? 1 : 0);
        } else {
            data.append(k, v);
        }
    });
    if (mainPhoto.value) data.append("main_photo", mainPhoto.value);
    data.append("pricing_tier_ids", JSON.stringify(selectedTierIds.value));
    router.post("/owner/vehicles", data);
}

// Surigao del Norte GeoJSON polygon (approximate, for demo)
    const surigaoGeoJson = {
        type: "FeatureCollection",
        features: [
            {
                type: "Feature",
                properties: {},
                geometry: {
                    type: "Polygon",
                    coordinates: [
                        [
                            [125.48545720374489, 9.801207813887503],
                            [125.48394937158277, 9.801134816678099],
                            [125.4824560626428, 9.800916528148765],
                            [125.48099166020462, 9.800555050821249],
                            [125.47957026901334, 9.800053866380772],
                            [125.47820557937605, 9.799417802127778],
                            [125.47691073525756, 9.79865298446448],
                            [125.4756982076485, 9.797766779865192],
                            [125.47457967442702, 9.796767723899952],
                            [125.47356590787237, 9.79566543899633],
                            [125.47266667091462, 9.79447054173279],
                            [125.47189062311993, 9.793194540557817],
                            [125.47124523731648, 9.791849724921354],
                            [125.47073672766314, 9.790449046887577],
                            [125.47036998985222, 9.789005996370452],
                            [125.47014855402033, 9.787534471194746],
                            [125.47007455081837, 9.786048643234757],
                            [125.47014869096546, 9.784562821920598],
                            [125.47037025847982, 9.78309131642699],
                            [125.47073711764999, 9.781648297871861],
                            [125.47124573367556, 9.78024766285169],
                            [125.47189120677649, 9.77890289962746],
                            [125.47266731943903, 9.77762695824917],
                            [125.47356659634224, 9.776432125868816],
                            [125.47458037638481, 9.775329908441458],
                            [125.47569889611839, 9.774330919952476],
                            [125.47691138378202, 9.773444780236375],
                            [125.47820616303265, 9.772680022369768],
                            [125.47957076537246, 9.772044010529008],
                            [125.48099205019147, 9.771542869102143],
                            [125.48245633127043, 9.771181423736623],
                            [125.48394950852794, 9.77096315488939],
                            [125.48545720374489, 9.770890164325817],
                            [125.48696489896184, 9.77096315488939],
                            [125.48845807621933, 9.771181423736623],
                            [125.48992235729831, 9.771542869102143],
                            [125.49134364211733, 9.772044010529008],
                            [125.49270824445713, 9.772680022369768],
                            [125.49400302370776, 9.773444780236375],
                            [125.4952155113714, 9.774330919952476],
                            [125.49633403110496, 9.775329908441458],
                            [125.49734781114755, 9.776432125868816],
                            [125.49824708805076, 9.77762695824917],
                            [125.49902320071328, 9.77890289962746],
                            [125.4996686738142, 9.78024766285169],
                            [125.5001772898398, 9.781648297871861],
                            [125.50054414900994, 9.78309131642699],
                            [125.50076571652433, 9.784562821920598],
                            [125.50083985667139, 9.786048643234757],
                            [125.50076585346946, 9.787534471194746],
                            [125.50054441763756, 9.789005996370452],
                            [125.50017767982663, 9.790449046887577],
                            [125.4996691701733, 9.791849724921354],
                            [125.49902378436985, 9.793194540557817],
                            [125.49824773657517, 9.79447054173279],
                            [125.4973484996174, 9.79566543899633],
                            [125.49633473306275, 9.796767723899952],
                            [125.49521619984127, 9.797766779865192],
                            [125.49400367223222, 9.79865298446448],
                            [125.49270882811372, 9.799417802127778],
                            [125.49134413847645, 9.800053866380772],
                            [125.48992274728516, 9.800555050821249],
                            [125.48845834484696, 9.800916528148765],
                            [125.48696503590699, 9.801134816678099],
                            [125.48545720374489, 9.801207813887503],
                        ],
                    ],
                },
            },
        ],
    };

const geoJsonStyle = () => ({
    color: "#1976d2",
    weight: 2,
    fill: false,
});

const bounds = [
    [9.770890164325817, 125.47007455081837],
    [9.801207813887503, 125.50083985667139],
    [125.49270882811372, 9.799417802127778],
    [125.49134413847645, 9.800053866380772],
    [125.48992274728516, 9.800555050821249],
    [125.48845834484696, 9.800916528148765],
    [125.48696503590699, 9.801134816678099],
    [125.48545720374489, 9.801207813887503],
];
watch(
    () => [form.lat, form.lng],
    () => {
        getLocationName();
    },
    { immediate: true }
);
</script>

<style>
@import "leaflet/dist/leaflet.css";
</style>
<style>
@import "leaflet/dist/leaflet.css";
</style>
