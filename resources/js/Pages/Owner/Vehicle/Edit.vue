<template>
  <OwnerLayout>
    <div class="bg-white p-6 rounded-lg shadow-md max-w-4xl mx-auto">
      <h1 class="text-3xl font-bold text-gray-800 mb-6">Edit Vehicle</h1>
      
      <form @submit.prevent="submit" class="space-y-6">
        <!-- Vehicle Type Display (Read-only) -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Vehicle Category</label>
          <div class="w-full p-3 bg-gray-100 border border-gray-300 rounded-md text-gray-700 font-medium">
            {{ vehicle.type?.category === 'car' ? '🚗 Car' : '🏍️ Motorcycle' }}
          </div>
          <p class="text-xs text-gray-500 mt-1">Vehicle category cannot be changed after creation</p>
        </div>
        
        <!-- Make and Model Row -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Make *</label>
            <select
              v-model="form.make_id"
              @change="onMakeChange"
              class="w-full p-3 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              required
            >
              <option value="">Select Make</option>
              <option v-for="make in makes" :key="make.id" :value="make.id">
                {{ make.name }}
              </option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Model *</label>
            <select
              v-model="form.model_id"
              class="w-full p-3 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              :disabled="!form.make_id || loadingModels"
              required
            >
              <option value="">{{ loadingModels ? 'Loading...' : 'Select Model' }}</option>
              <option v-for="model in models" :key="model.id" :value="model.id">
                {{ model.name }}
              </option>
            </select>
          </div>
        </div>

        <!-- Year and Transmission -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Year *</label>
            <input
              v-model="form.year"
              type="number"
              min="1900"
              :max="new Date().getFullYear() + 1"
              class="w-full p-3 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              required
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Transmission *</label>
            <select
              v-model="form.transmission_id"
              class="w-full p-3 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              required
            >
              <option value="">Select Transmission</option>
              <option v-for="trans in transmissions" :key="trans.id" :value="trans.id">
                {{ trans.name }}
              </option>
            </select>
          </div>
        </div>

        <!-- Fuel Type and Sub-Type -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Fuel Type *</label>
            <select
              v-model="form.fuel_type_id"
              class="w-full p-3 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              required
            >
              <option value="">Select Fuel Type</option>
              <option v-for="fuel in fuelTypes" :key="fuel.id" :value="fuel.id">
                {{ fuel.name }}
              </option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Vehicle Sub-Type *</label>
            <select
              v-model="form.type_id"
              class="w-full p-3 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              required
            >
              <option value="">Select Sub-Type</option>
              <option v-for="type in types" :key="type.id" :value="type.id">
                {{ type.sub_type }}
              </option>
            </select>
          </div>
        </div>

        <!-- License Plate and Color Row -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">License Plate</label>
            <input
              v-model="form.license_plate"
              type="text"
              class="w-full p-3 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="Enter license plate"
            />
            <p class="text-xs text-gray-500 mt-1">Leave blank if not assigned yet</p>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Color *</label>
            <input
              v-model="form.color"
              type="text"
              class="w-full p-3 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="Enter vehicle color"
              required
            />
          </div>
        </div>

        <!-- Description -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
          <textarea
            v-model="form.description"
            rows="3"
            class="w-full p-3 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
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
          <label for="is_available" class="ml-2 block text-sm text-gray-700">
            Vehicle is available for rental
          </label>
        </div>

        <!-- Photo Upload -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Main Photo</label>
          
          <div v-if="form.main_photo_url" class="mb-4">
            <p class="text-sm text-gray-600 mb-2">Current Photo</p>
            <img
              :src="form.main_photo_url"
              alt="Current main photo"
              class="w-48 h-32 object-cover rounded border"
            />
          </div>
          
          <FilePondUploader @file-added="onMainPhotoChange" />
        </div>

        <!-- Location Section -->
        <div class="border rounded-lg p-4 bg-gray-50 shadow-sm space-y-4">
          <h3 class="text-lg font-semibold">Vehicle Location</h3>
          
          <!-- Location Search -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Search Location</label>
            <input
              v-model="search"
              @input="onSearch"
              type="text"
              placeholder="Search for a location in Surigao del Norte"
              class="w-full p-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
            
            <div v-if="suggestions.length > 0" class="mt-2 bg-white border rounded-md shadow-lg max-h-40 overflow-y-auto">
              <div
                v-for="suggestion in suggestions"
                :key="suggestion.properties.osm_id"
                @click="selectSuggestion(suggestion)"
                class="p-2 hover:bg-gray-100 cursor-pointer border-b last:border-b-0"
              >
                <div class="text-sm font-medium">{{ suggestion.properties.name }}</div>
                <div class="text-xs text-gray-600">
                  {{ suggestion.properties.city }}, {{ suggestion.properties.country }}
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
              :max-bounds="bounds"
              :min-zoom="15"
              :max-zoom="18"
              @click="onMapClick"
            >
              <l-tile-layer url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png" />
              <l-marker :lat-lng="[form.lat, form.lng]" />
              <l-geo-json :geojson="surigaoGeoJson" :options-style="geoJsonStyle" />
            </l-map>
          </div>

          <!-- Info Box -->
          <div class="space-y-2 text-sm bg-white p-4 rounded shadow-inner border">
            <div>
              <span class="font-medium text-gray-700">Coordinates:</span>
              <span class="text-gray-600">Lat {{ form.lat }}, Lng {{ form.lng }}</span>
            </div>
            <div>
              <span class="font-medium text-gray-700">Location:</span>
              <span class="text-gray-600">{{ form.location_name || '—' }}</span>
            </div>
            <div class="flex items-center gap-2">
              <span class="font-medium text-gray-700">Map Link:</span>
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
            <h3 class="text-lg font-semibold">Pricing</h3>
            <button
              type="button"
              @click="loadPricingTiers"
              :disabled="loadingPricingTiers"
              class="flex items-center px-3 py-1 text-sm text-blue-600 hover:text-blue-800 border border-blue-300 hover:border-blue-400 rounded-md transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
            >
<svg
  :class="['w-4 h-4 mr-1', { 'animate-spin': loadingPricingTiers }]"
  fill="none"
  stroke="currentColor"
  viewBox="0 0 24 24"
>
  <circle class="opacity-25" cx="12" cy="12" r="10" stroke-width="4"></circle>
  <path
    class="opacity-75"
    fill="currentColor"
    d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"
  ></path>
</svg>

              {{ loadingPricingTiers ? 'Refreshing...' : 'Refresh' }}
            </button>
          </div>
          <div v-if="pricingTiers.length > 0">
            <p class="text-sm text-gray-600 mb-3">Select pricing tiers for this vehicle:</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
              <label
                v-for="tier in pricingTiers"
                :key="tier.id"
                class="flex items-center p-3 border rounded-md cursor-pointer hover:bg-gray-50"
              >
                <input
                  type="checkbox"
                  :value="tier.id"
                  v-model="selectedTierIds"
                  class="mr-3 h-4 w-4 text-blue-600"
                />
                <div>
                  <div class="font-medium">{{ tier.duration_from }} {{ tier.duration_unit }}</div>
                  <div class="text-sm text-gray-600">₱{{ tier.price }}</div>
                </div>
              </label>
            </div>
          </div>
          <div v-else-if="loadingPricingTiers" class="text-sm text-gray-500">
            Loading pricing tiers...
          </div>
          <div v-else class="text-sm text-gray-500">
            No pricing tiers available.
          </div>
          <p class="text-sm text-gray-500 mt-2">
            You can manage pricing tiers in the 
            <Link href="/owner/pricing-tiers" class="text-blue-600 underline">pricing management</Link> section.
          </p>
        </div>

        <!-- Submit Buttons -->
        <div class="border-t pt-6 flex space-x-4">
          <button
            type="submit"
            :disabled="submitting"
            class="bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white font-medium py-3 px-6 rounded-md transition-colors"
          >
            {{ submitting ? 'Updating Vehicle...' : 'Update Vehicle' }}
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
</template>

<script setup>
import { computed, onMounted, reactive, ref, watch } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import { throttle } from 'lodash-es'
import OwnerLayout from '@/Layouts/OwnerLayout.vue'
import { LMap, LTileLayer, LMarker, LGeoJson } from "@vue-leaflet/vue-leaflet"
import L from '@/plugins/leaflet-icon-fix'
import FilePondUploader from '@/Components/FilePondUploader.vue'

const props = defineProps({ 
  vehicle: Object, 
  makes: Array, 
  models: Array,
  types: Array, 
  fuelTypes: Array,
  transmissions: Array
})

const form = reactive({
  make_id: props.vehicle.make_id,
  model_id: props.vehicle.model_id,
  type_id: props.vehicle.type_id,
  fuel_type_id: props.vehicle.fuel_type_id,
  transmission_id: props.vehicle.transmission_id,
  license_plate: props.vehicle.license_plate,
  year: props.vehicle.year,
  color: props.vehicle.color,
  description: props.vehicle.description,
  is_available: props.vehicle.is_available,
  main_photo_url: props.vehicle.main_photo_url,
  lat: props.vehicle.lat ?? 9.785903415098108,
  lng: props.vehicle.lng ?? 125.49062330098809,
  location_name: props.vehicle.location_name,
})

const makes = ref(props.makes || [])
const models = ref(props.models || [])
const pricingTiers = ref([])
const selectedTierIds = ref((props.vehicle.pricing_tiers || []).map(t => t.id))
const loadingModels = ref(false)
const submitting = ref(false)
const loadingPricingTiers = ref(false)
const mainPhoto = ref(null)
const mainPhotoPreview = ref(null)

// Autocomplete state
const search = ref('')
const suggestions = ref([])

const getLocationName = throttle(async () => {
  if (form.lat && form.lng && !form.location_name) {
    try {
      const response = await fetch(
        `https://nominatim.openstreetmap.org/reverse?format=json&lat=${form.lat}&lon=${form.lng}`
      )
      const data = await response.json()
      form.location_name = data.display_name || ''
    } catch (err) {
      console.error('Failed to get location name:', err)
    }
  }
}, 1000)

let searchTimeout = null
function onSearch() {
  clearTimeout(searchTimeout)
  if (!search.value) {
    suggestions.value = []
    return
  }
  
  searchTimeout = setTimeout(async () => {
    const bbox = '125.25,9.77,125.50,9.80'
    const url = `https://photon.komoot.io/api/?q=${encodeURIComponent(search.value)}&limit=5&bbox=${bbox}`
    try {
      const res = await fetch(url)
      const data = await res.json()
      suggestions.value = data.features || []
    } catch (err) {
      console.error('Search failed:', err)
    }
  }, 300)
}

function selectSuggestion(s) {
  form.lat = s.geometry.coordinates[1]
  form.lng = s.geometry.coordinates[0]
  search.value = s.properties.name +
    (s.properties.city ? ', ' + s.properties.city : '') +
    (s.properties.country ? ', ' + s.properties.country : '')
  suggestions.value = []
}

function onMainPhotoChange(file) {
  mainPhoto.value = file
  // FilePond handles preview automatically, no need for manual preview
}

function onMapClick(e) {
  form.lat = e.latlng.lat
  form.lng = e.latlng.lng
}

async function onMakeChange() {
  if (!form.make_id) {
    form.model_id = ''
    models.value = []
    return
  }

  loadingModels.value = true
  try {
    const response = await fetch(`/api/vehicle-data/models/${form.make_id}`)
    const data = await response.json()
    models.value = data || []

    // ✅ Only reset if the current model_id isn't in the new models
    if (!models.value.some(m => m.id === form.model_id)) {
      form.model_id = ''
    }
  } catch (err) {
    console.error('Failed to load models:', err)
    models.value = []
    form.model_id = ''
  } finally {
    loadingModels.value = false
  }
}

// Fetch reusable pricing tiers for this owner
async function loadPricingTiers() {
  loadingPricingTiers.value = true
  try {
    const res = await fetch("/owner/pricing-tiers/list")
    if (res.ok) {
      const data = await res.json()
      pricingTiers.value = data.pricingTiers || []
    }
  } catch (err) {
    console.error('Failed to load pricing tiers:', err)
  } finally {
    loadingPricingTiers.value = false
  }
}

onMounted(async () => {
  await loadPricingTiers()
  
  // Load models if make is selected
  if (form.make_id) {
    await onMakeChange()
  }
})

async function submit() {
  submitting.value = true
  
  await getLocationName()
  
  const data = new FormData()
  
  // Add all form fields explicitly
  data.append('make_id', form.make_id)
  data.append('model_id', form.model_id)
  data.append('type_id', form.type_id)
  data.append('fuel_type_id', form.fuel_type_id)
  data.append('transmission_id', form.transmission_id)
  data.append('license_plate', form.license_plate || '')
  data.append('year', form.year)
  data.append('color', form.color)
  data.append('description', form.description || '')
  data.append('is_available', form.is_available ? '1' : '0')
  data.append('lat', form.lat)
  data.append('lng', form.lng)
  data.append('location_name', form.location_name || '')
  
  if (mainPhoto.value) {
    data.append("main_photo", mainPhoto.value)
  }
  
  data.append('_method', 'PUT')
  data.append('pricing_tier_ids', JSON.stringify(selectedTierIds.value))

  router.post(`/owner/vehicles/${props.vehicle.id}`, data, {
    onFinish: () => {
      submitting.value = false
    },
    onSuccess: () => {
      // Optionally redirect or show success message
    },
    onError: (errors) => {
      console.error('Update failed:', errors)
    }
  })
}

const surigaoGeoJson = {
  "type": "FeatureCollection",
  "features": [
    {
      "type": "Feature",
      "properties": {},
      "geometry": {
        "type": "Polygon",
        "coordinates": [
          [
            [125.48545720374489, 9.801207813887503],
            [125.48394937158277, 9.801134816678099],
            [125.4824560626428, 9.800916528148765],
            [125.48099166020462, 9.800555050821249],
            [125.47957026901334, 9.800053866380772],
            [125.47820557937605, 9.799417802127778],
            [125.48545720374489, 9.801207813887503],
          ],
        ],
      },
    },
  ]
}

const geoJsonStyle = () => ({
  color: "#1976d2",
  weight: 2,
  fill: false
})

const bounds = [
  [9.770890164325817, 125.47007455081837],
  [9.801207813887503, 125.50083985667139]
]

watch(
  () => [form.lat, form.lng],
  () => {
    getLocationName()
  }
)
</script>

<style>
@import "leaflet/dist/leaflet.css";
</style>
