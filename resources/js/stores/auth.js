import { defineStore } from "pinia";
import piniaPersistedstate from "pinia-plugin-persistedstate";
import axios from "axios";
import { router } from "@inertiajs/vue3";

export const useAuthStore = defineStore("auth", {
    state: () => ({
        user: null,
        loading: false,
    }),
    actions: {
        hasRole(...roles) {
            if (!this.user || !this.user.role) return false;
            // Support both string and object role
            if (typeof this.user.role === "string") {
                return roles.includes(this.user.role);
            }
            if (typeof this.user.role === "object" && this.user.role.name) {
                return roles.includes(this.user.role.name);
            }
            return false;
        },
        async fetchUser() {
            this.loading = true;
            try {
                await axios.get("/sanctum/csrf-cookie");
                const res = await axios.get("/api/user");
                this.user = res.data.user;
            } catch (e) {
                this.user = null;
                this.$reset(); // <-- clear persisted state if it fails
            } finally {
                this.loading = false;
            }
        },

        async login(data) {
            await axios.get("/sanctum/csrf-cookie");
            try {
                const res = await axios.post("/login", data);
                this.user = res.data.user;
                await this.fetchUser(); // <-- ensure user/role is loaded from backend
                router.visit("/dashboard");
            } catch (e) {
                // Show CSRF error in console for debugging
                if (e.response?.status === 419) {
                    console.error(
                        "CSRF token mismatch. Make sure SANCTUM_STATEFUL_DOMAINS is set and cookies are sent."
                    );
                }
                throw e;
            }
        },
        async register(data) {
            await axios.get("/sanctum/csrf-cookie");
            try {
                const res = await axios.post("/register", data);
                this.user = res.data.user;
                await this.fetchUser(); // <-- ensure user/role is loaded from backend
                router.visit("/dashboard");
            } catch (e) {
                if (e.response?.status === 419) {
                    console.error(
                        "CSRF token mismatch. Make sure SANCTUM_STATEFUL_DOMAINS is set and cookies are sent."
                    );
                }
                throw e;
            }
        },
        async logout() {
            await axios.get("/sanctum/csrf-cookie");
            try {
                await axios.post("/logout");
                this.user = null;
                router.visit("/");
            } catch (e) {
                if (e.response?.status === 419) {
                    console.error(
                        "CSRF token mismatch. Make sure SANCTUM_STATEFUL_DOMAINS is set and cookies are sent."
                    );
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
