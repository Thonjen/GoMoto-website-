<template>
  <AppLayout>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
      <aside class="md:col-span-1 bg-white p-6 rounded-lg shadow-md h-fit">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Account Settings</h2>
        <nav class="flex flex-col gap-2">
          <Link href="/profile" class="text-primary-600 font-medium hover:underline">My Profile</Link>
          <Link href="/profile/address" class="text-gray-700 hover:underline">My Address</Link>
          <Link href="/profile/license" class="text-gray-700 hover:underline">My License</Link>
          <Link href="/notifications" class="text-gray-700 hover:underline">Notifications</Link>
        </nav>
      </aside>

      <div class="md:col-span-3 bg-white p-6 rounded-lg shadow-md">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">My Profile</h1>

        <form @submit.prevent="updateProfile">
          <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Profile Image</label>
            <div class="flex items-center gap-4">
              <img :src="profileForm.avatarUrl" alt="Profile Avatar" class="w-24 h-24 rounded-full object-cover border border-gray-200" />
              <input type="file" @change="handleImageUpload" accept="image/*"
                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100" />
            </div>
          </div>

          <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
            <input type="text" id="name" v-model="profileForm.name" required
              class="w-full p-3 border border-gray-300 rounded-md focus:ring-primary-400 focus:border-primary-400" />
          </div>
          <div class="mb-4">
            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
            <input type="email" id="email" v-model="profileForm.email" required
              class="w-full p-3 border border-gray-300 rounded-md focus:ring-primary-400 focus:border-primary-400" />
          </div>
          <div class="mb-4">
            <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
            <input type="tel" id="phone" v-model="profileForm.phone"
              class="w-full p-3 border border-gray-300 rounded-md focus:ring-primary-400 focus:border-primary-400" />
          </div>
          <div class="mb-6">
            <label for="bio" class="block text-sm font-medium text-gray-700 mb-2">Bio (Optional)</label>
            <textarea id="bio" v-model="profileForm.bio" rows="4"
              class="w-full p-3 border border-gray-300 rounded-md focus:ring-primary-400 focus:border-primary-400"></textarea>
          </div>

          <button type="submit"
            class="bg-primary-600 text-white px-6 py-3 rounded-md font-semibold hover:bg-primary-700 transition-colors flex items-center justify-center gap-2">
            <Save class="h-5 w-5" />
            Save Profile
          </button>
        </form>

        <hr class="my-8 border-gray-200" />

        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Change Password</h2>
        <form @submit.prevent="changePassword">
          <div class="mb-4">
            <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
            <input type="password" id="current_password" v-model="passwordForm.current_password" required
              class="w-full p-3 border border-gray-300 rounded-md focus:ring-primary-400 focus:border-primary-400" />
          </div>
          <div class="mb-4">
            <label for="new_password" class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
            <input type="password" id="new_password" v-model="passwordForm.new_password" required
              class="w-full p-3 border border-gray-300 rounded-md focus:ring-primary-400 focus:border-primary-400" />
          </div>
          <div class="mb-6">
            <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
            <input type="password" id="new_password_confirmation" v-model="passwordForm.new_password_confirmation" required
              class="w-full p-3 border border-gray-300 rounded-md focus:ring-primary-400 focus:border-primary-400" />
          </div>
          <button type="submit"
            class="bg-gray-800 text-white px-6 py-3 rounded-md font-semibold hover:bg-gray-700 transition-colors flex items-center justify-center gap-2">
            <KeyRound class="h-5 w-5" />
            Change Password
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
import { Save, KeyRound } from 'lucide-vue-next';

const profileForm = ref({
  name: 'Jane Doe',
  email: 'jane.doe@example.com',
  phone: '09171234567',
  bio: 'Avid traveler and car enthusiast. Looking forward to renting out my spare vehicle!',
  avatarUrl: '/placeholder.svg?height=96&width=96', // Placeholder image
});

const passwordForm = ref({
  current_password: '',
  new_password: '',
  new_password_confirmation: '',
});

const handleImageUpload = (event) => {
  const file = event.target.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = (e) => {
      profileForm.value.avatarUrl = e.target.result;
    };
    reader.readAsDataURL(file);
    // In a real app, you'd upload this file to your server
    alert('Image selected for upload. (Not actually uploaded in this demo)');
  }
};

const updateProfile = () => {
  alert('Profile updated! (Not actually saved in this demo)');
  console.log('Profile data:', profileForm.value);
  // In a real Inertia app, this would be an Inertia.post('/profile', profileForm.value)
};

const changePassword = () => {
  if (passwordForm.value.new_password !== passwordForm.value.new_password_confirmation) {
    alert('New passwords do not match!');
    return;
  }
  alert('Password change requested! (Not actually changed in this demo)');
  console.log('Password change data:', passwordForm.value);
  // In a real Inertia app, this would be an Inertia.post('/profile/password', passwordForm.value)
};
</script>

<style scoped>
/* Tailwind CSS is used for styling */
</style>
