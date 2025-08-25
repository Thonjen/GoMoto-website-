<template>
    <!-- Rating Prompt Modal -->
    <div
        v-if="show"
        class="fixed inset-0 z-50 overflow-y-auto"
        aria-labelledby="modal-title"
        role="dialog"
        aria-modal="true"
    >
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div 
                class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                @click="$emit('close')"
            ></div>

            <!-- Center the modal contents -->
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <!-- Modal panel -->
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100 sm:mx-0 sm:h-10 sm:w-10">
                            <Star class="h-6 w-6 text-yellow-600" />
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                How was your rental experience?
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    You recently returned the <strong>{{ booking.vehicle?.make?.name }} {{ booking.vehicle?.model?.name }}</strong>. 
                                    Help other renters by sharing your experience!
                                </p>
                            </div>
                            
                            <!-- Quick Rating -->
                            <div class="mt-4">
                                <div class="flex items-center justify-center space-x-1">
                                    <Star
                                        v-for="star in 5"
                                        :key="star"
                                        @click="setQuickRating(star)"
                                        :class="[
                                            'h-8 w-8 cursor-pointer transition-colors',
                                            star <= quickRating 
                                                ? 'text-yellow-400 fill-yellow-400' 
                                                : 'text-gray-300 hover:text-yellow-300'
                                        ]"
                                    />
                                </div>
                                <p v-if="quickRating > 0" class="text-center text-sm text-gray-600 mt-1">
                                    {{ getRatingText(quickRating) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <!-- Submit Quick Rating -->
                    <button
                        v-if="quickRating > 0"
                        @click="submitQuickRating"
                        type="button"
                        :disabled="submitting"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50"
                    >
                        <span v-if="submitting" class="inline-block animate-spin rounded-full h-4 w-4 border-2 border-white border-t-transparent mr-2"></span>
                        {{ submitting ? 'Submitting...' : 'Submit Rating' }}
                    </button>
                    
                    <!-- Full Review Button -->
                    <button
                        @click="goToFullReview"
                        type="button"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                    >
                        Write Full Review
                    </button>
                    
                    <!-- Skip Button -->
                    <button
                        @click="$emit('close')"
                        type="button"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm"
                    >
                        Maybe Later
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { Star } from 'lucide-vue-next';

const props = defineProps({
    show: {
        type: Boolean,
        default: false
    },
    booking: {
        type: Object,
        required: true
    }
});

const emit = defineEmits(['close', 'rated']);

const quickRating = ref(0);
const submitting = ref(false);

const setQuickRating = (rating) => {
    quickRating.value = rating;
};

const getRatingText = (rating) => {
    const texts = {
        1: 'Poor',
        2: 'Fair', 
        3: 'Good',
        4: 'Very Good',
        5: 'Excellent'
    };
    return texts[rating] || '';
};

const submitQuickRating = async () => {
    if (!quickRating.value) return;
    
    submitting.value = true;
    
    router.post(route('ratings.store', props.booking.id), {
        rating: quickRating.value,
        comment: '',
        would_recommend: quickRating.value >= 4,
        rating_categories: {}
    }, {
        onSuccess: () => {
            emit('rated');
            emit('close');
        },
        onFinish: () => {
            submitting.value = false;
        }
    });
};

const goToFullReview = () => {
    emit('close');
    router.visit(route('ratings.create', props.booking.id));
};
</script>
