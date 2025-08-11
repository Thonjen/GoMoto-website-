<template>
    <GuestLayout>
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Filter Section -->
                <FilterSection 
                    :filters="filters"
                    :filter-options="filterOptions"
                    :available-models="availableModels"
                    :loading-models="loadingModels"
                    :is-filtering="isFiltering"
                    :show-advanced-filters="showAdvancedFilters"
                    @quick-filter="quickFilter"
                    @apply-filters="applyFilters"
                    @make-change="onMakeChange"
                    @toggle-advanced="showAdvancedFilters = !showAdvancedFilters"
                    @reset-filters="resetFilters"
                />

                <!-- Active Filters Display -->
                <div v-if="hasActiveFilters" class="mb-6">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="text-sm font-medium text-gray-700">Active filters:</span>
                        <span
                            v-for="filter in activeFilters"
                            :key="filter.key"
                            class="inline-flex items-center gap-1 px-3 py-1 bg-blue-100 text-blue-800 text-sm rounded-full"
                        >
                            {{ filter.label }}
                            <button
                                @click="removeFilter(filter.key)"
                                class="ml-1 text-blue-600 hover:text-blue-800"
                            >
                                ×
                            </button>
                        </span>
                    </div>
                </div>

                <!-- Results Summary & View Toggle -->
                <div class="mb-6">
                    <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center gap-4">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">
                                {{ vehicles.total }} Vehicle{{ vehicles.total !== 1 ? 's' : '' }} Found
                            </h2>
                            <p class="text-gray-600">
                                Showing {{ vehicles.from }}-{{ vehicles.to }} of {{ vehicles.total }} results
                                <span class="text-blue-600 font-medium">• Log in to book vehicles</span>
                            </p>
                        </div>
                        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                            <!-- View Toggle -->
                            <div class="flex bg-gray-100 rounded-lg p-1">
                                <button
                                    @click="viewMode = 'list'"
                                    :class="viewMode === 'list' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600 hover:text-gray-900'"
                                    class="px-3 py-2 rounded-md text-sm font-medium transition-colors"
                                >
                                    📋 List
                                </button>
                                <button
                                    @click="viewMode = 'map'"
                                    :class="viewMode === 'map' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600 hover:text-gray-900'"
                                    class="px-3 py-2 rounded-md text-sm font-medium transition-colors"
                                >
                                    🗺️ Map
                                </button>
                            </div>
                            
                            <!-- Login/Register Buttons -->
                            <div class="flex gap-2">
                                <Link href="/login" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
                                    Log In
                                </Link>
                                <Link href="/register" class="border border-blue-600 text-blue-600 px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-50 transition-colors">
                                    Register
                                </Link>
                            </div>
                            
                            <!-- Sort Dropdown -->
                            <div class="flex items-center gap-2">
                                <span class="text-sm text-gray-600 font-medium">Sort by:</span>
                                <select
                                    v-model="filters.sort_by"
                                    @change="applyFilters"
                                    class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 min-w-48"
                                >
                                    <option value="latest">Latest Added</option>
                                    <option value="price_low">Price: Low to High</option>
                                    <option value="price_high">Price: High to Low</option>
                                    <option value="popular">Most Popular</option>
                                    <option value="rating">Highest Rated</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Map View -->
                <VehicleMap 
                    v-if="viewMode === 'map'"
                    :vehicles="vehicles.data"
                    @view-detail="handleViewDetail"
                />

                <!-- Featured Vehicles Section (only show when no filters are applied) -->
                <div v-if="viewMode === 'list' && !hasActiveFilters && featuredVehicles?.length" class="mb-8">
                    <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg p-6 text-white">
                        <h3 class="text-xl font-bold mb-4">🌟 Featured Vehicles</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div
                                v-for="vehicle in featuredVehicles"
                                :key="`featured-${vehicle.id}`"
                                @click="handleViewDetail(vehicle.id)"
                                class="bg-white bg-opacity-20 backdrop-blur-sm rounded-lg p-4 cursor-pointer hover:bg-opacity-30 transition-all"
                            >
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 bg-white bg-opacity-30 rounded-lg flex items-center justify-center">
                                        {{ vehicle.type?.category === 'car' ? '🚗' : '🏍️' }}
                                    </div>
                                    <div>
                                        <h4 class="font-semibold">{{ vehicle.make?.name }} {{ vehicle.model?.name }}</h4>
                                        <p class="text-sm opacity-90">
                                            From ₱{{ Math.min(...(vehicle.pricing_tiers?.map(t => parseFloat(t.price)) || [0])) }}
                                        </p>
                                        <p class="text-xs opacity-75 mt-1">Click to view details & book</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Vehicle Grid with Login Prompt -->
                <div 
                    v-if="viewMode === 'list' && vehicles.data.length > 0"
                    class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6"
                >
                    <div
                        v-for="vehicle in vehicles.data"
                        :key="vehicle.id"
                        class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-all duration-300 relative group cursor-pointer"
                        @click="handleViewDetail(vehicle.id)"
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
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 002 2z"></path>
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

                            <!-- Login Required Overlay -->
                            <div class="absolute inset-0 bg-blue-600 bg-opacity-0 group-hover:bg-opacity-10 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300">
                                <div class="bg-white text-blue-600 px-4 py-2 rounded-full text-sm font-semibold shadow-lg transform translate-y-4 group-hover:translate-y-0 transition-transform">
                                    Click to view details
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
                            <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
                                <div class="flex items-center gap-4">
                                    <div class="flex items-center gap-1">
                                        <span>⛽</span>
                                        <span>{{ vehicle.fuelType?.name || 'N/A' }}</span>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <span>⚙️</span>
                                        <span>{{ vehicle.transmission?.name || 'Manual' }}</span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1 text-blue-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <span class="truncate font-medium">{{ vehicle.location_name || 'Surigao del Norte' }}</span>
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
                                    <span class="text-sm text-gray-500">/day</span>
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
                                    @click.stop="handleViewDetail(vehicle.id)"
                                >
                                    Login to View Details & Book
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

                <!-- No Results State -->
                <div
                    v-else-if="viewMode === 'list' && vehicles.data.length === 0"
                    class="text-center py-16"
                >
                    <div class="mx-auto w-32 h-32 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mb-6">
                        <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">No vehicles match your search</h3>
                    <div class="max-w-md mx-auto">
                        <p class="text-gray-600 mb-6">
                            Try adjusting your filters or search terms to find more vehicles.
                        </p>
                        
                        <!-- Suggestions -->
                        <div class="mb-6">
                            <p class="text-sm text-gray-600 mb-2">Try searching for:</p>
                            <div class="flex flex-wrap justify-center gap-2">
                                <button
                                    v-for="suggestion in ['Honda', 'Toyota', 'Yamaha', 'Automatic', 'Manual']"
                                    :key="suggestion"
                                    @click="quickSearch(suggestion)"
                                    class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm hover:bg-gray-200 transition-colors"
                                >
                                    {{ suggestion }}
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row gap-3 justify-center">
                        <button
                            @click="resetFilters"
                            class="bg-blue-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors"
                        >
                            Show All Vehicles
                        </button>
                        <button
                            @click="showAdvancedFilters = !showAdvancedFilters"
                            class="border border-gray-300 text-gray-700 px-8 py-3 rounded-lg font-semibold hover:bg-gray-50 transition-colors"
                        >
                            {{ showAdvancedFilters ? 'Hide' : 'Show' }} Advanced Filters
                        </button>
                    </div>
                </div>

                <!-- Pagination -->
                <div class="mt-6 flex justify-center gap-4">
                    <button
                        v-if="vehicles.prev_page_url"
                        @click="goTo(vehicles.prev_page_url)"
                        class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300 transition-colors"
                    >
                        Prev
                    </button>
                    <button
                        v-if="vehicles.next_page_url"
                        @click="goTo(vehicles.next_page_url)"
                        class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300 transition-colors"
                    >
                        Next
                    </button>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>

<script setup>
import { router, Link } from "@inertiajs/vue3";
import { ref, computed, watch, onMounted } from "vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import FilterSection from "@/Components/Vehicle/FilterSection.vue";
import VehicleMap from "@/Components/Vehicle/VehicleMap.vue";

const props = defineProps({
    vehicles: Object,
    filterOptions: Object,
    filters: Object,
    featuredVehicles: Array,
});

const showAdvancedFilters = ref(false);
const isFiltering = ref(false);
const loadingModels = ref(false);
const availableModels = ref([]);
const viewMode = ref('list'); // 'list' or 'map'

const filters = ref({
    search: props.filters?.search || "",
    category: props.filters?.category || "",
    make_id: props.filters?.make_id || "",
    model_id: props.filters?.model_id || "",
    type_id: props.filters?.type_id || "",
    fuel_type_id: props.filters?.fuel_type_id || "",
    transmission_id: props.filters?.transmission_id || "",
    color: props.filters?.color || "",
    price_from: props.filters?.price_from || "",
    price_to: props.filters?.price_to || "",
    available_from: props.filters?.available_from || "",
    available_to: props.filters?.available_to || "",
    sort_by: props.filters?.sort_by || "latest",
    per_page: props.filters?.per_page || "9",
});

// Computed properties
const hasActiveFilters = computed(() => {
    return Object.entries(filters.value).some(([key, value]) => {
        if (key === 'sort_by' && value === 'latest') return false;
        if (key === 'per_page' && value === '9') return false;
        return value !== "" && value !== null && value !== undefined;
    });
});

const activeFilters = computed(() => {
    const active = [];
    const filterLabels = {
        search: 'Search',
        category: 'Category',
        make_id: 'Make',
        model_id: 'Model',
        type_id: 'Sub-Type',
        fuel_type_id: 'Fuel Type',
        transmission_id: 'Transmission',
        color: 'Color',
        price_from: 'Min Price',
        price_to: 'Max Price',
        available_from: 'Available From',
        available_to: 'Available To',
        sort_by: 'Sort',
    };

    for (const [key, value] of Object.entries(filters.value)) {
        if (value && value !== "" && value !== "latest" && value !== "9") {
            let label = filterLabels[key] || key;
            
            // Get human-readable values
            if (key === 'make_id') {
                const make = props.filterOptions.makes.find(m => m.id == value);
                label += `: ${make?.name || value}`;
            } else if (key === 'model_id') {
                const model = availableModels.value.find(m => m.id == value);
                label += `: ${model?.name || value}`;
            } else if (key === 'fuel_type_id') {
                const fuel = props.filterOptions.fuelTypes.find(f => f.id == value);
                label += `: ${fuel?.name || value}`;
            } else if (key === 'transmission_id') {
                const trans = props.filterOptions.transmissions.find(t => t.id == value);
                label += `: ${trans?.name || value}`;
            } else if (key === 'type_id') {
                const type = props.filterOptions.types.find(t => t.id == value);
                label += `: ${type?.sub_type || value}`;
            } else if (key === 'sort_by') {
                const sortLabels = {
                    latest: 'Latest',
                    price_low: 'Price: Low to High',
                    price_high: 'Price: High to Low',
                    popular: 'Most Popular',
                    rating: 'Highest Rated'
                };
                label = sortLabels[value] || value;
            } else {
                label += `: ${value}`;
            }
            
            active.push({ key, label });
        }
    }
    
    return active;
});

// Methods
async function onMakeChange() {
    filters.value.model_id = "";
    availableModels.value = [];
    
    if (!filters.value.make_id) return;
    
    loadingModels.value = true;
    try {
        const selectedMake = props.filterOptions.makes.find(m => m.id == filters.value.make_id);
        if (selectedMake && selectedMake.models) {
            availableModels.value = selectedMake.models;
        }
    } catch (error) {
        console.error('Failed to load models:', error);
    } finally {
        loadingModels.value = false;
    }
}

function quickSearch(searchTerm) {
    filters.value.search = searchTerm;
    applyFilters();
}

function quickFilter(key, value) {
    if (filters.value[key] === value) {
        filters.value[key] = ""; // Toggle off if already selected
    } else {
        filters.value[key] = value;
    }
    applyFilters();
}

function removeFilter(key) {
    filters.value[key] = "";
    if (key === 'make_id') {
        filters.value.model_id = "";
        availableModels.value = [];
    }
    applyFilters();
}

function applyFilters() {
    isFiltering.value = true;
    
    // Clean up empty filters
    const cleanFilters = Object.fromEntries(
        Object.entries(filters.value).filter(([key, value]) => 
            value !== "" && value !== null && value !== undefined
        )
    );
    
    router.get("/vehicles", cleanFilters, {
        preserveState: true,
        replace: true,
        onFinish: () => {
            isFiltering.value = false;
        }
    });
}

function resetFilters() {
    filters.value = {
        search: "",
        category: "",
        make_id: "",
        model_id: "",
        type_id: "",
        fuel_type_id: "",
        transmission_id: "",
        color: "",
        price_from: "",
        price_to: "",
        available_from: "",
        available_to: "",
        sort_by: "latest",
        per_page: "9",
    };
    availableModels.value = [];
    applyFilters();
}

function goTo(url) {
    router.visit(url, { preserveState: true });
}

function handleViewDetail(vehicleId) {
    // Store the intended destination
    sessionStorage.setItem('intended_vehicle', vehicleId.toString());
    // Redirect to login with return URL
    router.visit('/login', {
        data: {
            intended: `/vehicles/${vehicleId}`
        }
    });
}

// Load models if make is pre-selected
onMounted(() => {
    if (filters.value.make_id) {
        onMakeChange();
    }
});

// Watch for make changes
watch(() => filters.value.make_id, onMakeChange);
</script>
