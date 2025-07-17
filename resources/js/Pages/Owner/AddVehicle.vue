<template>
  <AppLayout>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
      <aside class="md:col-span-1 bg-white p-6 rounded-lg shadow-md h-fit">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Owner Tools</h2>
        <nav class="flex flex-col gap-2">
          <Link href="/my-vehicles" class="text-gray-700 hover:underline">My Vehicles</Link>
          <Link href="/my-vehicles/create" class="text-primary-600 font-medium hover:underline">Add New Vehicle</Link>
          <Link href="/my-vehicles/bookings" class="text-gray-700 hover:underline">Booking Requests</Link>
          <Link href="/my-payments" class="text-gray-700 hover:underline">My Payments</Link>
        </nav>
      </aside>

      <div class="md:col-span-3 bg-white p-6 rounded-lg shadow-md">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Add New Vehicle</h1>

        <form @submit.prevent="submitVehicle">
          <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Vehicle Name (e.g., Toyota Camry 2022)</label>
            <input type="text" id="name" v-model="form.name" required
              class="w-full p-3 border border-gray-300 rounded-md focus:ring-primary-400 focus:border-primary-400" />
          </div>
          <div class="mb-4">
            <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Vehicle Type</label>
            <select id="type" v-model="form.type" required
              class="w-full p-3 border border-gray-300 rounded-md focus:ring-primary-400 focus:border-primary-400">
              <option value="">Select Type</option>
              <option value="sedan">Sedan</option>
              <option value="suv">SUV</option>
              <option value="van">Van</option>
              <option value="motorcycle">Motorcycle</option>
              <option value="truck">Truck</option>
            </select>
          </div>
          <div class="mb-4">
            <label for="location" class="block text-sm font-medium text-gray-700 mb-2">Location</label>
            <input type="text" id="location" v-model="form.location" required
              class="w-full p-3 border border-gray-300 rounded-md focus:ring-primary-400 focus:border-primary-400" />
          </div>
          <div class="mb-4">
            <label for="pricePerDay" class="block text-sm font-medium text-gray-700 mb-2">Price per Day (PHP)</label>
            <input type="number" id="pricePerDay" v-model="form.pricePerDay" required
              class="w-full p-3 border border-gray-300 rounded-md focus:ring-primary-400 focus:border-primary-400" />
          </div>
          <div class="mb-4">
            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
            <textarea id="description" v-model="form.description" rows="5"
              class="w-full p-3 border border-gray-300 rounded-md focus:ring-primary-400 focus:border-primary-400"></textarea>
          </div>
          <div class="mb-6">
            <label for="images" class="block text-sm font-medium text-gray-700 mb-2">Vehicle Images</label>
            <input type="file" id="images" @change="handleImageUpload" accept="image/*" multiple
              class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100" />
            <div v-if="form.imagePreviews.length" class="mt-4 grid grid-cols-2 sm:grid-cols-3 gap-4">
              <div v-for="(img, index) in form.imagePreviews" :key="index" class="relative">
                <img :src="img" class="w-full h-32 object-cover rounded-md border border-gray-200" />
                <button @click="removeImage(index)" type="button"
                  class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 text-xs hover:bg-red-600">
                  <X class="h-4 w-4" />
                </button>
              </div>
            </div>
          </div>

          <button type="submit"
            class="bg-primary-600 text-white px-6 py-3 rounded-md font-semibold hover:bg-primary-700 transition-colors flex items-center justify-center gap-2">
            <PlusCircle class="h-5 w-5" />
            Add Vehicle
          </button>
        </form>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { PlusCircle, X } from 'lucide-vue-next';

const form = ref({
  name: '',
  type: '',
  location: '',
  pricePerDay: null,
  description: '',
  images: [], // File objects
  imagePreviews: [], // URLs for preview
});

const handleImageUpload = (event) => {
  const files = Array.from(event.target.files);
  form.value.images = [...form.value.images, ...files];
  files.forEach(file => {
    const reader = new FileReader();
    reader.onload = (e) => {
      form.value.imagePreviews.push(e.target.result);
    };
    reader.readAsDataURL(file);
  });
};

const removeImage = (index) => {
  form.value.images.splice(index, 1);
  form.value.imagePreviews.splice(index, 1);
};

const submitVehicle = () => {
  if (form.value.images.length === 0) {
    alert('Please upload at least one vehicle image.');
    return;
  }
  alert('Vehicle added successfully! (Not actually saved in this demo)');
  console.log('Vehicle data:', form.value);
  // In a real Inertia app, this would be an Inertia.post('/my-vehicles', form.value)
  // You'd likely use FormData to send files
};
</script>

<style scoped>
/* Tailwind CSS is used for styling */
</style>
