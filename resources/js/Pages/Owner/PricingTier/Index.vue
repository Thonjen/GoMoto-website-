<template>
  <OwnerLayout>
    <div class="space-y-6">
      <!-- Header Section -->
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold text-white">My Pricing Tiers</h1>
          <p class="text-white/70 mt-1">Manage your vehicle rental pricing structure</p>
        </div>
      </div>

      <!-- Add New Tier Form -->
      <div class="glass-card-dark p-6 border border-white/20 shadow-glow">
        <h2 class="text-lg font-semibold text-white mb-4">Add New Pricing Tier</h2>
        <form @submit.prevent="addTier" class="flex flex-wrap gap-4">
          <div class="flex-1 min-w-[120px]">
            <label class="block text-sm font-medium text-white/80 mb-2">Duration</label>
            <input 
              v-model="newTier.duration_from" 
              type="number" 
              min="1" 
              placeholder="e.g., 1, 2, 24" 
              class="w-full border border-white/20 p-3 bg-white/10 text-white placeholder-white/50 rounded-lg backdrop-blur-sm focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition-all" 
              required 
            />
          </div>
          
          <div class="flex-1 min-w-[140px]">
            <label class="block text-sm font-medium text-white/80 mb-2">Unit</label>
            <select 
              v-model="newTier.duration_unit" 
              class="w-full border border-white/20 p-3 bg-white/10 text-white rounded-lg backdrop-blur-sm focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition-all" 
              required
            >
              <option value="minutes" class="bg-gray-800 text-white">minute(s)</option>
              <option value="hours" class="bg-gray-800 text-white">hour(s)</option>
              <option value="days" class="bg-gray-800 text-white">day(s)</option>
            </select>
          </div>
          
          <div class="flex-1 min-w-[140px]">
            <label class="block text-sm font-medium text-white/80 mb-2">Price (₱)</label>
            <input 
              v-model="newTier.price" 
              type="number" 
              min="0" 
              step="0.01" 
              placeholder="e.g., 100.00" 
              class="w-full border border-white/20 p-3 bg-white/10 text-white placeholder-white/50 rounded-lg backdrop-blur-sm focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition-all" 
              required 
            />
          </div>
          
          <div class="flex items-end">
            <button 
              type="submit" 
              class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-all duration-200 shadow-lg hover:shadow-xl flex items-center gap-2"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
              </svg>
              Add Tier
            </button>
          </div>
        </form>
      </div>

      <!-- Pricing Tiers Table -->
      <div class="glass-card-dark border border-white/20 shadow-glow overflow-hidden">
        <div class="px-6 py-4 border-b border-white/20">
          <h2 class="text-lg font-semibold text-white">Current Pricing Tiers</h2>
          <p class="text-white/60 text-sm mt-1">{{ pricingTiers.length }} tier{{ pricingTiers.length !== 1 ? 's' : '' }} configured</p>
        </div>
        
        <div v-if="pricingTiers.length > 0" class="overflow-x-auto">
          <table class="min-w-full divide-y divide-white/10">
            <thead class="bg-white/5">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-white/80 uppercase tracking-wider">Duration</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-white/80 uppercase tracking-wider">Unit</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-white/80 uppercase tracking-wider">Price</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-white/80 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-white/10">
              <tr v-for="tier in pricingTiers" :key="tier.id" class="hover:bg-white/5 transition-colors">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm font-medium text-white">{{ tier.duration_from }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-white/80">
                    {{ tier.duration_from == 1 ? tier.duration_unit.slice(0, -1) : tier.duration_unit }}
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm font-semibold text-green-400">₱{{ parseFloat(tier.price).toFixed(2) }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <div class="flex items-center justify-end gap-2">
                    <button 
                      @click="editTier(tier)" 
                      class="text-blue-400 hover:text-blue-300 bg-blue-500/20 hover:bg-blue-500/30 px-3 py-1 rounded-lg text-xs font-medium transition-colors border border-blue-500/30 flex items-center gap-1"
                    >
                      <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                      </svg>
                      Edit
                    </button>
                    <button 
                      @click="removeTier(tier.id)" 
                      class="text-red-400 hover:text-red-300 bg-red-500/20 hover:bg-red-500/30 px-3 py-1 rounded-lg text-xs font-medium transition-colors border border-red-500/30 flex items-center gap-1"
                    >
                      <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                      </svg>
                      Delete
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        
        <div v-else class="px-6 py-12 text-center">
          <svg class="w-12 h-12 text-white/40 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
          </svg>
          <h3 class="text-lg font-medium text-white/90 mb-2">No pricing tiers yet</h3>
          <p class="text-white/60">Create your first pricing tier to start setting rental rates for your vehicles.</p>
        </div>
      </div>
    </div>

    <!-- Edit Modal -->
    <div v-if="showEditModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50">
      <div class="glass-card-dark p-6 rounded-lg shadow-glow w-full max-w-md border border-white/20">
        <h2 class="text-xl font-bold mb-6 text-white">Edit Pricing Tier</h2>
        <form @submit.prevent="updateTier" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-white/80 mb-2">Duration</label>
            <input 
              v-model="editTierForm.duration_from" 
              type="number" 
              min="1" 
              class="w-full border border-white/20 p-3 bg-white/10 text-white rounded-lg backdrop-blur-sm focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition-all" 
              required 
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-white/80 mb-2">Unit</label>
            <select 
              v-model="editTierForm.duration_unit" 
              class="w-full border border-white/20 p-3 bg-white/10 text-white rounded-lg backdrop-blur-sm focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition-all" 
              required
            >
              <option value="minutes" class="bg-gray-800 text-white">minute(s)</option>
              <option value="hours" class="bg-gray-800 text-white">hour(s)</option>
              <option value="days" class="bg-gray-800 text-white">day(s)</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-white/80 mb-2">Price (₱)</label>
            <input 
              v-model="editTierForm.price" 
              type="number" 
              min="0" 
              step="0.01" 
              class="w-full border border-white/20 p-3 bg-white/10 text-white rounded-lg backdrop-blur-sm focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition-all" 
              required 
            />
          </div>
          <div class="flex gap-3 pt-4">
            <button 
              type="submit" 
              class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-lg font-medium transition-all duration-200 shadow-lg hover:shadow-xl"
            >
              Update Tier
            </button>
            <button 
              type="button" 
              @click="showEditModal = false" 
              class="flex-1 bg-white/10 hover:bg-white/20 text-white px-4 py-3 rounded-lg font-medium transition-all duration-200 border border-white/20"
            >
              Cancel
            </button>
          </div>
        </form>
      </div>
    </div>
  </OwnerLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import OwnerLayout from '@/Layouts/OwnerLayout.vue';

// Define props
const props = defineProps({
  pricingTiers: {
    type: Array,
    default: () => []
  }
});

// Use computed to make pricingTiers reactive to prop changes
const pricingTiers = computed(() => props.pricingTiers || []);
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

function addTier() {
  router.post('/owner/pricing-tiers', newTier.value, {
    onSuccess: () => {
      newTier.value = { duration_from: '', duration_unit: 'hours', price: '' };
      // The page will automatically refresh due to Inertia's default behavior
    },
    onError: (errors) => {
      console.error('Add tier failed:', errors);
    }
  });
}

function removeTier(id) {
  if (confirm('Delete this pricing tier?')) {
    router.delete(`/owner/pricing-tiers/${id}`, {
      onSuccess: () => {
        // The page will automatically refresh due to Inertia's default behavior
      },
      onError: (errors) => {
        console.error('Delete failed:', errors);
      }
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
      // The page will automatically refresh due to Inertia's default behavior
    },
    onError: (errors) => {
      console.error('Update failed:', errors);
    }
  });
}
</script>

<style scoped>
/* Enhanced form styling */
input, select {
  background-color: rgba(255, 255, 255, 0.1) !important;
  color: white !important;
}

input::placeholder {
  color: rgba(255, 255, 255, 0.5) !important;
}

/* Select options styling */
select option {
  background-color: #1f2937 !important;
  color: white !important;
}

/* Improved focus states */
input:focus, select:focus {
  outline: none !important;
  border-color: #3b82f6 !important;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;
}

/* Glass morphism effects */
.glass-card-dark {
  background: rgba(31, 41, 55, 0.8);
  backdrop-filter: blur(10px);
}

/* Enhanced button animations */
button {
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

button:hover {
  transform: translateY(-1px);
}

/* Table hover effects */
tbody tr:hover {
  background-color: rgba(255, 255, 255, 0.05) !important;
}

/* Modal backdrop */
.backdrop-blur-sm {
  backdrop-filter: blur(8px);
}
</style>
