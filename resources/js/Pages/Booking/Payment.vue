<template>
  <div class="max-w-xl mx-auto p-6 bg-white rounded shadow">
    <h1 class="text-2xl font-bold mb-4">Pay for Your Booking</h1>
    <div class="mb-4">
      <div class="font-semibold mb-2">Amount Due:</div>
      <div class="text-2xl font-bold text-green-700 mb-2">₱{{ booking.total_amount }}</div>
      <div class="mb-2">
        <span class="font-semibold">GCash QR Code:</span>
        <div v-if="gcashQrUrl" class="my-2">
          <img :src="gcashQrUrl" alt="GCash QR" class="w-48 h-48 object-contain border rounded" />
        </div>
        <div v-else class="text-gray-500">No QR code available.</div>
      </div>
    </div>
    <form @submit.prevent="submit" enctype="multipart/form-data" class="mb-4">
      <label class="block mb-2 font-semibold">Upload Payment Proof (Screenshot):</label>
      <input type="file" @change="onFileChange" accept="image/*" class="mb-2" required />
      <button class="bg-green-600 text-white px-4 py-2 rounded" :disabled="!file">Submit Payment Proof</button>
    </form>
    <div v-if="preview" class="mb-2">
      <img :src="preview" alt="Preview" class="w-32 h-32 object-contain border rounded" />
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
  booking: Object,
  gcashQrUrl: String,
});

const file = ref(null);
const preview = ref(null);

function onFileChange(e) {
  file.value = e.target.files[0];
  if (file.value) {
    preview.value = URL.createObjectURL(file.value);
  }
}

function submit() {
  const formData = new FormData();
  formData.append('receipt_image', file.value);
  router.post(`/booking/${props.booking.id}/payment-proof`, formData);
}
</script>
