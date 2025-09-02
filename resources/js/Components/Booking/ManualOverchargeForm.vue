<template>
    <!-- Manual Overcharge Form -->
    <div v-if="showManualOverchargeForm" class="glass-card border border-white/20 rounded-lg p-6 bg-white/5 backdrop-blur-sm shadow-glow">
        <h3 class="text-lg font-semibold text-white mb-4">➕ Add Manual Overcharge</h3>
        
        <div class="bg-red-50/10 border border-red-200/30 rounded p-4">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Overcharge Type</label>
                    <select v-model="manualOvercharge.type" 
                            class="w-full rounded-md border-gray-600 bg-gray-700 text-white shadow-sm focus:border-red-500 focus:ring-red-500">
                        <option value="1">⏰ Late Return</option>
                        <option value="2">🏙️ Out of City Use</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Amount (₱)</label>
                    <input v-model="manualOvercharge.amount" 
                           type="number" 
                           step="0.01" 
                           min="0"
                           class="w-full rounded-md border-gray-600 bg-gray-700 text-white shadow-sm focus:border-red-500 focus:ring-red-500"
                           placeholder="Enter overcharge amount">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Description</label>
                    <textarea v-model="manualOvercharge.details" 
                              class="w-full rounded-md border-gray-600 bg-gray-700 text-white shadow-sm focus:border-red-500 focus:ring-red-500" 
                              rows="3"
                              placeholder="Reason for this overcharge..."></textarea>
                </div>
                
                <div class="flex space-x-3 pt-2">
                    <button @click="$emit('addManualOvercharge', manualOvercharge)" 
                            :disabled="processing || !manualOvercharge.amount || parseFloat(manualOvercharge.amount) <= 0"
                            class="flex-1 bg-red-600 hover:bg-red-700 disabled:bg-gray-400 text-white font-medium py-2 px-4 rounded-lg transition-colors disabled:cursor-not-allowed">
                        {{ processing ? 'Adding...' : 'Add Overcharge' }}
                    </button>
                    <button @click="$emit('close')" 
                            class="flex-1 bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Preview -->
        <div v-if="manualOvercharge.amount && parseFloat(manualOvercharge.amount) > 0" 
             class="mt-4 p-3 bg-yellow-500/10 border border-yellow-500/20 rounded-lg">
            <h4 class="text-sm font-medium text-yellow-300 mb-2">Preview</h4>
            <div class="text-sm text-gray-300">
                <div class="flex justify-between">
                    <span>Type:</span>
                    <span>{{ getOverchargeTypeName(parseInt(manualOvercharge.type)) }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Amount:</span>
                    <span class="font-semibold text-red-400">₱{{ formatCurrency(manualOvercharge.amount) }}</span>
                </div>
                <div v-if="manualOvercharge.details" class="mt-2">
                    <span class="text-xs text-gray-400">Description:</span>
                    <p class="text-xs text-gray-300 mt-1">{{ manualOvercharge.details }}</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
const props = defineProps({
    showManualOverchargeForm: Boolean,
    manualOvercharge: Object,
    processing: Boolean,
});

defineEmits(['addManualOvercharge', 'close']);

function getOverchargeTypeName(typeId) {
    const types = {
        1: '⏰ Late Return',
        2: '🏙️ Out of City Use'
    };
    return types[typeId] || 'Unknown Overcharge';
}

function formatCurrency(amount) {
    if (amount === null || amount === undefined || isNaN(amount)) return '0.00';
    return parseFloat(amount).toFixed(2);
}
</script>
