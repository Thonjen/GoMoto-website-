<template>
    <AuthenticatedLayout>
        <div class="min-h-screen py-8" style="background: linear-gradient(135deg, #535862 0%, #3a3f4a 100%);">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="space-y-8">
                    <!-- Header Section -->
                    <div class="glass-card p-6 shadow-glow">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div>
                                <h1 class="text-3xl font-bold text-white">My Saved Vehicles</h1>
                                <p class="text-white/80 mt-1">
                                    Vehicles you've saved for future booking
                                </p>
                            </div>
                            
                            <!-- Quick Stats -->
                            <div class="flex gap-6">
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-blue-300">{{ stats.total_saves }}</div>
                                    <div class="text-xs text-white/60">Total Saved</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-green-300">{{ stats.total_lists }}</div>
                                    <div class="text-xs text-white/60">Wishlists</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-purple-300">{{ stats.recent_saves }}</div>
                                    <div class="text-xs text-white/60">This Week</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Wishlist Tabs -->
                    <div class="glass-card p-6 shadow-glow">
                        <div class="border-b border-white/20">
                            <nav class="-mb-px flex space-x-8">
                                <div
                                    v-for="list in wishlists"
                                    :key="list"
                                    class="flex items-center"
                                >
                                    <button
                                        @click="switchList(list)"
                                        :class="[
                                            'py-2 px-1 border-b-2 font-medium text-sm transition-colors',
                                            currentList === list
                                                ? 'border-primary-400 text-primary-300'
                                                : 'border-transparent text-white/60 hover:text-white hover:border-white/30'
                                        ]"
                                    >
                                        {{ list }}
                                        <span class="ml-2 bg-white/10 text-white/80 py-0.5 px-2 rounded-full text-xs border border-white/20">
                                            {{ getListCount(list) }}
                                        </span>
                                    </button>
                                    
                                    <!-- Delete List Button (only for custom lists) -->
                                    <button
                                        v-if="list !== 'My Saved Vehicles'"
                                        @click="deleteList(list)"
                                        class="ml-1 text-red-400 hover:text-red-300 p-1 rounded transition-colors"
                                        title="Delete this list"
                                    >
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </nav>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div v-if="savedVehicles.data.length === 0" class="glass-card p-12 shadow-glow text-center">
                        <div class="mx-auto w-32 h-32 bg-white/10 rounded-full flex items-center justify-center mb-6 backdrop-blur-sm border border-white/20">
                            <svg class="w-16 h-16 text-white/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-white mb-3">No saved vehicles in {{ currentList }}</h3>
                        <p class="text-white/80 mb-6 max-w-md mx-auto">
                            Start building your wishlist by saving vehicles you're interested in.
                        </p>
                        <Link
                            :href="route('public.vehicles.index')"
                            class="btn-primary inline-flex items-center gap-2"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Browse Vehicles
                        </Link>
                </div>

                <!-- Saved Vehicles Grid -->
                <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div
                        v-for="save in savedVehicles.data"
                        :key="`${save.vehicle.id}-${save.list_name}`"
                        class="glass-card rounded-xl shadow-glow overflow-hidden hover:shadow-2xl transition-all duration-300 group border border-white/20"
                    >
                        <!-- Vehicle Image -->
                        <div class="relative h-48 bg-gray-200">
                            <img
                                v-if="save.vehicle.main_photo_url"
                                :src="save.vehicle.main_photo_url"
                                :alt="`${save.vehicle.make?.name} ${save.vehicle.model?.name}`"
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

                            <!-- Saved Date Badge -->
                            <div class="absolute top-3 left-3 bg-white bg-opacity-90 backdrop-blur-sm text-gray-800 px-2 py-1 rounded-full text-xs font-medium">
                                Saved {{ formatDate(save.saved_at) }}
                            </div>

                            <!-- Remove from List Button -->
                            <button
                                @click="removeSave(save)"
                                class="absolute top-3 right-3 bg-red-500 bg-opacity-90 text-white p-2 rounded-full hover:bg-red-600 transition-colors opacity-0 group-hover:opacity-100"
                                title="Remove from list"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>

                        <!-- Vehicle Details -->
                        <div class="p-5">
                            <!-- Title -->
                            <div class="mb-3">
                                <h3 class="text-lg font-bold text-white group-hover:text-blue-300 transition-colors">
                                    {{ save.vehicle.make?.name }} {{ save.vehicle.model?.name }}
                                    <span class="text-white/60 font-normal">({{ save.vehicle.year }})</span>
                                </h3>
                                <p class="text-sm text-white/70">{{ save.vehicle.type?.sub_type }}</p>
                            </div>

                            <!-- Rating Display -->
                            <div v-if="save.vehicle.ratings_avg_rating > 0" class="mb-3">
                                <RatingDisplay 
                                    :rating="save.vehicle.ratings_avg_rating" 
                                    :count="save.vehicle.ratings_count"
                                    size="sm"
                                />
                            </div>

                            <!-- Owner Information -->
                            <div v-if="save.vehicle.owner" class="flex items-center gap-2 text-purple-300 bg-purple-500/20 px-3 py-2 rounded-lg mb-3 backdrop-blur-sm border border-purple-400/30">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <span class="text-sm font-medium">Owner: {{ save.vehicle.owner.name }}</span>
                            </div>

                            <!-- Pricing Info -->
                            <div v-if="save.vehicle.pricing_tiers?.length" class="mb-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-2xl font-bold text-green-300">
                                        ₱{{ Math.min(...save.vehicle.pricing_tiers.map(t => parseFloat(t.price))) }}
                                    </span>
                                    <span class="text-sm text-white/60">/day</span>
                                </div>
                            </div>

                            <!-- User Notes -->
                            <div v-if="save.notes" class="mb-4 p-3 bg-yellow-500/20 border border-yellow-400/30 rounded-lg backdrop-blur-sm">
                                <p class="text-sm text-yellow-200">
                                    <span class="font-medium">My notes:</span> {{ save.notes }}
                                </p>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex gap-2">
                                <Link
                                    :href="route('public.vehicles.show', save.vehicle.id)"
                                    class="flex-1 bg-blue-600 text-white py-2 px-4 rounded-lg font-semibold hover:bg-blue-700 transition-colors text-center"
                                >
                                    View Details
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="savedVehicles.last_page > 1" class="flex justify-center">
                    <nav class="flex space-x-2">
                        <Link
                            v-if="savedVehicles.prev_page_url"
                            :href="savedVehicles.prev_page_url"
                            class="px-4 py-2 glass-card text-white rounded hover:bg-white/20 transition-colors border border-white/20"
                        >
                            Previous
                        </Link>
                        <Link
                            v-if="savedVehicles.next_page_url"
                            :href="savedVehicles.next_page_url"
                            class="px-4 py-2 glass-card text-white rounded hover:bg-white/20 transition-colors border border-white/20"
                        >
                            Next
                        </Link>
                    </nav>
                </div>
            </div>
        </div>

        </div>

    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import RatingDisplay from '@/Components/Vehicle/RatingDisplay.vue'

const props = defineProps({
    savedVehicles: Object,
    wishlists: Array,
    currentList: String,
    stats: Object,
})

const getListCount = (listName) => {
    // Use counts from backend stats
    return props.stats?.list_counts?.[listName] || 0
}

const switchList = (listName) => {
    router.get(route('vehicles.saved'), { list: listName })
}

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric'
    })
}

const removeSave = async (save) => {
    if (!confirm(`Remove ${save.vehicle.make?.name} ${save.vehicle.model?.name} from ${save.list_name}?`)) {
        return
    }

    try {
        const response = await fetch('/api/vehicles/save', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                vehicle_id: save.vehicle.id,
                list_name: save.list_name
            })
        })

        if (response.ok) {
            // Reload the page to update the list
            router.reload()
        }
    } catch (error) {
        console.error('Error removing save:', error)
    }
}

const deleteList = async (listName) => {
    if (listName === 'My Saved Vehicles') return
    
    if (!confirm(`Delete the "${listName}" list? All vehicles in this list will be moved to "My Saved Vehicles".`)) {
        return
    }

    try {
        const response = await fetch('/api/vehicles/save/delete-list', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                list_name: listName
            })
        })

        if (response.ok) {
            // If we're currently viewing the deleted list, switch to default
            if (currentList === listName) {
                router.get(route('vehicles.saved'))
            } else {
                router.reload()
            }
        }
    } catch (error) {
        console.error('Error deleting list:', error)
    }
}
</script>
