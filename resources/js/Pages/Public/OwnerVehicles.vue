<template>
    <AuthenticatedLayout>
        <div class="min-h-screen">
            <!-- Owner Header -->
            <div class="relative bg-gradient-to-br from-[#535862] via-gray-700 to-gray-800 overflow-hidden">
                <!-- Background Pattern -->
                <div class="absolute inset-0 opacity-10" style="background-image: url('data:image/svg+xml;utf8,<svg width=&quot;60&quot; height=&quot;60&quot; viewBox=&quot;0 0 60 60&quot; xmlns=&quot;http://www.w3.org/2000/svg&quot;><g fill=&quot;none&quot; fill-rule=&quot;evenodd&quot;><g fill=&quot;%23ffffff&quot; fill-opacity=&quot;0.05&quot;><circle cx=&quot;7&quot; cy=&quot;7&quot; r=&quot;7&quot;/></g></g></svg>')"></div>
                
                <!-- Floating elements -->
                <div class="absolute top-1/4 left-1/4 w-32 h-32 bg-white/5 rounded-full blur-2xl animate-float"></div>
                <div class="absolute bottom-1/4 right-1/4 w-48 h-48 bg-white/5 rounded-full blur-3xl animate-float" style="animation-delay: 2s;"></div>
                
                <div class="relative z-10 max-w-7xl mx-auto px-6 py-12">
                    <!-- Owner Info Card -->
                    <div class="glass-card p-8 border border-white/20 shadow-2xl">
                        <div class="flex flex-col md:flex-row items-start md:items-center gap-6">
                            <!-- Avatar and Basic Info -->
                            <div class="flex items-center space-x-6">
                                <div class="relative group">
                                    <div v-if="owner.profile_picture_url" 
                                         class="w-20 h-20 rounded-2xl overflow-hidden shadow-xl border-4 border-white/20 group-hover:scale-105 transition-transform duration-300">
                                        <img :src="owner.profile_picture_url" :alt="owner.name" class="w-full h-full object-cover">
                                    </div>
                                    <div v-else 
                                         class="w-20 h-20 bg-gradient-to-br from-white/20 to-white/10 backdrop-blur-sm rounded-2xl flex items-center justify-center font-bold text-2xl text-white border-4 border-white/20 group-hover:scale-105 transition-transform duration-300">
                                        {{ owner.name?.charAt(0)?.toUpperCase() || 'O' }}
                                    </div>
                                    <!-- Online status indicator -->
                                    <div class="absolute -bottom-1 -right-1 w-6 h-6 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                                        <div 
                                            :class="[
                                                'w-3 h-3 rounded-full',
                                                owner.activity_status === 'online' ? 'bg-green-400 animate-pulse' :
                                                owner.activity_status === 'recently_active' ? 'bg-yellow-400' :
                                                owner.activity_status === 'active_today' ? 'bg-blue-400' :
                                                'bg-gray-400'
                                            ]"
                                        ></div>
                                    </div>
                                </div>
                                
                                <div>
                                    <h1 class="text-3xl font-black text-white mb-2 text-shadow">{{ owner.name }}</h1>
                                    <div class="flex items-center gap-3 mb-3">
                                        <div class="flex items-center gap-2 text-white/90">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10 2L3 7v11a1 1 0 001 1h3a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1h3a1 1 0 001-1V7l-7-5z"/>
                                            </svg>
                                            <span class="text-sm">{{ owner.activity_status_text }}</span>
                                        </div>
                                        
                                        <!-- Verified Badge -->
                                        <div v-if="owner.kyc_status === 'approved'" class="glass-card px-3 py-1">
                                            <span class="inline-flex items-center text-xs font-semibold text-white">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
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
                                <div class="glass-card p-4 border border-white/20">
                                    <h3 class="text-sm font-semibold text-white/90 mb-2 uppercase tracking-wide">About</h3>
                                    <p class="text-white/80 leading-relaxed">{{ owner.bio }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Owner Stats -->
            <div class="bg-[#00000040] backdrop-blur-lg border-b border-white/20 shadow-glow">
                <div class="max-w-7xl mx-auto px-6 py-8">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                        <div class="text-center group">
                            <div class="glass-card-dark bg-gradient-to-br from-[#535862] to-gray-700 rounded-2xl p-6 mb-3 group-hover:scale-105 transition-transform duration-300 border border-white/20 shadow-glow">
                                <svg class="w-8 h-8 text-white mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                <p class="text-2xl font-bold text-white">{{ vehicles.length }}</p>
                            </div>
                            <p class="text-sm text-white font-semibold">Vehicles Listed</p>
                        </div>
                        
                        <div class="text-center group">
                            <div class="glass-card-dark bg-gradient-to-br from-green-500 to-green-600 rounded-2xl p-6 mb-3 group-hover:scale-105 transition-transform duration-300 border border-white/20 shadow-glow">
                                <svg class="w-8 h-8 text-white mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                                </svg>
                                <p class="text-2xl font-bold text-white">{{ stats.total_bookings || '0' }}</p>
                            </div>
                            <p class="text-sm text-white font-semibold">Total Bookings</p>
                        </div>
                        
                        <div class="text-center group">
                            <div class="glass-card-dark bg-gradient-to-br from-yellow-500 to-orange-500 rounded-2xl p-6 mb-3 group-hover:scale-105 transition-transform duration-300 border border-white/20 shadow-glow">
                                <svg class="w-8 h-8 text-white mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                </svg>
                                <p class="text-2xl font-bold text-white">{{ stats.average_rating ? parseFloat(stats.average_rating).toFixed(1) : '4.9' }}</p>
                            </div>
                            <p class="text-sm text-white font-semibold">Average Rating</p>
                        </div>
                        
                        <div class="text-center group">
                            <div class="glass-card-dark bg-gradient-to-br from-purple-500 to-pink-500 rounded-2xl p-6 mb-3 group-hover:scale-105 transition-transform duration-300 border border-white/20 shadow-glow">
                                <svg class="w-8 h-8 text-white mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p class="text-2xl font-bold text-white">{{ formatJoinDate(owner.created_at) }}</p>
                            </div>
                            <p class="text-sm text-white font-semibold">Member Since</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sorting Nav -->
            <div class="bg-[#00000040] backdrop-blur-lg border-b border-white/20">
                <div class="max-w-7xl mx-auto px-6">
                    <div class="flex space-x-6 py-3">
                        <button 
                            @click="sortBy = 'popular'"
                            :class="[
                                'py-2 px-1 text-sm font-medium transition-colors relative',
                                sortBy === 'popular' 
                                    ? 'text-white' 
                                    : 'text-white/70 hover:text-white'
                            ]"
                        >
                            Popular
                            <div v-if="sortBy === 'popular'" class="absolute bottom-0 left-0 right-0 h-0.5 bg-blue-400"></div>
                        </button>
                        <button 
                            @click="sortBy = 'latest'"
                            :class="[
                                'py-2 px-1 text-sm font-medium transition-colors relative',
                                sortBy === 'latest' 
                                    ? 'text-white' 
                                    : 'text-white/70 hover:text-white'
                            ]"
                        >
                            Latest
                            <div v-if="sortBy === 'latest'" class="absolute bottom-0 left-0 right-0 h-0.5 bg-blue-400"></div>
                        </button>
                        <button 
                            @click="sortBy = 'price_low'"
                            :class="[
                                'py-2 px-1 text-sm font-medium transition-colors relative',
                                sortBy === 'price_low' 
                                    ? 'text-white' 
                                    : 'text-white/70 hover:text-white'
                            ]"
                        >
                            Price: Low to High
                            <div v-if="sortBy === 'price_low'" class="absolute bottom-0 left-0 right-0 h-0.5 bg-blue-400"></div>
                        </button>
                        <button 
                            @click="sortBy = 'price_high'"
                            :class="[
                                'py-2 px-1 text-sm font-medium transition-colors relative',
                                sortBy === 'price_high' 
                                    ? 'text-white' 
                                    : 'text-white/70 hover:text-white'
                            ]"
                        >
                            Price: High to Low
                            <div v-if="sortBy === 'price_high'" class="absolute bottom-0 left-0 right-0 h-0.5 bg-blue-400"></div>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Vehicle Grid -->
            <div class="bg-gray-900 min-h-screen">
                <div class="max-w-7xl mx-auto px-6 py-8">
                    <div v-if="sortedVehicles.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        <!-- Vehicle Card -->
                        <div 
                            v-for="vehicle in sortedVehicles" 
                            :key="vehicle.id"
                            class="glass-card-dark border border-white/20 hover:border-white/40 rounded-lg shadow-glow hover:shadow-glow-lg transition-all duration-300 overflow-hidden group cursor-pointer"
                            @click="viewVehicleDetail(vehicle.id)"
                        >
                            <div class="relative">
                                <img 
                                    :src="vehicle.main_photo_url || '/images/placeholder-vehicle.jpg'" 
                                    :alt="`${vehicle.make?.name} ${vehicle.model?.name}`" 
                                    class="w-full h-48 object-cover"
                                >
                                
                                <!-- Price Badge -->
                                <div 
                                    v-if="vehicle.pricing_tiers?.length && (vehicle.is_available && !vehicle.is_booked)"
                                    class="absolute top-3 right-3 bg-gradient-to-r from-green-500/90 to-green-600/90 text-white px-3 py-1.5 rounded-full text-sm font-semibold shadow-lg backdrop-blur-sm"
                                >
                                    From ₱{{ Math.min(...vehicle.pricing_tiers.map(t => parseFloat(t.price))) }}
                                </div>

                                <!-- Category Badge -->
                                <div class="absolute top-3 left-3 bg-white/20 backdrop-blur-sm px-2 py-1 rounded-full text-xs font-medium text-white flex items-center gap-1 shadow-sm border border-white/30">
                                    <span>{{ vehicle.type?.category === 'car' ? '🚗' : '🏍️' }}</span>
                                    <span>{{ vehicle.type?.sub_type || 'Vehicle' }}</span>
                                </div>

                                <!-- Status Badge -->
                                <div v-if="!vehicle.is_available || vehicle.is_booked" class="absolute bottom-3 left-3">
                                    <span 
                                        :class="[
                                            'px-2 py-1 rounded-full text-xs font-medium backdrop-blur-sm',
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
                                <div class="absolute bottom-3 right-3">
                                    <SaveButton
                                        :vehicle-id="vehicle.id"
                                        :initial-saved="vehicle.saves?.length > 0"
                                        :initial-save-count="vehicle.saves_count || 0"
                                        size="sm"
                                        :show-text="false"
                                        :show-count="false"
                                        @click.stop
                                    />
                                </div>
                            </div>
                            
                            <div class="p-3">
                                <h3 class="text-sm font-semibold text-white mb-2 group-hover:text-blue-400 transition-colors">
                                    {{ vehicle.make?.name }} {{ vehicle.model?.name }} {{ vehicle.year }}
                                </h3>
                                
                                <div class="flex items-center justify-between mb-2">
                                    <div v-if="vehicle.ratings_avg_rating > 0" class="flex items-center gap-1">
                                        <div class="flex text-yellow-400">
                                            <template v-for="i in 5" :key="i">
                                                <svg 
                                                    class="w-3 h-3" 
                                                    :class="i <= Math.floor(vehicle.ratings_avg_rating) ? 'fill-current' : 'text-white/30'"
                                                    viewBox="0 0 20 20"
                                                >
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                </svg>
                                            </template>
                                        </div>
                                        <span class="text-xs text-white/70">{{ parseFloat(vehicle.ratings_avg_rating).toFixed(1) }}</span>
                                    </div>
                                    <div v-else class="text-xs text-white/50">No reviews</div>
                                    <span class="text-xs text-white/70">{{ vehicle.location_name || 'Surigao del Norte' }}</span>
                                </div>
                                
                                <div class="flex items-center justify-between">
                                    <div>
                                        <span class="text-lg font-bold text-blue-400">
                                            ₱{{ getMinPrice(vehicle) }}
                                        </span>
                                        <span class="text-xs text-white/70">/day</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div v-else class="text-center py-12">
                        <div class="mx-auto w-24 h-24 glass-card-dark border border-white/20 rounded-full flex items-center justify-center mb-4 shadow-glow">
                            <svg class="w-12 h-12 text-white/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-white mb-1">No Vehicles Found</h3>
                        <p class="text-sm text-white/70">This owner hasn't listed any vehicles yet.</p>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="sortedVehicles.length > itemsPerPage" class="bg-[#00000040] backdrop-blur-lg border-t border-white/20">
                <div class="max-w-7xl mx-auto px-6 py-4">
                    <div class="flex justify-center items-center space-x-1">
                        <button 
                            v-if="currentPage > 1"
                            @click="currentPage--"
                            class="px-3 py-1.5 text-xs font-medium text-white glass-card-dark border border-white/20 rounded-lg hover:border-white/40 transition-colors"
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
                                    ? 'bg-blue-600 text-white border-blue-600' 
                                    : 'text-white glass-card-dark border border-white/20 hover:border-white/40'
                            ]"
                        >
                            {{ page }}
                        </button>
                        
                        <button 
                            v-if="currentPage < totalPages"
                            @click="currentPage++"
                            class="px-3 py-1.5 text-xs font-medium text-white glass-card-dark border border-white/20 rounded-lg hover:border-white/40 transition-colors"
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
import SaveButton from '@/Components/Vehicle/SaveButton.vue'

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

/* Custom float animation */
@keyframes float {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    50% { transform: translateY(-15px) rotate(180deg); }
}

.animate-float {
    animation: float 8s ease-in-out infinite;
}
</style>
