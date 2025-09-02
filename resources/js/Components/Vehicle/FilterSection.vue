<template>
    <div class="glass-card p-6 shadow-glow space-y-4">
        <!-- Quick Filters -->
        <div class="bg-white/5 p-4 rounded-lg backdrop-blur-sm border border-white/10">
            <h3 class="text-sm font-semibold text-white mb-3">
                Quick Filters
            </h3>
            <div class="flex flex-wrap gap-2">
                <button
                    type="button"
                    @click="emit('quickFilter', 'category', 'car')"
                    :class="[
                        'px-3 py-2 border rounded-lg text-xs font-medium transition-all duration-200',
                        filters.category === 'car'
                            ? 'bg-primary-500/20 text-primary-300 border-primary-500/30'
                            : 'bg-white/10 text-white/80 border-white/20 hover:bg-white/20 hover:text-white'
                    ]"
                >
                    🚗 Cars
                </button>
                <button
                    type="button"
                    @click="emit('quickFilter', 'category', 'motorcycle')"
                    :class="[
                        'px-3 py-2 border rounded-lg text-xs font-medium transition-all duration-200',
                        filters.category === 'motorcycle'
                            ? 'bg-primary-500/20 text-primary-300 border-primary-500/30'
                            : 'bg-white/10 text-white/80 border-white/20 hover:bg-white/20 hover:text-white'
                    ]"
                >
                    🏍️ Motorcycles
                </button>
                <button
                    type="button"
                    @click="emit('quickFilter', 'sort_by', 'price_low')"
                    :class="
                        filters.sort_by === 'price_low'
                            ? 'bg-green-500 text-white'
                            : 'bg-white text-gray-700 hover:bg-gray-100'
                    "
                    class="px-2 py-1 border border-gray-300 rounded-full text-xs font-medium transition-colors"
                >
                    💰 Low Price
                </button>
                <button
                    type="button"
                    @click="emit('quickFilter', 'sort_by', 'popular')"
                    :class="
                        filters.sort_by === 'popular'
                            ? 'bg-orange-500 text-white'
                            : 'bg-white text-gray-700 hover:bg-gray-100'
                    "
                    class="px-2 py-1 border border-gray-300 rounded-full text-xs font-medium transition-colors"
                >
                    🔥 Popular
                </button>
            </div>
        </div>

        <!-- Basic Filters -->
        <div class="p-3">
            <form @submit.prevent="emit('applyFilters')" class="space-y-2">
                <!-- Main Filter Row -->
                <div class="grid grid-cols-1 gap-2">
                    <div>
                        <label
                            class="block text-xs font-medium text-white mb-0.5"
                            >Make</label
                        >
                        <select
                            v-model="filters.make_id"
                            @change="emit('makeChange')"
                            class="w-full p-1.5 border border-gray-300 rounded text-xs focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                        >
                            <option value="">All</option>
                            <option
                                v-for="make in filterOptions.makes"
                                :key="make.id"
                                :value="make.id"
                            >
                                {{ make.name }}
                            </option>
                        </select>
                    </div>

                    <div>
                        <label
                            class="block text-xs font-medium text-white mb-0.5"
                            >Model</label
                        >
                        <select
                            v-model="filters.model_id"
                            :disabled="!filters.make_id || loadingModels"
                            class="w-full p-1.5 border border-gray-300 rounded text-xs focus:ring-1 focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100"
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
                            <option
                                v-for="model in availableModels"
                                :key="model.id"
                                :value="model.id"
                            >
                                {{ model.name }}
                            </option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label
                            class="block text-xs font-medium text-white mb-0.5"
                            >Fuel</label
                        >
                        <select
                            v-model="filters.fuel_type_id"
                            class="w-full p-1.5 border border-gray-300 rounded text-xs focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                        >
                            <option value="">All</option>
                            <option
                                v-for="fuel in filterOptions.fuelTypes"
                                :key="fuel.id"
                                :value="fuel.id"
                            >
                                {{ fuel.name }}
                            </option>
                        </select>
                    </div>

                    <div>
                        <label
                            class="block text-xs font-medium text-white mb-0.5"
                            >Trans.</label
                        >
                        <select
                            v-model="filters.transmission_id"
                            class="w-full p-1.5 border border-gray-300 rounded text-xs focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                        >
                            <option value="">All</option>
                            <option
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
                <div class="bg-blue-50 p-2 rounded">
                    <h4 class="text-xs font-semibold text-gray-800 mb-1">
                        📅 Availability
                    </h4>
                    <div class="grid grid-cols-1 gap-1.5">
                        <div>
                            <label
                                class="block text-xs font-medium text-white mb-0.5"
                                >From</label
                            >
                            <input
                                v-model="filters.available_from"
                                type="datetime-local"
                                :min="new Date().toISOString().slice(0, 16)"
                                class="w-full p-1.5 border border-gray-300 rounded text-xs focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                            />
                        </div>
                        <div>
                            <label
                                class="block text-xs font-medium text-white mb-0.5"
                                >To</label
                            >
                            <input
                                v-model="filters.available_to"
                                type="datetime-local"
                                :min="
                                    filters.available_from ||
                                    new Date().toISOString().slice(0, 16)
                                "
                                class="w-full p-1.5 border border-gray-300 rounded text-xs focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                            />
                        </div>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex gap-1.5 pt-2 border-t">
                    <button
                        type="submit"
                        :disabled="isFiltering"
                        class="flex-1 bg-blue-600 text-white px-3 py-1.5 rounded font-medium hover:bg-blue-700 disabled:bg-blue-400 transition-colors text-xs flex items-center justify-center gap-1"
                    >
                        <svg
                            v-if="isFiltering"
                            class="animate-spin h-3 w-3"
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
                        class="px-3 py-1.5 border border-gray-300 text-white rounded font-medium hover:bg-gray-50 transition-colors text-xs"
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
    showAdvancedFilters: Boolean,
});

const emit = defineEmits([
    "quickFilter",
    "applyFilters",
    "makeChange",
    "toggleAdvanced",
    "resetFilters",
]);
</script>
