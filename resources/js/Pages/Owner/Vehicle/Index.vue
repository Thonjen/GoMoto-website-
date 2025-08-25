<template>
    <OwnerLayout>
        <div class="min-h-screen bg-gray-50 py-8">
            <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header Section -->
                <div class="mb-8">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">My Vehicles</h1>
                            <p class="text-gray-600 mt-1">Manage your rental fleet</p>
                        </div>
                        <div class="flex gap-3">
                            <!-- Column Visibility Toggle -->
                            <div class="relative">
                                <button
                                    @click="showColumnOptions = !showColumnOptions"
                                    class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-lg font-medium transition-colors flex items-center gap-2"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"/>
                                    </svg>
                                    View Options
                                </button>
                                
                                <!-- Column Options Dropdown -->
                                <div v-if="showColumnOptions" class="absolute right-0 mt-2 w-64 bg-white rounded-lg shadow-lg border border-gray-200 z-10">
                                    <div class="p-4">
                                        <h3 class="text-sm font-semibold text-gray-900 mb-3">Show Columns</h3>
                                        <div class="space-y-2">
                                            <label v-for="column in availableColumns" :key="column.key" class="flex items-center">
                                                <input
                                                    type="checkbox"
                                                    v-model="visibleColumns[column.key]"
                                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                                >
                                                <span class="ml-2 text-sm text-gray-700">{{ column.label }}</span>
                                            </label>
                                        </div>
                                        <div class="mt-4 pt-3 border-t border-gray-200">
                                            <button
                                                @click="resetColumns"
                                                class="text-xs text-blue-600 hover:text-blue-800"
                                            >
                                                Reset to Default
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <Link
                                href="/owner/vehicles/create"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition-colors flex items-center gap-2 shadow-lg"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                Add New Vehicle
                            </Link>
                        </div>
                    </div>

                    <!-- Stats Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-6">
                        <div class="bg-white rounded-lg p-4 shadow-sm border">
                            <div class="flex items-center">
                                <div class="p-2 bg-blue-100 rounded-lg">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-600">Total Vehicles</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ vehicles.total || vehicles.data.length }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-white rounded-lg p-4 shadow-sm border">
                            <div class="flex items-center">
                                <div class="p-2 bg-green-100 rounded-lg">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-600">Available</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ availableCount }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-lg p-4 shadow-sm border">
                            <div class="flex items-center">
                                <div class="p-2 bg-yellow-100 rounded-lg">
                                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-600">Rented</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ rentedCount }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-lg p-4 shadow-sm border">
                            <div class="flex items-center">
                                <div class="p-2 bg-purple-100 rounded-lg">
                                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-600">Revenue</p>
                                    <p class="text-2xl font-bold text-gray-900">₱{{ totalRevenue.toLocaleString() }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Table Section -->
                <div v-if="vehicles.data.length > 0" class="bg-white rounded-lg shadow-sm border overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <!-- Always show Vehicle column -->
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vehicle</th>
                                    <th v-if="visibleColumns.details" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Details</th>
                                    <th v-if="visibleColumns.specifications" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Specifications</th>
                                    <th v-if="visibleColumns.status" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th v-if="visibleColumns.pricing" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pricing</th>
                                    <th v-if="visibleColumns.location" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                                    <!-- Always show Actions column -->
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="vehicle in vehicles.data" :key="vehicle.id" class="hover:bg-gray-50">
                                    <!-- Vehicle Image & Basic Info - Always visible -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-20 w-28">
                                                <img
                                                    v-if="vehicle.main_photo_url"
                                                    :src="vehicle.main_photo_url"
                                                    :alt="`${vehicle.make?.name} ${vehicle.model?.name}`"
                                                    class="h-20 w-28 object-cover rounded-lg cursor-pointer hover:opacity-80 transition-opacity"
                                                    @click="openImageModal(vehicle.main_photo_url, `${vehicle.make?.name} ${vehicle.model?.name}`)"
                                                />
                                                <div v-else class="h-20 w-28 bg-gray-200 rounded-lg flex items-center justify-center cursor-pointer hover:bg-gray-300 transition-colors">
                                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-bold text-gray-900">
                                                    {{ vehicle.make?.name || 'Unknown' }} {{ vehicle.model?.name || vehicle.type?.sub_type || 'Model' }}
                                                </div>
                                                <div class="text-sm text-gray-500">{{ vehicle.year }}</div>
                                                <div v-if="vehicle.license_plate" class="text-xs font-mono bg-gray-100 px-2 py-1 rounded mt-1 inline-block">
                                                    {{ vehicle.license_plate }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Details -->
                                    <td v-if="visibleColumns.details" class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ vehicle.type?.sub_type || 'N/A' }}</div>
                                        <div class="text-sm text-gray-500">{{ vehicle.color }}</div>
                                        <div v-if="vehicle.description" class="text-xs text-gray-400 max-w-xs truncate mt-1">
                                            {{ vehicle.description }}
                                        </div>
                                    </td>

                                    <!-- Specifications -->
                                    <td v-if="visibleColumns.specifications" class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm">
                                            <div class="flex items-center gap-1 mb-1">
                                                <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                                </svg>
                                                <span class="text-gray-600 text-xs">{{ vehicle.fuel_type?.name || 'N/A' }}</span>
                                            </div>
                                            <div class="flex items-center gap-1">
                                                <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                </svg>
                                                <span class="text-gray-600 text-xs">{{ vehicle.transmission?.name || 'Manual' }}</span>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Status -->
                                    <td v-if="visibleColumns.status" class="px-6 py-4 whitespace-nowrap">
                                        <span 
                                            :class="vehicle.is_available 
                                                ? 'bg-green-100 text-green-800 border-green-200' 
                                                : 'bg-red-100 text-red-800 border-red-200'"
                                            class="inline-flex px-2 py-1 text-xs font-semibold rounded-full border"
                                        >
                                            {{ vehicle.is_available ? 'Available' : 'Rented' }}
                                        </span>
                                    </td>

                                    <!-- Pricing -->
                                    <td v-if="visibleColumns.pricing" class="px-6 py-4 whitespace-nowrap">
                                        <div v-if="vehicle.pricing_tiers?.length" class="text-sm">
                                            <div class="font-semibold text-green-600">
                                                ₱{{ Math.min(...vehicle.pricing_tiers.map(t => parseFloat(t.price))) }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                /{{ vehicle.pricing_tiers.find(t => t.price == Math.min(...vehicle.pricing_tiers.map(pt => parseFloat(pt.price)))).duration_unit }}
                                            </div>
                                            <div class="text-xs text-gray-400">
                                                {{ vehicle.pricing_tiers.length }} tier{{ vehicle.pricing_tiers.length > 1 ? 's' : '' }}
                                            </div>
                                        </div>
                                        <div v-else class="text-sm text-gray-400">No pricing set</div>
                                    </td>

                                    <!-- Location -->
                                    <td v-if="visibleColumns.location" class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 max-w-xs">
                                            <div class="flex items-center gap-1">
                                                <svg class="w-3 h-3 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                </svg>
                                                <span class="text-xs text-gray-600 truncate">
                                                    {{ vehicle.location_name || 'Location not set' }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Actions - Always visible -->
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end gap-2">
                                            <Link
                                                :href="`/owner/vehicles/${vehicle.id}`"
                                                class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 px-3 py-1 rounded text-xs font-medium transition-colors"
                                            >
                                                View
                                            </Link>
                                            <Link
                                                :href="`/owner/vehicles/${vehicle.id}/edit`"
                                                class="text-gray-600 hover:text-gray-900 bg-gray-50 hover:bg-gray-100 px-3 py-1 rounded text-xs font-medium transition-colors"
                                            >
                                                Edit
                                            </Link>
                                            <button
                                                @click="confirmDelete(vehicle)"
                                                class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 px-3 py-1 rounded text-xs font-medium transition-colors"
                                            >
                                                Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else class="text-center py-12">
                    <div class="max-w-md mx-auto">
                        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">No vehicles yet</h3>
                        <p class="text-gray-600 mb-6">Start building your rental fleet by adding your first vehicle.</p>
                        <Link
                            href="/owner/vehicles/create"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition-colors inline-flex items-center gap-2"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Add Your First Vehicle
                        </Link>
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="vehicles.data.length > 0 && (vehicles.prev_page_url || vehicles.next_page_url)" class="mt-8 flex justify-center">
                    <div class="flex gap-2">
                        <button
                            v-if="vehicles.prev_page_url"
                            @click="goTo(vehicles.prev_page_url)"
                            class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors"
                        >
                            Previous
                        </button>
                        <button
                            v-if="vehicles.next_page_url"
                            @click="goTo(vehicles.next_page_url)"
                            class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors"
                        >
                            Next
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Image Modal -->
        <div v-if="imageModal.show" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-90" @click="closeImageModal">
            <div class="relative max-w-7xl max-h-full">
                <img 
                    :src="imageModal.src" 
                    :alt="imageModal.alt"
                    class="max-w-full max-h-[90vh] object-contain rounded-lg shadow-2xl"
                    @click.stop
                />
                <button 
                    @click="closeImageModal"
                    class="absolute top-4 right-4 text-white bg-black bg-opacity-50 hover:bg-opacity-75 rounded-full p-2 transition-colors"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
                <div class="absolute bottom-4 left-4 text-white bg-black bg-opacity-50 px-3 py-1 rounded">
                    {{ imageModal.alt }}
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div v-if="deleteModal.show" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-50">
            <div class="bg-white rounded-lg p-6 max-w-md w-full">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Delete Vehicle</h3>
                <p class="text-gray-600 mb-6">
                    Are you sure you want to delete <strong>{{ deleteModal.vehicle?.make?.name }} {{ deleteModal.vehicle?.model?.name }}</strong>? 
                    This action cannot be undone.
                </p>
                <div class="flex gap-3 justify-end">
                    <button
                        @click="cancelDelete"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors"
                    >
                        Cancel
                    </button>
                    <button
                        @click="deleteVehicle"
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors"
                    >
                        Delete Vehicle
                    </button>
                </div>
            </div>
        </div>

        <!-- Click outside to close column options -->
        <div v-if="showColumnOptions" @click="showColumnOptions = false" class="fixed inset-0 z-0"></div>
    </OwnerLayout>
</template>

<script setup>
import OwnerLayout from "@/Layouts/OwnerLayout.vue";
import { Link, router } from "@inertiajs/vue3";
import { ref, computed, onMounted, watch } from "vue";

const props = defineProps({ 
    vehicles: Object 
});

// Column visibility state
const showColumnOptions = ref(false);
const availableColumns = ref([
    { key: 'details', label: 'Details' },
    { key: 'specifications', label: 'Specifications' },
    { key: 'status', label: 'Status' },
    { key: 'pricing', label: 'Pricing' },
    { key: 'location', label: 'Location' }
]);

// Default visible columns (all columns visible by default)
const defaultVisibleColumns = {
    details: true,
    specifications: true,
    status: true,
    pricing: true,
    location: true
};

const visibleColumns = ref({ ...defaultVisibleColumns });

// Image modal state
const imageModal = ref({
    show: false,
    src: '',
    alt: ''
});

// Delete modal state
const deleteModal = ref({
    show: false,
    vehicle: null
});

// Load saved column preferences from localStorage
onMounted(() => {
    const saved = localStorage.getItem('vehicleTableColumns');
    if (saved) {
        try {
            visibleColumns.value = { ...defaultVisibleColumns, ...JSON.parse(saved) };
        } catch (e) {
            console.warn('Failed to parse saved column preferences');
        }
    }
});

// Save column preferences to localStorage when they change
watch(visibleColumns, (newVal) => {
    localStorage.setItem('vehicleTableColumns', JSON.stringify(newVal));
}, { deep: true });

// Reset columns to default
function resetColumns() {
    visibleColumns.value = { ...defaultVisibleColumns };
    showColumnOptions.value = false;
}

// Computed properties for stats
const availableCount = computed(() => {
    return props.vehicles.data.filter(vehicle => vehicle.is_available).length;
});

const rentedCount = computed(() => {
    return props.vehicles.data.filter(vehicle => !vehicle.is_available).length;
});

const totalRevenue = computed(() => {
    // Mock calculation - in real app, this would come from backend
    return props.vehicles.data.reduce((total, vehicle) => {
        if (vehicle.pricing_tiers?.length) {
            const minPrice = Math.min(...vehicle.pricing_tiers.map(t => parseFloat(t.price)));
            return total + (vehicle.is_available ? 0 : minPrice * 7); // Assume 7 days average rental
        }
        return total;
    }, 0);
});

// Image modal functions
function openImageModal(src, alt) {
    imageModal.value = {
        show: true,
        src: src,
        alt: alt
    };
}

function closeImageModal() {
    imageModal.value.show = false;
}

// Delete functions
function confirmDelete(vehicle) {
    deleteModal.value = {
        show: true,
        vehicle: vehicle
    };
}

function cancelDelete() {
    deleteModal.value.show = false;
}

function deleteVehicle() {
    if (deleteModal.value.vehicle) {
        router.delete(`/owner/vehicles/${deleteModal.value.vehicle.id}`);
        deleteModal.value.show = false;
    }
}

function goTo(url) {
    router.visit(url);
}
</script>