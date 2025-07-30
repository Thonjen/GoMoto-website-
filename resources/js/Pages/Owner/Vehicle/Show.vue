<template>

  
  <div>
    <h1 class="text-2xl font-bold mb-4">Vehicle Details</h1>
    <div class="mb-4">
      <div><b>Plate:</b> {{ vehicle.license_plate }}</div>
      <div><b>Brand:</b> {{ vehicle.brand?.name }}</div>
      <div><b>Type:</b> {{ vehicle.type?.category }}</div>
      <div><b>Fuel:</b> {{ vehicle.fuel_type?.name || vehicle.fuelType?.name }}</div>
      <div><b>Year:</b> {{ vehicle.year }}</div>
      <div><b>Color:</b> {{ vehicle.color }}</div>
      <div><b>Description:</b> {{ vehicle.description }}</div>
      <div><b>Available:</b> <span :class="vehicle.is_available ? 'text-green-600' : 'text-red-600'">{{ vehicle.is_available ? 'Yes' : 'No' }}</span></div>
      <div v-if="vehicle.main_photo_url" class="mt-2">
        <b>Main Photo:</b>
        <img :src="vehicle.main_photo_url" class="w-48 h-36 object-cover rounded mt-1" />
      </div>
      <div v-if="vehicle.lat && vehicle.lng" class="mt-2">
        <b>Location:</b>
        <div style="height: 200px;">
          <l-map
            style="height: 100%;"
            :zoom="20"
            :center="[vehicle.lat, vehicle.lng]"
            :zoomControl="false"
            :scrollWheelZoom="false"
            :doubleClickZoom="false"
            :dragging="false"
            :maxBounds="bounds"
            :minZoom="15"
            :maxZoom="18"
          >
            <l-tile-layer
              url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
            />
            <l-marker :lat-lng="[vehicle.lat, vehicle.lng]" />
            <l-geo-json :geojson="surigaoGeoJson" :options-style="geoJsonStyle" />
          </l-map>
        </div>
        <div class="text-xs mt-1">Lat: {{ vehicle.lat }}, Lng: {{ vehicle.lng }}</div>
      </div>
    </div>
    <div class="mb-4">
      <h2 class="font-bold mb-2">Photos</h2>
      <div class="flex flex-wrap gap-4">
        <div v-for="photo in vehicle.photos" :key="photo.id" class="relative">
          <img :src="photo.url" class="w-32 h-24 object-cover rounded" />
          <button @click="deletePhoto(photo.id)" class="absolute top-0 right-0 bg-red-600 text-white px-2 py-1 rounded">X</button>
        </div>
      </div>
      <FilePondUploaderMultiple :vehicleId="vehicle.id" />
    </div>
    <Link :href="`/owner/vehicles/${vehicle.id}/edit`" class="bg-yellow-600 text-white px-4 py-2 rounded">Edit</Link>
    <Link href="/owner/vehicles" class="ml-2 text-blue-600">Back</Link>
  </div>
</template>

<script setup>
import { router, Link } from '@inertiajs/vue3';
import FilePondUploaderMultiple from '@/Components/FilePondUploaderMultiple.vue';
import { LMap, LTileLayer, LMarker, LGeoJson } from "@vue-leaflet/vue-leaflet";
import L from '@/plugins/leaflet-icon-fix';

const props = defineProps({ vehicle: Object });

function deletePhoto(photoId) {
  if (confirm('Delete this photo?')) {
    router.delete(`/owner/vehicles/photos/${photoId}`, {
    });
  }
}

// Surigao del Norte GeoJSON polygon (same as in Create/Edit)
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
