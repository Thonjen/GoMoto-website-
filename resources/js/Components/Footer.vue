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
            <a href="#" class="w-10 h-10 bg-white/10 hover:bg-white/20 rounded-xl flex items-center justify-center transition-all duration-200 backdrop-blur-sm group">
              <svg class="w-5 h-5 text-white/70 group-hover:text-white" fill="currentColor" viewBox="0 0 24 24">
                <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/>
              </svg>
            </a>
            <a href="#" class="w-10 h-10 bg-white/10 hover:bg-white/20 rounded-xl flex items-center justify-center transition-all duration-200 backdrop-blur-sm group">
              <svg class="w-5 h-5 text-white/70 group-hover:text-white" fill="currentColor" viewBox="0 0 24 24">
                <path d="M22.46 6c-.77.35-1.6.58-2.46.69.88-.53 1.56-1.37 1.88-2.38-.83.5-1.75.85-2.72 1.05C18.37 4.5 17.26 4 16 4c-2.35 0-4.27 1.92-4.27 4.29 0 .34.04.67.11.98C8.28 9.09 5.11 7.38 3 4.79c-.37.63-.58 1.37-.58 2.15 0 1.49.75 2.81 1.91 3.56-.71 0-1.37-.2-1.95-.5v.03c0 2.08 1.48 3.82 3.44 4.21a4.22 4.22 0 0 1-1.93.07 4.28 4.28 0 0 0 4 2.98 8.521 8.521 0 0 1-5.33 1.84c-.34 0-.68-.02-1.02-.06C3.44 20.29 5.7 21 8.12 21 16 21 20.33 14.46 20.33 8.79c0-.19 0-.37-.01-.56.84-.6 1.56-1.36 2.14-2.23z"/>
              </svg>
            </a>
            <a href="#" class="w-10 h-10 bg-white/10 hover:bg-white/20 rounded-xl flex items-center justify-center transition-all duration-200 backdrop-blur-sm group">
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
