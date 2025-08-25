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
            try {
                // Get CSRF cookie first
                await axios.get("/sanctum/csrf-cookie");
                
                // Then attempt login
                const res = await axios.post("/login", data);
                this.user = res.data.user;
                
                // Fetch complete user data with role
                await this.fetchUser();
                
                // Redirect to dashboard
                router.visit("/dashboard");
            } catch (e) {
                // Handle email verification required
                if (e.response?.status === 403 && e.response?.data?.redirect) {
                    router.visit(e.response.data.redirect);
                    return;
                }
                
                // Show CSRF error in console for debugging
                if (e.response?.status === 419) {
                    console.error(
                        "CSRF token mismatch. Make sure SANCTUM_STATEFUL_DOMAINS is set and cookies are sent."
                    );
                    console.error("Error details:", e.response);
                    
                    // Try to get CSRF cookie again and retry once
                    try {
                        await axios.get("/sanctum/csrf-cookie");
                        const retryRes = await axios.post("/login", data);
                        this.user = retryRes.data.user;
                        await this.fetchUser();
                        router.visit("/dashboard");
                        return;
                    } catch (retryError) {
                        console.error("Retry failed:", retryError);
                    }
                }
                throw e;
            }
        },
        async register(data) {
            try {
                // Get CSRF cookie first
                await axios.get("/sanctum/csrf-cookie");
                
                // Then attempt registration
                const res = await axios.post("/register", data);
                
                // Check if response includes redirect for email verification
                if (res.data.redirect) {
                    // Extract email from redirect URL if present
                    const redirectUrl = res.data.redirect;
                    router.visit(redirectUrl);
                    return;
                }
                
                // Fallback to login flow if no redirect specified
                this.user = res.data.user;
                await this.fetchUser();
                router.visit("/dashboard");
            } catch (e) {
                if (e.response?.status === 419) {
                    console.error(
                        "CSRF token mismatch. Make sure SANCTUM_STATEFUL_DOMAINS is set and cookies are sent."
                    );
                    console.error("Error details:", e.response);
                    
                    // Try to get CSRF cookie again and retry once
                    try {
                        await axios.get("/sanctum/csrf-cookie");
                        const retryRes = await axios.post("/register", data);
                        
                        if (retryRes.data.redirect) {
                            const redirectUrl = retryRes.data.redirect;
                            router.visit(redirectUrl);
                            return;
                        }
                        
                        this.user = retryRes.data.user;
                        await this.fetchUser();
                        router.visit("/dashboard");
                        return;
                    } catch (retryError) {
                        console.error("Retry failed:", retryError);
                    }
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
