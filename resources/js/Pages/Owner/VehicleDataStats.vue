<template>
    <OwnerLayout>
        <div class="glass-card p-6 shadow-glow border border-white/20 max-w-4xl mx-auto">
            <h1 class="text-2xl font-bold text-white mb-6">Philippine Vehicle Data Management</h1>
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-primary-500/20 backdrop-blur-sm p-4 rounded-lg text-center border border-primary-400/30">
                    <h3 class="font-semibold text-primary-300">Car Makes</h3>
                    <p class="text-2xl font-bold text-primary-200">{{ stats.carMakes }}</p>
                </div>
                <div class="bg-green-500/20 backdrop-blur-sm p-4 rounded-lg text-center border border-green-400/30">
                    <h3 class="font-semibold text-green-300">Motorcycle Makes</h3>
                    <p class="text-2xl font-bold text-green-200">{{ stats.motorcycleMakes }}</p>
                </div>
                <div class="bg-purple-500/20 backdrop-blur-sm p-4 rounded-lg text-center border border-purple-400/30">
                    <h3 class="font-semibold text-purple-300">Models</h3>
                    <p class="text-2xl font-bold text-purple-200">{{ stats.models }}</p>
                </div>
                <div class="bg-orange-500/20 backdrop-blur-sm p-4 rounded-lg text-center border border-orange-400/30">
                    <h3 class="font-semibold text-orange-300">Fuel Types</h3>
                    <p class="text-2xl font-bold text-orange-200">{{ stats.fuelTypes }}</p>
                </div>
            </div>

            <div class="mb-6">
                <h2 class="text-lg font-semibold mb-4 text-white">Actions</h2>
                <div class="flex gap-4">
                    <form method="POST" action="/admin/populate-vehicle-data">
                        <input type="hidden" name="_token" :value="csrfToken" />
                        <button 
                            type="submit"
                            class="btn-primary px-4 py-2 font-medium"
                        >
                            Refresh Philippine Vehicle Data
                        </button>
                    </form>
                    
                    <Link 
                        href="/owner/vehicles/create" 
                        class="btn-glass px-4 py-2 font-medium"
                    >
                        Add Vehicle
                    </Link>
                </div>
            </div>

            <div class="text-sm text-white/70">
                <h3 class="font-semibold mb-2 text-white">Data Sources:</h3>
                <ul class="list-disc list-inside space-y-1">
                    <li>Vehicle makes and models are populated from Philippine market data</li>
                    <li>Includes popular car and motorcycle brands available in the Philippines</li>
                    <li>You can refresh the data anytime to reset to the latest Philippine market information</li>
                </ul>
            </div>
        </div>
    </OwnerLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import OwnerLayout from '@/Layouts/OwnerLayout.vue'

const props = defineProps({
    stats: Object
})

const csrfToken = computed(() => {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
})
</script>
