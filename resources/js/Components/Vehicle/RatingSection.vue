<template>
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Reviews & Ratings</h2>
        
        <!-- Rating Summary -->
        <div v-if="ratingStats.total_ratings > 0" class="mb-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center space-x-4">
                    <div class="text-center">
                        <div class="text-3xl font-bold text-gray-900">
                            {{ parseFloat(ratingStats.average_rating || 0).toFixed(1) }}
                        </div>
                        <div class="flex items-center justify-center space-x-1 mb-1">
                            <Star
                                v-for="star in 5"
                                :key="star"
                                :class="[
                                    'h-5 w-5',
                                    star <= Math.floor(parseFloat(ratingStats.average_rating || 0))
                                        ? 'text-yellow-400 fill-yellow-400'
                                        : star <= parseFloat(ratingStats.average_rating || 0)
                                        ? 'text-yellow-400 fill-yellow-400 opacity-50'
                                        : 'text-gray-300'
                                ]"
                            />
                        </div>
                        <div class="text-sm text-gray-600">
                            {{ ratingStats.total_ratings }} {{ ratingStats.total_ratings === 1 ? 'review' : 'reviews' }}
                        </div>
                    </div>
                    
                    <!-- Rating Distribution -->
                    <div class="flex-1 max-w-xs">
                        <div v-for="(count, stars) in ratingStats.rating_distribution" :key="stars" class="flex items-center space-x-2 mb-1">
                            <span class="text-sm text-gray-600 w-6">{{ stars }}★</span>
                            <div class="flex-1 h-2 bg-gray-200 rounded-full overflow-hidden">
                                <div 
                                    class="h-full bg-yellow-400 transition-all duration-300"
                                    :style="{ width: `${ratingStats.total_ratings ? (count / ratingStats.total_ratings) * 100 : 0}%` }"
                                ></div>
                            </div>
                            <span class="text-sm text-gray-500 w-8">{{ count }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Individual Reviews -->
        <div v-if="ratingStats.recent_ratings && ratingStats.recent_ratings.length > 0" class="space-y-4">
            <h3 class="font-medium text-gray-900">Recent Reviews</h3>
            
            <div
                v-for="rating in ratingStats.recent_ratings"
                :key="rating.id"
                class="border-b border-gray-200 pb-4 last:border-b-0 last:pb-0"
            >
                <div class="flex items-start space-x-3">
                    <img
                        :src="rating.user?.profile_picture_url || getDefaultAvatar(rating.user?.name)"
                        :alt="rating.user?.name || 'User'"
                        class="w-10 h-10 rounded-full object-cover"
                    />
                    <div class="flex-1">
                        <div class="flex items-center justify-between mb-2">
                            <div>
                                <h4 class="font-medium text-gray-900">
                                    {{ rating.user?.first_name || 'Anonymous' }} {{ rating.user?.last_name?.[0] || '' }}.
                                </h4>
                                <div class="flex items-center space-x-2">
                                    <div class="flex items-center space-x-1">
                                        <Star
                                            v-for="star in 5"
                                            :key="star"
                                            :class="[
                                                'h-4 w-4',
                                                star <= rating.rating
                                                    ? 'text-yellow-400 fill-yellow-400'
                                                    : 'text-gray-300'
                                            ]"
                                        />
                                    </div>
                                    <span class="text-sm text-gray-500">{{ formatDate(rating.rated_at) }}</span>
                                </div>
                            </div>
                            
                            <!-- Recommended Badge -->
                            <div v-if="rating.would_recommend" class="flex items-center space-x-1 text-green-600 text-sm">
                                <ThumbsUp class="h-4 w-4" />
                                <span>Recommended</span>
                            </div>
                        </div>
                        
                        <!-- Review Comment -->
                        <p v-if="rating.comment" class="text-gray-700 text-sm leading-relaxed mb-2">
                            {{ rating.comment }}
                        </p>
                        
                        <!-- Category Ratings -->
                        <div v-if="rating.rating_categories && Object.keys(rating.rating_categories).length > 0" 
                             class="grid grid-cols-2 gap-2 text-xs text-gray-600">
                            <div v-for="(score, category) in rating.rating_categories" :key="category" class="flex items-center space-x-1">
                                <span class="capitalize">{{ category }}:</span>
                                <div class="flex items-center">
                                    <Star
                                        v-for="star in score"
                                        :key="star"
                                        class="h-3 w-3 text-yellow-400 fill-yellow-400"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- View All Reviews Button -->
            <div v-if="ratingStats.total_ratings > ratingStats.recent_ratings.length" class="text-center pt-4">
                <button
                    @click="showAllReviews"
                    class="text-blue-600 hover:text-blue-700 font-medium text-sm"
                >
                    View all {{ ratingStats.total_ratings }} reviews
                </button>
            </div>
        </div>

        <!-- No Reviews State -->
        <div v-else-if="ratingStats.total_ratings === 0" class="text-center py-8 text-gray-500">
            <Star class="h-12 w-12 mx-auto text-gray-300 mb-2" />
            <p class="text-lg font-medium mb-1">No reviews yet</p>
            <p class="text-sm">Be the first to review this vehicle!</p>
        </div>
    </div>
</template>

<script setup>
import { Star, ThumbsUp } from 'lucide-vue-next';

const props = defineProps({
    ratingStats: {
        type: Object,
        required: true
    }
});

const formatDate = (dateString) => {
    if (!dateString) return '';
    return new Date(dateString).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric'
    });
};

const getDefaultAvatar = (name) => {
    if (!name) return 'https://ui-avatars.com/api/?name=U&background=3b82f6&color=ffffff';
    const initials = name.split(' ').map(n => n[0]).join('').toUpperCase();
    return `https://ui-avatars.com/api/?name=${initials}&background=3b82f6&color=ffffff`;
};

const showAllReviews = () => {
    // This could open a modal or navigate to a reviews page
    console.log('Show all reviews');
};
</script>
