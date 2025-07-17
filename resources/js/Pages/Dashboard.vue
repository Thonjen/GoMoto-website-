<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Search } from 'lucide-vue-next';

const searchQuery = ref('');

const featuredVehicles = ref([
    { id: 1, name: 'Toyota Camry', location: 'Manila', pricePerDay: 1500, imageUrl: '/placeholder.svg?height=200&width=300' },
    { id: 2, name: 'Honda CRV', location: 'Cebu', pricePerDay: 2000, imageUrl: '/placeholder.svg?height=200&width=300' },
    { id: 3, name: 'Ford Ranger', location: 'Davao', pricePerDay: 2500, imageUrl: '/placeholder.svg?height=200&width=300' },
    { id: 4, name: 'Mitsubishi Mirage', location: 'Quezon City', pricePerDay: 1200, imageUrl: '/placeholder.svg?height=200&width=300' },
    { id: 5, name: 'Nissan Terra', location: 'Makati', pricePerDay: 2800, imageUrl: '/placeholder.svg?height=200&width=300' },
]);

const searchVehicles = () => {
    // In a real Inertia app, this would navigate to the search page with query params
    alert(`Searching for: ${searchQuery.value}`);
    // Example: Inertia.visit(`/search?q=${searchQuery.value}`);
};
</script>

<template>

    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div
                    class="flex flex-col items-center justify-center text-center py-16 bg-gradient-to-r from-primary-500 to-primary-600 text-white rounded-lg shadow-lg">
                    <h1 class="text-4xl md:text-5xl font-bold mb-4">Your Next Adventure Starts Here</h1>
                    <p class="text-lg md:text-xl mb-8 max-w-2xl">Find the perfect vehicle for your journey, from sedans
                        to SUVs, all from local owners.</p>
                    <div class="w-full max-w-md px-4">
                        <form @submit.prevent="searchVehicles" class="flex flex-col sm:flex-row gap-4">
                            <input type="text" v-model="searchQuery" placeholder="Search by location or vehicle type..."
                                class="flex-grow p-3 rounded-md border border-gray-300 focus:ring-2 focus:ring-primary-400 focus:border-transparent text-gray-800" />
                            <button type="submit"
                                class="bg-white text-primary-600 px-6 py-3 rounded-md font-semibold hover:bg-gray-100 transition-colors flex items-center justify-center gap-2">
                                <Search class="h-5 w-5" />
                                Search
                            </button>
                        </form>
                    </div>
                </div>

                <section class="py-12">
                    <h2 class="text-3xl font-bold text-gray-800 mb-8 text-center">Featured Vehicles</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                        <div v-for="vehicle in featuredVehicles" :key="vehicle.id"
                            class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
                            <img :src="vehicle.imageUrl" :alt="vehicle.name" class="w-full h-48 object-cover" />
                            <div class="p-4">
                                <h3 class="text-xl font-semibold text-gray-800 mb-2">{{ vehicle.name }}</h3>
                                <p class="text-gray-600 mb-2">{{ vehicle.location }}</p>
                                <div class="flex items-center justify-between">
                                    <span class="text-lg font-bold text-primary-600">${{ vehicle.pricePerDay
                                        }}/day</span>
                                    <Link :href="`/vehicles/${vehicle.id}`"
                                        class="bg-primary-600 text-white px-4 py-2 rounded-md text-sm hover:bg-primary-700 transition-colors">
                                    View Details
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>

                </section>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
