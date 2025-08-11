<template>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <svg class="w-5 h-5 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                Overcharge Analytics
            </h3>
            
            <div class="flex space-x-2">
                <button
                    @click="timeframe = '7d'"
                    :class="timeframe === '7d' ? 'bg-purple-600 text-white' : 'bg-gray-100 text-gray-600'"
                    class="px-3 py-1 rounded-lg text-sm font-medium transition-colors"
                >
                    7 Days
                </button>
                <button
                    @click="timeframe = '30d'"
                    :class="timeframe === '30d' ? 'bg-purple-600 text-white' : 'bg-gray-100 text-gray-600'"
                    class="px-3 py-1 rounded-lg text-sm font-medium transition-colors"
                >
                    30 Days
                </button>
                <button
                    @click="timeframe = '90d'"
                    :class="timeframe === '90d' ? 'bg-purple-600 text-white' : 'bg-gray-100 text-gray-600'"
                    class="px-3 py-1 rounded-lg text-sm font-medium transition-colors"
                >
                    90 Days
                </button>
            </div>
        </div>
        
        <!-- Key Metrics -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-gradient-to-r from-green-50 to-green-100 rounded-lg p-4 border border-green-200">
                <div class="text-2xl font-bold text-green-600">₱{{ totalEarnings.toLocaleString() }}</div>
                <div class="text-sm text-green-700 font-medium">Total Earned</div>
                <div class="text-xs text-green-600 mt-1">
                    {{ overchargeRate.toFixed(1) }}% of bookings
                </div>
            </div>
            
            <div class="bg-gradient-to-r from-red-50 to-red-100 rounded-lg p-4 border border-red-200">
                <div class="text-2xl font-bold text-red-600">{{ lateReturns }}</div>
                <div class="text-sm text-red-700 font-medium">Late Returns</div>
                <div class="text-xs text-red-600 mt-1">
                    Avg {{ averageLateDuration.toFixed(1) }} hrs late
                </div>
            </div>
            
            <div class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-lg p-4 border border-blue-200">
                <div class="text-2xl font-bold text-blue-600">{{ outOfCityReturns }}</div>
                <div class="text-sm text-blue-700 font-medium">Out-of-City</div>
                <div class="text-xs text-blue-600 mt-1">
                    Avg {{ averageDistance.toFixed(1) }}km out
                </div>
            </div>
            
            <div class="bg-gradient-to-r from-purple-50 to-purple-100 rounded-lg p-4 border border-purple-200">
                <div class="text-2xl font-bold text-purple-600">{{ unpaidOvercharges }}</div>
                <div class="text-sm text-purple-700 font-medium">Pending</div>
                <div class="text-xs text-purple-600 mt-1">
                    ₱{{ unpaidAmount.toLocaleString() }} unpaid
                </div>
            </div>
        </div>
        
        <!-- Vehicle Performance -->
        <div v-if="vehicles && vehicles.length > 0" class="mb-6">
            <h4 class="font-medium text-gray-900 mb-3">Vehicle Performance</h4>
            <div class="space-y-3 max-h-64 overflow-y-auto">
                <div 
                    v-for="vehicle in vehicleStats" 
                    :key="vehicle.id"
                    class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border"
                >
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg flex items-center justify-center text-white font-bold text-sm">
                            {{ vehicle.make_name?.charAt(0) || 'V' }}{{ vehicle.model_name?.charAt(0) || '' }}
                        </div>
                        <div>
                            <div class="font-medium text-gray-900">{{ vehicle.make_name }} {{ vehicle.model_name }}</div>
                            <div class="text-sm text-gray-600">{{ vehicle.year }} • {{ vehicle.license_plate }}</div>
                        </div>
                    </div>
                    
                    <div class="text-right">
                        <div class="text-lg font-bold text-green-600">₱{{ vehicle.overcharge_earnings?.toLocaleString() || '0' }}</div>
                        <div class="text-xs text-gray-600">
                            {{ vehicle.overcharge_count || 0 }} incidents • {{ vehicle.overcharge_rate?.toFixed(1) || '0' }}% rate
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Trends and Patterns -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Common Issues -->
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                <h4 class="font-medium text-gray-900 mb-3 flex items-center">
                    <svg class="w-4 h-4 text-orange-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 15.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                    Common Issues
                </h4>
                
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Weekend late returns</span>
                        <span class="font-medium text-gray-900">{{ weekendLateReturns }}%</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Tourist season out-of-city</span>
                        <span class="font-medium text-gray-900">{{ touristSeasonOutOfCity }}%</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Repeat offenders</span>
                        <span class="font-medium text-gray-900">{{ repeatOffenders }}%</span>
                    </div>
                </div>
            </div>
            
            <!-- Recommendations -->
            <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                <h4 class="font-medium text-gray-900 mb-3 flex items-center">
                    <svg class="w-4 h-4 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                    Smart Recommendations
                </h4>
                
                <div class="space-y-3">
                    <div v-for="recommendation in getRecommendations()" :key="recommendation.id" 
                         class="flex items-start space-x-2">
                        <div class="w-2 h-2 bg-blue-500 rounded-full mt-2 flex-shrink-0"></div>
                        <div class="text-sm text-gray-700">{{ recommendation.text }}</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Action Items -->
        <div class="mt-6 bg-gradient-to-r from-amber-50 to-orange-50 rounded-lg p-4 border border-amber-200">
            <h4 class="font-medium text-gray-900 mb-3 flex items-center">
                <svg class="w-4 h-4 text-amber-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                Action Items
            </h4>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div v-if="unpaidAmount > 0" class="flex items-center justify-between p-3 bg-white rounded border">
                    <div>
                        <div class="font-medium text-gray-900">Follow up on unpaid overcharges</div>
                        <div class="text-sm text-gray-600">₱{{ unpaidAmount.toLocaleString() }} pending collection</div>
                    </div>
                    <button class="px-3 py-1 bg-green-600 text-white rounded text-sm font-medium hover:bg-green-700">
                        Review
                    </button>
                </div>
                
                <div v-if="shouldUpdateRates" class="flex items-center justify-between p-3 bg-white rounded border">
                    <div>
                        <div class="font-medium text-gray-900">Rate optimization needed</div>
                        <div class="text-sm text-gray-600">Your rates may need adjustment</div>
                    </div>
                    <button class="px-3 py-1 bg-blue-600 text-white rounded text-sm font-medium hover:bg-blue-700">
                        Adjust
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
    vehicles: {
        type: Array,
        default: () => []
    },
    overcharges: {
        type: Array,
        default: () => []
    },
    settings: {
        type: Object,
        required: true
    }
})

const timeframe = ref('30d')

// Calculate analytics based on data
const totalEarnings = computed(() => {
    return props.overcharges.reduce((sum, overcharge) => sum + parseFloat(overcharge.amount || 0), 0)
})

const lateReturns = computed(() => {
    return props.overcharges.filter(o => o.overcharge_type?.name === 'late_return').length
})

const outOfCityReturns = computed(() => {
    return props.overcharges.filter(o => o.overcharge_type?.name === 'out_of_city').length
})

const unpaidOvercharges = computed(() => {
    return props.overcharges.filter(o => !o.is_paid).length
})

const unpaidAmount = computed(() => {
    return props.overcharges
        .filter(o => !o.is_paid)
        .reduce((sum, overcharge) => sum + parseFloat(overcharge.amount || 0), 0)
})

const overchargeRate = computed(() => {
    const totalBookings = props.vehicles.reduce((sum, vehicle) => sum + (vehicle.bookings?.length || 0), 0)
    return totalBookings > 0 ? (props.overcharges.length / totalBookings) * 100 : 0
})

const averageLateDuration = computed(() => {
    const lateOvercharges = props.overcharges.filter(o => o.overcharge_type?.name === 'late_return')
    if (lateOvercharges.length === 0) return 0
    return lateOvercharges.reduce((sum, o) => sum + (o.units || 0), 0) / lateOvercharges.length
})

const averageDistance = computed(() => {
    const outOfCityOvercharges = props.overcharges.filter(o => o.overcharge_type?.name === 'out_of_city')
    if (outOfCityOvercharges.length === 0) return 0
    return outOfCityOvercharges.reduce((sum, o) => sum + (o.units || 0), 0) / outOfCityOvercharges.length
})

const vehicleStats = computed(() => {
    return props.vehicles.map(vehicle => {
        const vehicleOvercharges = props.overcharges.filter(o => o.booking?.vehicle_id === vehicle.id)
        const vehicleBookings = vehicle.bookings || []
        
        return {
            ...vehicle,
            overcharge_earnings: vehicleOvercharges.reduce((sum, o) => sum + parseFloat(o.amount || 0), 0),
            overcharge_count: vehicleOvercharges.length,
            overcharge_rate: vehicleBookings.length > 0 ? (vehicleOvercharges.length / vehicleBookings.length) * 100 : 0
        }
    })
})

// Mock data for demo purposes - in real app these would be calculated from actual data
const weekendLateReturns = computed(() => 65)
const touristSeasonOutOfCity = computed(() => 40)
const repeatOffenders = computed(() => 15)

const shouldUpdateRates = computed(() => {
    const lateRate = parseFloat(props.settings.late_return_rate || 100)
    return lateRate < 50 || lateRate > 200 // Suggest update if rates are too extreme
})

const getRecommendations = () => {
    const recommendations = []
    
    if (overchargeRate.value > 15) {
        recommendations.push({
            id: 1,
            text: "Your overcharge rate is high. Consider reviewing your policies or providing clearer instructions to customers."
        })
    }
    
    if (unpaidAmount.value > 1000) {
        recommendations.push({
            id: 2,
            text: "Consider implementing automatic payment collection or clearer payment reminders for overcharges."
        })
    }
    
    if (outOfCityReturns.value > lateReturns.value) {
        recommendations.push({
            id: 3,
            text: "Out-of-city returns are common. Consider providing a map showing city limits to customers."
        })
    }
    
    if (averageLateDuration.value > 3) {
        recommendations.push({
            id: 4,
            text: "Customers are returning significantly late. Consider sending reminder notifications before return time."
        })
    }
    
    if (recommendations.length === 0) {
        recommendations.push({
            id: 5,
            text: "Great job! Your overcharge management is working well. Keep monitoring for trends."
        })
    }
    
    return recommendations
}
</script>
