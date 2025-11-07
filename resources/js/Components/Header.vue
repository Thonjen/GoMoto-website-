<script setup>
import { ref, computed } from "vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import NavLink from "@/Components/NavLink.vue";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";
import { Link, usePage } from "@inertiajs/vue3";
import Footer from "@/Components/Footer.vue"; // import Footer
import KycWarningBanner from "@/Components/KycWarningBanner.vue";
import { useAuthStore } from "@/stores/auth";

const auth = useAuthStore();
const page = usePage();
console.log("AUTH USER:", auth.user); // ✅ See if role is present and named
const showingNavigationDropdown = ref(false);
const showKycModal = ref(false);

// Check if user needs KYC verification
const needsKycVerification = computed(() => {
    const user = page.props.auth?.user;
    if (!user) return false;
    
    // Admin users don't need KYC verification
    if (user.role?.name === 'admin') return false;
    
    // Check if user has not completed KYC verification
    return user.kyc_status !== 'approved';
});

// Check if user is verified
const isKycVerified = computed(() => {
    const user = page.props.auth?.user;
    if (!user) return false;
    return user.kyc_status === 'approved' || user.role?.name === 'admin';
});

const openKycModal = () => {
    showKycModal.value = true;
};

const closeKycModal = () => {
    showKycModal.value = false;
};
</script>

<template>
    <!-- KYC Warning Banner -->
    <KycWarningBanner />

    <nav class="w-full bg-transparent">
        <!-- Primary Navigation Menu -->
        <div class="flex h-20 justify-between items-center">
            <div class="flex items-center">
                <!-- Logo -->
                <div class="flex shrink-0 items-center space-x-3">
                    <Link
                        :href="route('dashboard')"
                        class="flex items-center space-x-3 group"
                    >
                        <div class="relative">
                            <ApplicationLogo
                                class="block h-10 w-auto text-white transition-transform duration-200 group-hover:scale-110"
                            />
                            <div
                                class="absolute inset-0 rounded-full bg-white/10 backdrop-blur-sm opacity-0 group-hover:opacity-100 transition-opacity duration-200 -z-10"
                            ></div>
                        </div>
                    </Link>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-6 sm:ml-10 sm:flex">
                    <!-- Dashboard Link with Lock State -->
                    <div v-if="$is('renter', 'owner')" class="relative">
                        <NavLink
                            v-if="!needsKycVerification"
                            :href="route('dashboard')"
                            :active="route().current('dashboard')"
                            class="text-white/90 hover:text-white font-medium transition-all duration-200 hover:scale-105"
                        >
                            Dashboard
                        </NavLink>
                        <div v-else 
                             @click="openKycModal"
                             class="relative cursor-pointer text-white/50 font-medium transition-all duration-200 hover:text-white/70 flex items-center">
                            Dashboard
                            <svg class="w-4 h-4 ml-1 text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                    </div>

                    <!-- Admin Dashboard (Always Accessible) -->
                    <NavLink
                        v-if="$is('admin')"
                        :href="route('admin.dashboard')"
                        :active="route().current('admin.dashboard')"
                        class="text-white/90 hover:text-white font-medium transition-all duration-200 hover:scale-105"
                    >
                        Dashboard
                    </NavLink>

                    <NavLink
                        v-if="$is('admin')"
                        :href="route('admin.reports')"
                        :active="route().current('admin.reports')"
                        class="text-white/90 hover:text-white font-medium transition-all duration-200 hover:scale-105"
                    >
                        View Reports
                    </NavLink>

                    <NavLink
                        v-if="$is('admin')"
                        :href="route('admin.bookings')"
                        :active="route().current('admin.bookings')"
                        class="text-white/90 hover:text-white font-medium transition-all duration-200 hover:scale-105"
                    >
                        View Bookings
                    </NavLink>

                                        <NavLink
                        v-if="$is('admin')"
                        :href="route('admin.kyc.logs')"
                        :active="route().current('admin.kyc.logs')"
                        class="text-white/90 hover:text-white font-medium transition-all duration-200 hover:scale-105"
                    >
                        KYC Logs
                    </NavLink>

                                        <NavLink
                        v-if="$is('admin')"
                        :href="route('admin.users')"
                        :active="route().current('admin.users')"
                        class="text-white/90 hover:text-white font-medium transition-all duration-200 hover:scale-105"
                    >
                        Manage Users                    </NavLink>

                                                                <NavLink
                        v-if="$is('admin')"
                        :href="route('admin.kyc.verifications')"
                        :active="route().current('admin.kyc.verifications')"
                        class="text-white/90 hover:text-white font-medium transition-all duration-200 hover:scale-105"
                    >
                        License Verification                  </NavLink>
                        

                                                                <NavLink
                        v-if="$is('admin')"
                        :href="route('admin.vehicles')"
                        :active="route().current('admin.vehicles')"
                        class="text-white/90 hover:text-white font-medium transition-all duration-200 hover:scale-105"
                    >
                        Manage Vehicles                    </NavLink>

                    <!-- Browse Vehicles Link with Lock State -->
                    <div v-if="$is('renter')" class="relative">
                        <NavLink
                            v-if="!needsKycVerification"
                            :href="route('public.vehicles.index')"
                            :active="route().current('public.vehicles.index')"
                            class="text-white/90 hover:text-white font-medium transition-all duration-200 hover:scale-105"
                        >
                            Browse Vehicles
                        </NavLink>
                        <div v-else 
                             @click="openKycModal"
                             class="relative cursor-pointer text-white/50 font-medium transition-all duration-200 hover:text-white/70 flex items-center">
                            Browse Vehicles
                            <svg class="w-4 h-4 ml-1 text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                    </div>

                    <!-- Saved Vehicles Link with Lock State -->
                    <div v-if="$is('renter')" class="relative">
                        <NavLink
                            v-if="!needsKycVerification"
                            :href="route('vehicles.saved')"
                            :active="route().current('vehicles.saved')"
                            class="text-white/90 hover:text-white font-medium transition-all duration-200 hover:scale-105"
                        >
                            💾 Saved
                        </NavLink>
                        <div v-else 
                             @click="openKycModal"
                             class="relative cursor-pointer text-white/50 font-medium transition-all duration-200 hover:text-white/70 flex items-center">
                            💾 Saved
                            <svg class="w-4 h-4 ml-1 text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                    </div>

                    <!-- My Bookings Link with Lock State -->
                    <div v-if="$is('renter')" class="relative">
                        <NavLink
                            v-if="!needsKycVerification"
                            :href="route('bookings.index')"
                            :active="route().current('bookings.*')"
                            class="text-white/90 hover:text-white font-medium transition-all duration-200 hover:scale-105"
                        >
                            My Bookings
                        </NavLink>
                        <div v-else 
                             @click="openKycModal"
                             class="relative cursor-pointer text-white/50 font-medium transition-all duration-200 hover:text-white/70 flex items-center">
                            My Bookings
                            <svg class="w-4 h-4 ml-1 text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                    </div>

                    <!-- My Vehicles Link with Lock State -->
                    <div v-if="$is('owner')" class="relative">
                        <NavLink
                            v-if="!needsKycVerification"
                            :href="route('owner.vehicles.index')"
                            :active="route().current('owner.vehicles.index')"
                            class="text-white/90 hover:text-white font-medium transition-all duration-200 hover:scale-105"
                        >
                            My Vehicles
                        </NavLink>
                        <div v-else 
                             @click="openKycModal"
                             class="relative cursor-pointer text-white/50 font-medium transition-all duration-200 hover:text-white/70 flex items-center">
                            My Vehicles
                            <svg class="w-4 h-4 ml-1 text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                    </div>

                    <!-- Booking Requests Link with Lock State -->
                    <div v-if="$is('owner')" class="relative">
                        <NavLink
                            v-if="!needsKycVerification"
                            :href="route('owner.bookings.index')"
                            :active="route().current('owner.bookings.index')"
                            class="text-white/90 hover:text-white font-medium transition-all duration-200 hover:scale-105"
                        >
                            Requests
                        </NavLink>
                        <div v-else 
                             @click="openKycModal"
                             class="relative cursor-pointer text-white/50 font-medium transition-all duration-200 hover:text-white/70 flex items-center">
                            Requests
                            <svg class="w-4 h-4 ml-1 text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:ml-6 sm:flex sm:items-center">
                <div class="relative ml-3">
                    <Dropdown align="right" width="48">
                        <template #trigger>
                            <span class="inline-flex rounded-md">
                                <button
                                    type="button"
                                    class="inline-flex items-center rounded-lg border border-white/20 bg-white/10 backdrop-blur-sm px-4 py-2 text-sm font-medium leading-4 text-white transition-all duration-200 hover:bg-white/20 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2 focus:ring-offset-transparent shadow-glow"
                                >
                                    {{ $page.props.auth.user.name }}

                                    <svg
                                        class="-mr-0.5 ml-2 h-4 w-4"
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20"
                                        fill="currentColor"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>
                                </button>
                            </span>
                        </template>

                        <template #content>
                            <DropdownLink
                                :href="route('profile.edit')"
                                class="text-gray-700 hover:text-gray-900"
                            >
                                Profile
                            </DropdownLink>
                            <DropdownLink
                                :href="route('logout')"
                                method="post"
                                as="button"
                                class="text-gray-700 hover:text-gray-900"
                            >
                                Log Out
                            </DropdownLink>
                        </template>
                    </Dropdown>
                </div>
            </div>

            <!-- Hamburger -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button
                    @click="
                        showingNavigationDropdown = !showingNavigationDropdown
                    "
                    class="inline-flex items-center justify-center rounded-lg p-2 text-white/80 transition-all duration-200 hover:bg-white/10 hover:text-white focus:bg-white/20 focus:text-white focus:outline-none backdrop-blur-sm"
                >
                    <svg
                        class="h-6 w-6"
                        stroke="currentColor"
                        fill="none"
                        viewBox="0 0 24 24"
                    >
                        <path
                            :class="{
                                hidden: showingNavigationDropdown,
                                'inline-flex': !showingNavigationDropdown,
                            }"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"
                        />
                        <path
                            :class="{
                                hidden: !showingNavigationDropdown,
                                'inline-flex': showingNavigationDropdown,
                            }"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"
                        />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Responsive Navigation Menu -->
        <div
            :class="{
                block: showingNavigationDropdown,
                hidden: !showingNavigationDropdown,
            }"
            class="sm:hidden border-t border-white/20 bg-black/20 backdrop-blur-md"
        >
            <div class="space-y-1 pb-3 pt-2 px-4">
                <!-- Dashboard Link (Responsive) -->
                <ResponsiveNavLink
                    v-if="$is('renter', 'owner') && !needsKycVerification"
                    :href="route('dashboard')"
                    :active="route().current('dashboard')"
                    class="text-white/90 hover:text-white hover:bg-white/10 rounded-lg transition-all duration-200"
                >
                    Dashboard
                </ResponsiveNavLink>
                <div v-if="$is('renter', 'owner') && needsKycVerification"
                     @click="openKycModal"
                     class="block px-4 py-2 text-base font-medium cursor-pointer text-white/50 hover:text-white/70 hover:bg-white/10 rounded-lg transition-all duration-200 flex items-center">
                    Dashboard
                    <svg class="w-4 h-4 ml-1 text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>

                <!-- Admin Dashboard (Always Accessible) -->
                <ResponsiveNavLink
                    v-if="$is('admin')"
                    :href="route('admin.dashboard')"
                    :active="route().current('admin.dashboard')"
                    class="text-white/90 hover:text-white hover:bg-white/10 rounded-lg transition-all duration-200"
                >
                    Dashboard
                </ResponsiveNavLink>

                <!-- Browse Vehicles (Responsive) -->
                <ResponsiveNavLink
                    v-if="$is('renter') && !needsKycVerification"
                    :href="route('public.vehicles.index')"
                    :active="route().current('public.vehicles.index')"
                    class="text-white/90 hover:text-white hover:bg-white/10 rounded-lg transition-all duration-200"
                >
                    Browse Vehicles
                </ResponsiveNavLink>
                <div v-if="$is('renter') && needsKycVerification"
                     @click="openKycModal"
                     class="block px-4 py-2 text-base font-medium cursor-pointer text-white/50 hover:text-white/70 hover:bg-white/10 rounded-lg transition-all duration-200 flex items-center">
                    Browse Vehicles
                    <svg class="w-4 h-4 ml-1 text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>

                <!-- Saved Vehicles (Responsive) -->
                <ResponsiveNavLink
                    v-if="$is('renter') && !needsKycVerification"
                    :href="route('vehicles.saved')"
                    :active="route().current('vehicles.saved')"
                    class="text-white/90 hover:text-white hover:bg-white/10 rounded-lg transition-all duration-200"
                >
                    💾 Saved
                </ResponsiveNavLink>
                <div v-if="$is('renter') && needsKycVerification"
                     @click="openKycModal"
                     class="block px-4 py-2 text-base font-medium cursor-pointer text-white/50 hover:text-white/70 hover:bg-white/10 rounded-lg transition-all duration-200 flex items-center">
                    💾 Saved
                    <svg class="w-4 h-4 ml-1 text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>

                <!-- My Bookings (Responsive) -->
                <ResponsiveNavLink
                    v-if="$is('renter') && !needsKycVerification"
                    :href="route('bookings.index')"
                    :active="route().current('bookings.*')"
                    class="text-white/90 hover:text-white hover:bg-white/10 rounded-lg transition-all duration-200"
                >
                    My Bookings
                </ResponsiveNavLink>
                <div v-if="$is('renter') && needsKycVerification"
                     @click="openKycModal"
                     class="block px-4 py-2 text-base font-medium cursor-pointer text-white/50 hover:text-white/70 hover:bg-white/10 rounded-lg transition-all duration-200 flex items-center">
                    My Bookings
                    <svg class="w-4 h-4 ml-1 text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>

                <!-- My Vehicles (Responsive) -->
                <ResponsiveNavLink
                    v-if="$is('owner') && !needsKycVerification"
                    :href="route('owner.vehicles.index')"
                    :active="route().current('owner.vehicles.index')"
                    class="text-white/90 hover:text-white hover:bg-white/10 rounded-lg transition-all duration-200"
                >
                    My Vehicles
                </ResponsiveNavLink>
                <div v-if="$is('owner') && needsKycVerification"
                     @click="openKycModal"
                     class="block px-4 py-2 text-base font-medium cursor-pointer text-white/50 hover:text-white/70 hover:bg-white/10 rounded-lg transition-all duration-200 flex items-center">
                    My Vehicles
                    <svg class="w-4 h-4 ml-1 text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>

                <!-- Requests (Responsive) -->
                <ResponsiveNavLink
                    v-if="$is('owner') && !needsKycVerification"
                    :href="route('owner.bookings.index')"
                    :active="route().current('owner.bookings.index')"
                    class="text-white/90 hover:text-white hover:bg-white/10 rounded-lg transition-all duration-200"
                >
                    Requests
                </ResponsiveNavLink>
                <div v-if="$is('owner') && needsKycVerification"
                     @click="openKycModal"
                     class="block px-4 py-2 text-base font-medium cursor-pointer text-white/50 hover:text-white/70 hover:bg-white/10 rounded-lg transition-all duration-200 flex items-center">
                    Requests
                    <svg class="w-4 h-4 ml-1 text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
            </div>

            <!-- Responsive Settings Options -->
            <div class="border-t border-white/20 pb-1 pt-4 px-4" :class="{ 'pointer-events-none opacity-50': needsKycVerification }">
                <div class="mb-3">
                    <div class="text-base font-medium text-white">
                        {{ $page.props.auth.user.first_name }}
                        {{ $page.props.auth.user.last_name }}
                    </div>
                    <div class="text-sm font-medium text-white/70">
                        {{ $page.props.auth.user.email }}
                    </div>
                </div>

                <div class="mt-3 space-y-1">
                    <ResponsiveNavLink
                        :href="route('profile.edit')"
                        class="text-white/90 hover:text-white hover:bg-white/10 rounded-lg transition-all duration-200"
                    >
                        Profile
                    </ResponsiveNavLink>
                    <ResponsiveNavLink
                        :href="route('logout')"
                        method="post"
                        as="button"
                        class="text-white/90 hover:text-white hover:bg-white/10 rounded-lg transition-all duration-200"
                    >
                        Log Out
                    </ResponsiveNavLink>
                </div>
            </div>
        </div>
    </nav>

    <!-- KYC Verification Modal -->
    <div v-if="showKycModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="closeKycModal"></div>

            <!-- Modal panel -->
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <!-- Lock icon -->
                            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Driver's License Verification Required
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Please complete your Driver's License verification to access this feature. You need to verify your license to continue using this functionality.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
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
</template>
