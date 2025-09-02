<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <!-- Welcome Header -->
                <div class="glass-card p-8 mb-8 shadow-glow">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-3xl font-bold text-white mb-2">Welcome back!</h1>
                            <p class="text-white/80 text-lg">Here's what's happening with your bookings</p>
                        </div>
                        <div class="text-right">
                            <div class="text-3xl font-bold text-white">{{ bookingStats.total_bookings }}</div>
                            <div class="text-sm text-white/70 uppercase tracking-wider">Total Bookings</div>
                        </div>
                    </div>
                </div>

                <!-- KYC Notification -->
                <div v-if="kycNotification" class="mb-8">
                    <div :class="[
                        'glass-card-dark p-6 border-l-4 shadow-glow border border-white/20',
                        kycNotification.type === 'success' ? 'border-l-green-400' :
                        kycNotification.type === 'warning' ? 'border-l-yellow-400' :
                        kycNotification.type === 'error' ? 'border-l-red-400' :
                        'border-l-blue-400'
                    ]">
                        <div class="flex items-start justify-between">
                            <div class="flex space-x-4">
                                <div :class="[
                                    'flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center backdrop-blur-sm border',
                                    kycNotification.type === 'success' ? 'bg-green-400/20 border-green-400/30' :
                                    kycNotification.type === 'warning' ? 'bg-yellow-400/20 border-yellow-400/30' :
                                    kycNotification.type === 'error' ? 'bg-red-400/20 border-red-400/30' :
                                    'bg-blue-400/20 border-blue-400/30'
                                ]">
                                    <svg v-if="kycNotification.icon === 'check'" class="w-6 h-6 text-green-300" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    <svg v-else-if="kycNotification.icon === 'warning'" class="w-6 h-6 text-yellow-300" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                    <svg v-else-if="kycNotification.icon === 'x'" class="w-6 h-6 text-red-300" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                    </svg>
                                    <svg v-else class="w-6 h-6 text-blue-300" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 :class="[
                                        'text-lg font-semibold',
                                        kycNotification.type === 'success' ? 'text-green-300' :
                                        kycNotification.type === 'warning' ? 'text-yellow-300' :
                                        kycNotification.type === 'error' ? 'text-red-300' :
                                        'text-blue-300'
                                    ]">
                                        {{ kycNotification.title }}
                                    </h3>
                                    <p :class="[
                                        'mt-1 text-base',
                                        kycNotification.type === 'success' ? 'text-green-200' :
                                        kycNotification.type === 'warning' ? 'text-yellow-200' :
                                        kycNotification.type === 'error' ? 'text-red-200' :
                                        'text-blue-200'
                                    ]">
                                        {{ kycNotification.message }}
                                    </p>
                                    <div v-if="kycNotification.reason" class="mt-2 p-3 bg-red-500/20 rounded text-sm text-red-200 border border-red-400/30">
                                        <strong>Reason:</strong> {{ kycNotification.reason }}
                                    </div>
                                </div>
                            </div>
                            <div class="ml-4">
                                <Link :href="kycNotification.url" :class="[
                                    'px-6 py-3 rounded-md text-sm font-semibold text-white shadow-lg transition-all duration-200 hover:shadow-xl transform hover:scale-105',
                                    kycNotification.type === 'success' ? 'bg-green-600 hover:bg-green-700' :
                                    kycNotification.type === 'warning' ? 'bg-yellow-600 hover:bg-yellow-700' :
                                    kycNotification.type === 'error' ? 'bg-red-600 hover:bg-red-700' :
                                    'bg-blue-600 hover:bg-blue-700'
                                ]">
                                    {{ kycNotification.action }}
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Active Rental Alert -->
                <div v-if="activeBooking" class="mb-8">
                    <div class="glass-card-dark border-l-4 border-green-400 p-6 shadow-glow">
                        <div class="flex items-start justify-between">
                            <div class="flex space-x-4">
                                <img :src="activeBooking.vehicle_image" :alt="activeBooking.vehicle_name" 
                                     class="w-20 h-20 object-cover rounded-lg border border-white/20" />
                                <div>
                                    <h3 class="text-xl font-semibold text-white">🚗 Currently Renting</h3>
                                    <p class="text-lg font-medium text-green-400">{{ activeBooking.vehicle_name }}</p>
                                    <p class="text-sm text-white/70">Owner: {{ activeBooking.owner_name }}</p>
                                    <p class="text-sm text-white/70">
                                        Expected return: {{ formatDateTime(activeBooking.expected_return) }}
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <div :class="[
                                    'text-lg font-bold px-3 py-1 rounded-full backdrop-blur-sm',
                                    activeBooking.time_remaining.overdue 
                                        ? 'bg-red-400/20 text-red-300 border border-red-400/30' 
                                        : 'bg-green-400/20 text-green-300 border border-green-400/30'
                                ]">
                                    {{ activeBooking.time_remaining.text }}
                                </div>
                                <div class="mt-3 space-x-2">
                                    <Link :href="`/bookings/${activeBooking.id}`" 
                                          class="bg-blue-500/80 hover:bg-blue-500 text-white px-4 py-2 rounded-md text-sm transition-all duration-200 backdrop-blur-sm border border-blue-400/30">
                                        View Details
                                    </Link>
                                    <a v-if="activeBooking.owner_phone" :href="`tel:${activeBooking.owner_phone}`"
                                       class="bg-green-500/80 hover:bg-green-500 text-white px-4 py-2 rounded-md text-sm transition-all duration-200 backdrop-blur-sm border border-green-400/30">
                                        Call Owner
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <div class="glass-card-dark p-6 text-center shadow-glow">
                        <div class="text-3xl font-bold text-blue-400">{{ bookingStats.completed_bookings }}</div>
                        <div class="text-sm text-white/70">Completed Trips</div>
                    </div>
                    <div class="glass-card-dark p-6 text-center shadow-glow">
                        <div class="text-3xl font-bold text-green-400">₱{{ formatCurrency(bookingStats.total_spent) }}</div>
                        <div class="text-sm text-white/70">Total Spent</div>
                    </div>
                    <div class="glass-card-dark p-6 text-center shadow-glow">
                        <div class="text-3xl font-bold text-yellow-700">{{ bookingStats.cancelled_bookings }}</div>
                        <div class="text-sm text-white/70">Cancelled</div>
                    </div>
                    <div class="glass-card-dark p-6 text-center shadow-glow">
                        <div :class="[
                            'text-3xl font-bold',
                            bookingStats.unpaid_overcharges > 0 ? 'text-red-400' : 'text-gray-400'
                        ]">
                            ₱{{ formatCurrency(bookingStats.unpaid_overcharges) }}
                        </div>
                        <div class="text-sm text-white/70">Unpaid Overcharges</div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    
                    <!-- Recent Bookings -->
                    <div class="lg:col-span-2">
                        <div class="glass-card-dark shadow-glow">
                            <div class="p-6 border-b border-white/20">
                                <div class="flex items-center justify-between">
                                    <h2 class="text-xl font-semibold text-white">Recent Bookings</h2>
                                    <Link href="/bookings" class="text-blue-400 hover:text-blue-300 text-sm font-medium transition-colors duration-200">
                                        View All
                                    </Link>
                                </div>
                            </div>
                            
                            <div v-if="!recentBookings || recentBookings.length === 0" 
                                 class="p-12 text-center">
                                <div class="w-16 h-16 mx-auto mb-4 bg-white/10 rounded-full flex items-center justify-center">
                                    <svg class="w-8 h-8 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-medium text-white mb-2">No bookings yet</h3>
                                <p class="text-white/70 mb-6">Start your journey by booking your first vehicle</p>
                            </div>

                            <div v-else class="divide-y divide-white/10">
                                <div v-for="booking in recentBookings" :key="booking.id" 
                                     class="p-6 hover:bg-white/5 transition-colors">
                                    <div class="flex items-start space-x-4">
                                        <img :src="booking.vehicle_image" :alt="booking.vehicle_name" 
                                             class="w-16 h-16 object-cover rounded-lg border border-white/20" />
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between">
                                                <h3 class="text-lg font-medium text-white truncate">
                                                    {{ booking.vehicle_name }}
                                                </h3>
                                                <span :class="getStatusClass(booking.status)"
                                                      class="px-2 py-1 rounded-full text-xs font-medium">
                                                    {{ formatStatus(booking.status) }}
                                                </span>
                                            </div>
                                            
                                            <div class="mt-2 grid grid-cols-2 gap-4 text-sm text-white/70">
                                                <div>
                                                    <span class="font-medium text-white/90">Pickup:</span> 
                                                    {{ formatDate(booking.pickup_datetime) }}
                                                </div>
                                                <div>
                                                    <span class="font-medium text-white/90">Return:</span> 
                                                    {{ formatDate(booking.expected_return) }}
                                                </div>
                                                <div>
                                                    <span class="font-medium text-white/90">Owner:</span> 
                                                    {{ booking.owner_name }}
                                                </div>
                                                <div>
                                                    <span class="font-medium text-white/90">Amount:</span> 
                                                    <span class="text-green-400">₱{{ formatCurrency(booking.total_amount) }}</span>
                                                </div>
                                            </div>

                                            <!-- Extension Request Status -->
                                            <div v-if="booking.extension_requests && booking.extension_requests.length > 0" class="mt-2">
                                                <div v-for="request in booking.extension_requests.slice(0, 1)" :key="request.id" 
                                                     :class="{
                                                         'text-yellow-600': request.status === 'pending',
                                                         'text-green-600': request.status === 'approved',
                                                         'text-red-600': request.status === 'rejected'
                                                     }" 
                                                     class="flex items-center text-sm">
                                                    <svg v-if="request.status === 'pending'" class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                                    </svg>
                                                    <svg v-else-if="request.status === 'approved'" class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                    </svg>
                                                    <svg v-else class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                                    </svg>
                                                    <span class="font-medium">
                                                        Extension {{ request.status === 'pending' ? 'pending' : (request.status === 'approved' ? 'approved' : 'rejected') }}
                                                        ({{ request.requested_hours }}h)
                                                    </span>
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
                        <div class="glass-card-dark shadow-glow p-6">
                            <h3 class="text-lg font-semibold text-white mb-4">Quick Actions</h3>
                            <div class="space-y-3">
                                <Link href="/vehicles" 
                                      class="w-full bg-blue-600 text-white px-4 py-2 rounded-md text-sm hover:bg-blue-700 flex items-center justify-center transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                    Browse Vehicles
                                </Link>
                                <Link href="/bookings" 
                                      class="w-full bg-white/10 text-white px-4 py-2 rounded-md text-sm hover:bg-white/20 flex items-center justify-center transition-colors border border-white/20">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                    My Bookings
                                </Link>
                                <Link href="/profile" 
                                      class="w-full bg-green-600 text-white px-4 py-2 rounded-md text-sm hover:bg-green-700 flex items-center justify-center transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    Update Profile
                                </Link>
                            </div>
                        </div>

                        <!-- Featured Vehicles -->
                        <div class="glass-card-dark shadow-glow">
                            <div class="p-6 border-b border-white/20">
                                <h3 class="text-lg font-semibold text-white">Featured Vehicles</h3>
                            </div>
                            <div class="p-4 space-y-4">
                                <div v-for="vehicle in featuredVehicles.slice(0, 3)" :key="vehicle.id" 
                                     class="flex items-center space-x-3 p-2 rounded-lg hover:bg-white/5 transition-colors">
                                    <img :src="vehicle.image" :alt="vehicle.name" 
                                         class="w-12 h-12 object-cover rounded border border-white/20" />
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-white truncate">{{ vehicle.name }}</p>
                                        <p class="text-xs text-white/70">{{ vehicle.location }}</p>
                                        <p class="text-xs text-blue-400">From ₱{{ formatCurrency(vehicle.price_from) }}</p>
                                    </div>
                                </div>
                                <Link href="/vehicles" 
                                      class="block text-center text-blue-400 hover:text-blue-300 text-sm font-medium pt-2 transition-colors">
                                    View All Vehicles →
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rating Prompt Modal -->
        <RatingPrompt
            :show="showRatingPrompt"
            :booking="bookingToRate"
            @close="closeRatingPrompt"
            @rated="handleRated"
        />
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import RatingPrompt from '@/Components/Rating/RatingPrompt.vue';

defineProps({
    recentBookings: Array,
    activeBooking: Object,
    bookingStats: Object,
    featuredVehicles: Array,
    kycNotification: Object,
});

const showRatingPrompt = ref(false);
const bookingToRate = ref(null);

// Check for rating prompts on page load
onMounted(async () => {
    try {
        const response = await fetch('/api/ratings/prompt');
        const data = await response.json();
        
        if (data.should_prompt && data.booking) {
            bookingToRate.value = data.booking;
            showRatingPrompt.value = true;
        }
    } catch (error) {
        console.log('Rating prompt check failed:', error);
    }
});

const closeRatingPrompt = () => {
    showRatingPrompt.value = false;
    bookingToRate.value = null;
};

const handleRated = () => {
    // Optionally refresh the page or show a success message
    closeRatingPrompt();
};

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
        'pending': 'bg-yellow-100 text-yellow-200',
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
