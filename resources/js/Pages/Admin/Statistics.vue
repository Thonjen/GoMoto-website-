<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    mostRentedVehicles: Array,
    highestEarningVehicles: Array,
    vehicleTypeStats: Array,
    monthlyBookings: Array,
    topOwners: Array,
    platformStats: Object,
    avgBookingDuration: Number,
});

// Function to format currency
const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP'
    }).format(amount);
};

// Function to format numbers
const formatNumber = (number) => {
    return new Intl.NumberFormat().format(number);
};
</script>

<template>
    <Head title="Platform Analytics - GoMOTO" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center space-x-3">
                <div class="p-2 bg-white/10 rounded-lg backdrop-blur-sm">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-white">Platform Analytics</h2>
                    <p class="text-sm text-white/70">Comprehensive insights across the GoMOTO platform</p>
                </div>
            </div>
        </template>

        <!-- Background Pattern -->
        <div class="fixed inset-0 pointer-events-none">
            <div class="absolute inset-0 bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900"></div>
            <div class="absolute inset-0 opacity-20">
                <div class="absolute top-0 left-0 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl"></div>
                <div class="absolute bottom-0 right-0 w-96 h-96 bg-purple-500/10 rounded-full blur-3xl"></div>
                <div class="absolute top-1/2 left-1/2 w-96 h-96 bg-green-500/10 rounded-full blur-3xl transform -translate-x-1/2 -translate-y-1/2"></div>
            </div>
        </div>

        <div class="relative py-8 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto space-y-8">
                
                <!-- Platform Overview Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Total Vehicles -->
                    <div class="glass-card group hover:scale-105 transition-all duration-300">
                        <div class="flex items-center">
                            <div class="p-4 rounded-2xl bg-gradient-to-br from-blue-500/20 to-blue-600/20 backdrop-blur-sm border border-blue-500/30 group-hover:from-blue-500/30 group-hover:to-blue-600/30 transition-all duration-300">
                                <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l2.414 2.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0M15 17a2 2 0 104 0"></path>
                                </svg>
                            </div>
                            <div class="ml-5">
                                <h3 class="text-sm font-medium text-white/60 uppercase tracking-wider">Platform Vehicles</h3>
                                <p class="text-3xl font-bold text-white">{{ formatNumber(platformStats.total_vehicles) }}</p>
                                <p class="text-sm text-emerald-400 font-medium">{{ formatNumber(platformStats.active_vehicles) }} available</p>
                            </div>
                        </div>
                    </div>

                    <!-- Platform Revenue -->
                    <div class="glass-card group hover:scale-105 transition-all duration-300">
                        <div class="flex items-center">
                            <div class="p-4 rounded-2xl bg-gradient-to-br from-emerald-500/20 to-emerald-600/20 backdrop-blur-sm border border-emerald-500/30 group-hover:from-emerald-500/30 group-hover:to-emerald-600/30 transition-all duration-300">
                                <svg class="w-8 h-8 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                            </div>
                            <div class="ml-5">
                                <h3 class="text-sm font-medium text-white/60 uppercase tracking-wider">Total Revenue</h3>
                                <p class="text-3xl font-bold text-white">{{ formatCurrency(platformStats.total_revenue) }}</p>
                                <p class="text-sm text-emerald-400 font-medium">{{ formatNumber(platformStats.completed_bookings) }} completed</p>
                            </div>
                        </div>
                    </div>

                    <!-- Total Users -->
                    <div class="glass-card group hover:scale-105 transition-all duration-300">
                        <div class="flex items-center">
                            <div class="p-4 rounded-2xl bg-gradient-to-br from-purple-500/20 to-purple-600/20 backdrop-blur-sm border border-purple-500/30 group-hover:from-purple-500/30 group-hover:to-purple-600/30 transition-all duration-300">
                                <svg class="w-8 h-8 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-7.5l1.5 1.5-3.75 3.75-1.5-1.5 3.75-3.75z"></path>
                                </svg>
                            </div>
                            <div class="ml-5">
                                <h3 class="text-sm font-medium text-white/60 uppercase tracking-wider">Community</h3>
                                <p class="text-3xl font-bold text-white">{{ formatNumber(platformStats.total_users) }}</p>
                                <p class="text-sm text-purple-400 font-medium">{{ formatNumber(platformStats.total_owners) }} owners</p>
                            </div>
                        </div>
                    </div>

                    <!-- Total Bookings -->
                    <div class="glass-card group hover:scale-105 transition-all duration-300">
                        <div class="flex items-center">
                            <div class="p-4 rounded-2xl bg-gradient-to-br from-amber-500/20 to-amber-600/20 backdrop-blur-sm border border-amber-500/30 group-hover:from-amber-500/30 group-hover:to-amber-600/30 transition-all duration-300">
                                <svg class="w-8 h-8 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                            </div>
                            <div class="ml-5">
                                <h3 class="text-sm font-medium text-white/60 uppercase tracking-wider">Bookings</h3>
                                <p class="text-3xl font-bold text-white">{{ formatNumber(platformStats.total_bookings) }}</p>
                                <p class="text-sm text-orange-400 font-medium">{{ formatNumber(platformStats.pending_bookings) }} pending</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Platform Health & Trends Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Platform Health -->
                    <div class="glass-card">
                        <div class="p-6 border-b border-white/10">
                            <h3 class="text-xl font-bold text-white flex items-center">
                                <svg class="w-5 h-5 mr-3 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </svg>
                                Platform Health
                            </h3>
                        </div>
                        <div class="p-6 space-y-6">
                            <div class="text-center p-6 bg-gradient-to-br from-emerald-500/10 to-emerald-600/10 rounded-2xl border border-emerald-500/20">
                                <div class="text-3xl font-bold text-emerald-400 mb-2">
                                    {{ platformStats.total_vehicles > 0 ? Math.round((platformStats.active_vehicles / platformStats.total_vehicles) * 100) : 0 }}%
                                </div>
                                <div class="text-sm text-white/60 uppercase tracking-wider">Vehicle Availability</div>
                            </div>
                            <div class="text-center p-6 bg-gradient-to-br from-blue-500/10 to-blue-600/10 rounded-2xl border border-blue-500/20">
                                <div class="text-3xl font-bold text-blue-400 mb-2">
                                    {{ platformStats.total_bookings > 0 ? Math.round((platformStats.completed_bookings / platformStats.total_bookings) * 100) : 0 }}%
                                </div>
                                <div class="text-sm text-white/60 uppercase tracking-wider">Completion Rate</div>
                            </div>
                            <div class="text-center p-6 bg-gradient-to-br from-purple-500/10 to-purple-600/10 rounded-2xl border border-purple-500/20">
                                <div class="text-3xl font-bold text-purple-400 mb-2">{{ avgBookingDuration }}</div>
                                <div class="text-sm text-white/60 uppercase tracking-wider">Avg. Duration (hrs)</div>
                            </div>
                        </div>
                    </div>

                    <!-- Monthly Trends -->
                    <div class="lg:col-span-2 glass-card">
                        <div class="p-6 border-b border-white/10">
                            <h3 class="text-xl font-bold text-white flex items-center">
                                <svg class="w-5 h-5 mr-3 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                </svg>
                                Rental Performance
                            </h3>
                        </div>
                        <div class="p-6">
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead>
                                        <tr class="border-b border-white/10">
                                            <th class="text-left py-3 px-4 text-white/60 font-medium uppercase tracking-wider text-xs">Period</th>
                                            <th class="text-left py-3 px-4 text-white/60 font-medium uppercase tracking-wider text-xs">Bookings</th>
                                            <th class="text-left py-3 px-4 text-white/60 font-medium uppercase tracking-wider text-xs">Revenue</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-white/5">
                                        <tr v-for="month in monthlyBookings" :key="month.month" class="hover:bg-white/5 transition-colors duration-200">
                                            <td class="py-4 px-4 text-white font-medium">{{ month.month }}</td>
                                            <td class="py-4 px-4 text-white/70">{{ formatNumber(month.bookings) }}</td>
                                            <td class="py-4 px-4 text-emerald-400 font-semibold">{{ formatCurrency(month.revenue) }}</td>
                                        </tr>
                                        <tr v-if="monthlyBookings.length === 0">
                                            <td colspan="3" class="py-8 text-center text-white/40">No data available</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Top Performing Vehicles -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Most Rented Vehicles -->
                    <div class="glass-card">
                        <div class="p-6 border-b border-white/10">
                            <h3 class="text-xl font-bold text-white flex items-center">
                                <svg class="w-5 h-5 mr-3 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                                </svg>
                                Most Popular Vehicles
                            </h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                <div v-for="(vehicle, index) in mostRentedVehicles.slice(0, 5)" :key="vehicle.id" 
                                     class="flex items-center justify-between p-4 bg-white/5 rounded-xl border border-white/10 hover:bg-white/10 transition-all duration-200">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-10 h-10 bg-gradient-to-br from-amber-500/20 to-amber-600/20 rounded-full flex items-center justify-center border border-amber-500/30">
                                            <span class="text-sm font-bold text-amber-400">#{{ index + 1 }}</span>
                                        </div>
                                        <div>
                                            <p class="text-white font-medium">{{ vehicle.name }}</p>
                                            <p class="text-xs text-white/50">{{ vehicle.plate_number }}</p>
                                            <p class="text-xs text-blue-400">Owner: {{ vehicle.owner }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-amber-400 font-bold text-lg">{{ formatNumber(vehicle.booking_count) }}</p>
                                        <p class="text-xs text-white/50">rentals</p>
                                    </div>
                                </div>
                                <div v-if="mostRentedVehicles.length === 0" class="text-center py-12 text-white/40">
                                    <svg class="w-12 h-12 mx-auto mb-4 text-white/20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H9m0 0H5m2 0v-2a2 2 0 012-2h2a2 2 0 012 2v2z"></path>
                                    </svg>
                                    <p>No vehicle data available</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Highest Earning Vehicles -->
                    <div class="glass-card">
                        <div class="p-6 border-b border-white/10">
                            <h3 class="text-xl font-bold text-white flex items-center">
                                <svg class="w-5 h-5 mr-3 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                                Top Revenue Generators
                            </h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                <div v-for="(vehicle, index) in highestEarningVehicles.slice(0, 5)" :key="vehicle.id" 
                                     class="flex items-center justify-between p-4 bg-white/5 rounded-xl border border-white/10 hover:bg-white/10 transition-all duration-200">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-10 h-10 bg-gradient-to-br from-emerald-500/20 to-emerald-600/20 rounded-full flex items-center justify-center border border-emerald-500/30">
                                            <span class="text-sm font-bold text-emerald-400">#{{ index + 1 }}</span>
                                        </div>
                                        <div>
                                            <p class="text-white font-medium">{{ vehicle.name }}</p>
                                            <p class="text-xs text-white/50">{{ vehicle.plate_number }}</p>
                                            <p class="text-xs text-blue-400">Owner: {{ vehicle.owner }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-emerald-400 font-bold">{{ formatCurrency(vehicle.total_earnings) }}</p>
                                        <p class="text-xs text-white/50">total earnings</p>
                                    </div>
                                </div>
                                <div v-if="highestEarningVehicles.length === 0" class="text-center py-12 text-white/40">
                                    <svg class="w-12 h-12 mx-auto mb-4 text-white/20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H9m0 0H5m2 0v-2a2 2 0 012-2h2a2 2 0 012 2v2z"></path>
                                    </svg>
                                    <p>No earnings data available</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Vehicle Type Distribution & Top Owners -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Vehicle Type Distribution -->
                    <div class="glass-card">
                        <div class="p-6 border-b border-white/10">
                            <h3 class="text-xl font-bold text-white flex items-center">
                                <svg class="w-5 h-5 mr-3 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
                                </svg>
                                Vehicle Categories
                            </h3>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-2 gap-4">
                                <div v-for="type in vehicleTypeStats" :key="type.sub_type" class="text-center p-4 bg-gradient-to-br from-cyan-500/10 to-cyan-600/10 rounded-xl border border-cyan-500/20 hover:scale-105 transition-transform duration-200">
                                    <div class="text-2xl font-bold text-cyan-400 mb-1">{{ formatNumber(type.count) }}</div>
                                    <div class="text-xs text-white/60 capitalize">{{ type.sub_type }}</div>
                                </div>
                                <div v-if="vehicleTypeStats.length === 0" class="col-span-full text-center py-12 text-white/40">
                                    <svg class="w-12 h-12 mx-auto mb-4 text-white/20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    <p>No vehicle type data</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Top Owners -->
                    <div class="glass-card">
                        <div class="p-6 border-b border-white/10">
                            <h3 class="text-xl font-bold text-white flex items-center">
                                <svg class="w-5 h-5 mr-3 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                Top Partners
                            </h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4 max-h-80 overflow-y-auto custom-scrollbar">
                                <div v-for="(owner, index) in topOwners.slice(0, 8)" :key="owner.id" 
                                     class="flex items-center justify-between p-4 bg-white/5 rounded-xl border border-white/10 hover:bg-white/10 transition-all duration-200">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-10 h-10 bg-gradient-to-br from-pink-500/20 to-pink-600/20 rounded-full flex items-center justify-center border border-pink-500/30">
                                            <span class="text-xs font-bold text-pink-400">{{ owner.name.charAt(0) }}</span>
                                        </div>
                                        <div>
                                            <p class="text-white font-medium">{{ owner.name }}</p>
                                            <p class="text-xs text-white/50">{{ owner.email }}</p>
                                            <div class="flex items-center space-x-3 mt-1">
                                                <span class="text-xs text-blue-400">{{ formatNumber(owner.vehicle_count) }} vehicles</span>
                                                <span class="text-xs text-amber-400">{{ formatNumber(owner.total_bookings) }} bookings</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-emerald-400 font-bold">{{ formatCurrency(owner.total_revenue) }}</p>
                                        <p class="text-xs text-white/50">revenue</p>
                                    </div>
                                </div>
                                <div v-if="topOwners.length === 0" class="text-center py-12 text-white/40">
                                    <svg class="w-12 h-12 mx-auto mb-4 text-white/20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-7.5l1.5 1.5-3.75 3.75-1.5-1.5 3.75-3.75z"></path>
                                    </svg>
                                    <p>No owner data available</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
/* Glassmorphism Card Style */
.glass-card {
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 1rem;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
}

/* Custom Scrollbar */
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 9999px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.2);
    border-radius: 9999px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.3);
}

/* Hover animations */
.glass-card:hover {
    border-color: rgba(255, 255, 255, 0.2);
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
    transform: translateY(-2px);
}

/* Glow effect for interactive elements */
.glass-card .group:hover {
    box-shadow: 0 0 30px rgba(255, 255, 255, 0.1);
}
</style>
