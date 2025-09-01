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
        // Helper method to ensure fresh CSRF token
        async ensureFreshCsrfToken() {
            try {
                await axios.get("/sanctum/csrf-cookie");
            } catch (error) {
                console.warn("Failed to refresh CSRF token:", error);
            }
        },
        
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
                await this.ensureFreshCsrfToken();
                const res = await axios.get("/api/user");
                this.user = res.data.user;
            } catch (e) {
                this.user = null;
                this.$reset(); // <-- clear persisted state if it fails
            } finally {
                this.loading = false;
            }
        },

        // Initialize auth state on app boot
        async initializeAuth() {
            // If we have a persisted user, verify they're still authenticated
            if (this.user) {
                await this.fetchUser();
            } else {
                // Ensure we have a fresh CSRF token for login attempts
                await this.ensureFreshCsrfToken();
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
                
                // Handle CSRF token mismatch (419 = Page Expired)
                if (e.response?.status === 419) {
                    console.warn("CSRF token expired, retrying with fresh token...");
                    
                    try {
                        // Get fresh CSRF cookie and retry
                        await axios.get("/sanctum/csrf-cookie");
                        const retryRes = await axios.post("/login", data);
                        this.user = retryRes.data.user;
                        await this.fetchUser();
                        router.visit("/dashboard");
                        return;
                    } catch (retryError) {
                        console.error("Login retry failed:", retryError);
                        // If retry also fails, show user-friendly error
                        throw new Error("Session expired. Please refresh the page and try again.");
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
            try {
                // Use Inertia for logout instead of axios to ensure proper CSRF handling
                router.post('/logout', {}, {
                    preserveState: false,
                    preserveScroll: false,
                    onSuccess: () => {
                        this.user = null;
                        router.visit('/');
                    },
                    onError: (errors) => {
                        console.error('Logout error:', errors);
                        // Force logout anyway by clearing state
                        this.user = null;
                        router.visit('/');
                    }
                });
            } catch (e) {
                console.error('Logout failed:', e);
                // Force logout anyway by clearing state
                this.user = null;
                router.visit('/');
            }
        },
    },
    persist: true,
});

// In main.js, after creating pinia:
// pinia.use(piniaPersistedstate);
// On app boot: useAuthStore().fetchUser();
