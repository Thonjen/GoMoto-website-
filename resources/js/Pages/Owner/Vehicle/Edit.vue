<template>
  <div>
    <h1 class="text-2xl font-bold mb-4">Edit Vehicle</h1>
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
      <button class="bg-blue-600 text-white px-4 py-2 rounded">Update</button>
    </form>
    <Link :href="`/owner/vehicles/${vehicle.id}`" class="ml-2 text-blue-600">Back</Link>
  </div>
</template>

<script setup>
import { reactive } from 'vue';
import { router, Link } from '@inertiajs/vue3';
const props = defineProps({ vehicle: Object, brands: Array, types: Array, fuelTypes: Array });

const form = reactive({
  license_plate: props.vehicle.license_plate,
  brand_id: props.vehicle.brand_id,
  type_id: props.vehicle.type_id,
  fuel_type_id: props.vehicle.fuel_type_id,
  year: props.vehicle.year,
  color: props.vehicle.color,
  description: props.vehicle.description,
  is_available: props.vehicle.is_available,
});

function submit() {
  router.put(`/owner/vehicles/${props.vehicle.id}`, form);
}
</script>
