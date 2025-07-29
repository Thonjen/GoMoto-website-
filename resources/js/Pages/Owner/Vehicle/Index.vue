<template>
  <div>
    <h1 class="text-2xl font-bold mb-4">My Vehicles</h1>
    <Link href="/owner/vehicles/create" class="bg-blue-600 text-white px-4 py-2 rounded mb-4 inline-block">Add Vehicle</Link>
    <table class="min-w-full bg-white">
      <thead>
        <tr>
          <th class="px-4 py-2">Plate</th>
          <th class="px-4 py-2">Brand</th>
          <th class="px-4 py-2">Type</th>
          <th class="px-4 py-2">Year</th>
          <th class="px-4 py-2">Available</th>
          <th class="px-4 py-2">Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="vehicle in vehicles.data" :key="vehicle.id">
          <td class="border px-4 py-2">{{ vehicle.license_plate }}</td>
          <td class="border px-4 py-2">{{ vehicle.brand?.name }}</td>
          <td class="border px-4 py-2">{{ vehicle.type?.category }}</td>
          <td class="border px-4 py-2">{{ vehicle.year }}</td>
          <td class="border px-4 py-2">
            <span :class="vehicle.is_available ? 'text-green-600' : 'text-red-600'">
              {{ vehicle.is_available ? 'Yes' : 'No' }}
            </span>
          </td>
          <td class="border px-4 py-2">
            <Link :href="`/owner/vehicles/${vehicle.id}`" class="text-blue-600 mr-2">Show</Link>
            <Link :href="`/owner/vehicles/${vehicle.id}/edit`" class="text-yellow-600 mr-2">Edit</Link>
            <button @click="destroy(vehicle.id)" class="text-red-600">Delete</button>
          </td>
        </tr>
      </tbody>
    </table>
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
import { Link, router } from '@inertiajs/vue3';
const props = defineProps({ vehicles: Object });

function destroy(id) {
  if (confirm('Delete this vehicle?')) {
    router.delete(`/owner/vehicles/${id}`);
  }
}
function goTo(url) {
  router.visit(url);
}
</script>
