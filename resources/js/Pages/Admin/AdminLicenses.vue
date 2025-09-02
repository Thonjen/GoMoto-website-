<template>
  <AppLayout>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
      <aside class="md:col-span-1 glass-card-dark p-6 h-fit">
        <h2 class="text-xl font-semibold text-white mb-4">Admin Panel</h2>
        <nav class="flex flex-col gap-2">
          <Link href="/admin/users" class="text-white/70 hover:text-white hover:underline transition-colors">User Management</Link>
          <Link href="/admin/vehicles" class="text-white/70 hover:text-white hover:underline transition-colors">Vehicle Listings</Link>
          <Link href="/admin/bookings" class="text-white/70 hover:text-white hover:underline transition-colors">Bookings</Link>
          <Link href="/admin/licenses" class="text-blue-400 font-medium hover:underline">License Verifications</Link>
          <Link href="/admin/disputes" class="text-white/70 hover:text-white hover:underline transition-colors">Disputes</Link>
          <Link href="/admin/reports" class="text-white/70 hover:text-white hover:underline transition-colors">Reports</Link>
        </nav>
      </aside>

      <div class="md:col-span-3 glass-card-dark p-6">
        <h1 class="text-3xl font-bold text-white mb-6">Driver's License Verifications</h1>

        <div class="mb-6">
          <input type="text" v-model="searchQuery" placeholder="Search by user name or email..."
            class="w-full p-3 bg-white/10 border border-white/20 rounded-md text-white placeholder-white/50 focus:ring-blue-400 focus:border-blue-400 backdrop-blur-sm" />
        </div>

        <div v-if="filteredLicenses.length > 0" class="overflow-x-auto">
          <table class="min-w-full glass-card border border-white/20 rounded-lg">
            <thead>
              <tr class="bg-white/10 backdrop-blur-sm text-left text-sm font-semibold text-white">
                <th class="py-3 px-4 border-b border-white/20">User</th>
                <th class="py-3 px-4 border-b border-white/20">License ID</th>
                <th class="py-3 px-4 border-b border-white/20">Submitted On</th>
                <th class="py-3 px-4 border-b border-white/20">Status</th>
                <th class="py-3 px-4 border-b border-white/20">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="license in filteredLicenses" :key="license.id" class="hover:bg-white/5 border-b border-white/10 last:border-b-0 transition-colors">
                <td class="py-3 px-4">
                  <div class="font-medium text-white">{{ license.userName }}</div>
                  <div class="text-sm text-white/70">{{ license.userEmail }}</div>
                </td>
                <td class="py-3 px-4 text-white/90">{{ license.licenseNumber }}</td>
                <td class="py-3 px-4 text-white/90">{{ license.submittedDate }}</td>
                <td class="py-3 px-4">
                  <span :class="['px-3 py-1 rounded-full text-xs font-medium backdrop-blur-sm',
                    license.status === 'Pending' ? 'bg-yellow-400/20 text-yellow-400' :
                    license.status === 'Approved' ? 'bg-green-400/20 text-green-400' :
                    'bg-red-400/20 text-red-400']">
                    {{ license.status }}
                  </span>
                </td>
                <td class="py-3 px-4 flex gap-2">
                  <button @click="viewLicense(license)"
                    class="bg-blue-500/80 text-white px-3 py-1 rounded-md text-xs font-medium hover:bg-blue-600 backdrop-blur-sm transition-colors">
                    View
                  </button>
                  <button v-if="license.status === 'Pending'" @click="approveLicense(license)"
                    class="bg-green-500/80 text-white px-3 py-1 rounded-md text-xs font-medium hover:bg-green-600 backdrop-blur-sm transition-colors">
                    Approve
                  </button>
                  <button v-if="license.status === 'Pending'" @click="rejectLicense(license)"
                    class="bg-red-500/80 text-white px-3 py-1 rounded-md text-xs font-medium hover:bg-red-600 backdrop-blur-sm transition-colors">
                    Reject
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <p v-else class="text-white/70 text-center py-8">No license verification requests.</p>

        <!-- License View Modal (Simplified) -->
        <div v-if="showLicenseModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
          <div class="glass-card-dark shadow-xl p-6 w-full max-w-2xl relative">
            <button @click="showLicenseModal = false" class="absolute top-3 right-3 text-white/70 hover:text-white transition-colors">
              <X class="h-6 w-6" />
            </button>
            <h2 class="text-2xl font-bold text-white mb-4">License Details for {{ selectedLicense.userName }}</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <p class="font-semibold text-white">Front Side:</p>
                <img :src="selectedLicense.frontImageUrl" alt="License Front" class="w-full h-48 object-contain border border-white/20 rounded-md mt-2 bg-white/5 backdrop-blur-sm" />
              </div>
              <div>
                <p class="font-semibold text-white">Back Side:</p>
                <img :src="selectedLicense.backImageUrl" alt="License Back" class="w-full h-48 object-contain border border-white/20 rounded-md mt-2 bg-white/5 backdrop-blur-sm" />
              </div>
            </div>
            <p class="mt-4 text-white/90"><strong class="text-white">License Number:</strong> {{ selectedLicense.licenseNumber }}</p>
            <p class="text-white/90"><strong class="text-white">Submitted On:</strong> {{ selectedLicense.submittedDate }}</p>
            <p class="text-white/90"><strong class="text-white">Current Status:</strong> {{ selectedLicense.status }}</p>
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

const licenses = ref([
  {
    id: 1,
    userName: 'John Doe',
    userEmail: 'john.doe@example.com',
    licenseNumber: 'A12-34-567',
    submittedDate: '2025-07-10',
    status: 'Pending',
    frontImageUrl: '/placeholder.svg?height=192&width=256',
    backImageUrl: '/placeholder.svg?height=192&width=256',
  },
  {
    id: 2,
    userName: 'Jane Smith',
    userEmail: 'jane.s@example.com',
    licenseNumber: 'B98-76-543',
    submittedDate: '2025-07-01',
    status: 'Approved',
    frontImageUrl: '/placeholder.svg?height=192&width=256',
    backImageUrl: '/placeholder.svg?height=192&width=256',
  },
  {
    id: 3,
    userName: 'Peter Jones',
    userEmail: 'peter.j@example.com',
    licenseNumber: 'C11-22-333',
    submittedDate: '2025-07-18',
    status: 'Pending',
    frontImageUrl: '/placeholder.svg?height=192&width=256',
    backImageUrl: '/placeholder.svg?height=192&width=256',
  },
]);

const searchQuery = ref('');
const showLicenseModal = ref(false);
const selectedLicense = ref(null);

const filteredLicenses = computed(() => {
  const query = searchQuery.value.toLowerCase();
  return licenses.value.filter(license =>
    license.userName.toLowerCase().includes(query) ||
    license.userEmail.toLowerCase().includes(query) ||
    license.licenseNumber.toLowerCase().includes(query)
  );
});

const viewLicense = (license) => {
  selectedLicense.value = license;
  showLicenseModal.value = true;
};

const approveLicense = (license) => {
  if (confirm(`Approve license for ${license.userName}?`)) {
    license.status = 'Approved';
    alert(`License for ${license.userName} approved! (Not actually updated in this demo)`);
    // In a real Inertia app, this would be an Inertia.post or Inertia.put request
  }
};

const rejectLicense = (license) => {
  if (confirm(`Reject license for ${license.userName}?`)) {
    license.status = 'Rejected';
    alert(`License for ${license.userName} rejected. (Not actually updated in this demo)`);
    // In a real Inertia app, this would be an Inertia.post or Inertia.put request
  }
};
</script>

<style scoped>
/* Tailwind CSS is used for styling */
</style>
