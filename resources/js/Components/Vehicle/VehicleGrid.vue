<template>
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    <div
      v-for="vehicle in vehicles"
      :key="vehicle.id"
      class="bg-white rounded-2xl shadow-sm border border-gray-200 flex flex-col hover:shadow-lg transition-all duration-300 cursor-pointer"
      @click="emit('viewDetail', vehicle.id)"
    >
      <!-- Vehicle Image -->
      <div class="relative h-48 bg-gray-100 rounded-t-2xl overflow-hidden">
        <img
          v-if="vehicle.main_photo_url"
          :src="vehicle.main_photo_url"
          :alt="`${vehicle.make?.name} ${vehicle.model?.name}`"
          class="w-full h-full object-cover"
        />
        <div v-else class="flex items-center justify-center h-full text-gray-400">
          <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2
                 l1.586-1.586a2 2 0 012.828 0L20 14
                 m-6-6h.01M6 20h12a2 2 0 002-2V6
                 a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2
                 2 0 002 2z"/>
          </svg>
        </div>

        <!-- Overlay for Status -->
        <div
          v-if="!vehicle.is_available || vehicle.is_booked"
          class="absolute inset-0 bg-black/70 flex items-center justify-center"
        >
          <p class="text-white font-semibold text-center text-sm">
            {{ !vehicle.is_available ? '❌ Unavailable' : '🚫 Booked' }}
          </p>
        </div>

        <!-- Price Badge -->
        <div
          v-if="vehicle.pricing_tiers?.length && (vehicle.is_available && !vehicle.is_booked)"
          class="absolute top-3 right-3 bg-gradient-to-r from-green-500 to-green-600 text-white px-3 py-1.5 rounded-full text-sm font-semibold shadow-md"
        >
          From ₱{{ Math.min(...vehicle.pricing_tiers.map(t => parseFloat(t.price))) }}
        </div>

        <!-- Category Badge -->
        <div class="absolute top-3 left-3 bg-white/90 px-2 py-1 rounded-full text-xs font-medium text-gray-700 flex items-center gap-1 shadow-sm">
          <span>{{ vehicle.type?.category === 'car' ? '🚗' : '🏍️' }}</span>
          <span>{{ vehicle.type?.sub_type || 'Vehicle' }}</span>
        </div>

        <!-- Save Button -->
        <div class="absolute bottom-3 right-3">
          <SaveButton
            :vehicle-id="vehicle.id"
            :initial-saved="vehicle.saves?.length > 0"
            :initial-save-count="vehicle.saves_count || 0"
            size="sm"
            :show-text="false"
            :show-count="false"
            @click.stop
          />
        </div>
      </div>

      <!-- Details -->
      <div class="flex flex-col flex-1 p-5 space-y-3">
        <!-- Title -->
        <div>
          <h3 class="text-lg font-semibold text-gray-900 leading-tight">
            {{ vehicle.make?.name }} {{ vehicle.model?.name }}
            <span v-if="vehicle.year" class="text-gray-500 font-normal">({{ vehicle.year }})</span>
          </h3>
          <p class="text-sm text-gray-500">{{ vehicle.type?.sub_type }}</p>
        </div>

<!-- Vehicle Info (better spacing) -->
<div class="space-y-2 text-sm text-gray-600">
  <!-- Owner -->
  <div class="flex items-center gap-2">
    <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
        d="M16 7a4 4 0 11-8 0 4 4 0 018
           0zM12 14a7 7 0 00-7 7h14a7 7
           0 00-7-7z"/>
    </svg>
    <span class="truncate">
      {{ vehicle.owner?.name || (vehicle.owner?.first_name + ' ' + vehicle.owner?.last_name) || 'Unknown' }}
    </span>
  </div>

  <!-- Rating -->
  <div v-if="vehicle.ratings_avg_rating > 0" class="flex items-center gap-2">
    <div class="flex items-center space-x-1">
      <span class="text-yellow-400">★</span>
      <span class="font-medium text-sm">{{ parseFloat(vehicle.ratings_avg_rating).toFixed(1) }}</span>
      <span class="text-gray-400 text-xs">({{ vehicle.ratings_count || 0 }})</span>
    </div>
  </div>
  <div v-else class="flex items-center gap-2 text-gray-400 text-xs">
    <span>⭐</span>
    <span>No reviews yet</span>
  </div>

  <!-- Location (full-width, wraps naturally) -->
  <div class="flex items-start gap-2 text-blue-600">
    <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
        d="M17.657 16.657L13.414 20.9a2
           2 0 01-2.828 0l-4.243-4.243a8
           8 0 1111.314 0z"/>
      <circle cx="12" cy="11" r="3" stroke-width="2"/>
    </svg>
    <span class="leading-snug break-words">
      {{ vehicle.location_name || 'Surigao del Norte' }}
    </span>
  </div>

  <!-- Fuel + Transmission (2 columns only) -->
  <div class="grid grid-cols-2 gap-3">
    <!-- Fuel -->
    <div class="flex items-center gap-2">
      <span>⛽</span>
      <span class="truncate">{{ vehicle.fuelType?.name || vehicle.fuel_type?.name || 'N/A' }}</span>
    </div>

    <!-- Transmission -->
    <div class="flex items-center gap-2">
      <span>⚙️</span>
      <span class="truncate">{{ vehicle.transmission?.name || 'Manual' }}</span>
    </div>
  </div>
</div>


        <!-- Description -->
        <p v-if="vehicle.description" class="text-sm text-gray-500 line-clamp-3">
          {{ vehicle.description }}
        </p>

        <div class="flex-1"></div> <!-- Spacer to push footer down -->

        <!-- Pricing -->
        <div>
          <div v-if="vehicle.pricing_tiers?.length && (vehicle.is_available && !vehicle.is_booked)">
            <div class="flex items-baseline justify-between">
              <span class="text-xl font-bold text-green-600">
                ₱{{ Math.min(...vehicle.pricing_tiers.map(t => parseFloat(t.price))) }}
              </span>
              <span class="text-sm text-gray-500">
                /{{ vehicle.pricing_tiers.find(t => t.price == Math.min(...vehicle.pricing_tiers.map(pt => parseFloat(pt.price)))).duration_unit }}
              </span>
            </div>
            <p class="text-xs text-gray-400 relative group cursor-help">
              {{ vehicle.pricing_tiers.length }} option{{ vehicle.pricing_tiers.length > 1 ? 's' : '' }}
              
              <!-- Tooltip -->
              <div class="absolute bottom-full left-0 mb-2 w-64 bg-gray-800 text-white text-xs rounded-lg p-3 opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none z-10 shadow-lg">
                <div class="font-semibold mb-2">Available Pricing Options:</div>
                <div class="space-y-1">
                  <div 
                    v-for="tier in vehicle.pricing_tiers" 
                    :key="tier.id"
                    class="flex justify-between items-center"
                  >
                    <span>
                      {{ tier.duration_from || tier.duration_value || tier.duration || 1 }} 
                      {{ tier.duration_unit || tier.unit || 'hour' }}{{ (tier.duration_from || tier.duration_value || tier.duration || 1) > 1 ? 's' : '' }}
                    </span>
                    <span class="font-semibold text-green-300">₱{{ parseFloat(tier.price).toFixed(0) }}</span>
                  </div>
                </div>
                <!-- Arrow pointing down -->
                <div class="absolute top-full left-4 w-0 h-0 border-l-4 border-r-4 border-t-4 border-l-transparent border-r-transparent border-t-gray-800"></div>
              </div>
            </p>
          </div>
          <div v-else class="text-sm text-gray-400">
            Not Available
          </div>
        </div>

        <!-- Action Button -->
        <div>
          <button
            v-if="vehicle.is_available && !vehicle.is_booked"
            class="w-full bg-blue-600 text-white py-2.5 rounded-lg font-medium hover:bg-blue-700 transition-colors"
            @click.stop="emit('viewDetail', vehicle.id)"
          >
            View Details & Book
          </button>
          <button
            v-else
            disabled
            class="w-full bg-gray-200 text-gray-500 py-2.5 rounded-lg font-medium cursor-not-allowed"
          >
            {{ !vehicle.is_available ? 'Unavailable' : 'Booked' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import SaveButton from '@/Components/Vehicle/SaveButton.vue'

defineProps({
  vehicles: Array
})
const emit = defineEmits(['viewDetail'])
</script>
