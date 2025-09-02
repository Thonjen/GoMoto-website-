<template>
  <div class="relative">
    <!-- Main Search Container -->
    <div class="glass-card p-8 md:p-10 backdrop-blur-lg border border-white/20">
      <!-- Header Section -->
      <div class="text-center mb-8">
        <h1 class="text-3xl md:text-4xl font-black text-white mb-4 text-shadow-lg">
          Find Your Perfect Ride
        </h1>
        <p class="text-lg md:text-xl text-white/90 font-light leading-relaxed">
          Discover premium vehicles for every journey across Surigao del Norte
        </p>
      </div>
      
      <!-- Enhanced Search Bar -->
      <div class="max-w-3xl mx-auto mb-8">
        <div class="relative group">
          <!-- Search Input -->
          <div class="relative">
            <input
              v-model="searchValue"
              @keyup.enter="handleSearch"
              @focus="isSearchFocused = true"
              @blur="isSearchFocused = false"
              type="text"
              class="w-full px-6 py-5 text-lg bg-white/10 border border-white/20 rounded-2xl text-white placeholder-white/70 backdrop-blur-md focus:bg-white/20 focus:border-white/40 focus:outline-none focus:ring-2 focus:ring-white/30 transition-all duration-300 pl-16 pr-32"
              placeholder="Search by make, model, type, or location..."
            />
            
            <!-- Search Icon -->
            <div class="absolute left-5 top-1/2 transform -translate-y-1/2">
              <svg 
                :class="[
                  'w-6 h-6 transition-colors duration-300',
                  isSearchFocused ? 'text-white' : 'text-white/70'
                ]" 
                fill="none" 
                stroke="currentColor" 
                viewBox="0 0 24 24"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
              </svg>
            </div>
            
            <!-- Search Button -->
            <button
              @click="handleSearch"
              class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-white/20 hover:bg-white/30 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-200 backdrop-blur-sm border border-white/20 hover:scale-105 active:scale-95"
            >
              <span class="hidden sm:inline">Search</span>
              <svg class="w-5 h-5 sm:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
              </svg>
            </button>
          </div>
          
          <!-- Search Suggestions Glow Effect -->
          <div 
            :class="[
              'absolute inset-0 rounded-2xl transition-opacity duration-300 pointer-events-none',
              isSearchFocused ? 'opacity-100' : 'opacity-0'
            ]"
            style="background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent); animation: searchGlow 2s ease-in-out infinite;"
          ></div>
        </div>
      </div>

      <!-- Quick Filters -->
      <div class="flex flex-wrap justify-center gap-3 mb-8">
        <button
          v-for="filter in quickFilters"
          :key="filter.label"
          @click="handleQuickFilter(filter)"
          class="glass-card px-4 py-2 text-white/90 hover:text-white hover:bg-white/20 rounded-xl text-sm font-medium transition-all duration-200 border border-white/20 hover:scale-105 active:scale-95 backdrop-blur-sm"
        >
          <component :is="filter.icon" class="w-4 h-4 inline mr-2" />
          {{ filter.label }}
        </button>
      </div>

      <!-- Popular Searches -->
      <div v-if="popularSearches?.length" class="text-center">
        <div class="inline-flex flex-wrap items-center justify-center gap-2">
          <span class="text-sm text-white/80 font-medium mr-3">Popular searches:</span>
          <button
            v-for="search in popularSearches"
            :key="search"
            @click="handleQuickSearch(search)"
            class="px-3 py-1.5 bg-white/10 hover:bg-white/20 text-white/90 hover:text-white rounded-lg text-sm transition-all duration-200 backdrop-blur-sm border border-white/10 hover:border-white/30 hover:scale-105 active:scale-95"
          >
            {{ search }}
          </button>
        </div>
      </div>
    </div>

    <!-- Floating Search Hints -->
    <div 
      v-if="showSearchHints && searchValue.length > 0"
      class="absolute top-full left-0 right-0 mt-2 glass-card p-4 border border-white/20 z-10 backdrop-blur-lg"
    >
      <div class="text-white/90 text-sm">
        <div class="flex items-center mb-2">
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
          <span class="font-medium">Quick tip:</span>
        </div>
        <p class="text-white/70">Try searching for specific car brands, motorcycle types, or locations in Surigao del Norte</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

defineProps({
  popularSearches: Array
})

const emit = defineEmits(['search', 'quickSearch', 'quickFilter'])

const searchValue = ref('')
const isSearchFocused = ref(false)
const showSearchHints = ref(false)

// Quick filter options
const quickFilters = computed(() => [
  {
    label: 'Cars',
    value: 'car',
    icon: 'CarIcon'
  },
  {
    label: 'Motorcycles', 
    value: 'motorcycle',
    icon: 'BikeIcon'
  },
  {
    label: 'SUVs',
    value: 'suv', 
    icon: 'TruckIcon'
  },
  {
    label: 'Automatic',
    value: 'automatic',
    icon: 'SettingsIcon'
  }
])

function handleSearch() {
  if (searchValue.value.trim()) {
    emit('search', searchValue.value.trim())
  }
}

function handleQuickSearch(searchTerm) {
  searchValue.value = searchTerm
  emit('quickSearch', searchTerm)
}

function handleQuickFilter(filter) {
  emit('quickFilter', filter.value)
}

// Simple icons as inline SVG components
const CarIcon = {
  template: `
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"></path>
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 6H4l3-3h7l3 3v11a1 1 0 01-1 1h-2m-6 0H6a1 1 0 01-1-1V6z"></path>
    </svg>
  `
}

const BikeIcon = {
  template: `
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <circle cx="18" cy="18" r="3"></circle>
      <circle cx="6" cy="18" r="3"></circle>
      <path d="M8 16h6l4-7H6"></path>
      <path d="M12 8V6"></path>
    </svg>
  `
}

const TruckIcon = {
  template: `
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"></path>
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 6h3l4 4v6H4V10l3-4h6z"></path>
    </svg>
  `
}

const SettingsIcon = {
  template: `
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
    </svg>
  `
}
</script>

<style scoped>
/* Custom search glow animation */
@keyframes searchGlow {
  0%, 100% {
    background-position: -200% center;
  }
  50% {
    background-position: 200% center;
  }
}

/* Enhanced focus states */
input:focus {
  box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.1);
}

/* Smooth transitions for all interactive elements */
button, input {
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Glass morphism enhancements */
.glass-card {
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
}
</style>
