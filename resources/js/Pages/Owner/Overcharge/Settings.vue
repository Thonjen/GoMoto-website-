<template>
    <Head title="Overcharge Settings" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Overcharge Settings
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
                
                <!-- Introduction -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <h3 class="text-sm font-medium text-blue-900">Overcharge Protection</h3>
                            <p class="text-sm text-blue-700">Charge renters for late returns or out-of-city use to protect your vehicles</p>
                        </div>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="bg-white rounded-lg shadow-sm border p-6" v-if="stats">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Your Overcharge Summary</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="text-center p-3 bg-green-50 rounded-lg">
                            <div class="text-2xl font-bold text-green-600">₱{{ formatCurrency(stats.total_overcharges || 0) }}</div>
                            <div class="text-xs text-gray-500">Total Earned</div>
                        </div>
                        <div class="text-center p-3 bg-yellow-50 rounded-lg">
                            <div class="text-2xl font-bold text-yellow-600">₱{{ formatCurrency(stats.unpaid_overcharges || 0) }}</div>
                            <div class="text-xs text-gray-500">Unpaid</div>
                        </div>
                        <div class="text-center p-3 bg-red-50 rounded-lg">
                            <div class="text-2xl font-bold text-red-600">{{ stats.late_returns_count || 0 }}</div>
                            <div class="text-xs text-gray-500">Late Returns</div>
                        </div>
                        <div class="text-center p-3 bg-blue-50 rounded-lg">
                            <div class="text-2xl font-bold text-blue-600">{{ stats.out_of_city_count || 0 }}</div>
                            <div class="text-xs text-gray-500">Out of City</div>
                        </div>
                    </div>
                </div>

                <!-- Settings Form -->
                <div class="bg-white rounded-lg shadow-sm border p-6">
                    <form @submit.prevent="updateSettings" class="space-y-6">
                        
                        <!-- Enable/Disable Toggle -->
                        <div class="flex items-center justify-between pb-4 border-b">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">Enable Overcharges</h3>
                                <p class="text-sm text-gray-600">Turn on automatic overcharge calculations for all your vehicles</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input 
                                    type="checkbox" 
                                    v-model="form.enable_overcharges"
                                    class="sr-only peer"
                                />
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>

                        <div v-if="form.enable_overcharges" class="space-y-8">
                            
                            <!-- Grace Period -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Grace Period (minutes)
                                </label>
                                <p class="text-xs text-gray-500 mb-3">
                                    How many minutes late before charging overcharges
                                </p>
                                <input
                                    type="number"
                                    min="0"
                                    max="720"
                                    v-model="form.grace_period_minutes"
                                    class="block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    placeholder="30"
                                />
                            </div>

                            <!-- Late Return Rate -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Late Return Rate (₱ per hour)
                                </label>
                                <p class="text-xs text-gray-500 mb-3">
                                    How much to charge for each hour the vehicle is returned late
                                </p>
                                <div class="relative">
                                    <input
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        v-model="form.late_return_rate"
                                        class="block w-full pl-8 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        placeholder="100.00"
                                    />
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                                        <span class="text-gray-500">₱</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Out of City Settings -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Out of City Base Fee (₱)
                                    </label>
                                    <p class="text-xs text-gray-500 mb-3">
                                        Fixed fee for returning outside city limits
                                    </p>
                                    <div class="relative">
                                        <input
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            v-model="form.out_of_city_base"
                                            class="block w-full pl-8 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                            placeholder="500.00"
                                        />
                                        <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                                            <span class="text-gray-500">₱</span>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Per Kilometer Rate (₱)
                                    </label>
                                    <p class="text-xs text-gray-500 mb-3">
                                        Additional charge per km from city center
                                    </p>
                                    <div class="relative">
                                        <input
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            v-model="form.out_of_city_rate"
                                            class="block w-full pl-8 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                            placeholder="25.00"
                                        />
                                        <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                                            <span class="text-gray-500">₱</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Example Preview -->
                            <div class="bg-gray-50 rounded-lg p-4">
                                <h4 class="text-sm font-medium text-gray-900 mb-2">Examples</h4>
                                <div class="text-sm text-gray-600 space-y-1">
                                    <div>• 2 hours late: ₱{{ (2 * (parseFloat(form.late_return_rate) || 0)).toFixed(2) }}</div>
                                    <div>• Returned 10km outside city: ₱{{ ((parseFloat(form.out_of_city_base) || 0) + (10 * (parseFloat(form.out_of_city_rate) || 0))).toFixed(2) }}</div>
                                </div>
                            </div>

                            <!-- Instructions for Renters -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Instructions for Renters (Optional)
                                </label>
                                <p class="text-xs text-gray-500 mb-3">
                                    These instructions will be shown to renters when they book your vehicles
                                </p>
                                <textarea
                                    v-model="form.overcharge_instructions"
                                    rows="3"
                                    class="block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    placeholder="Please return the vehicle on time and within city limits to avoid additional charges."
                                ></textarea>
                            </div>

                        </div>

                        <!-- Save Button -->
                        <div class="flex items-center justify-end pt-6 border-t">
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                <span v-if="form.processing">Saving...</span>
                                <span v-else>Save Settings</span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Recent Overcharges (if any) -->
                <div class="bg-white rounded-lg shadow-sm border p-6" v-if="recentOvercharges && recentOvercharges.length > 0">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Recent Overcharges</h3>
                        <Link :href="route('owner.overcharges.stats')" class="text-blue-600 hover:text-blue-800 text-sm">
                            View All →
                        </Link>
                    </div>
                    <div class="space-y-3">
                        <div v-for="overcharge in recentOvercharges" :key="overcharge.id" 
                             class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <div class="text-sm font-medium text-gray-900">
                                    Booking #{{ overcharge.booking_id }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ overcharge.details }} • {{ formatDate(overcharge.occurred_at) }}
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-medium text-gray-900">₱{{ formatCurrency(overcharge.amount) }}</div>
                                <div class="text-xs" :class="overcharge.is_paid ? 'text-green-600' : 'text-red-600'">
                                    {{ overcharge.is_paid ? 'Paid' : 'Unpaid' }}
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
import { Head, Link, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
    user: Object,
    vehicles: Array,
    stats: Object,
    recentOvercharges: Array
})

const form = useForm({
    enable_overcharges: props.user.enable_overcharges || false,
    late_return_rate: props.user.late_return_rate || 100.00,
    out_of_city_base: props.user.out_of_city_base || 500.00,
    out_of_city_rate: props.user.out_of_city_rate || 25.00,
    grace_period_minutes: props.user.grace_period_minutes || 30,
    overcharge_instructions: props.user.overcharge_instructions || ''
})

const updateSettings = () => {
    form.post(route('owner.overcharges.updateSettings'), {
        preserveScroll: true,
        onSuccess: () => {
            // Success message handled by flash message
        }
    })
}

const formatCurrency = (amount) => {
    return parseFloat(amount || 0).toFixed(2)
}

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    })
}
</script>
                        
                    