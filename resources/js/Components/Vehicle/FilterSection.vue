<template>
  <div class="glass-card p-6 space-y-6 backdrop-blur-md bg-black/25 rounded-2xl shadow-glow">
    <!-- Quick Filters -->
    <div class="bg-black/20 p-4 rounded-xl backdrop-blur-sm border border-white/10">
      <h3 class="text-sm font-bold text-white mb-3">Quick Filters</h3>
      <div class="flex flex-wrap gap-2">
        <button
          type="button"
          @click="emit('quickFilter', 'category', 'car')"
          :class="[
            'px-3 py-2 rounded-lg text-xs font-medium transition-all duration-200',
            filters.category === 'car'
              ? 'bg-white/20 text-white border-white/30'
              : 'bg-black/10 text-white/70 border-white/20 hover:bg-white/10 hover:text-white'
          ]"
        >
          🚗 Cars
        </button>
        <button
          type="button"
          @click="emit('quickFilter', 'category', 'motorcycle')"
          :class="[
            'px-3 py-2 rounded-lg text-xs font-medium transition-all duration-200',
            filters.category === 'motorcycle'
              ? 'bg-white/20 text-white border-white/30'
              : 'bg-black/10 text-white/70 border-white/20 hover:bg-white/10 hover:text-white'
          ]"
        >
          🏍️ Motorcycles
        </button>
        <button
          type="button"
          @click="emit('quickFilter', 'sort_by', 'price_low')"
          :class="[
            'px-3 py-1 rounded-full text-xs font-medium transition-all duration-200',
            filters.sort_by === 'price_low'
              ? 'bg-white/20 text-white border-white/30'
              : 'bg-black/10 text-white/70 border-white/20 hover:bg-white/10 hover:text-white'
          ]"
        >
          💰 Low Price
        </button>
        <button
          type="button"
          @click="emit('quickFilter', 'sort_by', 'popular')"
          :class="[
            'px-3 py-1 rounded-full text-xs font-medium transition-all duration-200',
            filters.sort_by === 'popular'
              ? 'bg-white/20 text-white border-white/30'
              : 'bg-black/10 text-white/70 border-white/20 hover:bg-white/10 hover:text-white'
          ]"
        >
          🔥 Popular
        </button>
      </div>
    </div>

    <!-- Basic Filters -->
    <div>
      <form @submit.prevent="emit('applyFilters')" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-semibold text-white mb-1">Make</label>
            <select
              v-model="filters.make_id"
              @change="emit('makeChange')"
              class="w-full p-2 rounded-lg bg-black/20 text-white border border-white/20 focus:ring-1 focus:ring-white focus:border-white text-xs"
            >
              <option value="">All</option>
              <option class="bg-gray-800 text-white"
                v-for="make in filterOptions.makes"
                :key="make.id"
                :value="make.id"
              >
                {{ make.name }}
              </option>
            </select>
          </div>

          <div>
            <label class="block text-xs font-semibold text-white mb-1">Model</label>
            <select
              v-model="filters.model_id"
              :disabled="!filters.make_id || loadingModels"
              class="w-full p-2 rounded-lg bg-black/20 text-white border border-white/20 focus:ring-1 focus:ring-white focus:border-white text-xs disabled:opacity-50"
            >
              <option value="">
                {{
                  loadingModels
                    ? "Loading..."
                    : filters.make_id
                    ? "All"
                    : "Select Make"
                }}
              </option>
              <option class="bg-gray-800 text-white"
                v-for="model in availableModels"
                :key="model.id"
                :value="model.id"
              >
                {{ model.name }}
              </option>
            </select>
          </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-semibold text-white mb-1">Fuel</label>
            <select
              v-model="filters.fuel_type_id"
              class="w-full p-2 rounded-lg bg-black/20 text-white border border-white/20 focus:ring-1 focus:ring-white focus:border-white text-xs"
            >
              <option value="">All</option>
              <option class="bg-gray-800 text-white"
                v-for="fuel in filterOptions.fuelTypes"
                :key="fuel.id"
                :value="fuel.id"
              >
                {{ fuel.name }}
              </option>
            </select>
          </div>

          <div>
            <label class="block text-xs font-semibold text-white mb-1">Trans.</label>
            <select
              v-model="filters.transmission_id"
              class="w-full p-2 rounded-lg bg-black/20 text-white border border-white/20 focus:ring-1 focus:ring-white focus:border-white text-xs"
            >
              <option value="">All</option>
              <option class="bg-gray-800 text-white"
                v-for="trans in filterOptions.transmissions"
                :key="trans.id"
                :value="trans.id"
              >
                {{ trans.name }}
              </option>
            </select>
          </div>
        </div>

        <!-- Availability -->
        <div class="bg-black/20 p-3 rounded-xl border border-white/10 backdrop-blur-sm">
          <h4 class="text-xs font-bold text-white mb-2">📅 Availability</h4>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-semibold text-white mb-1">From</label>
              <input
                v-model="filters.available_from"
                type="datetime-local"
                :min="new Date().toISOString().slice(0,16)"
                class="w-full p-2 rounded-lg bg-black/20 text-white border border-white/20 focus:ring-1 focus:ring-white focus:border-white text-xs"
              />
            </div>
            <div>
              <label class="block text-xs font-semibold text-white mb-1">To</label>
              <input
                v-model="filters.available_to"
                type="datetime-local"
                :min="filters.available_from || new Date().toISOString().slice(0,16)"
                class="w-full p-2 rounded-lg bg-black/20 text-white border border-white/20 focus:ring-1 focus:ring-white focus:border-white text-xs"
              />
            </div>
          </div>
        </div>

        <!-- Buttons -->
        <div class="flex gap-2 pt-2">
          <button
            type="submit"
            :disabled="isFiltering"
            class="flex-1 bg-gray-700/90 text-white px-4 py-2 rounded-xl font-semibold hover:bg-gray-700/70 disabled:bg-gray-600 transition-colors text-xs flex items-center justify-center gap-2"
          >
            <svg
              v-if="isFiltering"
              class="animate-spin h-4 w-4"
              fill="none"
              viewBox="0 0 24 24"
            >
              <circle
                class="opacity-25"
                cx="12"
                cy="12"
                r="10"
                stroke="currentColor"
                stroke-width="4"
              ></circle>
              <path
                class="opacity-75"
                fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.37 0 0 5.37 0 12h4z"
              ></path>
            </svg>
            {{ isFiltering ? "Searching..." : "Search" }}
          </button>
          <button
            type="button"
            @click="emit('resetFilters')"
            class="px-4 py-2 border border-white/30 text-white rounded-xl font-medium hover:bg-white/10 transition-colors text-xs"
          >
            Clear
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
});

const emit = defineEmits([
  "quickFilter",
  "applyFilters",
  "makeChange",
  "resetFilters",
]);
</script>

<style>
/* subtle glowing glass effect for cards */
.shadow-glow {
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4), 0 0 12px rgba(255, 255, 255, 0.05);
}
</style>
