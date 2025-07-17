<template>
  <AppLayout>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
      <aside class="md:col-span-1 bg-white p-6 rounded-lg shadow-md h-fit">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Admin Panel</h2>
        <nav class="flex flex-col gap-2">
          <Link href="/admin/users" class="text-gray-700 hover:underline">User Management</Link>
          <Link href="/admin/vehicles" class="text-primary-600 font-medium hover:underline">Vehicle Listings</Link>
          <Link href="/admin/bookings" class="text-gray-700 hover:underline">Bookings</Link>
          <Link href="/admin/licenses" class="text-gray-700 hover:underline">License Verifications</Link>
          <Link href="/admin/disputes" class="text-gray-700 hover:underline">Disputes</Link>
          <Link href="/admin/reports" class="text-gray-700 hover:underline">Reports</Link>
        </nav>
      </aside>

      <div class="md:col-span-3 bg-white p-6 rounded-lg shadow-md">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Vehicle Listings Moderation</h1>

        <div class="mb-6">
          <input type="text" v-model="searchQuery" placeholder="Search vehicles by name or owner..."
            class="w-full p-3 border border-gray-300 rounded-md focus:ring-primary-400 focus:border-primary-400" />
        </div>

        <div v-if="filteredVehicles.length > 0" class="overflow-x-auto">
          <table class="min-w-full bg-white border border-gray-200 rounded-lg">
            <thead>
              <tr class="bg-gray-100 text-left text-sm font-semibold text-gray-700">
                <th class="py-3 px-4 border-b">Vehicle</th>
                <th class="py-3 px-4 border-b">Owner</th>
                <th class="py-3 px-4 border-b">Location</th>
                <th class="py-3 px-4 border-b">Price/Day</th>
                <th class="py-3 px-4 border-b">Status</th>
                <th class="py-3 px-4 border-b">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="vehicle in filteredVehicles" :key="vehicle.id" class="hover:bg-gray-50 border-b border-gray-100 last:border-b-0">
                <td class="py-3 px-4">
                  <div class="font-medium text-gray-800">{{ vehicle.name }}</div>
                  <div class="text-sm text-gray-600">{{ vehicle.type }}</div>
                </td>
                <td class="py-3 px-4 text-gray-700">{{ vehicle.ownerName }}</td>
                <td class="py-3 px-4 text-gray-700">{{ vehicle.location }}</td>
                <td class="py-3 px-4 font-medium text-primary-600">₱{{ vehicle.pricePerDay.toLocaleString() }}</td>
                <td class="py-3 px-4">
                  <span :class="['px-3 py-1 rounded-full text-xs font-medium',
                    vehicle.isApproved ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800']">
                    {{ vehicle.isApproved ? 'Approved' : 'Pending' }}
                  </span>
                </td>
                <td class="py-3 px-4 flex gap-2">
                  <button v-if="!vehicle.isApproved" @click="approveVehicle(vehicle)"
                    class="bg-green-500 text-white px-3 py-1 rounded-md text-xs font-medium hover:bg-green-600">
                    Approve
                  </button>
                  <button @click="deleteVehicle(vehicle.id)"
                    class="bg-red-500 text-white px-3 py-1 rounded-md text-xs font-medium hover:bg-red-600">
                    Delete
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <p v-else class="text-gray-600 text-center py-8">No vehicle listings found.</p>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const vehicles = ref([
  { id: 1, name: 'Toyota Camry', type: 'Sedan', ownerName: 'John Doe', location: 'Manila', pricePerDay: 1500, isApproved: true },
  { id: 2, name: 'Honda CRV', type: 'SUV', ownerName: 'Jane Smith', location: 'Cebu', pricePerDay: 2000, isApproved: false },
  { id: 3, name: 'Ford Ranger', type: 'SUV', ownerName: 'Peter Jones', location: 'Davao', pricePerDay: 2500, isApproved: true },
  { id: 4, name: 'Mitsubishi Mirage', type: 'Sedan', ownerName: 'Alice Brown', location: 'Quezon City', pricePerDay: 1200, isApproved: false },
]);

const searchQuery = ref('');

const filteredVehicles = computed(() => {
  const query = searchQuery.value.toLowerCase();
  return vehicles.value.filter(vehicle =>
    vehicle.name.toLowerCase().includes(query) ||
    vehicle.ownerName.toLowerCase().includes(query)
  );
});

const approveVehicle = (vehicle) => {
  vehicle.isApproved = true;
  alert(`Vehicle "${vehicle.name}" approved! (Not actually updated in this demo)`);
  // In a real Inertia app, this would be an Inertia.post or Inertia.put request
};

const deleteVehicle = (id) => {
  if (confirm('Are you sure you want to delete this vehicle listing?')) {
    vehicles.value = vehicles.value.filter(v => v.id !== id);
    alert(`Vehicle ${id} deleted! (Not actually deleted in this demo)`);
    // In a real Inertia app, this would be an Inertia.delete request
  }
};
</script>

<style scoped>
/* Tailwind CSS is used for styling */
</style>
