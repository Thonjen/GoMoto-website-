<template>
    <div v-if="shouldShowWarning" class="bg-amber-50 border-l-4 border-amber-400 p-4 mb-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-amber-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-amber-800">
                    Driver's License Required
                </h3>
                <div class="mt-2 text-sm text-amber-700">
                    <p>
                        {{ warningMessage }}
                        <Link 
                            :href="flashWarning?.action_url || route('profile.edit')" 
                            class="font-medium underline text-amber-800 hover:text-amber-900"
                        >
                            Complete verification here
                        </Link>
                    </p>
                </div>
            </div>
            <div class="ml-auto pl-3">
                <div class="-mx-1.5 -my-1.5">
                    <button 
                        @click="dismissWarning" 
                        type="button" 
                        class="inline-flex bg-amber-50 rounded-md p-1.5 text-amber-500 hover:bg-amber-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-amber-50 focus:ring-amber-600"
                    >
                        <span class="sr-only">Dismiss</span>
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import { useAuthStore } from '@/stores/auth'

const auth = useAuthStore()
const page = usePage()

// Local storage key for dismissed warnings
const STORAGE_KEY = 'kyc_warning_dismissed'

// Check if warning was dismissed today
const isDismissedToday = ref(false)

const checkDismissalStatus = () => {
    const dismissed = localStorage.getItem(STORAGE_KEY)
    if (dismissed) {
        const dismissedDate = new Date(dismissed)
        const today = new Date()
        // Reset dismissal if it's a new day
        if (dismissedDate.toDateString() !== today.toDateString()) {
            localStorage.removeItem(STORAGE_KEY)
            isDismissedToday.value = false
        } else {
            isDismissedToday.value = true
        }
    }
}

onMounted(() => {
    checkDismissalStatus()
})

// Check for flash message from middleware
const flashWarning = computed(() => {
    return page.props.flash?.kyc_warning || null
})

// Determine what type of warning to show based on current route
const warningType = computed(() => {
    // If there's a flash warning, use its type
    if (flashWarning.value) {
        return flashWarning.value.type
    }
    
    const currentRoute = page.url
    
    if (currentRoute.includes('/vehicles/') && currentRoute.includes('/book')) {
        return 'booking'
    }
    if (currentRoute.includes('/bookings')) {
        return 'booking'
    }
    if (currentRoute.includes('/owner/vehicles')) {
        return 'listing'
    }
    
    return 'general'
})

// Check if user needs KYC/license verification
const needsLicenseVerification = computed(() => {
    const user = auth.user || page.props.auth?.user
    
    if (!user) return false
    
    // Admin users don't need verification
    if (user.role?.name === 'admin') return false
    
    // Check if user has uploaded both sides of license
    return !user.drivers_license_front || !user.drivers_license_back
})

const needsKycVerification = computed(() => {
    const user = auth.user || page.props.auth?.user
    
    if (!user) return false
    
    // Admin users don't need verification
    if (user.role?.name === 'admin') return false
    
    return user.kyc_status !== 'approved'
})

// Warning message to display
const warningMessage = computed(() => {
    // If there's a flash warning from middleware, use it
    if (flashWarning.value) {
        return flashWarning.value.message
    }
    
    // Default messages based on context
    if (warningType.value === 'booking') {
        if (needsLicenseVerification.value) {
            return 'You need to upload your driver\'s license to make vehicle bookings.'
        }
        if (needsKycVerification.value) {
            return 'You need to complete KYC verification to make bookings.'
        }
    }
    
    if (warningType.value === 'listing') {
        return 'You need to complete KYC verification to list vehicles.'
    }
    
    return 'Please complete your profile verification to access all features.'
})

// Show warning logic
const shouldShowWarning = computed(() => {
    // Always show flash warnings (they override dismissal)
    if (flashWarning.value) {
        return true
    }
    
    const user = auth.user || page.props.auth?.user
    const currentRoute = page.url
    
    // Don't show if dismissed today
    if (isDismissedToday.value) return false
    
    // Don't show if user is not logged in
    if (!user) return false
    
    // Don't show if user is admin
    if (user.role?.name === 'admin') return false
    
    // Don't show on profile edit page (where they can fix the issue)
    if (currentRoute.includes('/profile')) return false
    
    // Show warning based on route context
    if (warningType.value === 'booking') {
        // Show if user is trying to book but lacks license or KYC
        return needsLicenseVerification.value || needsKycVerification.value
    }
    
    if (warningType.value === 'listing') {
        // Show if user is trying to list vehicles but lacks KYC
        return needsKycVerification.value
    }
    
    // For other routes, only show if they're on a restricted page
    const restrictedRoutes = [
        '/bookings',
        '/vehicles/saved',
        '/owner/',
        '/vehicles/',
    ]
    
    const isOnRestrictedRoute = restrictedRoutes.some(route => currentRoute.includes(route))
    
    if (isOnRestrictedRoute) {
        return needsLicenseVerification.value || needsKycVerification.value
    }
    
    return false
})

const dismissWarning = () => {
    isDismissedToday.value = true
    localStorage.setItem(STORAGE_KEY, new Date().toISOString())
}

// Watch for route changes to re-evaluate warning
watch(() => page.url, () => {
    checkDismissalStatus()
})
</script>
