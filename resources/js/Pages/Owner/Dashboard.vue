<template>
    <Head title="Owner Dashboard" />

    <AuthenticatedLayout>
        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Welcome Header -->
                <div class="glass-card-dark p-8 mb-8 shadow-glow">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-3xl font-bold text-white">
                                Owner Dashboard
                            </h1>
                            <p class="text-white/80 mt-1">
                                Monitor your vehicles and track your earnings
                            </p>
                        </div>
                        <div class="text-right">
                            <div class="text-2xl font-bold text-white">
                                {{ vehicleStats.total_vehicles }}
                            </div>
                            <div class="text-sm text-white/70">
                                Total Vehicles
                            </div>
                        </div>
                    </div>
                </div>

                <!-- KYC Notification -->
                <div v-if="kycNotification" class="mb-8">
                    <div
                        :class="[
                            'glass-card-dark p-6 border-l-4 shadow-glow',
                            kycNotification.type === 'success'
                                ? 'border-green-400'
                                : kycNotification.type === 'warning'
                                ? 'border-yellow-400'
                                : kycNotification.type === 'error'
                                ? 'border-red-400'
                                : 'border-blue-400',
                        ]"
                    >
                        <div class="flex items-start justify-between">
                            <div class="flex space-x-3">
                                <div
                                    :class="[
                                        'flex-shrink-0 w-6 h-6 rounded-full flex items-center justify-center backdrop-blur-sm',
                                        kycNotification.type === 'success'
                                            ? 'bg-green-400/20'
                                            : kycNotification.type === 'warning'
                                            ? 'bg-yellow-400/20'
                                            : kycNotification.type === 'error'
                                            ? 'bg-red-400/20'
                                            : 'bg-blue-400/20',
                                    ]"
                                >
                                    <svg
                                        v-if="kycNotification.icon === 'check'"
                                        class="w-4 h-4 text-green-600"
                                        fill="currentColor"
                                        viewBox="0 0 20 20"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd"
                                        ></path>
                                    </svg>
                                    <svg
                                        v-else-if="
                                            kycNotification.icon === 'warning'
                                        "
                                        class="w-4 h-4 text-yellow-600"
                                        fill="currentColor"
                                        viewBox="0 0 20 20"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                            clip-rule="evenodd"
                                        ></path>
                                    </svg>
                                    <svg
                                        v-else-if="kycNotification.icon === 'x'"
                                        class="w-4 h-4 text-red-600"
                                        fill="currentColor"
                                        viewBox="0 0 20 20"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                            clip-rule="evenodd"
                                        ></path>
                                    </svg>
                                    <svg
                                        v-else
                                        class="w-4 h-4 text-blue-600"
                                        fill="currentColor"
                                        viewBox="0 0 20 20"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                            clip-rule="evenodd"
                                        ></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3
                                        :class="[
                                            'text-lg font-semibold text-white',
                                            kycNotification.type === 'success'
                                                ? 'text-green-200'
                                                : kycNotification.type ===
                                                  'warning'
                                                ? 'text-yellow-200'
                                                : kycNotification.type ===
                                                  'error'
                                                ? 'text-red-200'
                                                : 'text-blue-200',
                                        ]"
                                    >
                                        {{ kycNotification.title }}
                                    </h3>
                                    <p
                                        :class="[
                                            'mt-1 text-white/80',
                                            kycNotification.type === 'success'
                                                ? 'text-green-100'
                                                : kycNotification.type ===
                                                  'warning'
                                                ? 'text-yellow-100'
                                                : kycNotification.type ===
                                                  'error'
                                                ? 'text-red-100'
                                                : 'text-blue-100',
                                        ]"
                                    >
                                        {{ kycNotification.message }}
                                    </p>
                                    <div
                                        v-if="kycNotification.reason"
                                        class="mt-2 p-2 bg-red-400/20 backdrop-blur-sm rounded text-sm text-red-200"
                                    >
                                        <strong>Reason:</strong>
                                        {{ kycNotification.reason }}
                                    </div>
                                </div>
                            </div>
                            <div class="ml-4">
                                <Link
                                    :href="kycNotification.url"
                                    :class="[
                                        'px-4 py-2 rounded-md text-sm font-medium text-white bg-white/20 hover:bg-white/30 backdrop-blur-sm border border-white/30 transition-all duration-200 hover:scale-105',
                                        kycNotification.type === 'success'
                                            ? 'border-green-400/50'
                                            : kycNotification.type === 'warning'
                                            ? 'border-yellow-400/50'
                                            : kycNotification.type === 'error'
                                            ? 'border-red-400/50'
                                            : 'border-blue-400/50',
                                    ]"
                                >
                                    {{ kycNotification.action }}
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Vehicle Statistics -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8">
                    <div class="glass-card-dark p-6 text-center shadow-glow">
                        <div class="text-3xl font-bold text-green-400">
                            {{ vehicleStats.available_vehicles }}
                        </div>
                        <div class="text-sm text-white/70">Available</div>
                    </div>
                    <div class="glass-card-dark p-6 text-center shadow-glow">
                        <div class="text-3xl font-bold text-blue-400">
                            {{ vehicleStats.currently_booked }}
                        </div>
                        <div class="text-sm text-white/70">
                            Currently Booked
                        </div>
                    </div>
                    <div class="glass-card-dark p-6 text-center shadow-glow">
                        <div class="text-3xl font-bold text-gray-300">
                            {{ vehicleStats.unavailable_vehicles }}
                        </div>
                        <div class="text-sm text-white/70">Unavailable</div>
                    </div>
                    <div class="glass-card-dark p-6 text-center shadow-glow">
                        <div class="text-3xl font-bold text-purple-400">
                            {{ bookingStats.pending_bookings }}
                        </div>
                        <div class="text-sm text-white/70">
                            Pending Requests
                        </div>
                    </div>
                </div>

                <!-- Earnings Overview -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8">
                    <div class="glass-card-dark p-6 text-center shadow-glow">
                        <div class="text-3xl font-bold text-green-400">
                            ₱{{ formatCurrency(earningsStats.total_earnings) }}
                        </div>
                        <div class="text-sm text-white/70">Total Earnings</div>
                    </div>
                    <div class="glass-card-dark p-6 text-center shadow-glow">
                        <div class="text-3xl font-bold text-blue-400">
                            ₱{{
                                formatCurrency(
                                    earningsStats.this_month_earnings
                                )
                            }}
                        </div>
                        <div class="text-sm text-white/70">This Month</div>
                    </div>
                    <div class="glass-card-dark p-6 text-center shadow-glow">
                        <div class="text-3xl font-bold text-orange-400">
                            ₱{{
                                formatCurrency(earningsStats.pending_payments)
                            }}
                        </div>
                        <div class="text-sm text-white/70">
                            Pending Payments
                        </div>
                    </div>
                    <div class="glass-card-dark p-6 text-center shadow-glow">
                        <div class="text-3xl font-bold text-red-400">
                            ₱{{
                                formatCurrency(
                                    earningsStats.overcharge_earnings
                                )
                            }}
                        </div>
                        <div class="text-sm text-white/70">
                            Overcharge Earnings
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Active Bookings -->
                    <div class="lg:col-span-2">
                        <div class="glass-card-dark shadow-glow">
                            <div class="p-6 border-b border-white/20">
                                <div class="flex items-center justify-between">
                                    <h2
                                        class="text-xl font-semibold text-white"
                                    >
                                        Currently Active Rentals
                                    </h2>
                                    <Link
                                        v-if="!needsKycVerification"
                                        :href="route('owner.bookings.index')"
                                        class="text-blue-400 hover:text-blue-300 text-sm font-medium transition-colors duration-200"
                                    >
                                        View All
                                    </Link>
                                    <button
                                        v-if="needsKycVerification"
                                        @click="openKycModal"
                                        class="text-blue-400/50 hover:text-blue-300/70 text-sm font-medium transition-colors duration-200 cursor-pointer flex items-center"
                                    >
                                        View All
                                        <svg
                                            class="w-3 h-3 ml-1 text-red-300"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"
                                            ></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <div
                                v-if="activeBookings.length === 0"
                                class="p-8 text-center text-white/60"
                            >
                                <svg
                                    class="mx-auto h-12 w-12 text-white/40 mb-4"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
                                    ></path>
                                </svg>
                                <p>No active rentals</p>
                                <p class="text-sm mt-1">
                                    Your vehicles are not currently rented out
                                </p>
                            </div>

                            <div v-else class="divide-y divide-white/10">
                                <div
                                    v-for="booking in activeBookings"
                                    :key="booking.id"
                                    class="p-6 hover:bg-white/5 transition-colors"
                                >
                                    <div class="flex items-start space-x-4">
                                        <img
                                            :src="booking.vehicle_image"
                                            :alt="booking.vehicle_name"
                                            class="w-16 h-16 object-cover rounded-lg border border-white/20"
                                        />
                                        <div class="flex-1 min-w-0">
                                            <div
                                                class="flex items-center justify-between"
                                            >
                                                <h3
                                                    class="text-lg font-medium text-white truncate"
                                                >
                                                    {{ booking.vehicle_name }}
                                                </h3>
                                                <div
                                                    :class="[
                                                        'text-sm font-bold px-3 py-1 rounded-full',
                                                        booking.time_remaining
                                                            ?.overdue
                                                            ? 'bg-red-100 text-red-800'
                                                            : 'bg-green-100 text-green-800',
                                                    ]"
                                                >
                                                    {{
                                                        booking.time_remaining
                                                            ?.text
                                                    }}
                                                </div>
                                            </div>

                                            <div
                                                class="mt-2 grid grid-cols-2 gap-4 text-sm text-white/70"
                                            >
                                                <div>
                                                    <span
                                                        class="font-medium text-white/90"
                                                        >Renter:</span
                                                    >
                                                    {{ booking.renter_name }}
                                                </div>
                                                <div>
                                                    <span
                                                        class="font-medium text-white/90"
                                                        >Return:</span
                                                    >
                                                    {{
                                                        formatDateTime(
                                                            booking.expected_return
                                                        )
                                                    }}
                                                </div>
                                            </div>

                                            <!-- Overdue/Overcharge alerts -->
                                            <div
                                                v-if="booking.is_overdue"
                                                class="mt-2 flex items-center text-sm"
                                            >
                                                <svg
                                                    class="w-4 h-4 text-red-400 mr-1"
                                                    fill="currentColor"
                                                    viewBox="0 0 20 20"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                                        clip-rule="evenodd"
                                                    ></path>
                                                </svg>
                                                <span
                                                    class="text-red-400 font-medium"
                                                    >Vehicle is overdue!</span
                                                >
                                            </div>

                                            <div
                                                v-if="booking.has_overcharges"
                                                class="mt-2 flex items-center text-sm"
                                            >
                                                <svg
                                                    class="w-4 h-4 text-orange-400 mr-1"
                                                    fill="currentColor"
                                                    viewBox="0 0 20 20"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                        clip-rule="evenodd"
                                                    ></path>
                                                </svg>
                                                <span
                                                    class="text-orange-400 font-medium"
                                                >
                                                    Overcharges: ₱{{
                                                        formatCurrency(
                                                            booking.total_overcharges
                                                        )
                                                    }}
                                                </span>
                                            </div>

                                            <div class="mt-3 flex space-x-3">
                                                <Link
                                                    :href="
                                                        route(
                                                            'owner.bookings.show',
                                                            booking.id
                                                        )
                                                    "
                                                    class="text-blue-400 hover:text-blue-300 text-sm font-medium transition-colors"
                                                >
                                                    View Details
                                                </Link>
                                                <a
                                                    v-if="booking.renter_phone"
                                                    :href="`tel:${booking.renter_phone}`"
                                                    class="text-green-400 hover:text-green-300 text-sm font-medium transition-colors"
                                                >
                                                    Call Renter
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Bookings -->
                        <div class="glass-card-dark shadow-glow mt-8">
                            <div class="p-6 border-b border-white/20">
                                <div class="flex items-center justify-between">
                                    <h2
                                        class="text-xl font-semibold text-white"
                                    >
                                        Recent Bookings
                                    </h2>
                                    <Link
                                        v-if="!needsKycVerification"
                                        :href="route('owner.bookings.index')"
                                        class="text-blue-400 hover:text-blue-300 text-sm font-medium transition-colors"
                                    >
                                        View All
                                    </Link>
                                    <button
                                        v-if="needsKycVerification"
                                        @click="openKycModal"
                                        class="text-blue-400/50 hover:text-blue-300/70 text-sm font-medium transition-colors cursor-pointer flex items-center"
                                    >
                                        View All
                                        <svg
                                            class="w-3 h-3 ml-1 text-red-300"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"
                                            ></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <div class="divide-y divide-white/10">
                                <div
                                    v-for="booking in recentBookings.slice(
                                        0,
                                        5
                                    )"
                                    :key="booking.id"
                                    class="p-4 hover:bg-white/5 transition-colors"
                                >
                                    <div class="flex items-center space-x-3">
                                        <img
                                            :src="booking.vehicle_image"
                                            :alt="booking.vehicle_name"
                                            class="w-12 h-12 object-cover rounded border border-white/20"
                                        />
                                        <div class="flex-1 min-w-0">
                                            <div
                                                class="flex items-center justify-between"
                                            >
                                                <p
                                                    class="text-sm font-medium text-white truncate"
                                                >
                                                    {{ booking.vehicle_name }}
                                                </p>
                                                <span
                                                    :class="
                                                        getStatusClass(
                                                            booking.status
                                                        )
                                                    "
                                                    class="px-2 py-1 rounded-full text-xs font-medium"
                                                >
                                                    {{
                                                        formatStatus(
                                                            booking.status
                                                        )
                                                    }}
                                                </span>
                                            </div>
                                            <p class="text-xs text-white/70">
                                                {{ booking.renter_name }}
                                            </p>
                                            <p class="text-xs text-green-400">
                                                ₱{{
                                                    formatCurrency(
                                                        booking.total_amount
                                                    )
                                                }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Side Panel -->
                    <div class="space-y-6">
                        <!-- Quick Actions -->
                        <div class="glass-card-dark shadow-glow p-6">
                            <h3 class="text-lg font-semibold text-white mb-4">
                                Quick Actions
                            </h3>
                            <div class="space-y-3">
                                <!-- Add New Vehicle -->
                                <Link
                                    v-if="!needsKycVerification"
                                    :href="route('owner.vehicles.create')"
                                    class="w-full bg-blue-600 text-white px-4 py-2 rounded-md text-sm hover:bg-blue-700 flex items-center justify-center transition-colors"
                                >
                                    <svg
                                        class="w-4 h-4 mr-2"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M12 4v16m8-8H4"
                                        ></path>
                                    </svg>
                                    Add New Vehicle
                                </Link>
                                <button
                                    v-if="needsKycVerification"
                                    @click="openKycModal"
                                    class="w-full bg-blue-600/50 text-white/70 px-4 py-2 rounded-md text-sm cursor-pointer flex items-center justify-center transition-colors border border-blue-400/30"
                                >
                                    <svg
                                        class="w-4 h-4 mr-2"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M12 4v16m8-8H4"
                                        ></path>
                                    </svg>
                                    Add New Vehicle
                                    <svg
                                        class="w-4 h-4 ml-2 text-red-300"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"
                                        ></path>
                                    </svg>
                                </button>

                                <!-- Manage Bookings -->
                                <Link
                                    v-if="!needsKycVerification"
                                    :href="route('owner.bookings.index')"
                                    class="w-full bg-green-600 text-white px-4 py-2 rounded-md text-sm hover:bg-green-700 flex items-center justify-center transition-colors"
                                >
                                    <svg
                                        class="w-4 h-4 mr-2"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
                                        ></path>
                                    </svg>
                                    Manage Bookings
                                </Link>
                                <button
                                    v-if="needsKycVerification"
                                    @click="openKycModal"
                                    class="w-full bg-green-600/50 text-white/70 px-4 py-2 rounded-md text-sm cursor-pointer flex items-center justify-center transition-colors border border-green-400/30"
                                >
                                    <svg
                                        class="w-4 h-4 mr-2"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
                                        ></path>
                                    </svg>
                                    Manage Bookings
                                    <svg
                                        class="w-4 h-4 ml-2 text-red-300"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"
                                        ></path>
                                    </svg>
                                </button>

                                <!-- My Vehicles -->
                                <Link
                                    v-if="!needsKycVerification"
                                    :href="route('owner.vehicles.index')"
                                    class="w-full bg-purple-600 text-white px-4 py-2 rounded-md text-sm hover:bg-purple-700 flex items-center justify-center transition-colors"
                                >
                                    <svg
                                        class="w-4 h-4 mr-2"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H9m0 0H5m2 0v-2a2 2 0 012-2h2a2 2 0 012 2v2z"
                                        ></path>
                                    </svg>
                                    My Vehicles
                                </Link>

                                <!-- Statistics -->
                                <Link
                                    v-if="!needsKycVerification"
                                    :href="route('owner.statistics')"
                                    class="w-full bg-indigo-600 text-white px-4 py-2 rounded-md text-sm hover:bg-indigo-700 flex items-center justify-center transition-colors"
                                >
                                    <svg
                                        class="w-4 h-4 mr-2"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                                        ></path>
                                    </svg>
                                    Business Statistics
                                </Link>

                                <button
                                    v-if="needsKycVerification"
                                    @click="openKycModal"
                                    class="w-full bg-purple-600/50 text-white/70 px-4 py-2 rounded-md text-sm cursor-pointer flex items-center justify-center transition-colors border border-purple-400/30"
                                >
                                    <svg
                                        class="w-4 h-4 mr-2"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H9m0 0H5m2 0v-2a2 2 0 012-2h2a2 2 0 012 2v2z"
                                        ></path>
                                    </svg>
                                    My Vehicles
                                    <svg
                                        class="w-4 h-4 ml-2 text-red-300"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"
                                        ></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Most Popular Vehicle -->
                        <div
                            v-if="mostPopularVehicle"
                            class="glass-card-dark shadow-glow p-6"
                        >
                            <h3 class="text-lg font-semibold text-white mb-4">
                                Most Popular Vehicle
                            </h3>
                            <div class="flex items-center space-x-3">
                                <img
                                    :src="mostPopularVehicle.image"
                                    :alt="mostPopularVehicle.name"
                                    class="w-16 h-16 object-cover rounded-lg border border-white/20"
                                />
                                <div>
                                    <p class="font-medium text-white">
                                        {{ mostPopularVehicle.name }}
                                    </p>
                                    <p class="text-sm text-white/70">
                                        {{
                                            mostPopularVehicle.booking_count
                                        }}
                                        bookings
                                    </p>
                                    <Link
                                        :href="`/owner/vehicles/${mostPopularVehicle.id}`"
                                        class="text-blue-400 hover:text-blue-300 text-xs font-medium transition-colors"
                                    >
                                        View Details →
                                    </Link>
                                </div>
                            </div>
                        </div>

                        <!-- Vehicle Performance -->
                        <div class="glass-card-dark shadow-glow">
                            <div class="p-6 border-b border-white/20">
                                <h3 class="text-lg font-semibold text-white">
                                    Top Performing Vehicles
                                </h3>
                            </div>
                            <div class="p-4 space-y-4">
                                <!-- When there is no data -->
                                <div
                                    v-if="
                                        !vehiclePerformance ||
                                        vehiclePerformance.length === 0
                                    "
                                    class="text-center text-white/60 text-sm py-4"
                                >
                                    No vehicle performance data available.
                                </div>

                                <!-- When data exists -->
                                <div v-else>
                                    <div
                                        v-for="vehicle in vehiclePerformance.slice(
                                            0,
                                            3
                                        )"
                                        :key="vehicle.id"
                                        class="flex items-center space-x-3 p-2 rounded-lg hover:bg-white/5 transition-colors"
                                    >
                                        <img
                                            :src="vehicle.image"
                                            :alt="vehicle.name"
                                            class="w-10 h-10 object-cover rounded border border-white/20"
                                        />
                                        <div class="flex-1 min-w-0">
                                            <p
                                                class="text-sm font-medium text-white truncate"
                                            >
                                                {{ vehicle.name }}
                                            </p>
                                            <p class="text-xs text-white/70">
                                                {{
                                                    vehicle.total_bookings
                                                }}
                                                bookings
                                            </p>
                                            <p
                                                class="text-xs text-green-400 font-medium"
                                            >
                                                ₱{{
                                                    formatCurrency(
                                                        vehicle.total_earnings
                                                    )
                                                }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- View all link -->
                                <Link
                                    v-if="
                                        !needsKycVerification &&
                                        vehiclePerformance &&
                                        vehiclePerformance.length > 0
                                    "
                                    :href="route('owner.vehicles.index')"
                                    class="block text-center text-blue-400 hover:text-blue-300 text-sm font-medium pt-2 transition-colors"
                                >
                                    View All Vehicles →
                                </Link>

                                <button
                                    v-if="needsKycVerification"
                                    @click="openKycModal"
                                    class="block w-full text-center text-blue-400/50 hover:text-blue-300/70 text-sm font-medium pt-2 transition-colors cursor-pointer"
                                >
                                    View All Vehicles →
                                    <svg
                                        class="w-3 h-3 inline ml-1 text-red-300"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"
                                        ></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Calendar Preview -->
                        <div class="glass-card-dark shadow-glow p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-white">
                                    Upcoming Bookings
                                </h3>
                                <Link
                                    :href="route('owner.bookings.calendar')"
                                    class="text-blue-400 hover:text-blue-300 text-sm font-medium transition-colors"
                                >
                                    View Calendar
                                </Link>
                            </div>
                            <div
                                v-if="calendarEvents.length === 0"
                                class="text-center text-white/60 py-4"
                            >
                                <p class="text-sm">No upcoming bookings</p>
                            </div>
                            <div v-else class="space-y-2">
                                <div
                                    v-for="event in calendarEvents.slice(0, 3)"
                                    :key="event.id"
                                    class="p-3 bg-white/5 rounded-md border border-white/10 hover:bg-white/10 transition-colors"
                                >
                                    <p class="text-sm font-medium text-white">
                                        {{ event.title }}
                                    </p>
                                    <p class="text-xs text-white/70">
                                        {{ event.renter }}
                                    </p>
                                    <p class="text-xs text-white/60">
                                        {{ formatDate(event.start) }}
                                    </p>
                                    <span
                                        :class="getStatusClass(event.status)"
                                        class="inline-block px-2 py-1 rounded-full text-xs font-medium mt-1"
                                    >
                                        {{ formatStatus(event.status) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- KYC Verification Modal -->
        <div
            v-if="showKycModal"
            class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="modal-title"
            role="dialog"
            aria-modal="true"
        >
            <div
                class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0"
            >
                <!-- Background overlay -->
                <div
                    class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                    aria-hidden="true"
                    @click="closeKycModal"
                ></div>

                <!-- Modal panel -->
                <div
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
                >
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div
                                class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10"
                            >
                                <!-- Lock icon -->
                                <svg
                                    class="h-6 w-6 text-red-600"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"
                                    />
                                </svg>
                            </div>
                            <div
                                class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left"
                            >
                                <h3
                                    class="text-lg leading-6 font-medium text-gray-900"
                                    id="modal-title"
                                >
                                    Driver's License Verification Required
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">
                                        Please complete your Driver's License verification to
                                        access this feature. You need to verify
                                        your license to continue using this
                                        functionality.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div
                        class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse"
                    >
                        <Link
                            :href="route('kyc.verify')"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm"
                        >
                            Verify Now
                        </Link>
                        <button
                            @click="closeKycModal"
                            type="button"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                        >
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, usePage } from "@inertiajs/vue3";

const $page = usePage();

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
    kycNotification: Object,
});

const showKycModal = ref(false);

// KYC verification check
const needsKycVerification = computed(() => {
    const user = $page.props.auth.user;
    if (!user) return false;
    return user.kyc_status !== "approved" && user.role?.name !== "admin";
});

const openKycModal = () => {
    showKycModal.value = true;
};

const closeKycModal = () => {
    showKycModal.value = false;
};

function formatCurrency(amount) {
    return parseFloat(amount || 0).toLocaleString("en-US", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
}

function formatDateTime(dateTime) {
    if (!dateTime) return "N/A";
    return new Date(dateTime).toLocaleString("en-US", {
        month: "short",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
}

function formatDate(dateTime) {
    if (!dateTime) return "N/A";
    return new Date(dateTime).toLocaleDateString("en-US", {
        month: "short",
        day: "numeric",
        year: "numeric",
    });
}

function getStatusClass(status) {
    const classes = {
        pending: "bg-yellow-100 text-yellow-800",
        confirmed: "bg-green-100 text-green-800",
        completed: "bg-blue-100 text-blue-800",
        cancelled: "bg-red-100 text-red-800",
    };
    return classes[status] || "bg-gray-100 text-gray-800";
}

function formatStatus(status) {
    const statusMap = {
        pending: "Pending",
        confirmed: "Active",
        completed: "Completed",
        cancelled: "Cancelled",
    };
    return statusMap[status] || status;
}
</script>
