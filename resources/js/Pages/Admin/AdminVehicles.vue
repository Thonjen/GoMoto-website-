<template>
  <AuthenticatedLayout>
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="mb-8">
        <div class="flex justify-between items-center">
          <div>
            <h1 class="text-3xl font-bold text-gray-900">Vehicle Management</h1>
            <p class="mt-2 text-gray-600">Manage all vehicles in the system</p>
          </div>
          <Link :href="route('admin.dashboard')" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
            Back to Dashboard
          </Link>
        </div>
      </div>

      <!-- Statistics Cards -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white shadow rounded-lg p-6">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
              </div>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">Total Vehicles</dt>
                <dd class="text-lg font-medium text-gray-900">{{ vehicles.data ? vehicles.data.length : 0 }}</dd>
              </dl>
            </div>
          </div>
        </div>
        <div class="bg-white shadow rounded-lg p-6">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
              </div>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">Approved</dt>
                <dd class="text-lg font-medium text-gray-900">{{ vehicles.data ? vehicles.data.filter(v => v.status === 'approved').length : 0 }}</dd>
              </dl>
            </div>
          </div>
        </div>
        <div class="bg-white shadow rounded-lg p-6">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">Pending</dt>
                <dd class="text-lg font-medium text-gray-900">{{ vehicles.data ? vehicles.data.filter(v => v.status === 'pending').length : 0 }}</dd>
              </dl>
            </div>
          </div>
        </div>
      </div>

      <!-- Filters -->
      <div class="bg-white shadow rounded-lg mb-6">
        <div class="px-4 py-5 sm:p-6">

          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
              <input 
                type="text" 
                v-model="form.search" 
                @input="debouncedSearch"
                placeholder="Search by license plate, make, model, or owner..."
                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
              <select 
                v-model="form.status" 
                @change="search"
                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
                <option value="">All Status</option>
                <option value="pending">Pending</option>
                <option value="approved">Approved</option>
                <option value="rejected">Rejected</option>
                <option value="suspended">Suspended</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Availability</label>
              <select 
                v-model="form.available" 
                @change="search"
                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
                <option value="">All Availability</option>
                <option value="1">Available</option>
                <option value="0">Unavailable</option>
              </select>
            </div>
            <div class="flex items-end">
              <button 
                @click="clearFilters"
                class="w-full bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded"
              >
                Clear Filters
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Vehicles Table -->
      <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="px-4 py-5 sm:p-6">

          <!-- Loading State -->
          <div v-if="processing" class="text-center py-8">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            <p class="mt-2 text-gray-600">Loading vehicles...</p>
          </div>

          <!-- Vehicles Table -->
          <div v-else-if="vehicles.data && vehicles.data.length > 0" class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vehicle</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Owner</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Details</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Availability</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bookings</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="vehicle in vehicles.data" :key="vehicle.id" class="hover:bg-gray-50">
                  <td class="px-6 py-4 whitespace-nowrap">
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
                        <div class="text-sm font-medium text-gray-900">{{ vehicleName(vehicle) }}</div>
                        <div class="text-sm text-gray-500">{{ vehicle.license_plate }}</div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">{{ vehicle.owner?.first_name }} {{ vehicle.owner?.last_name }}</div>
                    <div class="text-sm text-gray-500">{{ vehicle.owner?.email }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">
                      <div><strong>Type:</strong> {{ vehicle.type?.name }}</div>
                      <div><strong>Year:</strong> {{ vehicle.year }}</div>
                      <div><strong>Fuel:</strong> {{ vehicle.fuel_type?.name }}</div>
                      <div><strong>Transmission:</strong> {{ vehicle.transmission?.name }}</div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="getStatusBadgeClass(vehicle.status)">
                      {{ vehicle.status ? vehicle.status.charAt(0).toUpperCase() + vehicle.status.slice(1) : 'Unknown' }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="vehicle.is_available 
                      ? 'bg-green-100 text-green-800 px-2 inline-flex text-xs leading-5 font-semibold rounded-full' 
                      : 'bg-red-100 text-red-800 px-2 inline-flex text-xs leading-5 font-semibold rounded-full'">
                      {{ vehicle.is_available ? 'Available' : 'Unavailable' }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    <div class="text-gray-900 font-medium">{{ vehicle.bookings_count || 0 }}</div>
                    <div class="text-xs text-gray-500">bookings</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <div class="flex space-x-2">
                      <button 
                        v-if="vehicle.status === 'pending'"
                        @click="approveVehicle(vehicle)"
                        :disabled="processing"
                        class="text-green-600 hover:text-green-900"
                      >
                        Approve
                      </button>
                      <button 
                        v-if="vehicle.status === 'pending'"
                        @click="rejectVehicle(vehicle)"
                        :disabled="processing"
                        class="text-red-600 hover:text-red-900"
                      >
                        Reject
                      </button>
                      <button 
                        v-if="vehicle.status === 'approved'"
                        @click="suspendVehicle(vehicle)"
                        :disabled="processing"
                        class="text-yellow-600 hover:text-yellow-900"
                      >
                        Suspend
                      </button>
                      <Link 
                        :href="`/vehicles/${vehicle.id}`"
                        class="text-blue-600 hover:text-blue-900"
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
          <div v-if="vehicles.links && vehicles.links.length > 3" class="mt-6">
            <nav class="flex items-center justify-between">
              <div class="flex-1 flex justify-between sm:hidden">
                <Link 
                  v-if="vehicles.prev_page_url" 
                  :href="vehicles.prev_page_url" 
                  class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                >
                  Previous
                </Link>
                <Link 
                  v-if="vehicles.next_page_url" 
                  :href="vehicles.next_page_url" 
                  class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                >
                  Next
                </Link>
              </div>
              <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                  <p class="text-sm text-gray-700">
                    Showing {{ vehicles.from }} to {{ vehicles.to }} of {{ vehicles.total }} results
                  </p>
                </div>
                <div>
                  <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                    <Link 
                      v-for="link in vehicles.links" 
                      :key="link.label"
                      :href="link.url || '#'"
                      v-html="link.label"
                      :class="[
                        'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                        link.active 
                          ? 'z-10 bg-blue-50 border-blue-500 text-blue-600' 
                          : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
                        !link.url ? 'cursor-not-allowed opacity-50' : 'cursor-pointer'
                      ]"
                    />
                  </nav>
                </div>
              </div>
            </nav>
          </div>
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
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
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
  const classes = 'px-2 inline-flex text-xs leading-5 font-semibold rounded-full ';
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

const clearFilters = () => {
  form.search = '';
  form.status = '';
  form.available = '';
  search();
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
