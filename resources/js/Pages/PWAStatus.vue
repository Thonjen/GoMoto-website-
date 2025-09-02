<template>
    <AppLayout>
        <div class="max-w-4xl mx-auto p-6">
            <div class="bg-white rounded-lg shadow-lg p-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-6">PWA Status & Testing</h1>
                
                <!-- PWA Installation Status -->
                <div class="mb-8 p-6 bg-blue-50 rounded-lg">
                    <h2 class="text-xl font-semibold text-blue-900 mb-4">Installation Status</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex items-center space-x-3">
                            <div :class="[isPWASupported ? 'bg-green-500' : 'bg-red-500', 'w-3 h-3 rounded-full']"></div>
                            <span class="text-sm font-medium">PWA Support: {{ isPWASupported ? 'Yes' : 'No' }}</span>
                        </div>
                        
                        <div class="flex items-center space-x-3">
                            <div :class="[isInstalled ? 'bg-green-500' : 'bg-yellow-500', 'w-3 h-3 rounded-full']"></div>
                            <span class="text-sm font-medium">Installed: {{ isInstalled ? 'Yes' : 'No' }}</span>
                        </div>
                        
                        <div class="flex items-center space-x-3">
                            <div :class="[canInstall ? 'bg-green-500' : 'bg-gray-500', 'w-3 h-3 rounded-full']"></div>
                            <span class="text-sm font-medium">Can Install: {{ canInstall ? 'Yes' : 'No' }}</span>
                        </div>
                        
                        <div class="flex items-center space-x-3">
                            <div :class="[isOnline ? 'bg-green-500' : 'bg-red-500', 'w-3 h-3 rounded-full']"></div>
                            <span class="text-sm font-medium">Online: {{ isOnline ? 'Yes' : 'No' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Service Worker Status -->
                <div class="mb-8 p-6 bg-green-50 rounded-lg">
                    <h2 class="text-xl font-semibold text-green-900 mb-4">Service Worker</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex items-center space-x-3">
                            <div :class="[swSupported ? 'bg-green-500' : 'bg-red-500', 'w-3 h-3 rounded-full']"></div>
                            <span class="text-sm font-medium">Supported: {{ swSupported ? 'Yes' : 'No' }}</span>
                        </div>
                        
                        <div class="flex items-center space-x-3">
                            <div :class="[swRegistered ? 'bg-green-500' : 'bg-yellow-500', 'w-3 h-3 rounded-full']"></div>
                            <span class="text-sm font-medium">Registered: {{ swRegistered ? 'Yes' : 'No' }}</span>
                        </div>
                    </div>
                    
                    <div class="mt-4 text-sm text-green-700">
                        <p>State: {{ swState }}</p>
                    </div>
                </div>

                <!-- Actions -->
                <div class="mb-8 p-6 bg-gray-50 rounded-lg">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Actions</h2>
                    
                    <div class="flex flex-wrap gap-4">
                        <button
                            v-if="canInstall"
                            @click="installPWA"
                            :disabled="installing"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            {{ installing ? 'Installing...' : 'Install App' }}
                        </button>
                        
                        <button
                            @click="checkForUpdates"
                            :disabled="checkingUpdates"
                            class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            {{ checkingUpdates ? 'Checking...' : 'Check for Updates' }}
                        </button>
                        
                        <button
                            @click="requestNotificationPermission"
                            class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700"
                        >
                            Enable Notifications
                        </button>
                        
                        <button
                            @click="testNotification"
                            class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700"
                        >
                            Test Notification
                        </button>
                        
                        <button
                            @click="shareApp"
                            class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700"
                        >
                            Share App
                        </button>
                    </div>
                </div>

                <!-- Features -->
                <div class="mb-8 p-6 bg-purple-50 rounded-lg">
                    <h2 class="text-xl font-semibold text-purple-900 mb-4">PWA Features</h2>
                    
                    <ul class="space-y-2 text-sm text-purple-700">
                        <li class="flex items-center space-x-2">
                            <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            <span>Offline functionality with service worker</span>
                        </li>
                        <li class="flex items-center space-x-2">
                            <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            <span>App-like experience with web app manifest</span>
                        </li>
                        <li class="flex items-center space-x-2">
                            <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            <span>Push notifications support</span>
                        </li>
                        <li class="flex items-center space-x-2">
                            <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            <span>Home screen installation</span>
                        </li>
                        <li class="flex items-center space-x-2">
                            <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            <span>App shortcuts for quick actions</span>
                        </li>
                        <li class="flex items-center space-x-2">
                            <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            <span>Network status detection</span>
                        </li>
                    </ul>
                </div>

                <!-- Installation Instructions -->
                <div class="p-6 bg-yellow-50 rounded-lg">
                    <h2 class="text-xl font-semibold text-yellow-900 mb-4">Installation Instructions</h2>
                    
                    <div class="space-y-4 text-sm text-yellow-800">
                        <div>
                            <h3 class="font-semibold">Mobile (Chrome/Safari):</h3>
                            <ol class="list-decimal list-inside space-y-1 ml-4">
                                <li>Look for the "Add to Home Screen" or "Install" prompt</li>
                                <li>Tap "Add" or "Install" when prompted</li>
                                <li>Find the GoMoto icon on your home screen</li>
                            </ol>
                        </div>
                        
                        <div>
                            <h3 class="font-semibold">Desktop (Chrome/Edge):</h3>
                            <ol class="list-decimal list-inside space-y-1 ml-4">
                                <li>Look for the install icon in the address bar</li>
                                <li>Click "Install" when prompted</li>
                                <li>The app will open in its own window</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { 
    isPWAInstalled, 
    isPWAInstallable, 
    installPWA as installPWAUtility,
    setupPWAInstallPrompt,
    registerServiceWorker,
    checkForUpdates as checkForUpdatesUtility,
    requestNotificationPermission as requestNotificationPermissionUtility,
    showNotification,
    shareContent,
    getNetworkStatus,
    setupNetworkListeners
} from '@/utils/pwa.js';

const isPWASupported = ref(true);
const isInstalled = ref(false);
const canInstall = ref(false);
const isOnline = ref(navigator.onLine);
const swSupported = ref('serviceWorker' in navigator);
const swRegistered = ref(false);
const swState = ref('Unknown');
const installing = ref(false);
const checkingUpdates = ref(false);

let cleanupNetworkListeners = null;

const installPWA = async () => {
    try {
        installing.value = true;
        const success = await installPWAUtility();
        
        if (success) {
            isInstalled.value = true;
            canInstall.value = false;
            alert('App installed successfully!');
        }
    } catch (error) {
        console.error('Installation failed:', error);
        alert('Installation failed: ' + error.message);
    } finally {
        installing.value = false;
    }
};

const checkForUpdates = async () => {
    try {
        checkingUpdates.value = true;
        await checkForUpdatesUtility();
        alert('Checked for updates!');
    } catch (error) {
        console.error('Update check failed:', error);
        alert('Update check failed: ' + error.message);
    } finally {
        checkingUpdates.value = false;
    }
};

const requestNotificationPermission = async () => {
    try {
        const granted = await requestNotificationPermissionUtility();
        if (granted) {
            alert('Notifications enabled!');
        } else {
            alert('Notifications permission denied.');
        }
    } catch (error) {
        alert('Error requesting notification permission: ' + error.message);
    }
};

const testNotification = () => {
    showNotification('GoMoto Test', {
        body: 'This is a test notification from GoMoto PWA!',
        tag: 'test-notification'
    });
};

const shareApp = async () => {
    const shareData = {
        title: 'GoMoto - Vehicle Rental',
        text: 'Book It, Ride It, Love It! Check out GoMoto for vehicle rentals.',
        url: window.location.origin
    };
    
    const shared = await shareContent(shareData);
    if (!shared) {
        // Fallback: copy to clipboard
        navigator.clipboard.writeText(window.location.origin).then(() => {
            alert('App URL copied to clipboard!');
        });
    }
};

const handleInstallPromptChange = (installable) => {
    canInstall.value = installable;
};

const handleOnline = () => {
    isOnline.value = true;
};

const handleOffline = () => {
    isOnline.value = false;
};

onMounted(async () => {
    // Check installation status
    isInstalled.value = isPWAInstalled();
    canInstall.value = isPWAInstallable();
    
    // Setup install prompt listener
    setupPWAInstallPrompt(handleInstallPromptChange);
    
    // Setup network listeners
    cleanupNetworkListeners = setupNetworkListeners(handleOnline, handleOffline);
    
    // Register service worker and check status
    if (swSupported.value) {
        try {
            const registration = await registerServiceWorker();
            if (registration) {
                swRegistered.value = true;
                swState.value = registration.active ? 'Active' : 'Registered';
            }
        } catch (error) {
            swState.value = 'Registration Failed';
            console.error('Service worker registration failed:', error);
        }
    }
    
    // Check PWA support
    isPWASupported.value = 'serviceWorker' in navigator && 'PushManager' in window;
    
    // Initial network status
    const networkStatus = getNetworkStatus();
    isOnline.value = networkStatus.online;
});
</script>
