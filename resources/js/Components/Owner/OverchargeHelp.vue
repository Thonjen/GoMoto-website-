<template>
    <div class="bg-gradient-to-br from-amber-50 to-yellow-100 rounded-xl p-6 border border-amber-200">
        <div class="flex items-start space-x-4">
            <div class="flex-shrink-0">
                <div class="w-12 h-12 bg-amber-500 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                </div>
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Quick Help & Tips</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- FAQ Section -->
                    <div>
                        <h4 class="font-medium text-gray-800 mb-2 flex items-center">
                            <svg class="w-4 h-4 text-amber-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Common Questions
                        </h4>
                        <div class="space-y-2 text-sm">
                            <div class="cursor-pointer p-2 rounded hover:bg-amber-100" @click="expandFaq('grace-period')">
                                <strong class="text-amber-800">Q: What's a good grace period?</strong>
                                <p v-if="activeFaq === 'grace-period'" class="text-gray-600 mt-1">
                                    15-30 minutes is ideal. It shows understanding for minor delays while preventing abuse. Longer periods reduce complaints but may encourage lateness.
                                </p>
                            </div>
                            
                            <div class="cursor-pointer p-2 rounded hover:bg-amber-100" @click="expandFaq('rates')">
                                <strong class="text-amber-800">Q: How do I set competitive rates?</strong>
                                <p v-if="activeFaq === 'rates'" class="text-gray-600 mt-1">
                                    Check local competitors, consider your costs (fuel, time, wear), and start moderate. You can always adjust based on customer feedback and patterns.
                                </p>
                            </div>
                            
                            <div class="cursor-pointer p-2 rounded hover:bg-amber-100" @click="expandFaq('communication')">
                                <strong class="text-amber-800">Q: How to reduce customer complaints?</strong>
                                <p v-if="activeFaq === 'communication'" class="text-gray-600 mt-1">
                                    Clear communication is key. Set expectations upfront, send reminders, and be reasonable about genuine emergencies or issues.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Best Practices -->
                    <div>
                        <h4 class="font-medium text-gray-800 mb-2 flex items-center">
                            <svg class="w-4 h-4 text-amber-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Best Practices
                        </h4>
                        <div class="space-y-2 text-sm text-gray-700">
                            <div class="flex items-start space-x-2">
                                <div class="w-2 h-2 bg-amber-500 rounded-full mt-1.5 flex-shrink-0"></div>
                                <span>Send return time reminders 1 hour before due</span>
                            </div>
                            <div class="flex items-start space-x-2">
                                <div class="w-2 h-2 bg-amber-500 rounded-full mt-1.5 flex-shrink-0"></div>
                                <span>Provide clear city boundary maps to customers</span>
                            </div>
                            <div class="flex items-start space-x-2">
                                <div class="w-2 h-2 bg-amber-500 rounded-full mt-1.5 flex-shrink-0"></div>
                                <span>Review and adjust rates quarterly based on data</span>
                            </div>
                            <div class="flex items-start space-x-2">
                                <div class="w-2 h-2 bg-amber-500 rounded-full mt-1.5 flex-shrink-0"></div>
                                <span>Document patterns to improve your policies</span>
                            </div>
                            <div class="flex items-start space-x-2">
                                <div class="w-2 h-2 bg-amber-500 rounded-full mt-1.5 flex-shrink-0"></div>
                                <span>Be flexible with first-time customers</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="mt-4 flex flex-wrap gap-2">
                    <button 
                        @click="$emit('showTutorial')"
                        class="px-3 py-1 bg-amber-100 text-amber-800 rounded-lg text-sm font-medium hover:bg-amber-200 transition-colors"
                    >
                        📚 View Full Tutorial
                    </button>
                    <button 
                        @click="$emit('contactSupport')"
                        class="px-3 py-1 bg-blue-100 text-blue-800 rounded-lg text-sm font-medium hover:bg-blue-200 transition-colors"
                    >
                        💬 Contact Support
                    </button>
                    <button 
                        @click="$emit('viewExamples')"
                        class="px-3 py-1 bg-green-100 text-green-800 rounded-lg text-sm font-medium hover:bg-green-200 transition-colors"
                    >
                        📋 See Examples
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'

defineEmits(['showTutorial', 'contactSupport', 'viewExamples'])

const activeFaq = ref(null)

const expandFaq = (faq) => {
    activeFaq.value = activeFaq.value === faq ? null : faq
}
</script>
