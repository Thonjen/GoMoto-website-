<template>
  <div>
    <h1 class="text-2xl font-bold mb-4">Available Vehicles</h1>
    <div class="mb-4 flex flex-wrap gap-4">
      <select v-model="filters.brand_id" class="border p-2">
        <option value="">All Brands</option>
        <option v-for="b in brands" :key="b.id" :value="b.id">{{ b.name }}</option>
      </select>
      <select v-model="filters.fuel_type_id" class="border p-2">
        <option value="">All Fuel Types</option>
        <option v-for="f in fuelTypes" :key="f.id" :value="f.id">{{ f.name }}</option>
      </select>
      <select v-model="filters.type_id" class="border p-2">
        <option value="">All Types</option>
        <option v-for="t in types" :key="t.id" :value="t.id">{{ t.category }}</option>
      </select>
      <input v-model="filters.color" type="text" placeholder="Color" class="border p-2" />
      <button @click="applyFilters" class="bg-blue-600 text-white px-4 py-2 rounded">Filter</button>
      <button @click="resetFilters" class="bg-gray-300 px-4 py-2 rounded">Reset</button>
    </div>
    <div v-if="vehicles.data.length === 0" class="text-gray-500">No vehicles found.</div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div v-for="vehicle in vehicles.data" :key="vehicle.id" class="border rounded p-4 shadow">
        <img v-if="vehicle.main_photo_url" :src="vehicle.main_photo_url" class="w-full h-40 object-cover rounded mb-2" />
        <div class="font-bold text-lg mb-1">{{ vehicle.license_plate }}</div>
        <div class="mb-1">Brand: {{ vehicle.brand?.name }}</div>
        <div class="mb-1">Type: {{ vehicle.type?.category }}</div>
        <div class="mb-1">Fuel: {{ vehicle.fuel_type?.name }}</div>
        <div class="mb-1">Year: {{ vehicle.year }}</div>
        <div class="mb-1">Color: {{ vehicle.color }}</div>
        <div class="mb-1">Available: <span :class="vehicle.is_available ? 'text-green-600' : 'text-red-600'">{{ vehicle.is_available ? 'Yes' : 'No' }}</span></div>
        <div class="mb-2 truncate">Description: {{ vehicle.description }}</div>
        <a :href="`/owner/vehicles/${vehicle.id}`" class="text-blue-600 underline">View Details</a>
      </div>
    </div>
    <div class="mt-4">
      <button
        v-if="vehicles.prev_page_url"
        @click="goTo(vehicles.prev_page_url)"
        class="mr-2 px-3 py-1 bg-gray-200 rounded"
      >Prev</button>
      <button
        v-if="vehicles.next_page_url"
        @click="goTo(vehicles.next_page_url)"
        class="px-3 py-1 bg-gray-200 rounded"
      >Next</button>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
  vehicles: Object,
  brands: Array,
  types: Array,
  fuelTypes: Array,
  filters: Object,
});

const filters = ref({
  brand_id: props.filters?.brand_id || '',
  fuel_type_id: props.filters?.fuel_type_id || '',
  type_id: props.filters?.type_id || '',
  color: props.filters?.color || '',
});

function applyFilters() {
  router.get('/vehicles', { ...filters.value }, { preserveState: true, replace: true });
}
function resetFilters() {
  filters.value = { brand_id: '', fuel_type_id: '', type_id: '', color: '' };
  applyFilters();
}
function goTo(url) {
  router.visit(url, { preserveState: true });
}
</script>
