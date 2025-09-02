<template>
    <AuthenticatedLayout>
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="glass-card-dark overflow-hidden shadow-glow sm:rounded-lg mb-6">
                    <div class="p-6 border-b border-white/20">
                        <div class="flex items-center justify-between">
                            <div>
                                <h1 class="text-2xl font-bold text-white">Vehicle Reviews & Ratings</h1>
                                <p class="text-white/80 mt-1">See what renters think about your vehicles</p>
                            </div>
                            <div class="flex items-center space-x-4">
                                <div class="text-center">
                                    <div class="text-3xl font-bold text-blue-400">{{ statistics.average_rating.toFixed(1) }}</div>
                                    <div class="text-sm text-white/70">Average Rating</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-3xl font-bold text-green-400">{{ statistics.total_ratings }}</div>
                                    <div class="text-sm text-white/70">Total Reviews</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-3xl font-bold text-purple-400">{{ statistics.recent_ratings }}</div>
                                    <div class="text-sm text-white/70">This Month</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ratings List -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div v-if="ratings.data.length > 0" class="space-y-6">
                            <div
                                v-for="rating in ratings.data"
                                :key="rating.id"
                                class="border-b border-gray-200 pb-6 last:border-b-0 last:pb-0"
                            >
                                <div class="flex items-start space-x-4">
                                    <!-- Vehicle Image -->
                                    <div class="w-20 h-20 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <span class="text-2xl">
                                            {{ rating.vehicle?.type?.category === 'car' ? '🚗' : '🏍️' }}
                                        </span>
                                    </div>

                                    <!-- Rating Content -->
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between mb-2">
                                            <div>
                                                <h3 class="font-semibold text-gray-900">
                                                    {{ rating.vehicle?.make?.name }} {{ rating.vehicle?.model?.name }}
                                                </h3>
                                                <p class="text-sm text-gray-500">
                                                    Booking #{{ rating.booking?.id }} • {{ formatDate(rating.rated_at) }}
                                                </p>
                                            </div>
                                            <div class="flex items-center space-x-2">
                                                <!-- Star Rating -->
                                                <div class="flex items-center space-x-1">
                                                    <Star
                                                        v-for="star in 5"
                                                        :key="star"
                                                        :class="[
                                                            'h-5 w-5',
                                                            star <= rating.rating
                                                                ? 'text-yellow-400 fill-yellow-400'
                                                                : 'text-gray-300'
                                                        ]"
                                                    />
                                                </div>
                                                <span class="font-medium text-gray-700">{{ rating.rating }}/5</span>
                                            </div>
                                        </div>

                                        <!-- Renter Info -->
                                        <div class="flex items-center space-x-2 mb-3">
                                            <img
                                                :src="rating.user?.profile_picture_url || getDefaultAvatar(rating.user?.name)"
                                                :alt="rating.user?.name"
                                                class="w-8 h-8 rounded-full object-cover"
                                            />
                                            <span class="text-sm font-medium text-gray-700">
                                                {{ rating.user?.first_name }} {{ rating.user?.last_name?.[0] }}.
                                            </span>
                                            <div v-if="rating.would_recommend" class="flex items-center space-x-1 text-green-600 text-sm">
                                                <ThumbsUp class="h-4 w-4" />
                                                <span>Recommended</span>
                                            </div>
                                        </div>

                                        <!-- Review Comment -->
                                        <p v-if="rating.comment" class="text-gray-700 mb-3 leading-relaxed">
                                            {{ rating.comment }}
                                        </p>

                                        <!-- Category Ratings -->
                                        <div v-if="rating.rating_categories && Object.keys(rating.rating_categories).length > 0" 
                                             class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                            <div v-for="(score, category) in rating.rating_categories" :key="category" 
                                                 class="flex items-center justify-between p-2 bg-gray-50 rounded">
                                                <span class="text-sm text-gray-600 capitalize">{{ category }}</span>
                                                <div class="flex items-center space-x-1">
                                                    <Star
                                                        v-for="star in score"
                                                        :key="star"
                                                        class="h-3 w-3 text-yellow-400 fill-yellow-400"
                                                    />
                                                    <span class="text-xs text-gray-500 ml-1">{{ score }}/5</span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Featured Badge -->
                                        <div v-if="rating.is_featured" class="mt-2">
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                ⭐ Featured Review
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- No Ratings State -->
                        <div v-else class="text-center py-16 text-gray-500">
                            <Star class="h-16 w-16 mx-auto text-gray-300 mb-4" />
                            <h3 class="text-xl font-medium mb-2">No reviews yet</h3>
                            <p class="max-w-md mx-auto">
                                When renters complete their bookings and leave reviews, they'll appear here.
                                Great reviews help attract more customers!
                            </p>
                        </div>

                        <!-- Pagination -->
                        <div v-if="ratings.data.length > 0" class="mt-8">
                            <nav class="flex items-center justify-between">
                                <div class="flex-1 flex justify-between sm:hidden">
                                    <Link
                                        v-if="ratings.prev_page_url"
                                        :href="ratings.prev_page_url"
                                        class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                                    >
                                        Previous
                                    </Link>
                                    <Link
                                        v-if="ratings.next_page_url"
                                        :href="ratings.next_page_url"
                                        class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                                    >
                                        Next
                                    </Link>
                                </div>
                                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                    <div>
                                        <p class="text-sm text-gray-700">
                                            Showing
                                            <span class="font-medium">{{ ratings.from }}</span>
                                            to
                                            <span class="font-medium">{{ ratings.to }}</span>
                                            of
                                            <span class="font-medium">{{ ratings.total }}</span>
                                            reviews
                                        </p>
                                    </div>
                                    <div>
                                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                            <Link
                                                v-if="ratings.prev_page_url"
                                                :href="ratings.prev_page_url"
                                                class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
                                            >
                                                <ChevronLeft class="h-5 w-5" />
                                            </Link>
                                            <Link
                                                v-if="ratings.next_page_url"
                                                :href="ratings.next_page_url"
                                                class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
                                            >
                                                <ChevronRight class="h-5 w-5" />
                                            </Link>
                                        </nav>
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
import { Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Star, ThumbsUp, ChevronLeft, ChevronRight } from 'lucide-vue-next';

const props = defineProps({
    ratings: Object,
    statistics: Object,
});

const formatDate = (dateString) => {
    if (!dateString) return '';
    return new Date(dateString).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const getDefaultAvatar = (name) => {
    if (!name) return 'https://ui-avatars.com/api/?name=U&background=3b82f6&color=ffffff';
    const initials = name.split(' ').map(n => n[0]).join('').toUpperCase();
    return `https://ui-avatars.com/api/?name=${initials}&background=3b82f6&color=ffffff`;
};
</script>
