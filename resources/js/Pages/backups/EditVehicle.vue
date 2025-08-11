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
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Edit Vehicle: {{ form.name }}</h1>

        <form @submit.prevent="updateVehicle">
          <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Vehicle Name</label>
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
            <label for="newImages" class="block text-sm font-medium text-gray-700 mb-2">Add New Images</label>
            <input type="file" id="newImages" @change="handleNewImageUpload" accept="image/*" multiple
              class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100" />
            <p class="text-xs text-gray-500 mt-1">Existing images will be shown below.</p>

            <div v-if="form.existingImageUrls.length || form.newImagePreviews.length" class="mt-4 grid grid-cols-2 sm:grid-cols-3 gap-4">
              <div v-for="(img, index) in form.existingImageUrls" :key="`existing-${index}`" class="relative">
                <img :src="img" class="w-full h-32 object-cover rounded-md border border-gray-200" />
                <button @click="removeExistingImage(index)" type="button"
                  class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 text-xs hover:bg-red-600">
                  <X class="h-4 w-4" />
                </button>
              </div>
              <div v-for="(img, index) in form.newImagePreviews" :key="`new-${index}`" class="relative">
                <img :src="img" class="w-full h-32 object-cover rounded-md border border-gray-200" />
                <button @click="removeNewImage(index)" type="button"
                  class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 text-xs hover:bg-red-600">
                  <X class="h-4 w-4" />
                </button>
              </div>
            </div>
          </div>

          <button type="submit"
            class="bg-primary-600 text-white px-6 py-3 rounded-md font-semibold hover:bg-primary-700 transition-colors flex items-center justify-center gap-2">
            <Save class="h-5 w-5" />
            Update Vehicle
          </button>
        </form>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Save, X } from 'lucide-vue-next';

const page = usePage();
const vehicleId = page.props.id || 1; // Get ID from route params, default to 1 for demo

const form = ref({
  name: '',
  type: '',
  location: '',
  pricePerDay: null,
  description: '',
  existingImageUrls: [], // URLs of images already on server
  newImages: [], // New file objects to upload
  newImagePreviews: [], // URLs for new image previews
});

onMounted(() => {
  // Simulate fetching existing vehicle data
  const existingVehicle = {
    id: vehicleId,
    name: 'Toyota Camry 2022',
    type: 'sedan',
    location: 'Manila',
    pricePerDay: 1800,
    description: 'A comfortable and reliable Toyota Camry, perfect for city driving or long road trips. Well-maintained and spacious interior. Equipped with air conditioning, Bluetooth, and GPS.',
    images: [
      '/placeholder.svg?height=128&width=192',
      '/placeholder.svg?height=128&width=192',
    ],
  };

  form.value.name = existingVehicle.name;
  form.value.type = existingVehicle.type;
  form.value.location = existingVehicle.location;
  form.value.pricePerDay = existingVehicle.pricePerDay;
  form.value.description = existingVehicle.description;
  form.value.existingImageUrls = existingVehicle.images;
});

const handleNewImageUpload = (event) => {
  const files = Array.from(event.target.files);
  form.value.newImages = [...form.value.newImages, ...files];
  files.forEach(file => {
    const reader = new FileReader();
    reader.onload = (e) => {
      form.value.newImagePreviews.push(e.target.result);
    };
    reader.readAsDataURL(file);
  });
};

const removeExistingImage = (index) => {
  if (confirm('Are you sure you want to remove this existing image?')) {
    form.value.existingImageUrls.splice(index, 1);
    alert('Existing image removed. (Not actually removed from server in this demo)');
    // In a real Inertia app, you'd send a request to delete this image from the server
  }
};

const removeNewImage = (index) => {
  form.value.newImages.splice(index, 1);
  form.value.newImagePreviews.splice(index, 1);
};

const updateVehicle = () => {
  alert(`Vehicle ${vehicleId} updated! (Not actually saved in this demo)`);
  console.log('Updated vehicle data:', form.value);
  // In a real Inertia app, this would be an Inertia.post or Inertia.put request
  // You'd likely use FormData to send files and indicate which existing images to keep/delete
};
</script>

<style scoped>
/* Tailwind CSS is used for styling */
</style>
