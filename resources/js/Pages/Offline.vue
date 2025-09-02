<template>
    <div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8 fullscreen-content standalone-content pwa-ios-fix">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <img class="mx-auto h-12 w-auto" src="/icons/icon-192x192.svg" alt="GoMoto">
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                You're Offline
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Check your internet connection and try again
            </p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
                <div class="text-center">
                    <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M12 2v20M2 12h20"></path>
                    </svg>
                    
                    <h3 class="mt-4 text-lg font-medium text-gray-900">No Internet Connection</h3>
                    <p class="mt-2 text-sm text-gray-500">
                        It looks like you're not connected to the internet. Some features may not work as expected.
                    </p>

                    <div class="mt-6">
                        <button
                            @click="retry"
                            :disabled="retrying"
                            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <span v-if="!retrying">Try Again</span>
                            <span v-else class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Checking...
                            </span>
                        </button>
                    </div>

                    <div class="mt-6 border-t border-gray-200 pt-6">
                        <h4 class="text-sm font-medium text-gray-900 mb-3">While you're offline, you can:</h4>
                        <ul class="text-sm text-gray-600 space-y-2">
                            <li class="flex items-center">
                                <svg class="h-4 w-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                View cached pages
                            </li>
                            <li class="flex items-center">
                                <svg class="h-4 w-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                Browse saved content
                            </li>
                            <li class="flex items-center">
                                <svg class="h-4 w-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                Prepare bookings (syncs when online)
                            </li>
                        </ul>
                    </div>

                    <div class="mt-6">
                        <button
                            @click="goHome"
                            class="w-full flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        >
                            Go to Home
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

export default {
    name: 'Offline',
    setup() {
        const retrying = ref(false);

        const retry = async () => {
            retrying.value = true;
            
            try {
                // Check if we're back online
                const response = await fetch('/api/health-check', {
                    method: 'HEAD',
                    cache: 'no-cache'
                });
                
                if (response.ok) {
                    // We're back online, redirect to home
                    router.visit('/');
                } else {
                    throw new Error('Still offline');
                }
            } catch (error) {
                // Still offline
                setTimeout(() => {
                    retrying.value = false;
                }, 1000);
            }
        };

        const goHome = () => {
            router.visit('/');
        };

        return {
            retrying,
            retry,
            goHome
        };
    }
};
</script>
