<template>
  <div class="max-w-xl mx-auto p-6 glass-card border border-white/20 rounded shadow-glow backdrop-blur-sm">
    <h1 class="text-2xl font-bold mb-4 text-white">Select Pricing Tier</h1>
    <div v-if="pricingTiers.length">
      <ul class="mb-4">
        <li v-for="tier in pricingTiers" :key="tier.id" class="mb-2 flex items-center gap-4">
          <label class="flex items-center gap-2 cursor-pointer text-white">
            <input
              type="radio"
              :value="tier.id"
              v-model="selectedTier"
              class="accent-blue-400"
            />
            <span>
              {{ tier.duration_from }} {{ tier.duration_unit.replace(/s$/, '') }}<span v-if="tier.duration_from > 1">s</span>
              - ₱{{ tier.price }}
            </span>
          </label>
        </li>
      </ul>
      <button
        class="bg-blue-400 hover:bg-blue-500 text-white px-4 py-2 rounded backdrop-blur-sm transition-colors"
        :disabled="!selectedTier"
        @click="submit"
      >Continue</button>
    </div>
    <div v-else class="text-white/60">No pricing tiers available.</div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
  booking: Object,
  pricingTiers: Array,
});

const selectedTier = ref(null);

function submit() {
  router.post(`/booking/${props.booking.id}/select-tier`, {
    pricing_tier_id: selectedTier.value,
  });
}
</script>
