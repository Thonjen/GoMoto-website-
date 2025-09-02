<template>
  <AuthenticatedLayout>
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="mb-8">
        <div class="flex justify-between items-center">
          <div>
            <h1 class="text-3xl font-bold text-white">Vehicle Management</h1>
            <p class="mt-2 text-white/70">Manage all vehicles in the system</p>
          </div>
          <Link :href="route('admin.dashboard')" class="bg-white/10 hover:bg-white/20 text-white font-bold py-2 px-4 rounded-md backdrop-blur-sm border border-white/20 transition-colors">
            Back to Dashboard
          </Link>
        </div>
      </div>

      <!-- Statistics Cards -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-blue-400/20 shadow-glow rounded-lg p-6 backdrop-blur-sm border border-white/20">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-8 h-8 bg-blue-400 rounded-md flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
              </div>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-white/70 truncate">Total Vehicles</dt>
                <dd class="text-lg font-medium text-white">{{ vehicles.data ? vehicles.data.length : 0 }}</dd>
              </dl>
            </div>
          </div>
        </div>
        <div class="bg-green-400/20 shadow-glow rounded-lg p-6 backdrop-blur-sm border border-white/20">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-8 h-8 bg-green-400 rounded-md flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
              </div>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-white/70 truncate">Approved</dt>
                <dd class="text-lg font-medium text-white">{{ vehicles.data ? vehicles.data.filter(v => v.status === 'approved').length : 0 }}</dd>
              </dl>
            </div>
          </div>
        </div>
        <div class="bg-yellow-400/20 shadow-glow rounded-lg p-6 backdrop-blur-sm border border-white/20">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-8 h-8 bg-yellow-400 rounded-md flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-white/70 truncate">Pending</dt>
                <dd class="text-lg font-medium text-white">{{ vehicles.data ? vehicles.data.filter(v => v.status === 'pending').length : 0 }}</dd>
              </dl>
            </div>
          </div>
        </div>
      </div>

      <!-- Filters -->
      <div class="glass-card-dark mb-6">
        <div class="px-4 py-5 sm:p-6">

          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
              <label class="block text-sm font-medium text-white mb-2">Search</label>
              <input 
                type="text" 
                v-model="form.search" 
                @input="debouncedSearch"
                placeholder="Search by license plate, make, model, or owner..."
                class="w-full bg-white/10 border border-white/20 rounded-md px-3 py-2 text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 backdrop-blur-sm"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-white mb-2">Status</label>
              <select 
                v-model="form.status" 
                @change="search"
                class="w-full bg-white/10 border border-white/20 rounded-md px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 backdrop-blur-sm"
              >
                <option value="" class="bg-gray-800 text-white">All Status</option>
                <option value="pending" class="bg-gray-800 text-white">Pending</option>
                <option value="approved" class="bg-gray-800 text-white">Approved</option>
                <option value="rejected" class="bg-gray-800 text-white">Rejected</option>
                <option value="suspended" class="bg-gray-800 text-white">Suspended</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-white mb-2">Availability</label>
              <select 
                v-model="form.available" 
                @change="search"
                class="w-full bg-white/10 border border-white/20 rounded-md px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 backdrop-blur-sm"
              >
                <option value="" class="bg-gray-800 text-white">All Availability</option>
                <option value="1" class="bg-gray-800 text-white">Available</option>
                <option value="0" class="bg-gray-800 text-white">Unavailable</option>
              </select>
            </div>
            <div class="flex items-end">
              <button 
                @click="clearFilters"
                class="w-full bg-white/10 hover:bg-white/20 text-white font-bold py-2 px-4 rounded-md backdrop-blur-sm border border-white/20 transition-colors"
              >
                Clear Filters
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Vehicles Table -->
      <div class="glass-card-dark overflow-hidden">
        <div class="px-4 py-5 sm:p-6">

          <!-- Loading State -->
          <div v-if="processing" class="text-center py-8">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-400"></div>
            <p class="mt-2 text-white/70">Loading vehicles...</p>
          </div>

          <!-- Vehicles Table -->
          <div v-else-if="vehicles.data && vehicles.data.length > 0" class="overflow-x-auto">
            <table class="min-w-full glass-card border border-white/20 rounded-lg">
              <thead class="bg-white/10 backdrop-blur-sm">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider border-b border-white/20">Vehicle</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider border-b border-white/20">Owner</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider border-b border-white/20">Details</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider border-b border-white/20">Status</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider border-b border-white/20">Availability</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider border-b border-white/20">Bookings</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider border-b border-white/20">Actions</th>
                </tr>
              </thead>
              <tbody class="glass-card divide-y divide-white/10">
                <tr v-for="vehicle in vehicles.data" :key="vehicle.id" class="hover:bg-white/5 transition-colors">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center space-x-3">
                      <div class="flex-shrink-0 h-12 w-12">
                        <img 
                          v-if="vehicle.photos && vehicle.photos.length > 0" 
                          :src="vehicle.photos[0].url" 
                          :alt="vehicleName(vehicle)"
                          class="h-12 w-12 rounded-lg object-cover border border-white/20"
                        />
                        <div v-else class="h-12 w-12 bg-white/10 rounded-lg flex items-center justify-center border border-white/20 backdrop-blur-sm">
                          <svg class="h-6 w-6 text-white/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                          </svg>
                        </div>
                      </div>
                      <div>
                        <div class="text-sm font-medium text-white">{{ vehicleName(vehicle) }}</div>
                        <div class="text-sm text-white/70">{{ vehicle.license_plate }}</div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-white">{{ vehicle.owner?.first_name }} {{ vehicle.owner?.last_name }}</div>
                    <div class="text-sm text-white/70">{{ vehicle.owner?.email }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-white/90">
                      <div><strong class="text-white">Type:</strong> {{ vehicle.type?.name }}</div>
                      <div><strong class="text-white">Year:</strong> {{ vehicle.year }}</div>
                      <div><strong class="text-white">Fuel:</strong> {{ vehicle.fuel_type?.name }}</div>
                      <div><strong class="text-white">Transmission:</strong> {{ vehicle.transmission?.name }}</div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="[
                      'px-3 py-1 rounded-full text-xs font-medium backdrop-blur-sm',
                      vehicle.status === 'approved' ? 'bg-green-400/20 text-green-400 border border-green-400/30' :
                      vehicle.status === 'pending' ? 'bg-yellow-400/20 text-yellow-400 border border-yellow-400/30' :
                      vehicle.status === 'rejected' ? 'bg-red-400/20 text-red-400 border border-red-400/30' :
                      'bg-gray-400/20 text-gray-400 border border-gray-400/30'
                    ]">
                      {{ vehicle.status ? vehicle.status.charAt(0).toUpperCase() + vehicle.status.slice(1) : 'Unknown' }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="[
                      'px-3 py-1 rounded-full text-xs font-medium backdrop-blur-sm',
                      vehicle.is_available 
                        ? 'bg-green-400/20 text-green-400 border border-green-400/30' 
                        : 'bg-red-400/20 text-red-400 border border-red-400/30'
                    ]">
                      {{ vehicle.is_available ? 'Available' : 'Unavailable' }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-white/70">
                    <div class="text-white font-medium">{{ vehicle.bookings_count || 0 }}</div>
                    <div class="text-xs text-white/50">bookings</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <div class="flex space-x-2">
                      <button 
                        v-if="vehicle.status === 'pending'"
                        @click="approveVehicle(vehicle)"
                        :disabled="processing"
                        class="bg-green-400/20 text-green-400 hover:bg-green-400/30 px-3 py-1 rounded-md text-xs font-medium backdrop-blur-sm border border-green-400/30 transition-colors"
                      >
                        Approve
                      </button>
                      <button 
                        v-if="vehicle.status === 'pending'"
                        @click="rejectVehicle(vehicle)"
                        :disabled="processing"
                        class="bg-red-400/20 text-red-400 hover:bg-red-400/30 px-3 py-1 rounded-md text-xs font-medium backdrop-blur-sm border border-red-400/30 transition-colors"
                      >
                        Reject
                      </button>
                      <button 
                        v-if="vehicle.status === 'approved'"
                        @click="suspendVehicle(vehicle)"
                      >
                        Suspend
                      </button>
                      <button 
                        v-if="vehicle.status === 'approved'"
                        @click="suspendVehicle(vehicle)"
                        :disabled="processing"
                        class="bg-yellow-400/20 text-yellow-400 hover:bg-yellow-400/30 px-3 py-1 rounded-md text-xs font-medium backdrop-blur-sm border border-yellow-400/30 transition-colors"
                      >
                        Suspend
                      </button>
                      <Link 
                        :href="`/vehicles/${vehicle.id}`"
                        class="bg-blue-400/20 text-blue-400 hover:bg-blue-400/30 px-3 py-1 rounded-md text-xs font-medium backdrop-blur-sm border border-blue-400/30 transition-colors"
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
            <svg class="mx-auto h-12 w-12 text-white/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-white">No vehicles found</h3>
            <p class="mt-1 text-sm text-white/70">Try adjusting your search criteria.</p>
          </div>

          <!-- Pagination -->
          <div v-if="vehicles.links && vehicles.links.length > 3" class="mt-6">
            <nav class="flex items-center justify-between">
              <div class="flex-1 flex justify-between sm:hidden">
                <Link 
                  v-if="vehicles.prev_page_url" 
                  :href="vehicles.prev_page_url" 
                  class="relative inline-flex items-center px-4 py-2 border border-white/20 text-sm font-medium rounded-md text-white bg-white/10 hover:bg-white/20 backdrop-blur-sm transition-colors"
                >
                  Previous
                </Link>
                <Link 
                  v-if="vehicles.next_page_url" 
                  :href="vehicles.next_page_url" 
                  class="ml-3 relative inline-flex items-center px-4 py-2 border border-white/20 text-sm font-medium rounded-md text-white bg-white/10 hover:bg-white/20 backdrop-blur-sm transition-colors"
                >
                  Next
                </Link>
              </div>
              <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                  <p class="text-sm text-white/70">
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
                        'relative inline-flex items-center px-4 py-2 border border-white/20 text-sm font-medium backdrop-blur-sm transition-colors',
                        link.active 
                          ? 'z-10 bg-blue-400/20 border-blue-400/30 text-blue-400' 
                          : 'bg-white/10 text-white/70 hover:bg-white/20 hover:text-white',
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
    <div v-if="$page.props.flash?.success" class="fixed top-4 right-4 bg-green-400/20 backdrop-blur-sm border border-green-400/30 text-green-400 px-6 py-3 rounded-lg shadow-glow z-50">
      {{ $page.props.flash.success }}
    </div>
    <div v-if="$page.props.flash?.error" class="fixed top-4 right-4 bg-red-400/20 backdrop-blur-sm border border-red-400/30 text-red-400 px-6 py-3 rounded-lg shadow-glow z-50">
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
