<template>
  <AppLayout>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
      <aside class="md:col-span-1 bg-white p-6 rounded-lg shadow-md h-fit">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Account Settings</h2>
        <nav class="flex flex-col gap-2">
          <Link href="/profile" class="text-gray-700 hover:underline">My Profile</Link>
          <Link href="/profile/address" class="text-gray-700 hover:underline">My Address</Link>
          <Link href="/profile/license" class="text-gray-700 hover:underline">My License</Link>
          <Link href="/notifications" class="text-primary-600 font-medium hover:underline">Notifications</Link>
        </nav>
      </aside>

      <div class="md:col-span-3 bg-white p-6 rounded-lg shadow-md">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Notifications</h1>

        <div v-if="notifications.length > 0" class="space-y-4">
          <div v-for="notification in notifications" :key="notification.id"
            :class="['p-4 rounded-md border', notification.read ? 'bg-gray-50 border-gray-200' : 'bg-blue-50 border-blue-200']">
            <div class="flex items-start gap-3">
              <Bell v-if="!notification.read" class="h-5 w-5 text-blue-600 shrink-0 mt-1" />
              <BellOff v-else class="h-5 w-5 text-gray-500 shrink-0 mt-1" />
              <div class="flex-grow">
                <p class="font-semibold text-gray-800">{{ notification.title }}</p>
                <p class="text-sm text-gray-700 mt-1">{{ notification.message }}</p>
                <p class="text-xs text-gray-500 mt-2">{{ notification.time }}</p>
                <div class="mt-3 flex gap-2">
                  <button v-if="!notification.read" @click="markAsRead(notification.id)"
                    class="text-sm text-primary-600 hover:underline">Mark as Read</button>
                  <button @click="deleteNotification(notification.id)"
                    class="text-sm text-red-600 hover:underline">Delete</button>
                </div>
              </div>
            </div>
          </div>
        </div>
        <p v-else class="text-gray-600 text-center py-8">You have no new notifications.</p>

        <div class="mt-8 flex justify-end">
          <button @click="markAllAsRead" v-if="unreadCount > 0"
            class="bg-primary-600 text-white px-6 py-3 rounded-md font-semibold hover:bg-primary-700 transition-colors flex items-center justify-center gap-2">
            <CheckCircle class="h-5 w-5" />
            Mark All as Read
          </button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Bell, BellOff, CheckCircle } from 'lucide-vue-next';

const notifications = ref([
  { id: 1, title: 'New Booking Request', message: 'You have a new booking request for Toyota Camry from John Doe.', time: '2 hours ago', read: false },
  { id: 2, title: 'License Verified', message: 'Your driver\'s license has been successfully verified!', time: '1 day ago', read: false },
  { id: 3, title: 'Booking Confirmed', message: 'Your booking for Honda CRV has been confirmed by the owner.', time: '3 days ago', read: true },
  { id: 4, title: 'Payment Received', message: 'You received a payment of ₱3,000 for your recent booking.', time: '5 days ago', read: true },
]);

const unreadCount = computed(() => notifications.value.filter(n => !n.read).length);

const markAsRead = (id) => {
  const notification = notifications.value.find(n => n.id === id);
  if (notification) {
    notification.read = true;
    alert(`Notification ${id} marked as read.`);
    // In a real Inertia app, this would be an Inertia.put('/notifications/{id}/read')
  }
};

const deleteNotification = (id) => {
  if (confirm('Are you sure you want to delete this notification?')) {
    notifications.value = notifications.value.filter(n => n.id !== id);
    alert(`Notification ${id} deleted.`);
    // In a real Inertia app, this would be an Inertia.delete('/notifications/{id}')
  }
};

const markAllAsRead = () => {
  notifications.value.forEach(n => n.read = true);
  alert('All notifications marked as read.');
  // In a real Inertia app, this would be an Inertia.post('/notifications/mark-all-read')
};
</script>

<style scoped>
/* Tailwind CSS is used for styling */
</style>
