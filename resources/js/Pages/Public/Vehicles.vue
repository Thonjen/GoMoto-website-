<template>
    <AuthenticatedLayout>
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <h1 class="text-3xl font-bold text-gray-800 mb-6">
                    Available Vehicles
                </h1>

                <!-- Filter Form -->
                <div class="bg-white p-6 rounded-lg shadow-md mb-8">
                    <form
                        @submit.prevent="applyFilters"
                        class="grid grid-cols-1 md:grid-cols-3 gap-6"
                    >
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-2"
                                >Brand</label
                            >
                            <select
                                v-model="filters.brand_id"
                                class="w-full p-2 border border-gray-300 rounded-md focus:ring-primary-400 focus:border-primary-400"
                            >
                                <option value="">All Brands</option>
                                <option
                                    v-for="b in brands"
                                    :key="b.id"
                                    :value="b.id"
                                >
                                    {{ b.name }}
                                </option>
                            </select>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-2"
                                >Fuel Type</label
                            >
                            <select
                                v-model="filters.fuel_type_id"
                                class="w-full p-2 border border-gray-300 rounded-md focus:ring-primary-400 focus:border-primary-400"
                            >
                                <option value="">All Fuel Types</option>
                                <option
                                    v-for="f in fuelTypes"
                                    :key="f.id"
                                    :value="f.id"
                                >
                                    {{ f.name }}
                                </option>
                            </select>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-2"
                                >Vehicle Type</label
                            >
                            <select
                                v-model="filters.type_id"
                                class="w-full p-2 border border-gray-300 rounded-md focus:ring-primary-400 focus:border-primary-400"
                            >
                                <option value="">All Types</option>
                                <option
                                    v-for="t in types"
                                    :key="t.id"
                                    :value="t.id"
                                >
                                    {{ t.category }}
                                </option>
                            </select>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-2"
                                >Color</label
                            >
                            <input
                                type="text"
                                v-model="filters.color"
                                class="w-full p-2 border border-gray-300 rounded-md focus:ring-primary-400 focus:border-primary-400"
                                placeholder="Color"
                            />
                        </div>

                        <div class="md:col-span-3 flex justify-end gap-3">
                            <button
                                type="submit"
                                class="bg-primary-600 text-white px-6 py-2 rounded-md font-semibold hover:bg-primary-700 transition-colors flex items-center gap-2"
                            >
                                Apply Filters
                            </button>
                            <button
                                type="button"
                                @click="resetFilters"
                                class="bg-gray-300 text-gray-800 px-6 py-2 rounded-md font-semibold hover:bg-gray-400 transition-colors"
                            >
                                Reset
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Vehicles List -->
                <div
                    class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6"
                >
                    <div
                        v-for="vehicle in vehicles.data"
                        :key="vehicle.id"
                        class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300 relative"
                    >
                        <img
                            v-if="vehicle.main_photo_url"
                            :src="vehicle.main_photo_url"
                            alt="Vehicle photo"
                            class="w-full h-48 object-cover"
                        />

                        <!-- Overlay -->
                        <div
                            v-if="!vehicle.is_available || vehicle.is_booked"
                            class="absolute inset-0 bg-black bg-opacity-60 flex items-center justify-center text-white text-lg font-bold z-10"
                        >
                            {{
                                !vehicle.is_available ? "Unavailable" : "Booked"
                            }}
                        </div>

                        <div class="p-4">
                            <h3
                                class="text-xl font-semibold text-gray-800 mb-1"
                            >
                                {{ vehicle.license_plate }}
                            </h3>
                            <p class="text-gray-600 mb-1">
                                Brand: {{ vehicle.brand?.name }}
                            </p>
                            <p class="text-gray-600 mb-1">
                                Type: {{ vehicle.type?.category }}
                            </p>
                            <!-- Show only the lowest price tier if available -->
                            <div
                                v-if="vehicle.pricing_tiers?.length"
                                class="mb-2"
                            >
                                <span class="font-semibold text-gray-700"
                                    >From:</span
                                >
                                <span class="text-green-700 font-bold">
                                    ₱{{
                                        Math.min(
                                            ...vehicle.pricing_tiers.map(
                                                (t) => t.price
                                            )
                                        )
                                    }}
                                </span>
                                <span class="text-gray-500 text-sm"
                                    >/
                                    {{
                                        vehicle.pricing_tiers[0].duration_unit.replace(
                                            /s$/,
                                            ""
                                        )
                                    }}</span
                                >
                            </div>
                            <p class="mb-1">
                                Available:
                                <span
                                    :class="
                                        vehicle.is_available
                                            ? 'text-green-600'
                                            : 'text-red-600'
                                    "
                                >
                                    {{ vehicle.is_available ? "Yes" : "No" }}
                                </span>
                            </p>

                            <!-- View Details Button -->
                            <button
                                @click="goToDetail(vehicle.id)"
                                class="mt-2 w-full bg-green-600 text-white px-4 py-2 rounded-md font-semibold hover:bg-green-700 transition-colors"
                            >
                                View Details
                            </button>
                        </div>
                    </div>

                    <p
                        v-if="vehicles.data.length === 0"
                        class="col-span-full text-center text-gray-600"
                    >
                        No vehicles found.
                    </p>
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
    </AuthenticatedLayout>
</template>

<script setup>
import { router } from "@inertiajs/vue3";
import { ref } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

const props = defineProps({
    vehicles: Object,
    brands: Array,
    types: Array,
    fuelTypes: Array,
    filters: Object,
});

const filters = ref({
    brand_id: props.filters?.brand_id || "",
    fuel_type_id: props.filters?.fuel_type_id || "",
    type_id: props.filters?.type_id || "",
    color: props.filters?.color || "",
});

function applyFilters() {
    router.get(
        "/vehicles",
        { ...filters.value },
        { preserveState: true, replace: true }
    );
}
function resetFilters() {
    filters.value = { brand_id: "", fuel_type_id: "", type_id: "", color: "" };
    applyFilters();
}
function goTo(url) {
    router.visit(url, { preserveState: true });
}
// Redirect to VehicleDetail page
function goToDetail(vehicleId) {
    router.visit(`/vehicles/${vehicleId}`);
}
</script>
