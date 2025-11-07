<template>
    <OwnerLayout>
        <div class="min-h-screen py-8" style="background: linear-gradient(135deg, #535862 0%, #3a3f4a 100%);">
            <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header Section -->
                <div class="mb-8">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div>
                            <h1 class="text-3xl font-bold text-white">My Vehicles</h1>
                            <p class="text-white/80 mt-1">Manage your rental fleet</p>
                        </div>
                        <div class="flex gap-3">
                            <!-- Column Visibility Toggle -->
                            <div class="relative">
                                <button
                                    @click="showColumnOptions = !showColumnOptions"
                                    class="bg-white/10 border border-white/20 hover:bg-white/20 text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center gap-2 backdrop-blur-sm"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"/>
                                    </svg>
                                    View Options
                                </button>
                                
                                <!-- Column Options Dropdown -->
                                <div v-if="showColumnOptions" class="absolute right-0 mt-2 w-64 glass-card-dark border border-white/20 rounded-lg shadow-glow z-10">
                                    <div class="p-4">
                                        <h3 class="text-sm font-semibold text-white mb-3">Show Columns</h3>
                                        <div class="space-y-2">
                                            <label v-for="column in availableColumns" :key="column.key" class="flex items-center">
                                                <input
                                                    type="checkbox"
                                                    v-model="visibleColumns[column.key]"
                                                    class="rounded border-white/20 bg-white/10 text-blue-600 focus:ring-blue-500 focus:ring-offset-0"
                                                >
                                                <span class="ml-2 text-sm text-white/80">{{ column.label }}</span>
                                            </label>
                                        </div>
                                        <div class="mt-4 pt-3 border-t border-white/20">
                                            <button
                                                @click="resetColumns"
                                                class="text-xs text-blue-400 hover:text-blue-300"
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
                        <div class="glass-card-dark p-4 shadow-glow border border-white/20">
                            <div class="flex items-center">
                                <div class="p-2 bg-blue-400/20 rounded-lg backdrop-blur-sm">
                                    <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-white/70">Total Vehicles</p>
                                    <p class="text-2xl font-bold text-white">{{ vehicles.total || vehicles.data.length }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="glass-card-dark p-4 shadow-glow border border-white/20">
                            <div class="flex items-center">
                                <div class="p-2 bg-green-400/20 rounded-lg backdrop-blur-sm">
                                    <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-white/70">Available</p>
                                    <p class="text-2xl font-bold text-white">{{ availableCount }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="glass-card-dark p-4 shadow-glow border border-white/20">
                            <div class="flex items-center">
                                <div class="p-2 bg-yellow-400/20 rounded-lg backdrop-blur-sm">
                                    <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-white/70">Rented</p>
                                    <p class="text-2xl font-bold text-white">{{ rentedCount }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="glass-card-dark p-4 shadow-glow border border-white/20">
                            <div class="flex items-center">
                                <div class="p-2 bg-purple-400/20 rounded-lg backdrop-blur-sm">
                                    <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-white/70">Revenue</p>
                                    <p class="text-2xl font-bold text-white">₱{{ totalRevenue.toLocaleString() }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Table Section -->
                <div v-if="vehicles.data.length > 0" class="space-y-4">
                    <!-- Selection and Action Panel -->
                    <div class="glass-card-dark shadow-glow border border-white/20 p-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="text-sm text-white/70">Selected Vehicle:</div>
                                <div v-if="selectedVehicle" class="text-sm font-medium text-white">
                                    <span class="font-semibold">
                                        {{ selectedVehicle.make?.name || 'Unknown Make' }} 
                                        {{ selectedVehicle.model?.name || selectedVehicle.type?.name || 'Unknown Model' }}
                                    </span>
                                    <span v-if="selectedVehicle.year" class="text-white/80 ml-1">({{ selectedVehicle.year }})</span>
                                    <span v-if="selectedVehicle.license_plate" class="ml-2 text-xs font-mono bg-white/10 text-white px-2 py-1 rounded border border-white/20">
                                        {{ selectedVehicle.license_plate }}
                                    </span>
                                </div>
                                <div v-else class="text-sm text-white/50 italic">
                                    No vehicle selected - click on a row to select
                                </div>
                            </div>
                            <div v-if="selectedVehicle" class="flex items-center gap-3">
                                <Link
                                    :href="`/owner/vehicles/${selectedVehicle.id}`"
                                    class="text-blue-400 hover:text-blue-300 bg-blue-500/20 hover:bg-blue-500/30 px-4 py-2 rounded-lg text-sm font-medium transition-colors border border-blue-500/30 flex items-center gap-2"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    View Details
                                </Link>
                                <Link
                                    :href="`/owner/vehicles/${selectedVehicle.id}/edit`"
                                    class="text-white/80 hover:text-white bg-white/10 hover:bg-white/20 px-4 py-2 rounded-lg text-sm font-medium transition-colors border border-white/20 flex items-center gap-2"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Edit Vehicle
                                </Link>
                                <button
                                    @click="confirmDelete(selectedVehicle)"
                                    class="text-red-400 hover:text-red-300 bg-red-500/20 hover:bg-red-500/30 px-4 py-2 rounded-lg text-sm font-medium transition-colors border border-red-500/30 flex items-center gap-2"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    Delete Vehicle
                                </button>
                                <button
                                    @click="selectedVehicle = null"
                                    class="text-white/60 hover:text-white/80 bg-white/5 hover:bg-white/10 px-3 py-2 rounded-lg text-sm transition-colors border border-white/10"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                            <div v-else class="text-xs text-white/40">
                                Select a vehicle to view available actions
                            </div>
                        </div>
                    </div>

                    <!-- Vehicle Table -->
                    <div class="glass-card-dark shadow-glow border border-white/20 overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-white/10">
                                <thead class="bg-white/5 backdrop-blur-sm">
                                    <tr>
                                        <!-- Always show Vehicle column -->
                                        <th class="px-6 py-3 text-left text-xs font-medium text-white/80 uppercase tracking-wider">Vehicle</th>
                                        <th v-if="visibleColumns.details" class="px-6 py-3 text-left text-xs font-medium text-white/80 uppercase tracking-wider">Details</th>
                                        <th v-if="visibleColumns.specifications" class="px-6 py-3 text-left text-xs font-medium text-white/80 uppercase tracking-wider">Specifications</th>
                                        <th v-if="visibleColumns.status" class="px-6 py-3 text-left text-xs font-medium text-white/80 uppercase tracking-wider">Status</th>
                                        <th v-if="visibleColumns.pricing" class="px-6 py-3 text-left text-xs font-medium text-white/80 uppercase tracking-wider">Pricing</th>
                                        <th v-if="visibleColumns.location" class="px-6 py-3 text-left text-xs font-medium text-white/80 uppercase tracking-wider">Location</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-white/10">
                                    <tr 
                                        v-for="vehicle in vehicles.data" 
                                        :key="vehicle.id" 
                                        :class="[
                                            'transition-all duration-200 cursor-pointer',
                                            selectedVehicle?.id === vehicle.id 
                                                ? 'bg-blue-500/20 border-blue-400/50 shadow-lg' 
                                                : 'hover:bg-white/5'
                                        ]"
                                        @click="selectedVehicle = selectedVehicle?.id === vehicle.id ? null : vehicle"
                                    >
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
                                                <div v-else class="h-20 w-28 bg-white/10 rounded-lg flex items-center justify-center cursor-pointer hover:bg-white/20 transition-colors border border-white/20">
                                                    <svg class="w-8 h-8 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-bold text-white">
                                                    {{ vehicle.make?.name || 'Unknown' }} {{ vehicle.model?.name || vehicle.type?.sub_type || 'Model' }}
                                                </div>
                                                <div class="text-sm text-white/70">{{ vehicle.year }}</div>
                                                <div v-if="vehicle.license_plate" class="text-xs font-mono bg-white/10 text-white px-2 py-1 rounded mt-1 inline-block border border-white/20">
                                                    {{ vehicle.license_plate }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Details -->
                                    <td v-if="visibleColumns.details" class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-white">{{ vehicle.type?.sub_type || 'N/A' }}</div>
                                        <div class="text-sm text-white/70">{{ vehicle.color }}</div>
                                        <div v-if="vehicle.description" class="text-xs text-white/50 max-w-xs truncate mt-1">
                                            {{ vehicle.description }}
                                        </div>
                                    </td>

                                    <!-- Specifications -->
                                    <td v-if="visibleColumns.specifications" class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm">
                                            <div class="flex items-center gap-1 mb-1">
                                                <svg class="w-3 h-3 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                                </svg>
                                                <span class="text-white/70 text-xs">{{ vehicle.fuel_type?.name || 'N/A' }}</span>
                                            </div>
                                            <div class="flex items-center gap-1">
                                                <svg class="w-3 h-3 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                </svg>
                                                <span class="text-white/70 text-xs">{{ vehicle.transmission?.name || 'Manual' }}</span>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Status -->
                                    <td v-if="visibleColumns.status" class="px-6 py-4 whitespace-nowrap">
                                        <span 
                                            :class="vehicle.is_available 
                                                ? 'bg-green-500/20 text-green-400 border-green-500/30' 
                                                : 'bg-red-500/20 text-red-400 border-red-500/30'"
                                            class="inline-flex px-2 py-1 text-xs font-semibold rounded-full border backdrop-blur-sm"
                                        >
                                            {{ vehicle.is_available ? 'Available' : 'Rented' }}
                                        </span>
                                    </td>

                                    <!-- Pricing -->
                                    <td v-if="visibleColumns.pricing" class="px-6 py-4 whitespace-nowrap">
                                        <div v-if="vehicle.pricing_tiers?.length" class="text-sm">
                                            <div class="font-semibold text-green-400">
                                                ₱{{ Math.min(...vehicle.pricing_tiers.map(t => parseFloat(t.price))) }}
                                            </div>
                                            <div class="text-xs text-white/70">
                                                /{{ vehicle.pricing_tiers.find(t => t.price == Math.min(...vehicle.pricing_tiers.map(pt => parseFloat(pt.price)))).duration_unit }}
                                            </div>
                                            <div class="text-xs text-white/50">
                                                {{ vehicle.pricing_tiers.length }} tier{{ vehicle.pricing_tiers.length > 1 ? 's' : '' }}
                                            </div>
                                        </div>
                                        <div v-else class="text-sm text-white/50">No pricing set</div>
                                    </td>

                                    <!-- Location -->
                                    <td v-if="visibleColumns.location" class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-white max-w-xs">
                                            <div class="flex items-center gap-1">
                                                <svg class="w-3 h-3 text-white/60 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                </svg>
                                                <span class="text-xs text-white/70 truncate">
                                                    {{ vehicle.location_name || 'Location not set' }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="vehicles.prev_page_url || vehicles.next_page_url" class="mt-8 flex justify-center">
                    <div class="flex gap-2">
                        <button
                            v-if="vehicles.prev_page_url"
                            @click="goTo(vehicles.prev_page_url)"
                            class="px-4 py-2 bg-white/10 border border-white/20 rounded-lg text-sm font-medium text-white hover:bg-white/20 transition-colors backdrop-blur-sm"
                        >
                            Previous
                        </button>
                        <button
                            v-if="vehicles.next_page_url"
                            @click="goTo(vehicles.next_page_url)"
                            class="px-4 py-2 bg-white/10 border border-white/20 rounded-lg text-sm font-medium text-white hover:bg-white/20 transition-colors backdrop-blur-sm"
                        >
                            Next
                        </button>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="text-center py-12">
                    <div class="max-w-md mx-auto">
                        <svg class="w-16 h-16 text-white/40 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <h3 class="text-lg font-semibold text-white mb-2">No vehicles yet</h3>
                        <p class="text-white/70 mb-6">Start building your rental fleet by adding your first vehicle.</p>
                        <Link
                            href="/owner/vehicles/create"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition-colors inline-flex items-center gap-2 shadow-lg"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Add Your First Vehicle
                        </Link>
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
            <div class="glass-card-dark border border-white/20 rounded-lg p-6 max-w-md w-full shadow-glow">
                <h3 class="text-lg font-semibold text-white mb-4">Delete Vehicle</h3>
                <p class="text-white/70 mb-6">
                    Are you sure you want to delete 
                    <strong class="text-white">
                        {{ deleteModal.vehicle?.make?.name || 'Unknown Make' }} 
                        {{ deleteModal.vehicle?.model?.name || deleteModal.vehicle?.type?.name || 'Unknown Model' }}
                        <span v-if="deleteModal.vehicle?.year">({{ deleteModal.vehicle.year }})</span>
                        <span v-if="deleteModal.vehicle?.license_plate" class="font-mono text-sm"> - {{ deleteModal.vehicle.license_plate }}</span>
                    </strong>? 
                    This action cannot be undone.
                </p>
                <div class="flex gap-3 justify-end">
                    <button
                        @click="cancelDelete"
                        class="px-4 py-2 border border-white/20 rounded-lg text-white/80 hover:bg-white/10 transition-colors backdrop-blur-sm"
                    >
                        Cancel
                    </button>
                    <button
                        @click="deleteVehicle"
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors shadow-lg"
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
import Swal from 'sweetalert2';

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

// Selected vehicle state
const selectedVehicle = ref(null);

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
    Swal.fire({
        title: 'Delete Vehicle?',
        html: `Are you sure you want to delete <strong>${vehicle.make?.name} ${vehicle.model?.name}</strong>?<br>This action cannot be undone.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, Delete It',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(`/owner/vehicles/${vehicle.id}`, {
                onSuccess: () => {
                    Swal.fire({
                        title: 'Deleted!',
                        text: 'Your vehicle has been deleted.',
                        icon: 'success',
                        confirmButtonColor: '#3b82f6'
                    });
                },
                onError: () => {
                    Swal.fire({
                        title: 'Error!',
                        text: 'There was an error deleting the vehicle. Please try again.',
                        icon: 'error',
                        confirmButtonColor: '#3b82f6'
                    });
                }
            });
        }
    });
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