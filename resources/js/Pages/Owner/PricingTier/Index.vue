<template>
  <OwnerLayout>
    <div class="max-w-2xl mx-auto glass-card-dark p-6 rounded shadow-glow">
      <h1 class="text-2xl font-bold mb-4 text-white">My Pricing Tiers</h1>
      <form @submit.prevent="addTier" class="flex gap-2 mb-6">
        <input v-model="newTier.duration_from" type="number" min="1" placeholder="Duration" class="border border-white/20 p-1 w-20 bg-white/10 text-white placeholder-white/60 rounded backdrop-blur-sm" required />
        <select v-model="newTier.duration_unit" class="border border-white/20 p-1 bg-white/10 text-white rounded backdrop-blur-sm" required>
          <option value="minutes">minute(s)</option>
          <option value="hours">hour(s)</option>
          <option value="days">day(s)</option>
        </select>
        <input v-model="newTier.price" type="number" min="0" step="0.01" placeholder="Price" class="border border-white/20 p-1 w-24 bg-white/10 text-white placeholder-white/60 rounded backdrop-blur-sm" required />
        <button type="submit" class="bg-blue-500/80 hover:bg-blue-500 text-white px-3 py-1 rounded transition-all duration-200 backdrop-blur-sm border border-blue-400/30">Add</button>
      </form>
      <table class="min-w-full glass-card border border-white/20">
        <thead>
          <tr class="border-b border-white/20">
            <th class="px-4 py-2 text-white">Duration</th>
            <th class="px-4 py-2 text-white">Unit</th>
            <th class="px-4 py-2 text-white">Price</th>
            <th class="px-4 py-2 text-white">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="tier in pricingTiers" :key="tier.id" class="border-b border-white/10">
            <td class="border-r border-white/10 px-4 py-2 text-white">{{ tier.duration_from }}</td>
            <td class="border-r border-white/10 px-4 py-2 text-white">
              {{ tier.duration_from == 1 ? tier.duration_unit.slice(0, -1) : tier.duration_unit }}
            </td>
            <td class="border-r border-white/10 px-4 py-2 text-white">₱{{ tier.price }}</td>
            <td class="px-4 py-2">
              <button @click="editTier(tier)" class="text-blue-400 hover:text-blue-300 mr-2 transition-colors">Edit</button>
              <button @click="removeTier(tier.id)" class="text-red-400 hover:text-red-300 transition-colors">Delete</button>
            </td>
          </tr>
        </tbody>
      </table>
      <!-- Edit Modal -->
      <div v-if="showEditModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50">
        <div class="glass-card-dark p-6 rounded shadow-glow w-full max-w-sm border border-white/20">
          <h2 class="text-lg font-bold mb-4 text-white">Edit Pricing Tier</h2>
          <form @submit.prevent="updateTier">
            <div class="mb-2">
              <label class="block mb-1 text-white">Duration</label>
              <input v-model="editTierForm.duration_from" type="number" min="1" class="border border-white/20 p-2 w-full bg-white/10 text-white rounded backdrop-blur-sm" required />
            </div>
            <div class="mb-2">
              <label class="block mb-1 text-white">Unit</label>
              <select v-model="editTierForm.duration_unit" class="border border-white/20 p-2 w-full bg-white/10 text-white rounded backdrop-blur-sm" required>
                <option value="minutes">minute(s)</option>
                <option value="hours">hour(s)</option>
                <option value="days">day(s)</option>
              </select>
            </div>
            <div class="mb-2">
              <label class="block mb-1 text-white">Price</label>
              <input v-model="editTierForm.price" type="number" min="0" step="0.01" class="border border-white/20 p-2 w-full bg-white/10 text-white rounded backdrop-blur-sm" required />
            </div>
            <div class="flex gap-2 mt-4">
              <button type="submit" class="bg-blue-500/80 hover:bg-blue-500 text-white px-4 py-2 rounded transition-all duration-200 backdrop-blur-sm border border-blue-400/30">Update</button>
              <button type="button" @click="showEditModal = false" class="bg-white/20 hover:bg-white/30 text-white px-4 py-2 rounded transition-all duration-200 backdrop-blur-sm border border-white/30">Cancel</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </OwnerLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import OwnerLayout from '@/Layouts/OwnerLayout.vue';

const pricingTiers = ref([]);
const newTier = ref({
  duration_from: '',
  duration_unit: 'hours',
  price: '',
});



// Edit modal state
const showEditModal = ref(false);
const editTierForm = ref({
  id: null,
  duration_from: '',
  duration_unit: 'hours',
  price: '',
});

function fetchTiers() {
  router.get('/owner/pricing-tiers', {}, {
    preserveState: true,
    onSuccess: (page) => {
      pricingTiers.value = page.props.pricingTiers || [];
    }
  });
}

function addTier() {
  router.post('/owner/pricing-tiers', newTier.value, {
    onSuccess: () => {
      newTier.value = { duration_from: '', duration_unit: 'hours', price: '' };
      fetchTiers();
    }
  });
}

function removeTier(id) {
  if (confirm('Delete this pricing tier?')) {
    router.delete(`/owner/pricing-tiers/${id}`, {
      onSuccess: fetchTiers
    });
  }
}

// Edit logic
function editTier(tier) {
  editTierForm.value = { ...tier };
  showEditModal.value = true;
}
function updateTier() {
  router.post(`/owner/pricing-tiers/${editTierForm.value.id}`, {
    ...editTierForm.value,
    _method: 'PUT'
  }, {
    onSuccess: () => {
      showEditModal.value = false;
      fetchTiers();
    }
  });
}

onMounted(fetchTiers);
</script>
