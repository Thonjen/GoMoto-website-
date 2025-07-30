<template>
  <OwnerLayout>
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
        <FilepondUploaderGcash
          ref="filePondUploader"
          :maxFiles="1"
          :acceptedFileTypes="['image/png', 'image/jpeg']"
          :uploadUrl="uploadUrl"
          :existingFileUrl="gcashQr.imageUrl"
          @upload-success="onUploadSuccess"
       />
      </div>
      <!-- </div>
      <div class="md:col-span-3 bg-white p-6 rounded-lg shadow-md">
        <div class="flex justify-between items-center mb-6">
          <h1 class="text-3xl font-bold text-gray-800">My Payments</h1>
          <Link href="/my-payments/qr"
            class="bg-primary-600 text-white px-4 py-2 rounded-md font-semibold hover:bg-primary-700 transition-colors flex items-center gap-2">
            <UploadCloud class="h-5 w-5" />
            Upload GCash QR
          </Link>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
          <div class="bg-green-50 p-6 rounded-lg shadow-md flex items-center gap-4">
            <DollarSign class="h-10 w-10 text-green-600" />
            <div>
              <p class="text-sm text-gray-600">Total Earnings</p>
              <p class="text-3xl font-bold text-gray-800">₱{{ totalEarnings.toLocaleString() }}</p>
            </div>
          </div>
          <div class="bg-blue-50 p-6 rounded-lg shadow-md flex items-center gap-4">
            <Wallet class="h-10 w-10 text-blue-600" />
            <div>
              <p class="text-sm text-gray-600">Pending Payout</p>
              <p class="text-3xl font-bold text-gray-800">₱{{ pendingPayout.toLocaleString() }}</p>
            </div>
          </div>
        </div>

        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Transaction History</h2>
        <div v-if="transactions.length > 0" class="overflow-x-auto">
          <table class="min-w-full bg-white border border-gray-200 rounded-lg">
            <thead>
              <tr class="bg-gray-100 text-left text-sm font-semibold text-gray-700">
                <th class="py-3 px-4 border-b">Date</th>
                <th class="py-3 px-4 border-b">Description</th>
                <th class="py-3 px-4 border-b">Amount</th>
                <th class="py-3 px-4 border-b">Status</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="transaction in transactions" :key="transaction.id" class="hover:bg-gray-50 border-b border-gray-100 last:border-b-0">
                <td class="py-3 px-4 text-gray-700">{{ transaction.date }}</td>
                <td class="py-3 px-4 text-gray-700">{{ transaction.description }}</td>
                <td class="py-3 px-4 font-medium" :class="transaction.type === 'credit' ? 'text-green-600' : 'text-red-600'">
                  {{ transaction.type === 'credit' ? '+' : '-' }}₱{{ transaction.amount.toLocaleString() }}
                </td>
                <td class="py-3 px-4">
                  <span :class="['px-3 py-1 rounded-full text-xs font-medium',
                    transaction.status === 'Completed' ? 'bg-green-100 text-green-800' :
                    transaction.status === 'Pending' ? 'bg-yellow-100 text-yellow-800' :
                    'bg-red-100 text-red-800']">
                    {{ transaction.status }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <p v-else class="text-gray-600 text-center py-8">No transactions recorded yet.</p>
      </div> -->

  </OwnerLayout>
</template>

<script setup>

import { computed, ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import OwnerLayout from '@/Layouts/OwnerLayout.vue';
import { DollarSign, Wallet, UploadCloud, Trash2} from 'lucide-vue-next';

import FilepondUploaderGcash from '@/Components/FilepondUploaderGcash.vue';

const props = defineProps({
  gcashQrUrl: String,
  gcashQrUploadDate: String,
});

const gcashQr = ref({
  imageUrl: props.gcashQrUrl,
  uploadDate: props.gcashQrUploadDate,
});

const uploadUrl = route('owner.gcash-qr.store');
const filePondUploader = ref(null);

function onUploadSuccess({ gcashQrUrl, gcashQrUploadDate }) {
  gcashQr.value.imageUrl = gcashQrUrl;
  gcashQr.value.uploadDate = gcashQrUploadDate;
  // Clear FilePond files after upload
  filePondUploader.value?.clearFiles?.();
}

function removeQrCode() {
  if (confirm('Are you sure you want to remove your GCash QR code?')) {
    router.delete(route('owner.gcash-qr.destroy'), {
      onSuccess: (page) => {
        gcashQr.value.imageUrl = '';
        gcashQr.value.uploadDate = '';
        // Clear FilePond files after removal
        filePondUploader.value?.clearFiles?.();
      },
      onError: () => {
        alert('Failed to remove QR code.');
      }
    });
  }
}


const transactions = ref([
  { id: 1, date: '2025-07-15', description: 'Booking for Toyota Camry (John Doe)', amount: 4500, type: 'credit', status: 'Completed' },
  { id: 2, date: '2025-07-10', description: 'Payout to GCash', amount: 10000, type: 'debit', status: 'Completed' },
  { id: 3, date: '2025-07-05', description: 'Booking for Honda CRV (Maria Santos)', amount: 6000, type: 'credit', status: 'Completed' },
  { id: 4, date: '2025-07-01', description: 'Booking for Ford Ranger (Pending Payout)', amount: 5000, type: 'credit', status: 'Pending' },
]);

const totalEarnings = computed(() => {
  return transactions.value.filter(t => t.type === 'credit' && t.status === 'Completed').reduce((sum, t) => sum + t.amount, 0);
});

const pendingPayout = computed(() => {
  return transactions.value.filter(t => t.type === 'credit' && t.status === 'Pending').reduce((sum, t) => sum + t.amount, 0);
});

// In a real Inertia app, this data would be passed as props from the controller
</script>

<style scoped>
/* Tailwind CSS is used for styling */
</style>
