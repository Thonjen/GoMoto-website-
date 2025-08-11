<template>
  <div class="text-center mb-8">
    <h1 class="text-4xl font-bold text-gray-900 mb-4">
      Find Your Perfect Ride
    </h1>
    <p class="text-lg text-gray-600 mb-8">
      Explore our wide selection of vehicles for your next adventure
    </p>
    
    <!-- Main Search Bar -->
    <div class="max-w-2xl mx-auto mb-6">
      <div class="relative">
        <input
          v-model="searchValue"
          @keyup.enter="handleSearch"
          type="text"
          class="w-full px-6 py-4 text-lg border-2 border-gray-200 rounded-full focus:ring-2 focus:ring-blue-500 focus:border-blue-500 pl-14"
          placeholder="Search by make, model, color, or location..."
        />
        <svg class="absolute left-5 top-1/2 transform -translate-y-1/2 w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
        </svg>
        <button
          @click="handleSearch"
          class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-blue-600 text-white px-6 py-2 rounded-full hover:bg-blue-700 transition-colors"
        >
          Search
        </button>
      </div>
    </div>

    <!-- Popular Searches -->
    <div v-if="popularSearches?.length" class="flex flex-wrap justify-center gap-2 mb-6">
      <span class="text-sm text-gray-600 mr-2">Popular:</span>
      <button
        v-for="search in popularSearches"
        :key="search"
        @click="handleQuickSearch(search)"
        class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm hover:bg-gray-200 transition-colors"
      >
        {{ search }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

defineProps({
  popularSearches: Array
})

const emit = defineEmits(['search', 'quickSearch'])

const searchValue = ref('')

function handleSearch() {
  emit('search', searchValue.value)
}

function handleQuickSearch(searchTerm) {
  searchValue.value = searchTerm
  emit('quickSearch', searchTerm)
}
</script>
