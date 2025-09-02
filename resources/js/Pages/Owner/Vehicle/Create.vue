<template>
    <OwnerLayout>
        <div class="min-h-screen py-8" style="background: linear-gradient(135deg, #535862 0%, #3a3f4a 100%);">
            <div class="glass-card-dark p-6 rounded-lg shadow-glow max-w-4xl mx-auto border border-white/20">
                <h1 class="text-3xl font-bold text-white mb-6">Add New Vehicle</h1>

            <!-- Location Section -->
            <div class="border border-white/20 rounded-lg p-4 bg-white/5 backdrop-blur-sm shadow-glow space-y-4 mb-6">
                <!-- Map -->
                <div class="h-72 rounded overflow-hidden border border-white/20 shadow-glow">
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
                <div class="space-y-2 text-sm bg-white/10 backdrop-blur-sm p-4 rounded shadow-glow border border-white/20">
                    <div>
                        <span class="font-medium text-white/90">Coordinates:</span>
                        <span class="text-white/70">
                            Lat {{ form.lat }}, Lng {{ form.lng }}
                        </span>
                    </div>
                    <div>
                        <span class="font-medium text-white/90">Location:</span>
                        <span class="text-white/70">
                            {{ form.location_name }}
                        </span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="font-medium text-white/90">Map Link:</span>
                        <a
                            :href="`https://www.google.com/maps?q=${form.lat},${form.lng}`"
                            target="_blank"
                            class="text-blue-400 hover:text-blue-300 underline flex items-center transition-colors"
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

            <!-- Form Section -->
            <form @submit.prevent="submit" class="space-y-6">
                
                <!-- Vehicle Type Selection -->
                <div>
                    <label class="block text-sm font-medium text-white mb-2">Vehicle Type *</label>
                    <div class="grid grid-cols-2 gap-4">
                        <button
                            type="button"
                            @click="form.vehicle_type = 'car'"
                            :class="[
                                'p-4 border-2 rounded-lg text-center font-medium transition-all backdrop-blur-sm',
                                form.vehicle_type === 'car' 
                                    ? 'border-blue-400 bg-blue-400/20 text-blue-300' 
                                    : 'border-white/30 hover:border-white/50 text-white/80 bg-white/10'
                            ]"
                        >
                            🚗 Car
                        </button>
                        <button
                            type="button"
                            @click="form.vehicle_type = 'motorcycle'"
                            :class="[
                                'p-4 border-2 rounded-lg text-center font-medium transition-all backdrop-blur-sm',
                                form.vehicle_type === 'motorcycle' 
                                    ? 'border-blue-400 bg-blue-400/20 text-blue-300' 
                                    : 'border-white/30 hover:border-white/50 text-white/80 bg-white/10'
                            ]"
                        >
                            🏍️ Motorcycle
                        </button>
                    </div>
                </div>

                <!-- Make and Model Row -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4" v-if="form.vehicle_type">
                    <div>
                        <label class="block text-sm font-medium text-white mb-1">Make *</label>
                        <select
                            v-model="form.make_id"
                            @change="onMakeChange"
                            class="w-full p-3 bg-white/10 border border-white/20 rounded-md text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent backdrop-blur-sm"
                            required
                        >
                            <option value="" class="bg-gray-800 text-white">Select Make</option>
                            <option v-for="make in filteredMakes" :key="make.id" :value="make.id" class="bg-gray-800 text-white">
                                {{ make.name }}
                            </option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-white mb-1">Model *</label>
                        <select
                            v-model="form.model_id"
                            class="w-full p-3 bg-white/10 border border-white/20 rounded-md text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent backdrop-blur-sm disabled:opacity-50"
                            :disabled="!form.make_id || loadingModels"
                            required
                        >
                            <option value="" class="bg-gray-800 text-white">{{ loadingModels ? 'Loading...' : 'Select Model' }}</option>
                            <option v-for="model in models" :key="model.id" :value="model.id" class="bg-gray-800 text-white">
                                {{ model.name }}
                            </option>
                        </select>
                    </div>
                </div>

                <!-- Year, Transmission, Fuel Type Row -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-white mb-1">Year *</label>
                        <select
                            v-model="form.year"
                            class="w-full p-3 bg-white/10 border border-white/20 rounded-md text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent backdrop-blur-sm"
                            required
                        >
                            <option value="" class="bg-gray-800 text-white">Select Year</option>
                            <option v-for="year in years" :key="year" :value="year" class="bg-gray-800 text-white">{{ year }}</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-white mb-1">Transmission *</label>
                        <select
                            v-model="form.transmission_id"
                            class="w-full p-3 bg-white/10 border border-white/20 rounded-md text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent backdrop-blur-sm"
                            required
                        >
                            <option value="" class="bg-gray-800 text-white">Select Transmission</option>
                            <option v-for="transmission in transmissions" :key="transmission.id" :value="transmission.id" class="bg-gray-800 text-white">
                                {{ transmission.name }}
                            </option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-white mb-1">Fuel Type *</label>
                        <select
                            v-model="form.fuel_type_id"
                            class="w-full p-3 bg-white/10 border border-white/20 rounded-md text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent backdrop-blur-sm"
                            required
                        >
                            <option value="" class="bg-gray-800 text-white">Select Fuel Type</option>
                            <option v-for="fuel in fuelTypes" :key="fuel.id" :value="fuel.id" class="bg-gray-800 text-white">
                                {{ fuel.name }}
                            </option>
                        </select>
                    </div>
                </div>

                <!-- License Plate and Color Row -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-white mb-1">License Plate</label>
                        <input
                            v-model="form.license_plate"
                            type="text"
                            class="w-full p-3 bg-white/10 border border-white/20 rounded-md text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent backdrop-blur-sm"
                            placeholder="Enter license plate"
                        />
                        <p class="text-xs text-white/70 mt-1">Leave blank if not assigned yet</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-white mb-1">Color *</label>
                        <input
                            v-model="form.color"
                            type="text"
                            class="w-full p-3 bg-white/10 border border-white/20 rounded-md text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent backdrop-blur-sm"
                            placeholder="Enter vehicle color"
                            required
                        />
                    </div>
                </div>

                <!-- Vehicle Type Sub-Category -->
                <div>
                    <label class="block text-sm font-medium text-white mb-1">Vehicle Sub-Type *</label>
                    <select
                        v-model="form.type_id"
                        class="w-full p-3 bg-white/10 border border-white/20 rounded-md text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent backdrop-blur-sm"
                        required
                    >
                        <option value="" class="bg-gray-800 text-white">Select Sub-Type</option>
                        <option v-for="type in filteredTypes" :key="type.id" :value="type.id" class="bg-gray-800 text-white">
                            {{ type.sub_type }}
                        </option>
                    </select>
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-medium text-white mb-1">Description</label>
                    <textarea
                        v-model="form.description"
                        rows="3"
                        class="w-full p-3 bg-white/10 border border-white/20 rounded-md text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent backdrop-blur-sm"
                        placeholder="Describe your vehicle"
                    ></textarea>
                </div>

                <!-- Photo Upload -->
                <div>
                    <label class="block text-sm font-medium text-white mb-1">Main Photo</label>
                    <FilePondUploader @file-added="onPhotoChange" />
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

                <!-- Submit Button -->
                <div class="border-t border-white/20 pt-6">
                    <button
                        type="submit"
                        :disabled="submitting"
                        class="w-full bg-blue-600 hover:bg-blue-700 disabled:bg-gray-500 disabled:opacity-50 text-white font-medium py-3 px-6 rounded-md transition-colors shadow-lg"
                    >
                        {{ submitting ? 'Creating Vehicle...' : 'Create Vehicle' }}
                    </button>
                </div>
            </form>
        </div>
        </div>
    </OwnerLayout>
</template>

<script setup>
import { ref, reactive, computed, onMounted, watch } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import { throttle } from "lodash-es";

import OwnerLayout from '@/Layouts/OwnerLayout.vue'
import { LMap, LTileLayer, LMarker, LGeoJson } from '@vue-leaflet/vue-leaflet'
import FilePondUploader from '@/Components/FilePondUploader.vue'

const props = defineProps({
    fuelTypes: Array,
    transmissions: Array,
    types: Array
})

const form = reactive({
    vehicle_type: '',
    make_id: '',
    model_id: '',
    type_id: '',
    fuel_type_id: '',
    transmission_id: '',
    year: '',
    license_plate: '',
    color: '',
    description: '',
    is_available: true,
    lat: 9.785903415098108, // Default to Surigao del Norte
    lng: 125.49062330098809,
    location_name: 'Surigao del Norte, Philippines'
})

const makes = ref([])
const models = ref([])
const pricingTiers = ref([])
const selectedTierIds = ref([])
const loadingModels = ref(false)
const submitting = ref(false)
const loadingPricingTiers = ref(false)
const photoFile = ref(null)
const photoPreview = ref(null)

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

const geoJsonStyle = () => ({
    color: "#1976d2",
    weight: 2,
    fill: false,
});

const bounds = [
    [9.770890164325817, 125.47007455081837], // Southwest
    [9.801207813887503, 125.50083985667139], // Northeast
];
watch(
    () => [form.lat, form.lng],
    () => {
        getLocationName();
    },
    { immediate: true }
);

// Generate years from 2000 to current year + 1
const years = computed(() => {
    const currentYear = new Date().getFullYear()
    const yearList = []
    for (let year = currentYear + 1; year >= 2000; year--) {
        yearList.push(year)
    }
    return yearList
})

const filteredMakes = computed(() => {
    if (!form.vehicle_type) return []
    return makes.value.filter(make => make.vehicle_type === form.vehicle_type)
})

const filteredTypes = computed(() => {
    if (!form.vehicle_type) return props.types || []
    // Map vehicle_type to match VehicleType category
    const categoryMapping = {
        'car': 'car',
        'motorcycle': 'motorcycle'
    }
    const targetCategory = categoryMapping[form.vehicle_type]
    return (props.types || []).filter(type => type.category === targetCategory)
})

// Load makes when component mounts
onMounted(async () => {
    await loadMakes()
    await loadPricingTiers()
})

// Watch for vehicle type changes
watch(() => form.vehicle_type, () => {
    form.make_id = ''
    form.model_id = ''
    form.type_id = ''
    models.value = []
})

async function loadMakes() {
    try {
        const response = await fetch('/api/vehicle-data/makes/car')
        if (response.ok) {
            const carMakes = await response.json()
            const motorcycleResponse = await fetch('/api/vehicle-data/makes/motorcycle')
            if (motorcycleResponse.ok) {
                const motorcycleMakes = await motorcycleResponse.json()
                makes.value = [...carMakes, ...motorcycleMakes]
            }
        }
    } catch (error) {
        console.error('Error loading makes:', error)
    }
}

async function loadPricingTiers() {
    loadingPricingTiers.value = true
    try {
        const response = await fetch('/owner/pricing-tiers/list')
        if (response.ok) {
            const data = await response.json()
            pricingTiers.value = data.pricingTiers || []
        }
    } catch (error) {
        console.error('Error loading pricing tiers:', error)
    } finally {
        loadingPricingTiers.value = false
    }
}

async function onMakeChange() {
    if (!form.make_id) {
        models.value = []
        return
    }
    
    loadingModels.value = true
    form.model_id = ''
    
    try {
        const response = await fetch(`/api/vehicle-data/models/${form.make_id}`)
        if (response.ok) {
            models.value = await response.json()
        } else {
            models.value = []
        }
    } catch (error) {
        console.error('Error loading models:', error)
        models.value = []
    } finally {
        loadingModels.value = false
    }
}

function onPhotoChange(file) {
    photoFile.value = file
    // FilePond handles preview automatically, no need for manual preview
}

// Map-related functions


function onMapClick(event) {
    form.lat = event.latlng.lat
    form.lng = event.latlng.lng
    
    // Reverse geocoding to get location name
    reverseGeocode(form.lat, form.lng)
}

async function reverseGeocode(lat, lng) {
    try {
        const response = await fetch(
            `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`
        )
        
        if (response.ok) {
            const data = await response.json()
            form.location_name = data.display_name.split(',').slice(0, 3).join(', ')
        }
    } catch (error) {
        console.error('Reverse geocoding error:', error)
        form.location_name = `${lat.toFixed(6)}, ${lng.toFixed(6)}`
    }
}

function submit() {
    submitting.value = true
    
    const formData = new FormData()
    
    // Add all form fields
    Object.entries(form).forEach(([key, value]) => {
        if (key === 'is_available') {
            formData.append(key, value ? '1' : '0')
        } else if (value !== null && value !== '') {
            formData.append(key, value)
        }
    })
    
    // Add photo if selected
    if (photoFile.value) {
        formData.append('main_photo', photoFile.value)
    }
    
    // Add pricing tiers
    formData.append('pricing_tier_ids', JSON.stringify(selectedTierIds.value))
    
    router.post('/owner/vehicles', formData, {
        onFinish: () => {
            submitting.value = false
        },
        onError: (errors) => {
            console.error('Validation errors:', errors)
        }
    })
}
</script>
<style>
@import "leaflet/dist/leaflet.css";
</style>
