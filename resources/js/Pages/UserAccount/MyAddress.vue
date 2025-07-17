<template>
  <AppLayout>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
      <aside class="md:col-span-1 bg-white p-6 rounded-lg shadow-md h-fit">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Account Settings</h2>
        <nav class="flex flex-col gap-2">
          <Link href="/profile" class="text-gray-700 hover:underline">My Profile</Link>
          <Link href="/profile/address" class="text-primary-600 font-medium hover:underline">My Address</Link>
          <Link href="/profile/license" class="text-gray-700 hover:underline">My License</Link>
          <Link href="/notifications" class="text-gray-700 hover:underline">Notifications</Link>
        </nav>
      </aside>

      <div class="md:col-span-3 bg-white p-6 rounded-lg shadow-md">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">My Address</h1>

        <div v-if="addresses.length > 0" class="space-y-6 mb-8">
          <div v-for="address in addresses" :key="address.id"
            class="border border-gray-200 rounded-md p-4 relative">
            <h3 class="font-semibold text-lg text-gray-800 mb-2">{{ address.label }}</h3>
            <p class="text-gray-700">{{ address.street }}, {{ address.city }}, {{ address.province }} {{ address.zip }}</p>
            <p class="text-gray-600">{{ address.country }}</p>
            <div class="flex gap-2 mt-4">
              <button @click="editAddress(address)"
                class="text-primary-600 hover:underline text-sm flex items-center gap-1">
                <Edit class="h-4 w-4" /> Edit
              </button>
              <button @click="deleteAddress(address.id)"
                class="text-red-600 hover:underline text-sm flex items-center gap-1">
                <Trash2 class="h-4 w-4" /> Delete
              </button>
            </div>
          </div>
        </div>
        <p v-else class="text-gray-600 mb-8">No addresses added yet.</p>

        <h2 class="text-2xl font-semibold text-gray-800 mb-4">{{ editingAddressId ? 'Edit Address' : 'Add New Address' }}</h2>
        <form @submit.prevent="saveAddress">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
              <label for="label" class="block text-sm font-medium text-gray-700 mb-2">Address Label (e.g., Home, Office)</label>
              <input type="text" id="label" v-model="form.label" required
                class="w-full p-3 border border-gray-300 rounded-md focus:ring-primary-400 focus:border-primary-400" />
            </div>
            <div>
              <label for="street" class="block text-sm font-medium text-gray-700 mb-2">Street Address</label>
              <input type="text" id="street" v-model="form.street" required
                class="w-full p-3 border border-gray-300 rounded-md focus:ring-primary-400 focus:border-primary-400" />
            </div>
            <div>
              <label for="city" class="block text-sm font-medium text-gray-700 mb-2">City</label>
              <input type="text" id="city" v-model="form.city" required
                class="w-full p-3 border border-gray-300 rounded-md focus:ring-primary-400 focus:border-primary-400" />
            </div>
            <div>
              <label for="province" class="block text-sm font-medium text-gray-700 mb-2">Province</label>
              <input type="text" id="province" v-model="form.province" required
                class="w-full p-3 border border-gray-300 rounded-md focus:ring-primary-400 focus:border-primary-400" />
            </div>
            <div>
              <label for="zip" class="block text-sm font-medium text-gray-700 mb-2">Zip Code</label>
              <input type="text" id="zip" v-model="form.zip" required
                class="w-full p-3 border border-gray-300 rounded-md focus:ring-primary-400 focus:border-primary-400" />
            </div>
            <div>
              <label for="country" class="block text-sm font-medium text-gray-700 mb-2">Country</label>
              <input type="text" id="country" v-model="form.country" required
                class="w-full p-3 border border-gray-300 rounded-md focus:ring-primary-400 focus:border-primary-400" />
            </div>
          </div>
          <button type="submit"
            class="bg-primary-600 text-white px-6 py-3 rounded-md font-semibold hover:bg-primary-700 transition-colors flex items-center justify-center gap-2">
            <Save class="h-5 w-5" />
            {{ editingAddressId ? 'Update Address' : 'Add Address' }}
          </button>
          <button v-if="editingAddressId" @click="cancelEdit" type="button"
            class="ml-4 bg-gray-200 text-gray-800 px-6 py-3 rounded-md font-semibold hover:bg-gray-300 transition-colors">
            Cancel
          </button>
        </form>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Save, Edit, Trash2 } from 'lucide-vue-next';

const addresses = ref([
  { id: 1, label: 'Home', street: '123 Main St', city: 'Quezon City', province: 'Metro Manila', zip: '1100', country: 'Philippines' },
  { id: 2, label: 'Office', street: '456 Business Ave', city: 'Makati', province: 'Metro Manila', zip: '1200', country: 'Philippines' },
]);

const form = ref({
  label: '',
  street: '',
  city: '',
  province: '',
  zip: '',
  country: 'Philippines',
});

const editingAddressId = ref(null);

const resetForm = () => {
  form.value = {
    label: '',
    street: '',
    city: '',
    province: '',
    zip: '',
    country: 'Philippines',
  };
  editingAddressId.value = null;
};

const saveAddress = () => {
  if (editingAddressId.value) {
    const index = addresses.value.findIndex(addr => addr.id === editingAddressId.value);
    if (index !== -1) {
      addresses.value[index] = { ...form.value, id: editingAddressId.value };
      alert('Address updated successfully! (Not actually saved in this demo)');
    }
  } else {
    const newId = addresses.value.length > 0 ? Math.max(...addresses.value.map(a => a.id)) + 1 : 1;
    addresses.value.push({ ...form.value, id: newId });
    alert('Address added successfully! (Not actually saved in this demo)');
  }
  resetForm();
  // In a real Inertia app, this would be an Inertia.post or Inertia.put request
};

const editAddress = (address) => {
  form.value = { ...address };
  editingAddressId.value = address.id;
};

const deleteAddress = (id) => {
  if (confirm('Are you sure you want to delete this address?')) {
    addresses.value = addresses.value.filter(addr => addr.id !== id);
    alert('Address deleted! (Not actually deleted in this demo)');
    // In a real Inertia app, this would be an Inertia.delete request
  }
};

const cancelEdit = () => {
  resetForm();
};
</script>

<style scoped>
/* Tailwind CSS is used for styling */
</style>
