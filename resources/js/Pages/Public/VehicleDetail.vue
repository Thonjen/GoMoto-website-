<template>
  <AppLayout>
    <div v-if="vehicle" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <div class="lg:col-span-2 bg-white p-6 rounded-lg shadow-md">
        <img :src="vehicle.imageUrl" :alt="vehicle.name" class="w-full h-96 object-cover rounded-lg mb-6" />
        <h1 class="text-3xl font-bold text-gray-800 mb-2">{{ vehicle.name }}</h1>
        <p class="text-gray-600 text-lg mb-4">{{ vehicle.location }}</p>

        <div class="grid grid-cols-2 gap-4 mb-6 text-gray-700">
          <div class="flex items-center gap-2">
            <Car class="h-5 w-5 text-primary-600" />
            <span>{{ vehicle.type }}</span>
          </div>
          <div class="flex items-center gap-2">
            <DollarSign class="h-5 w-5 text-primary-600" />
            <span>${{ vehicle.pricePerDay }}/day</span>
          </div>
          <div class="flex items-center gap-2">
            <Users class="h-5 w-5 text-primary-600" />
            <span>Seats: {{ vehicle.seats }}</span>
          </div>
          <div class="flex items-center gap-2">
            <Fuel class="h-5 w-5 text-primary-600" />
            <span>{{ vehicle.fuelType }}</span>
          </div>
        </div>

        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Description</h2>
        <p class="text-gray-700 leading-relaxed mb-6">{{ vehicle.description }}</p>

        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Owner Information</h2>
        <div class="flex items-center gap-4 mb-6">
          <img :src="vehicle.owner.avatarUrl" alt="Owner Avatar" class="w-16 h-16 rounded-full object-cover" />
          <div>
            <p class="font-semibold text-gray-800">{{ vehicle.owner.name }}</p>
            <p class="text-sm text-gray-600">Member since {{ vehicle.owner.memberSince }}</p>
          </div>
        </div>

        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Location Map</h2>
        <div class="w-full h-64 bg-gray-200 rounded-lg flex items-center justify-center text-gray-500">
          <MapPin class="h-8 w-8 mr-2" />
          Placeholder Map
        </div>
      </div>

      <div class="lg:col-span-1 bg-white p-6 rounded-lg shadow-md h-fit sticky top-24">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Book This Vehicle</h2>
        <form @submit.prevent="submitBooking">
          <div class="mb-4">
            <label for="pickupDate" class="block text-sm font-medium text-gray-700 mb-2">Pickup Date</label>
            <input type="date" id="pickupDate" v-model="bookingForm.pickupDate" required
              class="w-full p-2 border border-gray-300 rounded-md focus:ring-primary-400 focus:border-primary-400" />
          </div>
          <div class="mb-4">
            <label for="returnDate" class="block text-sm font-medium text-gray-700 mb-2">Return Date</label>
            <input type="date" id="returnDate" v-model="bookingForm.returnDate" required
              class="w-full p-2 border border-gray-300 rounded-md focus:ring-primary-400 focus:border-primary-400" />
          </div>
          <div class="mb-6">
            <label for="message" class="block text-sm font-medium text-gray-700 mb-2">Message to Owner (Optional)</label>
            <textarea id="message" v-model="bookingForm.message" rows="3"
              class="w-full p-2 border border-gray-300 rounded-md focus:ring-primary-400 focus:border-primary-400"></textarea>
          </div>
          <button type="submit"
            class="w-full bg-primary-600 text-white px-6 py-3 rounded-md font-semibold hover:bg-primary-700 transition-colors flex items-center justify-center gap-2">
            <CalendarCheck class="h-5 w-5" />
            Request Booking
          </button>
        </form>
      </div>
    </div>
    <div v-else class="text-center py-12 text-gray-600">
      <p>Loading vehicle details or vehicle not found...</p>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3'; // Assuming usePage is available for props
import AppLayout from '@/Layouts/AppLayout.vue';
import { Car, DollarSign, Users, Fuel, MapPin, CalendarCheck } from 'lucide-vue-next';

// In a real Inertia app, vehicle data would be passed as props
const page = usePage();
const vehicle = ref(null); // This would be page.props.vehicle

// Simulate fetching vehicle data based on ID from route params
onMounted(() => {
  const vehicleId = page.props.id || 1; // Get ID from route params, default to 1 for demo
  // In a real app, you'd fetch this from your backend
  vehicle.value = {
    id: vehicleId,
    name: 'Toyota Camry 2022',
    location: 'Manila, Philippines',
    type: 'Sedan',
    pricePerDay: 1800,
    seats: 5,
    fuelType: 'Gasoline',
    description: 'A comfortable and reliable Toyota Camry, perfect for city driving or long road trips. Well-maintained and spacious interior. Equipped with air conditioning, Bluetooth, and GPS.',
    imageUrl: '/placeholder.svg?height=400&width=600',
    owner: {
      name: 'Juan Dela Cruz',
      memberSince: 'January 2023',
      avatarUrl: '/placeholder.svg?height=64&width=64',
    },
  };
});

const bookingForm = ref({
  pickupDate: '',
  returnDate: '',
  message: '',
});

const submitBooking = () => {
  if (!bookingForm.value.pickupDate || !bookingForm.value.returnDate) {
    alert('Please select both pickup and return dates.');
    return;
  }
  if (new Date(bookingForm.value.pickupDate) >= new Date(bookingForm.value.returnDate)) {
    alert('Return date must be after pickup date.');
    return;
  }
  alert(`Booking request for ${vehicle.value.name} from ${bookingForm.value.pickupDate} to ${bookingForm.value.returnDate}. Message: "${bookingForm.value.message}"`);
  // In a real Inertia app, this would be an Inertia.post('/bookings', bookingForm.value)
};
</script>

<style scoped>
/* Tailwind CSS is used for styling */
</style>
