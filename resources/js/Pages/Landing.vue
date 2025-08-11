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
</script>

<template>
    <GuestLayout>
        <Head title="Vehicle Rental - Find Your Perfect Ride" />
        
        <!-- Hero Section -->
        <div class="relative min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50">
            <div class="absolute inset-0 bg-white/40"></div>
            
            <div class="relative z-10 container mx-auto px-4 py-16">

                <!-- Main Hero Content -->
                <div class="max-w-4xl mx-auto text-center py-16">
                    <!-- Search Section -->
                    <HeroSearchSection 
                        :popular-searches="popularSearches"
                        @search="handleSearch"
                        @quick-search="handleQuickSearch"
                    />
                    
                    <!-- CTA Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-center mt-8">
                        <Link href="/vehicles" class="bg-blue-600 text-white px-8 py-4 rounded-lg text-lg font-semibold hover:bg-blue-700 transition-colors shadow-lg">
                            Browse All Vehicles
                        </Link>
                        <Link href="/register" class="border-2 border-blue-600 text-blue-600 px-8 py-4 rounded-lg text-lg font-semibold hover:bg-blue-50 transition-colors">
                            Become a Partner
                        </Link>
                    </div>
                </div>

                <!-- Features Section -->
                <div class="max-w-6xl mx-auto py-16">
                    <div class="text-center mb-12">
                        <h2 class="text-3xl font-bold text-gray-900 mb-4">Why Choose Us?</h2>
                        <p class="text-lg text-gray-600">Experience the best vehicle rental service in Surigao del Norte</p>
                    </div>
                    
                    <div class="grid md:grid-cols-3 gap-8">
                        <div class="text-center p-6">
                            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <span class="text-3xl">🚗</span>
                            </div>
                            <h3 class="text-xl font-semibold mb-2">Wide Selection</h3>
                            <p class="text-gray-600">Choose from cars, motorcycles, and specialized vehicles for every need</p>
                        </div>
                        
                        <div class="text-center p-6">
                            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <span class="text-3xl">💰</span>
                            </div>
                            <h3 class="text-xl font-semibold mb-2">Best Prices</h3>
                            <p class="text-gray-600">Competitive rates with flexible pricing options for all budgets</p>
                        </div>
                        
                        <div class="text-center p-6">
                            <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <span class="text-3xl">🛡️</span>
                            </div>
                            <h3 class="text-xl font-semibold mb-2">Safe & Secure</h3>
                            <p class="text-gray-600">All vehicles are properly maintained and insured for your safety</p>
                        </div>
                    </div>
                </div>

                <!-- Footer Links -->
                <div class="text-center py-8 border-t border-gray-200">
                    <div class="flex flex-wrap justify-center gap-6 text-sm text-gray-600">
                        <Link href="/privacy" class="hover:text-blue-600">Privacy Policy</Link>
                        <Link href="/terms" class="hover:text-blue-600">Terms of Service</Link>
                        <span>Laravel v{{ laravelVersion }} (PHP v{{ phpVersion }})</span>
                    </div>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>