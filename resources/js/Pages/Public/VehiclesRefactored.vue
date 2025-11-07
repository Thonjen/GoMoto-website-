<template>
    <AuthenticatedLayout>
        <div class="py-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <!-- Main Layout: Vehicles on Left, Filters on Right -->
                <div class="flex flex-col lg:flex-row gap-8">
                    <!-- Left Section: Vehicles Content -->
                    <div class="flex-1 lg:w-3/4">
                        <!-- Active Filters Display -->
                        <div v-if="hasActiveFilters" class="mb-6">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="text-sm font-medium text-white"
                                    >Active filters:</span
                                >
                                <span
                                    v-for="filter in activeFilters"
                                    :key="filter.key"
                                    class="inline-flex items-center gap-1 px-3 py-1 bg-primary-500/20 text-primary-300 text-sm rounded-full backdrop-blur-sm border border-primary-400/30"
                                >
                                    {{ filter.label }}
                                    <button
                                        @click="removeFilter(filter.key)"
                                        class="ml-1 text-primary-300 hover:text-white"
                                    >
                                        ×
                                    </button>
                                </span>
                            </div>
                        </div>

                        <!-- Results Summary & View Toggle -->
                        <div class="mb-6">
                            <div
                                class="flex flex-col lg:flex-row lg:justify-between lg:items-center gap-4"
                            >
                                <div>
                                    <h2
                                        class="text-2xl font-bold text-white"
                                    >
                                        {{ vehicles.total }} Vehicle{{
                                            vehicles.total !== 1 ? "s" : ""
                                        }}
                                        Found
                                    </h2>
                                    <p class="text-white/70">
                                        Showing {{ vehicles.from }}-{{
                                            vehicles.to
                                        }}
                                        of {{ vehicles.total }} results
                                    </p>
                                </div>
                                <div
                                    class="flex flex-col sm:flex-row items-start sm:items-center gap-3"
                                >
                                    <!-- View Toggle -->
                                    <div
                                        class="flex bg-white/10 backdrop-blur-sm rounded-lg p-1 border border-white/20"
                                    >
                                        <button
                                            @click="viewMode = 'list'"
                                            :class="
                                                viewMode === 'list'
                                                    ? 'bg-white/20 text-white shadow-glow'
                                                    : 'text-white/70 hover:text-white'
                                            "
                                            class="px-3 py-2 rounded-md text-sm font-medium transition-all duration-300"
                                        >
                                            📋 List
                                        </button>
                                        <button
                                            @click="viewMode = 'map'"
                                            :class="
                                                viewMode === 'map'
                                                    ? 'bg-white/20 text-white shadow-glow'
                                                    : 'text-white/70 hover:text-white'
                                            "
                                            class="px-3 py-2 rounded-md text-sm font-medium transition-all duration-300"
                                        >
                                            🗺️ Map
                                        </button>
                                    </div>

                                    <!-- Sort Dropdown (only show in list mode) -->
                                    <div v-if="viewMode === 'list'" class="flex items-center gap-2">
                                    <span class="text-sm text-white/70 font-medium">Sort by:</span>
                                    <select
                                        v-model="filters.sort_by"
                                        @change="applyFilters"
                                        class="px-4 py-2 bg-black/60 border border-white/20 rounded-lg text-sm text-white backdrop-blur-sm focus:ring-2 focus:ring-white/40 focus:border-white/40 min-w-48 appearance-none"
                                    >
                                        <option
                                        value="latest"
                                        class="bg-black/80 text-white hover:bg-white/10 active:bg-white/20"
                                        >
                                        Latest Added
                                        </option>
                                        <option
                                        value="price_low"
                                        class="bg-black/80 text-white hover:bg-white/10 active:bg-white/20"
                                        >
                                        Price: Low to High
                                        </option>
                                        <option
                                        value="price_high"
                                        class="bg-black/80 text-white hover:bg-white/10 active:bg-white/20"
                                        >
                                        Price: High to Low
                                        </option>
                                        <option
                                        value="popular"
                                        class="bg-black/80 text-white hover:bg-white/10 active:bg-white/20"
                                        >
                                        Most Popular
                                        </option>
                                        <option
                                        value="rating"
                                        class="bg-black/80 text-white hover:bg-white/10 active:bg-white/20"
                                        >
                                        Highest Rated
                                        </option>
                                    </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Map View -->
                        <VehicleMap
                            v-if="viewMode === 'map'"
                            :vehicles="vehicles.data"
                            @view-detail="goToDetail"
                        />

                        <!-- Featured Vehicles Section (only show when no filters are applied) -->
                        <div
                            v-if="
                                viewMode === 'list' &&
                                !hasActiveFilters &&
                                featuredVehicles?.length
                            "
                            class="mb-8"
                        >
                            <div
                                class="glass-card p-6 shadow-glow border border-white/20"
                            >
                                <h3 class="text-xl font-bold mb-4 text-white">
                                    🌟 Featured Vehicles
                                </h3>
                                <div
                                    class="grid grid-cols-1 md:grid-cols-2 gap-4"
                                >
                                    <div
                                        v-for="vehicle in featuredVehicles"
                                        :key="`featured-${vehicle.id}`"
                                        @click="goToDetail(vehicle.id)"
                                        class="bg-white/10 backdrop-blur-sm rounded-lg p-4 cursor-pointer hover:bg-white/20 transition-all duration-300 border border-white/20"
                                    >
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-lg flex items-center justify-center border border-white/30"
                                            >
                                                {{
                                                    vehicle.type?.category ===
                                                    "car"
                                                        ? "🚗"
                                                        : "🏍️"
                                                }}
                                            </div>
                                            <div>
                                                <h4 class="font-semibold text-white">
                                                    {{ vehicle.make?.name }}
                                                    {{ vehicle.model?.name }}
                                                </h4>
                                                <p
                                                    v-if="vehicle.owner"
                                                    class="text-xs text-white/70"
                                                >
                                                    Owner:
                                                    {{ vehicle.owner.name }}
                                                </p>
                                                <div
                                                    class="flex items-center justify-between"
                                                >
                                                    <p
                                                        class="text-sm text-white/90"
                                                    >
                                                        From ₱{{
                                                            Math.min(
                                                                ...(vehicle.pricing_tiers?.map(
                                                                    (t) =>
                                                                        parseFloat(
                                                                            t.price
                                                                        )
                                                                ) || [0])
                                                            )
                                                        }}
                                                    </p>
                                                    <!-- Add rating display -->
                                                    <div
                                                        v-if="
                                                            vehicle.ratings_avg_rating >
                                                            0
                                                        "
                                                        class="flex items-center space-x-1"
                                                    >
                                                        <span
                                                            class="text-yellow-400"
                                                            >★</span
                                                        >
                                                        <span class="text-xs">{{
                                                            parseFloat(
                                                                vehicle.ratings_avg_rating
                                                            ).toFixed(1)
                                                        }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Vehicle Grid -->
                        <VehicleGrid
                            v-if="
                                viewMode === 'list' && vehicles.data.length > 0
                            "
                            :vehicles="vehicles.data"
                            @view-detail="goToDetail"
                        />

                        <!-- No Results State -->
                        <div
                            v-else-if="
                                viewMode === 'list' &&
                                vehicles.data.length === 0
                            "
                            class="text-center py-16"
                        >
                            <div
                                class="mx-auto w-32 h-32 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mb-6"
                            >
                                <svg
                                    class="w-16 h-16 text-gray-400"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
                                    ></path>
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold text-white mb-3">
                                No vehicles match your search
                            </h3>
                            <div class="max-w-md mx-auto">
                                <p class="text-white/70 mb-6">
                                    Try adjusting your filters or search terms
                                    to find more vehicles.
                                </p>

                                <!-- Suggestions -->
                                <div class="mb-6">
                                    <p class="text-sm text-white/70 mb-2">
                                        Try searching for:
                                    </p>
                                    <div
                                        class="flex flex-wrap justify-center gap-2"
                                    >
                                        <button
                                            v-for="suggestion in [
                                                'Honda',
                                                'Toyota',
                                                'Yamaha',
                                                'Automatic',
                                                'Manual',
                                            ]"
                                            :key="suggestion"
                                            @click="quickSearch(suggestion)"
                                            class="px-3 py-1 bg-white/10 text-white/80 rounded-full text-sm hover:bg-white/20 transition-all duration-300 backdrop-blur-sm border border-white/20"
                                        >
                                            {{ suggestion }}
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div
                                class="flex flex-col sm:flex-row gap-3 justify-center"
                            >
                                <button
                                    @click="resetFilters"
                                    class="btn-primary px-8 py-3 font-semibold"
                                >
                                    Show All Vehicles
                                </button>
                                <button
                                    @click="
                                        showAdvancedFilters =
                                            !showAdvancedFilters
                                    "
                                    class="btn-glass text-white px-8 py-3 font-semibold text-white"
                                >
                                    {{
                                        showAdvancedFilters ? "Hide" : "Show"
                                    }}
                                    Advanced Filters
                                </button>
                            </div>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6 flex justify-center gap-4">
                            <button
                                v-if="vehicles.prev_page_url"
                                @click="goTo(vehicles.prev_page_url)"
                                class="btn-glass px-4 py-2 font-medium"
                            >
                                Prev
                            </button>
                            <button
                                v-if="vehicles.next_page_url"
                                @click="goTo(vehicles.next_page_url)"
                                class="btn-glass px-4 py-2 font-medium"
                            >
                                Next
                            </button>
                        </div>
                    </div>

                    <!-- Right Section: Filter Sidebar -->
                    <div class="lg:w-1/4">
                        <div class="sticky top-6">
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
                                @toggle-advanced="
                                    showAdvancedFilters = !showAdvancedFilters
                                "
                                @reset-filters="resetFilters"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { router } from "@inertiajs/vue3";
import { ref, computed, watch, onMounted } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import FilterSection from "@/Components/Vehicle/FilterSection.vue";
import VehicleMap from "@/Components/Vehicle/VehicleMap.vue";
import VehicleGrid from "@/Components/Vehicle/VehicleGrid.vue";
import RatingDisplay from "@/Components/Vehicle/RatingDisplay.vue";
import SaveButton from "@/Components/Vehicle/SaveButton.vue";

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
const viewMode = ref("list"); // 'list' or 'map'

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
        if (key === "sort_by" && value === "latest") return false;
        if (key === "per_page" && value === "9") return false;
        return value !== "" && value !== null && value !== undefined;
    });
});

const activeFilters = computed(() => {
    const active = [];
    const filterLabels = {
        search: "Search",
        category: "Category",
        make_id: "Make",
        model_id: "Model",
        type_id: "Sub-Type",
        fuel_type_id: "Fuel Type",
        transmission_id: "Transmission",
        color: "Color",
        price_from: "Min Price",
        price_to: "Max Price",
        available_from: "Available From",
        available_to: "Available To",
        sort_by: "Sort",
    };

    for (const [key, value] of Object.entries(filters.value)) {
        if (value && value !== "" && value !== "latest" && value !== "9") {
            let label = filterLabels[key] || key;

            // Get human-readable values
            if (key === "make_id") {
                const make = props.filterOptions.makes.find(
                    (m) => m.id == value
                );
                label += `: ${make?.name || value}`;
            } else if (key === "model_id") {
                const model = availableModels.value.find((m) => m.id == value);
                label += `: ${model?.name || value}`;
            } else if (key === "fuel_type_id") {
                const fuel = props.filterOptions.fuelTypes.find(
                    (f) => f.id == value
                );
                label += `: ${fuel?.name || value}`;
            } else if (key === "transmission_id") {
                const trans = props.filterOptions.transmissions.find(
                    (t) => t.id == value
                );
                label += `: ${trans?.name || value}`;
            } else if (key === "type_id") {
                const type = props.filterOptions.types.find(
                    (t) => t.id == value
                );
                label += `: ${type?.sub_type || value}`;
            } else if (key === "sort_by") {
                const sortLabels = {
                    latest: "Latest",
                    price_low: "Price: Low to High",
                    price_high: "Price: High to Low",
                    popular: "Most Popular",
                    rating: "Highest Rated",
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
        const selectedMake = props.filterOptions.makes.find(
            (m) => m.id == filters.value.make_id
        );
        if (selectedMake && selectedMake.models) {
            availableModels.value = selectedMake.models;
        }
    } catch (error) {
        console.error("Failed to load models:", error);
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
    if (key === "make_id") {
        filters.value.model_id = "";
        availableModels.value = [];
    }
    applyFilters();
}

function applyFilters() {
    isFiltering.value = true;

    // Clean up empty filters
    const cleanFilters = Object.fromEntries(
        Object.entries(filters.value).filter(
            ([key, value]) =>
                value !== "" && value !== null && value !== undefined
        )
    );

    router.get("/vehicles", cleanFilters, {
        preserveState: true,
        replace: true,
        onFinish: () => {
            isFiltering.value = false;
        },
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

function goToDetail(vehicleId) {
    router.visit(`/vehicles/${vehicleId}`);
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
