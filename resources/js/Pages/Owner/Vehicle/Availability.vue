<template>
    <AuthenticatedLayout>
        <Head :title="`Availability Calendar - ${vehicle.make.name} ${vehicle.model.name}`" />
        
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="glass-card-dark overflow-hidden shadow-glow sm:rounded-lg mb-6">
                    <div class="p-6 border-b border-white/20">
                        <div class="flex items-center justify-between">
                            <div>
                                <h1 class="text-3xl font-bold text-white">
                                    Availability Calendar
                                </h1>
                                <p class="text-white/80 mt-1">
                                    Manage availability for {{ vehicle.make.name }} {{ vehicle.model.name }}
                                </p>
                            </div>
                            <Link 
                                :href="route('owner.vehicles.show', vehicle.id)"
                                class="inline-flex items-center px-4 py-2 bg-white/20 text-white rounded-md hover:bg-white/30 transition-all duration-200 backdrop-blur-sm border border-white/30"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Back to Vehicle
                            </Link>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                    <!-- Calendar Section -->
                    <div class="lg:col-span-3">
                        <div class="glass-card-dark overflow-hidden shadow-glow sm:rounded-lg">
                            <div class="p-6">
                                <div class="flex items-center justify-between mb-6">
                                    <h2 class="text-xl font-semibold text-white">Calendar View</h2>
                                    <div class="flex space-x-2">
                                        <button
                                            @click="previousMonth"
                                            class="p-2 bg-white/20 rounded-md hover:bg-white/30 transition-all duration-200 backdrop-blur-sm border border-white/30"
                                        >
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                            </svg>
                                        </button>
                                        <div class="flex items-center px-4 py-2 bg-white/10 rounded-md backdrop-blur-sm border border-white/20">
                                            <span class="font-medium text-white">{{ currentMonthYear }}</span>
                                        </div>
                                        <button
                                            @click="nextMonth"
                                            class="p-2 bg-white/20 rounded-md hover:bg-white/30 transition-all duration-200 backdrop-blur-sm border border-white/30"
                                        >
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <!-- Calendar Grid -->
                                <div class="grid grid-cols-7 gap-1 mb-4">
                                    <!-- Day Headers -->
                                    <div 
                                        v-for="day in ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']"
                                        :key="day"
                                        class="p-2 text-center text-sm font-medium text-white/70 border-b border-white/20"
                                    >
                                        {{ day }}
                                    </div>
                                    
                                    <!-- Calendar Days -->
                                    <div
                                        v-for="day in calendarDays"
                                        :key="`${day.date}-${day.month}`"
                                        :class="[
                                            'relative p-2 h-24 border border-white/20 cursor-pointer hover:bg-white/10 transition-colors backdrop-blur-sm',
                                            {
                                                'text-white/50': !day.isCurrentMonth,
                                                'bg-blue-400/20 border-blue-400/50 shadow-glow': day.isToday,
                                                'bg-red-400/20 border-red-400/50': day.isBlocked,
                                                'bg-yellow-400/20 border-yellow-400/50': day.hasBookings,
                                                'bg-white/5': day.isPast
                                            }
                                        ]"
                                        @click="selectDate(day)"
                                    >
                                        <div :class="[
                                            'text-sm font-medium text-white',
                                            {
                                                'text-blue-400 font-bold': day.isToday
                                            }
                                        ]">
                                            {{ day.day }}
                                        </div>
                                        
                                        <!-- Today indicator -->
                                        <div v-if="day.isToday" class="absolute top-1 left-1">
                                            <div class="w-2 h-2 bg-blue-400 rounded-full shadow-glow"></div>
                                        </div>
                                        
                                        <!-- Blocked indicator -->
                                        <div v-if="day.isBlocked" class="absolute top-1 right-1">
                                            <div class="w-2 h-2 bg-red-500 rounded-full"></div>
                                        </div>
                                        
                                        <!-- Booking indicator -->
                                        <div v-if="day.hasBookings" class="absolute bottom-1 left-1">
                                            <div class="w-2 h-2 bg-yellow-500 rounded-full"></div>
                                        </div>
                                        
                                        <!-- Selection indicator -->
                                        <div v-if="day.isSelected" class="absolute inset-0 border-2 border-blue-500 rounded pointer-events-none"></div>
                                    </div>
                                </div>

                                <!-- Legend -->
                                <div class="flex flex-wrap gap-4 text-sm">
                                    <div class="flex items-center">
                                        <div class="w-3 h-3 bg-red-100 border border-red-300 rounded mr-2"></div>
                                        <span>Blocked</span>
                                    </div>
                                    <div class="flex items-center">
                                        <div class="w-3 h-3 bg-yellow-100 border border-yellow-300 rounded mr-2"></div>
                                        <span>Booked</span>
                                    </div>
                                    <div class="flex items-center">
                                        <div class="w-3 h-3 bg-blue-100 border border-blue-400 rounded mr-2 relative">
                                            <div class="absolute top-0 left-0 w-1 h-1 bg-blue-600 rounded-full"></div>
                                        </div>
                                        <span class="font-medium">Today</span>
                                    </div>
                                    <div class="flex items-center">
                                        <div class="w-3 h-3 bg-white/20 border border-white/30 rounded mr-2 backdrop-blur-sm"></div>
                                        <span class="text-white/80">Past Date</span>
                                    </div>
                                    <div class="flex items-center">
                                        <div class="w-3 h-3 bg-purple-400/20 border border-purple-400/30 rounded mr-2 backdrop-blur-sm"></div>
                                        <span class="text-white/80">Recurring Block</span>
                                    </div>
                                    <div class="flex items-center">
                                        <div class="w-3 h-3 bg-orange-400/20 border border-orange-400/30 rounded mr-2 backdrop-blur-sm"></div>
                                        <span class="text-white/80">Time-based Block</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions Panel -->
                    <div class="space-y-6">
                        <!-- Quick Actions -->
                        <div class="glass-card-dark overflow-hidden shadow-glow sm:rounded-lg border border-white/20">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-white mb-4">Quick Actions</h3>
                                
                                <div class="space-y-3">
                                    <button
                                        @click="showBlockModal = true"
                                        class="w-full flex items-center justify-center px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors"
                                    >
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                        </svg>
                                        Block Selected Dates
                                    </button>
                                    
                                    <button
                                        @click="showBulkBlockModal = true"
                                        class="w-full flex items-center justify-center px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700 transition-colors"
                                    >
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        Block Date Range
                                    </button>
                                    
                                    <button
                                        @click="showRecurringDaysModal = true"
                                        class="w-full flex items-center justify-center px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 transition-colors"
                                    >
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                        </svg>
                                        Recurring Days Block
                                    </button>
                                    
                                    <button
                                        v-if="selectedDates.length > 0"
                                        @click="clearSelection"
                                        class="w-full flex items-center justify-center px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors"
                                    >
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        Clear Selection
                                    </button>
                                </div>
                                
                                <div v-if="selectedDates.length > 0" class="mt-4 p-3 bg-blue-50 rounded-md">
                                    <p class="text-sm text-blue-700">
                                        {{ selectedDates.length }} date(s) selected
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Current Blocks -->
                        <div class="glass-card-dark overflow-hidden shadow-glow sm:rounded-lg border border-white/20">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-white mb-4">Active Blocks</h3>
                                
                                <div v-if="blocks.length === 0" class="text-white/70 text-sm">
                                    No availability blocks set.
                                </div>
                                
                                <div v-else class="space-y-3 max-h-64 overflow-y-auto">
                                    <div
                                        v-for="block in blocks"
                                        :key="block.id"
                                        class="p-3 border border-white/20 rounded-md bg-white/5 backdrop-blur-sm"
                                    >
                                        <div class="flex items-start justify-between">
                                            <div class="flex-1">
                                                <div class="flex items-center gap-2 mb-1">
                                                    <p class="text-sm font-medium text-white">
                                                        {{ formatDate(block.blocked_date) }}
                                                    </p>
                                                    <span v-if="block.is_recurring" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-400/20 text-purple-400">
                                                        Recurring
                                                    </span>
                                                    <span v-if="block.is_time_based" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-400/20 text-blue-400">
                                                        Time-based
                                                    </span>
                                                </div>
                                                
                                                <p class="text-xs text-white/70 capitalize">
                                                    {{ block.block_type.replace('_', ' ') }}
                                                </p>
                                                
                                                <!-- Show time information for time-based blocks -->
                                                <div v-if="block.is_time_based && !block.affects_whole_day && block.start_time" class="text-xs text-blue-400 mt-1">
                                                    🕐 {{ block.start_time }} - {{ block.end_time }}
                                                </div>
                                                
                                                <!-- Show recurring pattern -->
                                                <div v-if="block.is_recurring" class="text-xs text-purple-400 mt-1">
                                                    <span v-if="block.recurring_type === 'custom_days' && block.recurring_days">
                                                        📅 Every {{ formatRecurringDays(block.recurring_days) }}
                                                    </span>
                                                    <span v-else-if="block.recurring_type">
                                                        📅 {{ recurringTypes[block.recurring_type] }}
                                                    </span>
                                                    <span v-if="block.recurring_end_date"> until {{ formatDate(block.recurring_end_date) }}</span>
                                                </div>
                                                
                                                <p v-if="block.reason" class="text-xs text-white/70 mt-1">
                                                    {{ block.reason }}
                                                </p>
                                            </div>
                                            <button
                                                @click="deleteBlock(block)"
                                                class="text-red-600 hover:text-red-800 transition-colors"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Block Dates Modal -->
        <Modal :show="showBlockModal" @close="showBlockModal = false">
            <div class="p-6">
                <h3 class="text-lg font-medium text-white mb-4">Block Selected Dates</h3>
                
                <form @submit.prevent="blockSelectedDates">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-white">Block Type</label>
                            <select
                                v-model="blockForm.block_type"
                                class="mt-1 block w-full border-white/20 rounded-md shadow-sm focus:border-blue-400 focus:ring-blue-400 bg-white/10 text-white backdrop-blur-sm"
                                required
                            >
                                <option value="" class="bg-gray-800 text-white">Select type...</option>
                                <option v-for="(label, value) in blockTypes" :key="value" :value="value" class="bg-gray-800 text-white">
                                    {{ label }}
                                </option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-white">Reason (Optional)</label>
                            <input
                                v-model="blockForm.reason"
                                type="text"
                                class="mt-1 block w-full border-white/20 rounded-md shadow-sm focus:border-blue-400 focus:ring-blue-400 bg-white/10 text-white backdrop-blur-sm placeholder-white/50"
                                placeholder="Brief reason for blocking..."
                            />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-white">Notes (Optional)</label>
                            <textarea
                                v-model="blockForm.notes"
                                rows="3"
                                class="mt-1 block w-full border-white/20 rounded-md shadow-sm focus:border-blue-400 focus:ring-blue-400 bg-white/10 text-white backdrop-blur-sm placeholder-white/50"
                                placeholder="Additional notes..."
                            ></textarea>
                        </div>

                        <div class="flex items-center">
                            <input
                                v-model="blockForm.is_recurring"
                                type="checkbox"
                                class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                            />
                            <label class="ml-2 block text-sm text-gray-700">
                                Make this a recurring block
                            </label>
                        </div>

                        <div v-if="blockForm.is_recurring" class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Recurring Type</label>
                                <select
                                    v-model="blockForm.recurring_type"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                >
                                    <option value="">Select frequency...</option>
                                    <option v-for="(label, value) in recurringTypes" :key="value" :value="value">
                                        {{ label }}
                                    </option>
                                </select>
                            </div>

                            <div v-if="blockForm.recurring_type === 'custom_days'" class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Select Days</label>
                                <div class="grid grid-cols-2 gap-2">
                                    <label 
                                        v-for="(dayName, dayValue) in daysOfWeek" 
                                        :key="dayValue" 
                                        class="flex items-center"
                                    >
                                        <input
                                            v-model="blockForm.recurring_days"
                                            :value="parseInt(dayValue)"
                                            type="checkbox"
                                            class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                        />
                                        <span class="ml-2 text-sm">{{ dayName }}</span>
                                    </label>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">End Date (Optional)</label>
                                <input
                                    v-model="blockForm.recurring_end_date"
                                    type="date"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                />
                            </div>
                        </div>

                        <!-- Time-based blocking options -->
                        <div class="border-t pt-4">
                            <div class="flex items-center mb-3">
                                <input
                                    v-model="blockForm.is_time_based"
                                    type="checkbox"
                                    class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                />
                                <label class="ml-2 block text-sm text-gray-700">
                                    Time-based restriction (block specific hours only)
                                </label>
                            </div>

                            <div v-if="blockForm.is_time_based" class="space-y-3">
                                <div class="flex items-center">
                                    <input
                                        v-model="blockForm.affects_whole_day"
                                        type="checkbox"
                                        class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                    />
                                    <label class="ml-2 block text-sm text-gray-700">
                                        Block entire day (ignores time settings)
                                    </label>
                                </div>

                                <div v-if="!blockForm.affects_whole_day" class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Start Time</label>
                                        <input
                                            v-model="blockForm.start_time"
                                            type="time"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                            required
                                        />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">End Time</label>
                                        <input
                                            v-model="blockForm.end_time"
                                            type="time"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                            required
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="p-3 bg-gray-50 rounded-md">
                            <p class="text-sm text-gray-600">
                                Selected dates: {{ selectedDates.join(', ') }}
                            </p>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 mt-6">
                        <button
                            type="button"
                            @click="showBlockModal = false"
                            class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors"
                        >
                            Block Dates
                        </button>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- Bulk Block Modal -->
        <Modal :show="showBulkBlockModal" @close="showBulkBlockModal = false">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Block Date Range</h3>
                
                <form @submit.prevent="bulkBlockDates">
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Start Date</label>
                                <input
                                    v-model="bulkBlockForm.start_date"
                                    type="date"
                                    :min="today"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    required
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">End Date</label>
                                <input
                                    v-model="bulkBlockForm.end_date"
                                    type="date"
                                    :min="bulkBlockForm.start_date || today"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    required
                                />
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Block Type</label>
                            <select
                                v-model="bulkBlockForm.block_type"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                required
                            >
                                <option value="">Select type...</option>
                                <option v-for="(label, value) in blockTypes" :key="value" :value="value">
                                    {{ label }}
                                </option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Reason (Optional)</label>
                            <input
                                v-model="bulkBlockForm.reason"
                                type="text"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="Brief reason for blocking..."
                            />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Notes (Optional)</label>
                            <textarea
                                v-model="bulkBlockForm.notes"
                                rows="3"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="Additional notes..."
                            ></textarea>
                        </div>

                        <!-- Time-based blocking options -->
                        <div class="border-t pt-4">
                            <div class="flex items-center mb-3">
                                <input
                                    v-model="bulkBlockForm.is_time_based"
                                    type="checkbox"
                                    class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                />
                                <label class="ml-2 block text-sm text-gray-700">
                                    Time-based restriction (block specific hours only)
                                </label>
                            </div>

                            <div v-if="bulkBlockForm.is_time_based" class="space-y-3">
                                <div class="flex items-center">
                                    <input
                                        v-model="bulkBlockForm.affects_whole_day"
                                        type="checkbox"
                                        class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                    />
                                    <label class="ml-2 block text-sm text-gray-700">
                                        Block entire day (ignores time settings)
                                    </label>
                                </div>

                                <div v-if="!bulkBlockForm.affects_whole_day" class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Start Time</label>
                                        <input
                                            v-model="bulkBlockForm.start_time"
                                            type="time"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                            required
                                        />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">End Time</label>
                                        <input
                                            v-model="bulkBlockForm.end_time"
                                            type="time"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                            required
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 mt-6">
                        <button
                            type="button"
                            @click="showBulkBlockModal = false"
                            class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors"
                        >
                            Block Date Range
                        </button>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- Recurring Days Modal -->
        <Modal :show="showRecurringDaysModal" @close="showRecurringDaysModal = false">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Block Recurring Days</h3>
                <p class="text-sm text-gray-600 mb-4">
                    Set up blocks that repeat on specific days of the week (e.g., every Tuesday and Friday for maintenance)
                </p>
                
                <form @submit.prevent="storeRecurringDays">
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Start Date</label>
                                <input
                                    v-model="recurringDaysForm.start_date"
                                    type="date"
                                    :min="today"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    required
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">End Date (Optional)</label>
                                <input
                                    v-model="recurringDaysForm.end_date"
                                    type="date"
                                    :min="recurringDaysForm.start_date || today"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                />
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Select Days to Block</label>
                            <div class="grid grid-cols-2 gap-2">
                                <label 
                                    v-for="(dayName, dayValue) in daysOfWeek" 
                                    :key="dayValue" 
                                    class="flex items-center"
                                >
                                    <input
                                        v-model="recurringDaysForm.recurring_days"
                                        :value="parseInt(dayValue)"
                                        type="checkbox"
                                        class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                    />
                                    <span class="ml-2 text-sm">{{ dayName }}</span>
                                </label>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">
                                Example: Select "Tuesday" and "Friday" for maintenance every Tuesday and Friday
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Block Type</label>
                            <select
                                v-model="recurringDaysForm.block_type"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                required
                            >
                                <option value="">Select type...</option>
                                <option v-for="(label, value) in blockTypes" :key="value" :value="value">
                                    {{ label }}
                                </option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Reason (Optional)</label>
                            <input
                                v-model="recurringDaysForm.reason"
                                type="text"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="Brief reason for blocking..."
                            />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Notes (Optional)</label>
                            <textarea
                                v-model="recurringDaysForm.notes"
                                rows="3"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="Additional notes..."
                            ></textarea>
                        </div>

                        <!-- Time-based blocking options -->
                        <div class="border-t pt-4">
                            <div class="flex items-center mb-3">
                                <input
                                    v-model="recurringDaysForm.is_time_based"
                                    type="checkbox"
                                    class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                />
                                <label class="ml-2 block text-sm text-gray-700">
                                    Time-based restriction (block specific hours only)
                                </label>
                            </div>

                            <div v-if="recurringDaysForm.is_time_based" class="space-y-3">
                                <div class="flex items-center">
                                    <input
                                        v-model="recurringDaysForm.affects_whole_day"
                                        type="checkbox"
                                        class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                    />
                                    <label class="ml-2 block text-sm text-gray-700">
                                        Block entire day (ignores time settings)
                                    </label>
                                </div>

                                <div v-if="!recurringDaysForm.affects_whole_day" class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Start Time</label>
                                        <input
                                            v-model="recurringDaysForm.start_time"
                                            type="time"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                            required
                                        />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">End Time</label>
                                        <input
                                            v-model="recurringDaysForm.end_time"
                                            type="time"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                            required
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 mt-6">
                        <button
                            type="button"
                            @click="showRecurringDaysModal = false"
                            class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 transition-colors"
                        >
                            Create Recurring Block
                        </button>
                    </div>
                </form>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Modal from '@/Components/Modal.vue'

const props = defineProps({
    vehicle: Object,
    blocks: Array,
    bookings: Array,
    blockTypes: Object,
    recurringTypes: Object,
    daysOfWeek: Object,
})

// Reactive state
const currentDate = ref(new Date())
const selectedDates = ref([])
const showBlockModal = ref(false)
const showBulkBlockModal = ref(false)
const showRecurringDaysModal = ref(false)

// Initialize calendar to current month
onMounted(() => {
    // Ensure currentDate is set to the first day of current month for proper display
    const now = new Date()
    currentDate.value = new Date(now.getFullYear(), now.getMonth(), 1)
})

// Form data
const blockForm = ref({
    block_type: '',
    reason: '',
    notes: '',
    is_recurring: false,
    recurring_type: '',
    recurring_days: [],
    recurring_end_date: '',
    is_time_based: false,
    affects_whole_day: true,
    start_time: '',
    end_time: '',
})

const bulkBlockForm = ref({
    start_date: '',
    end_date: '',
    block_type: '',
    reason: '',
    notes: '',
    is_time_based: false,
    affects_whole_day: true,
    start_time: '',
    end_time: '',
})

const recurringDaysForm = ref({
    start_date: '',
    end_date: '',
    recurring_days: [],
    block_type: '',
    reason: '',
    notes: '',
    is_time_based: false,
    affects_whole_day: true,
    start_time: '',
    end_time: '',
})

// Computed properties
const today = computed(() => {
    return new Date().toISOString().split('T')[0]
})

const currentMonthYear = computed(() => {
    return currentDate.value.toLocaleDateString('en-US', { 
        month: 'long', 
        year: 'numeric' 
    })
})

const calendarDays = computed(() => {
    const year = currentDate.value.getFullYear()
    const month = currentDate.value.getMonth()
    
    // Get first day of month and last day of month
    const firstDay = new Date(year, month, 1)
    const lastDay = new Date(year, month + 1, 0)
    
    // Get the first Sunday of the calendar view
    const startDate = new Date(firstDay)
    startDate.setDate(firstDay.getDate() - firstDay.getDay())
    
    const days = []
    const currentLoop = new Date(startDate)
    
    // Get today's date string for comparison
    const todayStr = new Date().toISOString().split('T')[0]
    const todayDate = new Date()
    todayDate.setHours(0, 0, 0, 0)
    
    // Generate 42 days (6 weeks) for calendar grid
    for (let i = 0; i < 42; i++) {
        const dateStr = currentLoop.toISOString().split('T')[0]
        const loopDate = new Date(currentLoop)
        loopDate.setHours(0, 0, 0, 0)
        
        days.push({
            date: dateStr,
            day: currentLoop.getDate(),
            month: currentLoop.getMonth(),
            year: currentLoop.getFullYear(),
            isCurrentMonth: currentLoop.getMonth() === month,
            isToday: dateStr === todayStr,
            isPast: loopDate < todayDate,
            isSelected: selectedDates.value.includes(dateStr),
            isBlocked: isDateBlocked(dateStr),
            hasBookings: hasBookingsOnDate(dateStr),
        })
        
        currentLoop.setDate(currentLoop.getDate() + 1)
    }
    
    return days
})

// Methods
const isDateBlocked = (date) => {
    return props.blocks.some(block => {
        if (block.blocked_date === date) return true
        
        if (block.is_recurring) {
            // Simple recurring logic - would need to be expanded for complex patterns
            const blockDate = new Date(block.blocked_date)
            const checkDate = new Date(date)
            
            if (block.recurring_type === 'weekly') {
                return blockDate.getDay() === checkDate.getDay() && checkDate >= blockDate
            }
            // Add more recurring logic as needed
        }
        
        return false
    })
}

const hasBookingsOnDate = (date) => {
    return props.bookings.some(booking => {
        const pickupDate = new Date(booking.pickup_datetime).toISOString().split('T')[0]
        // This is simplified - would need proper end date calculation
        return pickupDate === date
    })
}

const selectDate = (day) => {
    if (day.isPast || day.isBlocked || day.hasBookings) return
    
    const dateStr = day.date
    const index = selectedDates.value.indexOf(dateStr)
    
    if (index > -1) {
        selectedDates.value.splice(index, 1)
    } else {
        selectedDates.value.push(dateStr)
    }
}

const clearSelection = () => {
    selectedDates.value = []
}

const previousMonth = () => {
    currentDate.value = new Date(currentDate.value.getFullYear(), currentDate.value.getMonth() - 1, 1)
}

const nextMonth = () => {
    currentDate.value = new Date(currentDate.value.getFullYear(), currentDate.value.getMonth() + 1, 1)
}

const blockSelectedDates = () => {
    if (selectedDates.value.length === 0) return
    
    router.post(route('owner.vehicles.availability.store', props.vehicle.id), {
        dates: selectedDates.value,
        ...blockForm.value
    }, {
        onSuccess: () => {
            showBlockModal.value = false
            selectedDates.value = []
            resetBlockForm()
        }
    })
}

const bulkBlockDates = () => {
    router.post(route('owner.vehicles.availability.bulk', props.vehicle.id), bulkBlockForm.value, {
        onSuccess: () => {
            showBulkBlockModal.value = false
            resetBulkBlockForm()
        }
    })
}

const storeRecurringDays = () => {
    if (recurringDaysForm.value.recurring_days.length === 0) {
        alert('Please select at least one day of the week.')
        return
    }
    
    router.post(route('owner.vehicles.availability.recurring-days', props.vehicle.id), recurringDaysForm.value, {
        onSuccess: () => {
            showRecurringDaysModal.value = false
            resetRecurringDaysForm()
        }
    })
}

const deleteBlock = (block) => {
    if (confirm('Are you sure you want to remove this availability block?')) {
        router.delete(route('owner.vehicles.availability.destroy', [props.vehicle.id, block.id]))
    }
}

const resetBlockForm = () => {
    blockForm.value = {
        block_type: '',
        reason: '',
        notes: '',
        is_recurring: false,
        recurring_type: '',
        recurring_days: [],
        recurring_end_date: '',
        is_time_based: false,
        affects_whole_day: true,
        start_time: '',
        end_time: '',
    }
}

const resetBulkBlockForm = () => {
    bulkBlockForm.value = {
        start_date: '',
        end_date: '',
        block_type: '',
        reason: '',
        notes: '',
        is_time_based: false,
        affects_whole_day: true,
        start_time: '',
        end_time: '',
    }
}

const resetRecurringDaysForm = () => {
    recurringDaysForm.value = {
        start_date: '',
        end_date: '',
        recurring_days: [],
        block_type: '',
        reason: '',
        notes: '',
        is_time_based: false,
        affects_whole_day: true,
        start_time: '',
        end_time: '',
    }
}

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric'
    })
}

const formatRecurringDays = (dayNumbers) => {
    if (!dayNumbers || !Array.isArray(dayNumbers)) return ''
    
    const dayNames = dayNumbers.map(dayNum => {
        const dayName = props.daysOfWeek[dayNum]
        return dayName ? dayName.substring(0, 3) : '' // Short form like "Mon", "Tue"
    }).filter(Boolean)
    
    return dayNames.join(', ')
}
</script>
