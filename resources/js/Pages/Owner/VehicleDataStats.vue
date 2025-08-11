<template>
    <OwnerLayout>
        <div class="bg-white p-6 rounded-lg shadow-md max-w-4xl mx-auto">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">Philippine Vehicle Data Management</h1>
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-blue-50 p-4 rounded-lg text-center">
                    <h3 class="font-semibold text-blue-800">Car Makes</h3>
                    <p class="text-2xl font-bold text-blue-600">{{ stats.carMakes }}</p>
                </div>
                <div class="bg-green-50 p-4 rounded-lg text-center">
                    <h3 class="font-semibold text-green-800">Motorcycle Makes</h3>
                    <p class="text-2xl font-bold text-green-600">{{ stats.motorcycleMakes }}</p>
                </div>
                <div class="bg-purple-50 p-4 rounded-lg text-center">
                    <h3 class="font-semibold text-purple-800">Models</h3>
                    <p class="text-2xl font-bold text-purple-600">{{ stats.models }}</p>
                </div>
                <div class="bg-orange-50 p-4 rounded-lg text-center">
                    <h3 class="font-semibold text-orange-800">Fuel Types</h3>
                    <p class="text-2xl font-bold text-orange-600">{{ stats.fuelTypes }}</p>
                </div>
            </div>

            <div class="mb-6">
                <h2 class="text-lg font-semibold mb-4">Actions</h2>
                <div class="flex gap-4">
                    <form method="POST" action="/admin/populate-vehicle-data">
                        <input type="hidden" name="_token" :value="csrfToken" />
                        <button 
                            type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition-colors"
                        >
                            Refresh Philippine Vehicle Data
                        </button>
                    </form>
                    
                    <Link 
                        href="/owner/vehicles/create" 
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md transition-colors"
                    >
                        Add Vehicle
                    </Link>
                </div>
            </div>

            <div class="text-sm text-gray-600">
                <h3 class="font-semibold mb-2">Data Sources:</h3>
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
