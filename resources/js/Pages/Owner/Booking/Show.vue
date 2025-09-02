<template>
    <AuthenticatedLayout>

        <div class="py-12">
            <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
                <div class="glass-card-dark overflow-hidden shadow-glow sm:rounded-lg">
                    <div class="p-6 text-white">
                        <div class="mb-6">
                            <button
                                @click="goBack"
                                class="inline-flex items-center text-blue-600 hover:text-blue-800 font-semibold transition-colors"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5 mr-2"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M15 19l-7-7 7-7"
                                    />
                                </svg>
                                Back to Booking Requests
                            </button>
                        </div>

                        <div class="mb-6">
                            <h1 class="text-3xl font-bold text-white">
                                Booking #{{ booking.id }}
                            </h1>
                            <div class="flex items-center space-x-4 mt-2">
                                <span
                                    :class="getStatusClass(booking.status)"
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
                                >
                                    {{ formatStatus(booking.status) }}
                                </span>
                                <span class="text-sm text-white/70">
                                    Requested
                                    {{ formatDate(booking.created_at) }}
                                </span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                            <!-- Main Content -->
                            <div class="lg:col-span-2 space-y-6">
                                <!-- Customer Information Component -->
                                <CustomerInformation :booking="booking" />

                                <!-- Driver's License Verification Component -->
                                <DriversLicenseVerification 
                                    :booking="booking" 
                                    @open-license-modal="openLicenseModal"
                                />

                                <!-- Vehicle Information Component -->
                                <VehicleInformation :booking="booking" />

                                <!-- Booking Details Component -->
                                <BookingDetails 
                                    :booking="booking" 
                                    :estimated-return="estimatedReturn" 
                                />

                                <!-- Live Overcharge Tracking Component -->
                                <LiveOverchargeTracking 
                                    :booking="booking"
                                    :expected-return-time="expectedReturnTime"
                                    :estimated-return="estimatedReturn"
                                    :potential-overcharges="potentialOvercharges"
                                    @refresh-overcharge-status="refreshOverchargeStatus"
                                    @contact-renter="contactRenter"
                                />

                                <!-- Final Payment Breakdown Component -->
                                <FinalPaymentBreakdown 
                                    :booking="booking"
                                    :expected-return-time="expectedReturnTime"
                                    :estimated-return="estimatedReturn"
                                    :processing="processing"
                                    @recalculate-overcharges="recalculateOvercharges"
                                    @toggle-manual-overcharge-form="showManualOverchargeForm = !showManualOverchargeForm"
                                    @mark-overcharge-as-paid="markOverchargeAsPaid"
                                />

                                <!-- Manual Overcharge Form Component -->
                                <ManualOverchargeForm 
                                    v-if="showManualOverchargeForm"
                                    :show-manual-overcharge-form="showManualOverchargeForm"
                                    :manual-overcharge="manualOvercharge"
                                    :processing="processing"
                                    @add-manual-overcharge="addManualOvercharge"
                                    @close="showManualOverchargeForm = false"
                                />

                                <!-- Payment Proof Component -->
                                <PaymentProof 
                                    :booking="booking"
                                    @open-receipt-modal="openReceiptModal"
                                />
                            </div>

                            <!-- Actions Sidebar -->
                            <div class="lg:col-span-1">
                                <ActionsSidebar 
                                    :booking="booking"
                                    :processing="processing"
                                    @confirm-payment="confirmPayment"
                                    @confirm-booking="confirmBooking"
                                    @reject-booking="rejectBooking"
                                    @complete-booking="completeBooking"
                                    @go-to-vehicle="goToVehicle"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Driver's License Modal -->
        <div
            v-if="showLicenseModal"
            @click.self="closeLicenseModal"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
        >
            <div
                class="bg-gray-800 border border-gray-600/30 rounded-lg max-w-4xl w-full max-h-[90vh] overflow-auto"
            >
                <div class="p-4 border-b border-gray-600/30 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-white">
                        Driver's License - {{ licenseModalData.side === 'front' ? 'Front Side' : 'Back Side' }}
                    </h3>
                    <button
                        @click="closeLicenseModal"
                        class="text-gray-400 hover:text-gray-200 transition-colors"
                    >
                        <svg
                            class="w-6 h-6"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"
                            ></path>
                        </svg>
                    </button>
                </div>
                <div class="p-4 text-center">
                    <img
                        :src="licenseModalData.imageUrl"
                        alt="Driver's License"
                        class="max-w-full h-auto mx-auto rounded border"
                    />
                </div>
            </div>
        </div>

        <!-- Payment Receipt Modal -->
        <div
            v-if="showReceiptModal"
            @click.self="closeReceiptModal"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
        >
            <div class="bg-gray-800 border border-gray-600/30 rounded-lg max-w-4xl w-full max-h-[90vh] overflow-auto">
                <div class="p-4 border-b border-gray-600/30 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-white">Payment Receipt</h3>
                    <button
                        @click="closeReceiptModal"
                        class="text-gray-400 hover:text-gray-200 transition-colors"
                    >
                        <svg
                            class="w-6 h-6"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"
                            ></path>
                        </svg>
                    </button>
                </div>
                <div class="p-4 text-center">
                    <img
                        :src="receiptModalImageUrl"
                        alt="Payment Receipt"
                        class="max-w-full h-auto mx-auto rounded border"
                    />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { computed, ref } from "vue";
import { router } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

// Import all the new components
import CustomerInformation from "@/Components/Booking/CustomerInformation.vue";
import DriversLicenseVerification from "@/Components/Booking/DriversLicenseVerification.vue";
import VehicleInformation from "@/Components/Booking/VehicleInformation.vue";
import BookingDetails from "@/Components/Booking/BookingDetails.vue";
import LiveOverchargeTracking from "@/Components/Booking/LiveOverchargeTracking.vue";
import FinalPaymentBreakdown from "@/Components/Booking/FinalPaymentBreakdown.vue";
import ManualOverchargeForm from "@/Components/Booking/ManualOverchargeForm.vue";
import PaymentProof from "@/Components/Booking/PaymentProof.vue";
import ActionsSidebar from "@/Components/Booking/ActionsSidebar.vue";

const props = defineProps({
    booking: Object,
    expectedReturnTime: String,
    potentialOvercharges: Array,
    recentOvercharges: Array,
    stats: Object,
});

const processing = ref(false);
const showManualOverchargeForm = ref(false);
const manualOvercharge = ref({
    type: '1',
    amount: '',
    details: ''
});

// Modal state for license verification
const showLicenseModal = ref(false);
const licenseModalData = ref({
    side: '',
    imageUrl: ''
});

// Modal state for payment receipt
const showReceiptModal = ref(false);
const receiptModalImageUrl = ref('');

const estimatedReturn = computed(() => {
    const pickup = new Date(props.booking.pickup_datetime);
    const tier = props.booking.pricing_tier;

    let returnDate = new Date(pickup);

    switch (tier.duration_unit) {
        case "minutes":
            returnDate.setMinutes(returnDate.getMinutes() + tier.duration_from);
            break;
        case "hours":
            returnDate.setHours(returnDate.getHours() + tier.duration_from);
            break;
        case "days":
            returnDate.setDate(returnDate.getDate() + tier.duration_from);
            break;
    }

    return returnDate.toLocaleString("en-US", {
        weekday: "short",
        month: "short",
        day: "numeric",
        year: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
});

function getStatusClass(status) {
    const classes = {
        pending: "bg-yellow-100 text-yellow-800",
        confirmed: "bg-blue-100 text-blue-800",
        completed: "bg-green-100 text-green-800",
        cancelled: "bg-red-100 text-red-800",
    };
    return classes[status] || "bg-gray-100 text-gray-800";
}

function formatStatus(status) {
    const statusMap = {
        pending: "Pending",
        confirmed: "Confirmed",
        completed: "Completed",
        cancelled: "Cancelled",
    };
    return statusMap[status] || status;
}

function formatDate(date) {
    return new Date(date).toLocaleDateString("en-US", {
        year: "numeric",
        month: "long",
        day: "numeric",
    });
}

function goBack() {
    router.visit(route("owner.bookings.index"));
}

function goToVehicle() {
    router.visit(route("vehicles.show", props.booking.vehicle.id));
}

function confirmPayment() {
    processing.value = true;
    router.post(
        route("owner.bookings.confirmPayment", props.booking.id),
        {},
        {
            onFinish: () => {
                processing.value = false;
            },
        }
    );
}

function confirmBooking() {
    processing.value = true;
    router.post(
        route("owner.bookings.confirm", props.booking.id),
        {},
        {
            onFinish: () => {
                processing.value = false;
            },
        }
    );
}

function rejectBooking() {
    if (
        confirm(
            "Are you sure you want to reject this booking? This action cannot be undone."
        )
    ) {
        processing.value = true;
        router.post(
            route("owner.bookings.reject", props.booking.id),
            {},
            {
                onFinish: () => {
                    processing.value = false;
                },
            }
        );
    }
}

function completeBooking() {
    if (confirm('Are you sure you want to mark this booking as completed? This will calculate any applicable overcharges.')) {
        processing.value = true;
        router.post(
            route("owner.bookings.complete", props.booking.id),
            {},
            {
                onFinish: () => {
                    processing.value = false;
                },
                onSuccess: () => {
                    // Reload to show updated overcharge data
                    router.reload();
                }
            }
        );
    }
}

function markOverchargeAsPaid(overchargeId) {
    if (confirm('Mark this overcharge as paid?')) {
        processing.value = true;
        router.post(route('owner.overcharges.markAsPaid', overchargeId), {}, {
            onFinish: () => {
                processing.value = false;
            },
            onSuccess: () => {
                // Refresh the booking data
                router.reload({ only: ['booking'] });
            }
        });
    }
}

function refreshOverchargeStatus() {
    router.reload({
        only: ['booking', 'expectedReturnTime', 'potentialOvercharges']
    });
}

function recalculateOvercharges() {
    if (confirm('This will recalculate overcharges for this completed booking. Continue?')) {
        processing.value = true;
        router.post(route('overcharges.calculate', props.booking.id), {}, {
            onFinish: () => {
                processing.value = false;
            },
            onSuccess: () => {
                // Reload the page to show updated overcharge data
                router.reload();
            }
        });
    }
}

function addManualOvercharge(overchargeData) {
    if (!overchargeData.amount || parseFloat(overchargeData.amount) <= 0) {
        alert('Please enter a valid amount');
        return;
    }

    if (confirm(`Add ₱${overchargeData.amount} overcharge to this booking?`)) {
        processing.value = true;
        
        const data = {
            overcharge_type_id: parseInt(overchargeData.type),
            amount: parseFloat(overchargeData.amount),
            details: overchargeData.details || 'Manual overcharge added by owner',
            units: 1,
            rate_applied: parseFloat(overchargeData.amount),
            is_paid: false,
            occurred_at: new Date().toISOString()
        };

        router.post(route('owner.overcharges.add', props.booking.id), data, {
            onFinish: () => {
                processing.value = false;
            },
            onSuccess: () => {
                // Reset form and hide it
                manualOvercharge.value = { type: '1', amount: '', details: '' };
                showManualOverchargeForm.value = false;
                // Reload the page to show updated overcharge data
                router.reload();
            }
        });
    }
}

function contactRenter() {
    const phone = props.booking.user.phone;
    const email = props.booking.user.email;
    
    if (phone) {
        window.open(`tel:${phone}`, '_self');
    } else if (email) {
        const subject = encodeURIComponent(`Regarding Your Booking #${props.booking.id} - Overcharge Notice`);
        const body = encodeURIComponent(`Hello ${props.booking.user.first_name},\n\nThis is regarding your current vehicle rental booking (#${props.booking.id}).\n\nWe've detected some potential overcharges that may apply to your rental. Please contact us to discuss.\n\nBest regards,\nYour Vehicle Owner`);
        window.open(`mailto:${email}?subject=${subject}&body=${body}`, '_self');
    } else {
        alert('No contact information available for this renter.');
    }
}

// Modal handlers for Driver's License
function openLicenseModal(side, imageUrl) {
    licenseModalData.value = {
        side,
        imageUrl
    };
    showLicenseModal.value = true;
}

function closeLicenseModal() {
    showLicenseModal.value = false;
    licenseModalData.value = {
        side: '',
        imageUrl: ''
    };
}

// Modal handlers for Payment Receipt
function openReceiptModal(imageUrl) {
    receiptModalImageUrl.value = imageUrl;
    showReceiptModal.value = true;
}

function closeReceiptModal() {
    showReceiptModal.value = false;
    receiptModalImageUrl.value = '';
}
</script>

<style scoped>
/* Add any component-specific styles here */
</style>
