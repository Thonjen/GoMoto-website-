<template>
    <div class="glass-card border border-white/20 rounded-lg p-6 bg-white/5 backdrop-blur-sm shadow-glow">
        <h2 class="text-xl font-semibold mb-4 text-white">
            Vehicle Information
        </h2>
        <div class="flex space-x-4">
            <img
                :src="booking.vehicle.main_photo_url || '/images/placeholder-vehicle.jpg'"
                :alt="booking.vehicle.year + ' ' + booking.vehicle.make.name + ' ' + booking.vehicle.model.name"
                class="w-32 h-32 object-cover rounded-lg"
            />
            <div class="flex-1">
                <h3 class="text-lg font-semibold text-white">
                    {{ booking.vehicle.year }} {{ booking.vehicle.make.name }} {{ booking.vehicle.model.name }}
                </h3>
                <div class="space-y-1 text-sm text-white/70">
                    <div>Type: {{ booking.vehicle.type.name }}</div>
                    <div>Transmission: {{ booking.vehicle.transmission.name }}</div>
                    <div>Fuel: {{ booking.vehicle.fuel_type.name }}</div>
                </div>
            </div>
        </div>
        
        <div class="mt-6">
            <h2 class="text-xl font-semibold mb-4">
                Location Map
            </h2>

            <div class="w-full h-56 bg-gray-200 rounded-lg overflow-hidden">
                <iframe
                    v-if="booking.vehicle.lat && booking.vehicle.lng"
                    :src="`https://maps.google.com/maps?q=${booking.vehicle.lat},${booking.vehicle.lng}&z=18&t=k&output=embed`"
                    width="100%"
                    height="224"
                    style="border:0;"
                    allowfullscreen
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"
                >
                </iframe>

                <div
                    v-else
                    class="flex items-center justify-center h-full text-white/70"
                >
                    <div class="text-center">
                        <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <p>Location not available</p>
                    </div>
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
