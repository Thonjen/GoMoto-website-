<script setup>
import { Link } from '@inertiajs/vue3';
import ApplicationLogo from "@/Components/ApplicationLogo.vue";

</script>

<template>
  <footer class="relative bg-gradient-to-t from-black/50 to-transparent backdrop-blur-lg border-t border-white/10">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
        <!-- Brand Section -->
        <div class="col-span-1 md:col-span-2">
          <div class="flex items-center space-x-3 mb-4">
                        <div class="flex shrink-0 items-center space-x-3">
                                <div class="relative">
                                    <ApplicationLogo
    class="block h-10 w-auto scale-125 text-white transition-transform duration-200 group-hover:scale-150"
/>

                                    <div class="absolute inset-0 rounded-full bg-white/10 backdrop-blur-sm opacity-0 group-hover:opacity-100 transition-opacity duration-200 -z-10"></div>
                                </div>
                        </div>
          </div>
          <p class="text-white/80 max-w-md mb-6">
            Your trusted partner for premium vehicle rentals in Surigao del Norte. 
            Experience the future of transportation with our modern, secure platform.
          </p>
          <div class="flex space-x-4">
<a href="https://www.facebook.com/share/r/178tco8CZu/" target="_blank" rel="noopener noreferrer"
   class="w-10 h-10 bg-white/10 hover:bg-white/20 rounded-xl flex items-center justify-center transition-all duration-200 backdrop-blur-sm group">
  <svg class="w-5 h-5 text-white/70 group-hover:text-white" fill="currentColor" viewBox="0 0 24 24">
    <path d="M22.675 0H1.325C.593 0 0 .593 0 1.325v21.351C0 23.407.593 24 1.325 24H12.82v-9.294H9.692v-3.622h3.128V8.413c0-3.1 1.893-4.788 4.659-4.788 1.325 0 2.464.099 2.797.143v3.24l-1.918.001c-1.504 0-1.795.715-1.795 1.763v2.313h3.587l-.467 3.622h-3.12V24h6.116C23.407 24 24 23.407 24 22.675V1.325C24 .593 23.407 0 22.675 0z"/>
  </svg>
</a>

<a href="https://www.youtube.com/watch?v=xvFZjo5PgG0" target="_blank" rel="noopener noreferrer"
   class="w-10 h-10 bg-white/10 hover:bg-white/20 rounded-xl flex items-center justify-center transition-all duration-200 backdrop-blur-sm group">
  <svg class="w-5 h-5 text-white/70 group-hover:text-white" fill="currentColor" viewBox="0 0 24 24">
    <path d="M23.498 6.186a2.997 2.997 0 0 0-2.113-2.12C19.258 3.5 12 3.5 12 3.5s-7.258 0-9.385.566a2.997 2.997 0 0 0-2.113 2.12A31.81 31.81 0 0 0 0 12a31.81 31.81 0 0 0 .502 5.814 2.997 2.997 0 0 0 2.113 2.12C4.742 20.5 12 20.5 12 20.5s7.258 0 9.385-.566a2.997 2.997 0 0 0 2.113-2.12A31.81 31.81 0 0 0 24 12a31.81 31.81 0 0 0-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
  </svg>
</a>

            <a href="https://tenor.com/search/rickroll-gifs"  target="_blank" rel="noopener noreferrer" class="w-10 h-10 bg-white/10 hover:bg-white/20 rounded-xl flex items-center justify-center transition-all duration-200 backdrop-blur-sm group">
              <svg class="w-5 h-5 text-white/70 group-hover:text-white" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.618 11.174-.105-.949-.199-2.403.041-3.439.219-.937 1.406-5.957 1.406-5.957s-.359-.219-.359-1.219c0-1.142.662-1.994 1.488-1.994.219 0 .359.159.359.438 0 .219-.139.518-.219.797-.179.797.399 1.442 1.187 1.442 1.406 0 2.593-1.488 2.593-3.681 0-1.925-1.387-3.271-3.371-3.271-2.304 0-3.651 1.725-3.651 3.51 0 .697.264 1.445.593 1.846.065.08.074.149.055.23-.061.252-.196.796-.223.906-.035.146-.116.177-.268.107-1.001-.465-1.624-1.926-1.624-3.1 0-2.523 1.834-4.84 5.287-4.84 2.781 0 4.916 1.983 4.916 4.626 0 2.761-1.739 4.979-4.151 4.979-.811 0-1.573-.421-1.834-.92l-.498 1.902c-.181.695-.669 1.566-.995 2.097A12.013 12.013 0 0 0 12.017 24c6.624 0 11.99-5.367 11.99-11.987C24.007 5.367 18.641.001.017 0z"/>
              </svg>
            </a>
          </div>
        </div>

        <!-- Quick Links -->
        <div>
          <h3 class="text-lg font-semibold text-white mb-4">Quick Links</h3>
          <nav class="flex flex-col space-y-3">
            <Link href="/vehicles" class="text-white/70 hover:text-white transition-colors duration-200">Browse Vehicles</Link>
            <Link href="/register" class="text-white/70 hover:text-white transition-colors duration-200">Become a Partner</Link>
            <Link href="/login" class="text-white/70 hover:text-white transition-colors duration-200">Sign In</Link>
            <Link href="/about" class="text-white/70 hover:text-white transition-colors duration-200">About Us</Link>
          </nav>
        </div>

        <!-- Legal -->
        <div>
          <h3 class="text-lg font-semibold text-white mb-4">Legal</h3>
          <nav class="flex flex-col space-y-3">
            <Link href="/terms" class="text-white/70 hover:text-white transition-colors duration-200">Terms of Service</Link>
            <Link href="/privacy" class="text-white/70 hover:text-white transition-colors duration-200">Privacy Policy</Link>
            <Link href="/contact" class="text-white/70 hover:text-white transition-colors duration-200">Contact</Link>
            <Link href="/support" class="text-white/70 hover:text-white transition-colors duration-200">Support</Link>
          </nav>
        </div>
      </div>

      <!-- Bottom Bar -->
      <div class="pt-8 border-t border-white/10">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
          <p class="text-white/60 text-sm">
            &copy; {{ new Date().getFullYear() }} GoMOTO. All rights reserved.
          </p>
          <div class="flex items-center space-x-6 text-sm text-white/60">
            <span>Made with ❤️ in Surigao del Norte</span>
            <div class="flex items-center space-x-2">
              <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
              <span>All systems operational</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </footer>
</template>

<style scoped>
/* Custom gradient animation */
@keyframes shimmer {
  0% { background-position: -200% center; }
  100% { background-position: 200% center; }
}

.animate-shimmer {
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
  background-size: 200% 100%;
  animation: shimmer 3s ease-in-out infinite;
}
</style>
