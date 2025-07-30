<template>
  <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
      <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

      <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

      <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
          <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Complete Payment</h3>
            <button @click="$emit('close')" class="text-gray-400 hover:text-gray-600">
              <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>

          <!-- Booking Summary -->
          <div class="bg-gray-50 p-4 rounded-lg mb-6">
            <h4 class="font-semibold text-gray-900 mb-3">Booking Details</h4>
            <div class="space-y-2 text-sm">
              <div class="flex justify-between">
                <span>Vehicle:</span>
                <span>{{ booking.vehicle.brand.name }} {{ booking.vehicle.year }}</span>
              </div>
              <div class="flex justify-between">
                <span>Duration:</span>
                <span>{{ booking.duration_in_days }} day(s)</span>
              </div>
              <div class="flex justify-between">
                <span>Pickup:</span>
                <span>{{ formatDateTime(booking.start_datetime) }}</span>
              </div>
              <div class="flex justify-between">
                <span>Return:</span>
                <span>{{ formatDateTime(booking.end_datetime) }}</span>
              </div>
              <div class="border-t pt-2 flex justify-between font-bold text-lg">
                <span>Total Amount:</span>
                <span class="text-blue-600">₱{{ booking.total_amount }}</span>
              </div>
            </div>
          </div>

          <!-- Payment Methods -->
          <div class="mb-6">
            <h4 class="font-semibold text-gray-900 mb-4">Select Payment Method</h4>
            <div class="space-y-3">
              <!-- Credit/Debit Card -->
              <label class="relative">
                <input
                  v-model="selectedPaymentMethod"
                  type="radio"
                  value="card"
                  class="sr-only"
                />
                <div :class="[
                  'cursor-pointer rounded-lg border p-4 flex items-center',
                  selectedPaymentMethod === 'card' ? 'border-blue-500 bg-blue-50' : 'border-gray-300'
                ]">
                  <svg class="h-6 w-6 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                  </svg>
                  <div>
                    <div class="font-medium">Credit/Debit Card</div>
                    <div class="text-sm text-gray-500">Visa, Mastercard, JCB</div>
                  </div>
                </div>
              </label>

              <!-- PayMaya -->
              <label class="relative">
                <input
                  v-model="selectedPaymentMethod"
                  type="radio"
                  value="paymaya"
                  class="sr-only"
                />
                <div :class="[
                  'cursor-pointer rounded-lg border p-4 flex items-center',
                  selectedPaymentMethod === 'paymaya' ? 'border-blue-500 bg-blue-50' : 'border-gray-300'
                ]">
                  <div class="h-6 w-6 bg-green-500 rounded mr-3 flex items-center justify-center">
                    <span class="text-white text-xs font-bold">PM</span>
                  </div>
                  <div>
                    <div class="font-medium">PayMaya</div>
                    <div class="text-sm text-gray-500">Digital wallet payment</div>
                  </div>
                </div>
              </label>

              <!-- GCash -->
              <label class="relative">
                <input
                  v-model="selectedPaymentMethod"
                  type="radio"
                  value="gcash"
                  class="sr-only"
                />
                <div :class="[
                  'cursor-pointer rounded-lg border p-4 flex items-center',
                  selectedPaymentMethod === 'gcash' ? 'border-blue-500 bg-blue-50' : 'border-gray-300'
                ]">
                  <div class="h-6 w-6 bg-blue-500 rounded mr-3 flex items-center justify-center">
                    <span class="text-white text-xs font-bold">G</span>
                  </div>
                  <div>
                    <div class="font-medium">GCash</div>
                    <div class="text-sm text-gray-500">Mobile wallet payment</div>
                  </div>
                </div>
              </label>

              <!-- Bank Transfer -->
              <label class="relative">
                <input
                  v-model="selectedPaymentMethod"
                  type="radio"
                  value="bank_transfer"
                  class="sr-only"
                />
                <div :class="[
                  'cursor-pointer rounded-lg border p-4 flex items-center',
                  selectedPaymentMethod === 'bank_transfer' ? 'border-blue-500 bg-blue-50' : 'border-gray-300'
                ]">
                  <svg class="h-6 w-6 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/>
                  </svg>
                  <div>
                    <div class="font-medium">Bank Transfer</div>
                    <div class="text-sm text-gray-500">Direct bank transfer</div>
                  </div>
                </div>
              </label>

              <!-- Cash on Pickup -->
              <label class="relative">
                <input
                  v-model="selectedPaymentMethod"
                  type="radio"
                  value="cash"
                  class="sr-only"
                />
                <div :class="[
                  'cursor-pointer rounded-lg border p-4 flex items-center',
                  selectedPaymentMethod === 'cash' ? 'border-blue-500 bg-blue-50' : 'border-gray-300'
                ]">
                  <svg class="h-6 w-6 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                  </svg>
                  <div>
                    <div class="font-medium">Cash on Pickup</div>
                    <div class="text-sm text-gray-500">Pay when you collect the vehicle</div>
                  </div>
                </div>
              </label>
            </div>
          </div>

          <!-- Card Details Form (shown when card is selected) -->
          <div v-if="selectedPaymentMethod === 'card'" class="mb-6 p-4 border rounded-lg">
            <h5 class="font-medium text-gray-900 mb-4">Card Details</h5>
            <form @submit.prevent="processCardPayment" class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Card Number</label>
                <input 
                  v-model="cardForm.number"
                  type="text" 
                  placeholder="1234 5678 9012 3456"
                  maxlength="19"
                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                  @input="formatCardNumber"
                />
              </div>
              
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Expiry Date</label>
                  <input 
                    v-model="cardForm.expiry"
                    type="text" 
                    placeholder="MM/YY"
                    maxlength="5"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    @input="formatExpiry"
                  />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">CVV</label>
                  <input 
                    v-model="cardForm.cvv"
                    type="text" 
                    placeholder="123"
                    maxlength="4"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                  />
                </div>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Cardholder Name</label>
                <input 
                  v-model="cardForm.name"
                  type="text" 
                  placeholder="John Doe"
                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                />
              </div>
            </form>
          </div>

          <!-- Payment Terms -->
          <div class="mb-6 p-4 bg-yellow-50 rounded-lg">
            <div class="flex">
              <svg class="h-5 w-5 text-yellow-400 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
              </svg>
              <div class="text-sm">
                <p class="font-medium text-yellow-800">Payment Terms</p>
                <ul class="mt-1 text-yellow-700 space-y-1">
                  <li>• Full payment is required to confirm your booking</li>
                  <li>• Free cancellation up to 24 hours before pickup</li>
                  <li>• Security deposit may be required at pickup</li>
                  <li>• Refunds will be processed within 3-5 business days</li>
                </ul>
              </div>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="flex justify-end space-x-4">
            <button 
              type="button"
              @click="$emit('close')"
              class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
            >
              Cancel
            </button>
            <button 
              @click="processPayment"
              :disabled="!selectedPaymentMethod || processing"
              class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50"
            >
              {{ processing ? 'Processing...' : `Pay ₱${booking.total_amount}` }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { router, route } from '@inertiajs/vue3'

const props = defineProps({
  booking: Object,
})

const emit = defineEmits(['close', 'payment-completed'])

const selectedPaymentMethod = ref('card')
const processing = ref(false)

const cardForm = reactive({
  number: '',
  expiry: '',
  cvv: '',
  name: '',
})

const formatDateTime = (dateString) => {
  return new Date(dateString).toLocaleString()
}

const formatCardNumber = (event) => {
  let value = event.target.value.replace(/\s/g, '').replace(/[^0-9]/gi, '')
  let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value
  cardForm.number = formattedValue
}

const formatExpiry = (event) => {
  let value = event.target.value.replace(/\D/g, '')
  if (value.length >= 2) {
    value = value.substring(0, 2) + '/' + value.substring(2, 4)
  }
  cardForm.expiry = value
}

const processPayment = async () => {
  processing.value = true
  
  try {
    const paymentData = {
      booking_id: props.booking.id,
      payment_method: selectedPaymentMethod.value,
      amount: props.booking.total_amount,
    }

    if (selectedPaymentMethod.value === 'card') {
      paymentData.card_details = cardForm
    }

    // Simulate payment processing
    await new Promise(resolve => setTimeout(resolve, 2000))

    // In a real app, you would make an API call here
    router.post(route('payments.store', props.booking.id), paymentData, {
      onSuccess: (response) => {
        emit('payment-completed', response.props.payment)
      },
      onError: (errors) => {
        console.error('Payment failed:', errors)
      }
    })
  } catch (error) {
    console.error('Payment error:', error)
  } finally {
    processing.value = false
  }
}
</script>
