<template>
  <AuthenticatedLayout>
    <!-- Main Container with Dark Theme -->
    <div class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900">
      <!-- Background Pattern -->
      <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-gray-700/20 via-gray-900 to-gray-900"></div>
      
      <div class="relative z-10 py-12">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
          
          <!-- Header Section -->
          <div class="text-center mb-12">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-r from-gray-600 via-gray-700 to-gray-800 rounded-full mb-6 shadow-2xl relative">
              <div class="absolute inset-0 bg-gradient-to-r from-blue-500/20 to-purple-500/20 rounded-full animate-pulse"></div>
              <svg class="w-10 h-10 text-white relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m-6 4h6m4-12v12a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707z"/>
              </svg>
            </div>
            <h1 class="text-5xl font-bold text-white mb-4 tracking-tight">
              Payment Verification
            </h1>
            <p class="text-xl text-gray-300 max-w-3xl mx-auto leading-relaxed">
              Upload your payment proof to complete your GoMoto booking. Our secure verification system ensures your transaction is processed quickly and safely.
            </p>
            <div class="mt-6 flex flex-col sm:flex-row items-center justify-center gap-4">
              <div class="inline-flex items-center px-6 py-3 bg-gray-800/40 backdrop-blur-sm border border-gray-600/30 rounded-full">
                <svg class="w-4 h-4 mr-2 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span class="text-sm font-medium text-gray-300">Booking #{{ bookingId }}</span>
              </div>
              <div class="inline-flex items-center px-4 py-2 bg-green-500/20 backdrop-blur-sm border border-green-500/30 rounded-full">
                <div class="w-2 h-2 bg-green-400 rounded-full mr-2 animate-pulse"></div>
                <span class="text-sm font-medium text-green-300">Secure Process</span>
              </div>
            </div>
          </div>

          <!-- Main Content Grid -->
          <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Sidebar - Booking Details -->
            <div class="lg:col-span-1 space-y-6">
              
              <!-- Quick Actions Card -->
              <div class="bg-gray-800/40 backdrop-blur-sm border border-gray-600/30 rounded-2xl p-6 shadow-2xl">
                <h3 class="text-lg font-semibold text-white mb-4 flex items-center">
                  <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                  </svg>
                  Quick Actions
                </h3>
                <nav class="space-y-3">
                  <Link 
                    href="/my-bookings" 
                    class="flex items-center p-3 text-gray-300 hover:text-white hover:bg-gray-700/50 rounded-xl transition-all duration-200 group"
                  >
                    <div class="p-2 bg-gray-700/50 rounded-lg mr-3 group-hover:bg-gray-600/50">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                      </svg>
                    </div>
                    <span class="font-medium">My Bookings</span>
                  </Link>
                  <Link 
                    href="/vehicles" 
                    class="flex items-center p-3 text-gray-300 hover:text-white hover:bg-gray-700/50 rounded-xl transition-all duration-200 group"
                  >
                    <div class="p-2 bg-gray-700/50 rounded-lg mr-3 group-hover:bg-gray-600/50">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2v0a2 2 0 01-2-2v-5H8z"/>
                      </svg>
                    </div>
                    <span class="font-medium">Browse Vehicles</span>
                  </Link>
                </nav>
              </div>

              <!-- Booking Summary Card -->
              <div class="bg-gray-800/40 backdrop-blur-sm border border-gray-600/30 rounded-2xl p-6 shadow-2xl">
                <h3 class="text-lg font-semibold text-white mb-4 flex items-center">
                  <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                  </svg>
                  Booking Summary
                </h3>
                <div class="space-y-4">
                  <div class="flex justify-between items-center py-3 border-b border-gray-600/30">
                    <span class="text-gray-400 flex items-center">
                      <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2v0a2 2 0 01-2-2v-5H8z"/>
                      </svg>
                      Vehicle
                    </span>
                    <span class="text-white font-medium">{{ bookingDetails.vehicleName }}</span>
                  </div>
                  <div class="flex justify-between items-center py-3 border-b border-gray-600/30">
                    <span class="text-gray-400 flex items-center">
                      <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                      </svg>
                      Owner
                    </span>
                    <span class="text-white font-medium">{{ bookingDetails.ownerName }}</span>
                  </div>
                  <div class="flex justify-between items-center py-3 border-b border-gray-600/30">
                    <span class="text-gray-400 flex items-center">
                      <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 9l2 2 4-4"/>
                      </svg>
                      Pickup
                    </span>
                    <span class="text-white font-medium">{{ bookingDetails.pickupDate }}</span>
                  </div>
                  <div class="flex justify-between items-center py-3 border-b border-gray-600/30">
                    <span class="text-gray-400 flex items-center">
                      <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                      </svg>
                      Return
                    </span>
                    <span class="text-white font-medium">{{ bookingDetails.returnDate }}</span>
                  </div>
                  <div class="flex justify-between items-center py-4 bg-gradient-to-r from-gray-700/50 to-gray-600/50 rounded-xl px-4 mt-6">
                    <span class="text-gray-300 font-medium flex items-center">
                      <svg class="w-5 h-5 mr-2 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                      </svg>
                      Total Amount
                    </span>
                    <span class="text-2xl font-bold text-white">₱{{ bookingDetails.totalPrice.toLocaleString() }}</span>
                  </div>
                </div>
                
                <!-- Payment Status -->
                <div class="mt-6 p-4 bg-yellow-500/10 border border-yellow-500/20 rounded-xl">
                  <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                    <span class="text-yellow-300 font-medium">Payment Pending</span>
                  </div>
                  <p class="text-yellow-200 text-sm mt-1">Upload payment proof to complete your booking</p>
                </div>
              </div>
            </div>

            <!-- Main Content Area -->
            <div class="lg:col-span-2 space-y-6">
              
              <!-- Current Proof Display -->
              <div v-if="proofOfPayment.imageUrl" class="bg-gray-800/40 backdrop-blur-sm border border-gray-600/30 rounded-2xl p-6 shadow-2xl">
                <div class="flex items-center justify-between mb-6">
                  <h2 class="text-2xl font-semibold text-white flex items-center">
                    <svg class="w-6 h-6 mr-2 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Current Payment Proof
                  </h2>
                  <span class="inline-flex items-center px-3 py-1 bg-green-500/20 border border-green-500/30 rounded-full text-sm font-medium text-green-300">
                    ✓ Uploaded
                  </span>
                </div>
                
                <div class="text-center">
                  <div class="inline-block bg-gray-700/30 p-6 rounded-2xl border border-gray-600/30">
                    <img 
                      :src="proofOfPayment.imageUrl" 
                      alt="Payment Proof" 
                      class="max-w-full max-h-64 object-contain rounded-xl shadow-lg"
                    />
                  </div>
                  <p class="text-sm text-gray-400 mt-4 mb-6">Uploaded on {{ proofOfPayment.uploadDate }}</p>
                  
                  <button 
                    @click="removeProof"
                    class="inline-flex items-center justify-center px-6 py-3 bg-red-500/20 border border-red-500/30 text-red-300 rounded-xl hover:bg-red-500/30 hover:border-red-500/50 transition-all duration-200 font-medium"
                  >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Remove Proof
                  </button>
                </div>
              </div>

              <!-- Upload Form -->
              <div class="bg-gray-800/40 backdrop-blur-sm border border-gray-600/30 rounded-2xl shadow-2xl overflow-hidden">
                
                <!-- Form Header -->
                <div class="bg-gradient-to-r from-gray-700/50 to-gray-600/50 px-6 py-4 border-b border-gray-600/30">
                  <h2 class="text-2xl font-semibold text-white flex items-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                    {{ proofOfPayment.imageUrl ? 'Update Payment Proof' : 'Upload Payment Proof' }}
                  </h2>
                  <p class="text-gray-300 mt-2">
                    Upload a clear screenshot or photo of your payment receipt for verification
                  </p>
                </div>

                <!-- Form Content -->
                <div class="p-6">
                  
                  <!-- Instructions -->
                  <div class="bg-blue-500/10 border border-blue-500/20 rounded-xl p-6 mb-6">
                    <div class="flex items-start space-x-4">
                      <div class="p-2 bg-blue-500/20 rounded-lg flex-shrink-0">
                        <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                      </div>
                      <div class="flex-1">
                        <p class="text-blue-300 font-semibold text-lg mb-3">Payment Upload Guidelines</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                          <div class="space-y-3">
                            <div class="flex items-start space-x-2">
                              <svg class="w-4 h-4 text-blue-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                              </svg>
                              <span class="text-blue-200 text-sm">Take a clear screenshot of your payment confirmation</span>
                            </div>
                            <div class="flex items-start space-x-2">
                              <svg class="w-4 h-4 text-blue-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                              </svg>
                              <span class="text-blue-200 text-sm">Ensure transaction amount (₱{{ bookingDetails.totalPrice.toLocaleString() }}) is visible</span>
                            </div>
                            <div class="flex items-start space-x-2">
                              <svg class="w-4 h-4 text-blue-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                              </svg>
                              <span class="text-blue-200 text-sm">Include reference number and timestamp</span>
                            </div>
                          </div>
                          <div class="space-y-3">
                            <div class="flex items-start space-x-2">
                              <svg class="w-4 h-4 text-blue-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                              </svg>
                              <span class="text-blue-200 text-sm">Supported: PNG, JPEG, JPG (max 5MB)</span>
                            </div>
                            <div class="flex items-start space-x-2">
                              <svg class="w-4 h-4 text-blue-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                              </svg>
                              <span class="text-blue-200 text-sm">Verification usually takes 5-10 minutes</span>
                            </div>
                            <div class="flex items-start space-x-2">
                              <svg class="w-4 h-4 text-blue-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                              </svg>
                              <span class="text-blue-200 text-sm">Owner will be notified automatically</span>
                            </div>
                          </div>
                        </div>
                        <div class="mt-4 p-3 bg-blue-500/10 rounded-lg border border-blue-500/20">
                          <p class="text-blue-300 text-sm font-medium">💡 Pro Tip: Make sure your payment app shows "Transaction Successful" before taking the screenshot</p>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Upload Form -->
                  <form @submit.prevent="uploadProof" class="space-y-6">
                    
                    <!-- File Upload Area -->
                    <div class="space-y-2">
                      <label for="receiptFile" class="block text-sm font-medium text-white">
                        Select Receipt Image
                      </label>
                      
                      <!-- Drag & Drop Zone -->
                      <div class="relative group">
                        <input 
                          type="file" 
                          id="receiptFile" 
                          @change="handleFileChange" 
                          accept="image/png,image/jpeg,image/jpg" 
                          required
                          class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                        />
                        
                        <div class="border-2 border-dashed border-gray-600/50 rounded-2xl p-12 text-center hover:border-gray-500/50 hover:bg-gray-700/20 transition-all duration-300 group-hover:scale-[1.02]">
                          <div class="space-y-6">
                            <div class="mx-auto w-20 h-20 bg-gradient-to-br from-gray-700/50 to-gray-600/50 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                              <svg class="w-10 h-10 text-gray-400 group-hover:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                              </svg>
                            </div>
                            <div>
                              <p class="text-white font-semibold text-xl mb-2">Drop your payment receipt here</p>
                              <p class="text-gray-400 text-lg">or <span class="text-blue-400 font-medium hover:text-blue-300 transition-colors">click to browse files</span></p>
                              <div class="mt-4 flex items-center justify-center space-x-6 text-sm text-gray-500">
                                <div class="flex items-center space-x-1">
                                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                  </svg>
                                  <span>PNG, JPEG, JPG</span>
                                </div>
                                <div class="flex items-center space-x-1">
                                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                  </svg>
                                  <span>Max 5MB</span>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      
                      <!-- Selected File Info -->
                      <div v-if="selectedFile" class="flex items-center justify-between p-4 bg-gray-700/30 rounded-xl border border-gray-600/30">
                        <div class="flex items-center space-x-3">
                          <div class="p-2 bg-green-500/20 rounded-lg">
                            <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                          </div>
                          <div>
                            <p class="text-white font-medium">{{ selectedFile.name }}</p>
                            <p class="text-sm text-gray-400">{{ (selectedFile.size / 1024 / 1024).toFixed(2) }} MB</p>
                          </div>
                        </div>
                        <button 
                          type="button" 
                          @click="clearSelectedFile"
                          class="p-2 text-gray-400 hover:text-white hover:bg-gray-600/50 rounded-lg transition-colors"
                        >
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                          </svg>
                        </button>
                      </div>
                    </div>
                    
                    <!-- Submit Button -->
                    <div class="flex space-x-4">
                      <button 
                        type="submit"
                        :disabled="!selectedFile"
                        class="flex-1 inline-flex items-center justify-center px-6 py-4 bg-gradient-to-r from-gray-600 to-gray-700 border border-gray-500/30 text-white rounded-xl hover:from-gray-500 hover:to-gray-600 hover:border-gray-400/50 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 font-medium text-lg shadow-xl"
                      >
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        {{ proofOfPayment.imageUrl ? 'Update Proof' : 'Upload Proof' }}
                      </button>
                    </div>
                  </form>
                </div>
              </div>

              <!-- Enhanced Security Notice -->
              <div class="bg-gray-800/40 backdrop-blur-sm border border-gray-600/30 rounded-2xl p-6 shadow-2xl">
                <div class="flex items-start space-x-4">
                  <div class="p-3 bg-green-500/20 rounded-xl">
                    <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.414-4.414a2 2 0 111.414 5.828L11.828 19l-4-4-4-4 4-4 4 4-4 4z"/>
                    </svg>
                  </div>
                  <div class="flex-1">
                    <h3 class="text-white font-semibold text-lg mb-2">🔒 Security & Privacy Guaranteed</h3>
                    <p class="text-gray-300 mb-4 leading-relaxed">
                      Your payment information is protected with military-grade encryption. Only the vehicle owner will see your payment proof for verification purposes.
                    </p>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                      <div class="bg-gray-700/30 rounded-lg p-3 text-center">
                        <svg class="w-6 h-6 text-green-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        <p class="text-sm text-gray-300 font-medium">SSL Encrypted</p>
                      </div>
                      <div class="bg-gray-700/30 rounded-lg p-3 text-center">
                        <svg class="w-6 h-6 text-blue-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-sm text-gray-300 font-medium">Instant Verification</p>
                      </div>
                      <div class="bg-gray-700/30 rounded-lg p-3 text-center">
                        <svg class="w-6 h-6 text-purple-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        <p class="text-sm text-gray-300 font-medium">Fast Processing</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Support Section -->
              <div class="bg-gray-800/40 backdrop-blur-sm border border-gray-600/30 rounded-2xl p-6 shadow-2xl">
                <h3 class="text-white font-semibold text-lg mb-4 flex items-center">
                  <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                  Need Help?
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div class="bg-gray-700/20 rounded-xl p-4">
                    <h4 class="text-white font-medium mb-2">Common Issues</h4>
                    <ul class="text-gray-300 text-sm space-y-1">
                      <li>• Upload failed? Check file size (max 5MB)</li>
                      <li>• Blurry image? Retake with better lighting</li>
                      <li>• Wrong amount? Contact support immediately</li>
                    </ul>
                  </div>
                  <div class="bg-gray-700/20 rounded-xl p-4">
                    <h4 class="text-white font-medium mb-2">Contact Support</h4>
                    <div class="space-y-2 text-sm">
                      <div class="flex items-center text-gray-300">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        support@gomoto.ph
                      </div>
                      <div class="flex items-center text-gray-300">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        24/7 Live Chat
                      </div>
                    </div>
                  </div>
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
import { ref, onMounted } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const page = usePage();
const bookingId = page.props.id || 1; // Get ID from route params, default to 1 for demo

const bookingDetails = ref({
  vehicleName: 'Toyota Camry 2022',
  ownerName: 'Juan Dela Cruz',
  pickupDate: 'Jul 20, 2025',
  returnDate: 'Jul 25, 2025',
  totalPrice: 9000,
});

const proofOfPayment = ref({
  imageUrl: '', // Simulate no existing proof initially
  uploadDate: '',
});

const selectedFile = ref(null);

onMounted(() => {
  // Simulate fetching booking details and existing proof
  // In a real app, this data would be passed as props from the controller
  if (bookingId === 1) { // Example for booking ID 1 - show existing proof
    proofOfPayment.value.imageUrl = '/placeholder.svg?height=256&width=256';
    proofOfPayment.value.uploadDate = 'July 18, 2025';
  }
});

const handleFileChange = (event) => {
  const file = event.target.files[0];
  if (file) {
    // Validate file type
    const allowedTypes = ['image/png', 'image/jpeg', 'image/jpg'];
    if (!allowedTypes.includes(file.type)) {
      alert('Please select a valid image file (PNG, JPEG, or JPG).');
      event.target.value = '';
      return;
    }
    
    // Validate file size (5MB max)
    if (file.size > 5 * 1024 * 1024) {
      alert('File size must be less than 5MB.');
      event.target.value = '';
      return;
    }
    
    selectedFile.value = file;
  }
};

const clearSelectedFile = () => {
  selectedFile.value = null;
  document.getElementById('receiptFile').value = '';
};

const uploadProof = () => {
  if (!selectedFile.value) {
    alert('Please select a receipt image to upload.');
    return;
  }
  
  // Show success message
  alert('Payment proof uploaded successfully! The vehicle owner will be notified for verification.');
  
  // Simulate successful upload
  proofOfPayment.value.imageUrl = URL.createObjectURL(selectedFile.value);
  proofOfPayment.value.uploadDate = new Date().toLocaleDateString('en-US', { 
    year: 'numeric', 
    month: 'long', 
    day: 'numeric' 
  });
  
  // Clear selected file
  clearSelectedFile();
  
  // In a real Inertia app, this would be:
  // Inertia.post(`/my-bookings/${bookingId}/receipt`, { receipt: selectedFile.value })
};

const removeProof = () => {
  if (confirm('Are you sure you want to remove this payment proof? You will need to upload a new one for verification.')) {
    proofOfPayment.value.imageUrl = '';
    proofOfPayment.value.uploadDate = '';
    alert('Payment proof removed successfully.');
    // In a real Inertia app, this would be:
    // Inertia.delete(`/my-bookings/${bookingId}/receipt`)
  }
};
</script>

<style scoped>
/* Modern GoMoto styling - glassmorphism effects and smooth transitions */

/* Custom scrollbar for better UX */
::-webkit-scrollbar {
  width: 6px;
}

::-webkit-scrollbar-track {
  background: rgba(107, 114, 128, 0.1);
  border-radius: 3px;
}

::-webkit-scrollbar-thumb {
  background: rgba(107, 114, 128, 0.3);
  border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
  background: rgba(107, 114, 128, 0.5);
}

/* Enhanced hover effects for better interaction feedback */
.hover-lift {
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.hover-lift:hover {
  transform: translateY(-2px);
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

/* Custom file input styling */
input[type="file"] {
  background: transparent;
}

/* Animation for success states */
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.fade-in-up {
  animation: fadeInUp 0.6s ease-out;
}

/* Focus states for better accessibility */
button:focus,
input:focus {
  outline: 2px solid rgba(59, 130, 246, 0.5);
  outline-offset: 2px;
}

/* Smooth transitions for all interactive elements */
button,
.hover-lift,
input,
a {
  transition: all 0.2s ease;
}

/* Loading spinner animation */
@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

.spin {
  animation: spin 1s linear infinite;
}
</style>
