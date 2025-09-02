<template>
    <AuthenticatedLayout>
        <div class="min-h-screen py-8">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="space-y-6">
                    <!-- Header -->
                    <div class="glass-card p-6 shadow-glow">
                        <div class="flex items-center justify-between">
                            <div>
                                <h1 class="text-2xl font-bold text-white">Rate Your Experience</h1>
                                <p class="text-white/80">Help other renters by sharing your feedback</p>
                            </div>
                            <div class="flex-shrink-0">
                                <button
                                    @click="goBack"
                                    class="btn-glass flex items-center gap-2"
                                >
                                    <ArrowLeft class="h-4 w-4" />
                                    Back
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Vehicle Information -->
                    <div class="glass-card p-6 shadow-glow">
                        <div class="bg-white/5 rounded-lg p-4 backdrop-blur-sm border border-white/10">
                            <div class="flex items-center space-x-4">
                                <div class="w-16 h-16 bg-gradient-to-br from-primary-500 to-purple-600 rounded-lg flex items-center justify-center text-white text-xl font-bold shadow-lg">
                                    {{ vehicle.type?.category === 'car' ? '🚗' : '🏍️' }}
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-white">
                                        {{ vehicle.make?.name }} {{ vehicle.model?.name }} {{ vehicle.year }}
                                    </h3>
                                    <p class="text-white/80">{{ vehicle.type?.sub_type }} • {{ vehicle.color }}</p>
                                    <p class="text-sm text-white/60">
                                        Owner: {{ vehicle.owner?.name }} • 
                                        Rental: {{ formatDate(booking.pickup_datetime) }} - {{ formatDate(booking.calculated_end_datetime) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Rating Form -->
                    <div class="glass-card p-6 shadow-glow">
                        <form @submit.prevent="submitRating" class="space-y-6">
                            <!-- Overall Rating -->
                            <div>
                                <label class="block text-sm font-medium text-white mb-3">
                                    Overall Rating *
                                </label>
                                <div class="flex items-center space-x-2">
                                    <div class="flex items-center space-x-1">
                                        <Star
                                            v-for="star in 5"
                                            :key="star"
                                            @click="setOverallRating(star)"
                                            :class="[
                                                'h-8 w-8 cursor-pointer transition-colors',
                                                star <= form.rating 
                                                    ? 'text-yellow-400 fill-yellow-400' 
                                                    : 'text-white hover:text-yellow-300'
                                            ]"
                                        />
                                    </div>
                                    <span v-if="form.rating > 0" class="text-sm text-white ml-2">
                                        {{ getRatingText(form.rating) }}
                                    </span>
                                </div>
                                <div v-if="errors.rating" class="mt-1 text-sm text-red-600">{{ errors.rating }}</div>
                            </div>

                            <!-- Category Ratings -->
                            <div class="mb-6">
                                <h3 class="text-sm font-medium text-white mb-3">Rate Specific Aspects (Optional)</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div v-for="(category, key) in ratingCategories" :key="key" class="space-y-2">
                                        <label class="text-sm text-white">{{ category.label }}</label>
                                        <div class="flex items-center space-x-1">
                                            <Star
                                                v-for="star in 5"
                                                :key="star"
                                                @click="setCategoryRating(key, star)"
                                                :class="[
                                                    'h-5 w-5 cursor-pointer transition-colors',
                                                    star <= (form.rating_categories[key] || 0)
                                                        ? 'text-yellow-400 fill-yellow-400' 
                                                        : 'text-white hover:text-yellow-300'
                                                ]"
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Written Review -->
                            <div class="mb-6">
                                <label for="comment" class="block text-sm font-medium text-white mb-2">
                                    Share Your Experience (Optional)
                                </label>
                                <textarea
                                    id="comment"
                                    v-model="form.comment"
                                    rows="4"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Tell other renters about your experience with this vehicle and owner..."
                                ></textarea>
                                <p class="mt-1 text-sm text-gray-500">
                                    {{ form.comment?.length || 0 }}/1000 characters
                                </p>
                                <div v-if="errors.comment" class="mt-1 text-sm text-red-600">{{ errors.comment }}</div>
                            </div>

                            <!-- Recommendation -->
                            <div class="mb-6">
                                <div class="flex items-center space-x-3">
                                    <input
                                        id="would_recommend"
                                        v-model="form.would_recommend"
                                        type="checkbox"
                                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                    />
                                    <label for="would_recommend" class="text-sm font-medium text-white">
                                        I would recommend this vehicle to other renters
                                    </label>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="flex items-center justify-between">
                                <div class="text-sm text-gray-500">
                                    * Required fields
                                </div>
                                <div class="flex space-x-3">
                                    <button
                                        type="button"
                                        @click="goBack"
                                        class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                    >
                                        Cancel
                                    </button>
                                    <button
                                        type="submit"
                                        :disabled="!form.rating || processing"
                                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50 disabled:cursor-not-allowed"
                                    >
                                        <Send v-if="!processing" class="h-4 w-4 mr-2" />
                                        <span v-if="processing" class="inline-block animate-spin rounded-full h-4 w-4 border-2 border-white border-t-transparent mr-2"></span>
                                        {{ processing ? 'Submitting...' : 'Submit Rating' }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Star, Send, ArrowLeft } from 'lucide-vue-next';

const page = usePage();
const props = defineProps({
    booking: Object,
    vehicle: Object,
    errors: Object,
});

const processing = ref(false);

const form = reactive({
    rating: 0,
    comment: '',
    would_recommend: true,
    rating_categories: {}
});

const ratingCategories = {
    cleanliness: { label: 'Cleanliness', icon: '🧽' },
    condition: { label: 'Vehicle Condition', icon: '🔧' },
    punctuality: { label: 'Owner Punctuality', icon: '⏰' },
    communication: { label: 'Communication', icon: '💬' }
};

const setOverallRating = (rating) => {
    form.rating = rating;
};

const setCategoryRating = (category, rating) => {
    form.rating_categories[category] = rating;
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

const formatDate = (dateString) => {
    if (!dateString) return '';
    return new Date(dateString).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric'
    });
};

const submitRating = () => {
    if (!form.rating) {
        return;
    }

    processing.value = true;

    router.post(route('ratings.store', props.booking.id), form, {
        onFinish: () => {
            processing.value = false;
        }
    });
};

const goBack = () => {
    window.history.length > 1 ? window.history.back() : router.visit('/dashboard');
};
</script>
