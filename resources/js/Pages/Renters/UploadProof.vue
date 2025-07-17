<template>
  <AppLayout>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
      <aside class="md:col-span-1 bg-white p-6 rounded-lg shadow-md h-fit">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Renter Tools</h2>
        <nav class="flex flex-col gap-2">
          <Link href="/my-bookings" class="text-primary-600 font-medium hover:underline">My Bookings</Link>
        </nav>
      </aside>

      <div class="md:col-span-3 bg-white p-6 rounded-lg shadow-md">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Upload Proof of Payment for Booking #{{ bookingId }}</h1>

        <div class="bg-blue-50 p-4 rounded-md mb-6">
          <p class="font-semibold text-blue-800">Booking Details:</p>
          <p class="text-blue-700">Vehicle: {{ bookingDetails.vehicleName }}</p>
          <p class="text-blue-700">Owner: {{ bookingDetails.ownerName }}</p>
          <p class="text-blue-700">Dates: {{ bookingDetails.pickupDate }} - {{ bookingDetails.returnDate }}</p>
          <p class="text-blue-700 font-bold">Amount Due: ₱{{ bookingDetails.totalPrice.toLocaleString() }}</p>
        </div>

        <p class="text-gray-700 mb-6">Please upload a screenshot or photo of your GCash payment receipt. This will be sent to the owner for verification.</p>

        <div v-if="proofOfPayment.imageUrl" class="mb-8 text-center">
          <h2 class="text-2xl font-semibold text-gray-800 mb-4">Current Proof of Payment</h2>
          <img :src="proofOfPayment.imageUrl" alt="Proof of Payment" class="w-64 h-64 object-contain mx-auto border border-gray-200 rounded-md p-2" />
          <p class="text-sm text-gray-600 mt-2">Uploaded on: {{ proofOfPayment.uploadDate }}</p>
          <button @click="removeProof"
            class="mt-6 bg-red-500 text-white px-6 py-3 rounded-md font-semibold hover:bg-red-600 transition-colors flex items-center justify-center gap-2 mx-auto">
            <Trash2 class="h-5 w-5" />
            Remove Proof
          </button>
        </div>

        <h2 class="text-2xl font-semibold text-gray-800 mb-4">{{ proofOfPayment.imageUrl ? 'Update Proof' : 'Upload Proof' }}</h2>
        <form @submit.prevent="uploadProof">
          <div class="mb-6">
            <label for="receiptFile" class="block text-sm font-medium text-gray-700 mb-2">Select Receipt Image</label>
            <input type="file" id="receiptFile" @change="handleFileChange" accept="image/png, image/jpeg" required
              class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100" />
            <p v-if="selectedFile" class="text-xs text-gray-500 mt-1">Selected: {{ selectedFile.name }}</p>
          </div>
          <button type="submit"
            class="bg-primary-600 text-white px-6 py-3 rounded-md font-semibold hover:bg-primary-700 transition-colors flex items-center justify-center gap-2">
            <UploadCloud class="h-5 w-5" />
            {{ proofOfPayment.imageUrl ? 'Update Proof' : 'Upload Proof' }}
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
import { UploadCloud, Trash2 } from 'lucide-vue-next';

const page = usePage();
const bookingId = page.props.id || 1; // Get ID from route params, default to 1 for demo

const bookingDetails = ref({
  vehicleName: 'Toyota Camry',
  ownerName: 'Juan Dela Cruz',
  pickupDate: '2025-07-20',
  returnDate: '2025-07-25',
  totalPrice: 9000,
});

const proofOfPayment = ref({
  imageUrl: '', // Simulate no existing proof
  uploadDate: '',
});

const selectedFile = ref(null);

onMounted(() => {
  // Simulate fetching booking details and existing proof
  // In a real app, this data would be passed as props from the controller
  if (bookingId === 1) { // Example for booking ID 1
    proofOfPayment.value.imageUrl = '/placeholder.svg?height=256&width=256';
    proofOfPayment.value.uploadDate = '2025-07-18';
  }
});

const handleFileChange = (event) => {
  selectedFile.value = event.target.files[0];
};

const uploadProof = () => {
  if (!selectedFile.value) {
    alert('Please select a receipt image to upload.');
    return;
  }
  alert('Proof of payment uploaded! (Not actually saved in this demo)');
  // Simulate successful upload
  proofOfPayment.value.imageUrl = URL.createObjectURL(selectedFile.value);
  proofOfPayment.value.uploadDate = new Date().toISOString().slice(0, 10);
  selectedFile.value = null; // Clear selected file
  // In a real Inertia app, this would be an Inertia.post(`/my-bookings/${bookingId}/receipt`, { receipt: selectedFile.value })
};

const removeProof = () => {
  if (confirm('Are you sure you want to remove this proof of payment?')) {
    proofOfPayment.value.imageUrl = '';
    proofOfPayment.value.uploadDate = '';
    alert('Proof of payment removed. (Not actually removed in this demo)');
    // In a real Inertia app, this would be an Inertia.delete(`/my-bookings/${bookingId}/receipt`)
  }
};
</script>

<style scoped>
/* Tailwind CSS is used for styling */
</style>
