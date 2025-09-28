<template>
    <button
        @click="toggleSave"
        :disabled="loading"
        :class="[
            'flex items-center gap-1 px-3 py-2 rounded-full text-sm font-medium transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 backdrop-blur-sm border shadow-lg',
            saved 
                ? 'bg-red-500/90 text-white hover:bg-red-600 focus:ring-red-400 border-red-500 shadow-red-500/25' 
                : 'bg-gray-800/90 text-white hover:bg-gray-700 focus:ring-gray-500 border-gray-600 shadow-gray-800/25',
            loading && 'opacity-50 cursor-not-allowed',
            size === 'sm' && 'px-2 py-1 text-xs',
            size === 'lg' && 'px-4 py-3 text-base'
        ]"
        :title="saved ? `Remove from ${listName}` : `Save to ${listName}`"
    >
        <div class="relative">
            <!-- Heart Icon -->
            <svg 
                :class="[
                    'transition-all duration-200',
                    size === 'sm' ? 'w-3 h-3' : size === 'lg' ? 'w-6 h-6' : 'w-4 h-4',
                    saved ? 'text-white scale-110' : 'text-white'
                ]"
                :fill="saved ? 'currentColor' : 'none'"
                stroke="currentColor" 
                viewBox="0 0 24 24"
            >
                <path 
                    stroke-linecap="round" 
                    stroke-linejoin="round" 
                    stroke-width="2" 
                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"
                />
            </svg>
            
            <!-- Loading spinner overlay -->
            <div v-if="loading" class="absolute inset-0 flex items-center justify-center">
                <svg 
                    :class="[
                        'animate-spin',
                        size === 'sm' ? 'w-3 h-3' : size === 'lg' ? 'w-6 h-6' : 'w-4 h-4'
                    ]"
                    fill="none" 
                    viewBox="0 0 24 24"
                >
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
        </div>
        
        <span v-if="showText" class="ml-1">
            {{ saved ? 'Saved' : 'Save' }}
        </span>
        
        <!-- Save count (optional) -->
        <span v-if="showCount && saveCount > 0" class="text-xs opacity-75">
            ({{ saveCount }})
        </span>
    </button>
</template>

<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
    vehicleId: {
        type: [Number, String],
        required: true
    },
    initialSaved: {
        type: Boolean,
        default: false
    },
    initialSaveCount: {
        type: Number,
        default: 0
    },
    listName: {
        type: String,
        default: 'My Saved Vehicles'
    },
    size: {
        type: String,
        default: 'md', // sm, md, lg
        validator: (value) => ['sm', 'md', 'lg'].includes(value)
    },
    showText: {
        type: Boolean,
        default: true
    },
    showCount: {
        type: Boolean,
        default: false
    }
})

const emit = defineEmits(['saved', 'unsaved', 'error'])

const saved = ref(props.initialSaved)
const saveCount = ref(props.initialSaveCount)
const loading = ref(false)

const toggleSave = async () => {
    if (loading.value) return

    loading.value = true

    try {
        const response = await fetch('/api/vehicles/save/toggle', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                vehicle_id: props.vehicleId,
                list_name: props.listName
            })
        })

        if (!response.ok) {
            throw new Error('Failed to toggle save status')
        }

        const data = await response.json()
        
        saved.value = data.saved
        saveCount.value = data.save_count

        // Emit events for parent components
        if (data.saved) {
            emit('saved', {
                vehicleId: props.vehicleId,
                listName: props.listName,
                saveCount: data.save_count
            })
        } else {
            emit('unsaved', {
                vehicleId: props.vehicleId,
                listName: props.listName,
                saveCount: data.save_count
            })
        }

        // Show toast notification
        if (window.showToast) {
            window.showToast(data.message, data.saved ? 'success' : 'info')
        }

    } catch (error) {
        console.error('Error toggling save status:', error)
        emit('error', error)
        
        // Show error notification
        if (window.showToast) {
            window.showToast('Failed to update save status. Please try again.', 'error')
        }
    } finally {
        loading.value = false
    }
}

// Expose methods for parent components
defineExpose({
    toggleSave,
    saved: computed(() => saved.value),
    saveCount: computed(() => saveCount.value)
})
</script>

<style scoped>
/* Additional hover effects */
button:hover .heart-icon {
    transform: scale(1.1);
}

button:active .heart-icon {
    transform: scale(0.95);
}

/* Smooth heart animation */
.heart-icon {
    transition: transform 0.2s ease-in-out;
}
</style>
