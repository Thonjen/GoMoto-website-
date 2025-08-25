<template>
    
    <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm">
        <h4 class="text-lg font-medium text-gray-900 mb-4">
            Extension Settings
        </h4>
        
        <form @submit.prevent="updateSettings" class="space-y-4">
            <div class="flex items-center space-x-3">
                <input
                    type="checkbox"
                    :id="`allow-extensions-${vehicle.id}`"
                    v-model="form.allow_extensions"
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                />
                <label :for="`allow-extensions-${vehicle.id}`" class="text-sm font-medium text-gray-700">
                    Allow booking extensions for this vehicle
                </label>
            </div>

            <div v-if="form.allow_extensions" class="space-y-4 ml-7">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Maximum extension hours
                    </label>
                    <select
                        v-model="form.max_extension_hours"
                        class="block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    >
                        <option value="12">12 hours</option>
                        <option value="24">24 hours (1 day)</option>
                        <option value="48">48 hours (2 days)</option>
                        <option value="72">72 hours (3 days)</option>
                        <option value="168">168 hours (1 week)</option>
                    </select>
                </div>

                <div class="flex items-center space-x-3">
                    <input
                        type="checkbox"
                        :id="`require-approval-${vehicle.id}`"
                        v-model="form.require_approval_for_extensions"
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                    />
                    <label :for="`require-approval-${vehicle.id}`" class="text-sm font-medium text-gray-700">
                        Require owner approval for extensions
                    </label>
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded-md p-3">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">
                                Extension Policy
                            </h3>
                            <div class="mt-2 text-sm text-blue-700">
                                <p v-if="form.require_approval_for_extensions">
                                    Renters will submit extension requests that you can approve or reject. You'll be notified of new requests.
                                </p>
                                <p v-else>
                                    Renters can extend their bookings instantly up to {{ form.max_extension_hours }} hours by paying the additional amount.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50"
                >
                    {{ form.processing ? 'Saving...' : 'Save Settings' }}
                </button>
            </div>
        </form>
    </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import OwnerLayout from '@/Layouts/OwnerLayout.vue'

const props = defineProps({
    vehicle: Object,
})

const form = useForm({
    allow_extensions: props.vehicle.allow_extensions ?? true,
    max_extension_hours: props.vehicle.max_extension_hours ?? 72,
    require_approval_for_extensions: props.vehicle.require_approval_for_extensions ?? true,
})

const updateSettings = () => {
    form.post(route('owner.vehicles.extensionSettings', props.vehicle.id), {
        onSuccess: () => {
            // Success message will be handled by flash messages
        }
    })
}
</script>
