<template>
  <AppLayout>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
      <aside class="md:col-span-1 glass-card-dark p-6 h-fit">
        <h2 class="text-xl font-semibold text-white mb-4">Admin Panel</h2>
        <nav class="flex flex-col gap-2">
          <Link href="/admin/users" class="text-white/70 hover:text-white hover:underline transition-colors">User Management</Link>
          <Link href="/admin/vehicles" class="text-white/70 hover:text-white hover:underline transition-colors">Vehicle Listings</Link>
          <Link href="/admin/bookings" class="text-white/70 hover:text-white hover:underline transition-colors">Bookings</Link>
          <Link href="/admin/licenses" class="text-white/70 hover:text-white hover:underline transition-colors">License Verifications</Link>
          <Link href="/admin/disputes" class="text-blue-400 font-medium hover:underline">Disputes</Link>
          <Link href="/admin/reports" class="text-white/70 hover:text-white hover:underline transition-colors">Reports</Link>
        </nav>
      </aside>

      <div class="md:col-span-3 glass-card-dark p-6">
        <h1 class="text-3xl font-bold text-white mb-6">Dispute Resolution</h1>

        <div class="mb-6">
          <input type="text" v-model="searchQuery" placeholder="Search disputes by ID, users, or vehicle..."
            class="w-full p-3 bg-white/10 border border-white/20 rounded-md text-white placeholder-white/50 focus:ring-blue-400 focus:border-blue-400 backdrop-blur-sm" />
        </div>

        <div v-if="filteredDisputes.length > 0" class="overflow-x-auto">
          <table class="min-w-full glass-card border border-white/20 rounded-lg">
            <thead>
              <tr class="bg-white/10 backdrop-blur-sm text-left text-sm font-semibold text-white">
                <th class="py-3 px-4 border-b border-white/20">Dispute ID</th>
                <th class="py-3 px-4 border-b border-white/20">Booking ID</th>
                <th class="py-3 px-4 border-b border-white/20">Reported By</th>
                <th class="py-3 px-4 border-b border-white/20">Against</th>
                <th class="py-3 px-4 border-b border-white/20">Issue</th>
                <th class="py-3 px-4 border-b border-white/20">Status</th>
                <th class="py-3 px-4 border-b border-white/20">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="dispute in filteredDisputes" :key="dispute.id" class="hover:bg-white/5 border-b border-white/10 last:border-b-0 transition-colors">
                <td class="py-3 px-4 text-white/90">{{ dispute.id }}</td>
                <td class="py-3 px-4 text-white/90">{{ dispute.bookingId }}</td>
                <td class="py-3 px-4 text-white/90">{{ dispute.reportedBy }}</td>
                <td class="py-3 px-4 text-white/90">{{ dispute.againstUser }}</td>
                <td class="py-3 px-4 text-white/90">{{ dispute.issue.substring(0, 50) }}...</td>
                <td class="py-3 px-4">
                  <span :class="['px-3 py-1 rounded-full text-xs font-medium backdrop-blur-sm',
                    dispute.status === 'Open' ? 'bg-red-400/20 text-red-400' :
                    dispute.status === 'Under Review' ? 'bg-yellow-400/20 text-yellow-400' :
                    'bg-green-400/20 text-green-400']">
                    {{ dispute.status }}
                  </span>
                </td>
                <td class="py-3 px-4 flex gap-2">
                  <button @click="viewDispute(dispute)"
                    class="bg-blue-500/80 text-white px-3 py-1 rounded-md text-xs font-medium hover:bg-blue-600 backdrop-blur-sm transition-colors">
                    View
                  </button>
                  <button v-if="dispute.status !== 'Resolved'" @click="resolveDispute(dispute)"
                    class="bg-green-500/80 text-white px-3 py-1 rounded-md text-xs font-medium hover:bg-green-600 backdrop-blur-sm transition-colors">
                    Resolve
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <p v-else class="text-white/70 text-center py-8">No disputes found.</p>

        <!-- Dispute Detail Modal (Simplified) -->
        <div v-if="showDisputeModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
          <div class="glass-card-dark shadow-xl p-6 w-full max-w-2xl relative">
            <button @click="showDisputeModal = false" class="absolute top-3 right-3 text-white/70 hover:text-white transition-colors">
              <X class="h-6 w-6" />
            </button>
            <h2 class="text-2xl font-bold text-white mb-4">Dispute #{{ selectedDispute.id }}</h2>
            <p class="mb-2 text-white/90"><strong class="text-white">Booking ID:</strong> {{ selectedDispute.bookingId }}</p>
            <p class="mb-2 text-white/90"><strong class="text-white">Reported By:</strong> {{ selectedDispute.reportedBy }}</p>
            <p class="mb-2 text-white/90"><strong class="text-white">Against:</strong> {{ selectedDispute.againstUser }}</p>
            <p class="mb-2 text-white/90"><strong class="text-white">Issue:</strong> {{ selectedDispute.issue }}</p>
            <p class="mb-4 text-white/90"><strong class="text-white">Status:</strong> {{ selectedDispute.status }}</p>
            <p class="font-semibold text-white">Evidence:</p>
            <div class="grid grid-cols-2 gap-4 mt-2">
              <img v-for="(img, index) in selectedDispute.evidenceImages" :key="index" :src="img" class="w-full h-32 object-contain border border-white/20 rounded-md bg-white/5 backdrop-blur-sm" />
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
