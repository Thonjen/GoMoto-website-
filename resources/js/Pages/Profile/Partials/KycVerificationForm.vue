<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const page = usePage();
const user = computed(() => page.props.auth.user);
const userProfile = computed(() => page.props.user || {});

const form = useForm({
    drivers_license_front: null,
    drivers_license_back: null,
});

const driversLicenseFrontPreview = ref(null);
const driversLicenseBackPreview = ref(null);

const handleFileChange = (event, field) => {
    const file = event.target.files[0];
    if (file) {
        processFile(file, field);
    }
};

const processFile = (file, field) => {
    // Validate file type
    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    if (!allowedTypes.includes(file.type)) {
        alert('Please upload a valid image file (JPEG, PNG, or GIF).');
        return;
    }
    
    // Validate file size (2MB max)
    if (file.size > 2 * 1024 * 1024) {
        alert('File size must be less than 2MB.');
        return;
    }
    
    form[field] = file;
    
    // Create preview
    const reader = new FileReader();
    reader.onload = (e) => {
        if (field === 'drivers_license_front') {
            driversLicenseFrontPreview.value = e.target.result;
        } else {
            driversLicenseBackPreview.value = e.target.result;
        }
    };
    reader.readAsDataURL(file);
};

const removeFile = (field) => {
    form[field] = null;
    if (field === 'drivers_license_front') {
        driversLicenseFrontPreview.value = null;
    } else {
        driversLicenseBackPreview.value = null;
    }
};

// Drag and drop handlers
const handleDragOver = (event) => {
    event.preventDefault();
    event.stopPropagation();
    event.dataTransfer.dropEffect = 'copy';
};

const handleDragEnter = (event) => {
    event.preventDefault();
    event.stopPropagation();
};

const handleDragLeave = (event) => {
    event.preventDefault();
    event.stopPropagation();
};

const handleDrop = (event, field) => {
    event.preventDefault();
    event.stopPropagation();
    
    const files = event.dataTransfer.files;
    if (files.length > 0) {
        processFile(files[0], field);
    }
};

const submitKyc = () => {
    form.post(route('profile.kyc.submit'), {
        forceFormData: true,
    });
};
</script>

<template>
    <section class="max-w-full">
        <!-- Modern Header -->
        <div class="mb-8">
            <div class="flex items-center space-x-3 mb-3">
                <div class="p-2 bg-blue-100 rounded-xl">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.031 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-900">
                    Identity Verification
                </h2>
            </div>
            <p class="text-gray-600 leading-relaxed">
                Secure your account by verifying your identity with official documents. This helps us maintain a safe community for everyone.
            </p>
        </div>

        <!-- Enhanced Account Status Display -->
        <div class="mb-8" v-if="user?.status && user.status !== 'active'">
            <div class="bg-red-50 border border-red-200 rounded-2xl p-6">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="p-2 bg-red-100 rounded-xl">
                        <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-red-900">Account Access Restricted</h3>
                        <span :class="[
                            'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium mt-1',
                            user.status === 'banned' ? 'bg-red-200 text-red-800' :
                            user.status === 'suspended' ? 'bg-orange-200 text-orange-800' :
                            'bg-gray-200 text-gray-800'
                        ]">
                            {{ user.status.toUpperCase() }}
                        </span>
                    </div>
                </div>
                <p class="text-red-800 leading-relaxed">
                    Your account has been {{ user.status }}. Please contact our support team for assistance with restoring your account access.
                </p>
            </div>
        </div>

        <!-- Enhanced KYC Status Display -->
        <div class="mb-8" v-if="user?.kyc_status">
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center space-x-3">
                        <div :class="[
                            'p-2 rounded-xl',
                            user.kyc_status === 'approved' ? 'bg-green-100' :
                            user.kyc_status === 'under_review' ? 'bg-blue-100' :
                            user.kyc_status === 'rejected' ? 'bg-red-100' :
                            'bg-yellow-100'
                        ]">
                            <svg v-if="user.kyc_status === 'approved'" class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <svg v-else-if="user.kyc_status === 'under_review'" class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                            </svg>
                            <svg v-else-if="user.kyc_status === 'rejected'" class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            <svg v-else class="w-5 h-5 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Verification Status</h3>
                            <p class="text-sm text-gray-600">Track your identity verification progress</p>
                        </div>
                    </div>
                    <span :class="[
                        'inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold',
                        user.kyc_status === 'approved' ? 'bg-green-100 text-green-800' :
                        user.kyc_status === 'under_review' ? 'bg-blue-100 text-blue-800' :
                        user.kyc_status === 'rejected' ? 'bg-red-100 text-red-800' :
                        'bg-yellow-100 text-yellow-800'
                    ]">
                        {{ user.kyc_status.replace('_', ' ').toUpperCase() }}
                    </span>
                </div>
                
                <!-- Timeline Information -->
                <div class="space-y-3 mb-6">
                    <div v-if="user.license_submitted_at" class="flex items-center text-sm text-gray-600">
                        <svg class="w-4 h-4 mr-2 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                        </svg>
                        <strong>Submitted:</strong> {{ new Date(user.license_submitted_at).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' }) }}
                    </div>
                    
                    <div v-if="user.kyc_verified_at" class="flex items-center text-sm text-gray-600">
                        <svg class="w-4 h-4 mr-2 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <strong>{{ user.kyc_status === 'approved' ? 'Approved' : 'Processed' }}:</strong> 
                        {{ new Date(user.kyc_verified_at).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' }) }}
                    </div>
                </div>
                
                <!-- Status-specific Messages -->
                <div v-if="user.kyc_status === 'rejected' && user.kyc_rejection_reason" 
                     class="bg-red-50 border border-red-200 rounded-xl p-4 mb-4">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-red-500 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <h4 class="text-sm font-semibold text-red-800 mb-1">Verification Rejected</h4>
                            <p class="text-sm text-red-700">{{ user.kyc_rejection_reason }}</p>
                        </div>
                    </div>
                </div>
                
                <div v-if="user.kyc_status === 'approved'" 
                     class="bg-green-50 border border-green-200 rounded-xl p-4 mb-4">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-green-500 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <h4 class="text-sm font-semibold text-green-800 mb-1">✅ Verification Complete</h4>
                            <p class="text-sm text-green-700">Your identity has been successfully verified! You now have full access to make bookings and list vehicles on our platform.</p>
                        </div>
                    </div>
                </div>
                
                <div v-if="user.kyc_status === 'under_review'" 
                     class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-4">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-blue-500 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <h4 class="text-sm font-semibold text-blue-800 mb-1">⏳ Under Review</h4>
                            <p class="text-sm text-blue-700">Our verification team is currently reviewing your documents. This process typically takes 24-48 hours. We'll notify you once the review is complete.</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Account Restrictions Notice -->
            <div v-if="user.kyc_status !== 'approved' && user.status === 'active'" 
                 class="bg-amber-50 border border-amber-200 rounded-xl p-4 mt-4">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-amber-500 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    <div>
                        <h4 class="text-sm font-semibold text-amber-800 mb-1">Account Restrictions</h4>
                        <p class="text-sm text-amber-700">
                            <span v-if="!user.can_book">You cannot make bookings</span>
                            <span v-if="!user.can_book && !user.can_list_vehicles"> and </span>
                            <span v-if="!user.can_list_vehicles">you cannot list vehicles</span>
                            until your identity verification is complete.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modern Document Upload Form -->
        <form @submit.prevent="submitKyc" v-if="user?.status === 'active' || !user?.status">
            <div class="bg-white rounded-2xl border border-gray-200 p-8 shadow-sm">
                <div class="mb-8">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="p-2 bg-blue-100 rounded-xl">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">Driver's License Verification</h3>
                            <p class="text-gray-600">Upload clear, high-quality photos of both sides of your driver's license</p>
                        </div>
                    </div>
                    
                    <!-- Requirements -->
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                        <h4 class="text-sm font-semibold text-blue-900 mb-2">Photo Requirements:</h4>
                        <ul class="text-sm text-blue-800 space-y-1">
                            <li class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                Clear, well-lit photos with all text readable
                            </li>
                            <li class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                No glare or shadows covering important information
                            </li>
                            <li class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                JPEG, PNG, or GIF format, maximum 2MB per image
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Front License Upload -->
                    <div class="space-y-4">
                        <div class="flex items-center space-x-2 mb-3">
                            <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded-full">1</span>
                            <h4 class="text-lg font-semibold text-gray-900">Front Side</h4>
                        </div>
                        
                        <!-- Upload Area -->
                        <div class="relative">
                            <input
                                id="drivers_license_front"
                                type="file"
                                accept="image/*"
                                @change="handleFileChange($event, 'drivers_license_front')"
                                class="sr-only"
                            />
                            
                            <label 
                                for="drivers_license_front" 
                                class="cursor-pointer block"
                                @dragover="handleDragOver"
                                @dragenter="handleDragEnter"
                                @dragleave="handleDragLeave"
                                @drop="handleDrop($event, 'drivers_license_front')"
                            >
                                <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 hover:border-blue-400 
                                           hover:bg-blue-50 transition-all duration-200 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                    </svg>
                                    <p class="text-gray-600 font-medium">Drop image here or click to upload</p>
                                    <p class="text-sm text-gray-500 mt-1">Front side of your driver's license</p>
                                </div>
                            </label>
                        </div>
                        
                        <InputError class="mt-2" :message="form.errors.drivers_license_front" />
                        
                        <!-- Current/Preview Image -->
                        <div v-if="userProfile?.drivers_license_front_url && !driversLicenseFrontPreview" class="mt-4">
                            <div class="flex items-center space-x-2 mb-3">
                                <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                <p class="text-sm font-medium text-green-700">Current front image</p>
                            </div>
                            <div class="relative inline-block">
                                <img :src="userProfile.drivers_license_front_url" alt="Driver's License Front" 
                                     class="h-40 w-auto rounded-xl border-2 border-green-200 shadow-sm">
                                <div class="absolute top-3 right-3 bg-green-500 text-white rounded-lg px-2 py-1 text-xs font-medium">
                                    ✓ Uploaded
                                </div>
                            </div>
                        </div>
                        
                        <div v-if="driversLicenseFrontPreview" class="mt-4">
                            <p class="text-sm font-medium text-blue-700 mb-3">Preview - Front Side</p>
                            <div class="relative inline-block">
                                <img :src="driversLicenseFrontPreview" alt="License Front Preview" 
                                     class="h-40 w-auto rounded-xl border-2 border-blue-200 shadow-sm">
                                <button
                                    type="button"
                                    @click="removeFile('drivers_license_front')"
                                    class="absolute -top-2 -right-2 bg-red-500 hover:bg-red-600 text-white 
                                           rounded-full w-8 h-8 flex items-center justify-center shadow-lg 
                                           transition-colors duration-200"
                                >
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Back License Upload -->
                    <div class="space-y-4">
                        <div class="flex items-center space-x-2 mb-3">
                            <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded-full">2</span>
                            <h4 class="text-lg font-semibold text-gray-900">Back Side</h4>
                        </div>
                        
                        <!-- Upload Area -->
                        <div class="relative">
                            <input
                                id="drivers_license_back"
                                type="file"
                                accept="image/*"
                                @change="handleFileChange($event, 'drivers_license_back')"
                                class="sr-only"
                            />
                            
                            <label 
                                for="drivers_license_back" 
                                class="cursor-pointer block"
                                @dragover="handleDragOver"
                                @dragenter="handleDragEnter"
                                @dragleave="handleDragLeave"
                                @drop="handleDrop($event, 'drivers_license_back')"
                            >
                                <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 hover:border-blue-400 
                                           hover:bg-blue-50 transition-all duration-200 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                    </svg>
                                    <p class="text-gray-600 font-medium">Drop image here or click to upload</p>
                                    <p class="text-sm text-gray-500 mt-1">Back side of your driver's license</p>
                                </div>
                            </label>
                        </div>
                        
                        <InputError class="mt-2" :message="form.errors.drivers_license_back" />
                        
                        <!-- Current/Preview Image -->
                        <div v-if="userProfile?.drivers_license_back_url && !driversLicenseBackPreview" class="mt-4">
                            <div class="flex items-center space-x-2 mb-3">
                                <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                <p class="text-sm font-medium text-green-700">Current back image</p>
                            </div>
                            <div class="relative inline-block">
                                <img :src="userProfile.drivers_license_back_url" alt="Driver's License Back" 
                                     class="h-40 w-auto rounded-xl border-2 border-green-200 shadow-sm">
                                <div class="absolute top-3 right-3 bg-green-500 text-white rounded-lg px-2 py-1 text-xs font-medium">
                                    ✓ Uploaded
                                </div>
                            </div>
                        </div>
                        
                        <div v-if="driversLicenseBackPreview" class="mt-4">
                            <p class="text-sm font-medium text-blue-700 mb-3">Preview - Back Side</p>
                            <div class="relative inline-block">
                                <img :src="driversLicenseBackPreview" alt="License Back Preview" 
                                     class="h-40 w-auto rounded-xl border-2 border-blue-200 shadow-sm">
                                <button
                                    type="button"
                                    @click="removeFile('drivers_license_back')"
                                    class="absolute -top-2 -right-2 bg-red-500 hover:bg-red-600 text-white 
                                           rounded-full w-8 h-8 flex items-center justify-center shadow-lg 
                                           transition-colors duration-200"
                                >
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-between pt-8 border-t border-gray-200 mt-8">
                    <div class="flex items-center gap-4">
                        <PrimaryButton 
                            :disabled="form.processing"
                            class="bg-blue-600 hover:bg-blue-700 focus:ring-blue-500 px-8 py-3 rounded-xl
                                   font-semibold text-white shadow-sm hover:shadow-md transition-all duration-200
                                   disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <svg v-if="form.processing" class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            {{ form.processing ? 'Submitting...' : 
                               user.kyc_status === 'rejected' ? 'Resubmit for Verification' : 'Submit for Verification' }}
                        </PrimaryButton>
                        
                        <Transition
                            enter-active-class="transition ease-out duration-300"
                            enter-from-class="opacity-0 transform scale-95"
                            enter-to-class="opacity-100 transform scale-100"
                            leave-active-class="transition ease-in duration-200"
                            leave-from-class="opacity-100 transform scale-100"
                            leave-to-class="opacity-0 transform scale-95"
                        >
                            <div v-if="form.recentlySuccessful" 
                                 class="flex items-center text-green-700 bg-green-50 px-4 py-2 rounded-xl border border-green-200">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="font-medium text-sm">Documents submitted successfully!</span>
                            </div>
                        </Transition>
                    </div>
                    
                    <p class="text-xs text-gray-500">
                        Secure upload • Data encrypted
                    </p>
                </div>
            </div>
        </form>
        
        <!-- Form disabled for banned/suspended users -->
        <div v-else class="mt-6 p-4 bg-gray-50 border border-gray-200 rounded-md">
            <p class="text-sm text-gray-600">
                <strong>Form Disabled:</strong> You cannot update your KYC information while your account is {{ user?.status }}. 
                Please contact support for assistance.
            </p>
        </div>
    </section>
</template>
