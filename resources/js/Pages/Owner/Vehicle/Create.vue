<template>
  <div>
    <h1 class="text-2xl font-bold mb-4">Add Vehicle</h1>
    <form @submit.prevent="submit">
      <div class="mb-2">
        <label>License Plate</label>
        <input v-model="form.license_plate" class="border p-2 w-full" required />
      </div>
      <div class="mb-2">
        <label>Brand</label>
        <select v-model="form.brand_id" class="border p-2 w-full" required>
          <option v-for="b in brands" :key="b.id" :value="b.id">{{ b.name }}</option>
        </select>
      </div>
      <div class="mb-2">
        <label>Type</label>
        <select v-model="form.type_id" class="border p-2 w-full" required>
          <option v-for="t in types" :key="t.id" :value="t.id">{{ t.category }}</option>
        </select>
      </div>
      <div class="mb-2">
        <label>Fuel Type</label>
        <select v-model="form.fuel_type_id" class="border p-2 w-full" required>
          <option v-for="f in fuelTypes" :key="f.id" :value="f.id">{{ f.name }}</option>
        </select>
      </div>
      <div class="mb-2">
        <label>Year</label>
        <input v-model="form.year" type="number" class="border p-2 w-full" required />
      </div>
      <div class="mb-2">
        <label>Color</label>
        <input v-model="form.color" class="border p-2 w-full" required />
      </div>
      <div class="mb-2">
        <label>Description</label>
        <textarea v-model="form.description" class="border p-2 w-full"></textarea>
      </div>
      <div class="mb-2">
        <label>
          <input type="checkbox" v-model="form.is_available" />
          Available
        </label>
      </div>
      <button class="bg-blue-600 text-white px-4 py-2 rounded">Save</button>
    </form>
  </div>
</template>

<script setup>
import { reactive } from 'vue';
import { router } from '@inertiajs/vue3';
const props = defineProps({ brands: Array, types: Array, fuelTypes: Array });

const form = reactive({
  license_plate: '',
  brand_id: '',
  type_id: '',
  fuel_type_id: '',
  year: '',
  color: '',
  description: '',
  is_available: true,
});

function submit() {
  router.post('/owner/vehicles', form);
}
</script>
