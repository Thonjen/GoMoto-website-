<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
  <footer class="bg-white py-6 px-4 md:px-6 text-center text-sm text-black border-t border-gray-200">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      <div class="container mx-auto flex flex-col md:flex-row justify-between items-center gap-4">
        <p>&copy; {{ new Date().getFullYear() }} GoMOTO. All rights reserved.</p>
        <nav class="flex gap-4">
          <Link href="/terms" class="hover:underline">Terms of Service</Link>
          <Link href="/privacy" class="hover:underline">Privacy Policy</Link>
        </nav>
      </div>
    </div>
  </footer>
</template>
