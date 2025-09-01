<template>
    <AuthenticatedLayout>
        <div class="min-h-screen bg-gray-50">
            <!-- Owner Header -->
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-8 text-white max-w-7xl mx-auto px-6 py-8 mt-12 ">
                <div class="max-w-7xl mx-auto">
                    <!-- Owner Info -->
                    <div class="flex flex-col md:flex-row items-start md:items-center gap-4">
                        <div class="flex items-center space-x-4">
                            <div v-if="owner.profile_picture_url" class="w-16 h-16 rounded-xl overflow-hidden shadow-lg border-4 border-white/20">
                                <img :src="owner.profile_picture_url" :alt="owner.name" class="w-full h-full object-cover">
                            </div>
                            <div v-else class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center font-bold text-xl text-white border-4 border-white/20">
                                {{ owner.name?.charAt(0)?.toUpperCase() || 'O' }}
                            </div>
                            <div>
                                <h1 class="text-2xl font-bold mb-1">{{ owner.name }}</h1>
                                <div class="flex items-center gap-2">
                                    <div class="flex items-center gap-1">
                                        <div 
                                            :class="[
                                                'w-1.5 h-1.5 rounded-full',
                                                owner.activity_status === 'online' ? 'bg-green-400' :
                                                owner.activity_status === 'recently_active' ? 'bg-yellow-400' :
                                                owner.activity_status === 'active_today' ? 'bg-blue-400' :
                                                'bg-gray-400'
                                            ]"
                                        ></div>
                                        <p class="text-xs opacity-90">{{ owner.activity_status_text }}</p>
                                    </div>
                                    <!-- Verified Badge -->
                                    <div v-if="owner.kyc_status === 'approved'">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <svg class="w-2.5 h-2.5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                            </svg>
                                            Verified Owner
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Bio Section -->
                        <div v-if="owner.bio" class="flex-1 md:ml-6">
                            <div class="bg-white/10 backdrop-blur-sm rounded-lg p-3 border border-white/20">
                                <h3 class="text-xs font-semibold opacity-90 mb-1">About</h3>
                                <p class="text-xs opacity-80 leading-relaxed">{{ owner.bio }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Owner Stats -->
            <div class="bg-white border-b">
                <div class="max-w-7xl mx-auto px-6 py-6">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                        <div class="text-center">
                            <div class="bg-indigo-50 rounded-xl p-4 mb-2">
                                <svg class="w-6 h-6 text-indigo-600 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                <p class="text-xl font-bold text-gray-900">{{ vehicles.length }}</p>
                            </div>
                            <p class="text-sm text-gray-600 font-medium">Vehicles Listed</p>
                        </div>
                        <div class="text-center">
                            <div class="bg-green-50 rounded-xl p-4 mb-2">
                                <svg class="w-6 h-6 text-green-600 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                <p class="text-xl font-bold text-gray-900">{{ stats.total_bookings || '0' }}</p>
                            </div>
                            <p class="text-sm text-gray-600 font-medium">Total Bookings</p>
                        </div>
                        <div class="text-center">
                            <div class="bg-yellow-50 rounded-xl p-4 mb-2">
                                <svg class="w-6 h-6 text-yellow-600 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                </svg>
                                <p class="text-xl font-bold text-gray-900 flex items-center justify-center gap-1">
                                    <span>{{ stats.average_rating ? parseFloat(stats.average_rating).toFixed(1) : '0.0' }}</span>
                                </p>
                            </div>
                            <p class="text-sm text-gray-600 font-medium">Average Rating</p>
                        </div>
                        <div class="text-center">
                            <div class="bg-purple-50 rounded-xl p-4 mb-2">
                                <svg class="w-6 h-6 text-purple-600 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <p class="text-xl font-bold text-gray-900">{{ formatJoinDate(owner.created_at) }}</p>
                            </div>
                            <p class="text-sm text-gray-600 font-medium">Member Since</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sorting Nav -->
            <div class="bg-white border-b">
                <div class="max-w-7xl mx-auto px-6">
                    <div class="flex space-x-6 py-3">
                        <button 
                            @click="sortBy = 'popular'"
                            :class="[
                                'py-2 px-1 text-sm font-medium transition-colors relative',
                                sortBy === 'popular' 
                                    ? 'text-orange-600' 
                                    : 'text-gray-600 hover:text-orange-600'
                            ]"
                        >
                            Popular
                            <div v-if="sortBy === 'popular'" class="absolute bottom-0 left-0 right-0 h-0.5 bg-orange-600"></div>
                        </button>
                        <button 
                            @click="sortBy = 'latest'"
                            :class="[
                                'py-2 px-1 text-sm font-medium transition-colors relative',
                                sortBy === 'latest' 
                                    ? 'text-orange-600' 
                                    : 'text-gray-600 hover:text-orange-600'
                            ]"
                        >
                            Latest
                            <div v-if="sortBy === 'latest'" class="absolute bottom-0 left-0 right-0 h-0.5 bg-orange-600"></div>
                        </button>
                        <button 
                            @click="sortBy = 'price_low'"
                            :class="[
                                'py-2 px-1 text-sm font-medium transition-colors relative',
                                sortBy === 'price_low' 
                                    ? 'text-orange-600' 
                                    : 'text-gray-600 hover:text-orange-600'
                            ]"
                        >
                            Price: Low to High
                            <div v-if="sortBy === 'price_low'" class="absolute bottom-0 left-0 right-0 h-0.5 bg-orange-600"></div>
                        </button>
                        <button 
                            @click="sortBy = 'price_high'"
                            :class="[
                                'py-2 px-1 text-sm font-medium transition-colors relative',
                                sortBy === 'price_high' 
                                    ? 'text-orange-600' 
                                    : 'text-gray-600 hover:text-orange-600'
                            ]"
                        >
                            Price: High to Low
                            <div v-if="sortBy === 'price_high'" class="absolute bottom-0 left-0 right-0 h-0.5 bg-orange-600"></div>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Vehicle Grid -->
            <div class="bg-gray-50 min-h-screen">
                <div class="max-w-7xl mx-auto px-6 py-8">
                    <div v-if="sortedVehicles.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        <!-- Vehicle Card -->
                        <div 
                            v-for="vehicle in sortedVehicles" 
                            :key="vehicle.id"
                            class="bg-white rounded-lg shadow-sm hover:shadow-md transition-all duration-200 overflow-hidden group cursor-pointer"
                            @click="viewVehicleDetail(vehicle.id)"
                        >
                            <div class="relative">
                                <img 
                                    :src="vehicle.main_photo_url || '/images/placeholder-vehicle.jpg'" 
                                    :alt="`${vehicle.make?.name} ${vehicle.model?.name}`" 
                                    class="w-full h-48 object-cover"
                                >
                                
                                <!-- Status Badge -->
                                <div class="absolute top-2 left-2">
                                    <span 
                                        :class="[
                                            'px-1.5 py-0.5 rounded-full text-xs font-medium backdrop-blur-sm',
                                            !vehicle.is_available 
                                                ? 'bg-red-500/90 text-white' 
                                                : vehicle.is_booked 
                                                    ? 'bg-yellow-500/90 text-white'
                                                    : 'bg-green-500/90 text-white'
                                        ]"
                                    >
                                        {{ !vehicle.is_available ? 'Not Available' : vehicle.is_booked ? 'Booked' : 'Available' }}
                                    </span>
                                </div>

                                <!-- Save Button -->
                                <button 
                                    @click.stop="toggleSave(vehicle)"
                                    class="absolute top-2 right-2 p-1.5 rounded-full bg-white/80 backdrop-blur-sm hover:bg-white transition-colors"
                                >
                                    <svg 
                                        class="w-4 h-4" 
                                        :class="vehicle.is_saved ? 'text-red-500 fill-current' : 'text-gray-600'"
                                        fill="none" 
                                        stroke="currentColor" 
                                        viewBox="0 0 24 24"
                                    >
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                    </svg>
                                </button>
                            </div>
                            
                            <div class="p-3">
                                <h3 class="text-sm font-semibold text-gray-900 mb-2 group-hover:text-orange-600 transition-colors">
                                    {{ vehicle.make?.name }} {{ vehicle.model?.name }} {{ vehicle.year }}
                                </h3>
                                
                                <div class="flex items-center justify-between mb-2">
                                    <div v-if="vehicle.ratings_avg_rating > 0" class="flex items-center gap-1">
                                        <div class="flex text-yellow-400">
                                            <template v-for="i in 5" :key="i">
                                                <svg 
                                                    class="w-3 h-3" 
                                                    :class="i <= Math.floor(vehicle.ratings_avg_rating) ? 'fill-current' : 'text-gray-300'"
                                                    viewBox="0 0 20 20"
                                                >
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                </svg>
                                            </template>
                                        </div>
                                        <span class="text-xs text-gray-600">{{ parseFloat(vehicle.ratings_avg_rating).toFixed(1) }}</span>
                                    </div>
                                    <div v-else class="text-xs text-gray-400">No reviews</div>
                                    <span class="text-xs text-gray-600">{{ vehicle.location_name || 'Surigao del Norte' }}</span>
                                </div>
                                
                                <div class="flex items-center justify-between">
                                    <div>
                                        <span class="text-lg font-bold text-orange-600">
                                            ₱{{ getMinPrice(vehicle) }}
                                        </span>
                                        <span class="text-xs text-gray-600">/day</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div v-else class="text-center py-12">
                        <div class="mx-auto w-24 h-24 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-700 mb-1">No Vehicles Found</h3>
                        <p class="text-sm text-gray-500">This owner hasn't listed any vehicles yet.</p>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="sortedVehicles.length > itemsPerPage" class="bg-white border-t">
                <div class="max-w-7xl mx-auto px-6 py-4">
                    <div class="flex justify-center items-center space-x-1">
                        <button 
                            v-if="currentPage > 1"
                            @click="currentPage--"
                            class="px-3 py-1.5 text-xs font-medium text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-gray-700 transition-colors"
                        >
                            ← Previous
                        </button>
                        
                        <button 
                            v-for="page in totalPages" 
                            :key="page"
                            @click="currentPage = page"
                            :class="[
                                'px-3 py-1.5 text-xs font-medium rounded-lg transition-colors',
                                currentPage === page 
                                    ? 'bg-orange-600 text-white border-orange-600' 
                                    : 'text-gray-600 bg-white border border-gray-300 hover:bg-gray-50 hover:text-gray-700'
                            ]"
                        >
                            {{ page }}
                        </button>
                        
                        <button 
                            v-if="currentPage < totalPages"
                            @click="currentPage++"
                            class="px-3 py-1.5 text-xs font-medium text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-gray-700 transition-colors"
                        >
                            Next →
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const page = usePage()
const { owner, vehicles, stats } = page.props

// Reactive state
const sortBy = ref('popular')
const currentPage = ref(1)
const itemsPerPage = 15

// Computed properties
const sortedVehicles = computed(() => {
    let sorted = [...vehicles]
    
    switch (sortBy.value) {
        case 'latest':
            sorted = sorted.sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
            break
        case 'price_low':
            sorted = sorted.sort((a, b) => getMinPrice(a) - getMinPrice(b))
            break
        case 'price_high':
            sorted = sorted.sort((a, b) => getMinPrice(b) - getMinPrice(a))
            break
        case 'popular':
        default:
            sorted = sorted.sort((a, b) => (b.ratings_avg_rating || 0) - (a.ratings_avg_rating || 0))
            break
    }
    
    // Apply pagination
    const start = (currentPage.value - 1) * itemsPerPage
    const end = start + itemsPerPage
    return sorted.slice(start, end)
})

const totalPages = computed(() => {
    return Math.ceil(vehicles.length / itemsPerPage)
})

// Methods
function getMinPrice(vehicle) {
    if (!vehicle.pricing_tiers || vehicle.pricing_tiers.length === 0) {
        return 0
    }
    return Math.min(...vehicle.pricing_tiers.map(tier => parseFloat(tier.price)))
}

function formatJoinDate(dateString) {
    const date = new Date(dateString)
    const now = new Date()
    const diffTime = Math.abs(now - date)
    const diffMonths = Math.ceil(diffTime / (1000 * 60 * 60 * 24 * 30))
    
    if (diffMonths < 12) {
        return `${diffMonths} mos`
    } else {
        const years = Math.floor(diffMonths / 12)
        return `${years} yr${years > 1 ? 's' : ''}`
    }
}

function viewVehicleDetail(vehicleId) {
    router.visit(route('public.vehicles.show', vehicleId))
}

function toggleSave(vehicle) {
    // TODO: Implement save/unsave functionality
    console.log('Toggle save for vehicle:', vehicle.id)
    // You can add the API call here to save/unsave the vehicle
}

function followOwner() {
    // TODO: Implement follow functionality
    console.log('Follow owner:', owner.id)
}

function chatWithOwner() {
    // TODO: Implement chat functionality
    console.log('Chat with owner:', owner.id)
}
</script>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
