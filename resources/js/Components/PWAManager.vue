<template>
  <div v-if="showInstallPrompt" class="fixed bottom-4 left-4 right-4 z-50 md:left-auto md:right-4 md:w-80">
    <div class="bg-white rounded-lg shadow-lg border p-4 animate-slide-up">
      <div class="flex items-start space-x-3">
        <div class="flex-shrink-0">
          <img src="/icon2/favicon-96x96.png" alt="GoMoto" class="w-12 h-12 rounded-lg" />
        </div>
        <div class="flex-1 min-w-0">
          <h3 class="text-sm font-semibold text-gray-900">Install GoMoto</h3>
          <p class="text-xs text-gray-600 mt-1">
            Add GoMoto to your home screen for quick access and offline features.
          </p>
        </div>
      </div>
      
      <div class="mt-4 flex space-x-2">
        <button
          @click="installApp"
          :disabled="installing"
          class="flex-1 bg-indigo-600 text-white text-sm font-medium px-3 py-2 rounded-md hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
        >
          <span v-if="!installing">Install</span>
          <span v-else class="flex items-center justify-center">
            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Installing...
          </span>
        </button>
        <button
          @click="dismissPrompt"
          class="px-3 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 transition-colors"
        >
          Later
        </button>
      </div>
    </div>
  </div>

  <!-- Network Status Indicator -->
  <div v-if="!isOnline" class="fixed top-0 left-0 right-0 z-50 bg-red-600 text-white text-center py-2 text-sm">
    <div class="flex items-center justify-center space-x-2">
      <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
      </svg>
      <span>You're offline. Some features may be limited.</span>
    </div>
  </div>

  <!-- Update Available Notification -->
  <div v-if="updateAvailable" class="fixed top-16 left-4 right-4 z-50 md:left-auto md:right-4 md:w-80">
    <div class="bg-blue-600 text-white rounded-lg shadow-lg p-4">
      <div class="flex items-center justify-between">
        <div>
          <h3 class="text-sm font-semibold">Update Available</h3>
          <p class="text-xs mt-1 opacity-90">A new version of GoMoto is ready.</p>
        </div>
        <button
          @click="updateApp"
          class="ml-4 bg-blue-700 hover:bg-blue-800 px-3 py-1 rounded text-xs font-medium transition-colors"
        >
          Update
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, onUnmounted } from 'vue';
import { 
  setupPWAInstallPrompt, 
  installPWA, 
  isPWAInstalled,
  setupNetworkListeners,
  getNetworkStatus,
  registerServiceWorker
} from '../utils/pwa.js';

export default {
  name: 'PWAManager',
  setup() {
    const showInstallPrompt = ref(false);
    const installing = ref(false);
    const isOnline = ref(navigator.onLine);
    const updateAvailable = ref(false);
    
    let cleanupNetworkListeners = null;

    const handleInstallPromptChange = (canInstall) => {
      showInstallPrompt.value = canInstall && !isPWAInstalled();
    };

    const installApp = async () => {
      try {
        installing.value = true;
        const success = await installPWA();
        
        if (success) {
          showInstallPrompt.value = false;
          // Show success message
          console.log('App installed successfully');
        }
      } catch (error) {
        console.error('Installation failed:', error);
        alert('Installation failed. Please try again.');
      } finally {
        installing.value = false;
      }
    };

    const dismissPrompt = () => {
      showInstallPrompt.value = false;
      // Remember dismissal for this session
      sessionStorage.setItem('pwa-install-dismissed', 'true');
    };

    const updateApp = () => {
      window.location.reload();
    };

    const handleOnline = () => {
      isOnline.value = true;
    };

    const handleOffline = () => {
      isOnline.value = false;
    };

    onMounted(async () => {
      // Check if user already dismissed the prompt this session
      const dismissed = sessionStorage.getItem('pwa-install-dismissed');
      
      if (!dismissed && !isPWAInstalled()) {
        setupPWAInstallPrompt(handleInstallPromptChange);
      }

      // Setup network listeners
      cleanupNetworkListeners = setupNetworkListeners(handleOnline, handleOffline);

      // Register service worker
      try {
        const registration = await registerServiceWorker();
        
        if (registration) {
          // Listen for service worker updates
          registration.addEventListener('updatefound', () => {
            const newWorker = registration.installing;
            
            newWorker.addEventListener('statechange', () => {
              if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                updateAvailable.value = true;
              }
            });
          });
        }
      } catch (error) {
        console.error('Failed to register service worker:', error);
      }

      // Initial network status
      const networkStatus = getNetworkStatus();
      isOnline.value = networkStatus.online;
    });

    onUnmounted(() => {
      if (cleanupNetworkListeners) {
        cleanupNetworkListeners();
      }
    });

    return {
      showInstallPrompt,
      installing,
      isOnline,
      updateAvailable,
      installApp,
      dismissPrompt,
      updateApp
    };
  }
};
</script>

<style scoped>
@keyframes slide-up {
  from {
    transform: translateY(100%);
    opacity: 0;
  }
  to {
    transform: translateY(0);
    opacity: 1;
  }
}

.animate-slide-up {
  animation: slide-up 0.3s ease-out;
}
</style>
