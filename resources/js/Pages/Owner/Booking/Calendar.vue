<template>
    <OwnerLayout>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-6">
                            <div>
                                <h1 class="text-3xl font-bold text-gray-800">Booking Calendar</h1>
                                <p class="text-gray-600 mt-2">View all your vehicle bookings in calendar format</p>
                            </div>
                            
                            <!-- View Toggle Buttons -->
                            <div class="flex bg-gray-100 rounded-lg p-1 mr-4">
                                <Link
                                    :href="route('owner.bookings.index')"
                                    class="px-3 py-2 rounded-md text-sm font-medium transition-all hover:bg-white hover:shadow"
                                >
                                    List View
                                </Link>
                                <Link
                                    :href="route('owner.bookings.calendar')"
                                    class="px-3 py-2 rounded-md text-sm font-medium transition-all bg-white shadow"
                                >
                                    Calendar View
                                </Link>
                            </div>
                            
                            <!-- Calendar View Toggle Buttons -->
                            <div class="flex bg-gray-100 rounded-lg p-1">
                                <button
                                    @click="currentView = 'dayGridMonth'"
                                    :class="currentView === 'dayGridMonth' ? 'bg-white shadow' : ''"
                                    class="px-3 py-2 rounded-md text-sm font-medium transition-all"
                                >
                                    Month
                                </button>
                                <button
                                    @click="currentView = 'timeGridWeek'"
                                    :class="currentView === 'timeGridWeek' ? 'bg-white shadow' : ''"
                                    class="px-3 py-2 rounded-md text-sm font-medium transition-all"
                                >
                                    Week
                                </button>
                                <button
                                    @click="currentView = 'timeGridDay'"
                                    :class="currentView === 'timeGridDay' ? 'bg-white shadow' : ''"
                                    class="px-3 py-2 rounded-md text-sm font-medium transition-all"
                                >
                                    Day
                                </button>
                            </div>
                        </div>

                        <!-- Vehicle Filter -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Filter by Vehicle</label>
                            <select v-model="selectedVehicleId" @change="filterEvents" class="w-64 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">All Vehicles</option>
                                <option v-for="vehicle in vehicles" :key="vehicle.id" :value="vehicle.id">
                                    {{ vehicle.brand?.name }} {{ vehicle.type?.sub_type }} ({{ vehicle.license_plate }})
                                </option>
                            </select>
                        </div>

                        <!-- Status Legend -->
                        <div class="mb-6 flex flex-wrap gap-4 text-sm">
                            <div class="flex items-center">
                                <div class="w-4 h-4 bg-yellow-500 rounded mr-2"></div>
                                <span>Pending</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-4 h-4 bg-green-500 rounded mr-2"></div>
                                <span>Confirmed</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-4 h-4 bg-gray-500 rounded mr-2"></div>
                                <span>Completed</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-4 h-4 bg-red-500 rounded mr-2"></div>
                                <span>Cancelled</span>
                            </div>
                        </div>

                        <!-- Calendar -->
                        <FullCalendar
                            ref="fullCalendar"
                            :options="calendarOptions"
                            class="bg-white"
                        />
                    </div>
                </div>

        <!-- Event Details Modal -->
        <div v-if="showEventModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeEventModal"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modal-title">
                                    Booking Details
                                </h3>
                                
                                <div v-if="selectedEvent" class="space-y-4">
                                    <!-- Booking Status -->
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm font-medium text-gray-500">Status:</span>
                                        <span :class="getStatusClass(selectedEvent.extendedProps.status)" 
                                              class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                                            {{ formatStatus(selectedEvent.extendedProps.status) }}
                                        </span>
                                    </div>

                                    <!-- Vehicle Info -->
                                    <div>
                                        <span class="text-sm font-medium text-gray-500">Vehicle:</span>
                                        <p class="text-sm text-gray-900">
                                            {{ selectedEvent.extendedProps.vehicle.brand }} {{ selectedEvent.extendedProps.vehicle.type }}
                                            <span class="text-gray-500">({{ selectedEvent.extendedProps.vehicle.license_plate }})</span>
                                        </p>
                                    </div>

                                    <!-- Customer Info -->
                                    <div>
                                        <span class="text-sm font-medium text-gray-500">Customer:</span>
                                        <p class="text-sm text-gray-900">{{ selectedEvent.extendedProps.user.name }}</p>
                                        <p class="text-xs text-gray-500">{{ selectedEvent.extendedProps.user.email }}</p>
                                        <p v-if="selectedEvent.extendedProps.user.phone" class="text-xs text-gray-500">
                                            {{ selectedEvent.extendedProps.user.phone }}
                                        </p>
                                    </div>

                                    <!-- Booking Details -->
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <span class="text-sm font-medium text-gray-500">Duration:</span>
                                            <p class="text-sm text-gray-900">{{ selectedEvent.extendedProps.duration }}</p>
                                        </div>
                                        <div>
                                            <span class="text-sm font-medium text-gray-500">Amount:</span>
                                            <p class="text-sm text-gray-900">₱{{ selectedEvent.extendedProps.amount }}</p>
                                        </div>
                                    </div>

                                    <!-- Timing -->
                                    <div>
                                        <span class="text-sm font-medium text-gray-500">Pickup Time:</span>
                                        <p class="text-sm text-gray-900">{{ formatDateTime(selectedEvent.start) }}</p>
                                    </div>
                                    <div>
                                        <span class="text-sm font-medium text-gray-500">Return Time:</span>
                                        <p class="text-sm text-gray-900">{{ formatDateTime(selectedEvent.end) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button 
                            @click="viewBookingDetails"
                            type="button" 
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm"
                        >
                            View Full Details
                        </button>
                        <button 
                            @click="closeEventModal"
                            type="button" 
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                        >
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </OwnerLayout>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import OwnerLayout from '@/Layouts/OwnerLayout.vue';
import FullCalendar from '@fullcalendar/vue3';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction';

const props = defineProps({
    events: Array,
    vehicles: Array,
});

const currentView = ref('dayGridMonth');
const selectedVehicleId = ref('');
const showEventModal = ref(false);
const selectedEvent = ref(null);
const fullCalendar = ref(null);

const filteredEvents = ref(props.events);

const calendarOptions = computed(() => ({
    plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
    initialView: currentView.value,
    headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: '',
    },
    events: filteredEvents.value,
    editable: false,
    selectable: false,
    selectMirror: true,
    dayMaxEvents: true,
    weekends: true,
    height: 'auto',
    eventClick: handleEventClick,
    eventDisplay: 'block',
    displayEventTime: true,
    displayEventEnd: true,
}));

function handleEventClick(clickInfo) {
    selectedEvent.value = clickInfo.event;
    showEventModal.value = true;
}

function closeEventModal() {
    showEventModal.value = false;
    selectedEvent.value = null;
}

function viewBookingDetails() {
    if (selectedEvent.value) {
        router.visit(route('owner.bookings.show', selectedEvent.value.extendedProps.booking_id));
    }
}

function filterEvents() {
    if (selectedVehicleId.value === '') {
        filteredEvents.value = props.events;
    } else {
        filteredEvents.value = props.events.filter(event => 
            event.extendedProps.vehicle.id == selectedVehicleId.value
        );
    }
    
    // Update calendar
    if (fullCalendar.value) {
        const calendarApi = fullCalendar.value.getApi();
        calendarApi.removeAllEvents();
        calendarApi.addEventSource(filteredEvents.value);
    }
}

function formatDateTime(dateString) {
    return new Date(dateString).toLocaleString('en-US', {
        weekday: 'short',
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

function formatStatus(status) {
    const statusMap = {
        'pending': 'Pending',
        'confirmed': 'Confirmed',
        'completed': 'Completed',
        'cancelled': 'Cancelled',
    };
    return statusMap[status] || status;
}

function getStatusClass(status) {
    const classes = {
        'pending': 'bg-yellow-100 text-yellow-800',
        'confirmed': 'bg-green-100 text-green-800',
        'completed': 'bg-gray-100 text-gray-800',
        'cancelled': 'bg-red-100 text-red-800',
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
}

onMounted(() => {
    // Initialize filter
    filterEvents();
});

// Watch for currentView changes and update calendar
watch(currentView, (newView) => {
    if (fullCalendar.value) {
        const calendarApi = fullCalendar.value.getApi();
        calendarApi.changeView(newView);
    }
});
</script>

<style>
/* FullCalendar theme customizations */
:root {
  --fc-border-color: #e5e7eb;
  --fc-today-bg-color: #dbeafe;
  --fc-event-bg-color: #3b82f6;
  --fc-event-border-color: #3b82f6;
}

.fc-theme-standard .fc-scrollgrid {
    border-color: var(--fc-border-color);
}

.fc-theme-standard td, .fc-theme-standard th {
    border-color: var(--fc-border-color);
}

.fc-event {
    border-radius: 4px;
    padding: 2px 4px;
    font-size: 12px;
    line-height: 1.2;
}

.fc-event-time {
    font-weight: 600;
}

.fc-event-title {
    font-weight: 500;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.fc-daygrid-event {
    margin-bottom: 1px;
}

.fc-timegrid-event {
    border-radius: 3px;
    padding: 1px 3px;
}

/* Mobile responsiveness */
@media (max-width: 768px) {
    .fc-toolbar {
        flex-direction: column;
        gap: 10px;
    }
    
    .fc-toolbar-chunk {
        display: flex;
        justify-content: center;
    }
}
</style>
