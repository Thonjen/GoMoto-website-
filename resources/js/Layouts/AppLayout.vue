<template>
  <div class="min-h-screen flex flex-col bg-gray-50 fullscreen-content standalone-content pwa-ios-fix">
    <header class="bg-white shadow-sm py-4 px-6 flex items-center justify-between sticky top-0 z-10">
      <Link href="/" class="flex items-center gap-2 text-lg font-semibold text-gray-800">
      <Car class="h-6 w-6 text-primary" />
      <span>GoMOTO</span>
      </Link>

      <!-- Desktop Navigation -->
      <nav class="hidden md:flex items-center gap-6">
        <Link v-for="item in publicNav" :key="item.name" :href="item.route"
          class="text-sm font-medium text-gray-600 hover:text-primary transition-colors">
        {{ item.name }}
        </Link>
        <Link v-if="!isLoggedIn" href="/login"
          class="text-sm font-medium text-gray-600 hover:text-primary transition-colors">
        Login
        </Link>
        <Link v-if="!isLoggedIn" href="/register"
          class="text-sm font-medium text-primary hover:text-primary/80 transition-colors">
        Register
        </Link>
        <template v-if="isLoggedIn">
          <Link href="/dashboard" class="text-sm font-medium text-gray-600 hover:text-primary transition-colors">
          Dashboard
          </Link>
          <Link href="/my-vehicles" class="text-sm font-medium text-gray-600 hover:text-primary transition-colors">
          My Vehicles
          </Link>
          <Link href="/my-bookings" class="text-sm font-medium text-gray-600 hover:text-primary transition-colors">
          My Bookings
          </Link>
          <button @click="logout" class="text-sm font-medium text-red-600 hover:text-red-700 transition-colors">
            Logout
          </button>
        </template>
      </nav>

    <!-- Mobile Menu Overlay -->
    <div 
      v-if="isMobileMenuOpen" 
      class="fixed top-0 left-0 w-full h-full bg-black bg-opacity-50 z-[9999] md:hidden"
      @click="toggleMobileMenu">
    </div>

    <!-- Mobile Slide-in Menu -->
    <nav 
      :class="[
        'fixed top-0 right-0 h-full w-64 bg-white shadow-xl z-[10000] transform transition-transform duration-300 ease-in-out md:hidden',
        isMobileMenuOpen ? 'translate-x-0' : 'translate-x-full'
      ]">
      <div class="flex justify-end p-4">
        <button @click="toggleMobileMenu" class="p-2 rounded-md hover:bg-gray-100">
          <X class="h-6 w-6 text-gray-700" />
          <span class="sr-only">Close navigation menu</span>
        </button>
      </div>
      <div class="flex flex-col gap-4 p-4">
        <Link 
          v-for="item in publicNav" 
          :key="item.name" 
          :href="item.route"
          class="text-base font-medium text-gray-700 hover:text-primary transition-colors"
          @click="toggleMobileMenu">
          {{ item.name }}
        </Link>

        <template v-if="!isLoggedIn">
          <Link href="/login" class="text-base font-medium text-gray-700 hover:text-primary transition-colors" @click="toggleMobileMenu">
            Login
          </Link>
          <Link href="/register" class="text-base font-medium text-primary hover:text-primary/80 transition-colors" @click="toggleMobileMenu">
            Register
          </Link>
        </template>

        <template v-if="isLoggedIn">
          <hr class="my-2 border-gray-200" />
          <Link href="/dashboard" class="text-base font-medium text-gray-700 hover:text-primary transition-colors" @click="toggleMobileMenu">
            Dashboard
          </Link>
          <Link href="/my-vehicles" class="text-base font-medium text-gray-700 hover:text-primary transition-colors" @click="toggleMobileMenu">
            My Vehicles
          </Link>
          <Link href="/my-bookings" class="text-base font-medium text-gray-700 hover:text-primary transition-colors" @click="toggleMobileMenu">
            My Bookings
          </Link>
          <button @click="logout" class="text-base font-medium text-red-600 hover:text-red-700 transition-colors">
            Logout
          </button>
        </template>
      </div>
    </nav>


    <main class="flex-grow container mx-auto px-4 py-8 md:px-6">
      <slot />
    </main>

    <Footer />
    
    <!-- PWA Manager Component -->
    <PWAManager />
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import { Car, Search, User, Settings, Package, DollarSign, FileText, Shield, Bell, ShoppingCart, Plus, CalendarCheck, Menu, X } from 'lucide-vue-next';
import Footer from '@/Components/Footer.vue'; // import Footer
import PWAManager from '@/Components/PWAManager.vue'; // import PWA Manager

// This would typically come from Inertia's shared props or a global state
const isLoggedIn = ref(false); // For demonstration, assume user is not logged in by default
// You would update this based on actual authentication status from your Laravel backend

const isMobileMenuOpen = ref(false);

const toggleMobileMenu = () => {
  isMobileMenuOpen.value = !isMobileMenuOpen.value;
};

const logout = () => {
  // In a real Inertia app, this would be an Inertia.post('/logout') request
  alert('Logging out...');
  isLoggedIn.value = false; // Simulate logout
  isMobileMenuOpen.value = false;
  // Redirect to login or home page
  // Inertia.visit('/login');
};

const publicNav = [
  { name: 'Home', route: '/' },
  { name: 'Search', route: '/search' },
];

// Example of how you might conditionally render navigation based on user roles
// In a real app, you'd have more robust role management
// const userRole = 'guest'; // 'guest', 'renter', 'owner', 'admin'
</script>

<style scoped>
/* Tailwind CSS is used for styling, no custom CSS needed here unless for specific overrides */
</style>
