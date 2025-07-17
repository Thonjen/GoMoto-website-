<template>
  <AppLayout>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
      <aside class="md:col-span-1 bg-white p-6 rounded-lg shadow-md h-fit">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Account Settings</h2>
        <nav class="flex flex-col gap-2">
          <Link href="/profile" class="text-gray-700 hover:underline">My Profile</Link>
          <Link href="/profile/address" class="text-gray-700 hover:underline">My Address</Link>
          <Link href="/profile/license" class="text-primary-600 font-medium hover:underline">My License</Link>
          <Link href="/notifications" class="text-gray-700 hover:underline">Notifications</Link>
        </nav>
      </aside>

      <div class="md:col-span-3 bg-white p-6 rounded-lg shadow-md">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">My Driver's License</h1>

        <div v-if="licenseStatus.isUploaded" class="mb-8">
          <h2 class="text-2xl font-semibold text-gray-800 mb-4">Current License Status</h2>
          <div :class="['p-4 rounded-md flex items-center gap-4',
            licenseStatus.isVerified ? 'bg-green-50 text-green-800' : 'bg-yellow-50 text-yellow-800']">
            <template v-if="licenseStatus.isVerified">
              <CheckCircle class="h-8 w-8" />
              <div>
                <p class="font-semibold text-lg">Verified!</p>
                <p class="text-sm">Your driver's license has been successfully verified.</p>
              </div>
            </template>
            <template v-else>
              <Clock class="h-8 w-8" />
              <div>
                <p class="font-semibold text-lg">Pending Verification</p>
                <p class="text-sm">Your license is under review. Please allow 1-2 business days for verification.</p>
              </div>
            </template>
          </div>

          <div class="mt-6">
            <h3 class="text-xl font-semibold text-gray-800 mb-3">Uploaded License Images</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div class="border border-gray-200 rounded-md overflow-hidden">
                <img :src="licenseStatus.frontImageUrl" alt="License Front" class="w-full h-48 object-contain bg-gray-100" />
                <p class="text-center text-sm text-gray-600 py-2">Front Side</p>
              </div>
              <div class="border border-gray-200 rounded-md overflow-hidden">
                <img :src="licenseStatus.backImageUrl" alt="License Back" class="w-full h-48 object-contain bg-gray-100" />
                <p class="text-center text-sm text-gray-600 py-2">Back Side</p>
              </div>
            </div>
            <button @click="removeLicense"
              class="mt-6 bg-red-500 text-white px-6 py-3 rounded-md font-semibold hover:bg-red-600 transition-colors flex items-center justify-center gap-2">
              <Trash2 class="h-5 w-5" />
              Remove License
            </button>
          </div>
        </div>

        <div v-else>
          <h2 class="text-2xl font-semibold text-gray-800 mb-4">Upload Your Driver's License</h2>
          <p class="text-gray-700 mb-6">Please upload clear photos of both the front and back of your valid driver's license for verification. This is required to rent or list vehicles.</p>

          <form @submit.prevent="uploadLicense">
            <div class="mb-4">
              <label for="licenseFront" class="block text-sm font-medium text-gray-700 mb-2">License Front Side</label>
              <input type="file" id="licenseFront" @change="handleFileChange($event, 'front')" accept="image/*" required
                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100" />
              <p v-if="form.frontFile" class="text-xs text-gray-500 mt-1">Selected: {{ form.frontFile.name }}</p>
            </div>
            <div class="mb-6">
              <label for="licenseBack" class="block text-sm font-medium text-gray-700 mb-2">License Back Side</label>
              <input type="file" id="licenseBack" @change="handleFileChange($event, 'back')" accept="image/*" required
                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100" />
              <p v-if="form.backFile" class="text-xs text-gray-500 mt-1">Selected: {{ form.backFile.name }}</p>
            </div>
            <button type="submit"
              class="bg-primary-600 text-white px-6 py-3 rounded-md font-semibold hover:bg-primary-700 transition-colors flex items-center justify-center gap-2">
              <UploadCloud class="h-5 w-5" />
              Upload License
            </button>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { UploadCloud, CheckCircle, Clock, Trash2 } from 'lucide-vue-next';

const licenseStatus = ref({
  isUploaded: false, // Simulate if license is already uploaded
  isVerified: false, // Simulate if license is verified
  frontImageUrl: '/placeholder.svg?height=200&width=300', // Placeholder
  backImageUrl: '/placeholder.svg?height=200&width=300', // Placeholder
});

const form = ref({
  frontFile: null,
  backFile: null,
});

const handleFileChange = (event, side) => {
  if (side === 'front') {
    form.value.frontFile = event.target.files[0];
  } else {
    form.value.backFile = event.target.files[0];
  }
};

const uploadLicense = () => {
  if (!form.value.frontFile || !form.value.backFile) {
    alert('Please select both front and back images of your license.');
    return;
  }
  alert('License images uploaded for verification! (Not actually uploaded in this demo)');
  // Simulate successful upload and pending status
  licenseStatus.value.isUploaded = true;
  licenseStatus.value.isVerified = false;
  licenseStatus.value.frontImageUrl = URL.createObjectURL(form.value.frontFile);
  licenseStatus.value.backImageUrl = URL.createObjectURL(form.value.backFile);
  // In a real Inertia app, this would be an Inertia.post('/profile/license', form.value)
};

const removeLicense = () => {
  if (confirm('Are you sure you want to remove your license? You will need to re-upload for verification.')) {
    licenseStatus.value.isUploaded = false;
    licenseStatus.value.isVerified = false;
    licenseStatus.value.frontImageUrl = '';
    licenseStatus.value.backImageUrl = '';
    form.value.frontFile = null;
    form.value.backFile = null;
    alert('License removed. (Not actually removed in this demo)');
    // In a real Inertia app, this would be an Inertia.delete('/profile/license')
  }
};
</script>

<style scoped>
/* Tailwind CSS is used for styling */
</style>
