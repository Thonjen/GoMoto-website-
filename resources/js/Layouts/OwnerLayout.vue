<script setup>
import { ref, onMounted, onUnmounted } from "vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import NavLink from "@/Components/NavLink.vue";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";
import { Link } from "@inertiajs/vue3";
import Footer from "@/Components/Footer.vue";
import Header from "@/Components/Header.vue";
import PWAManager from "@/Components/PWAManager.vue";

const showingNavigationDropdown = ref(false);
const isScrolled = ref(false);

const handleScroll = () => {
    isScrolled.value = window.scrollY > 20;
};

onMounted(() => {
    window.addEventListener('scroll', handleScroll);
});

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll);
});
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-[#535862] via-gray-800 to-gray-900">
        <!-- Background Pattern -->
        <div class="fixed inset-0 opacity-[0.05]" style="background-image: url('data:image/svg+xml;utf8,<svg width=&quot;100&quot; height=&quot;100&quot; viewBox=&quot;0 0 100 100&quot; xmlns=&quot;http://www.w3.org/2000/svg&quot;><circle cx=&quot;20&quot; cy=&quot;20&quot; r=&quot;2&quot; fill=&quot;%23ffffff&quot;/><circle cx=&quot;80&quot; cy=&quot;80&quot; r=&quot;2&quot; fill=&quot;%23ffffff&quot;/></svg>')"></div>
        
        <!-- Enhanced Header -->
        <div class="relative z-50">
            <nav 
                :class="[
                    'top-0 left-0 right-0 z-50 transition-all duration-300',
                    isScrolled 
                        ? 'glass-nav-dark scrolled shadow-2xl' 
                        : 'glass-nav-dark'
                ]"
            >
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex h-20 justify-between items-center">
                        <Header />
                    </div>
                </div>
            </nav>
        </div>

        <!-- Page Heading -->
        <header 
            class="relative bg-gradient-to-r from-[#535862] to-gray-700 shadow-xl" 
            v-if="$slots.header"
        >
            <div class="absolute inset-0 bg-[#00000040]"></div>
            <div class="relative z-10 mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
                <div class="glass-card-dark p-6 border border-white/20 shadow-2xl">
                    <slot name="header" />
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="relative z-10">
            <div class="min-h-screen py-8">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <aside class="md:col-span-1 glass-card p-6 h-fit shadow-glow">
                            <h2 class="text-xl font-semibold text-white mb-4">
                                Owner Tools
                            </h2>
                            <nav class="flex flex-col gap-3">
                                <!-- Parent -->
                                <NavLink
                                    :href="route('owner.vehicles.index')"
                                    :active="route().current('owner.vehicles.index')"
                                    class="text-white/80 font-medium hover:text-white transition-colors p-3 rounded-lg hover:bg-white/10 flex items-center gap-2"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                    My Vehicles
                                </NavLink>

                                <!-- Child (only shows inside vehicles routes) -->
                                <NavLink
                                    v-if="route().current('owner.vehicles.*')" 
                                    class="ml-6 text-white/60 font-medium hover:text-white transition-colors p-2 rounded-lg hover:bg-white/10 text-sm flex items-center gap-2"
                                    :href="route('owner.vehicles.create')"
                                    :active="route().current('owner.vehicles.create')"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Add New Vehicle
                                </NavLink>

                                <!-- Payment System -->
                                <NavLink
                                    :href="route('owner.payment-settings.show')"
                                    :active="route().current('owner.payment-settings.show')"
                                    class="text-white/80 font-medium hover:text-white transition-colors p-3 rounded-lg hover:bg-white/10 flex items-center gap-2"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                    </svg>
                                    Payment System
                                </NavLink>

                                <!-- Child link: only visible when inside payment routes -->
                                <NavLink
                                    v-if="route().current('owner.payment-settings.*') || route().current('owner.gcash-qr.*')" 
                                    class="ml-6 text-white/60 font-medium hover:text-white transition-colors p-2 rounded-lg hover:bg-white/10 text-sm flex items-center gap-2"
                                    :href="route('owner.gcash-qr.show')"
                                    :active="route().current('owner.gcash-qr.show')"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                    </svg>
                                    Upload GCASH QR
                                </NavLink>

                                <NavLink
                                    v-if="$is('owner')"
                                    :href="route('owner.bookings.index')"
                                    :active="route().current('owner.bookings.*')"
                                    class="text-white/80 font-medium hover:text-white transition-colors p-3 rounded-lg hover:bg-white/10 flex items-center gap-2"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                    Booking Requests
                                </NavLink>

                                <NavLink
                                    v-if="$is('owner')"
                                    :href="route('owner.extensionRequests.index')"
                                    :active="route().current('owner.extensionRequests.index')"
                                    class="text-white/80 font-medium hover:text-white transition-colors p-3 rounded-lg hover:bg-white/10 flex items-center gap-2"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Extension Requests
                                </NavLink>

                                <!-- Parent: Overcharges -->
                                <NavLink
                                    :href="route('owner.overcharges.settings')"
                                    :active="route().current('owner.overcharges.settings')"
                                    class="text-white/80 font-medium hover:text-white transition-colors p-3 rounded-lg hover:bg-white/10 flex items-center gap-2"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                    </svg>
                                    Overcharges
                                </NavLink>

                                <!-- Pricing Tiers -->
                                <NavLink
                                    :href="route('owner.pricing-tiers.index')"
                                    :active="route().current('owner.pricing-tiers.*')"
                                    class="text-white/80 font-medium hover:text-white transition-colors p-3 rounded-lg hover:bg-white/10 flex items-center gap-2"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                    </svg>
                                    Pricing Tiers
                                </NavLink>

                                <!-- Child links: visible when inside overcharges routes -->
                                <NavLink
                                    v-if="route().current('owner.overcharges.*')" 
                                    class="ml-6 text-white/60 font-medium hover:text-white transition-colors p-2 rounded-lg hover:bg-white/10 text-sm flex items-center gap-2"
                                    :href="route('owner.overcharges.stats')"
                                    :active="route().current('owner.overcharges.stats')"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                    </svg>
                                    Overcharges Statistics
                                </NavLink>
                            </nav>
                        </aside>

                        <div class="md:col-span-3 glass-card p-6 shadow-glow">
                            <slot />
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <Footer />
        
        <!-- PWA Manager Component -->
        <PWAManager />
    </div>
</template>

<style scoped>
/* Smooth transitions for layout changes */
* {
    transition: background-color 0.3s ease, box-shadow 0.3s ease;
}
</style>
