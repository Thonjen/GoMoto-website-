<template>
    <Head title="Overcharge Statistics" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Overcharge Statistics
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                
                <!-- Back to Settings -->
                <div>
                    <Link :href="route('owner.overcharges.index')" 
                          class="inline-flex items-center text-blue-600 hover:text-blue-800 text-sm">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        Back to Settings
                    </Link>
                </div>

                <!-- Statistics Summary -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Summary</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="bg-green-50 rounded-lg p-4 text-center">
                                <div class="text-2xl font-bold text-green-600">
                                    ₱{{ formatCurrency(stats.total_overcharges) }}
                                </div>
                                <div class="text-sm text-gray-500">Total Earned</div>
                            </div>
                            <div class="bg-yellow-50 rounded-lg p-4 text-center">
                                <div class="text-2xl font-bold text-yellow-600">
                                    ₱{{ formatCurrency(stats.unpaid_overcharges) }}
                                </div>
                                <div class="text-sm text-gray-500">Unpaid</div>
                            </div>
                            <div class="bg-red-50 rounded-lg p-4 text-center">
                                <div class="text-2xl font-bold text-red-600">
                                    {{ stats.late_returns_count }}
                                </div>
                                <div class="text-sm text-gray-500">Late Returns</div>
                            </div>
                            <div class="bg-blue-50 rounded-lg p-4 text-center">
                                <div class="text-2xl font-bold text-blue-600">
                                    {{ stats.out_of_city_count }}
                                </div>
                                <div class="text-sm text-gray-500">Out of City</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">
                            Recent Overcharges
                        </h3>

                        <div class="overflow-x-auto">
                            <div v-if="!overcharges.data || overcharges.data.length === 0" class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <p class="mt-2 text-lg font-medium text-gray-900">No overcharges yet</p>
                                <p class="mt-1 text-gray-500">When renters return vehicles late or outside the city, overcharges will appear here.</p>
                            </div>
                            <table v-else class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Booking / Vehicle
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Type
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Details
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Amount
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Date
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="overcharge in overcharges.data" :key="overcharge.id">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                Booking #{{ overcharge.booking.id }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ overcharge.booking.vehicle?.brand?.name || 'Unknown Brand' }} 
                                                {{ overcharge.booking.vehicle?.vehicle_type?.name || 'Unknown Type' }}
                                                <span v-if="overcharge.booking.vehicle?.license_plate" class="text-xs bg-gray-100 px-1 rounded">
                                                    {{ overcharge.booking.vehicle.license_plate }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span :class="getTypeColor(overcharge.overcharge_type.name)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                                {{ overcharge.overcharge_type.description }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ overcharge.details }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            ₱{{ formatCurrency(overcharge.amount) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span :class="overcharge.is_paid ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" 
                                                  class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                                {{ overcharge.is_paid ? 'Paid' : 'Unpaid' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ formatDate(overcharge.occurred_at) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <button 
                                                v-if="!overcharge.is_paid"
                                                @click="markAsPaid(overcharge.id)"
                                                class="text-green-600 hover:text-green-900"
                                            >
                                                Mark as Paid
                                            </button>
                                            <span v-else class="text-gray-400">
                                                Paid
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-4" v-if="overcharges.links && overcharges.links.length > 0">
                            <nav class="flex items-center justify-between">
                                <div class="flex-1 flex justify-between sm:hidden">
                                    <Link v-if="overcharges.prev_page_url" :href="overcharges.prev_page_url" 
                                          class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                        Previous
                                    </Link>
                                    <Link v-if="overcharges.next_page_url" :href="overcharges.next_page_url" 
                                          class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                        Next
                                    </Link>
                                </div>
                                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                    <div v-if="overcharges.from && overcharges.to && overcharges.total">
                                        <p class="text-sm text-gray-700">
                                            Showing {{ overcharges.from }} to {{ overcharges.to }} of {{ overcharges.total }} results
                                        </p>
                                    </div>
                                    <div>
                                        <span class="relative z-0 inline-flex shadow-sm rounded-md">
                                            <template v-for="(link, index) in (overcharges.links || [])" :key="index">
                                                <Link v-if="link && link.url"
                                                      :href="link.url"
                                                      :class="link.active ? 'bg-blue-50 border-blue-500 text-blue-600' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'"
                                                      class="relative inline-flex items-center px-4 py-2 border text-sm font-medium"
                                                      v-html="link.label">
                                                </Link>
                                                <span v-else-if="link && !link.url && link.label"
                                                      :class="link.active ? 'bg-blue-50 border-blue-500 text-blue-600' : 'bg-white border-gray-300 text-gray-500'"
                                                      class="relative inline-flex items-center px-4 py-2 border text-sm font-medium cursor-default"
                                                      v-html="link.label">
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
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
    overcharges: Object,
    stats: Object,
})

const getTypeColor = (typeName) => {
    switch (typeName) {
        case 'late_return':
            return 'bg-red-100 text-red-800'
        case 'out_of_city':
            return 'bg-blue-100 text-blue-800'
        default:
            return 'bg-gray-100 text-gray-800'
    }
}

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    })
}

const formatCurrency = (amount) => {
    return parseFloat(amount || 0).toFixed(2)
}

const markAsPaid = (overchargeId) => {
    if (confirm('Mark this overcharge as paid?')) {
        router.post(route('owner.overcharges.markPaid', overchargeId), {}, {
            onSuccess: () => {
                // Success message handled by flash message
            }
        })
    }
}
</script>
