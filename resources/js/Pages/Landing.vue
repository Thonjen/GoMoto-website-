<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import HeroSearchSection from '@/Components/Vehicle/HeroSearchSection.vue';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    laravelVersion: String,
    phpVersion: String,
})

// Popular searches for the landing page
const popularSearches = ['Honda Civic', 'Toyota Vios', 'Yamaha', 'Honda Beat', 'Automatic', 'SUV', 'Motorcycle'];

function handleSearch(searchTerm) {
    if (searchTerm.trim()) {
        router.visit('/vehicles', {
            data: { search: searchTerm.trim() }
        });
    } else {
        router.visit('/vehicles');
    }
}

function handleQuickSearch(searchTerm) {
    router.visit('/vehicles', {
        data: { search: searchTerm }
    });
}

function handleQuickFilter(filterValue) {
    router.visit('/vehicles', {
        data: { category: filterValue }
    });
}
</script>

<template>
    <GuestLayout>
        <Head title="GoMOTO - Premium Vehicle Rental Platform" />
        
        <!-- Hero Section -->
        <div class="relative min-h-screen flex items-center justify-center overflow-hidden">
            <!-- Background overlay for better text readability -->
            <div class="absolute inset-0 bg-gradient-to-b from-black/30 via-transparent to-black/50"></div>
            
            <!-- Floating elements for visual appeal -->
            <div class="absolute top-1/4 left-1/4 w-64 h-64 bg-white/5 rounded-full blur-3xl animate-float"></div>
            <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-white/5 rounded-full blur-3xl animate-float" style="animation-delay: 3s;"></div>
            
            <div class="relative z-10 container mx-auto px-4 py-16">
                <!-- Main Hero Content -->
                <div class="max-w-6xl mx-auto text-center">
                    <!-- Hero Title -->
                    <div class="mb-8 animate-fade-in">
                        <h1 class="text-5xl md:text-7xl font-black text-white mb-6 text-shadow-lg leading-tight">
                            Your Perfect
                            <span class="block bg-gradient-to-r from-white to-white/80 bg-clip-text text-transparent">
                                Ride Awaits
                            </span>
                        </h1>
                        <p class="text-xl md:text-2xl text-white/90 font-light max-w-3xl mx-auto text-shadow">
                            Discover premium vehicles for every journey in Surigao del Norte. 
                            Safe, reliable, and professionally maintained.
                        </p>
                    </div>
                    
                    <!-- Search Section -->
                    <div class="mb-12 animate-slide-up" style="animation-delay: 0.2s;">
                        <HeroSearchSection 
                            :popular-searches="popularSearches"
                            @search="handleSearch"
                            @quick-search="handleQuickSearch"
                            @quick-filter="handleQuickFilter"
                        />
                    </div>
                    
                    <!-- CTA Buttons -->
                    <div class="flex flex-col sm:flex-row gap-6 justify-center mb-16 animate-slide-up" style="animation-delay: 0.4s;">
                        <Link href="/vehicles" class="btn-primary text-lg px-10 py-4 shadow-glow">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            Explore Vehicles
                        </Link>
                        <Link href="/register" class="btn-glass text-lg px-10 py-4">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Become a Partner
                        </Link>
                    </div>

                </div>
            </div>
        </div>

        <!-- Features Section -->
        <div class="relative py-24 bg-gradient-to-b from-transparent to-black/20">
            <div class="container mx-auto px-4">
                <div class="max-w-6xl mx-auto">
                    <!-- Section Header -->
                    <div class="text-center mb-16">
                        <h2 class="text-4xl md:text-5xl font-bold text-white mb-6 text-shadow">
                            Why Choose GoMOTO?
                        </h2>
                        <p class="text-xl text-white/90 max-w-3xl mx-auto">
                            Experience the future of vehicle rental with our cutting-edge platform
                        </p>
                    </div>
                    
                    <!-- Features Grid -->
                    <div class="grid md:grid-cols-3 gap-8">
                        <div class="glass-card-dark border border-white/20 shadow-glow text-center p-8 group hover:scale-105 transition-all duration-300">
                            <div class="w-20 h-20 bg-gradient-to-br from-white/20 to-white/10 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold text-white mb-4">Premium Fleet</h3>
                            <p class="text-white/80 leading-relaxed">
                                Choose from our carefully curated selection of premium vehicles, 
                                from luxury cars to adventure motorcycles
                            </p>
                        </div>
                        
                        <div class="glass-card-dark border border-white/20 shadow-glow text-center p-8 group hover:scale-105 transition-all duration-300">
                            <div class="w-20 h-20 bg-gradient-to-br from-white/20 to-white/10 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold text-white mb-4">Smart Pricing</h3>
                            <p class="text-white/80 leading-relaxed">
                                Transparent, competitive rates with no hidden fees. 
                                Get the best value for your money
                            </p>
                        </div>
                        
                        <div class="glass-card-dark border border-white/20 shadow-glow text-center p-8 group hover:scale-105 transition-all duration-300">
                            <div class="w-20 h-20 bg-gradient-to-br from-white/20 to-white/10 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold text-white mb-4">Trusted & Secure</h3>
                            <p class="text-white/80 leading-relaxed">
                                All vehicles are properly insured and maintained. 
                                Your safety is our top priority
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Call to Action Section -->
        <div class="relative py-24">
            <div class="container mx-auto px-4">
                <div class="max-w-4xl mx-auto text-center">
                    <div class="glass-card-dark border border-white/20 shadow-glow p-12">
                        <h2 class="text-4xl md:text-5xl font-bold text-white mb-6 text-shadow">
                            Ready to Hit the Road?
                        </h2>
                        <p class="text-xl text-white/90 mb-8 max-w-2xl mx-auto">
                            Join thousands of satisfied customers who trust GoMOTO for their transportation needs
                        </p>
                        <div class="flex flex-col sm:flex-row gap-6 justify-center">
                            <Link href="/vehicles" class="btn-primary text-lg px-12 py-4 shadow-glow">
                                Start Your Journey
                            </Link>
                            <Link href="/register" class="btn-glass text-lg px-12 py-4">
                                List Your Vehicle
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </GuestLayout>
</template>

<style scoped>
/* Component-specific animations */
@keyframes float {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    50% { transform: translateY(-20px) rotate(180deg); }
}

.animate-float {
    animation: float 12s ease-in-out infinite;
}

/* Smooth scroll behavior */
html {
    scroll-behavior: smooth;
}
</style>