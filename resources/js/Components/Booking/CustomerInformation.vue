<template>
    <div class="glass-card border border-white/20 rounded-lg p-6 bg-white/5 backdrop-blur-sm shadow-glow">
        <h2 class="text-xl font-semibold mb-4 text-white">
            Customer Information
        </h2>
        <div class="flex items-start space-x-4">
            <div
                class="w-16 h-16 bg-blue-500 rounded-full flex items-center justify-center text-white text-xl font-semibold"
            >
                {{ booking.user.first_name.charAt(0) }}{{
                    booking.user.last_name.charAt(0)
                }}
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-semibold text-white">
                    {{ booking.user.first_name }} {{ booking.user.last_name }}
                </h3>
                <div class="space-y-1 text-sm text-white/70">
                    <div>{{ booking.user.email }}</div>
                    <div>{{ booking.user.phone }}</div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
const props = defineProps({
    booking: Object,
});
</script>
