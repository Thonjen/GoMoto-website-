<template>
  <GuestLayout>
    <div class="py-12">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Vehicle Search</h1>

    <div class="bg-white p-6 rounded-lg shadow-md mb-8">
      <form @submit.prevent="applyFilters" class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div>
          <label for="location" class="block text-sm font-medium text-gray-700 mb-2">Location</label>
          <input type="text" id="location" v-model="filters.location"
            class="w-full p-2 border border-gray-300 rounded-md focus:ring-primary-400 focus:border-primary-400"
            placeholder="e.g., Manila, Cebu" />
        </div>
        <div>
          <label for="price" class="block text-sm font-medium text-gray-700 mb-2">Max Price per Day</label>
          <input type="number" id="price" v-model="filters.maxPrice"
            class="w-full p-2 border border-gray-300 rounded-md focus:ring-primary-400 focus:border-primary-400"
            placeholder="e.g., 2500" />
        </div>
        <div>
          <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Vehicle Type</label>
          <select id="type" v-model="filters.type"
            class="w-full p-2 border border-gray-300 rounded-md focus:ring-primary-400 focus:border-primary-400">
            <option value="">All Types</option>
            <option value="sedan">Sedan</option>
            <option value="suv">SUV</option>
            <option value="van">Van</option>
            <option value="motorcycle">Motorcycle</option>
          </select>
        </div>
        <div class="md:col-span-3 flex justify-end">
          <button type="submit"
            class="bg-primary-600 text-white px-6 py-2 rounded-md font-semibold hover:bg-primary-700 transition-colors flex items-center gap-2">
            <Filter class="h-5 w-5" />
            Apply Filters
          </button>
        </div>
      </form>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <div v-for="vehicle in filteredVehicles" :key="vehicle.id"
        class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
        <img :src="vehicle.imageUrl" :alt="vehicle.name" class="w-full h-48 object-cover" />
        <div class="p-4">
          <h3 class="text-xl font-semibold text-gray-800 mb-2">{{ vehicle.name }}</h3>
          <p class="text-gray-600 mb-2">{{ vehicle.location }}</p>
          <div class="flex items-center justify-between">
            <span class="text-lg font-bold text-primary-600">${{ vehicle.pricePerDay }}/day</span>
            <Link :href="`/vehicles/${vehicle.id}`"
              class="bg-primary-600 text-white px-4 py-2 rounded-md text-sm hover:bg-primary-700 transition-colors">
              View Details
            </Link>
          </div>
        </div>
      </div>
      <p v-if="filteredVehicles.length === 0" class="col-span-full text-center text-gray-600">No vehicles found matching your criteria.</p>
    </div>
    </div>
    </div>
  </GuestLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Filter } from 'lucide-vue-next';

const filters = ref({
  location: '',
  maxPrice: null,
  type: '',
});

const allVehicles = ref([
  { id: 1, name: 'Toyota Camry', location: 'Manila', pricePerDay: 1500, type: 'sedan', imageUrl: '/placeholder.svg?height=200&width=300' },
  { id: 2, name: 'Honda CRV', location: 'Cebu', pricePerDay: 2000, type: 'suv', imageUrl: '/placeholder.svg?height=200&width=300' },
  { id: 3, name: 'Ford Ranger', location: 'Davao', pricePerDay: 2500, type: 'suv', imageUrl: '/placeholder.svg?height=200&width=300' },
  { id: 4, name: 'Mitsubishi Mirage', location: 'Quezon City', pricePerDay: 1200, type: 'sedan', imageUrl: '/placeholder.svg?height=200&width=300' },
  { id: 5, name: 'Nissan Terra', location: 'Makati', pricePerDay: 2800, type: 'suv', imageUrl: '/placeholder.svg?height=200&width=300' },
  { id: 6, name: 'Hyundai Starex', location: 'Pasig', pricePerDay: 3000, type: 'van', imageUrl: '/placeholder.svg?height=200&width=300' },
  { id: 7, name: 'Yamaha NMAX', location: 'Taguig', pricePerDay: 800, type: 'motorcycle', imageUrl: '/placeholder.svg?height=200&width=300' },
]);

const filteredVehicles = computed(() => {
  return allVehicles.value.filter(vehicle => {
    const matchesLocation = filters.value.location ? vehicle.location.toLowerCase().includes(filters.value.location.toLowerCase()) : true;
    const matchesPrice = filters.value.maxPrice ? vehicle.pricePerDay <= filters.value.maxPrice : true;
    const matchesType = filters.value.type ? vehicle.type === filters.value.type : true;
    return matchesLocation && matchesPrice && matchesType;
  });
});

const applyFilters = () => {
  // In a real Inertia app, this would trigger a new Inertia request with updated filters
  console.log('Applying filters:', filters.value);
  // Example: Inertia.get('/search', filters.value, { preserveState: true });
};
</script>

<style scoped>
/* Tailwind CSS is used for styling */
</style>
