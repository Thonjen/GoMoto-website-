<template>
    <Head title="Overcharge Statistics" />

    <OwnerLayout>
        <div class="glass-card-dark overflow-hidden shadow-glow sm:rounded-lg">
            <div class="p-6 text-white">
                <div class="mb-6">
                    <h1 class="text-3xl font-bold text-white">Overcharge Statistics</h1>
                    <p class="text-white/80 mt-2">
                        See and manage your overcharge settings
                    </p>
                </div>
                <!-- Statistics Summary -->
                <div
                    class="glass-card border border-white/20 rounded-lg shadow-glow mb-6"
                >
                    <div
                        class="glass-card overflow-hidden shadow-sm sm:rounded-lg"
                    >
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-white mb-4">
                                Summary
                            </h3>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <div
                                    class="bg-green-400/20 backdrop-blur-sm rounded-lg p-4 text-center border border-green-400/30"
                                >
                                    <div
                                        class="text-2xl font-bold text-green-400"
                                    >
                                        ₱{{
                                            formatCurrency(
                                                stats.total_overcharges
                                            )
                                        }}
                                    </div>
                                    <div class="text-sm text-white/70">
                                        Total Earned
                                    </div>
                                </div>
                                <div
                                    class="bg-yellow-400/20 backdrop-blur-sm rounded-lg p-4 text-center border border-yellow-400/30"
                                >
                                    <div
                                        class="text-2xl font-bold text-yellow-400"
                                    >
                                        ₱{{
                                            formatCurrency(
                                                stats.unpaid_overcharges
                                            )
                                        }}
                                    </div>
                                    <div class="text-sm text-white/70">
                                        Unpaid
                                    </div>
                                </div>
                                <div
                                    class="bg-red-400/20 backdrop-blur-sm rounded-lg p-4 text-center border border-red-400/30"
                                >
                                    <div
                                        class="text-2xl font-bold text-red-400"
                                    >
                                        {{ stats.late_returns_count }}
                                    </div>
                                    <div class="text-sm text-white/70">
                                        Late Returns
                                    </div>
                                </div>
                                <div
                                    class="bg-blue-400/20 backdrop-blur-sm rounded-lg p-4 text-center border border-blue-400/30"
                                >
                                    <div
                                        class="text-2xl font-bold text-blue-400"
                                    >
                                        {{ stats.out_of_city_count }}
                                    </div>
                                    <div class="text-sm text-white/70">
                                        Out of City
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    class="glass-card border border-white/20 rounded-lg shadow-glow"
                >
                    <div
                        class="glass-card overflow-hidden shadow-sm sm:rounded-lg"
                    >
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-white mb-4">
                                Recent Overcharges
                            </h3>

                            <div class="overflow-x-auto">
                                <div
                                    v-if="
                                        !overcharges.data ||
                                        overcharges.data.length === 0
                                    "
                                    class="text-center py-8"
                                >
                                    <svg
                                        class="mx-auto h-12 w-12 text-white/40"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                        />
                                    </svg>
                                    <p
                                        class="mt-2 text-lg font-medium text-white"
                                    >
                                        No overcharges yet
                                    </p>
                                    <p class="mt-1 text-white/60">
                                        When renters return vehicles late or
                                        outside the city, overcharges will
                                        appear here.
                                    </p>
                                </div>
                                <table
                                    v-else
                                    class="min-w-full divide-y divide-white/10"
                                >
                                    <thead class="bg-white/5">
                                        <tr>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-white/70 uppercase tracking-wider"
                                            >
                                                Booking / Vehicle
                                            </th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-white/70 uppercase tracking-wider"
                                            >
                                                Type
                                            </th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-white/70 uppercase tracking-wider"
                                            >
                                                Details
                                            </th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-white/70 uppercase tracking-wider"
                                            >
                                                Amount
                                            </th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-white/70 uppercase tracking-wider"
                                            >
                                                Status
                                            </th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-white/70 uppercase tracking-wider"
                                            >
                                                Date
                                            </th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-white/70 uppercase tracking-wider"
                                            >
                                                Actions
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody
                                        class="divide-y divide-white/10"
                                    >
                                        <tr
                                            v-for="overcharge in overcharges.data"
                                            :key="overcharge.id"
                                            class="hover:bg-white/5 transition-colors"
                                        >
                                            <td
                                                class="px-6 py-4 whitespace-nowrap"
                                            >
                                                <div
                                                    class="text-sm font-medium text-white"
                                                >
                                                    Booking #{{
                                                        overcharge.booking.id
                                                    }}
                                                </div>
                                                <div
                                                    class="text-sm text-white/60"
                                                >
                                                    {{
                                                        overcharge.booking
                                                            .vehicle?.brand
                                                            ?.name ||
                                                        "Unknown Brand"
                                                    }}
                                                    {{
                                                        overcharge.booking
                                                            .vehicle
                                                            ?.vehicle_type
                                                            ?.name ||
                                                        "Unknown Type"
                                                    }}
                                                    <span
                                                        v-if="
                                                            overcharge.booking
                                                                .vehicle
                                                                ?.license_plate
                                                        "
                                                        class="text-xs bg-white/10 px-1 rounded text-white/80"
                                                    >
                                                        {{
                                                            overcharge.booking
                                                                .vehicle
                                                                .license_plate
                                                        }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap"
                                            >
                                                <span
                                                    :class="
                                                        getTypeColor(
                                                            overcharge
                                                                .overcharge_type
                                                                .name
                                                        )
                                                    "
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                                >
                                                    {{
                                                        overcharge
                                                            .overcharge_type
                                                            .description
                                                    }}
                                                </span>
                                            </td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm text-white"
                                            >
                                                {{ overcharge.details }}
                                            </td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm font-medium text-white"
                                            >
                                                ₱{{
                                                    formatCurrency(
                                                        overcharge.amount
                                                    )
                                                }}
                                            </td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap"
                                            >
                                                <span
                                                    :class="
                                                        overcharge.is_paid
                                                            ? 'bg-green-400/20 text-green-400 border-green-400/30'
                                                            : 'bg-red-400/20 text-red-400 border-red-400/30'
                                                    "
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full border backdrop-blur-sm"
                                                >
                                                    {{
                                                        overcharge.is_paid
                                                            ? "Paid"
                                                            : "Unpaid"
                                                    }}
                                                </span>
                                            </td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm text-white/70"
                                            >
                                                {{
                                                    formatDate(
                                                        overcharge.occurred_at
                                                    )
                                                }}
                                            </td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm font-medium"
                                            >
                                                <button
                                                    v-if="!overcharge.is_paid"
                                                    @click="
                                                        markAsPaid(
                                                            overcharge.id
                                                        )
                                                    "
                                                    class="text-green-400 hover:text-green-300 transition-colors"
                                                >
                                                    Mark as Paid
                                                </button>
                                                <span
                                                    v-else
                                                    class="text-white/40"
                                                >
                                                    Paid
                                                </span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div
                                class="mt-4"
                                v-if="
                                    overcharges.links &&
                                    overcharges.links.length > 0
                                "
                            >
                                <nav class="flex items-center justify-between">
                                    <div
                                        class="flex-1 flex justify-between sm:hidden"
                                    >
                                        <Link
                                            v-if="overcharges.prev_page_url"
                                            :href="overcharges.prev_page_url"
                                            class="relative inline-flex items-center px-4 py-2 border border-white/20 text-sm font-medium rounded-md text-white bg-white/10 hover:bg-white/20 backdrop-blur-sm transition-colors"
                                        >
                                            Previous
                                        </Link>
                                        <Link
                                            v-if="overcharges.next_page_url"
                                            :href="overcharges.next_page_url"
                                            class="ml-3 relative inline-flex items-center px-4 py-2 border border-white/20 text-sm font-medium rounded-md text-white bg-white/10 hover:bg-white/20 backdrop-blur-sm transition-colors"
                                        >
                                            Next
                                        </Link>
                                    </div>
                                    <div
                                        class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between"
                                    >
                                        <div
                                            v-if="
                                                overcharges.from &&
                                                overcharges.to &&
                                                overcharges.total
                                            "
                                        >
                                            <p class="text-sm text-white/70">
                                                Showing
                                                {{ overcharges.from }} to
                                                {{ overcharges.to }} of
                                                {{ overcharges.total }} results
                                            </p>
                                        </div>
                                        <div>
                                            <span
                                                class="relative z-0 inline-flex shadow-sm rounded-md"
                                            >
                                                <template
                                                    v-for="(
                                                        link, index
                                                    ) in overcharges.links ||
                                                    []"
                                                    :key="index"
                                                >
                                                    <Link
                                                        v-if="link && link.url"
                                                        :href="link.url"
                                                        :class="
                                                            link.active
                                                                ? 'bg-blue-400/20 border-blue-400/30 text-blue-400'
                                                                : 'bg-white/10 border-white/20 text-white/70 hover:bg-white/20'
                                                        "
                                                        class="relative inline-flex items-center px-4 py-2 border text-sm font-medium backdrop-blur-sm transition-colors"
                                                        v-html="link.label"
                                                    >
                                                    </Link>
                                                    <span
                                                        v-else-if="
                                                            link &&
                                                            !link.url &&
                                                            link.label
                                                        "
                                                        :class="
                                                            link.active
                                                                ? 'bg-blue-400/20 border-blue-400/30 text-blue-400'
                                                                : 'bg-white/10 border-white/20 text-white/50'
                                                        "
                                                        class="relative inline-flex items-center px-4 py-2 border text-sm font-medium cursor-default backdrop-blur-sm"
                                                        v-html="link.label"
                                                    >
                                                    </span>
                                                </template>
                                            </span>
                                        </div>
                                    </div>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </OwnerLayout>
</template>

<script setup>
import { Head, Link } from "@inertiajs/vue3";
import { router } from "@inertiajs/vue3";
import OwnerLayout from "@/Layouts/OwnerLayout.vue";

const props = defineProps({
    overcharges: Object,
    stats: Object,
});

const getTypeColor = (typeName) => {
    switch (typeName) {
        case "late_return":
            return "bg-red-100 text-red-800";
        case "out_of_city":
            return "bg-blue-100 text-blue-800";
        default:
            return "bg-gray-100 text-gray-800";
    }
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString("en-US", {
        year: "numeric",
        month: "short",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
};

const formatCurrency = (amount) => {
    return parseFloat(amount || 0).toFixed(2);
};

const markAsPaid = (overchargeId) => {
    if (confirm("Mark this overcharge as paid?")) {
        router.post(
            route("owner.overcharges.markPaid", overchargeId),
            {},
            {
                onSuccess: () => {
                    // Success message handled by flash message
                },
            }
        );
    }
};
</script>
