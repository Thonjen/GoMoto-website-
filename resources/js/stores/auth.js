import { defineStore } from 'pinia';
import piniaPersistedstate from 'pinia-plugin-persistedstate';
import axios from 'axios';
import { router } from '@inertiajs/vue3';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,
        loading: false,
    }),
    actions: {
        async fetchUser() {
            this.loading = true;
            try {
                await axios.get('/sanctum/csrf-cookie'); // <-- Ensure CSRF cookie
                const res = await axios.get('/user');
                this.user = res.data.user; // <-- expects { user: ... }
            } catch {
                this.user = null;
            } finally {
                this.loading = false;
            }
        },
        async login(data) {
            await axios.get('/sanctum/csrf-cookie');
            try {
                const res = await axios.post('/login', data);
                this.user = res.data.user;
                router.visit('/dashboard');
            } catch (e) {
                // Show CSRF error in console for debugging
                if (e.response?.status === 419) {
                    console.error('CSRF token mismatch. Make sure SANCTUM_STATEFUL_DOMAINS is set and cookies are sent.');
                }
                throw e;
            }
        },
        async register(data) {
            await axios.get('/sanctum/csrf-cookie');
            try {
                const res = await axios.post('/register', data);
                this.user = res.data.user;
                router.visit('/dashboard');
            } catch (e) {
                if (e.response?.status === 419) {
                    console.error('CSRF token mismatch. Make sure SANCTUM_STATEFUL_DOMAINS is set and cookies are sent.');
                }
                throw e;
            }
        },
        async logout() {
            await axios.get('/sanctum/csrf-cookie');
            try {
                await axios.post('/logout');
                this.user = null;
                router.visit('/');
            } catch (e) {
                if (e.response?.status === 419) {
                    console.error('CSRF token mismatch. Make sure SANCTUM_STATEFUL_DOMAINS is set and cookies are sent.');
                }
                throw e;
            }
        },
    },
    persist: true,
});

// In main.js, after creating pinia:
// pinia.use(piniaPersistedstate);
// On app boot: useAuthStore().fetchUser();
