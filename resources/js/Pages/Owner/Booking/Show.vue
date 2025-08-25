<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Booking Management
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="mb-6">
                            <button
                                @click="goBack"
                                class="inline-flex items-center text-blue-600 hover:text-blue-800 font-semibold transition-colors"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5 mr-2"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M15 19l-7-7 7-7"
                                    />
                                </svg>
                                Back to Booking Requests
                            </button>
                        </div>

                        <div class="mb-6">
                            <h1 class="text-3xl font-bold text-gray-800">
                                Booking #{{ booking.id }}
                            </h1>
                            <div class="flex items-center space-x-4 mt-2">
                                <span
                                    :class="getStatusClass(booking.status)"
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
                                >
                                    {{ formatStatus(booking.status) }}
                                </span>
                                <span class="text-sm text-gray-500">
                                    Requested
                                    {{ formatDate(booking.created_at) }}
                                </span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                            <!-- Main Content -->
                            <div class="lg:col-span-2 space-y-6">
                                <!-- Customer Information -->
                                <div class="bg-gray-50 rounded-lg p-6">
                                    <h2 class="text-xl font-semibold mb-4">
                                        Customer Information
                                    </h2>
                                    <div class="flex items-start space-x-4">
                                        <div
                                            class="w-16 h-16 bg-blue-500 rounded-full flex items-center justify-center text-white text-xl font-semibold"
                                        >
                                            {{
                                                booking.user.first_name.charAt(
                                                    0
                                                )
                                            }}{{
                                                booking.user.last_name.charAt(0)
                                            }}
                                        </div>
                                        <div class="flex-1">
                                            <h3 class="text-lg font-semibold">
                                                {{ booking.user.first_name }}
                                                {{ booking.user.last_name }}
                                            </h3>
                                            <div
                                                class="space-y-1 text-sm text-gray-600"
                                            >
                                                <div>
                                                    <span class="font-medium"
                                                        >Email:</span
                                                    >
                                                    <a
                                                        :href="`mailto:${booking.user.email}`"
                                                        class="text-blue-600 hover:text-blue-800"
                                                    >
                                                        {{ booking.user.email }}
                                                    </a>
                                                </div>
                                                <div v-if="booking.user.phone">
                                                    <span class="font-medium"
                                                        >Phone:</span
                                                    >
                                                    <a
                                                        :href="`tel:${booking.user.phone}`"
                                                        class="text-blue-600 hover:text-blue-800"
                                                    >
                                                        {{ booking.user.phone }}
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Vehicle Information -->
                                <div class="bg-gray-50 rounded-lg p-6">
                                    <h2 class="text-xl font-semibold mb-4">
                                        Vehicle Information
                                    </h2>
                                    <div class="flex space-x-4">
                                        <img
                                            :src="
                                                booking.vehicle
                                                    .main_photo_url ||
                                                '/images/placeholder-vehicle.jpg'
                                            "
                                            :alt="`${booking.vehicle.brand?.name} ${booking.vehicle.type?.sub_type}`"
                                            class="w-32 h-32 object-cover rounded-lg"
                                        />
                                        <div class="flex-1">
                                            <h3 class="text-lg font-semibold">
                                                {{
                                                    booking.vehicle.brand?.name
                                                }}
                                                {{
                                                    booking.vehicle.type
                                                        ?.sub_type
                                                }}
                                            </h3>
                                            <p class="text-gray-600 mb-3">
                                                {{
                                                    booking.vehicle
                                                        .license_plate
                                                }}
                                            </p>
                                            <div
                                                class="grid grid-cols-2 gap-4 text-sm text-gray-600"
                                            >
                                                <div>
                                                    <span class="font-medium"
                                                        >Year:</span
                                                    >
                                                    {{ booking.vehicle.year }}
                                                </div>
                                                <div>
                                                    <span class="font-medium"
                                                        >Color:</span
                                                    >
                                                    {{ booking.vehicle.color }}
                                                </div>
                                                <div>
                                                    <span class="font-medium"
                                                        >Fuel:</span
                                                    >
                                                    {{
                                                        booking.vehicle
                                                            .fuel_type?.name
                                                    }}
                                                </div>
                                                <div>
                                                    <span class="font-medium"
                                                        >Location:</span
                                                    >
                                                    {{
                                                        booking.vehicle
                                                            .location_name
                                                    }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                                                        <h2 class="text-xl font-semibold mb-4">
                                        Location Map
                                    </h2>

                                    <div
                                        class="w-5xl h-56 bg-gray-200 rounded-lg overflow-hidden"
                                    >
                                        <iframe
                                            v-if="
                                                booking.vehicle.lat &&
                                                booking.vehicle.lng
                                            "
                                            :src="`https://maps.google.com/maps?q=${booking.vehicle.lat},${booking.vehicle.lng}&z=18&t=k&output=embed`"
                                            class="w-full h-full border-0"
                                            loading="lazy"
                                            allowfullscreen
                                        >
                                        </iframe>

                                        <div
                                            v-else
                                            class="flex items-center justify-center h-full text-gray-500"
                                        >
                                            <MapPin class="h-8 w-8 mr-2" />
                                            <span>Location not available</span>
                                        </div>
                                    </div>
                                </div>

<!-- Enhanced Booking Details with Overcharge Information -->
<div class="bg-gray-50 rounded-lg p-6">
    <h2 class="text-xl font-semibold mb-4">
        Booking Details
    </h2>
    <div class="grid grid-cols-2 gap-6">
        <div>
            <span class="text-sm text-gray-500">Duration</span>
            <div class="font-semibold">
                {{ booking.pricing_tier.duration_from }}
                {{ booking.pricing_tier.duration_from === 1
                    ? booking.pricing_tier.duration_unit.slice(0, -1)
                    : booking.pricing_tier.duration_unit
                }}
            </div>
        </div>
        <div>
            <span class="text-sm text-gray-500">Base Rental Fee</span>
            <div class="text-xl font-bold text-green-600">
                ₱{{ formatCurrency(booking.total_amount) }}
            </div>
        </div>
        <div>
            <span class="text-sm text-gray-500">Pickup Date & Time</span>
            <div class="font-semibold">
                {{ formatDateTime(booking.pickup_datetime) }}
            </div>
        </div>
        <div>
            <span class="text-sm text-gray-500">Estimated Return</span>
            <div class="font-semibold">
                {{ estimatedReturn }}
            </div>
        </div>
    </div>

    <!-- Total Amount Summary -->
    <div class="mt-6 pt-4 border-t border-gray-200">
        <div class="bg-white rounded-lg p-4">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            💰 Applied Overcharges
        </h3>
        <div class="space-y-3">
            <div v-for="overcharge in booking.overcharges" :key="overcharge.id" 
                 class="bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex justify-between items-start">
                    <div>
                        <div class="font-medium text-red-700">
                            {{ getOverchargeTypeName(overcharge.overcharge_type_id) }}
                        </div>
                        <div class="text-sm text-gray-600">
                            {{ formatOverchargeDetails(overcharge.details) }}
                        </div>
                        <div class="text-xs text-gray-500 mt-1">
                            Applied on {{ formatDateTime(overcharge.occurred_at) }}
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="font-bold text-red-600">₱{{ overcharge.amount }}</div>
                        <div :class="overcharge.is_paid ? 'text-green-600' : 'text-orange-600'" 
                             class="text-xs font-medium">
                            {{ overcharge.is_paid ? '✅ PAID' : '⏳ PENDING' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Overcharge Summary for Completed Bookings -->
        <div class="mt-4 bg-gray-100 rounded-lg p-4">
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div class="flex justify-between">
                    <span>Base Rental:</span>
                    <span class="font-semibold">₱{{ booking.total_amount }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Total Overcharges:</span>
                    <span class="font-semibold text-red-600">₱{{ calculateTotalOvercharges }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Paid Overcharges:</span>
                    <span class="text-green-600">₱{{ calculatePaidOvercharges }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Pending Collection:</span>
                    <span class="text-orange-600">₱{{ calculateUnpaidOvercharges }}</span>
                </div>
            </div>
            <div class="border-t mt-3 pt-3">
                <div class="flex justify-between font-bold text-lg">
                    <span>Grand Total:</span>
                    <span>₱{{ grandTotal }}</span>
                </div>
            </div>
        </div>
        </div>
    </div>
</div>

    <!-- Live Overcharge Tracking (for confirmed bookings) -->
    <div v-if="booking.status === 'confirmed'" class="mt-6 pt-6 border-t">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">
                ⚠️ Live Overcharge Monitoring
            </h3>
            <button 
                @click="refreshOverchargeStatus"
                class="text-blue-600 hover:text-blue-800 text-sm font-medium transition-colors"
            >
                🔄 Refresh Status
            </button>
        </div>

        <!-- Time Status -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div class="bg-white rounded-lg p-3 border">
                <div class="text-sm text-gray-500">Current Status</div>
                <div :class="timeRemaining.isOverdue ? 'text-red-600 font-bold' : 'text-green-600 font-semibold'">
                    {{ timeRemaining.text }}
                </div>
            </div>
            <div class="bg-white rounded-lg p-3 border">
                <div class="text-sm text-gray-500">Expected Return</div>
                <div class="font-semibold">
                    {{ formatDateTime(expectedReturnTime || estimatedReturn) }}
                </div>
            </div>
        </div>

        <!-- Potential Overcharges -->
        <div v-if="potentialOvercharges && potentialOvercharges.length > 0" class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <h4 class="font-semibold text-yellow-800 mb-3">
                🚨 Active Overcharges Detected
            </h4>
            <div class="space-y-3">
                <div v-for="overcharge in potentialOvercharges" :key="overcharge.overcharge_type_id" 
                     class="bg-white rounded p-3 border-l-4 border-yellow-400">
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="font-medium text-yellow-800">
                                {{ getOverchargeTypeName(overcharge.overcharge_type_id) }}
                            </div>
                            <div class="text-sm text-gray-600">
                                {{ formatOverchargeDetails(overcharge.details) }}
                            </div>
                            <div class="text-xs text-gray-500 mt-1">
                                {{ overcharge.overcharge_type_id === 1 ? formatTimeFromHours(overcharge.units) : `${overcharge.units} km` }} 
                                × ₱{{ overcharge.rate_applied }} per {{ overcharge.overcharge_type_id === 1 ? 'hour' : 'km' }}
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="font-bold text-yellow-700">₱{{ overcharge.amount }}</div>
                            <div class="text-xs text-yellow-600">Will be applied at completion</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Contact Renter Button -->
            <div class="mt-4 pt-3 border-t border-yellow-200">
                <button 
                    @click="contactRenter"
                    class="w-full bg-yellow-600 hover:bg-yellow-700 text-white font-medium py-2 px-4 rounded-lg transition-colors"
                >
                    📞 Contact Renter About Overcharges
                </button>
            </div>
        </div>

        <!-- Show manual overcharge detection when overdue but no automatic overcharges -->
        <div v-else-if="booking.status === 'confirmed' && timeRemaining.isOverdue" class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex items-start">
                <div class="text-red-600 mr-3">⚠️</div>
                <div class="flex-1">
                    <div class="font-semibold text-red-800">Vehicle is Overdue - Potential Overcharges</div>
                    <div class="text-sm text-red-600 mt-1">
                        This rental is {{ timeRemaining.text }}. Overcharges may apply when the booking is completed.
                    </div>
                    <div class="text-xs text-red-500 mt-2">
                        Automatic overcharge calculation will occur when the booking is marked as completed.
                        Contact the renter to discuss return or extension options.
                    </div>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-red-200">
                <button 
                    @click="contactRenter"
                    class="w-full bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition-colors"
                >
                    📞 Contact Renter - Vehicle Overdue
                </button>
            </div>
        </div>

        <!-- No Overcharges -->
        <div v-else-if="booking.status === 'confirmed'" class="bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="text-green-600 mr-3">✅</div>
                <div>
                    <div class="font-semibold text-green-800">No Overcharges Detected</div>
                    <div class="text-sm text-green-600">Rental is proceeding normally within guidelines</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Applied Overcharges (for completed bookings) -->
    <div v-if="booking.status === 'completed' && booking.overcharges && booking.overcharges.length > 0" 
         class="mt-6 pt-6 border-t">

    </div>

    <!-- No Overcharges for Completed Booking -->
    <div v-if="booking.status === 'completed' && (!booking.overcharges || booking.overcharges.length === 0)" 
         class="mt-6 pt-6 border-t">
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="text-green-600 mr-3">✅</div>
                <div>
                    <div class="font-semibold text-green-800">Clean Rental - No Overcharges</div>
                    <div class="text-sm text-green-600">
                        Rental completed without any violations. Total earned: ₱{{ formatCurrency(booking.total_amount) }}
                    </div>
                </div>
            </div>
            
            <!-- Debug Information -->
            <div class="mt-3 pt-3 border-t border-green-200 text-xs text-gray-500">
                <div>Expected Return: {{ expectedReturnTime || estimatedReturn }}</div>
                <div>Actual Return: {{ booking.actual_return_time ? formatDateTime(booking.actual_return_time) : 'Not set' }}</div>
                <div>Overcharges Array: {{ booking.overcharges ? booking.overcharges.length : 'null' }} items</div>
                <div>Has Overcharges Flag: {{ booking.has_overcharges ? 'true' : 'false' }}</div>
                <div>Total Overcharges Amount: ₱{{ formatCurrency(booking.total_overcharges || 0) }}</div>
            </div>
            
            <!-- Manual Overcharge Calculation -->
            <div class="mt-3 pt-3 border-t border-green-200">
                <button @click="recalculateOvercharges" 
                        :disabled="processing"
                        class="text-sm bg-yellow-600 hover:bg-yellow-700 disabled:bg-gray-400 text-white font-medium py-2 px-3 rounded transition-colors disabled:cursor-not-allowed">
                    🔄 Recalculate Overcharges
                </button>
                <div class="text-xs text-gray-500 mt-1">
                    Click if this booking should have overcharges but doesn't show any
                </div>
            </div>
            
            <!-- Manual Overcharge Addition -->
            <div class="mt-3 pt-3 border-t border-green-200">
                <button @click="showManualOverchargeForm = !showManualOverchargeForm" 
                        class="text-sm bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-3 rounded transition-colors">
                    ➕ Add Manual Overcharge
                </button>
                
                <!-- Manual Overcharge Form -->
                <div v-if="showManualOverchargeForm" class="mt-3 p-3 bg-red-50 border border-red-200 rounded">
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Type</label>
                            <select v-model="manualOvercharge.type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                <option value="1">Late Return</option>
                                <option value="2">Out of City Use</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Amount (₱)</label>
                            <input v-model="manualOvercharge.amount" type="number" step="0.01" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea v-model="manualOvercharge.details" 
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" rows="2"
                                      placeholder="Reason for this overcharge..."></textarea>
                        </div>
                        <div class="flex space-x-2">
                            <button @click="addManualOvercharge" 
                                    :disabled="processing || !manualOvercharge.amount"
                                    class="text-sm bg-red-600 hover:bg-red-700 disabled:bg-gray-400 text-white font-medium py-1 px-2 rounded transition-colors disabled:cursor-not-allowed">
                                Add Overcharge
                            </button>
                            <button @click="showManualOverchargeForm = false" 
                                    class="text-sm bg-gray-600 hover:bg-gray-700 text-white font-medium py-1 px-2 rounded transition-colors">
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

                                <!-- Add this section after the "Booking Details" section in Show.vue -->
<div v-if="booking.status === 'completed'" class="bg-green-50 border border-green-200 rounded-lg p-6">
    <h2 class="text-xl font-semibold mb-4 text-green-800">
        💰 Final Payment Breakdown
    </h2>
    
    <!-- Base Rental Charges -->
    <div class="bg-white rounded-lg p-4 mb-4">
        <h3 class="text-lg font-medium text-gray-800 mb-3 flex items-center">
            🎯 Base Rental Fee
        </h3>
        <div class="space-y-2 text-sm">
            <div class="flex justify-between">
                <span>Duration:</span>
                <span>{{ booking.pricing_tier.duration_from }} {{ booking.pricing_tier.duration_unit }}</span>
            </div>
            <div class="flex justify-between">
                <span>Rate:</span>
                <span>₱{{ booking.pricing_tier.price }}</span>
            </div>
            <div class="flex justify-between font-semibold text-green-600 border-t pt-2">
                <span>Base Total:</span>
                <span>₱{{ booking.total_amount }}</span>
            </div>
        </div>
    </div>

    <!-- Overcharge Fees (if any) -->
    <div v-if="booking.overcharges && booking.overcharges.length > 0" class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
        <h3 class="text-lg font-medium text-red-800 mb-3 flex items-center">
            ⚠️ Additional Overcharge Fees
        </h3>
        <div class="space-y-3">
            <div v-for="overcharge in booking.overcharges" :key="overcharge.id" class="bg-white rounded p-3">
                <div class="flex justify-between items-start">
                    <div>
                        <div class="font-medium text-red-700">
                            {{ getOverchargeTypeName(overcharge.overcharge_type_id) }}
                        </div>
                        <div class="text-sm text-gray-600">
                            {{ formatOverchargeDetails(overcharge.details) }}
                        </div>
                        <div class="text-xs text-gray-500 mt-1">
                            {{ overcharge.overcharge_type_id === 1 ? formatTimeFromHours(overcharge.units) : `${overcharge.units} km` }} 
                            × ₱{{ overcharge.rate_applied }}
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="font-semibold text-red-600">₱{{ overcharge.amount }}</div>
                        <div :class="overcharge.is_paid ? 'text-green-600' : 'text-orange-600'" class="text-xs font-medium">
                            {{ overcharge.is_paid ? '✅ PAID' : '⏳ PENDING' }}
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Overcharge Summary -->
            <div class="border-t pt-3">
                <div class="flex justify-between font-semibold text-red-600">
                    <span>Total Overcharges:</span>
                    <span>₱{{ calculateTotalOvercharges }}</span>
                </div>
                <div class="flex justify-between text-sm text-gray-600">
                    <span>Paid:</span>
                    <span class="text-green-600">₱{{ calculatePaidOvercharges }}</span>
                </div>
                <div class="flex justify-between text-sm text-gray-600">
                    <span>Pending:</span>
                    <span class="text-orange-600">₱{{ calculateUnpaidOvercharges }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Grand Total -->
    <div class="bg-gray-800 text-white rounded-lg p-4">
        <div class="flex justify-between items-center">
            <div>
                <h3 class="text-lg font-semibold">Total Amount Due</h3>
                <p class="text-sm text-gray-300">Base rental + overcharges</p>
            </div>
            <div class="text-right">
                <div class="text-2xl font-bold">₱{{ grandTotal }}</div>
                <div class="text-sm text-gray-300">
                    {{ allOverchargesPaid ? '✅ Fully Paid' : '⏳ Partial Payment' }}
                </div>
            </div>
        </div>
        
        <!-- Payment Actions for Overcharges -->
        <div v-if="unpaidOvercharges.length > 0" class="mt-4 pt-4 border-t border-gray-600">
            <h4 class="text-sm font-medium mb-2">Overcharge Collection Actions:</h4>
            <div class="space-y-2">
                <button
                    v-if="!overcharge.is_paid"
                    @click="markOverchargeAsPaid(overcharge.id)"
                    :disabled="processing"
                    class="text-xs bg-green-600 hover:bg-green-700 disabled:bg-gray-400 text-white font-medium py-1 px-2 rounded transition-colors disabled:cursor-not-allowed">
                    Mark as Paid
                </button>
            </div>
        </div>
    </div>

                                <div class="lg:col-span-1">
                                <div
                                    class="bg-white border rounded-lg p-6 sticky top-6"
                                >
                                    <h2 class="text-xl font-semibold mb-4">
                                        Actions
                                    </h2>

                                    <!-- Payment Status -->
                                    <div class="mb-6">
                                        <span class="text-sm text-gray-500"
                                            >Payment Status</span
                                        >
                                        <div class="mt-1">
                                            <span
                                                :class="
                                                    getPaymentStatusClass(
                                                        booking.payment?.paid_at
                                                    )
                                                "
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                            >
                                                {{
                                                    booking.payment?.paid_at
                                                        ? "Payment Confirmed"
                                                        : "Payment Pending"
                                                }}
                                            </span>
                                        </div>
                                        <div class="mt-2 text-sm text-gray-600">
                                            <span class="font-medium"
                                                >Method:</span
                                            >
                                            {{
                                                booking.payment?.payment_mode
                                                    ?.name || "N/A"
                                            }}
                                        </div>
                                        
                                        <!-- Overcharge Status (for completed bookings) -->
                                        <div v-if="booking.status === 'completed' && hasOvercharges" class="mt-3 pt-2 border-t">
                                            <span class="text-sm text-gray-500">Overcharge Status</span>
                                            <div class="mt-1">
                                                <span v-if="allOverchargesPaid"
                                                      class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    ✅ All Overcharges Paid
                                                </span>
                                                <span v-else
                                                      class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                                    ⏳ {{ unpaidOvercharges.length }} Pending Payment(s)
                                                </span>
                                            </div>
                                            <div class="mt-1 text-xs text-gray-500">
                                                Outstanding: ₱{{ formatCurrency(calculateUnpaidOvercharges) }}
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="space-y-3">
                                        <!-- Actions for pending bookings -->
                                        <div
                                            v-if="booking.status === 'pending'"
                                            class="space-y-3"
                                        >
                                            <button
                                                v-if="
                                                    booking.payment
                                                        ?.receipt_image &&
                                                    !booking.payment.paid_at
                                                "
                                                @click="confirmPayment"
                                                :disabled="processing"
                                                class="w-full py-2 px-4 bg-green-600 hover:bg-green-700 disabled:bg-gray-400 text-white font-semibold rounded-lg transition-colors disabled:cursor-not-allowed"
                                            >
                                                {{
                                                    processing
                                                        ? "Processing..."
                                                        : "Confirm Payment & Booking"
                                                }}
                                            </button>

                                            <button
                                                v-if="
                                                    booking.payment?.type ===
                                                        'cod' ||
                                                    booking.payment?.paid_at
                                                "
                                                @click="confirmBooking"
                                                :disabled="processing"
                                                class="w-full py-2 px-4 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white font-semibold rounded-lg transition-colors disabled:cursor-not-allowed"
                                            >
                                                {{
                                                    processing
                                                        ? "Processing..."
                                                        : "Confirm Booking"
                                                }}
                                            </button>

                                            <button
                                                @click="rejectBooking"
                                                :disabled="processing"
                                                class="w-full py-2 px-4 bg-red-600 hover:bg-red-700 disabled:bg-gray-400 text-white font-semibold rounded-lg transition-colors disabled:cursor-not-allowed"
                                            >
                                                {{
                                                    processing
                                                        ? "Processing..."
                                                        : "Reject Booking"
                                                }}
                                            </button>
                                        </div>

                                        <!-- Complete booking action -->
                                        <button
                                            v-if="
                                                booking.status === 'confirmed'
                                            "
                                            @click="completeBooking"
                                            :disabled="processing"
                                            class="w-full py-2 px-4 bg-purple-600 hover:bg-purple-700 disabled:bg-gray-400 text-white font-semibold rounded-lg transition-colors disabled:cursor-not-allowed"
                                        >
                                            {{
                                                processing
                                                    ? "Processing..."
                                                    : "Mark as Completed"
                                            }}
                                        </button>

                                        <!-- Status info for completed/cancelled -->
                                        <div
                                            v-if="
                                                booking.status === 'completed'
                                            "
                                            class="p-3 bg-green-50 border border-green-200 rounded"
                                        >
                                            <div
                                                class="text-green-800 font-medium text-sm"
                                            >
                                                Booking Completed
                                            </div>
                                            <div
                                                class="text-green-600 text-xs mt-1"
                                            >
                                                This booking has been marked as
                                                completed.
                                            </div>
                                        </div>

                                        <div
                                            v-if="
                                                booking.status === 'cancelled'
                                            "
                                            class="p-3 bg-red-50 border border-red-200 rounded"
                                        >
                                            <div
                                                class="text-red-800 font-medium text-sm"
                                            >
                                                Booking Cancelled
                                            </div>
                                            <div
                                                class="text-red-600 text-xs mt-1"
                                            >
                                                This booking has been cancelled.
                                            </div>
                                        </div>
                                    </div>

                                    <div class="border-t pt-4 mt-6">
                                        <button
                                            @click="goToVehicle"
                                            class="w-full py-2 px-4 border border-gray-300 text-gray-700 hover:bg-gray-50 font-semibold rounded-lg transition-colors"
                                        >
                                            View Vehicle
                                        </button>
                                    </div>

                                        </div>
                                    </div>
</div>

                                <!-- Payment Proof (if GCash) -->
                                <div
                                    v-if="booking.payment?.receipt_image"
                                    class="bg-yellow-50 border border-yellow-200 rounded-lg p-6"
                                >
                                    <h2
                                        class="text-xl font-semibold mb-4 text-yellow-800"
                                    >
                                        Payment Proof Submitted
                                    </h2>
                                    <div class="flex items-start space-x-4">
                                        <img
                                            :src="booking.payment.receipt_image"
                                            alt="Payment Receipt"
                                            class="w-48 h-48 object-cover rounded border cursor-pointer"
                                            @click="openReceiptModal"
                                        />
                                        <div class="flex-1">
                                            <p class="text-yellow-800 mb-3">
                                                Customer has uploaded payment
                                                proof. Please verify the payment
                                                before confirming the booking.
                                            </p>
                                            <div
                                                v-if="
                                                    booking.payment
                                                        .reference_number
                                                "
                                                class="text-sm text-yellow-700"
                                            >
                                                <span class="font-medium"
                                                    >Reference Number:</span
                                                >
                                                <span class="font-mono">{{
                                                    booking.payment
                                                        .reference_number
                                                }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Actions Sidebar -->

                                </div>
                            </div>
                        </div>
                    </div>


        <!-- Receipt Modal -->
        <div
            v-if="showReceiptModal"
            @click.self="closeReceiptModal"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
        >
            <div
                class="bg-white rounded-lg max-w-3xl w-full max-h-[90vh] overflow-auto"
            >
                <div class="p-4 border-b flex justify-between items-center">
                    <h3 class="text-lg font-semibold">Payment Receipt</h3>
                    <button
                        @click="closeReceiptModal"
                        class="text-gray-400 hover:text-gray-600"
                    >
                        <svg
                            class="w-6 h-6"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"
                            ></path>
                        </svg>
                    </button>
                </div>
                <div class="p-4 text-center">
                    <img
                        :src="booking.payment?.receipt_image"
                        alt="Payment Receipt"
                        class="max-w-full h-auto mx-auto rounded border"
                    />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { computed, ref } from "vue";
import { router } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

const props = defineProps({
    booking: Object,
    expectedReturnTime: String,
    potentialOvercharges: Array,
    recentOvercharges: Array,
    stats: Object,
});



const processing = ref(false);
const showReceiptModal = ref(false);
const showManualOverchargeForm = ref(false);
const manualOvercharge = ref({
    type: '1',
    amount: '',
    details: ''
});

const estimatedReturn = computed(() => {
    const pickup = new Date(props.booking.pickup_datetime);
    const tier = props.booking.pricing_tier;

    let returnDate = new Date(pickup);

    switch (tier.duration_unit) {
        case "minutes":
            returnDate.setMinutes(returnDate.getMinutes() + tier.duration_from);
            break;
        case "hours":
            returnDate.setHours(returnDate.getHours() + tier.duration_from);
            break;
        case "days":
            returnDate.setDate(returnDate.getDate() + tier.duration_from);
            break;
    }

    return returnDate.toLocaleString("en-US", {
        weekday: "short",
        month: "short",
        day: "numeric",
        year: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
});

// Live tracking methods
const timeRemaining = computed(() => {
    const now = new Date();
    const expected = new Date(props.expectedReturnTime || estimatedReturn.value);
    const diffMs = expected.getTime() - now.getTime();
    const diffHours = Math.floor(diffMs / (1000 * 60 * 60));
    const diffMinutes = Math.floor((diffMs % (1000 * 60 * 60)) / (1000 * 60));
    
    if (diffMs <= 0) {
        const overdueHours = Math.floor(Math.abs(diffMs) / (1000 * 60 * 60));
        const overdueMinutes = Math.floor((Math.abs(diffMs) % (1000 * 60 * 60)) / (1000 * 60));
        
        return {
            isOverdue: true,
            text: overdueHours > 0 
                ? `${overdueHours}h ${overdueMinutes}m overdue` 
                : `${overdueMinutes}m overdue`
        };
    }
    
    return {
        isOverdue: false,
        text: diffHours > 0 
            ? `${diffHours}h ${diffMinutes}m remaining` 
            : `${diffMinutes}m remaining`
    };
});

function getStatusClass(status) {
    const classes = {
        pending: "bg-yellow-100 text-yellow-800",
        confirmed: "bg-blue-100 text-blue-800",
        completed: "bg-green-100 text-green-800",
        cancelled: "bg-red-100 text-red-800",
    };
    return classes[status] || "bg-gray-100 text-gray-800";
}

function getPaymentStatusClass(paidAt) {
    return paidAt
        ? "bg-green-100 text-green-800"
        : "bg-yellow-100 text-yellow-800";
}

function formatStatus(status) {
    const statusMap = {
        pending: "Pending",
        confirmed: "Confirmed",
        completed: "Completed",
        cancelled: "Cancelled",
    };
    return statusMap[status] || status;
}

function formatDateTime(datetime) {
    return new Date(datetime).toLocaleString("en-US", {
        weekday: "long",
        year: "numeric",
        month: "long",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
}

function formatDate(date) {
    return new Date(date).toLocaleDateString("en-US", {
        year: "numeric",
        month: "long",
        day: "numeric",
    });
}

function formatCurrency(amount) {
  if (amount === null || amount === undefined || isNaN(amount)) return '0.00';
  return parseFloat(amount).toFixed(2);
}

function formatTimeFromHours(hours) {
  if (!hours || hours <= 0) return '0 minutes';
  
  if (hours < 1) {
    // Less than 1 hour, show in minutes
    const minutes = Math.round(hours * 60);
    return minutes === 1 ? '1 minute' : `${minutes} minutes`;
  } else {
    // 1 hour or more
    const wholeHours = Math.floor(hours);
    const remainingMinutes = Math.round((hours - wholeHours) * 60);
    
    const hourText = wholeHours === 1 ? '1 hour' : `${wholeHours} hours`;
    
    if (remainingMinutes > 0) {
      const minuteText = remainingMinutes === 1 ? '1 minute' : `${remainingMinutes} minutes`;
      return `${hourText} ${minuteText}`;
    } else {
      return hourText;
    }
  }
}

function formatOverchargeDetails(details) {
  // Fix old overcharge details that might have decimal hours
  // Match patterns like "Late return by 0.42222222222222 hours"
  return details.replace(/Late return by ([\d.]+) hours/g, (match, hours) => {
    const hoursNumber = parseFloat(hours);
    const formattedTime = formatTimeFromHours(hoursNumber);
    return `Late return by ${formattedTime}`;
  });
}

function openReceiptModal() {
    showReceiptModal.value = true;
}

function closeReceiptModal() {
    showReceiptModal.value = false;
}

function goBack() {
    router.visit(route("owner.bookings.index"));
}

function goToVehicle() {
    router.visit(route("vehicles.show", props.booking.vehicle.id));
}

function confirmPayment() {
    processing.value = true;
    router.post(
        route("owner.bookings.confirmPayment", props.booking.id),
        {},
        {
            onFinish: () => {
                processing.value = false;
            },
        }
    );
}

function confirmBooking() {
    processing.value = true;
    router.post(
        route("owner.bookings.confirm", props.booking.id),
        {},
        {
            onFinish: () => {
                processing.value = false;
            },
        }
    );
}

function rejectBooking() {
    if (
        confirm(
            "Are you sure you want to reject this booking? This action cannot be undone."
        )
    ) {
        processing.value = true;
        router.post(
            route("owner.bookings.reject", props.booking.id),
            {},
            {
                onFinish: () => {
                    processing.value = false;
                },
            }
        );
    }
}


function completeBooking() {
    if (confirm('Are you sure you want to mark this booking as completed? This will calculate any applicable overcharges.')) {
        processing.value = true;
        router.post(
            route("owner.bookings.complete", props.booking.id),
            {},
            {
                onFinish: () => {
                    processing.value = false;
                },
                onSuccess: () => {
                    // Reload to show updated overcharge data
                    router.reload();
                }
            }
        );
    }
}







const allOverchargesPaid = computed(() => {
    if (!props.booking.overcharges || props.booking.overcharges.length === 0) return true;
    return props.booking.overcharges.every(o => o.is_paid);
});

const unpaidOvercharges = computed(() => {
    if (!props.booking.overcharges) return [];
    return props.booking.overcharges.filter(o => !o.is_paid);
});

// Add this method to mark overcharges as paid
function markOverchargeAsPaid(overchargeId) {
    if (confirm('Mark this overcharge as paid?')) {
        processing.value = true;
        router.post(route('owner.overcharges.markAsPaid', overchargeId), {}, {
            onFinish: () => {
                processing.value = false;
            },
            onSuccess: () => {
                // Refresh the booking data
                router.reload({ only: ['booking'] });
            }
        });
    }
}


const calculateTotalOvercharges = computed(() => {
    if (!props.booking.overcharges || props.booking.overcharges.length === 0) return 0;
    return props.booking.overcharges.reduce((sum, overcharge) => {
        return sum + parseFloat(overcharge.amount || 0);
    }, 0);
});

const calculatePaidOvercharges = computed(() => {
    if (!props.booking.overcharges || props.booking.overcharges.length === 0) return 0;
    return props.booking.overcharges
        .filter(o => o.is_paid)
        .reduce((sum, overcharge) => {
            return sum + parseFloat(overcharge.amount || 0);
        }, 0);
});

const calculateUnpaidOvercharges = computed(() => {
    if (!props.booking.overcharges || props.booking.overcharges.length === 0) return 0;
    return props.booking.overcharges
        .filter(o => !o.is_paid)
        .reduce((sum, overcharge) => {
            return sum + parseFloat(overcharge.amount || 0);
        }, 0);
});

const grandTotal = computed(() => {
    const baseAmount = parseFloat(props.booking.total_amount || 0);
    const overchargeAmount = calculateTotalOvercharges.value;
    return baseAmount + overchargeAmount;
});

const hasOvercharges = computed(() => {
    return props.booking.overcharges && props.booking.overcharges.length > 0;
});

// Add the missing methods
function getOverchargeTypeName(typeId) {
    const types = {
        1: '⏰ Late Return',
        2: '🏙️ Out of City Use'
    };
    return types[typeId] || 'Unknown Overcharge';
}

function refreshOverchargeStatus() {
    router.reload({
        only: ['booking', 'expectedReturnTime', 'potentialOvercharges']
    });
}

function recalculateOvercharges() {
    if (confirm('This will recalculate overcharges for this completed booking. Continue?')) {
        processing.value = true;
        router.post(route('overcharges.calculate', props.booking.id), {}, {
            onFinish: () => {
                processing.value = false;
            },
            onSuccess: () => {
                // Reload the page to show updated overcharge data
                router.reload();
            }
        });
    }
}

function addManualOvercharge() {
    if (!manualOvercharge.value.amount || parseFloat(manualOvercharge.value.amount) <= 0) {
        alert('Please enter a valid amount');
        return;
    }

    if (confirm(`Add ₱${manualOvercharge.value.amount} overcharge to this booking?`)) {
        processing.value = true;
        
        const data = {
            overcharge_type_id: parseInt(manualOvercharge.value.type),
            amount: parseFloat(manualOvercharge.value.amount),
            details: manualOvercharge.value.details || 'Manual overcharge added by owner',
            units: 1,
            rate_applied: parseFloat(manualOvercharge.value.amount),
            is_paid: false,
            occurred_at: new Date().toISOString()
        };

        router.post(route('owner.overcharges.add', props.booking.id), data, {
            onFinish: () => {
                processing.value = false;
            },
            onSuccess: () => {
                // Reset form and hide it
                manualOvercharge.value = { type: '1', amount: '', details: '' };
                showManualOverchargeForm.value = false;
                // Reload the page to show updated overcharge data
                router.reload();
            }
        });
    }
}

function contactRenter() {
    const phone = props.booking.user.phone;
    const email = props.booking.user.email;
    
    if (phone) {
        window.open(`tel:${phone}`, '_self');
    } else if (email) {
        const subject = encodeURIComponent(`Regarding Your Booking #${props.booking.id} - Overcharge Notice`);
        const body = encodeURIComponent(`Hello ${props.booking.user.first_name},\n\nThis is regarding your current vehicle rental booking (#${props.booking.id}).\n\nWe've detected some potential overcharges that may apply to your rental. Please contact us to discuss.\n\nBest regards,\nYour Vehicle Owner`);
        window.open(`mailto:${email}?subject=${subject}&body=${body}`, '_self');
    } else {
        alert('No contact information available for this renter.');
    }
}
</script>

<style scoped>
/* Add any component-specific styles here */
</style>
