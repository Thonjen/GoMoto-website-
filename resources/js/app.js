import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import { isRole } from './utils/role';
import { createPinia } from 'pinia';
import piniaPersistedstate from 'pinia-plugin-persistedstate';
import { useAuthStore } from './stores/auth'; // 👈 make sure this path is correct
import axios from 'axios';
import PWAManager from './Components/PWAManager.vue';

// Configure axios for Laravel Sanctum
axios.defaults.withCredentials = true;
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.headers.common['Accept'] = 'application/json';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

const pinia = createPinia();
pinia.use(piniaPersistedstate);

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
setup({ el, App, props, plugin }) {
    const vueApp = createApp({ 
        render: () => h(App, props),
        components: {
            PWAManager
        }
    });

    vueApp.use(plugin).use(pinia).use(ZiggyVue);

    // Register PWAManager as a global component
    vueApp.component('PWAManager', PWAManager);

    const auth = useAuthStore();
    auth.user = props.initialPage.props.auth?.user || null;

    // Initialize auth state to ensure fresh CSRF tokens
    auth.initializeAuth().catch(console.error);
if (import.meta.env.DEV) {
    import('https://cats-correlation-mph-races.trycloudflare.com/@vite/client');
}

vueApp.config.globalProperties.$is = (...roles) => {
  return roles.some(role => auth.hasRole(role));
};
    return vueApp.mount(el);
},
    progress: {
        color: '#4B5563',
    },
});
