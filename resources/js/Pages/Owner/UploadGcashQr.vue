<template>
  <AppLayout>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
      <aside class="md:col-span-1 bg-white p-6 rounded-lg shadow-md h-fit">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Owner Tools</h2>
        <nav class="flex flex-col gap-2">
          <Link href="/my-vehicles" class="text-gray-700 hover:underline">My Vehicles</Link>
          <Link href="/my-vehicles/create" class="text-gray-700 hover:underline">Add New Vehicle</Link>
          <Link href="/my-vehicles/bookings" class="text-gray-700 hover:underline">Booking Requests</Link>
          <Link href="/my-payments" class="text-primary-600 font-medium hover:underline">My Payments</Link>
        </nav>
      </aside>

      <div class="md:col-span-3 bg-white p-6 rounded-lg shadow-md">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Upload GCash QR Code</h1>

        <p class="text-gray-700 mb-6">Upload your static GCash QR code image here. This will allow renters to easily pay you for bookings.</p>

        <div v-if="gcashQr.imageUrl" class="mb-8 text-center">
          <h2 class="text-2xl font-semibold text-gray-800 mb-4">Current GCash QR Code</h2>
          <img :src="gcashQr.imageUrl" alt="GCash QR Code" class="w-64 h-64 object-contain mx-auto border border-gray-200 rounded-md p-2" />
          <p class="text-sm text-gray-600 mt-2">Uploaded on: {{ gcashQr.uploadDate }}</p>
          <button @click="removeQrCode"
            class="mt-6 bg-red-500 text-white px-6 py-3 rounded-md font-semibold hover:bg-red-600 transition-colors flex items-center justify-center gap-2 mx-auto">
            <Trash2 class="h-5 w-5" />
            Remove QR Code
          </button>
        </div>

        <h2 class="text-2xl font-semibold text-gray-800 mb-4">{{ gcashQr.imageUrl ? 'Update QR Code' : 'Upload QR Code' }}</h2>
        <form @submit.prevent="uploadQrCode">
          <div class="mb-6">
            <label for="qrCodeFile" class="block text-sm font-medium text-gray-700 mb-2">Select QR Code Image</label>
            <input type="file" id="qrCodeFile" @change="handleFileChange" accept="image/png, image/jpeg" required
              class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100" />
            <p v-if="selectedFile" class="text-xs text-gray-500 mt-1">Selected: {{ selectedFile.name }}</p>
          </div>
          <button type="submit"
            class="bg-primary-600 text-white px-6 py-3 rounded-md font-semibold hover:bg-primary-700 transition-colors flex items-center justify-center gap-2">
            <UploadCloud class="h-5 w-5" />
            {{ gcashQr.imageUrl ? 'Update QR Code' : 'Upload QR Code' }}
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
import { UploadCloud, Trash2 } from 'lucide-vue-next';

const gcashQr = ref({
  imageUrl: '/placeholder.svg?height=256&width=256', // Simulate an existing QR code
  uploadDate: '2025-06-01',
});

const selectedFile = ref(null);

const handleFileChange = (event) => {
  selectedFile.value = event.target.files[0];
};

const uploadQrCode = () => {
  if (!selectedFile.value) {
    alert('Please select a QR code image to upload.');
    return;
  }
  alert('GCash QR code uploaded/updated! (Not actually saved in this demo)');
  // Simulate successful upload
  gcashQr.value.imageUrl = URL.createObjectURL(selectedFile.value);
  gcashQr.value.uploadDate = new Date().toISOString().slice(0, 10);
  selectedFile.value = null; // Clear selected file
  // In a real Inertia app, this would be an Inertia.post('/my-payments/qr', { qr_code: selectedFile.value })
};

const removeQrCode = () => {
  if (confirm('Are you sure you want to remove your GCash QR code?')) {
    gcashQr.value.imageUrl = '';
    gcashQr.value.uploadDate = '';
    alert('GCash QR code removed. (Not actually removed in this demo)');
    // In a real Inertia app, this would be an Inertia.delete('/my-payments/qr')
  }
};
</script>

<style scoped>
/* Tailwind CSS is used for styling */
</style>
