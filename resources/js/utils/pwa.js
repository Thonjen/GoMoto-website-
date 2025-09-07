// PWA utility functions
let deferredPrompt = null;

// Check if PWA is installable
export const isPWAInstallable = () => {
    return deferredPrompt !== null;
};

// Check if running as PWA
export const isPWAInstalled = () => {
    return window.matchMedia('(display-mode: standalone)').matches ||
           window.matchMedia('(display-mode: fullscreen)').matches ||
           window.matchMedia('(display-mode: minimal-ui)').matches ||
           window.navigator.standalone === true ||
           document.referrer.includes('android-app://');
};

// Check if running in fullscreen mode
export const isFullscreenMode = () => {
    return window.matchMedia('(display-mode: fullscreen)').matches;
};

// Install PWA
export const installPWA = async () => {
    if (!deferredPrompt) {
        throw new Error('PWA is not installable');
    }

    const result = await deferredPrompt.prompt();
    deferredPrompt = null;
    
    return result.outcome === 'accepted';
};

// Setup PWA install prompt
export const setupPWAInstallPrompt = (callback = null) => {
    window.addEventListener('beforeinstallprompt', (event) => {
        // Prevent the mini-infobar from appearing on mobile
        event.preventDefault();
        
        // Stash the event so it can be triggered later
        deferredPrompt = event;
        
        // Update UI to notify the user they can install the PWA
        if (callback) {
            callback(true);
        }
    });

    window.addEventListener('appinstalled', () => {
        deferredPrompt = null;
        if (callback) {
            callback(false);
        }
        console.log('PWA was installed');
    });
};

// Register service worker
export const registerServiceWorker = async () => {
    if ('serviceWorker' in navigator) {
        try {
            const registration = await navigator.serviceWorker.register('/sw.js', {
                scope: '/'
            });

            console.log('Service Worker registered successfully:', registration);

            // Handle service worker updates
            registration.addEventListener('updatefound', () => {
                const newWorker = registration.installing;
                
                newWorker.addEventListener('statechange', () => {
                    if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                        // New service worker is available
                        console.log('New service worker available');
                        
                        // Show update notification to user
                        if (confirm('A new version is available. Reload to update?')) {
                            newWorker.postMessage({ type: 'SKIP_WAITING' });
                            window.location.reload();
                        }
                    }
                });
            });

            return registration;
        } catch (error) {
            console.error('Service Worker registration failed:', error);
            throw error;
        }
    } else {
        console.warn('Service Workers are not supported');
        return null;
    }
};

// Check for app updates
export const checkForUpdates = async () => {
    if ('serviceWorker' in navigator) {
        const registration = await navigator.serviceWorker.ready;
        return registration.update();
    }
};

// Request notification permission
export const requestNotificationPermission = async () => {
    if ('Notification' in window) {
        const permission = await Notification.requestPermission();
        return permission === 'granted';
    }
    return false;
};

// Show notification
export const showNotification = (title, options = {}) => {
    if ('serviceWorker' in navigator && 'Notification' in window) {
        navigator.serviceWorker.ready.then(registration => {
            registration.showNotification(title, {
                icon: '/icon2/favicon-192x192.png',
                badge: '/icon2/favicon-96x96.png',
                vibrate: [200, 100, 200],
                ...options
            });
        });
    }
};

// Share API
export const shareContent = async (shareData) => {
    if (navigator.share) {
        try {
            await navigator.share(shareData);
            return true;
        } catch (error) {
            console.error('Error sharing:', error);
            return false;
        }
    }
    return false;
};

// Network status
export const getNetworkStatus = () => {
    return {
        online: navigator.onLine,
        connection: navigator.connection || navigator.mozConnection || navigator.webkitConnection
    };
};

// Setup network listeners
export const setupNetworkListeners = (onOnline, onOffline) => {
    window.addEventListener('online', onOnline);
    window.addEventListener('offline', onOffline);
    
    return () => {
        window.removeEventListener('online', onOnline);
        window.removeEventListener('offline', onOffline);
    };
};
