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
  :zoom="16"
  :center="mapCenter"
  :useGlobalLeaflet="true"
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
import { computed, ref } from 'vue'

import '@/plugins/leaflet-icon-fix'

const props = defineProps({
  vehicles: {
    type: Array,
    default: () => []
  }
})

const mapRef = ref(null)

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
})
</script>

<style>
@import "leaflet/dist/leaflet.css";
</style>
