<template>
  <AppLayout>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
      <aside class="md:col-span-1 bg-white p-6 rounded-lg shadow-md h-fit">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Admin Panel</h2>
        <nav class="flex flex-col gap-2">
          <Link href="/admin/dashboard" class="text-gray-700 hover:underline">Dashboard</Link>
          <Link href="/admin/users" class="text-gray-700 hover:underline">User Management</Link>
          <Link href="/admin/vehicles" class="text-primary-600 font-medium hover:underline">Vehicle Management</Link>
          <Link href="/admin/bookings" class="text-gray-700 hover:underline">Bookings</Link>
          <Link href="/admin/kyc/verifications" class="text-gray-700 hover:underline">KYC Verifications</Link>
          <Link href="/admin/payments" class="text-gray-700 hover:underline">Payments</Link>
          <Link href="/admin/reports" class="text-gray-700 hover:underline">Reports</Link>
        </nav>
      </aside>

      <div class="md:col-span-3 bg-white p-6 rounded-lg shadow-md">
        <div class="flex justify-between items-center mb-6">
          <h1 class="text-3xl font-bold text-gray-800">Vehicle Management</h1>
          
          <!-- Statistics Cards -->
          <div class="flex gap-4">
            <div class="bg-blue-50 px-4 py-2 rounded-lg">
              <div class="text-blue-600 font-semibold">{{ vehicles.data ? vehicles.data.length : 0 }}</div>
              <div class="text-blue-500 text-sm">Total</div>
            </div>
            <div class="bg-green-50 px-4 py-2 rounded-lg">
              <div class="text-green-600 font-semibold">{{ vehicles.data ? vehicles.data.filter(v => v.status === 'approved').length : 0 }}</div>
              <div class="text-green-500 text-sm">Approved</div>
            </div>
            <div class="bg-yellow-50 px-4 py-2 rounded-lg">
              <div class="text-yellow-600 font-semibold">{{ vehicles.data ? vehicles.data.filter(v => v.status === 'pending').length : 0 }}</div>
              <div class="text-yellow-500 text-sm">Pending</div>
            </div>
          </div>
        </div>

        <!-- Search and Filters -->
        <div class="mb-6 flex flex-col sm:flex-row gap-4">
          <div class="flex-1">
            <input 
              type="text" 
              v-model="form.search" 
              @input="debouncedSearch"
              placeholder="Search vehicles by license plate, make, model, or owner..."
              class="w-full p-3 border border-gray-300 rounded-md focus:ring-primary-400 focus:border-primary-400" 
            />
          </div>
          <div class="flex gap-2">
            <select 
              v-model="form.status" 
              @change="search"
              class="px-3 py-2 border border-gray-300 rounded-md focus:ring-primary-400 focus:border-primary-400"
            >
              <option value="">All Status</option>
              <option value="pending">Pending</option>
              <option value="approved">Approved</option>
              <option value="rejected">Rejected</option>
              <option value="suspended">Suspended</option>
            </select>
            <select 
              v-model="form.available" 
              @change="search"
              class="px-3 py-2 border border-gray-300 rounded-md focus:ring-primary-400 focus:border-primary-400"
            >
              <option value="">All Availability</option>
              <option value="1">Available</option>
              <option value="0">Unavailable</option>
            </select>
          </div>
        </div>

        <!-- Loading State -->
        <div v-if="processing" class="text-center py-8">
          <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
          <p class="mt-2 text-gray-600">Loading vehicles...</p>
        </div>

        <!-- Vehicles Table -->
        <div v-else-if="vehicles.data && vehicles.data.length > 0" class="overflow-x-auto">
          <table class="min-w-full bg-white border border-gray-200 rounded-lg">
            <thead>
              <tr class="bg-gray-100 text-left text-sm font-semibold text-gray-700">
                <th class="py-3 px-4 border-b">Vehicle</th>
                <th class="py-3 px-4 border-b">Owner</th>
                <th class="py-3 px-4 border-b">Details</th>
                <th class="py-3 px-4 border-b">Status</th>
                <th class="py-3 px-4 border-b">Availability</th>
                <th class="py-3 px-4 border-b">Bookings</th>
                <th class="py-3 px-4 border-b">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="vehicle in vehicles.data" :key="vehicle.id" class="hover:bg-gray-50 border-b border-gray-100 last:border-b-0">
                <td class="py-3 px-4">
                  <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0 h-12 w-12">
                      <img 
                        v-if="vehicle.photos && vehicle.photos.length > 0" 
                        :src="vehicle.photos[0].url" 
                        :alt="vehicleName(vehicle)"
                        class="h-12 w-12 rounded-lg object-cover"
                      />
                      <div v-else class="h-12 w-12 bg-gray-200 rounded-lg flex items-center justify-center">
                        <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                      </div>
                    </div>
                    <div>
                      <div class="font-medium text-gray-800">{{ vehicleName(vehicle) }}</div>
                      <div class="text-sm text-gray-600">{{ vehicle.license_plate }}</div>
                    </div>
                  </div>
                </td>
                <td class="py-3 px-4">
                  <div class="text-gray-800">{{ vehicle.owner?.first_name }} {{ vehicle.owner?.last_name }}</div>
                  <div class="text-sm text-gray-600">{{ vehicle.owner?.email }}</div>
                </td>
                <td class="py-3 px-4">
                  <div class="text-sm">
                    <div><strong>Type:</strong> {{ vehicle.type?.name }}</div>
                    <div><strong>Year:</strong> {{ vehicle.year }}</div>
                    <div><strong>Fuel:</strong> {{ vehicle.fuel_type?.name }}</div>
                    <div><strong>Transmission:</strong> {{ vehicle.transmission?.name }}</div>
                  </div>
                </td>
                <td class="py-3 px-4">
                  <span :class="getStatusBadgeClass(vehicle.status)">
                    {{ vehicle.status ? vehicle.status.charAt(0).toUpperCase() + vehicle.status.slice(1) : 'Unknown' }}
                  </span>
                </td>
                <td class="py-3 px-4">
                  <span :class="vehicle.is_available 
                    ? 'bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-medium' 
                    : 'bg-red-100 text-red-800 px-3 py-1 rounded-full text-xs font-medium'">
                    {{ vehicle.is_available ? 'Available' : 'Unavailable' }}
                  </span>
                </td>
                <td class="py-3 px-4 text-center">
                  <div class="text-gray-800 font-medium">{{ vehicle.bookings_count || 0 }}</div>
                  <div class="text-xs text-gray-500">bookings</div>
                </td>
                <td class="py-3 px-4">
                  <div class="flex flex-wrap gap-1">
                    <button 
                      v-if="vehicle.status === 'pending'"
                      @click="approveVehicle(vehicle)"
                      :disabled="processing"
                      class="bg-green-500 text-white px-2 py-1 rounded text-xs font-medium hover:bg-green-600 disabled:opacity-50"
                    >
                      Approve
                    </button>
                    <button 
                      v-if="vehicle.status === 'pending'"
                      @click="rejectVehicle(vehicle)"
                      :disabled="processing"
                      class="bg-red-500 text-white px-2 py-1 rounded text-xs font-medium hover:bg-red-600 disabled:opacity-50"
                    >
                      Reject
                    </button>
                    <button 
                      v-if="vehicle.status === 'approved'"
                      @click="suspendVehicle(vehicle)"
                      :disabled="processing"
                      class="bg-yellow-500 text-white px-2 py-1 rounded text-xs font-medium hover:bg-yellow-600 disabled:opacity-50"
                    >
                      Suspend
                    </button>
                    <Link 
                      :href="`/vehicles/${vehicle.id}`"
                      class="bg-blue-500 text-white px-2 py-1 rounded text-xs font-medium hover:bg-blue-600"
                    >
                      View
                    </Link>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- No Results -->
        <div v-else class="text-center py-12">
          <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
          </svg>
          <h3 class="mt-2 text-sm font-medium text-gray-900">No vehicles found</h3>
          <p class="mt-1 text-sm text-gray-500">Try adjusting your search criteria.</p>
        </div>

        <!-- Pagination -->
        <div v-if="vehicles.links && vehicles.links.length > 3" class="mt-6 flex justify-center">
          <nav class="flex space-x-2">
            <Link 
              v-for="link in vehicles.links" 
              :key="link.label"
              :href="link.url"
              v-html="link.label"
              :class="[
                'px-3 py-2 text-sm font-medium rounded-md',
                link.active 
                  ? 'bg-primary-600 text-white' 
                  : link.url 
                    ? 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50' 
                    : 'bg-gray-100 text-gray-400 cursor-not-allowed'
              ]"
            />
          </nav>
        </div>
      </div>
    </div>

    <!-- Success/Error Messages -->
    <div v-if="$page.props.flash?.success" class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
      {{ $page.props.flash.success }}
    </div>
    <div v-if="$page.props.flash?.error" class="fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
      {{ $page.props.flash.error }}
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { debounce } from 'lodash';

const props = defineProps({
  vehicles: Object,
  filters: Object
});

const page = usePage();
const processing = ref(false);
const form = reactive({
  search: props.filters?.search || '',
  status: props.filters?.status || '',
  available: props.filters?.available || ''
});

const vehicleName = (vehicle) => {
  const make = vehicle.make?.name || '';
  const model = vehicle.model?.name || '';
  return make && model ? `${make} ${model}` : vehicle.license_plate || 'Unknown Vehicle';
};

const getStatusBadgeClass = (status) => {
  const classes = 'px-3 py-1 rounded-full text-xs font-medium ';
  switch (status) {
    case 'approved':
      return classes + 'bg-green-100 text-green-800';
    case 'pending':
      return classes + 'bg-yellow-100 text-yellow-800';
    case 'rejected':
      return classes + 'bg-red-100 text-red-800';
    case 'suspended':
      return classes + 'bg-gray-100 text-gray-800';
    default:
      return classes + 'bg-gray-100 text-gray-800';
  }
};

const search = () => {
  processing.value = true;
  router.get('/admin/vehicles', form, {
    preserveState: true,
    preserveScroll: true,
    onFinish: () => {
      processing.value = false;
    }
  });
};

const debouncedSearch = debounce(search, 300);

const approveVehicle = (vehicle) => {
  if (confirm(`Are you sure you want to approve "${vehicleName(vehicle)}"?`)) {
    processing.value = true;
    router.post(`/admin/vehicles/${vehicle.id}/approve`, {}, {
      onFinish: () => {
        processing.value = false;
      }
    });
  }
};

const rejectVehicle = (vehicle) => {
  if (confirm(`Are you sure you want to reject "${vehicleName(vehicle)}"?`)) {
    processing.value = true;
    router.post(`/admin/vehicles/${vehicle.id}/reject`, {}, {
      onFinish: () => {
        processing.value = false;
      }
    });
  }
};

const suspendVehicle = (vehicle) => {
  if (confirm(`Are you sure you want to suspend "${vehicleName(vehicle)}"? This will cancel all active bookings.`)) {
    processing.value = true;
    router.post(`/admin/vehicles/${vehicle.id}/suspend`, {}, {
      onFinish: () => {
        processing.value = false;
      }
    });
  }
};

// Auto-hide flash messages
onMounted(() => {
  const flash = page.props.flash;
  if (flash?.success || flash?.error) {
    setTimeout(() => {
      router.reload({ only: [] }); // Clear flash messages
    }, 5000);
  }
});
</script>

<style scoped>
/* Custom styles if needed */
</style>
