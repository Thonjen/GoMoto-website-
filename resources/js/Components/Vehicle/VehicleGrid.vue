<template>
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    <div
      v-for="vehicle in vehicles"
      :key="vehicle.id"
      class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-all duration-300 relative group cursor-pointer"
      @click="emit('viewDetail', vehicle.id)"
    >
      <!-- Vehicle Image -->
      <div class="relative h-48 bg-gray-200">
        <img
          v-if="vehicle.main_photo_url"
          :src="vehicle.main_photo_url"
          :alt="`${vehicle.make?.name} ${vehicle.model?.name}`"
          class="w-full h-full object-cover"
        />
        <div
          v-else
          class="w-full h-full flex items-center justify-center text-gray-500"
        >
          <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
          </svg>
        </div>

        <!-- Status Overlay -->
        <div
          v-if="!vehicle.is_available || vehicle.is_booked"
          class="absolute inset-0 bg-black bg-opacity-80 flex items-center justify-center"
        >
          <div class="text-white text-center">
            <div class="text-xl font-bold mb-2">
              {{ !vehicle.is_available ? '❌ Unavailable' : '🚫 Currently Booked' }}
            </div>
            <div class="text-sm opacity-90">
              {{ !vehicle.is_available ? 'Vehicle not available for rental' : 'Vehicle is currently rented' }}
            </div>
          </div>
        </div>

        <!-- Price Badge -->
        <div
          v-if="vehicle.pricing_tiers?.length && (vehicle.is_available && !vehicle.is_booked)"
          class="absolute top-3 right-3 bg-gradient-to-r from-green-500 to-green-600 text-white px-3 py-2 rounded-full text-sm font-bold shadow-lg"
        >
          <div class="text-xs opacity-90">From</div>
          <div>₱{{ Math.min(...vehicle.pricing_tiers.map(t => parseFloat(t.price))) }}</div>
        </div>

        <!-- Vehicle Category Badge -->
        <div class="absolute top-3 left-3 flex flex-col gap-2">
          <div class="bg-white bg-opacity-90 backdrop-blur-sm text-gray-800 px-2 py-1 rounded-full text-xs font-medium flex items-center gap-1">
            <span>{{ vehicle.type?.category === 'car' ? '🚗' : '🏍️' }}</span>
            <span>{{ vehicle.type?.sub_type || 'Vehicle' }}</span>
          </div>
        </div>
      </div>

      <!-- Vehicle Details -->
      <div class="p-5">
        <!-- Title -->
        <div class="mb-3">
          <h3 class="text-lg font-bold text-gray-900 group-hover:text-blue-600 transition-colors">
            {{ vehicle.make?.name }} {{ vehicle.model?.name }}
            <span v-if="vehicle.year" class="text-gray-600 font-normal">({{ vehicle.year }})</span>
          </h3>
          <p class="text-sm text-gray-600">{{ vehicle.type?.sub_type }}</p>
        </div>

        <!-- Vehicle Info -->
        <div class="space-y-3 mb-4">
          <!-- Fuel Type and Transmission -->
          <div class="flex items-center gap-6">
            <div class="flex items-center gap-2">
              <span class="text-lg">⛽</span>
<span class="text-sm text-gray-600">{{ vehicle.fuel_type?.name || 'N/A' }}</span>
            </div>
            <div class="flex items-center gap-2">
              <span class="text-lg">⚙️</span>
              <span class="text-sm text-gray-600">{{ vehicle.transmission?.name || 'Manual' }}</span>
            </div>
          </div>
          
          <!-- Location -->
          <div class="flex items-center gap-2 text-blue-600">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
            <span class="text-sm font-medium truncate">{{ vehicle.location_name || 'Surigao del Norte' }}</span>
          </div>
        </div>

        <!-- Description -->
        <p v-if="vehicle.description" class="text-sm text-gray-600 mb-4 line-clamp-2">
          {{ vehicle.description }}
        </p>

        <!-- Pricing Info -->
        <div v-if="vehicle.pricing_tiers?.length && (vehicle.is_available && !vehicle.is_booked)" class="mb-4">
          <div class="flex items-center justify-between">
<span class="text-2xl font-bold text-green-600">
  ₱{{ Math.min(...vehicle.pricing_tiers.map(t => parseFloat(t.price))) }}
</span>
<span class="text-sm text-gray-500">
  /{{ vehicle.pricing_tiers.find(t => t.price == Math.min(...vehicle.pricing_tiers.map(pt => parseFloat(pt.price)))).duration_unit }}
</span>
          </div>
          <div class="text-xs text-gray-500 mt-1">
            {{ vehicle.pricing_tiers.length }} pricing option{{ vehicle.pricing_tiers.length > 1 ? 's' : '' }} available
          </div>
        </div>

        <!-- Action Button -->
        <div class="mt-4">
          <button
            v-if="vehicle.is_available && !vehicle.is_booked"
            class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg font-semibold hover:bg-blue-700 transition-colors group-hover:shadow-lg"
            @click.stop="emit('viewDetail', vehicle.id)"
          >
            View Details & Book
          </button>
          <button
            v-else
            disabled
            class="w-full bg-gray-300 text-gray-500 py-3 px-4 rounded-lg font-semibold cursor-not-allowed"
          >
            {{ !vehicle.is_available ? 'Unavailable' : 'Currently Booked' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  vehicles: Array
})

const emit = defineEmits(['viewDetail'])
</script>
