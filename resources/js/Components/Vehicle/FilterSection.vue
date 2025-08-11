<template>
  <div class="bg-white rounded-lg shadow-md mb-8 overflow-hidden">
    <!-- Quick Filters -->
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-4 border-b">
      <h3 class="text-lg font-semibold text-gray-800 mb-3">Quick Filters</h3>
      <div class="flex flex-wrap gap-2">
        <button
          type="button"
          @click="emit('quickFilter', 'category', 'car')"
          :class="filters.category === 'car' ? 'bg-blue-500 text-white' : 'bg-white text-gray-700 hover:bg-gray-100'"
          class="px-4 py-2 border border-gray-300 rounded-full text-sm font-medium transition-colors"
        >
          🚗 Cars Only
        </button>
        <button
          type="button"
          @click="emit('quickFilter', 'category', 'motorcycle')"
          :class="filters.category === 'motorcycle' ? 'bg-blue-500 text-white' : 'bg-white text-gray-700 hover:bg-gray-100'"
          class="px-4 py-2 border border-gray-300 rounded-full text-sm font-medium transition-colors"
        >
          🏍️ Motorcycles Only
        </button>
        <button
          type="button"
          @click="emit('quickFilter', 'sort_by', 'price_low')"
          :class="filters.sort_by === 'price_low' ? 'bg-green-500 text-white' : 'bg-white text-gray-700 hover:bg-gray-100'"
          class="px-4 py-2 border border-gray-300 rounded-full text-sm font-medium transition-colors"
        >
          💰 Lowest Price First
        </button>
        <button
          type="button"
          @click="emit('quickFilter', 'sort_by', 'popular')"
          :class="filters.sort_by === 'popular' ? 'bg-orange-500 text-white' : 'bg-white text-gray-700 hover:bg-gray-100'"
          class="px-4 py-2 border border-gray-300 rounded-full text-sm font-medium transition-colors"
        >
          🔥 Most Popular
        </button>
      </div>
    </div>

    <!-- Basic Filters -->
    <div class="p-6">
      <form @submit.prevent="emit('applyFilters')" class="space-y-4">
        <!-- Main Filter Row -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Make</label>
            <select
              v-model="filters.make_id"
              @change="emit('makeChange')"
              class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            >
              <option value="">All Makes</option>
              <option v-for="make in filterOptions.makes" :key="make.id" :value="make.id">
                {{ make.name }}
              </option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Model</label>
            <select
              v-model="filters.model_id"
              :disabled="!filters.make_id || loadingModels"
              class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100"
            >
              <option value="">{{ loadingModels ? 'Loading...' : filters.make_id ? 'All Models' : 'Select Make First' }}</option>
              <option v-for="model in availableModels" :key="model.id" :value="model.id">
                {{ model.name }}
              </option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Fuel Type</label>
            <select
              v-model="filters.fuel_type_id"
              class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            >
              <option value="">All Fuel Types</option>
              <option v-for="fuel in filterOptions.fuelTypes" :key="fuel.id" :value="fuel.id">
                {{ fuel.name }}
              </option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Transmission</label>
            <select
              v-model="filters.transmission_id"
              class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            >
              <option value="">All Transmissions</option>
              <option v-for="trans in filterOptions.transmissions" :key="trans.id" :value="trans.id">
                {{ trans.name }}
              </option>
            </select>
          </div>
        </div>

        <!-- Availability Date Filter -->
        <div class="bg-blue-50 p-4 rounded-lg">
          <h4 class="text-sm font-semibold text-gray-800 mb-3">📅 Check Availability</h4>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">From Date</label>
              <input
                v-model="filters.available_from"
                type="datetime-local"
                :min="new Date().toISOString().slice(0, 16)"
                class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">To Date</label>
              <input
                v-model="filters.available_to"
                type="datetime-local"
                :min="filters.available_from || new Date().toISOString().slice(0, 16)"
                class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              />
            </div>
          </div>
          <p class="text-xs text-gray-600 mt-2">
            Only shows vehicles available for your selected dates
          </p>
        </div>

        <!-- More Filters Toggle -->
        <div class="flex justify-center">
          <button
            type="button"
            @click="emit('toggleAdvanced')"
            class="text-blue-600 hover:text-blue-800 font-medium flex items-center gap-2 px-4 py-2 rounded-lg hover:bg-blue-50 transition-colors"
          >
            <span>{{ showAdvancedFilters ? 'Hide' : 'More' }} Filters</span>
            <svg class="w-4 h-4 transform transition-transform" :class="showAdvancedFilters ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
          </button>
        </div>

        <!-- Advanced Filters (Collapsible) -->
        <div v-show="showAdvancedFilters" class="space-y-4 border-t pt-4">
          <!-- Price Range -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-3">Price Range (₱/day)</label>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
              <div>
                <input
                  v-model="filters.price_from"
                  type="number"
                  :min="filterOptions.priceRange?.min"
                  :max="filterOptions.priceRange?.max"
                  class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                  placeholder="Min Price"
                />
              </div>
              <div class="text-center text-gray-500 font-medium">to</div>
              <div>
                <input
                  v-model="filters.price_to"
                  type="number"
                  :min="filterOptions.priceRange?.min"
                  :max="filterOptions.priceRange?.max"
                  class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                  placeholder="Max Price"
                />
              </div>
            </div>
            <div v-if="filterOptions.priceRange" class="mt-2 text-sm text-gray-600">
              Available range: ₱{{ filterOptions.priceRange.min }} - ₱{{ filterOptions.priceRange.max }}
            </div>
          </div>

          <!-- Additional Filters Row -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Vehicle Sub-Type</label>
              <select
                v-model="filters.type_id"
                class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              >
                <option value="">All Sub-Types</option>
                <option v-for="type in filterOptions.types" :key="type.id" :value="type.id">
                  {{ type.category }} - {{ type.sub_type }}
                </option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Color</label>
              <div class="relative">
                <input
                  v-model="filters.color"
                  type="text"
                  class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                  placeholder="Enter color"
                  list="color-suggestions"
                />
                <datalist id="color-suggestions">
                  <option v-for="color in filterOptions.popularColors" :key="color" :value="color">
                    {{ color }}
                  </option>
                </datalist>
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Results per page</label>
              <select
                v-model="filters.per_page"
                class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              >
                <option value="6">6 vehicles</option>
                <option value="9">9 vehicles</option>
                <option value="12">12 vehicles</option>
                <option value="18">18 vehicles</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t">
          <button
            type="submit"
            :disabled="isFiltering"
            class="flex-1 bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 disabled:bg-blue-400 transition-colors flex items-center justify-center gap-2"
          >
            <svg v-if="isFiltering" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ isFiltering ? 'Searching...' : 'Search Vehicles' }}
          </button>
          <button
            type="button"
            @click="emit('resetFilters')"
            class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition-colors"
          >
            Clear All Filters
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
defineProps({
  filters: Object,
  filterOptions: Object,
  availableModels: Array,
  loadingModels: Boolean,
  isFiltering: Boolean,
  showAdvancedFilters: Boolean
})

const emit = defineEmits(['quickFilter', 'applyFilters', 'makeChange', 'toggleAdvanced', 'resetFilters'])
</script>
