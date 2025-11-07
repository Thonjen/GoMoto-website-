<template>
  <div>
    <h1 class="text-2xl font-bold mb-4">Edit Vehicle</h1>
    <form @submit.prevent="submit" enctype="multipart/form-data">
      <div class="mb-2">
        <label>License Plate</label>
        <input v-model="form.license_plate" class="border p-2 w-full" required />
      </div>
      <div class="mb-2">
        <label>Brand</label>
        <select v-model="form.brand_id" class="border p-2 w-full" required>
          <option v-for="b in brands" :key="b.id" :value="b.id">{{ b.name }}</option>
        </select>
      </div>
      <div class="mb-2">
        <label>Type</label>
        <select v-model="form.type_id" class="border p-2 w-full" required>
          <option v-for="t in types" :key="t.id" :value="t.id">{{ t.category }}</option>
        </select>
      </div>
      <div class="mb-2">
        <label>Fuel Type</label>
        <select v-model="form.fuel_type_id" class="border p-2 w-full" required>
          <option v-for="f in fuelTypes" :key="f.id" :value="f.id">{{ f.name }}</option>
        </select>
      </div>
      <div class="mb-2">
        <label>Year</label>
        <input v-model="form.year" type="number" class="border p-2 w-full" required />
      </div>
      <div class="mb-2">
        <label>Color</label>
        <input v-model="form.color" class="border p-2 w-full" required />
      </div>
      <div class="mb-2">
        <label>Description</label>
        <textarea v-model="form.description" class="border p-2 w-full"></textarea>
      </div>
      <div class="mb-2">
        <label>
          <input type="checkbox" v-model="form.is_available" />
          Available
        </label>
      </div>
      <div class="mb-2">
        <label>Main Photo</label>
        <input type="file" @change="onMainPhotoChange" accept="image/*" class="border p-2 w-full" />
        <div v-if="mainPhotoPreview || form.main_photo_url" class="mt-2">
          <img :src="mainPhotoPreview || form.main_photo_url" class="w-32 h-24 object-cover rounded" />
        </div>
      </div>
      <div class="mb-2">
        <label>Location</label>
        <!-- Autocomplete input -->
        <input
          v-model="search"
          @input="onSearch"
          type="text"
          placeholder="Search location..."
          class="border p-2 w-full mb-2"
          autocomplete="off"
        />
        <ul v-if="suggestions.length" class="border bg-white max-h-40 overflow-y-auto mb-2">
          <li
            v-for="(s, i) in suggestions"
            :key="i"
            @click="selectSuggestion(s)"
            class="p-2 hover:bg-gray-100 cursor-pointer"
          >
            {{ s.properties.name }}<span v-if="s.properties.city">, {{ s.properties.city }}</span>
            <span v-if="s.properties.country">, {{ s.properties.country }}</span>
          </li>
        </ul>
        <div style="height: 300px;">
          <l-map
            style="height: 100%;"
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
            <l-geo-json :geojson="surigaoGeoJson" :options-style="geoJsonStyle" />
          </l-map>
        </div>

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
      <div class="mt-8">
        <h2 class="text-xl font-bold mb-2">Pricing</h2>
        <div>
          <label class="block font-medium mb-1">Select Pricing Tiers:</label>
          <select v-model="selectedTierIds" multiple class="border p-2 rounded w-full max-w-lg">
            <option v-for="tier in pricingTiers" :key="tier.id" :value="tier.id">
              {{ tier.duration_from }} {{ tier.duration_unit }} - ₱{{ tier.price }}
            </option>
          </select>
          <div class="text-xs text-gray-500 mt-1">
            Manage your pricing tiers <a href="/owner/pricing-tiers" class="text-blue-600 underline">here</a>.
          </div>
          <!-- Show currently associated pricing tiers -->
          <div v-if="selectedTierIds.length" class="mt-2">
            <span class="font-semibold text-sm">Currently Associated Tiers:</span>
            <ul class="list-disc ml-6 text-sm">
              <li v-for="tier in pricingTiers.filter(t => selectedTierIds.includes(t.id))" :key="tier.id">
                {{ tier.duration_from }} {{ tier.duration_unit }} - ₱{{ tier.price }}
              </li>
            </ul>
          </div>
        </div>
      </div>
      <button class="bg-blue-600 text-white px-4 py-2 rounded">Update</button>
    </form>
    <Link :href="`/owner/vehicles/${vehicle.id}`" class="ml-2 text-blue-600">Back</Link>
  </div>
</template>

<script setup>
import { computed, onMounted, reactive, onUnmounted, ref, watch } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import { throttle } from 'lodash-es'; // or debounce
import { LMap, LTileLayer, LMarker, LGeoJson } from "@vue-leaflet/vue-leaflet";
import L from '@/plugins/leaflet-icon-fix';

const props = defineProps({ vehicle: Object, brands: Array, types: Array, fuelTypes: Array });

const form = reactive({
  license_plate: props.vehicle.license_plate,
  make_id: props.vehicle.make_id,
  model_id: props.vehicle.model_id,
  type_id: props.vehicle.type_id,
  fuel_type_id: props.vehicle.fuel_type_id,
  transmission_id: props.vehicle.transmission_id,
  year: props.vehicle.year,
  color: props.vehicle.color,
  description: props.vehicle.description,
  is_available: props.vehicle.is_available,
  main_photo_url: props.vehicle.main_photo_url,
  lat: props.vehicle.lat ?? 9.785903415098108,
  lng: props.vehicle.lng ?? 125.49062330098809,
  location_name: props.vehicle.location_name,
});
const mainPhoto = ref(null);
const mainPhotoPreview = ref(null);

// Autocomplete state
const search = ref('');
const suggestions = ref([]);



const getLocationName = throttle(async () => {
  if (form.lat && form.lng && !form.location_name) {
    try {
      const res = await fetch(
        `https://nominatim.openstreetmap.org/reverse?lat=${form.lat}&lon=${form.lng}&format=json`
      );
      const data = await res.json();
      form.location_name = data.display_name || '';
    } catch (err) {
      form.location_name = '';
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
  // Surigao del Norte bounding box: [minLon, minLat, maxLon, maxLat]
  // Tighter bounds for Surigao del Norte
  searchTimeout = setTimeout(async () => {
    const bbox = '125.25,9.77,125.50,9.80';
    const url = `https://photon.komoot.io/api/?q=${encodeURIComponent(search.value)}&limit=5&bbox=${bbox}`;
    const res = await fetch(url);
    const data = await res.json();
    suggestions.value = data.features || [];
  }, 300);
}
function selectSuggestion(s) {
  form.lat = s.geometry.coordinates[1];
  form.lng = s.geometry.coordinates[0];
  search.value = s.properties.name +
    (s.properties.city ? ', ' + s.properties.city : '') +
    (s.properties.country ? ', ' + s.properties.country : '');
  suggestions.value = [];
}

function onMainPhotoChange(e) {
  const file = e.target.files[0];
  mainPhoto.value = file;
  if (file) {
    mainPhotoPreview.value = URL.createObjectURL(file);
  }
}

function onMapClick(e) {
  form.lat = e.latlng.lat;
  form.lng = e.latlng.lng;
}

const pricingTiers = ref([]);
const selectedTierIds = ref((props.vehicle.pricing_tiers || []).map(t => t.id));

// Fetch reusable pricing tiers for this owner
onMounted(async () => {
    const res = await fetch("/owner/pricing-tiers/list");
    if (res.ok) {
        const data = await res.json();
        pricingTiers.value = data.pricingTiers || [];
    }
});

async function submit() {
  await getLocationName();
  const data = new FormData();
  Object.entries(form).forEach(([k, v]) => {
    data.append(k, k === "is_available" ? (v ? 1 : 0) : v);
  });
  if (mainPhoto.value) data.append("main_photo", mainPhoto.value);
  data.append('_method', 'PUT');
  data.append('pricing_tier_ids', JSON.stringify(selectedTierIds.value));

  router.post(`/owner/vehicles/${props.vehicle.id}`, data);
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
  fill: false
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
  }
);

</script>


<style>
@import "leaflet/dist/leaflet.css";
</style>
