<template>
    <AuthenticatedLayout>
        <!-- Main Container with Modern Design -->
        <div class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900">
            <!-- Background Pattern -->
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-gray-700/20 via-gray-900 to-gray-900"></div>
            
            <div class="relative z-10 py-12">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    
                    <!-- Header Section -->
                    <div class="text-center mb-12">
                        <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-r from-gray-600 via-gray-700 to-gray-800 rounded-full mb-6 shadow-2xl relative">
                            <div class="absolute inset-0 bg-gradient-to-r from-green-500/20 to-blue-500/20 rounded-full animate-pulse"></div>
                            <svg class="w-10 h-10 text-white relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <h1 class="text-5xl font-bold text-white mb-4 tracking-tight">
                            Complete Your Payment
                        </h1>
                        <p class="text-xl text-gray-300 max-w-3xl mx-auto leading-relaxed">
                            Secure your GoMoto booking with our trusted payment system. Fast, safe, and completely verified.
                        </p>
                        <div class="mt-6 flex flex-col sm:flex-row items-center justify-center gap-4">
                            <div class="inline-flex items-center px-6 py-3 bg-gray-800/40 backdrop-blur-sm border border-gray-600/30 rounded-full">
                                <svg class="w-4 h-4 mr-2 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <span class="text-sm font-medium text-gray-300">Booking #{{ booking.id }}</span>
                            </div>
                            <div class="inline-flex items-center px-4 py-2 bg-green-500/20 backdrop-blur-sm border border-green-500/30 rounded-full">
                                <div class="w-2 h-2 bg-green-400 rounded-full mr-2 animate-pulse"></div>
                                <span class="text-sm font-medium text-green-300">SSL Secured</span>
                            </div>
                        </div>
                    </div>

                    <!-- Main Content Grid -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        
                        <!-- Booking Summary Sidebar -->
                        <div class="lg:col-span-1 space-y-6">
                            
                            <!-- Vehicle Card -->
                            <div class="bg-gray-800/40 backdrop-blur-sm border border-gray-600/30 rounded-2xl p-6 shadow-2xl">
                                <h3 class="text-lg font-semibold text-white mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2v0a2 2 0 01-2-2v-5H8z"/>
                                    </svg>
                                    Your Vehicle
                                </h3>
                                <div class="aspect-video bg-gray-700/30 rounded-xl overflow-hidden mb-4">
                                    <img
                                        :src="booking.vehicle.main_photo_url || '/images/placeholder-vehicle.jpg'"
                                        :alt="`${booking.vehicle.brand?.name} ${booking.vehicle.type?.sub_type}`"
                                        class="w-full h-full object-cover"
                                    />
                                </div>
                                <h4 class="font-semibold text-white text-lg mb-2">
                                    {{ booking.vehicle.brand?.name }}
                                    {{ booking.vehicle.type?.sub_type }}
                                </h4>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between text-gray-400">
                                        <span>License Plate:</span>
                                        <span class="text-white font-medium">{{ booking.vehicle.license_plate }}</span>
                                    </div>
                                    <div class="flex justify-between text-gray-400">
                                        <span>Location:</span>
                                        <span class="text-white font-medium">{{ booking.vehicle.location_name }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Booking Details Card -->
                            <div class="bg-gray-800/40 backdrop-blur-sm border border-gray-600/30 rounded-2xl p-6 shadow-2xl">
                                <h3 class="text-lg font-semibold text-white mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 9l2 2 4-4"/>
                                    </svg>
                                    Trip Details
                                </h3>
                                <div class="space-y-4">
                                    <div class="flex justify-between items-center py-3 border-b border-gray-600/30">
                                        <span class="text-gray-400 flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            Duration
                                        </span>
                                        <span class="text-white font-medium">
                                            {{ booking.pricing_tier.duration_from }}
                                            {{ booking.pricing_tier.duration_unit }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between items-center py-3 border-b border-gray-600/30">
                                        <span class="text-gray-400 flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 9l2 2 4-4"/>
                                            </svg>
                                            Pickup
                                        </span>
                                        <span class="text-white font-medium">{{ formatDateTime(booking.pickup_datetime) }}</span>
                                    </div>
                                    <div class="flex justify-between items-center py-3 border-b border-gray-600/30">
                                        <span class="text-gray-400 flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                            </svg>
                                            Payment Method
                                        </span>
                                        <span class="text-white font-medium">{{ booking.payment.payment_mode.name }}</span>
                                    </div>
                                    <div class="flex justify-between items-center py-4 bg-gradient-to-r from-gray-700/50 to-gray-600/50 rounded-xl px-4 mt-6">
                                        <span class="text-gray-300 font-medium flex items-center">
                                            <svg class="w-5 h-5 mr-2 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                            </svg>
                                            Total Amount
                                        </span>
                                        <span class="text-2xl font-bold text-white">₱{{ booking.total_amount }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Content Area -->
                        <div class="lg:col-span-2 space-y-6">
                            
                            <!-- Location Map -->
                            <div class="bg-gray-800/40 backdrop-blur-sm border border-gray-600/30 rounded-2xl p-6 shadow-2xl">
                                <h3 class="text-lg font-semibold text-white mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    Pickup Location
                                </h3>
                                <div class="aspect-video bg-gray-700/30 rounded-xl overflow-hidden border border-gray-600/30">
                                    <iframe
                                        v-if="booking.vehicle.lat && booking.vehicle.lng"
                                        :src="`https://maps.google.com/maps?q=${booking.vehicle.lat},${booking.vehicle.lng}&z=18&t=k&output=embed`"
                                        class="w-full h-full border-0"
                                        loading="lazy"
                                        allowfullscreen
                                    >
                                    </iframe>
                                    <div
                                        v-else
                                        class="flex items-center justify-center h-full text-gray-400"
                                    >
                                        <svg class="w-12 h-12 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        </svg>
                                        <span class="text-lg">Location not available</span>
                                    </div>
                                </div>
                                <p class="text-gray-300 mt-3 text-center font-medium">
                                    📍 {{ booking.vehicle.location_name }}
                                </p>
                            </div>

                            <!-- GCash Payment Section -->
                            <div v-if="booking.payment.type === 'gcash'" class="bg-gray-800/40 backdrop-blur-sm border border-gray-600/30 rounded-2xl shadow-2xl overflow-hidden">
                                
                                <!-- Payment Header -->
                                <div class="bg-gradient-to-r from-blue-600/20 to-green-600/20 px-6 py-4 border-b border-gray-600/30">
                                    <h2 class="text-2xl font-semibold text-white flex items-center">
                                        <svg class="w-6 h-6 mr-2 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                        </svg>
                                        GCash Payment
                                    </h2>
                                    <p class="text-gray-300 mt-2">
                                        Scan the QR code or follow the payment instructions below
                                    </p>
                                </div>

                                <div class="p-6">
                                    <div v-if="booking.vehicle.owner.gcash_qr" class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                                        
                                        <!-- QR Code Section -->
                                        <div class="text-center">
                                            <h3 class="text-xl font-semibold text-white mb-6 flex items-center justify-center">
                                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                                </svg>
                                                Scan to Pay
                                            </h3>
                                            <div class="bg-white p-6 rounded-2xl border-4 border-gray-600/30 inline-block shadow-2xl">
                                                <img
                                                    :src="'/storage/' + booking.vehicle.owner.gcash_qr"
                                                    alt="GCash QR Code"
                                                    class="w-64 h-64 object-contain"
                                                />
                                            </div>
                                            <div class="mt-6 p-4 bg-green-500/10 border border-green-500/20 rounded-xl">
                                                <p class="text-green-300 font-semibold text-lg">
                                                    Amount to Pay: <span class="text-2xl">₱{{ booking.total_amount }}</span>
                                                </p>
                                                <p class="text-green-200 text-sm mt-1">Make sure to pay the exact amount</p>
                                            </div>
                                        </div>

                                        <!-- Upload Receipt Section -->
                                        <div>
                                            <h3 class="text-xl font-semibold text-white mb-6 flex items-center">
                                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                                </svg>
                                                Upload Payment Proof
                                            </h3>

                                            <div v-if="!booking.payment.receipt_image">
                                                <!-- Upload Instructions -->
                                                <div class="bg-blue-500/10 border border-blue-500/20 rounded-xl p-4 mb-6">
                                                    <h4 class="text-blue-300 font-medium mb-2">📱 Payment Steps:</h4>
                                                    <ol class="text-blue-200 text-sm space-y-1 list-decimal list-inside">
                                                        <li>Open your GCash app</li>
                                                        <li>Scan the QR code above</li>
                                                        <li>Enter amount: ₱{{ booking.total_amount }}</li>
                                                        <li>Complete the payment</li>
                                                        <li>Take a screenshot of confirmation</li>
                                                        <li>Upload the screenshot below</li>
                                                    </ol>
                                                </div>

                                                <!-- Upload Form -->
                                                <form @submit.prevent="uploadReceipt" class="space-y-6">
                                                    <div>
                                                        <label for="receipt" class="block text-sm font-medium text-white mb-3">
                                                            Select Payment Screenshot
                                                        </label>
                                                        <div class="relative group">
                                                            <input
                                                                type="file"
                                                                id="receipt"
                                                                @change="handleFileChange"
                                                                accept="image/*"
                                                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                                                required
                                                            />
                                                            <div class="border-2 border-dashed border-gray-600/50 rounded-xl p-8 text-center hover:border-gray-500/50 hover:bg-gray-700/20 transition-all duration-300 group-hover:scale-[1.02]">
                                                                <div class="space-y-4">
                                                                    <div class="mx-auto w-16 h-16 bg-gradient-to-br from-gray-700/50 to-gray-600/50 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                                                        <svg class="w-8 h-8 text-gray-400 group-hover:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                                        </svg>
                                                                    </div>
                                                                    <div>
                                                                        <p class="text-white font-medium text-lg">Drop receipt here</p>
                                                                        <p class="text-gray-400">or <span class="text-blue-400 font-medium">click to browse</span></p>
                                                                        <p class="text-sm text-gray-500 mt-2">PNG, JPEG, JPG accepted</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div v-if="errors.receipt_image" class="mt-2 text-sm text-red-400">
                                                            {{ errors.receipt_image }}
                                                        </div>
                                                    </div>

                                                    <button
                                                        type="submit"
                                                        :disabled="!selectedFile || uploading"
                                                        class="w-full flex justify-center items-center py-4 px-6 bg-gradient-to-r from-green-600 to-green-700 border border-green-500/30 text-white rounded-xl hover:from-green-500 hover:to-green-600 hover:border-green-400/50 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 font-medium text-lg shadow-xl"
                                                    >
                                                        <svg v-if="!uploading" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                                        </svg>
                                                        <svg v-else class="w-5 h-5 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                                        </svg>
                                                        {{ uploading ? "Uploading..." : "Upload Receipt" }}
                                                    </button>
                                                </form>
                                            </div>

                                            <!-- Receipt Uploaded Success -->
                                            <div v-else class="text-center">
                                                <div class="bg-green-500/10 border border-green-500/20 rounded-2xl p-8">
                                                    <svg class="w-16 h-16 text-green-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    <h4 class="text-2xl font-semibold text-green-300 mb-3">
                                                        Receipt Uploaded Successfully!
                                                    </h4>
                                                    <p class="text-green-200 mb-6 text-lg">
                                                        Your payment proof has been submitted and is awaiting verification by the vehicle owner.
                                                    </p>
                                                    <div class="bg-gray-700/30 p-4 rounded-xl mb-6">
                                                        <img
                                                            :src="booking.payment.receipt_image"
                                                            alt="Payment Receipt"
                                                            class="mx-auto max-w-xs rounded-lg border border-gray-600/30 shadow-lg"
                                                        />
                                                    </div>
                                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                                        <div class="bg-gray-700/20 rounded-lg p-3 text-center">
                                                            <svg class="w-6 h-6 text-blue-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                            </svg>
                                                            <p class="text-gray-300 font-medium">Processing Time</p>
                                                            <p class="text-gray-400">5-10 minutes</p>
                                                        </div>
                                                        <div class="bg-gray-700/20 rounded-lg p-3 text-center">
                                                            <svg class="w-6 h-6 text-purple-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4.343 4.343l1.414 1.414m9.899 9.899l1.414 1.414M5.757 18.243l1.414-1.414M18.243 5.757l1.414 1.414M12 1v6m0 6v6m11-7h-6m-6 0H1"/>
                                                            </svg>
                                                            <p class="text-gray-300 font-medium">Notification Sent</p>
                                                            <p class="text-gray-400">To Owner</p>
                                                        </div>
                                                        <div class="bg-gray-700/20 rounded-lg p-3 text-center">
                                                            <svg class="w-6 h-6 text-green-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                            </svg>
                                                            <p class="text-gray-300 font-medium">Next Step</p>
                                                            <p class="text-gray-400">Owner Verification</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- No QR Code Error -->
                                    <div v-else class="bg-red-500/10 border border-red-500/20 rounded-2xl p-8 text-center">
                                        <svg class="w-16 h-16 text-red-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                        </svg>
                                        <h3 class="text-xl font-semibold text-red-300 mb-3">QR Code Not Available</h3>
                                        <p class="text-red-200 mb-6">
                                            The vehicle owner has not uploaded their GCash QR code yet. Please contact the owner directly for payment instructions.
                                        </p>
                                        <div class="bg-gray-700/30 rounded-xl p-4">
                                            <p class="text-gray-300 font-medium mb-2">Owner Contact:</p>
                                            <p class="text-white">{{ booking.vehicle.owner.first_name }} {{ booking.vehicle.owner.last_name }}</p>
                                            <p v-if="booking.vehicle.owner.phone" class="text-blue-400">{{ booking.vehicle.owner.phone }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- COD Payment Section -->
                            <div v-else class="bg-gray-800/40 backdrop-blur-sm border border-gray-600/30 rounded-2xl shadow-2xl overflow-hidden">
                                
                                <!-- COD Header -->
                                <div class="bg-gradient-to-r from-blue-600/20 to-purple-600/20 px-6 py-4 border-b border-gray-600/30">
                                    <h2 class="text-2xl font-semibold text-white flex items-center">
                                        <svg class="w-6 h-6 mr-2 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                        Cash on Delivery
                                    </h2>
                                    <p class="text-gray-300 mt-2">
                                        Pay when you pick up the vehicle
                                    </p>
                                </div>

                                <div class="p-6">
                                    <div class="bg-blue-500/10 border border-blue-500/20 rounded-2xl p-8">
                                        <div class="text-center mb-8">
                                            <svg class="w-20 h-20 text-blue-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                            </svg>
                                            <h3 class="text-2xl font-semibold text-blue-300 mb-3">
                                                Payment Instructions
                                            </h3>
                                            <p class="text-blue-200 text-lg">
                                                You have selected Cash on Delivery payment method.
                                            </p>
                                        </div>
                                        
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <div class="space-y-4">
                                                <h4 class="text-lg font-semibold text-white mb-3">📋 Checklist:</h4>
                                                <div class="space-y-3">
                                                    <div class="flex items-start space-x-3">
                                                        <div class="p-1 bg-green-500/20 rounded-full">
                                                            <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                            </svg>
                                                        </div>
                                                        <div>
                                                            <p class="text-white font-medium">Prepare exact amount</p>
                                                            <p class="text-gray-400 text-sm">₱{{ booking.total_amount }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="flex items-start space-x-3">
                                                        <div class="p-1 bg-green-500/20 rounded-full">
                                                            <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                            </svg>
                                                        </div>
                                                        <div>
                                                            <p class="text-white font-medium">Bring valid ID</p>
                                                            <p class="text-gray-400 text-sm">For verification purposes</p>
                                                        </div>
                                                    </div>
                                                    <div class="flex items-start space-x-3">
                                                        <div class="p-1 bg-green-500/20 rounded-full">
                                                            <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                            </svg>
                                                        </div>
                                                        <div>
                                                            <p class="text-white font-medium">Arrive on time</p>
                                                            <p class="text-gray-400 text-sm">{{ formatDateTime(booking.pickup_datetime) }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="flex items-start space-x-3">
                                                        <div class="p-1 bg-green-500/20 rounded-full">
                                                            <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                            </svg>
                                                        </div>
                                                        <div>
                                                            <p class="text-white font-medium">Contact owner if needed</p>
                                                            <p class="text-gray-400 text-sm">For schedule changes</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="bg-gray-700/30 rounded-xl p-6">
                                                <h4 class="text-lg font-semibold text-white mb-4">💰 Payment Summary</h4>
                                                <div class="space-y-3">
                                                    <div class="flex justify-between py-2 border-b border-gray-600/30">
                                                        <span class="text-gray-400">Rental Fee</span>
                                                        <span class="text-white font-medium">₱{{ booking.total_amount }}</span>
                                                    </div>
                                                    <div class="flex justify-between py-2 border-b border-gray-600/30">
                                                        <span class="text-gray-400">Payment Method</span>
                                                        <span class="text-white font-medium">Cash</span>
                                                    </div>
                                                    <div class="flex justify-between py-3 text-lg font-semibold">
                                                        <span class="text-green-300">Total to Pay</span>
                                                        <span class="text-green-300">₱{{ booking.total_amount }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Owner Contact Information -->
                            <div class="bg-gray-800/40 backdrop-blur-sm border border-gray-600/30 rounded-2xl p-6 shadow-2xl">
                                <h3 class="text-lg font-semibold text-white mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Need Help? Contact Vehicle Owner
                                </h3>
                                <div class="bg-gray-700/30 rounded-xl p-6">
                                    <div class="flex items-center space-x-4 mb-4">
                                        <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center text-white text-lg font-semibold">
                                            {{ booking.vehicle.owner.first_name.charAt(0) }}{{ booking.vehicle.owner.last_name.charAt(0) }}
                                        </div>
                                        <div>
                                            <p class="text-white font-semibold text-lg">
                                                {{ booking.vehicle.owner.first_name }} {{ booking.vehicle.owner.last_name }}
                                            </p>
                                            <p class="text-gray-400">Vehicle Owner</p>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div v-if="booking.vehicle.owner.phone" class="flex items-center space-x-3 p-3 bg-gray-600/30 rounded-lg">
                                            <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                            </svg>
                                            <a
                                                :href="`tel:${booking.vehicle.owner.phone}`"
                                                class="text-blue-400 hover:text-blue-300 font-medium transition-colors"
                                            >
                                                {{ booking.vehicle.owner.phone }}
                                            </a>
                                        </div>
                                        <div class="flex items-center space-x-3 p-3 bg-gray-600/30 rounded-lg">
                                            <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                            </svg>
                                            <span class="text-gray-300 font-medium">Message Available</span>
                                        </div>
                                    </div>
                                    <p class="text-gray-400 text-sm mt-4">
                                        If you have any questions about your booking, payment, or need to change the pickup time, please contact the vehicle owner directly.
                                    </p>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex flex-col sm:flex-row gap-4">
                                <button
                                    @click="goToBookings"
                                    class="flex-1 flex items-center justify-center px-6 py-4 border border-gray-600/30 rounded-xl shadow-sm text-lg font-medium text-gray-300 bg-gray-800/40 hover:bg-gray-700/40 hover:text-white transition-all duration-200 backdrop-blur-sm"
                                >
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                    View My Bookings
                                </button>

                                <button
                                    @click="goToVehicle"
                                    class="flex-1 flex items-center justify-center px-6 py-4 border border-transparent rounded-xl shadow-sm text-lg font-medium text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-500 hover:to-blue-600 transition-all duration-200"
                                >
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2v0a2 2 0 01-2-2v-5H8z"/>
                                    </svg>
                                    Back to Vehicle
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from "vue";
import { router, useForm } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

const props = defineProps({
    booking: Object,
    errors: Object,
});

const selectedFile = ref(null);
const uploading = ref(false);

const uploadForm = useForm({
    receipt_image: null,
});

function handleFileChange(event) {
    selectedFile.value = event.target.files[0];
    uploadForm.receipt_image = event.target.files[0];
}

function uploadReceipt() {
    if (!selectedFile.value) return;

    uploading.value = true;

    uploadForm.post(route("bookings.uploadReceipt", props.booking.id), {
        onSuccess: () => {
            selectedFile.value = null;
        },
        onError: () => {
            uploading.value = false;
        },
        onFinish: () => {
            uploading.value = false;
        },
    });
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

function goToBookings() {
    router.visit(route("bookings.index"));
}

function goToVehicle() {
    router.visit(`/vehicles/${props.booking.vehicle.id}`);
}
</script>
