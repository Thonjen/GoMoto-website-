<template>
    <AuthenticatedLayout>
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Hero Search Section -->
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
                                v-model="filters.search"
                                @keyup.enter="applyFilters"
                                type="text"
                                class="w-full px-6 py-4 text-lg border-2 border-gray-200 rounded-full focus:ring-2 focus:ring-blue-500 focus:border-blue-500 pl-14"
                                placeholder="Search by make, model, color, or location..."
                            />
                            <svg class="absolute left-5 top-1/2 transform -translate-y-1/2 w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <button
                                @click="applyFilters"
                                class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-blue-600 text-white px-6 py-2 rounded-full hover:bg-blue-700 transition-colors"
                            >
                                Search
                            </button>
                        </div>
                    </div>

                    <!-- Popular Searches -->
                    <div v-if="filterOptions.popularSearches?.length" class="flex flex-wrap justify-center gap-2 mb-6">
                        <span class="text-sm text-gray-600 mr-2">Popular:</span>
                        <button
                            v-for="search in filterOptions.popularSearches"
                            :key="search"
                            @click="quickSearch(search)"
                            class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm hover:bg-gray-200 transition-colors"
                        >
                            {{ search }}
                        </button>
                    </div>
                </div>

                <!-- Advanced Filter Form -->
                <div class="bg-white rounded-lg shadow-md mb-8 overflow-hidden">
                    <!-- Quick Filters -->
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-4 border-b">
                        <h3 class="text-lg font-semibold text-gray-800 mb-3">Quick Filters</h3>
                        <div class="flex flex-wrap gap-2">
                            <button
                                type="button"
                                @click="quickFilter('category', 'car')"
                                :class="filters.category === 'car' ? 'bg-blue-500 text-white' : 'bg-white text-gray-700 hover:bg-gray-100'"
                                class="px-4 py-2 border border-gray-300 rounded-full text-sm font-medium transition-colors"
                            >
                                🚗 Cars Only
                            </button>
                            <button
                                type="button"
                                @click="quickFilter('category', 'motorcycle')"
                                :class="filters.category === 'motorcycle' ? 'bg-blue-500 text-white' : 'bg-white text-gray-700 hover:bg-gray-100'"
                                class="px-4 py-2 border border-gray-300 rounded-full text-sm font-medium transition-colors"
                            >
                                🏍️ Motorcycles Only
                            </button>
                            <button
                                type="button"
                                @click="quickFilter('sort_by', 'price_low')"
                                :class="filters.sort_by === 'price_low' ? 'bg-green-500 text-white' : 'bg-white text-gray-700 hover:bg-gray-100'"
                                class="px-4 py-2 border border-gray-300 rounded-full text-sm font-medium transition-colors"
                            >
                                💰 Lowest Price First
                            </button>
                            <button
                                type="button"
                                @click="quickFilter('sort_by', 'year_new')"
                                :class="filters.sort_by === 'year_new' ? 'bg-purple-500 text-white' : 'bg-white text-gray-700 hover:bg-gray-100'"
                                class="px-4 py-2 border border-gray-300 rounded-full text-sm font-medium transition-colors"
                            >
                                ✨ Newest First
                            </button>
                            <button
                                type="button"
                                @click="quickFilter('sort_by', 'popular')"
                                :class="filters.sort_by === 'popular' ? 'bg-orange-500 text-white' : 'bg-white text-gray-700 hover:bg-gray-100'"
                                class="px-4 py-2 border border-gray-300 rounded-full text-sm font-medium transition-colors"
                            >
                                🔥 Most Popular
                            </button>
                        </div>
                    </div>

                    <!-- Basic Filters -->
                    <div class="p-6">
                        <form @submit.prevent="applyFilters" class="space-y-4">
                            <!-- Main Filter Row -->
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Make</label>
                                    <select
                                        v-model="filters.make_id"
                                        @change="onMakeChange"
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
                                    @click="showAdvancedFilters = !showAdvancedFilters"
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
                                    @click="resetFilters"
                                    class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition-colors"
                                >
                                    Clear All Filters
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

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

                <!-- Results Summary & Map Toggle -->
                <div class="mb-6">
                    <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center gap-4">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">
                                {{ vehicles.total }} Vehicle{{ vehicles.total !== 1 ? 's' : '' }} Found
                            </h2>
                            <p class="text-gray-600">
                                Showing {{ vehicles.from }}-{{ vehicles.to }} of {{ vehicles.total }} results
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
                                    <option value="year_new">Year: Newest First</option>
                                    <option value="year_old">Year: Oldest First</option>
                                    <option value="popular">Most Popular</option>
                                    <option value="rating">Highest Rated</option>
                                    <option v-if="hasLocationFilter" value="distance">Closest to Me</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Map View -->
                <div v-if="viewMode === 'map'" class="mb-8">
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <div class="p-4 border-b">
                            <h3 class="text-lg font-semibold text-gray-800">Vehicle Locations in Surigao del Norte</h3>
                            <p class="text-sm text-gray-600">Click on markers to view vehicle details</p>
                        </div>
                        <div class="h-96 relative">
                            <l-map
                                style="height: 100%"
                                :zoom="13"
                                :center="mapCenter"
                                :max-bounds="bounds"
                                :min-zoom="12"
                                :max-zoom="18"
                            >
                                <l-tile-layer url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png" />
                                <l-geo-json :geojson="surigaoGeoJson" :options-style="geoJsonStyle" />
                                
                                <!-- Vehicle Markers -->
                                <l-marker 
                                    v-for="vehicle in vehicles.data" 
                                    :key="`marker-${vehicle.id}`"
                                    :lat-lng="[vehicle.lat, vehicle.lng]"
                                    v-if="vehicle.lat && vehicle.lng"
                                >
                                    <l-popup>
                                        <div class="w-64 p-2">
                                            <div class="flex items-start gap-3">
                                                <img
                                                    v-if="vehicle.main_photo_url"
                                                    :src="vehicle.main_photo_url"
                                                    :alt="`${vehicle.make?.name} ${vehicle.model?.name}`"
                                                    class="w-16 h-12 object-cover rounded"
                                                />
                                                <div v-else class="w-16 h-12 bg-gray-200 rounded flex items-center justify-center">
                                                    <span class="text-gray-500 text-xs">No image</span>
                                                </div>
                                                <div class="flex-1">
                                                    <h4 class="font-semibold text-gray-900">
                                                        {{ vehicle.make?.name }} {{ vehicle.model?.name }}
                                                        <span v-if="vehicle.year" class="text-gray-600 font-normal">({{ vehicle.year }})</span>
                                                    </h4>
                                                    <p class="text-sm text-gray-600">{{ vehicle.type?.sub_type }}</p>
                                                    <div class="flex items-center gap-2 mt-1 text-xs text-gray-500">
                                                        <span>{{ vehicle.fuelType?.name || 'N/A' }}</span>
                                                        <span>•</span>
                                                        <span>{{ vehicle.transmission?.name || 'Manual' }}</span>
                                                    </div>
                                                    <div v-if="vehicle.pricing_tiers?.length" class="mt-2">
                                                        <span class="text-green-600 font-semibold text-sm">
                                                            From ₱{{ Math.min(...vehicle.pricing_tiers.map(t => parseFloat(t.price))) }}
                                                        </span>
                                                    </div>
                                                    <button
                                                        @click="goToDetail(vehicle.id)"
                                                        class="mt-2 bg-blue-600 text-white text-xs px-3 py-1 rounded hover:bg-blue-700 transition-colors"
                                                    >
                                                        View Details
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </l-popup>
                                </l-marker>
                            </l-map>
                        </div>
                    </div>
                </div>

                <!-- Featured Vehicles Section (only show when no filters are applied) -->
                <div v-if="!hasActiveFilters && featuredVehicles?.length" class="mb-8">
                    <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg p-6 text-white">
                        <h3 class="text-xl font-bold mb-4">🌟 Featured Vehicles</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div
                                v-for="vehicle in featuredVehicles"
                                :key="`featured-${vehicle.id}`"
                                @click="goToDetail(vehicle.id)"
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
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Vehicles List -->
                <div
                    v-if="viewMode === 'list' && vehicles.data.length > 0"
                    class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6"
                >
                    <div
                        v-for="vehicle in vehicles.data"
                        :key="vehicle.id"
                        class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-all duration-300 relative group cursor-pointer"
                        @click="goToDetail(vehicle.id)"
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
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>

                            <!-- Status Overlay -->
                            <div
                                v-if="!vehicle.is_available || vehicle.is_booked"
                                class="absolute inset-0 bg-black bg-opacity-80 flex items-center justify-center"
                            >
                                <div class="text-white text-center">
                                    <div class="text-lg font-bold mb-2">
                                        {{ !vehicle.is_available ? "❌ Unavailable" : "📅 Currently Booked" }}
                                    </div>
                                    <div class="text-sm opacity-90">
                                        {{ !vehicle.is_available ? "Not available for rental" : "Check back later" }}
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

                            <!-- Vehicle Category & Features Badge -->
                            <div class="absolute top-3 left-3 flex flex-col gap-2">
                                <span class="bg-white bg-opacity-95 text-gray-800 px-3 py-1 rounded-full text-xs font-semibold shadow-md">
                                    {{ vehicle.type?.category === 'car' ? '🚗 Car' : '🏍️ Motorcycle' }}
                                </span>
                                <div class="flex flex-col gap-1">
                                    <span
                                        v-if="vehicle.year && vehicle.year >= new Date().getFullYear() - 2"
                                        class="bg-blue-500 text-white px-2 py-1 rounded-full text-xs font-medium shadow-md"
                                    >
                                        ✨ New
                                    </span>
                                    <span
                                        v-if="vehicle.instant_book_available"
                                        class="bg-orange-500 text-white px-2 py-1 rounded-full text-xs font-medium shadow-md"
                                    >
                                        ⚡ Instant
                                    </span>
                                </div>
                            </div>

                            <!-- Distance Badge (if location filtering is used) -->
                            <div
                                v-if="hasLocationFilter && vehicle.distance"
                                class="absolute bottom-3 right-3 bg-purple-500 text-white px-2 py-1 rounded-full text-xs font-medium shadow-md"
                            >
                                📍 {{ Math.round(vehicle.distance) }}km away
                            </div>
                        </div>

                        <!-- Vehicle Details -->
                        <div class="p-5">
                            <!-- Title -->
                            <div class="mb-3">
                                <h3 class="text-lg font-bold text-gray-900 mb-1">
                                    {{ vehicle.make?.name }} {{ vehicle.model?.name }}
                                    <span v-if="vehicle.year" class="text-gray-600 font-normal">({{ vehicle.year }})</span>
                                </h3>
                                <div class="flex items-center gap-2 text-sm text-gray-600">
                                    <span v-if="vehicle.license_plate" class="bg-gray-100 px-2 py-1 rounded font-mono">
                                        {{ vehicle.license_plate }}
                                    </span>
                                    <span class="text-blue-600">
                                        {{ vehicle.type?.sub_type }}
                                    </span>
                                </div>
                            </div>

                            <!-- Specifications Grid -->
                            <div class="grid grid-cols-2 gap-3 text-sm text-gray-600 mb-4">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                    <span class="font-medium">{{ vehicle.fuelType?.name || vehicle.fuel_type?.name || 'N/A' }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <span class="font-medium">{{ vehicle.transmission?.name || 'Manual' }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                                    </svg>
                                    <span class="font-medium">{{ vehicle.color }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <span class="truncate font-medium">{{ vehicle.location_name || 'Surigao del Norte' }}</span>
                                </div>
                            </div>

                            <!-- Additional Vehicle Info -->
                            <div v-if="vehicle.seats || vehicle.doors || vehicle.engine_size" class="flex flex-wrap gap-3 text-xs text-gray-500 mb-4">
                                <span v-if="vehicle.seats" class="bg-gray-100 px-2 py-1 rounded">
                                    👥 {{ vehicle.seats }} seats
                                </span>
                                <span v-if="vehicle.doors" class="bg-gray-100 px-2 py-1 rounded">
                                    🚪 {{ vehicle.doors }} doors
                                </span>
                                <span v-if="vehicle.engine_size" class="bg-gray-100 px-2 py-1 rounded">
                                    🔧 {{ vehicle.engine_size }}L
                                </span>
                            </div>

                            <!-- Pricing Tiers -->
                            <div v-if="vehicle.pricing_tiers?.length" class="mb-4">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-medium text-gray-700">Rental Options:</span>
                                </div>
                                <div class="space-y-1">
                                    <div
                                        v-for="(tier, index) in vehicle.pricing_tiers.slice(0, 2)"
                                        :key="tier.id"
                                        class="flex justify-between items-center text-sm"
                                    >
                                        <span class="text-gray-600">
                                            {{ tier.duration_from }} {{ tier.duration_from === 1 ? tier.duration_unit.slice(0, -1) : tier.duration_unit }}
                                        </span>
                                        <span class="font-semibold text-green-600">₱{{ tier.price }}</span>
                                    </div>
                                    <div
                                        v-if="vehicle.pricing_tiers.length > 2"
                                        class="text-xs text-gray-500"
                                    >
                                        +{{ vehicle.pricing_tiers.length - 2 }} more option{{ vehicle.pricing_tiers.length - 2 > 1 ? 's' : '' }}
                                    </div>
                                </div>
                            </div>

                            <!-- Action Button -->
                            <div class="pt-3 border-t border-gray-100">
                                <button
                                    @click.stop="goToDetail(vehicle.id)"
                                    :disabled="!vehicle.is_available || vehicle.is_booked"
                                    class="w-full py-2 px-4 rounded-lg font-medium transition-colors"
                                    :class="vehicle.is_available && !vehicle.is_booked
                                        ? 'bg-blue-600 hover:bg-blue-700 text-white'
                                        : 'bg-gray-300 text-gray-500 cursor-not-allowed'"
                                >
                                    {{ vehicle.is_available && !vehicle.is_booked ? 'View Details & Book' : 'Unavailable' }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- No Results State -->
                <div
                    v-if="viewMode === 'list' && vehicles.data.length === 0"
                    class="text-center py-16"
                >
                    <div class="mx-auto w-32 h-32 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mb-6">
                        <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">No vehicles match your search</h3>
                    <div class="max-w-md mx-auto">
                        <p class="text-gray-600 mb-6">
                            We couldn't find any vehicles matching your criteria. Try adjusting your filters or search terms.
                        </p>
                        
                        <!-- Suggestions -->
                        <div class="mb-6">
                            <h4 class="text-sm font-semibold text-gray-800 mb-3">Try these suggestions:</h4>
                            <div class="flex flex-wrap justify-center gap-2">
                                <button
                                    @click="quickFilter('category', 'car')"
                                    class="px-3 py-2 bg-blue-100 text-blue-700 rounded-lg text-sm hover:bg-blue-200 transition-colors"
                                >
                                    🚗 Show all cars
                                </button>
                                <button
                                    @click="quickFilter('category', 'motorcycle')"
                                    class="px-3 py-2 bg-green-100 text-green-700 rounded-lg text-sm hover:bg-green-200 transition-colors"
                                >
                                    🏍️ Show all motorcycles
                                </button>
                                <button
                                    @click="expandSearchRadius"
                                    v-if="hasLocationFilter"
                                    class="px-3 py-2 bg-purple-100 text-purple-700 rounded-lg text-sm hover:bg-purple-200 transition-colors"
                                >
                                    📍 Expand search area
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
    </AuthenticatedLayout>
</template>

<script setup>
import { router } from "@inertiajs/vue3";
import { ref, computed, watch, onMounted } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { LMap, LTileLayer, LMarker, LPopup, LGeoJson } from "@vue-leaflet/vue-leaflet";
import '@/plugins/leaflet-icon-fix';

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

// Map configuration
const mapCenter = ref([9.786048643234757, 125.49062330098809]); // Center of Surigao del Norte

const surigaoGeoJson = {
    type: "FeatureCollection",
    features: [
        {
            type: "Feature",
            properties: {},
            geometry: {
                type: "Polygon",
                coordinates: [
                    [
                        [125.48545720374489, 9.801207813887503],
                        [125.48394937158277, 9.801134816678099],
                        [125.4824560626428, 9.800916528148765],
                        [125.48099166020462, 9.800555050821249],
                        [125.47957026901334, 9.800053866380772],
                        [125.47820557937605, 9.799417802127778],
                        [125.47691073525756, 9.79865298446448],
                        [125.4756982076485, 9.797766779865192],
                        [125.47457967442702, 9.796767723899952],
                        [125.47356590787237, 9.79566543899633],
                        [125.47266667091462, 9.79447054173279],
                        [125.47189062311993, 9.793194540557817],
                        [125.47124523731648, 9.791849724921354],
                        [125.47073672766314, 9.790449046887577],
                        [125.47036998985222, 9.789005996370452],
                        [125.47014855402033, 9.787534471194746],
                        [125.47007455081837, 9.786048643234757],
                        [125.47014869096546, 9.784562821920598],
                        [125.47037025847982, 9.78309131642699],
                        [125.47073711764999, 9.781648297871861],
                        [125.47124573367556, 9.78024766285169],
                        [125.47189120677649, 9.77890289962746],
                        [125.47266731943903, 9.77762695824917],
                        [125.47356659634224, 9.776432125868816],
                        [125.47458037638481, 9.775329908441458],
                        [125.47569889611839, 9.774330919952476],
                        [125.47691138378202, 9.773444780236375],
                        [125.47820616303265, 9.772680022369768],
                        [125.47957076537246, 9.772044010529008],
                        [125.48099205019147, 9.771542869102143],
                        [125.48245633127043, 9.771181423736623],
                        [125.48394950852794, 9.77096315488939],
                        [125.48545720374489, 9.770890164325817],
                        [125.48696489896184, 9.77096315488939],
                        [125.48845807621933, 9.771181423736623],
                        [125.48992235729831, 9.771542869102143],
                        [125.49134364211733, 9.772044010529008],
                        [125.49270824445713, 9.772680022369768],
                        [125.49400302370776, 9.773444780236375],
                        [125.4952155113714, 9.774330919952476],
                        [125.49633403110496, 9.775329908441458],
                        [125.49734781114755, 9.776432125868816],
                        [125.49824708805076, 9.77762695824917],
                        [125.49902320071328, 9.77890289962746],
                        [125.4996686738142, 9.78024766285169],
                        [125.5001772898398, 9.781648297871861],
                        [125.50054414900994, 9.78309131642699],
                        [125.50076571652433, 9.784562821920598],
                        [125.50083985667139, 9.786048643234757],
                        [125.50076585346946, 9.787534471194746],
                        [125.50054441763756, 9.789005996370452],
                        [125.50017767982663, 9.790449046887577],
                        [125.4996691701733, 9.791849724921354],
                        [125.49902378436985, 9.793194540557817],
                        [125.49824773657517, 9.79447054173279],
                        [125.4973484996174, 9.79566543899633],
                        [125.49633473306275, 9.796767723899952],
                        [125.49521619984127, 9.797766779865192],
                        [125.49400367223222, 9.79865298446448],
                        [125.49270882811372, 9.799417802127778],
                        [125.49134413847645, 9.800053866380772],
                        [125.48992274728516, 9.800555050821249],
                        [125.48845834484696, 9.800916528148765],
                        [125.48696503590699, 9.801134816678099],
                        [125.48545720374489, 9.801207813887503],
                    ],
                ],
            },
        },
    ],
};

const geoJsonStyle = () => ({
    color: "#1976d2",
    weight: 2,
    fill: false,
});

const bounds = [
    [9.770890164325817, 125.47007455081837],
    [9.801207813887503, 125.50083985667139],
];

// Computed properties
const hasActiveFilters = computed(() => {
    return Object.entries(filters.value).some(([key, value]) => {
        if (key === 'sort_by' && value === 'latest') return false;
        if (key === 'per_page' && value === '9') return false;
        return value !== "" && value !== null && value !== undefined;
    });
});

const hasLocationFilter = computed(() => {
    return filters.value.lat && filters.value.lng;
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
                    year_new: 'Year: Newest First',
                    year_old: 'Year: Oldest First',
                    popular: 'Most Popular',
                    rating: 'Highest Rated',
                    distance: 'Closest to Me'
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

function goToDetail(vehicleId) {
    router.visit(`/vehicles/${vehicleId}`);
}

function expandSearchRadius() {
    if (hasLocationFilter.value) {
        const currentRadius = parseInt(filters.value.radius) || 10;
        filters.value.radius = Math.min(currentRadius + 10, 100); // Max 100km
        applyFilters();
    }
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

<style>
@import "leaflet/dist/leaflet.css";
</style>
