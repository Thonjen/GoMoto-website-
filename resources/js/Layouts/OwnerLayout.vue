<script setup>
import { ref } from "vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import NavLink from "@/Components/NavLink.vue";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";
import { Link } from "@inertiajs/vue3";
import Footer from "@/Components/Footer.vue"; // import Footer
import Header from "@/Components/Header.vue";
const showingNavigationDropdown = ref(false);
</script>

<template>
    <div>
        <div class="min-h-screen bg-gray-100">
            <Header />

            <!-- Page Heading -->
            <header class="bg-white shadow" v-if="$slots.header">
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div class="mt-4 grid grid-cols-1 md:grid-cols-4 gap-6">
                        <aside
                            class="md:col-span-1 bg-white p-6 rounded-lg shadow-md h-fit"
                        >
                            <h2
                                class="text-xl font-semibold text-gray-800 mb-4"
                            >
                                Owner Tools
                            </h2>
                            <nav class="flex flex-col gap-2">
                                <NavLink
                                    :href="route('owner.vehicles.index')"
                                    :active="route().current('owner.vehicles.index')"
                                >
                                    My Vehicles
                                </NavLink>

                                <NavLink
                                    :href="route('owner.vehicles.create')"
                                    :active="route().current('owner.vehicles.create')"
                                >
                                    Add New Vehicle
                                </NavLink>

                                <NavLink
                                    :href="route('owner.gcash-qr.show')"
                                    :active="route().current('owner.gcash-qr.show')"
                                >
                                    Upload GCASH QR
                                </NavLink>

                                                                <NavLink
                                    :href="route('owner.payment-settings.show')"
                                    :active="route().current('owner.payment-settings.show')"
                                >
                                    Payment System
                                </NavLink>




                        <NavLink
                            v-if="$is('owner')"
                            :href="route('owner.bookings.index')"
                            :active="route().current('owner.bookings.*')"
                        >
                            Booking Requests
                        </NavLink>

                            </nav>
                        </aside>

                        <div
                            class="md:col-span-3 bg-white p-6 rounded-lg shadow-md"
                        >
                            <slot />
                        </div>
                    </div>
                </div>
            </main>

            <Footer />
        </div>
    </div>
</template>
