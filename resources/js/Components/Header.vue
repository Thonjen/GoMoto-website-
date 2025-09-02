<script setup>
import { ref } from "vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import NavLink from "@/Components/NavLink.vue";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";
import { Link } from "@inertiajs/vue3";
import Footer from "@/Components/Footer.vue"; // import Footer
import KycWarningBanner from "@/Components/KycWarningBanner.vue";
import { useAuthStore } from "@/stores/auth";

const auth = useAuthStore();
console.log("AUTH USER:", auth.user); // ✅ See if role is present and named
const showingNavigationDropdown = ref(false);
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
                    <Link :href="route('dashboard')" class="flex items-center space-x-3 group">
                        <div class="relative">
                            <ApplicationLogo
                                class="block h-10 w-auto text-white transition-transform duration-200 group-hover:scale-110"
                            />
                            <div class="absolute inset-0 rounded-full bg-white/10 backdrop-blur-sm opacity-0 group-hover:opacity-100 transition-opacity duration-200 -z-10"></div>
                        </div>
                    </Link>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-6 sm:ml-10 sm:flex">
                    <NavLink
                        v-if="$is('renter', 'owner')"
                        :href="route('dashboard')"
                        :active="route().current('dashboard')"
                        class="text-white/90 hover:text-white font-medium transition-all duration-200 hover:scale-105"
                    >
                        Dashboard
                    </NavLink>

                    <NavLink
                        v-if="$is('admin')"
                        :href="route('admin.dashboard')"
                        :active="route().current('admin.dashboard')"
                        class="text-white/90 hover:text-white font-medium transition-all duration-200 hover:scale-105"
                    >
                        Admin
                    </NavLink>

                    <NavLink
                        v-if="$is('renter')"
                        :href="route('public.vehicles.index')"
                        :active="route().current('public.vehicles.index')"
                        class="text-white/90 hover:text-white font-medium transition-all duration-200 hover:scale-105"
                    >
                        Browse Vehicles
                    </NavLink>

                    <NavLink
                        v-if="$is('renter')"
                        :href="route('vehicles.saved')"
                        :active="route().current('vehicles.saved')"
                        class="text-white/90 hover:text-white font-medium transition-all duration-200 hover:scale-105"
                    >
                        💾 Saved
                    </NavLink>

                    <NavLink
                        v-if="$is('renter')"
                        :href="route('bookings.index')"
                        :active="route().current('bookings.*')"
                        class="text-white/90 hover:text-white font-medium transition-all duration-200 hover:scale-105"
                    >
                        My Bookings
                    </NavLink>

                    <NavLink
                        v-if="$is('owner')"
                        :href="route('owner.vehicles.index')"
                        :active="route().current('owner.vehicles.index')"
                        class="text-white/90 hover:text-white font-medium transition-all duration-200 hover:scale-105"
                    >
                        My Vehicles
                    </NavLink>

                    <NavLink
                        v-if="$is('owner')"
                        :href="route('owner.bookings.index')"
                        :active="route().current('owner.bookings.index')"
                        class="text-white/90 hover:text-white font-medium transition-all duration-200 hover:scale-105"
                    >
                        Requests
                    </NavLink>
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
                            <DropdownLink :href="route('profile.edit')" class="text-gray-700 hover:text-gray-900">
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
                    @click="showingNavigationDropdown = !showingNavigationDropdown"
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
                <ResponsiveNavLink
                    v-if="$is('renter', 'owner')"
                    :href="route('dashboard')"
                    :active="route().current('dashboard')"
                    class="text-white/90 hover:text-white hover:bg-white/10 rounded-lg transition-all duration-200"
                >
                    Dashboard
                </ResponsiveNavLink>

                <ResponsiveNavLink
                    v-if="$is('admin')"
                    :href="route('admin.dashboard')"
                    :active="route().current('admin.dashboard')"
                    class="text-white/90 hover:text-white hover:bg-white/10 rounded-lg transition-all duration-200"
                >
                    Dashboard
                </ResponsiveNavLink>

                <ResponsiveNavLink
                    v-if="$is('renter')"
                    :href="route('public.vehicles.index')"
                    :active="route().current('public.vehicles.index')"
                    class="text-white/90 hover:text-white hover:bg-white/10 rounded-lg transition-all duration-200"
                >
                    Browse Vehicles
                </ResponsiveNavLink>

                <ResponsiveNavLink
                    v-if="$is('renter')"
                    :href="route('vehicles.saved')"
                    :active="route().current('vehicles.saved')"
                    class="text-white/90 hover:text-white hover:bg-white/10 rounded-lg transition-all duration-200"
                >
                    💾 Saved
                </ResponsiveNavLink>

                <ResponsiveNavLink
                    v-if="$is('renter')"
                    :href="route('bookings.index')"
                    :active="route().current('bookings.*')"
                    class="text-white/90 hover:text-white hover:bg-white/10 rounded-lg transition-all duration-200"
                >
                    My Bookings
                </ResponsiveNavLink>

                <ResponsiveNavLink
                    v-if="$is('owner')"
                    :href="route('owner.vehicles.index')"
                    :active="route().current('owner.vehicles.index')"
                    class="text-white/90 hover:text-white hover:bg-white/10 rounded-lg transition-all duration-200"
                >
                    My Vehicles
                </ResponsiveNavLink>

                <ResponsiveNavLink
                    v-if="$is('owner')"
                    :href="route('owner.bookings.index')"
                    :active="route().current('owner.bookings.index')"
                    class="text-white/90 hover:text-white hover:bg-white/10 rounded-lg transition-all duration-200"
                >
                    Requests
                </ResponsiveNavLink>
            </div>

            <!-- Responsive Settings Options -->
            <div class="border-t border-white/20 pb-1 pt-4 px-4">
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
                    <ResponsiveNavLink :href="route('profile.edit')" class="text-white/90 hover:text-white hover:bg-white/10 rounded-lg transition-all duration-200">
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
</template>
