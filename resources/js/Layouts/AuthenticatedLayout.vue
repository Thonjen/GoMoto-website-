<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/vue3';
import Footer from '@/Components/Footer.vue';
import Header from '@/Components/Header.vue';
import PWAManager from '@/Components/PWAManager.vue';

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
        
        <!-- Enhanced Header with Guest Layout Style -->
        <div class="relative z-50">
            <nav 
                :class="[
                    ' top-0 left-0 right-0 z-50 transition-all duration-300',
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
            <div class="min-h-screen">
                <slot />
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