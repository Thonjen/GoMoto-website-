<template>
    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="space-y-8">
                <!-- Header Section -->
                <div class="mb-8">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">My Saved Vehicles</h1>
                            <p class="text-gray-600 mt-1">
                                Vehicles you've saved for future booking
                            </p>
                        </div>
                        
                        <!-- Quick Stats -->
                        <div class="flex gap-4">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-blue-600">{{ stats.total_saves }}</div>
                                <div class="text-xs text-gray-500">Total Saved</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-green-600">{{ stats.total_lists }}</div>
                                <div class="text-xs text-gray-500">Wishlists</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-purple-600">{{ stats.recent_saves }}</div>
                                <div class="text-xs text-gray-500">This Week</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Wishlist Tabs -->
                <div class="mb-6">
                    <div class="border-b border-gray-200">
                        <nav class="-mb-px flex space-x-8">
                            <div
                                v-for="list in wishlists"
                                :key="list"
                                class="flex items-center"
                            >
                                class="ml-1 text-red-400 hover:text-red-600 p-1 rounded transition-colors"
                                title="Delete this list"
                            >
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-8">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">My Saved Vehicles</h1>
                            <p class="text-gray-600 mt-1">
                                Vehicles you've saved for future booking
                            </p>
                        </div>
                        
                        <!-- Quick Stats -->
                        <div class="flex gap-4">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-blue-600">{{ stats.total_saves }}</div>
                                <div class="text-xs text-gray-500">Total Saved</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-green-600">{{ stats.total_lists }}</div>
                                <div class="text-xs text-gray-500">Wishlists</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-purple-600">{{ stats.recent_saves }}</div>
                                <div class="text-xs text-gray-500">This Week</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Wishlist Tabs -->
                <div class="mb-6">
                    <div class="border-b border-gray-200">
                        <nav class="-mb-px flex space-x-8">
                            <button
                                v-for="list in wishlists"
                                :key="list"
                                @click="switchList(list)"
                                :class="[
                                    'py-2 px-1 border-b-2 font-medium text-sm transition-colors',
                                    currentList === list
                                        ? 'border-blue-500 text-blue-600'
                                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                                ]"
                            >
                                {{ list }}
                                <span class="ml-2 bg-gray-100 text-gray-600 py-0.5 px-2 rounded-full text-xs">
                                    {{ getListCount(list) }}
                                </span>
                            </button>
                            
                            <!-- Create New List Button -->
                            <button
                                @click="showCreateListModal = true"
                                class="py-2 px-1 border-b-2 border-transparent text-gray-400 hover:text-gray-600 font-medium text-sm transition-colors flex items-center"
                            >
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                New List
                            </button>
                        </nav>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-if="savedVehicles.data.length === 0" class="text-center py-16">
                    <div class="mx-auto w-32 h-32 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mb-6">
                        <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">No saved vehicles in {{ currentList }}</h3>
                    <p class="text-gray-600 mb-6 max-w-md mx-auto">
                        Start building your wishlist by saving vehicles you're interested in.
                    </p>
                    <Link
                        :href="route('public.vehicles.index')"
                        class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition-colors"
                    >
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                        class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-all duration-300 group"
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
                                <h3 class="text-lg font-bold text-gray-900 group-hover:text-blue-600 transition-colors">
                                    {{ save.vehicle.make?.name }} {{ save.vehicle.model?.name }}
                                    <span v-if="save.vehicle.year" class="text-gray-600 font-normal">({{ save.vehicle.year }})</span>
                                </h3>
                                <p class="text-sm text-gray-600">{{ save.vehicle.type?.sub_type }}</p>
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
                            <div v-if="save.vehicle.owner" class="flex items-center gap-2 text-purple-600 bg-purple-50 px-3 py-2 rounded-lg mb-3">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <span class="text-sm font-medium">Owner: {{ save.vehicle.owner.name }}</span>
                            </div>

                            <!-- Pricing Info -->
                            <div v-if="save.vehicle.pricing_tiers?.length" class="mb-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-2xl font-bold text-green-600">
                                        ₱{{ Math.min(...save.vehicle.pricing_tiers.map(t => parseFloat(t.price))) }}
                                    </span>
                                    <span class="text-sm text-gray-500">/day</span>
                                </div>
                            </div>

                            <!-- User Notes -->
                            <div v-if="save.notes" class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                                <p class="text-sm text-yellow-800">
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
                                <button
                                    @click="moveToList(save)"
                                    class="bg-gray-100 text-gray-600 py-2 px-3 rounded-lg hover:bg-gray-200 transition-colors"
                                    title="Move to different list"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="savedVehicles.last_page > 1" class="mt-8 flex justify-center">
                    <nav class="flex space-x-2">
                        <Link
                            v-if="savedVehicles.prev_page_url"
                            :href="savedVehicles.prev_page_url"
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition-colors"
                        >
                            Previous
                        </Link>
                        <Link
                            v-if="savedVehicles.next_page_url"
                            :href="savedVehicles.next_page_url"
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition-colors"
                        >
                            Next
                        </Link>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Create New List Modal -->
        <div v-if="showCreateListModal" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showCreateListModal = false"></div>
                
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form @submit.prevent="createNewList">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Create New Wishlist</h3>
                            <input
                                v-model="newListName"
                                type="text"
                                placeholder="Enter wishlist name..."
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                required
                                maxlength="100"
                            />
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button
                                type="submit"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm"
                            >
                                Create List
                            </button>
                            <button
                                type="button"
                                @click="showCreateListModal = false"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                            >
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Move to List Modal -->
        <div v-if="showMoveModal" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showMoveModal = false"></div>
                
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form @submit.prevent="moveVehicleToList">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Move Vehicle to List</h3>
                            <p class="text-sm text-gray-600 mb-4">
                                Move {{ selectedSave?.vehicle?.make?.name }} {{ selectedSave?.vehicle?.model?.name }} to a different list:
                            </p>
                            <select
                                v-model="targetListName"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                required
                            >
                                <option value="">Select destination list...</option>
                                <option 
                                    v-for="list in wishlists.filter(l => l !== currentList)"
                                    :key="list"
                                    :value="list"
                                >
                                    {{ list }}
                                </option>
                            </select>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button
                                type="submit"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm"
                            >
                                Move Vehicle
                            </button>
                            <button
                                type="button"
                                @click="showMoveModal = false"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                            >
                                Cancel
                            </button>
                        </div>
                    </form>
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

const showCreateListModal = ref(false)
const newListName = ref('')
const showMoveModal = ref(false)
const selectedSave = ref(null)
const targetListName = ref('')

const getListCount = (listName) => {
    // This would need to be passed from the backend or calculated
    return 0
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

const createNewList = async () => {
    if (!newListName.value.trim()) return

    try {
        const response = await fetch('/api/vehicles/save/create-list', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                list_name: newListName.value.trim()
            })
        })

        if (response.ok) {
            showCreateListModal.value = false
            newListName.value = ''
            router.reload()
        }
    } catch (error) {
        console.error('Error creating list:', error)
    }
}

const moveToList = (save) => {
    selectedSave.value = save
    targetListName.value = ''
    showMoveModal.value = true
}

const moveVehicleToList = async () => {
    if (!targetListName.value || !selectedSave.value) return

    try {
        const response = await fetch('/api/vehicles/save/move', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                vehicle_id: selectedSave.value.vehicle.id,
                from_list: selectedSave.value.list_name,
                to_list: targetListName.value
            })
        })

        if (response.ok) {
            showMoveModal.value = false
            selectedSave.value = null
            targetListName.value = ''
            router.reload()
        }
    } catch (error) {
        console.error('Error moving vehicle:', error)
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
