<template>
  <div>
    <h1 class="text-2xl font-bold mb-4">Vehicle Details</h1>
    <div class="mb-4">
      <div><b>Plate:</b> {{ vehicle.license_plate }}</div>
      <div><b>Brand:</b> {{ vehicle.brand?.name }}</div>
      <div><b>Type:</b> {{ vehicle.type?.category }}</div>
      <div><b>Fuel:</b> {{ vehicle.fuel_type?.name || vehicle.fuelType?.name }}</div>
      <div><b>Year:</b> {{ vehicle.year }}</div>
      <div><b>Color:</b> {{ vehicle.color }}</div>
      <div><b>Description:</b> {{ vehicle.description }}</div>
      <div><b>Available:</b> <span :class="vehicle.is_available ? 'text-green-600' : 'text-red-600'">{{ vehicle.is_available ? 'Yes' : 'No' }}</span></div>
    </div>
    <div class="mb-4">
      <h2 class="font-bold mb-2">Photos</h2>
      <div class="flex flex-wrap gap-4">
        <div v-for="photo in vehicle.photos" :key="photo.id" class="relative">
          <img :src="photo.url" class="w-32 h-24 object-cover rounded" />
          <button @click="deletePhoto(photo.id)" class="absolute top-0 right-0 bg-red-600 text-white px-2 py-1 rounded">X</button>
        </div>
      </div>
      <FilePondUploader :vehicleId="vehicle.id" />
    </div>
    <Link :href="`/owner/vehicles/${vehicle.id}/edit`" class="bg-yellow-600 text-white px-4 py-2 rounded">Edit</Link>
    <Link href="/owner/vehicles" class="ml-2 text-blue-600">Back</Link>
  </div>
</template>

<script setup>
import { router, Link } from '@inertiajs/vue3';
import FilePondUploader from '@/Components/FilePondUploader.vue';
const props = defineProps({ vehicle: Object });

function deletePhoto(photoId) {
  if (confirm('Delete this photo?')) {
    router.delete(`/owner/vehicles/photos/${photoId}`, {
    });
  }
}
</script>
