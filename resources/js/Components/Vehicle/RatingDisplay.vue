<template>
    <div class="flex items-center space-x-2">
        <!-- Star Rating Display -->
        <div class="flex items-center space-x-1">
            <Star
                v-for="star in 5"
                :key="star"
                :class="[
                    'h-4 w-4',
                    star <= Math.floor(averageRating)
                        ? 'text-yellow-400 fill-yellow-400'
                        : star <= averageRating
                        ? 'text-yellow-400 fill-yellow-400 opacity-50'
                        : 'text-gray-300'
                ]"
            />
        </div>
        
        <!-- Rating Score and Count -->
        <div class="flex items-center space-x-1 text-sm">
            <span v-if="averageRating > 0" class="font-medium text-gray-700">
                {{ averageRating.toFixed(1) }}
            </span>
            <span v-if="totalRatings > 0" class="text-gray-500">
                ({{ totalRatings }} {{ totalRatings === 1 ? 'review' : 'reviews' }})
            </span>
            <span v-if="totalRatings === 0" class="text-gray-500 text-xs">
                No reviews yet
            </span>
        </div>

        <!-- High Rating Badge -->
        <div
            v-if="showHighRatingBadge && isHighRated"
            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800"
        >
            <span class="mr-1">⭐</span>
            Top Rated
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { Star } from 'lucide-vue-next';

const props = defineProps({
    averageRating: {
        type: Number,
        default: 0
    },
    totalRatings: {
        type: Number,
        default: 0
    },
    showHighRatingBadge: {
        type: Boolean,
        default: false
    },
    compact: {
        type: Boolean,
        default: false
    }
});

const isHighRated = computed(() => {
    return props.averageRating >= 4.5 && props.totalRatings >= 5;
});
</script>
