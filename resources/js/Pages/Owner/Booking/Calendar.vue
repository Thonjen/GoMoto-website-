<template>
    <OwnerLayout>
                <div class="glass-card-dark shadow-glow overflow-hidden">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-6">
                            <div>
                                <h1 class="text-3xl font-bold text-white">Booking Calendar</h1>
                                <p class="text-white/70 mt-2">View all your vehicle bookings in calendar format</p>
                            </div>
                            
                            <!-- View Toggle Buttons -->
                            <div class="flex bg-white/10 rounded-lg p-1 mr-4 backdrop-blur-sm border border-white/20">
                                <Link
                                    :href="route('owner.bookings.index')"
                                    class="px-3 py-2 rounded-md text-sm font-medium transition-all hover:bg-white/20 text-white/70 hover:text-white"
                                >
                                    List View
                                </Link>
                                <Link
                                    :href="route('owner.bookings.calendar')"
                                    class="px-3 py-2 rounded-md text-sm font-medium transition-all bg-white/20 backdrop-blur-sm text-white border border-white/30"
                                >
                                    Calendar View
                                </Link>
                            </div>
                            
                            <!-- Calendar View Toggle Buttons -->
                            <div class="flex bg-white/10 rounded-lg p-1 backdrop-blur-sm border border-white/20">
                                <button
                                    @click="currentView = 'dayGridMonth'"
                                    :class="currentView === 'dayGridMonth' ? 'bg-white/20 backdrop-blur-sm text-white border border-white/30' : 'text-white/70 hover:text-white'"
                                    class="px-3 py-2 rounded-md text-sm font-medium transition-all hover:bg-white/10"
                                >
                                    Month
                                </button>
                                <button
                                    @click="currentView = 'timeGridWeek'"
                                    :class="currentView === 'timeGridWeek' ? 'bg-white/20 backdrop-blur-sm text-white border border-white/30' : 'text-white/70 hover:text-white'"
                                    class="px-3 py-2 rounded-md text-sm font-medium transition-all hover:bg-white/10"
                                >
                                    Week
                                </button>
                                <button
                                    @click="currentView = 'timeGridDay'"
                                    :class="currentView === 'timeGridDay' ? 'bg-white/20 backdrop-blur-sm text-white border border-white/30' : 'text-white/70 hover:text-white'"
                                    class="px-3 py-2 rounded-md text-sm font-medium transition-all hover:bg-white/10"
                                >
                                    Day
                                </button>
                            </div>
                        </div>

                        <!-- Vehicle Filter -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-white/90 mb-2">Filter by Vehicle</label>
                            <select v-model="selectedVehicleId" @change="filterEvents" class="w-64 bg-white/10 border-white/30 text-white rounded-md backdrop-blur-sm focus:border-blue-400 focus:ring-blue-400 placeholder-white/50">
                                <option value="" class="bg-gray-800 text-white">All Vehicles</option>
                                <option v-for="vehicle in vehicles" :key="vehicle.id" :value="vehicle.id" class="bg-gray-800 text-white">
                                    {{ vehicle.brand?.name }} {{ vehicle.type?.sub_type }} ({{ vehicle.license_plate }})
                                </option>
                            </select>
                        </div>

                        <!-- Status Legend -->
                        <div class="mb-6 flex flex-wrap gap-4 text-sm">
                            <div class="flex items-center">
                                <div class="w-4 h-4 bg-yellow-500 rounded mr-2"></div>
                                <span class="text-white/80">Pending</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-4 h-4 bg-green-500 rounded mr-2"></div>
                                <span class="text-white/80">Confirmed</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-4 h-4 bg-white/20 rounded mr-2 backdrop-blur-sm border border-white/30"></div>
                                <span class="text-white/80">Completed</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-4 h-4 bg-red-500 rounded mr-2"></div>
                                <span class="text-white/80">Cancelled</span>
                            </div>
                        </div>

                        <!-- Calendar -->
                        <FullCalendar
                            ref="fullCalendar"
                            :options="calendarOptions"
                            class="bg-white/5 backdrop-blur-sm rounded-lg border border-white/20"
                        />
                    </div>
                </div>

        <div v-if="showEventModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" @click="closeEventModal"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom glass-card-dark rounded-lg text-left overflow-hidden shadow-glow transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-white/20">
                    <div class="px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-white mb-4" id="modal-title">
                                    Booking Details
                                </h3>
                                
                                <div v-if="selectedEvent" class="space-y-4">
                                    <!-- Booking Status -->
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm font-medium text-white/70">Status:</span>
                                        <span :class="getStatusClass(selectedEvent.extendedProps.status)" 
                                              class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                                            {{ formatStatus(selectedEvent.extendedProps.status) }}
                                        </span>
                                    </div>

                                    <!-- Vehicle Info -->
                                    <div>
                                        <span class="text-sm font-medium text-white/70">Vehicle:</span>
                                        <p class="text-sm text-white">
                                            {{ selectedEvent.extendedProps.vehicle.brand }} {{ selectedEvent.extendedProps.vehicle.type }}
                                            <span class="text-white/60">({{ selectedEvent.extendedProps.vehicle.license_plate }})</span>
                                        </p>
                                    </div>

                                    <!-- Customer Info -->
                                    <div>
                                        <span class="text-sm font-medium text-white/70">Customer:</span>
                                        <p class="text-sm text-white">{{ selectedEvent.extendedProps.user.name }}</p>
                                        <p class="text-xs text-white/70">{{ selectedEvent.extendedProps.user.email }}</p>
                                        <p v-if="selectedEvent.extendedProps.user.phone" class="text-xs text-white/70">
                                            {{ selectedEvent.extendedProps.user.phone }}
                                        </p>
                                    </div>

                                    <!-- Booking Details -->
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <span class="text-sm font-medium text-white/70">Duration:</span>
                                            <p class="text-sm text-white">{{ selectedEvent.extendedProps.duration }}</p>
                                        </div>
                                        <div>
                                            <span class="text-sm font-medium text-white/70">Amount:</span>
                                            <p class="text-sm text-green-400">₱{{ selectedEvent.extendedProps.amount }}</p>
                                        </div>
                                    </div>

                                    <!-- Timing -->
                                    <div>
                                        <span class="text-sm font-medium text-white/70">Pickup Time:</span>
                                        <p class="text-sm text-white">{{ formatDateTime(selectedEvent.start) }}</p>
                                    </div>
                                    <div>
                                        <span class="text-sm font-medium text-white/70">Return Time:</span>
                                        <p class="text-sm text-white">{{ formatDateTime(selectedEvent.end) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white/5 backdrop-blur-sm px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-white/20">
                        <button 
                            @click="viewBookingDetails"
                            type="button" 
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors"
                        >
                            View Full Details
                        </button>
                        <button 
                            @click="closeEventModal"
                            type="button" 
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-white/30 shadow-sm px-4 py-2 bg-white/10 text-base font-medium text-white hover:bg-white/20 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors backdrop-blur-sm"
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
    firstDay: 1, // Start week on Monday
    slotMinTime: '06:00:00',
    slotMaxTime: '22:00:00',
    allDaySlot: true,
    slotDuration: '01:00:00',
    slotLabelInterval: '01:00:00',
    eventTimeFormat: {
        hour: '2-digit',
        minute: '2-digit',
        hour12: true
    },
    dayHeaderFormat: {
        weekday: 'short',
        day: 'numeric',
        month: 'short'
    }
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
        'completed': 'bg-white/20 text-white',
        'cancelled': 'bg-red-100 text-red-800',
    };
    return classes[status] || 'bg-white/20 text-white';
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
/* ======================= */
/* FullCalendar Dark Theme  */
/* ======================= */

/* Main calendar background */
.fc {
    background: transparent !important;
}

/* Calendar header (month/year, navigation buttons) */
.fc .fc-toolbar-title {
    color: white !important;
    font-weight: bold !important;
}

.fc .fc-button {
    color: white !important;
    background: rgba(255, 255, 255, 0.1) !important;
    border: 1px solid rgba(255, 255, 255, 0.3) !important;
    backdrop-filter: blur(4px) !important;
}

.fc .fc-button:hover {
    background: rgba(255, 255, 255, 0.2) !important;
    color: white !important;
}

.fc .fc-button:focus {
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.5) !important;
}

.fc .fc-button-primary:disabled {
    background: rgba(255, 255, 255, 0.05) !important;
    border-color: rgba(255, 255, 255, 0.2) !important;
    color: rgba(255, 255, 255, 0.5) !important;
}

/* Column headers (Sun, Mon, Tue...) */
.fc .fc-col-header-cell-cushion {
    color: white !important;
    font-weight: 600 !important;
    padding: 8px 4px !important;
}

.fc .fc-col-header-cell {
    background: rgba(255, 255, 255, 0.05) !important;
    border-color: rgba(255, 255, 255, 0.2) !important;
}

/* Day numbers in month view */
.fc .fc-daygrid-day-number {
    color: white !important;
    font-weight: 500 !important;
}

/* Time labels in timeGrid (week/day views) */
.fc .fc-timegrid-axis-cushion {
    color: rgba(255, 255, 255, 0.8) !important;
    font-size: 12px !important;
}

.fc .fc-timegrid-slot-label-cushion {
    color: rgba(255, 255, 255, 0.7) !important;
}

/* Events styling */
.fc .fc-event {
    color: white !important;
    background-color: rgba(59, 130, 246, 0.8) !important;
    border: 1px solid rgba(59, 130, 246, 1) !important;
    backdrop-filter: blur(4px) !important;
}

.fc .fc-event:hover {
    background-color: rgba(59, 130, 246, 0.9) !important;
}

/* Event time and title */
.fc .fc-event-time,
.fc .fc-event-title {
    color: white !important;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3) !important;
}

/* Day cells */
.fc .fc-daygrid-day,
.fc .fc-timegrid-col {
    background: transparent !important;
}

/* Today highlighting */
.fc .fc-day-today {
    background-color: rgba(59, 130, 246, 0.1) !important;
}

.fc .fc-day-today .fc-daygrid-day-number {
    background-color: rgba(59, 130, 246, 0.8) !important;
    color: white !important;
    border-radius: 50% !important;
    width: 24px !important;
    height: 24px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-weight: bold !important;
}

/* Grid borders */
.fc-theme-standard .fc-scrollgrid,
.fc-theme-standard td,
.fc-theme-standard th {
    border-color: rgba(255, 255, 255, 0.15) !important;
}

/* Week view specific styles */
.fc .fc-timegrid-event-harness {
    background: transparent !important;
}

.fc .fc-timegrid-event {
    border-radius: 4px !important;
    margin: 1px !important;
}

/* All-day events */
.fc .fc-timegrid-divider {
    background: rgba(255, 255, 255, 0.1) !important;
}

.fc .fc-timegrid-slot {
    border-color: rgba(255, 255, 255, 0.1) !important;
}

.fc .fc-timegrid-slot:hover {
    background-color: rgba(255, 255, 255, 0.05) !important;
}

/* Event rounded corners and padding */
.fc-event {
    border-radius: 6px !important;
    padding: 2px 6px !important;
    font-size: 12px !important;
    line-height: 1.3 !important;
    min-height: 20px !important;
}

.fc-timegrid-event {
    border-radius: 4px !important;
    padding: 2px 4px !important;
}

/* Event text weight */
.fc-event-time {
    font-weight: 600 !important;
    font-size: 11px !important;
}

.fc-event-title {
    font-weight: 500 !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    white-space: nowrap !important;
    font-size: 12px !important;
}

/* Scroll bars */
.fc-scroller::-webkit-scrollbar {
    width: 6px !important;
}

.fc-scroller::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.1) !important;
    border-radius: 3px !important;
}

.fc-scroller::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.3) !important;
    border-radius: 3px !important;
}

.fc-scroller::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.5) !important;
}

/* Mobile responsiveness */
@media (max-width: 768px) {
    .fc-toolbar {
        flex-direction: column !important;
        gap: 10px !important;
    }
    
    .fc-toolbar-chunk {
        display: flex !important;
        justify-content: center !important;
    }
    
    .fc .fc-button {
        padding: 6px 8px !important;
        font-size: 12px !important;
    }
    
    .fc .fc-toolbar-title {
        font-size: 18px !important;
    }
}

</style>
