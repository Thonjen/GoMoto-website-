<template>
  <OwnerLayout>
          <!-- Main Content -->
      <div class="md:col-span-3">
        <div class="glass-card p-6 shadow-glow border border-white/20">
          <h1 class="text-3xl font-bold text-white mb-6">Payment Settings</h1>
          
          <div v-if="$page.props.errors.payment_methods" class="mb-4 p-4 bg-red-500/20 border-l-4 border-red-400 text-red-300 backdrop-blur-sm rounded">
            {{ $page.props.errors.payment_methods }}
          </div>

          <form @submit.prevent="updateSettings" class="space-y-6">
            <!-- Cash on Delivery -->
            <div class="flex items-start space-x-3">
              <div class="flex items-center h-5">
                <input 
                  id="accepts_cod"
                  v-model="form.accepts_cod"
                  type="checkbox"
                  class="w-4 h-4 text-primary-500 bg-white/10 border-white/30 rounded focus:ring-primary-400 focus:ring-2 backdrop-blur-sm"
                />
              </div>
              <div class="ml-3 text-sm">
                <label for="accepts_cod" class="font-medium text-white">
                  Accept Cash on Delivery (COD)
                </label>
                <p class="text-white/70">
                  Allow renters to pay cash when they pick up the vehicle
                </p>
              </div>
            </div>

            <!-- GCash QR -->
            <div class="flex items-start space-x-3">
              <div class="flex items-center h-5">
                <input 
                  id="accepts_gcash"
                  v-model="form.accepts_gcash"
                  type="checkbox"
                  :disabled="!user.has_gcash_qr"
                  class="w-4 h-4 text-primary-500 bg-white/10 border-white/30 rounded focus:ring-primary-400 focus:ring-2 disabled:opacity-50 disabled:cursor-not-allowed backdrop-blur-sm"
                />
              </div>
              <div class="ml-3 text-sm">
                <label for="accepts_gcash" class="font-medium text-white">
                  Accept GCash QR Payments
                </label>
                <p class="text-white/70">
                  Allow renters to pay via GCash using your QR code
                </p>
                <div v-if="!user.has_gcash_qr" class="mt-2">
                  <p class="text-amber-600 text-sm">
                    <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    You need to upload a GCash QR code first.
                        <NavLink
        :href="route('owner.gcash-qr.show')"
        class="!text-blue-600 ml-1"    >
        Upload here
    </NavLink>
                  </p>

                </div>
                <div v-else-if="user.gcash_qr_url" class="mt-2">
                  <p class="text-green-400 text-sm">
                    <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    GCash QR code is uploaded and ready to use
                  </p>
                </div>
                <div v-if="$page.props.errors.accepts_gcash" class="mt-2 text-red-400 text-sm">
                  {{ $page.props.errors.accepts_gcash }}
                </div>
              </div>
            </div>

            <!-- Current GCash QR Preview (if available) -->
            <div v-if="user.has_gcash_qr && user.gcash_qr_url" class="mt-4">
              <label class="block text-sm font-medium text-white mb-2">
                Current GCash QR Code:
              </label>
              <div class="max-w-xs">
                <img 
                  :src="user.gcash_qr_url" 
                  alt="GCash QR Code"
                  class="w-full h-auto border border-white/20 rounded-lg shadow-glow"
                />
              </div>
            </div>

            <!-- Warning about payment methods -->
            <div class="bg-yellow-500/20 border-l-4 border-yellow-400 p-4 backdrop-blur-sm rounded">
              <div class="flex">
                <div class="flex-shrink-0">
                  <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                  </svg>
                </div>
                <div class="ml-3">
                  <p class="text-sm text-yellow-300">
                    <strong>Important:</strong> You must enable at least one payment method. 
                    Renters will only see the payment options you have enabled when booking your vehicles.
                  </p>
                </div>
              </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end">
              <button
                type="submit"
                :disabled="processing"
                class="btn-primary py-2 px-4 text-sm font-medium disabled:opacity-50 disabled:cursor-not-allowed"
              >
                <svg v-if="processing" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ processing ? 'Saving...' : 'Save Settings' }}
              </button>
            </div>
          </form>
        </div>
        </div>
  </OwnerLayout>
</template>

<script setup>
import NavLink from "@/Components/NavLink.vue";

import { ref, reactive } from 'vue'
import { Link, router, useForm } from '@inertiajs/vue3'
import OwnerLayout from '@/Layouts/OwnerLayout.vue'

const props = defineProps({
  user: Object
})

const form = useForm({
  accepts_cod: props.user.accepts_cod,
  accepts_gcash: props.user.accepts_gcash,
})

const processing = ref(false)

const updateSettings = () => {
  processing.value = true
  
  form.post(route('owner.payment-settings.update'), {
    onSuccess: () => {
      processing.value = false
    },
    onError: () => {
      processing.value = false
    }
  })
}
</script>
