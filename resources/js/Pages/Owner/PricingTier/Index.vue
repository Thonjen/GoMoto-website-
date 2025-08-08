<template>
  <OwnerLayout>
    <div class="max-w-2xl mx-auto bg-white p-6 rounded shadow">
      <h1 class="text-2xl font-bold mb-4">My Pricing Tiers</h1>
      <form @submit.prevent="addTier" class="flex gap-2 mb-6">
        <input v-model="newTier.duration_from" type="number" min="1" placeholder="Duration" class="border p-1 w-20" required />
        <select v-model="newTier.duration_unit" class="border p-1" required>
          <option value="minutes">minute(s)</option>
          <option value="hours">hour(s)</option>
          <option value="days">day(s)</option>
        </select>
        <input v-model="newTier.price" type="number" min="0" step="0.01" placeholder="Price" class="border p-1 w-24" required />
        <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded">Add</button>
      </form>
      <table class="min-w-full bg-white border">
        <thead>
          <tr>
            <th class="px-4 py-2">Duration</th>
            <th class="px-4 py-2">Unit</th>
            <th class="px-4 py-2">Price</th>
            <th class="px-4 py-2">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="tier in pricingTiers" :key="tier.id">
            <td class="border px-4 py-2">{{ tier.duration_from }}</td>
<td class="border px-4 py-2">
  {{ tier.duration_from == 1 ? tier.duration_unit.slice(0, -1) : tier.duration_unit }}
</td>            <td class="border px-4 py-2">₱{{ tier.price }}</td>
            <td class="border px-4 py-2">
              <button @click="editTier(tier)" class="text-blue-600 mr-2">Edit</button>
              <button @click="removeTier(tier.id)" class="text-red-600">Delete</button>
            </td>
          </tr>
        </tbody>
      </table>
      <!-- Edit Modal -->
      <div v-if="showEditModal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded shadow-lg w-full max-w-sm">
          <h2 class="text-lg font-bold mb-4">Edit Pricing Tier</h2>
          <form @submit.prevent="updateTier">
            <div class="mb-2">
              <label class="block mb-1">Duration</label>
              <input v-model="editTierForm.duration_from" type="number" min="1" class="border p-2 w-full" required />
            </div>
            <div class="mb-2">
              <label class="block mb-1">Unit</label>
              <select v-model="editTierForm.duration_unit" class="border p-2 w-full" required>
                <option value="minutes">minute(s)</option>
                <option value="hours">hour(s)</option>
                <option value="days">day(s)</option>
              </select>
            </div>
            <div class="mb-2">
              <label class="block mb-1">Price</label>
              <input v-model="editTierForm.price" type="number" min="0" step="0.01" class="border p-2 w-full" required />
            </div>
            <div class="flex gap-2 mt-4">
              <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Update</button>
              <button type="button" @click="showEditModal = false" class="bg-gray-300 px-4 py-2 rounded">Cancel</button>
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
