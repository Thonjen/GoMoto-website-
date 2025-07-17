<template>
  <AppLayout>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
      <aside class="md:col-span-1 bg-white p-6 rounded-lg shadow-md h-fit">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Admin Panel</h2>
        <nav class="flex flex-col gap-2">
          <Link href="/admin/users" class="text-gray-700 hover:underline">User Management</Link>
          <Link href="/admin/vehicles" class="text-gray-700 hover:underline">Vehicle Listings</Link>
          <Link href="/admin/bookings" class="text-gray-700 hover:underline">Bookings</Link>
          <Link href="/admin/licenses" class="text-gray-700 hover:underline">License Verifications</Link>
          <Link href="/admin/disputes" class="text-primary-600 font-medium hover:underline">Disputes</Link>
          <Link href="/admin/reports" class="text-gray-700 hover:underline">Reports</Link>
        </nav>
      </aside>

      <div class="md:col-span-3 bg-white p-6 rounded-lg shadow-md">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Dispute Resolution</h1>

        <div class="mb-6">
          <input type="text" v-model="searchQuery" placeholder="Search disputes by ID, users, or vehicle..."
            class="w-full p-3 border border-gray-300 rounded-md focus:ring-primary-400 focus:border-primary-400" />
        </div>

        <div v-if="filteredDisputes.length > 0" class="overflow-x-auto">
          <table class="min-w-full bg-white border border-gray-200 rounded-lg">
            <thead>
              <tr class="bg-gray-100 text-left text-sm font-semibold text-gray-700">
                <th class="py-3 px-4 border-b">Dispute ID</th>
                <th class="py-3 px-4 border-b">Booking ID</th>
                <th class="py-3 px-4 border-b">Reported By</th>
                <th class="py-3 px-4 border-b">Against</th>
                <th class="py-3 px-4 border-b">Issue</th>
                <th class="py-3 px-4 border-b">Status</th>
                <th class="py-3 px-4 border-b">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="dispute in filteredDisputes" :key="dispute.id" class="hover:bg-gray-50 border-b border-gray-100 last:border-b-0">
                <td class="py-3 px-4 text-gray-700">{{ dispute.id }}</td>
                <td class="py-3 px-4 text-gray-700">{{ dispute.bookingId }}</td>
                <td class="py-3 px-4 text-gray-700">{{ dispute.reportedBy }}</td>
                <td class="py-3 px-4 text-gray-700">{{ dispute.againstUser }}</td>
                <td class="py-3 px-4 text-gray-700">{{ dispute.issue.substring(0, 50) }}...</td>
                <td class="py-3 px-4">
                  <span :class="['px-3 py-1 rounded-full text-xs font-medium',
                    dispute.status === 'Open' ? 'bg-red-100 text-red-800' :
                    dispute.status === 'Under Review' ? 'bg-yellow-100 text-yellow-800' :
                    'bg-green-100 text-green-800']">
                    {{ dispute.status }}
                  </span>
                </td>
                <td class="py-3 px-4 flex gap-2">
                  <button @click="viewDispute(dispute)"
                    class="bg-blue-500 text-white px-3 py-1 rounded-md text-xs font-medium hover:bg-blue-600">
                    View
                  </button>
                  <button v-if="dispute.status !== 'Resolved'" @click="resolveDispute(dispute)"
                    class="bg-green-500 text-white px-3 py-1 rounded-md text-xs font-medium hover:bg-green-600">
                    Resolve
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <p v-else class="text-gray-600 text-center py-8">No disputes found.</p>

        <!-- Dispute Detail Modal (Simplified) -->
        <div v-if="showDisputeModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
          <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-2xl relative">
            <button @click="showDisputeModal = false" class="absolute top-3 right-3 text-gray-500 hover:text-gray-700">
              <X class="h-6 w-6" />
            </button>
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Dispute #{{ selectedDispute.id }}</h2>
            <p class="mb-2 text-gray-700"><strong>Booking ID:</strong> {{ selectedDispute.bookingId }}</p>
            <p class="mb-2 text-gray-700"><strong>Reported By:</strong> {{ selectedDispute.reportedBy }}</p>
            <p class="mb-2 text-gray-700"><strong>Against:</strong> {{ selectedDispute.againstUser }}</p>
            <p class="mb-2 text-gray-700"><strong>Issue:</strong> {{ selectedDispute.issue }}</p>
            <p class="mb-4 text-gray-700"><strong>Status:</strong> {{ selectedDispute.status }}</p>
            <p class="font-semibold text-gray-800">Evidence:</p>
            <div class="grid grid-cols-2 gap-4 mt-2">
              <img v-for="(img, index) in selectedDispute.evidenceImages" :key="index" :src="img" class="w-full h-32 object-contain border border-gray-200 rounded-md" />
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { X } from 'lucide-vue-next';

const disputes = ref([
  {
    id: 1,
    bookingId: 101,
    reportedBy: 'John Doe (Renter)',
    againstUser: 'Jane Smith (Owner)',
    issue: 'Vehicle was not clean upon pickup and had a minor scratch not reported.',
    status: 'Open',
    evidenceImages: ['/placeholder.svg?height=128&width=192', '/placeholder.svg?height=128&width=192'],
  },
  {
    id: 2,
    bookingId: 102,
    reportedBy: 'Jane Smith (Owner)',
    againstUser: 'Maria Santos (Renter)',
    issue: 'Vehicle was returned late by 3 hours without prior notification.',
    status: 'Under Review',
    evidenceImages: [],
  },
  {
    id: 3,
    bookingId: 103,
    reportedBy: 'Peter Jones (Renter)',
    againstUser: 'Alice Brown (Owner)',
    issue: 'Owner cancelled last minute without valid reason.',
    status: 'Resolved',
    evidenceImages: [],
  },
]);

const searchQuery = ref('');
const showDisputeModal = ref(false);
const selectedDispute = ref(null);

const filteredDisputes = computed(() => {
  const query = searchQuery.value.toLowerCase();
  return disputes.value.filter(dispute =>
    dispute.id.toString().includes(query) ||
    dispute.reportedBy.toLowerCase().includes(query) ||
    dispute.againstUser.toLowerCase().includes(query) ||
    dispute.issue.toLowerCase().includes(query)
  );
});

const viewDispute = (dispute) => {
  selectedDispute.value = dispute;
  showDisputeModal.value = true;
};

const resolveDispute = (dispute) => {
  if (confirm(`Are you sure you want to mark dispute #${dispute.id} as resolved?`)) {
    dispute.status = 'Resolved';
    alert(`Dispute #${dispute.id} resolved. (Not actually updated in this demo)`);
    // In a real Inertia app, this would be an Inertia.post or Inertia.put request
  }
};
</script>

<style scoped>
/* Tailwind CSS is used for styling */
</style>
