<template>
  <AppLayout>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
      <aside class="md:col-span-1 bg-white p-6 rounded-lg shadow-md h-fit">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Owner Tools</h2>
        <nav class="flex flex-col gap-2">
          <Link href="/my-vehicles" class="text-primary-600 font-medium hover:underline">My Vehicles</Link>
          <Link href="/my-vehicles/create" class="text-gray-700 hover:underline">Add New Vehicle</Link>
          <Link href="/my-vehicles/bookings" class="text-gray-700 hover:underline">Booking Requests</Link>
          <Link href="/my-payments" class="text-gray-700 hover:underline">My Payments</Link>
        </nav>
      </aside>

      <div class="md:col-span-3 bg-white p-6 rounded-lg shadow-md">
        <div class="flex justify-between items-center mb-6">
          <h1 class="text-3xl font-bold text-gray-800">My Vehicles</h1>
          <Link href="/my-vehicles/create"
            class="bg-primary-600 text-white px-4 py-2 rounded-md font-semibold hover:bg-primary-700 transition-colors flex items-center gap-2">
            <Plus class="h-5 w-5" />
            Add New Vehicle
          </Link>
        </div>

        <div v-if="vehicles.length > 0" class="space-y-6">
          <div v-for="vehicle in vehicles" :key="vehicle.id"
            class="flex flex-col sm:flex-row items-center bg-gray-50 border border-gray-200 rounded-lg p-4 gap-4">
            <img :src="vehicle.imageUrl" :alt="vehicle.name" class="w-full sm:w-32 h-24 object-cover rounded-md" />
            <div class="flex-grow">
              <h3 class="text-xl font-semibold text-gray-800">{{ vehicle.name }}</h3>
              <p class="text-gray-600 text-sm">{{ vehicle.location }}</p>
              <p class="text-primary-600 font-bold mt-1">${{ vehicle.pricePerDay }}/day</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
              <Link :href="`/my-vehicles/${vehicle.id}/edit`"
                class="bg-blue-500 text-white px-4 py-2 rounded-md text-sm hover:bg-blue-600 transition-colors flex items-center justify-center gap-1">
                <Edit class="h-4 w-4" /> Edit
              </Link>
              <button @click="deleteVehicle(vehicle.id)"
                class="bg-red-500 text-white px-4 py-2 rounded-md text-sm hover:bg-red-600 transition-colors flex items-center justify-center gap-1">
                <Trash2 class="h-4 w-4" /> Delete
              </button>
            </div>
          </div>
        </div>
        <p v-else class="text-gray-600 text-center py-8">You have not listed any vehicles yet. Click "Add New Vehicle" to get started!</p>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Plus, Edit, Trash2 } from 'lucide-vue-next';

const vehicles = ref([
  { id: 1, name: 'Toyota Camry 2022', location: 'Manila', pricePerDay: 1800, imageUrl: '/placeholder.svg?height=96&width=128' },
  { id: 2, name: 'Honda CRV 2020', location: 'Cebu', pricePerDay: 2200, imageUrl: '/placeholder.svg?height=96&width=128' },
  { id: 3, name: 'Ford Ranger 2019', location: 'Davao', pricePerDay: 2500, imageUrl: '/placeholder.svg?height=96&width=128' },
]);

const deleteVehicle = (id) => {
  if (confirm('Are you sure you want to delete this vehicle listing?')) {
    vehicles.value = vehicles.value.filter(v => v.id !== id);
    alert(`Vehicle ${id} deleted! (Not actually deleted in this demo)`);
    // In a real Inertia app, this would be an Inertia.delete('/my-vehicles/{id}')
  }
};
</script>

<style scoped>
/* Tailwind CSS is used for styling */
</style>
