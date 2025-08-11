<template>
    <Head title="Owner Dashboard" />

    <AuthenticatedLayout>
        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <!-- Welcome Header -->
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-lg shadow-lg p-6 text-white mb-8">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-3xl font-bold">Owner Dashboard</h1>
                            <p class="text-blue-100 mt-1">Monitor your vehicles and track your earnings</p>
                        </div>
                        <div class="text-right">
                            <div class="text-2xl font-bold">{{ vehicleStats.total_vehicles }}</div>
                            <div class="text-sm text-blue-100">Total Vehicles</div>
                        </div>
                    </div>
                </div>

                <!-- Vehicle Statistics -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white rounded-lg shadow-md p-6 text-center">
                        <div class="text-3xl font-bold text-green-600">{{ vehicleStats.available_vehicles }}</div>
                        <div class="text-sm text-gray-600">Available</div>
                    </div>
                    <div class="bg-white rounded-lg shadow-md p-6 text-center">
                        <div class="text-3xl font-bold text-blue-600">{{ vehicleStats.currently_booked }}</div>
                        <div class="text-sm text-gray-600">Currently Booked</div>
                    </div>
                    <div class="bg-white rounded-lg shadow-md p-6 text-center">
                        <div class="text-3xl font-bold text-gray-600">{{ vehicleStats.unavailable_vehicles }}</div>
                        <div class="text-sm text-gray-600">Unavailable</div>
                    </div>
                    <div class="bg-white rounded-lg shadow-md p-6 text-center">
                        <div class="text-3xl font-bold text-purple-600">{{ bookingStats.pending_bookings }}</div>
                        <div class="text-sm text-gray-600">Pending Requests</div>
                    </div>
                </div>

                <!-- Earnings Overview -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white rounded-lg shadow-md p-6 text-center">
                        <div class="text-3xl font-bold text-green-600">₱{{ formatCurrency(earningsStats.total_earnings) }}</div>
                        <div class="text-sm text-gray-600">Total Earnings</div>
                    </div>
                    <div class="bg-white rounded-lg shadow-md p-6 text-center">
                        <div class="text-3xl font-bold text-blue-600">₱{{ formatCurrency(earningsStats.this_month_earnings) }}</div>
                        <div class="text-sm text-gray-600">This Month</div>
                    </div>
                    <div class="bg-white rounded-lg shadow-md p-6 text-center">
                        <div class="text-3xl font-bold text-orange-600">₱{{ formatCurrency(earningsStats.pending_payments) }}</div>
                        <div class="text-sm text-gray-600">Pending Payments</div>
                    </div>
                    <div class="bg-white rounded-lg shadow-md p-6 text-center">
                        <div class="text-3xl font-bold text-red-600">₱{{ formatCurrency(earningsStats.overcharge_earnings) }}</div>
                        <div class="text-sm text-gray-600">Overcharge Earnings</div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    
                    <!-- Active Bookings -->
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-lg shadow-md">
                            <div class="p-6 border-b border-gray-200">
                                <div class="flex items-center justify-between">
                                    <h2 class="text-xl font-semibold text-gray-900">Currently Active Rentals</h2>
                                    <Link :href="route('owner.bookings.index')" 
                                          class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                                        View All
                                    </Link>
                                </div>
                            </div>
                            
                            <div v-if="activeBookings.length === 0" class="p-8 text-center text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                <p>No active rentals</p>
                                <p class="text-sm mt-1">Your vehicles are not currently rented out</p>
                            </div>

                            <div v-else class="divide-y divide-gray-200">
                                <div v-for="booking in activeBookings" :key="booking.id" 
                                     class="p-6 hover:bg-gray-50 transition-colors">
                                    <div class="flex items-start space-x-4">
                                        <img :src="booking.vehicle_image" :alt="booking.vehicle_name" 
                                             class="w-16 h-16 object-cover rounded-lg" />
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between">
                                                <h3 class="text-lg font-medium text-gray-900 truncate">
                                                    {{ booking.vehicle_name }}
                                                </h3>
                                                <div :class="[
                                                    'text-sm font-bold px-3 py-1 rounded-full',
                                                    booking.time_remaining?.overdue 
                                                        ? 'bg-red-100 text-red-800' 
                                                        : 'bg-green-100 text-green-800'
                                                ]">
                                                    {{ booking.time_remaining?.text }}
                                                </div>
                                            </div>
                                            
                                            <div class="mt-2 grid grid-cols-2 gap-4 text-sm text-gray-600">
                                                <div>
                                                    <span class="font-medium">Renter:</span> 
                                                    {{ booking.renter_name }}
                                                </div>
                                                <div>
                                                    <span class="font-medium">Return:</span> 
                                                    {{ formatDateTime(booking.expected_return) }}
                                                </div>
                                            </div>

                                            <!-- Overdue/Overcharge alerts -->
                                            <div v-if="booking.is_overdue" class="mt-2 flex items-center text-sm">
                                                <svg class="w-4 h-4 text-red-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                                </svg>
                                                <span class="text-red-600 font-medium">Vehicle is overdue!</span>
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
                                                <Link :href="route('owner.bookings.show', booking.id)" 
                                                      class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                                                    View Details
                                                </Link>
                                                <a v-if="booking.renter_phone" :href="`tel:${booking.renter_phone}`"
                                                   class="text-green-600 hover:text-green-700 text-sm font-medium">
                                                    Call Renter
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Bookings -->
                        <div class="bg-white rounded-lg shadow-md mt-8">
                            <div class="p-6 border-b border-gray-200">
                                <div class="flex items-center justify-between">
                                    <h2 class="text-xl font-semibold text-gray-900">Recent Bookings</h2>
                                    <Link :href="route('owner.bookings.index')" 
                                          class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                                        View All
                                    </Link>
                                </div>
                            </div>
                            
                            <div class="divide-y divide-gray-200">
                                <div v-for="booking in recentBookings.slice(0, 5)" :key="booking.id" 
                                     class="p-4 hover:bg-gray-50 transition-colors">
                                    <div class="flex items-center space-x-3">
                                        <img :src="booking.vehicle_image" :alt="booking.vehicle_name" 
                                             class="w-12 h-12 object-cover rounded" />
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between">
                                                <p class="text-sm font-medium text-gray-900 truncate">
                                                    {{ booking.vehicle_name }}
                                                </p>
                                                <span :class="getStatusClass(booking.status)"
                                                      class="px-2 py-1 rounded-full text-xs font-medium">
                                                    {{ formatStatus(booking.status) }}
                                                </span>
                                            </div>
                                            <p class="text-xs text-gray-500">{{ booking.renter_name }}</p>
                                            <p class="text-xs text-gray-500">₱{{ formatCurrency(booking.total_amount) }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Side Panel -->
                    <div class="space-y-6">
                        
                        <!-- Quick Actions -->
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                            <div class="space-y-3">
                                <Link :href="route('owner.vehicles.create')" 
                                      class="w-full bg-blue-600 text-white px-4 py-2 rounded-md text-sm hover:bg-blue-700 flex items-center justify-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Add New Vehicle
                                </Link>
                                <Link :href="route('owner.bookings.index')" 
                                      class="w-full bg-green-600 text-white px-4 py-2 rounded-md text-sm hover:bg-green-700 flex items-center justify-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                    Manage Bookings
                                </Link>
                                <Link :href="route('owner.vehicles.index')" 
                                      class="w-full bg-purple-600 text-white px-4 py-2 rounded-md text-sm hover:bg-purple-700 flex items-center justify-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H9m0 0H5m2 0v-2a2 2 0 012-2h2a2 2 0 012 2v2z"></path>
                                    </svg>
                                    My Vehicles
                                </Link>
                            </div>
                        </div>

                        <!-- Most Popular Vehicle -->
                        <div v-if="mostPopularVehicle" class="bg-white rounded-lg shadow-md p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Most Popular Vehicle</h3>
                            <div class="flex items-center space-x-3">
                                <img :src="mostPopularVehicle.image" :alt="mostPopularVehicle.name" 
                                     class="w-16 h-16 object-cover rounded-lg" />
                                <div>
                                    <p class="font-medium text-gray-900">{{ mostPopularVehicle.name }}</p>
                                    <p class="text-sm text-gray-600">{{ mostPopularVehicle.booking_count }} bookings</p>
                                    <Link :href="`/owner/vehicles/${mostPopularVehicle.id}`"
                                          class="text-blue-600 hover:text-blue-700 text-xs font-medium">
                                        View Details →
                                    </Link>
                                </div>
                            </div>
                        </div>

                        <!-- Vehicle Performance -->
                        <div class="bg-white rounded-lg shadow-md">
                            <div class="p-6 border-b border-gray-200">
                                <h3 class="text-lg font-semibold text-gray-900">Top Performing Vehicles</h3>
                            </div>
                            <div class="p-4 space-y-4">
                                <div v-for="vehicle in vehiclePerformance.slice(0, 3)" :key="vehicle.id" 
                                     class="flex items-center space-x-3">
                                    <img :src="vehicle.image" :alt="vehicle.name" 
                                         class="w-10 h-10 object-cover rounded" />
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">{{ vehicle.name }}</p>
                                        <p class="text-xs text-gray-500">{{ vehicle.total_bookings }} bookings</p>
                                        <p class="text-xs text-green-600 font-medium">₱{{ formatCurrency(vehicle.total_earnings) }}</p>
                                    </div>
                                </div>
                                <Link :href="route('owner.vehicles.index')" 
                                      class="block text-center text-blue-600 hover:text-blue-700 text-sm font-medium pt-2">
                                    View All Vehicles →
                                </Link>
                            </div>
                        </div>

                        <!-- Calendar Preview -->
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">Upcoming Bookings</h3>
                                <Link :href="route('owner.bookings.calendar')" 
                                      class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                                    View Calendar
                                </Link>
                            </div>
                            <div v-if="calendarEvents.length === 0" class="text-center text-gray-500 py-4">
                                <p class="text-sm">No upcoming bookings</p>
                            </div>
                            <div v-else class="space-y-2">
                                <div v-for="event in calendarEvents.slice(0, 3)" :key="event.id" 
                                     class="p-3 bg-gray-50 rounded-md">
                                    <p class="text-sm font-medium text-gray-900">{{ event.title }}</p>
                                    <p class="text-xs text-gray-600">{{ event.renter }}</p>
                                    <p class="text-xs text-gray-500">{{ formatDate(event.start) }}</p>
                                    <span :class="getStatusClass(event.status)"
                                          class="inline-block px-2 py-1 rounded-full text-xs font-medium mt-1">
                                        {{ formatStatus(event.status) }}
                                    </span>
                                </div>
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
import { Head, Link } from '@inertiajs/vue3';
defineProps({
    vehicleStats: Object,
    recentBookings: Array,
    activeBookings: Array,
    earningsStats: Object,
    bookingStats: Object,
    mostPopularVehicle: Object,
    calendarEvents: Array,
    vehiclePerformance: Array,
    vehicles: Array,
});

function formatCurrency(amount) {
    return parseFloat(amount || 0).toLocaleString('en-US', { 
        minimumFractionDigits: 2, 
        maximumFractionDigits: 2 
    });
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
</script>
