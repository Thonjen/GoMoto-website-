<template>
    <div v-if="notifications.length > 0" class="space-y-4">
        <div 
            v-for="notification in notifications" 
            :key="notification.id"
            :class="getNotificationClass(notification.type)"
            class="rounded-lg p-4 border flex items-start space-x-3"
        >
            <div class="flex-shrink-0 mt-0.5">
                <component :is="getNotificationIcon(notification.type)" class="w-5 h-5" />
            </div>
            
            <div class="flex-1 min-w-0">
                <h4 class="text-sm font-medium" :class="getTextColor(notification.type)">
                    {{ notification.title }}
                </h4>
                <p class="text-sm mt-1" :class="getSubTextColor(notification.type)">
                    {{ notification.message }}
                </p>
                
                <!-- Action buttons if available -->
                <div v-if="notification.actions" class="mt-3 flex space-x-2">
                    <button
                        v-for="action in notification.actions"
                        :key="action.label"
                        @click="action.handler(notification)"
                        :class="getActionButtonClass(notification.type)"
                        class="px-3 py-1 rounded text-xs font-medium transition-colors"
                    >
                        {{ action.label }}
                    </button>
                </div>
            </div>
            
            <div class="flex-shrink-0">
                <button 
                    @click="dismissNotification(notification.id)"
                    class="text-gray-400 hover:text-gray-600"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    settings: {
        type: Object,
        required: true
    },
    vehicles: {
        type: Array,
        default: () => []
    },
    overcharges: {
        type: Array,
        default: () => []
    },
    recentBookings: {
        type: Array,
        default: () => []
    }
})

const emit = defineEmits(['dismiss', 'action'])

// Generate smart notifications based on data analysis
const notifications = computed(() => {
    const notifications = []
    let notificationId = 1

    // Check for high overcharge rates
    const totalBookings = props.vehicles.reduce((sum, v) => sum + (v.bookings?.length || 0), 0)
    const overchargeRate = totalBookings > 0 ? (props.overcharges.length / totalBookings) * 100 : 0
    
    if (overchargeRate > 20) {
        notifications.push({
            id: notificationId++,
            type: 'warning',
            title: 'High Overcharge Rate Detected',
            message: `${overchargeRate.toFixed(1)}% of your bookings have overcharges. Consider reviewing your policies or providing clearer instructions.`,
            actions: [
                { label: 'Review Settings', handler: () => emit('action', 'review-settings') },
                { label: 'View Analytics', handler: () => emit('action', 'view-analytics') }
            ]
        })
    }

    // Check for extreme rates
    const lateRate = parseFloat(props.settings.late_return_rate || 0)
    if (lateRate > 200) {
        notifications.push({
            id: notificationId++,
            type: 'error',
            title: 'Very High Late Return Rate',
            message: `Your ₱${lateRate}/hour rate is significantly above market average. This might deter customers.`,
            actions: [
                { label: 'Adjust Rate', handler: () => emit('action', 'adjust-rate') }
            ]
        })
    } else if (lateRate < 50) {
        notifications.push({
            id: notificationId++,
            type: 'info',
            title: 'Low Late Return Rate',
            message: `Your ₱${lateRate}/hour rate is below market average. Consider increasing it to better protect your business.`,
            actions: [
                { label: 'Review Market Rates', handler: () => emit('action', 'market-rates') }
            ]
        })
    }

    // Check for unpaid overcharges
    const unpaidOvercharges = props.overcharges.filter(o => !o.is_paid)
    if (unpaidOvercharges.length > 0) {
        const totalUnpaid = unpaidOvercharges.reduce((sum, o) => sum + parseFloat(o.amount || 0), 0)
        notifications.push({
            id: notificationId++,
            type: 'warning',
            title: 'Unpaid Overcharges',
            message: `You have ₱${totalUnpaid.toLocaleString()} in unpaid overcharges from ${unpaidOvercharges.length} incidents.`,
            actions: [
                { label: 'Follow Up', handler: () => emit('action', 'follow-up-payments') },
                { label: 'View Details', handler: () => emit('action', 'view-unpaid') }
            ]
        })
    }

    // Check for no grace period
    if (parseInt(props.settings.grace_period_minutes || 0) === 0) {
        notifications.push({
            id: notificationId++,
            type: 'suggestion',
            title: 'Consider Adding Grace Period',
            message: 'A 15-30 minute grace period can improve customer satisfaction while still protecting your business.',
            actions: [
                { label: 'Add Grace Period', handler: () => emit('action', 'add-grace-period') }
            ]
        })
    }

    // Success notification for good setup
    if (notifications.length === 0 && props.settings.enable_overcharges) {
        notifications.push({
            id: notificationId++,
            type: 'success',
            title: 'Optimal Overcharge Setup',
            message: 'Your overcharge settings appear to be well-balanced. Keep monitoring for patterns and adjustments.',
            actions: [
                { label: 'View Statistics', handler: () => emit('action', 'view-stats') }
            ]
        })
    }

    return notifications
})

const getNotificationClass = (type) => {
    const classes = {
        success: 'bg-green-50 border-green-200',
        warning: 'bg-yellow-50 border-yellow-200', 
        error: 'bg-red-50 border-red-200',
        info: 'bg-blue-50 border-blue-200',
        suggestion: 'bg-purple-50 border-purple-200'
    }
    return classes[type] || classes.info
}

const getTextColor = (type) => {
    const colors = {
        success: 'text-green-800',
        warning: 'text-yellow-800',
        error: 'text-red-800', 
        info: 'text-blue-800',
        suggestion: 'text-purple-800'
    }
    return colors[type] || colors.info
}

const getSubTextColor = (type) => {
    const colors = {
        success: 'text-green-600',
        warning: 'text-yellow-600',
        error: 'text-red-600',
        info: 'text-blue-600', 
        suggestion: 'text-purple-600'
    }
    return colors[type] || colors.info
}

const getActionButtonClass = (type) => {
    const classes = {
        success: 'bg-green-100 text-green-800 hover:bg-green-200',
        warning: 'bg-yellow-100 text-yellow-800 hover:bg-yellow-200',
        error: 'bg-red-100 text-red-800 hover:bg-red-200',
        info: 'bg-blue-100 text-blue-800 hover:bg-blue-200',
        suggestion: 'bg-purple-100 text-purple-800 hover:bg-purple-200'
    }
    return classes[type] || classes.info
}

const getNotificationIcon = (type) => {
    const icons = {
        success: 'svg',
        warning: 'svg', 
        error: 'svg',
        info: 'svg',
        suggestion: 'svg'
    }
    
    // Return SVG component based on type
    return 'svg' // Simplified for now
}

const dismissNotification = (id) => {
    emit('dismiss', id)
}
</script>
