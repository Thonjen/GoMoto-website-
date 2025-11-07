<template>
  <OwnerLayout>
    
      <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
          <h1 class="text-2xl md:text-3xl font-bold text-white">Vehicle Details</h1>
          
          <!-- Desktop Button Layout -->
          <div class="hidden sm:flex gap-2 lg:gap-3 flex-wrap">
            <Link 
              :href="`/owner/vehicles/${vehicle.id}/edit`" 
              class="bg-blue-500/80 hover:bg-blue-500 text-white font-medium py-2 px-3 lg:px-4 rounded-md transition-all duration-200 inline-flex items-center backdrop-blur-sm border border-blue-400/30 text-sm lg:text-base whitespace-nowrap"
            >
              <svg class="w-4 h-4 mr-1 lg:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
              </svg>
              <span class="hidden lg:inline">Edit Vehicle</span>
              <span class="lg:hidden">Edit</span>
            </Link>
            <Link 
              :href="route('owner.vehicles.availability', vehicle.id)" 
              class="bg-green-500/80 hover:bg-green-500 text-white font-medium py-2 px-3 lg:px-4 rounded-md transition-all duration-200 inline-flex items-center backdrop-blur-sm border border-green-400/30 text-sm lg:text-base whitespace-nowrap"
            >
              <svg class="w-4 h-4 mr-1 lg:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
              </svg>
              <span class="hidden lg:inline">Availability Calendar</span>
              <span class="lg:hidden">Calendar</span>
            </Link>
            <Link 
              href="/owner/vehicles" 
              class="bg-white/20 hover:bg-white/30 text-white font-medium py-2 px-3 lg:px-4 rounded-md transition-all duration-200 backdrop-blur-sm border border-white/30 text-sm lg:text-base whitespace-nowrap"
            >
              <span class="hidden lg:inline">Back to List</span>
              <span class="lg:hidden">Back</span>
            </Link>
          </div>
        </div>
        
        <!-- Mobile Button Layout -->
        <div class="sm:hidden mt-4 space-y-3">
          <div class="grid grid-cols-2 gap-2">
            <Link 
              :href="`/owner/vehicles/${vehicle.id}/edit`" 
              class="bg-blue-500/80 hover:bg-blue-500 text-white font-medium py-3 px-4 rounded-md transition-all duration-200 flex items-center justify-center backdrop-blur-sm border border-blue-400/30 text-sm"
            >
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
              </svg>
              Edit Vehicle
            </Link>
            <Link 
              href="/owner/vehicles" 
              class="bg-white/20 hover:bg-white/30 text-white font-medium py-3 px-4 rounded-md transition-all duration-200 flex items-center justify-center backdrop-blur-sm border border-white/30 text-sm"
            >
              Back to List
            </Link>
          </div>
          <Link 
            :href="route('owner.vehicles.availability', vehicle.id)" 
            class="bg-green-500/80 hover:bg-green-500 text-white font-medium py-3 px-4 rounded-md transition-all duration-200 flex items-center justify-center backdrop-blur-sm border border-green-400/30 text-sm w-full"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            Availability Calendar
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
          
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <div>
              <label class="block text-sm font-medium text-white">Make</label>
              <p class="mt-1 text-sm text-white font-medium break-words">{{ vehicle.make?.name || 'Unknown' }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-white">Model</label>
              <p class="mt-1 text-sm text-white font-medium break-words">{{ vehicle.model?.name || 'Unknown' }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-white">Sub-Type</label>
              <p class="mt-1 text-sm text-white font-medium break-words">{{ vehicle.type?.sub_type || 'N/A' }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-white">Year</label>
              <p class="mt-1 text-sm text-white font-medium">{{ vehicle.year }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-white">Color</label>
              <p class="mt-1 text-sm text-white font-medium break-words">{{ vehicle.color }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-white">License Plate</label>
              <p class="mt-1 text-sm text-white font-medium break-words">{{ vehicle.license_plate || 'Not Set' }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-white">Fuel Type</label>
              <p class="mt-1 text-sm text-white font-medium break-words">{{ vehicle.fuelType?.name || vehicle.fuel_type?.name || 'Unknown' }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-white">Transmission</label>
              <p class="mt-1 text-sm text-white font-medium break-words">{{ vehicle.transmission?.name || 'Unknown' }}</p>
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
        <div v-if="vehicle.pricing_tiers && vehicle.pricing_tiers.length > 0" class="glass-card p-4 md:p-6 border border-white/20">
          <h3 class="text-lg font-semibold mb-4 text-white">Pricing Tiers</h3>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 md:gap-4">
            <div v-for="tier in vehicle.pricing_tiers" :key="tier.id" class="bg-white/10 backdrop-blur-sm p-3 md:p-4 rounded-lg border border-white/20 shadow-glow">
              <div class="flex justify-between items-center">
                <div>
                  <span class="text-xs md:text-sm font-medium text-white/70 break-words">
                    {{ tier.duration_from }} {{ tier.duration_from === 1 ? tier.duration_unit.slice(0, -1) : tier.duration_unit }}
                  </span>
                </div>
                <div class="text-base md:text-lg font-bold text-green-400 ml-2">
                  ₱{{ tier.price }}
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
import Swal from 'sweetalert2';

const props = defineProps({ vehicle: Object });

function deletePhoto(photoId) {
  Swal.fire({
    title: 'Delete Photo?',
    text: "Are you sure you want to delete this photo? This action cannot be undone.",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#ef4444',
    cancelButtonColor: '#6b7280',
    confirmButtonText: 'Yes, Delete It',
    cancelButtonText: 'Cancel'
  }).then((result) => {
    if (result.isConfirmed) {
      router.delete(`/owner/vehicles/photos/${photoId}`, {
        onSuccess: () => {
          Swal.fire({
            title: 'Deleted!',
            text: 'Photo has been deleted successfully.',
            icon: 'success',
            confirmButtonColor: '#3b82f6'
          });
        },
        onError: () => {
          Swal.fire({
            title: 'Error!',
            text: 'There was an error deleting the photo. Please try again.',
            icon: 'error',
            confirmButtonColor: '#3b82f6'
          });
        }
      });
    }
  });
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
  
  /* Ensure mobile containers don't overflow */
  .glass-card-dark {
    margin-left: 0.75rem;
    margin-right: 0.75rem;
    max-width: calc(100vw - 1.5rem);
  }
  
  /* Improve mobile text wrapping */
  .break-words {
    word-wrap: break-word;
    word-break: break-word;
    hyphens: auto;
  }
  
  /* Ensure buttons don't wrap awkwardly */
  .whitespace-nowrap {
    white-space: nowrap;
  }
}

@media (min-width: 640px) and (max-width: 1023px) {
  .photos-grid {
    grid-template-columns: repeat(3, 1fr);
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

/* Ensure map container is responsive */
@media (max-width: 640px) {
  .h-72 {
    height: 16rem; /* Slightly smaller on mobile */
  }
}
</style>
