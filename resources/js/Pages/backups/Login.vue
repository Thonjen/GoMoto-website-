<template>
  <AppLayout>
    <div class="flex items-center justify-center min-h-[calc(100vh-150px)] px-4">
      <div class="w-full max-w-md bg-white p-8 rounded-lg shadow-lg">
        <h1 class="text-3xl font-bold text-center text-gray-800 mb-6">Login to GoMOTO</h1>
        <form @submit.prevent="submitLogin">
          <div class="mb-4">
            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
            <input type="email" id="email" v-model="form.email" required
              class="w-full p-3 border border-gray-300 rounded-md focus:ring-primary-400 focus:border-primary-400"
              placeholder="you@example.com" />
          </div>
          <div class="mb-6">
            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
            <input type="password" id="password" v-model="form.password" required
              class="w-full p-3 border border-gray-300 rounded-md focus:ring-primary-400 focus:border-primary-400"
              placeholder="••••••••" />
          </div>
          <button type="submit"
            class="w-full bg-primary-600 text-white px-6 py-3 rounded-md font-semibold hover:bg-primary-700 transition-colors flex items-center justify-center gap-2">
            <LogIn class="h-5 w-5" />
            Login
          </button>
        </form>
        <p class="text-center text-sm text-gray-600 mt-6">
          Don't have an account?
          <Link href="/register" class="text-primary-600 hover:underline">Register here</Link>
        </p>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { LogIn } from 'lucide-vue-next';

const form = ref({
  email: '',
  password: '',
});

const submitLogin = () => {
  alert(`Attempting to log in with Email: ${form.value.email}`);
  // In a real Inertia app, this would be an Inertia.post('/login', form.value)
  // On successful login, Inertia would redirect to dashboard or intended page
};
</script>

<style scoped>
/* Tailwind CSS is used for styling */
</style>
