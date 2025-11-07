<script setup>
import { ref, onMounted, onUnmounted } from "vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import NavLink from "@/Components/NavLink.vue";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";
import { Link } from "@inertiajs/vue3";
import Footer from "@/Components/Footer.vue";
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
        <!-- Animated background pattern -->
        <div class="absolute inset-0 opacity-10" style="background-image: url('data:image/svg+xml;utf8,<svg width=&quot;60&quot; height=&quot;60&quot; viewBox=&quot;0 0 60 60&quot; xmlns=&quot;http://www.w3.org/2000/svg&quot;><g fill=&quot;none&quot; fill-rule=&quot;evenodd&quot;><g fill=&quot;%23ffffff&quot; fill-opacity=&quot;0.05&quot;><circle cx=&quot;7&quot; cy=&quot;7&quot; r=&quot;7&quot;/></g></g></svg>')"></div>
        
        <!-- Navigation -->
        <nav 
            :class="[
                'fixed top-0 left-0 right-0 z-50 transition-all duration-300',
                isScrolled 
                    ? 'glass-nav-dark scrolled' 
                    : 'glass-nav-dark'
            ]"
        >
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-20 justify-between items-center">
                    <div class="flex items-center">
                        <!-- Logo -->
                        <div class="flex shrink-0 items-center space-x-3">
                            <Link :href="route('dashboard')" class="flex items-center space-x-3 group">
                                <div class="relative">
                                    <ApplicationLogo
                                        class="block h-10 w-auto text-white transition-transform duration-200 group-hover:scale-110"
                                    />
                                    <div class="absolute inset-0 rounded-full bg-white/10 backdrop-blur-sm opacity-0 group-hover:opacity-100 transition-opacity duration-200 -z-10"></div>
                                </div>
                            </Link>
                        </div>
                    </div>

                    <!-- Desktop Navigation -->
                    <div class="hidden sm:flex sm:items-center sm:space-x-8">
                        <Link
                            :href="route('Landing')"
                            :class="[
                                'font-medium transition-all duration-200 hover:scale-105 no-underline',
                                route().current('Landing') 
                                    ? '!text-white font-semibold' 
                                    : 'text-white/90 hover:text-white'
                            ]"
                        >
                            Home
                        </Link>

                        <Link
                            :href="route('login')"
                            :class="[
                                'font-medium transition-all duration-200 hover:scale-105 no-underline',
                                route().current('login') 
                                    ? '!text-white font-semibold' 
                                    : 'text-white/90 hover:text-white'
                            ]"
                        >
                            Login
                        </Link>

                        <Link
                            :href="route('register')"
                            class="btn-glass text-sm font-semibold transition-all duration-200 hover:scale-105"
                        >
                            Get Started
                        </Link>
                    </div>

                    <!-- Mobile menu button -->
                    <div class="sm:hidden">
                        <button
                            @click="showingNavigationDropdown = !showingNavigationDropdown"
                            class="relative inline-flex items-center justify-center rounded-xl p-3 text-white/80 hover:text-white hover:bg-white/10 focus:outline-none focus:bg-white/10 transition-all duration-200"
                        >
                            <svg
                                class="h-6 w-6 transition-transform duration-200"
                                :class="{ 'rotate-90': showingNavigationDropdown }"
                                stroke="currentColor"
                                fill="none"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    :class="{
                                        hidden: showingNavigationDropdown,
                                        'inline-flex': !showingNavigationDropdown,
                                    }"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16"
                                />
                                <path
                                    :class="{
                                        hidden: !showingNavigationDropdown,
                                        'inline-flex': showingNavigationDropdown,
                                    }"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"
                                />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile Navigation Menu -->
            <div
                :class="{
                    block: showingNavigationDropdown,
                    hidden: !showingNavigationDropdown,
                }"
                class="sm:hidden border-t border-white/10"
            >
                <div class="bg-black/20 backdrop-blur-lg">
                    <div class="space-y-1 px-4 py-6">
                        <Link
                            :href="route('Landing')"
                            :class="[
                                'block px-4 py-3 rounded-xl font-medium transition-all duration-200 no-underline',
                                route().current('Landing') 
                                    ? '!text-black bg-white hover:bg-white' 
                                    : 'text-white/90 hover:text-white hover:bg-white/10'
                            ]"
                        >
                            Home
                        </Link>

                        <Link
                            :href="route('login')"
                            :class="[
                                'block px-4 py-3 rounded-xl font-medium transition-all duration-200 no-underline',
                                route().current('login') 
                                    ? '!text-black bg-white hover:bg-white' 
                                    : 'text-white/90 hover:text-white hover:bg-white/10'
                            ]"
                        >
                            Login
                        </Link>

                        <Link
                            :href="route('register')"
                            :class="[
                                'block px-4 py-3 rounded-xl font-medium transition-all duration-200 no-underline',
                                route().current('register') 
                                    ? '!text-black bg-white hover:bg-white' 
                                    : 'text-white/90 hover:text-white hover:bg-white/10'
                            ]"
                        >
                            Register
                        </Link>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Heading -->
        <header 
            v-if="$slots.header" 
            class="pt-24 pb-8 bg-gradient-to-r from-black/20 to-transparent"
        >
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                <slot name="header" />
            </div>
        </header>

        <!-- Page Content -->
        <main class="relative z-10 pt-20">
            <slot />
        </main>

        <Footer />
        
        <!-- PWA Manager Component -->
        <PWAManager />
    </div>
</template>

<style scoped>
/* Custom animations for this component */
@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
}

.animate-float {
    animation: float 6s ease-in-out infinite;
}
</style>
