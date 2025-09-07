<template>
  <OwnerLayout>
    <div class="glass-card-dark p-6 rounded-lg shadow-glow max-w-4xl mx-auto">
      <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-white">Vehicle Details</h1>
        <div class="flex gap-3">
          <Link 
            :href="`/owner/vehicles/${vehicle.id}/edit`" 
            class="bg-blue-500/80 hover:bg-blue-500 text-white font-medium py-2 px-4 rounded-md transition-all duration-200 inline-flex items-center backdrop-blur-sm border border-blue-400/30"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
            </svg>
            Edit Vehicle
          </Link>
          <Link 
            :href="route('owner.vehicles.availability', vehicle.id)" 
            class="bg-green-500/80 hover:bg-green-500 text-white font-medium py-2 px-4 rounded-md transition-all duration-200 inline-flex items-center backdrop-blur-sm border border-green-400/30"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            Availability Calendar
          </Link>
          <Link 
            href="/owner/vehicles" 
            class="bg-white/20 hover:bg-white/30 text-white font-medium py-2 px-4 rounded-md transition-all duration-200 backdrop-blur-sm border border-white/30"
          >
            Back to List
          </Link>
        </div>
      </div>

      <div class="space-y-6">
        <!-- Vehicle Category and Basic Info -->
        <div class="glass-card p-6 border border-white/20">
          <h2 class="text-xl font-semibold mb-4 text-white">Basic Information</h2>
          
          <!-- Vehicle Category -->
          <div class="mb-4">
            <label class="block text-sm font-medium text-white mb-2">Vehicle Category</label>
            <div class="inline-flex items-center px-4 py-2 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
              {{ vehicle.type?.category === 'car' ? '🚗 Car' : '🏍️ Motorcycle' }}
            </div>
          </div>
          
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div>
              <label class="block text-sm font-medium text-white">Make</label>
              <p class="mt-1 text-sm text-white font-medium">{{ vehicle.make?.name || 'Unknown' }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-white">Model</label>
              <p class="mt-1 text-sm text-white font-medium">{{ vehicle.model?.name || 'Unknown' }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-white">Sub-Type</label>
              <p class="mt-1 text-sm text-white font-medium">{{ vehicle.type?.sub_type || 'N/A' }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-white">Year</label>
              <p class="mt-1 text-sm text-white font-medium">{{ vehicle.year }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-white">Color</label>
              <p class="mt-1 text-sm text-white font-medium">{{ vehicle.color }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-white">License Plate</label>
              <p class="mt-1 text-sm text-white font-medium">{{ vehicle.license_plate || 'Not Set' }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-white">Fuel Type</label>
              <p class="mt-1 text-sm text-white font-medium">{{ vehicle.fuelType?.name || vehicle.fuel_type?.name || 'Unknown' }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-white">Transmission</label>
              <p class="mt-1 text-sm text-white font-medium">{{ vehicle.transmission?.name || 'Unknown' }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-white">Availability</label>
              <div class="mt-1">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" :class="vehicle.is_available ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                  {{ vehicle.is_available ? 'Available' : 'Not Available' }}
                </span>
              </div>
            </div>
          </div>
          
          <div v-if="vehicle.description" class="mt-4">
            <label class="block text-sm font-medium text-white">Description</label>
            <p class="mt-1 text-sm text-white">{{ vehicle.description }}</p>
          </div>
        </div>

        <!-- Main Photo -->
        <div v-if="vehicle.main_photo_url" class="glass-card p-6 border border-white/20">
          <h3 class="text-lg font-semibold mb-4 text-white">Main Photo</h3>
          <div class="flex justify-center">
            <img :src="vehicle.main_photo_url" class="max-w-md w-full h-auto object-cover rounded-lg shadow-md border border-white/20" alt="Vehicle main photo" />
          </div>
        </div>

        <!-- Location Section -->
        <div v-if="vehicle.lat && vehicle.lng" class="glass-card p-6 border border-white/20">
          <h3 class="text-lg font-semibold mb-4 text-white">Vehicle Location</h3>
          
          <div class="mb-4">
            <label class="block text-sm font-medium text-white/90">Address</label>
            <p class="mt-1 text-sm text-white">{{ vehicle.location_name || 'Unknown location' }}</p>
            <a
              :href="`https://www.google.com/maps?q=${vehicle.lat},${vehicle.lng}`"
              target="_blank"
              rel="noopener noreferrer"
              class="inline-flex items-center mt-2 text-blue-400 hover:text-blue-300 text-sm font-medium transition-colors"
            >
              <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-1M6 12h8m-8 6h8m2-6h4m-4 0V6a2 2 0 00-2-2h-4"></path>
              </svg>
              View on Google Maps
            </a>
          </div>
          
          <!-- Map -->
          <div class="h-72 rounded-lg overflow-hidden border border-white/20 shadow-glow">
            <l-map
              style="height: 100%"
              :zoom="16"
              :center="[vehicle.lat, vehicle.lng]"
              :zoomControl="false"
              :scrollWheelZoom="false"
              :doubleClickZoom="false"
              :dragging="false"
              :maxBounds="bounds"
              :minZoom="15"
              :maxZoom="18"
            >
              <l-tile-layer url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png" />
              <l-marker :lat-lng="[vehicle.lat, vehicle.lng]" />
              <l-geo-json :geojson="surigaoGeoJson" :options-style="geoJsonStyle" />
            </l-map>
          </div>
          
          <div class="mt-2 text-xs text-white/60">
            Coordinates: {{ vehicle.lat }}, {{ vehicle.lng }}
          </div>
        </div>

        <!-- Additional Photos -->
        <div class="glass-card p-6 border border-white/20">
          <h3 class="text-lg font-semibold mb-4 text-white">Additional Photos</h3>
          
          <!-- Existing Photos Display -->
          <div v-if="vehicle.photos && vehicle.photos.length > 0" class="mb-6">
            <h4 class="text-md font-medium text-white mb-3">Current Photos ({{ vehicle.photos.length }})</h4>
            <div class="photos-grid grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
              <div 
                v-for="photo in vehicle.photos" 
                :key="photo.id" 
                class="photo-container relative aspect-square bg-gray-800/50 rounded-lg overflow-hidden border border-white/20 shadow-glow group cursor-pointer"
              >
                <img 
                  :src="photo.url" 
                  :alt="`Vehicle photo ${photo.id}`"
                  class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                  @click="openImagePreview(photo.url)"
                />
                <!-- Overlay with controls -->
                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition-all duration-300 flex items-center justify-center opacity-0 group-hover:opacity-100">
                  <div class="flex gap-2">
                    <button
                      @click.stop="openImagePreview(photo.url)"
                      class="bg-blue-500/80 hover:bg-blue-500 text-white p-2 rounded-full transition-colors backdrop-blur-sm"
                      title="View full size"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                      </svg>
                    </button>
                    <button
                      @click.stop="deletePhoto(photo.id)"
                      class="bg-red-500/80 hover:bg-red-500 text-white p-2 rounded-full transition-colors backdrop-blur-sm"
                      title="Delete photo"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                      </svg>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Upload Component Container -->
          <div class="upload-section overflow-hidden">
            <h4 class="text-md font-medium text-white mb-3">Upload New Photos</h4>
            <CustomImageUploader 
              :multiple="true" 
              :vehicle-id="vehicle.id" 
              :max-files="8"
              @files-uploaded="refreshPhotos" 
            />
          </div>
        </div>

        <!-- Pricing Tiers -->
        <div v-if="vehicle.pricing_tiers && vehicle.pricing_tiers.length > 0" class="glass-card p-6 border border-white/20">
          <h3 class="text-lg font-semibold mb-4 text-white">Pricing Tiers</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div v-for="tier in vehicle.pricing_tiers" :key="tier.id" class="bg-white/10 backdrop-blur-sm p-4 rounded-lg border border-white/20 shadow-glow">
              <div class="flex justify-between items-center">
                <div>
                  <span class="text-sm font-medium text-white/70">
                    {{ tier.duration_from }} {{ tier.duration_from === 1 ? tier.duration_unit.slice(0, -1) : tier.duration_unit }}
                  </span>
                </div>
                <div class="text-lg font-bold text-green-400">
                  ₱{{ tier.price }}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </OwnerLayout>
</template>

<script setup>
import { router, Link } from '@inertiajs/vue3';
import CustomImageUploader from '@/Components/CustomImageUploader.vue';
import { LMap, LTileLayer, LMarker, LGeoJson } from "@vue-leaflet/vue-leaflet";
import L from '@/plugins/leaflet-icon-fix';
import { throttle } from 'lodash-es'; // or debounce
import { computed, onMounted, reactive, onUnmounted, ref, watch } from 'vue';
import OwnerLayout from '@/Layouts/OwnerLayout.vue';

const props = defineProps({ vehicle: Object });

function deletePhoto(photoId) {
  if (confirm('Delete this photo?')) {
    router.delete(`/owner/vehicles/photos/${photoId}`, {
      onSuccess: () => {
        // The page will automatically refresh due to Inertia's default behavior
      }
    });
  }
}

function openImagePreview(imageUrl) {
  // Create and show a modal or lightbox for image preview
  const modal = document.createElement('div');
  modal.className = 'photo-modal fixed inset-0 z-[9999] flex items-center justify-center bg-black bg-opacity-75 p-4';
  modal.innerHTML = `
    <div class="relative max-w-5xl max-h-full">
      <img src="${imageUrl}" class="max-w-full max-h-full object-contain rounded-lg shadow-2xl" alt="Vehicle photo preview" />
      <button class="absolute top-4 right-4 text-white bg-black bg-opacity-50 rounded-full w-10 h-10 flex items-center justify-center hover:bg-opacity-75 transition-colors text-xl font-bold">
        ×
      </button>
    </div>
  `;
  
  modal.addEventListener('click', (e) => {
    if (e.target === modal || e.target.textContent === '×') {
      document.body.removeChild(modal);
      document.body.style.overflow = 'auto'; // Restore scroll
    }
  });
  
  // Add escape key listener
  const handleEscape = (e) => {
    if (e.key === 'Escape') {
      document.body.removeChild(modal);
      document.body.style.overflow = 'auto';
      document.removeEventListener('keydown', handleEscape);
    }
  };
  document.addEventListener('keydown', handleEscape);
  
  // Prevent body scroll when modal is open
  document.body.style.overflow = 'hidden';
  document.body.appendChild(modal);
}

function refreshPhotos() {
  // Refresh the page to show newly uploaded photos
  router.reload({ only: ['vehicle'] });
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

/* Custom styles for the photo section */
.aspect-square {
  aspect-ratio: 1 / 1;
}

/* Photo hover effects */
.photo-container {
  position: relative;
  overflow: hidden;
  border-radius: 0.5rem;
  transition: all 0.3s ease;
}

.photo-container:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
}

/* Ensure images fill container properly */
.photo-container img {
  transition: transform 0.3s ease;
}

.photo-container:hover img {
  transform: scale(1.05);
}

/* Modal styling */
.photo-modal {
  backdrop-filter: blur(8px);
  z-index: 9999 !important;
}

/* Ensure upload section is properly positioned */
.upload-section {
  border-top: 1px solid rgba(255, 255, 255, 0.2);
  padding-top: 1.5rem;
  margin-top: 1.5rem;
  position: relative;
  z-index: 1;
}

/* Responsive grid adjustments */
@media (max-width: 640px) {
  .photos-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 0.75rem;
  }
}

@media (min-width: 1024px) {
  .photos-grid {
    grid-template-columns: repeat(4, 1fr);
  }
}

@media (min-width: 1280px) {
  .photos-grid {
    grid-template-columns: repeat(5, 1fr);
  }
}
</style>
