<template>
  <div class="mb-8">
    <div class="glass-card shadow-glow border border-white/20 overflow-hidden">
      <div class="p-4 border-b border-white/20">
        <h3 class="text-lg font-semibold text-white">Vehicle Locations in Surigao del Norte</h3>
        <p class="text-sm text-white/70">Click on markers to view vehicle details</p>
      </div>
      <div class="h-96 relative">


<l-map
  ref="mapRef"
  style="height: 100%"
  :bounds="vehicleBounds"
  :min-zoom="15"
  :max-zoom="18"
  :zoom="16"
  :center="mapCenter"
  @moveend="keepInsidePolygon"
>
          <l-tile-layer url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png" />
          <l-geo-json :geojson="surigaoGeoJson" :options-style="geoJsonStyle" />
          
          <!-- Vehicle Markers -->
          <template v-for="vehicle in displayVehicles" :key="`marker-${vehicle.id}`">
            <l-marker :lat-lng="[parseFloat(vehicle.lat), parseFloat(vehicle.lng)]">
              <l-popup>
                <div class="w-64 p-2 bg-black/80 backdrop-blur-sm rounded-lg text-white border border-white/20">
                  <div class="flex items-start gap-3">
                    <img
                      v-if="vehicle.main_photo_url"
                      :src="vehicle.main_photo_url"
                      :alt="`${vehicle.make?.name} ${vehicle.model?.name}`"
                      class="w-16 h-12 object-cover rounded"
                    />
                    <div v-else class="w-16 h-12 bg-white/10 rounded flex items-center justify-center">
                      <span class="text-white/50 text-xs">No image</span>
                    </div>
                    <div class="flex-1">
                      <h4 class="font-semibold text-white">
                        {{ vehicle.make?.name }} {{ vehicle.model?.name }}
                        <span v-if="vehicle.year" class="text-white/70 font-normal">({{ vehicle.year }})</span>
                      </h4>
                      <p class="text-sm text-white/70">{{ vehicle.type?.sub_type }}</p>
                      <p v-if="vehicle.owner" class="text-xs text-primary-300 font-medium">
                        Owner: {{ vehicle.owner.name || (vehicle.owner.first_name + ' ' + vehicle.owner.last_name).trim() || 'Unknown Name' }}
                      </p>
                      <div class="flex items-center gap-2 mt-1 text-xs text-white/60">
                        <span>{{ vehicle.fuelType?.name || vehicle.fuel_type?.name || 'N/A' }}</span>
                        <span>•</span>
                        <span>{{ vehicle.transmission?.name || 'Manual' }}</span>
                      </div>
                      <div v-if="vehicle.pricing_tiers?.length" class="mt-2">
                        <span class="text-green-400 font-semibold text-sm">
                          From ₱{{ Math.min(...vehicle.pricing_tiers.map(t => parseFloat(t.price))) }}
                        </span>
                      </div>
                      <button
                        @click="emit('viewDetail', vehicle.id)"
                        class="mt-2 btn-primary text-xs px-3 py-1 font-medium"
                      >
                        View Details
                      </button>
                    </div>
                  </div>
                </div>
              </l-popup>
            </l-marker>
          </template>
        </l-map>
      </div>
    </div>
  </div>
</template>

<script setup>
import { LMap, LTileLayer, LMarker, LPopup, LGeoJson } from "@vue-leaflet/vue-leaflet"
import { computed, watch, ref } from 'vue'
import { latLng, polygon, latLngBounds  } from "leaflet"

import '@/plugins/leaflet-icon-fix'

const props = defineProps({
  vehicles: {
    type: Array,
    default: () => []
  }
})

const mapRef = ref(null)

const keepInsidePolygon = () => {
  const map = mapRef.value.leafletObject
  const center = map.getCenter()
  const poly = polygon(polygonCoords.map(c => [c[1], c[0]])) // lat,lng order!

  if (!poly.getBounds().contains(center)) {
    map.panInsideBounds(poly.getBounds(), { animate: true })
  }
}

const vehicleBounds = computed(() => {
  if (!validVehicles.value.length) return bounds
  return latLngBounds(validVehicles.value.map(v => [v.lat, v.lng]))
})
const emit = defineEmits(['viewDetail'])



// Computed property to filter vehicles with valid coordinates
const validVehicles = computed(() => {
  if (!props.vehicles || !Array.isArray(props.vehicles)) {
    return []
  }
  return props.vehicles.filter(vehicle => 
    vehicle && 
    vehicle.lat && 
    vehicle.lng && 
    !isNaN(parseFloat(vehicle.lat)) && 
    !isNaN(parseFloat(vehicle.lng))
  )
})



const displayVehicles = computed(() => validVehicles.value)


// Map configuration
const mapCenter = [9.786048643234757, 125.49062330098809]

// Extract bounds from the polygon coordinates
const polygonCoords = [
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
]

// Calculate bounds from polygon coordinates
const lngs = polygonCoords.map(coord => coord[0])
const lats = polygonCoords.map(coord => coord[1])
const minLng = Math.min(...lngs)
const maxLng = Math.max(...lngs)
const minLat = Math.min(...lats)
const maxLat = Math.max(...lats)

// Add a small buffer to ensure the polygon fits within bounds
const buffer = 0.0002
const bounds = [
  [minLat - buffer, minLng - buffer], // Southwest corner
  [maxLat + buffer, maxLng + buffer]  // Northeast corner
]

const surigaoGeoJson = {
  type: "FeatureCollection",
  features: [
    {
      type: "Feature",
      properties: {},
      geometry: {
        type: "Polygon",
        coordinates: [polygonCoords],
      },
    },
  ],
}

const geoJsonStyle = () => ({
  color: "#1976d2",
  weight: 2,
  fill: false,
})
</script>

<style>
@import "leaflet/dist/leaflet.css";
</style>
