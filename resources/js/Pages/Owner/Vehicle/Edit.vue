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
        <div class="text-xs mt-1">Lat: {{ form.lat }}, Lng: {{ form.lng }}</div>
      </div>
      <button class="bg-blue-600 text-white px-4 py-2 rounded">Update</button>
    </form>
    <Link :href="`/owner/vehicles/${vehicle.id}`" class="ml-2 text-blue-600">Back</Link>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import { LMap, LTileLayer, LMarker, LGeoJson } from "@vue-leaflet/vue-leaflet";
import L from '@/plugins/leaflet-icon-fix';

const props = defineProps({ vehicle: Object, brands: Array, types: Array, fuelTypes: Array });

const form = reactive({
  license_plate: props.vehicle.license_plate,
  brand_id: props.vehicle.brand_id,
  type_id: props.vehicle.type_id,
  fuel_type_id: props.vehicle.fuel_type_id,
  year: props.vehicle.year,
  color: props.vehicle.color,
  description: props.vehicle.description,
  is_available: props.vehicle.is_available,
  main_photo_url: props.vehicle.main_photo_url,
  lat: props.vehicle.lat ?? 9.785903415098108,
  lng: props.vehicle.lng ?? 125.49062330098809,
});
const mainPhoto = ref(null);
const mainPhotoPreview = ref(null);

// Autocomplete state
const search = ref('');
const suggestions = ref([]);

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

function submit() {
  const data = new FormData();
  Object.entries(form).forEach(([k, v]) => {
    if (k === 'is_available') {
      data.append('is_available', v ? 1 : 0);
    } else {
      data.append(k, v);
    }
  });
  if (mainPhoto.value) data.append('main_photo', mainPhoto.value);
  router.post(`/owner/vehicles/${props.vehicle.id}?_method=PUT`, data);
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
            [125.48545720374489,9.801207813887503],
            [125.48394937158277,9.801134816678099],
            [125.4824560626428,9.800916528148765],
            [125.48099166020462,9.800555050821249],
            [125.47957026901334,9.800053866380772],
            [125.47820557937605,9.799417802127778],
            [125.47691073525756,9.79865298446448],
            [125.4756982076485,9.797766779865192],
            [125.47457967442702,9.796767723899952],
            [125.47356590787237,9.79566543899633],
            [125.47266667091462,9.79447054173279],
            [125.47189062311993,9.793194540557817],
            [125.47124523731648,9.791849724921354],
            [125.47073672766314,9.790449046887577],
            [125.47036998985222,9.789005996370452],
            [125.47014855402033,9.787534471194746],
            [125.47007455081837,9.786048643234757],
            [125.47014869096546,9.784562821920598],
            [125.47037025847982,9.78309131642699],
            [125.47073711764999,9.781648297871861],
            [125.47124573367556,9.78024766285169],
            [125.47189120677649,9.77890289962746],
            [125.47266731943903,9.77762695824917],
            [125.47356659634224,9.776432125868816],
            [125.47458037638481,9.775329908441458],
            [125.47569889611839,9.774330919952476],
            [125.47691138378202,9.773444780236375],
            [125.47820616303265,9.772680022369768],
            [125.47957076537246,9.772044010529008],
            [125.48099205019147,9.771542869102143],
            [125.48245633127043,9.771181423736623],
            [125.48394950852794,9.77096315488939],
            [125.48545720374489,9.770890164325817],
            [125.48696489896184,9.77096315488939],
            [125.48845807621933,9.771181423736623],
            [125.48992235729831,9.771542869102143],
            [125.49134364211733,9.772044010529008],
            [125.49270824445713,9.772680022369768],
            [125.49400302370776,9.773444780236375],
            [125.4952155113714,9.774330919952476],
            [125.49633403110496,9.775329908441458],
            [125.49734781114755,9.776432125868816],
            [125.49824708805076,9.77762695824917],
            [125.49902320071328,9.77890289962746],
            [125.4996686738142,9.78024766285169],
            [125.5001772898398,9.781648297871861],
            [125.50054414900994,9.78309131642699],
            [125.50076571652433,9.784562821920598],
            [125.50083985667139,9.786048643234757],
            [125.50076585346946,9.787534471194746],
            [125.50054441763756,9.789005996370452],
            [125.50017767982663,9.790449046887577],
            [125.4996691701733,9.791849724921354],
            [125.49902378436985,9.793194540557817],
            [125.49824773657517,9.79447054173279],
            [125.4973484996174,9.79566543899633],
            [125.49633473306275,9.796767723899952],
            [125.49521619984127,9.797766779865192],
            [125.49400367223222,9.79865298446448],
            [125.49270882811372,9.799417802127778],
            [125.49134413847645,9.800053866380772],
            [125.48992274728516,9.800555050821249],
            [125.48845834484696,9.800916528148765],
            [125.48696503590699,9.801134816678099],
            [125.48545720374489,9.801207813887503]
          ]
        ]
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
  [9.801207813887503, 125.50083985667139]
];
</script>

<style>
@import "leaflet/dist/leaflet.css";
</style>
