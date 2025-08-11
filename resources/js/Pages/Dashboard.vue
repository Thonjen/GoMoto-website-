<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <!-- Welcome Header -->
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-lg shadow-lg p-6 text-white mb-8">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-3xl font-bold">Welcome back!</h1>
                            <p class="text-blue-100 mt-1">Here's what's happening with your bookings</p>
                        </div>
                        <div class="text-right">
                            <div class="text-2xl font-bold">{{ bookingStats.total_bookings }}</div>
                            <div class="text-sm text-blue-100">Total Bookings</div>
                        </div>
                    </div>
                </div>

                <!-- Active Rental Alert -->
                <div v-if="activeBooking" class="mb-8">
                    <div class="bg-white rounded-lg shadow-md border-l-4 border-green-500 p-6">
                        <div class="flex items-start justify-between">
                            <div class="flex space-x-4">
                                <img :src="activeBooking.vehicle_image" :alt="activeBooking.vehicle_name" 
                                     class="w-20 h-20 object-cover rounded-lg" />
                                <div>
                                    <h3 class="text-xl font-semibold text-gray-900">🚗 Currently Renting</h3>
                                    <p class="text-lg font-medium text-green-600">{{ activeBooking.vehicle_name }}</p>
                                    <p class="text-sm text-gray-600">Owner: {{ activeBooking.owner_name }}</p>
                                    <p class="text-sm text-gray-600">
                                        Expected return: {{ formatDateTime(activeBooking.expected_return) }}
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <div :class="[
                                    'text-lg font-bold px-3 py-1 rounded-full',
                                    activeBooking.time_remaining.overdue 
                                        ? 'bg-red-100 text-red-800' 
                                        : 'bg-green-100 text-green-800'
                                ]">
                                    {{ activeBooking.time_remaining.text }}
                                </div>
                                <div class="mt-3 space-x-2">
                                    <Link :href="`/bookings/${activeBooking.id}`" 
                                          class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm hover:bg-blue-700">
                                        View Details
                                    </Link>
                                    <a v-if="activeBooking.owner_phone" :href="`tel:${activeBooking.owner_phone}`"
                                       class="bg-green-600 text-white px-4 py-2 rounded-md text-sm hover:bg-green-700">
                                        Call Owner
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white rounded-lg shadow-md p-6 text-center">
                        <div class="text-3xl font-bold text-blue-600">{{ bookingStats.completed_bookings }}</div>
                        <div class="text-sm text-gray-600">Completed Trips</div>
                    </div>
                    <div class="bg-white rounded-lg shadow-md p-6 text-center">
                        <div class="text-3xl font-bold text-green-600">₱{{ formatCurrency(bookingStats.total_spent) }}</div>
                        <div class="text-sm text-gray-600">Total Spent</div>
                    </div>
                    <div class="bg-white rounded-lg shadow-md p-6 text-center">
                        <div class="text-3xl font-bold text-yellow-600">{{ bookingStats.cancelled_bookings }}</div>
                        <div class="text-sm text-gray-600">Cancelled</div>
                    </div>
                    <div class="bg-white rounded-lg shadow-md p-6 text-center">
                        <div :class="[
                            'text-3xl font-bold',
                            bookingStats.unpaid_overcharges > 0 ? 'text-red-600' : 'text-gray-400'
                        ]">
                            ₱{{ formatCurrency(bookingStats.unpaid_overcharges) }}
                        </div>
                        <div class="text-sm text-gray-600">Unpaid Overcharges</div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    
                    <!-- Recent Bookings -->
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-lg shadow-md">
                            <div class="p-6 border-b border-gray-200">
                                <div class="flex items-center justify-between">
                                    <h2 class="text-xl font-semibold text-gray-900">Recent Bookings</h2>
                                    <Link href="/bookings" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                                        View All
                                    </Link>
                                </div>
                            </div>
                            
                            <div v-if="recentBookings.length === 0" class="p-8 text-center text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                <p>No bookings yet</p>
                                <p class="text-sm mt-1">Start by browsing available vehicles</p>
                                <Link href="/vehicles" class="mt-4 bg-blue-600 text-white px-4 py-2 rounded-md text-sm hover:bg-blue-700 inline-block">
                                    Browse Vehicles
                                </Link>
                            </div>

                            <div v-else class="divide-y divide-gray-200">
                                <div v-for="booking in recentBookings" :key="booking.id" 
                                     class="p-6 hover:bg-gray-50 transition-colors">
                                    <div class="flex items-start space-x-4">
                                        <img :src="booking.vehicle_image" :alt="booking.vehicle_name" 
                                             class="w-16 h-16 object-cover rounded-lg" />
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between">
                                                <h3 class="text-lg font-medium text-gray-900 truncate">
                                                    {{ booking.vehicle_name }}
                                                </h3>
                                                <span :class="getStatusClass(booking.status)"
                                                      class="px-2 py-1 rounded-full text-xs font-medium">
                                                    {{ formatStatus(booking.status) }}
                                                </span>
                                            </div>
                                            
                                            <div class="mt-2 grid grid-cols-2 gap-4 text-sm text-gray-600">
                                                <div>
                                                    <span class="font-medium">Pickup:</span> 
                                                    {{ formatDate(booking.pickup_datetime) }}
                                                </div>
                                                <div>
                                                    <span class="font-medium">Return:</span> 
                                                    {{ formatDate(booking.expected_return) }}
                                                </div>
                                                <div>
                                                    <span class="font-medium">Owner:</span> 
                                                    {{ booking.owner_name }}
                                                </div>
                                                <div>
                                                    <span class="font-medium">Amount:</span> 
                                                    ₱{{ formatCurrency(booking.total_amount) }}
                                                </div>
                                            </div>

                                            <!-- Overdue/Overcharge alerts -->
                                            <div v-if="booking.is_overdue" class="mt-2 flex items-center text-sm">
                                                <svg class="w-4 h-4 text-red-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                                </svg>
                                                <span class="text-red-600 font-medium">Overdue - potential overcharges</span>
                                            </div>

                                            <div v-if="booking.has_overcharges" class="mt-2 flex items-center text-sm">
                                                <svg class="w-4 h-4 text-orange-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                                </svg>
                                                <span class="text-orange-600 font-medium">
                                                    Overcharges: ₱{{ formatCurrency(booking.total_overcharges) }}
                                                </span>
                                            </div>

                                            <div class="mt-3 flex space-x-3">
                                                <Link :href="`/bookings/${booking.id}`" 
                                                      class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                                                    View Details
                                                </Link>
                                                <button v-if="booking.can_extend && booking.is_overdue" 
                                                        @click="redirectToExtend(booking.id)"
                                                        class="text-green-600 hover:text-green-700 text-sm font-medium">
                                                    Extend Booking
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions & Featured Vehicles -->
                    <div class="space-y-6">
                        
                        <!-- Quick Actions -->
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                            <div class="space-y-3">
                                <Link href="/vehicles" 
                                      class="w-full bg-blue-600 text-white px-4 py-2 rounded-md text-sm hover:bg-blue-700 flex items-center justify-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                    Browse Vehicles
                                </Link>
                                <Link href="/bookings" 
                                      class="w-full bg-gray-600 text-white px-4 py-2 rounded-md text-sm hover:bg-gray-700 flex items-center justify-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                    My Bookings
                                </Link>
                                <Link href="/profile" 
                                      class="w-full bg-green-600 text-white px-4 py-2 rounded-md text-sm hover:bg-green-700 flex items-center justify-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    Update Profile
                                </Link>
                            </div>
                        </div>

                        <!-- Featured Vehicles -->
                        <div class="bg-white rounded-lg shadow-md">
                            <div class="p-6 border-b border-gray-200">
                                <h3 class="text-lg font-semibold text-gray-900">Featured Vehicles</h3>
                            </div>
                            <div class="p-4 space-y-4">
                                <div v-for="vehicle in featuredVehicles.slice(0, 3)" :key="vehicle.id" 
                                     class="flex items-center space-x-3">
                                    <img :src="vehicle.image" :alt="vehicle.name" 
                                         class="w-12 h-12 object-cover rounded" />
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">{{ vehicle.name }}</p>
                                        <p class="text-xs text-gray-500">{{ vehicle.location }}</p>
                                        <p class="text-xs text-blue-600">From ₱{{ formatCurrency(vehicle.price_from) }}</p>
                                    </div>
                                </div>
                                <Link href="/vehicles" 
                                      class="block text-center text-blue-600 hover:text-blue-700 text-sm font-medium pt-2">
                                    View All Vehicles →
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

defineProps({
    recentBookings: Array,
    activeBooking: Object,
    bookingStats: Object,
    featuredVehicles: Array,
});

function formatCurrency(amount) {
    return parseFloat(amount).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function formatDateTime(dateTime) {
    if (!dateTime) return 'N/A';
    return new Date(dateTime).toLocaleString('en-US', {
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

function formatDate(dateTime) {
    if (!dateTime) return 'N/A';
    return new Date(dateTime).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric'
    });
}

function getStatusClass(status) {
    const classes = {
        'pending': 'bg-yellow-100 text-yellow-800',
        'confirmed': 'bg-green-100 text-green-800',
        'completed': 'bg-blue-100 text-blue-800',
        'cancelled': 'bg-red-100 text-red-800',
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
}

function formatStatus(status) {
    const statusMap = {
        'pending': 'Pending',
        'confirmed': 'Active',
        'completed': 'Completed',
        'cancelled': 'Cancelled',
    };
    return statusMap[status] || status;
}

function redirectToExtend(bookingId) {
    router.visit(`/bookings/${bookingId}#extend`);
}
</script>
