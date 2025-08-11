<template>
  <AppLayout>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
      <aside class="md:col-span-1 bg-white p-6 rounded-lg shadow-md h-fit">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Owner Tools</h2>
        <nav class="flex flex-col gap-2">
          <Link href="/my-vehicles" class="text-gray-700 hover:underline">My Vehicles</Link>
          <Link href="/my-vehicles/create" class="text-gray-700 hover:underline">Add New Vehicle</Link>
          <Link href="/my-vehicles/bookings" class="text-gray-700 hover:underline">Booking Requests</Link>
          <Link href="/my-payments" class="text-primary-600 font-medium hover:underline">My Payments</Link>
        </nav>
      </aside>

      <div class="md:col-span-3 bg-white p-6 rounded-lg shadow-md">
        <div class="flex justify-between items-center mb-6">
          <h1 class="text-3xl font-bold text-gray-800">My Payments</h1>
          <Link href="/my-payments/qr"
            class="bg-primary-600 text-white px-4 py-2 rounded-md font-semibold hover:bg-primary-700 transition-colors flex items-center gap-2">
            <UploadCloud class="h-5 w-5" />
            Upload GCash QR
          </Link>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
          <div class="bg-green-50 p-6 rounded-lg shadow-md flex items-center gap-4">
            <DollarSign class="h-10 w-10 text-green-600" />
            <div>
              <p class="text-sm text-gray-600">Total Earnings</p>
              <p class="text-3xl font-bold text-gray-800">₱{{ totalEarnings.toLocaleString() }}</p>
            </div>
          </div>
          <div class="bg-blue-50 p-6 rounded-lg shadow-md flex items-center gap-4">
            <Wallet class="h-10 w-10 text-blue-600" />
            <div>
              <p class="text-sm text-gray-600">Pending Payout</p>
              <p class="text-3xl font-bold text-gray-800">₱{{ pendingPayout.toLocaleString() }}</p>
            </div>
          </div>
        </div>

        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Transaction History</h2>
        <div v-if="transactions.length > 0" class="overflow-x-auto">
          <table class="min-w-full bg-white border border-gray-200 rounded-lg">
            <thead>
              <tr class="bg-gray-100 text-left text-sm font-semibold text-gray-700">
                <th class="py-3 px-4 border-b">Date</th>
                <th class="py-3 px-4 border-b">Description</th>
                <th class="py-3 px-4 border-b">Amount</th>
                <th class="py-3 px-4 border-b">Status</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="transaction in transactions" :key="transaction.id" class="hover:bg-gray-50 border-b border-gray-100 last:border-b-0">
                <td class="py-3 px-4 text-gray-700">{{ transaction.date }}</td>
                <td class="py-3 px-4 text-gray-700">{{ transaction.description }}</td>
                <td class="py-3 px-4 font-medium" :class="transaction.type === 'credit' ? 'text-green-600' : 'text-red-600'">
                  {{ transaction.type === 'credit' ? '+' : '-' }}₱{{ transaction.amount.toLocaleString() }}
                </td>
                <td class="py-3 px-4">
                  <span :class="['px-3 py-1 rounded-full text-xs font-medium',
                    transaction.status === 'Completed' ? 'bg-green-100 text-green-800' :
                    transaction.status === 'Pending' ? 'bg-yellow-100 text-yellow-800' :
                    'bg-red-100 text-red-800']">
                    {{ transaction.status }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <p v-else class="text-gray-600 text-center py-8">No transactions recorded yet.</p>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { DollarSign, Wallet, UploadCloud } from 'lucide-vue-next';

const transactions = ref([
  { id: 1, date: '2025-07-15', description: 'Booking for Toyota Camry (John Doe)', amount: 4500, type: 'credit', status: 'Completed' },
  { id: 2, date: '2025-07-10', description: 'Payout to GCash', amount: 10000, type: 'debit', status: 'Completed' },
  { id: 3, date: '2025-07-05', description: 'Booking for Honda CRV (Maria Santos)', amount: 6000, type: 'credit', status: 'Completed' },
  { id: 4, date: '2025-07-01', description: 'Booking for Ford Ranger (Pending Payout)', amount: 5000, type: 'credit', status: 'Pending' },
]);

const totalEarnings = computed(() => {
  return transactions.value.filter(t => t.type === 'credit' && t.status === 'Completed').reduce((sum, t) => sum + t.amount, 0);
});

const pendingPayout = computed(() => {
  return transactions.value.filter(t => t.type === 'credit' && t.status === 'Pending').reduce((sum, t) => sum + t.amount, 0);
});

// In a real Inertia app, this data would be passed as props from the controller
</script>

<style scoped>
/* Tailwind CSS is used for styling */
</style>
