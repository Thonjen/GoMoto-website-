<template>
    <Head title="Extension Requests" />

    <OwnerLayout>
        <div class="glass-card border border-white/20 rounded-lg shadow-glow">
            <div class="p-6 text-white">
                <h1 class="text-3xl font-bold text-white">
                    Extension Requests
                </h1>
                <p class="text-white/70 mt-2">
                    Manage extension requests for your vehicles
                </p>
                <div class="glass-card-dark border border-white/20 rounded-lg shadow-glow mt-6">
                    <div class="p-6">
                        <div
                            v-if="extensionRequests.data.length === 0"
                            class="text-center py-8"
                        >
                            <svg
                                class="mx-auto h-12 w-12 text-white/40"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                />
                            </svg>
                            <p class="mt-2 text-lg font-medium text-white">
                                No extension requests
                            </p>
                            <p class="mt-1 text-white/60">
                                When renters request to extend their bookings,
                                they will appear here.
                            </p>
                        </div>

                        <div v-else class="space-y-6">
                            <div
                                v-for="request in extensionRequests.data"
                                :key="request.id"
                                class="glass-card border border-white/20 rounded-lg p-6 hover:bg-white/10 transition-all backdrop-blur-sm"
                            >
                                <!-- Header with Renter and Vehicle Info -->
                                <div
                                    class="flex items-start justify-between mb-4"
                                >
                                    <div class="flex items-center space-x-4">
                                        <div class="flex-shrink-0">
                                            <div
                                                class="w-12 h-12 bg-blue-400/20 border border-blue-400/30 rounded-full flex items-center justify-center backdrop-blur-sm"
                                            >
                                                <svg
                                                    class="w-6 h-6 text-blue-400"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    viewBox="0 0 24 24"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                                                    />
                                                </svg>
                                            </div>
                                        </div>
                                        <div>
                                            <h3
                                                class="text-lg font-semibold text-white"
                                            >
                                                {{ request.booking.user.name }}
                                            </h3>
                                            <p class="text-sm text-white/70">
                                                {{
                                                    request.booking.vehicle
                                                        .brand?.name
                                                }}
                                                {{
                                                    request.booking.vehicle
                                                        .vehicle_type?.name
                                                }}
                                            </p>
                                            <p class="text-xs text-white/50">
                                                Booking #{{
                                                    request.booking.id
                                                }}
                                            </p>
                                        </div>
                                    </div>
                                    <span
                                        :class="getStatusClass(request.status)"
                                        class="px-3 py-1 rounded-full text-sm font-medium backdrop-blur-sm"
                                    >
                                        {{ formatStatus(request.status) }}
                                    </span>
                                </div>

                                <div class="bg-white/5 border border-white/20 rounded-lg p-4 mb-4 backdrop-blur-sm">
                                    <div
                                        class="grid grid-cols-1 md:grid-cols-3 gap-4"
                                    >
                                        <div class="text-center">
                                            <p
                                                class="text-2xl font-bold text-blue-400"
                                            >
                                                {{ request.requested_hours }}
                                            </p>
                                            <p class="text-sm text-white/60">
                                                Hours Extension
                                            </p>
                                        </div>
                                        <div class="text-center">
                                            <p
                                                class="text-2xl font-bold text-green-400"
                                            >
                                                ₱{{
                                                    formatCurrency(
                                                        request.calculated_cost
                                                    )
                                                }}
                                            </p>
                                            <p class="text-sm text-white/60">
                                                Additional Cost
                                            </p>
                                        </div>
                                        <div class="text-center">
                                            <p class="text-sm text-white/60">
                                                Requested
                                            </p>
                                            <p class="font-medium text-white">
                                                {{
                                                    formatDate(
                                                        request.created_at
                                                    )
                                                }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div v-if="request.reason" class="mb-4">
                                    <p
                                        class="text-sm font-medium text-white mb-1"
                                    >
                                        Reason:
                                    </p>
                                    <p
                                        class="text-white/80 bg-white/5 border border-white/20 rounded p-3 backdrop-blur-sm"
                                    >
                                        {{ request.reason }}
                                    </p>
                                </div>

                                <div v-if="request.owner_notes" class="mb-4">
                                    <p
                                        class="text-sm font-medium text-white mb-1"
                                    >
                                        Your Notes:
                                    </p>
                                    <p
                                        class="text-white/80 bg-white/5 border border-white/20 rounded p-3 backdrop-blur-sm"
                                    >
                                        {{ request.owner_notes }}
                                    </p>
                                </div>

                                <!-- Action buttons for pending requests -->
                                <div
                                    v-if="request.status === 'pending'"
                                    class="flex justify-end space-x-3"
                                >
                                    <button
                                        @click="showRejectionModal(request)"
                                        class="px-4 py-2 bg-red-400/80 hover:bg-red-400 text-white text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-red-400/50 transition-colors backdrop-blur-sm"
                                    >
                                        Reject
                                    </button>
                                    <button
                                        @click="showApprovalModal(request)"
                                        class="px-6 py-2 bg-green-400/80 hover:bg-green-400 text-white text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-green-400/50 transition-colors backdrop-blur-sm"
                                    >
                                        Approve
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6" v-if="extensionRequests.links">
                            <nav class="flex items-center justify-between">
                                <div
                                    class="flex-1 flex justify-between sm:hidden"
                                >
                                    <Link
                                        v-if="extensionRequests.prev_page_url"
                                        :href="extensionRequests.prev_page_url"
                                        class="relative inline-flex items-center px-4 py-2 border border-white/20 text-sm font-medium rounded-md text-white bg-white/10 hover:bg-white/20 backdrop-blur-sm transition-colors"
                                    >
                                        Previous
                                    </Link>
                                    <Link
                                        v-if="extensionRequests.next_page_url"
                                        :href="extensionRequests.next_page_url"
                                        class="ml-3 relative inline-flex items-center px-4 py-2 border border-white/20 text-sm font-medium rounded-md text-white bg-white/10 hover:bg-white/20 backdrop-blur-sm transition-colors"
                                    >
                                        Next
                                    </Link>
                                </div>
                                <div
                                    class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between"
                                >
                                    <div>
                                        <p class="text-sm text-white/70">
                                            Showing
                                            {{ extensionRequests.from }} to
                                            {{ extensionRequests.to }} of
                                            {{
                                                extensionRequests.total
                                            }}
                                            results
                                        </p>
                                    </div>
                                    <div>
                                        <span
                                            class="relative z-0 inline-flex shadow-sm rounded-md"
                                        >
                                            <template
                                                v-for="(
                                                    link, index
                                                ) in extensionRequests.links"
                                                :key="index"
                                            >
                                                <Link
                                                    v-if="link && link.url"
                                                    :href="link.url"
                                                    :class="
                                                        link.active
                                                            ? 'bg-blue-400/20 border-blue-400/30 text-blue-400'
                                                            : 'bg-white/10 border-white/20 text-white/70 hover:bg-white/20'
                                                    "
                                                    class="relative inline-flex items-center px-4 py-2 border text-sm font-medium backdrop-blur-sm transition-colors"
                                                    v-html="link.label"
                                                >
                                                </Link>
                                            </template>
                                        </span>
                                    </div>
                                </div>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Approval Modal -->
        <div
            v-if="showApproval"
            @click.self="closeModals"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
        >
            <div class="glass-card border border-white/20 rounded-lg max-w-md w-full mx-4 p-6 backdrop-blur-sm">
                <h3 class="text-lg font-medium text-white mb-4">
                    Approve Extension Request
                </h3>
                <p class="text-white/70 mb-4">
                    Approve {{ selectedRequest?.requested_hours }} hours
                    extension for Booking #{{ selectedRequest?.booking.id }}?
                </p>
                <form @submit.prevent="approveRequest">
                    <div class="mb-4">
                        <label
                            class="block text-sm font-medium text-white mb-2"
                        >
                            Notes to renter (optional)
                        </label>
                        <textarea
                            v-model="approvalForm.owner_notes"
                            rows="3"
                            class="w-full bg-white/10 border-white/20 text-white placeholder-white/50 rounded-md shadow-sm focus:border-green-400 focus:ring-green-400/50 backdrop-blur-sm"
                            placeholder="Any additional notes or instructions..."
                        ></textarea>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button
                            type="button"
                            @click="closeModals"
                            class="px-4 py-2 border border-white/20 rounded-md text-white/70 hover:bg-white/10 backdrop-blur-sm transition-colors"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            :disabled="approvalForm.processing"
                            class="px-4 py-2 bg-green-400/80 hover:bg-green-400 text-white rounded-md disabled:opacity-50 backdrop-blur-sm transition-colors"
                        >
                            {{
                                approvalForm.processing
                                    ? "Approving..."
                                    : "Approve"
                            }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Rejection Modal -->
        <div
            v-if="showRejection"
            @click.self="closeModals"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
        >
            <div class="glass-card border border-white/20 rounded-lg max-w-md w-full mx-4 p-6 backdrop-blur-sm">
                <h3 class="text-lg font-medium text-white mb-4">
                    Reject Extension Request
                </h3>
                <p class="text-white/70 mb-4">
                    Reject {{ selectedRequest?.requested_hours }} hours
                    extension for Booking #{{ selectedRequest?.booking.id }}?
                </p>
                <form @submit.prevent="rejectRequest">
                    <div class="mb-4">
                        <label
                            class="block text-sm font-medium text-white mb-2"
                        >
                            Reason for rejection
                        </label>
                        <textarea
                            v-model="rejectionForm.owner_notes"
                            rows="3"
                            class="w-full bg-white/10 border-white/20 text-white placeholder-white/50 rounded-md shadow-sm focus:border-red-400 focus:ring-red-400/50 backdrop-blur-sm"
                            placeholder="Please explain why you're rejecting this request..."
                            required
                        ></textarea>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button
                            type="button"
                            @click="closeModals"
                            class="px-4 py-2 border border-white/20 rounded-md text-white/70 hover:bg-white/10 backdrop-blur-sm transition-colors"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            :disabled="rejectionForm.processing"
                            class="px-4 py-2 bg-red-400/80 hover:bg-red-400 text-white rounded-md disabled:opacity-50 backdrop-blur-sm transition-colors"
                        >
                            {{
                                rejectionForm.processing
                                    ? "Rejecting..."
                                    : "Reject"
                            }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </OwnerLayout>
</template>

<script setup>
import { ref } from "vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import OwnerLayout from "@/Layouts/OwnerLayout.vue";

const props = defineProps({
    extensionRequests: Object,
});

const showApproval = ref(false);
const showRejection = ref(false);
const selectedRequest = ref(null);

const approvalForm = useForm({
    owner_notes: "",
});

const rejectionForm = useForm({
    owner_notes: "",
});

const getStatusClass = (status) => {
    switch (status) {
        case "pending":
            return "bg-yellow-400/20 text-yellow-400 border-yellow-400/30";
        case "approved":
            return "bg-green-400/20 text-green-400 border-green-400/30";
        case "rejected":
            return "bg-red-400/20 text-red-400 border-red-400/30";
        default:
            return "bg-white/10 text-white/70 border-white/20";
    }
};

const formatStatus = (status) => {
    return status.charAt(0).toUpperCase() + status.slice(1);
};

const formatCurrency = (amount) => {
    return parseFloat(amount || 0).toFixed(2);
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString("en-US", {
        year: "numeric",
        month: "short",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
};

const showApprovalModal = (request) => {
    selectedRequest.value = request;
    approvalForm.owner_notes = "";
    showApproval.value = true;
};

const showRejectionModal = (request) => {
    selectedRequest.value = request;
    rejectionForm.owner_notes = "";
    showRejection.value = true;
};

const closeModals = () => {
    showApproval.value = false;
    showRejection.value = false;
    selectedRequest.value = null;
};

const approveRequest = () => {
    approvalForm.post(
        route("owner.extensionRequests.approve", selectedRequest.value.id),
        {
            onSuccess: () => {
                closeModals();
            },
        }
    );
};

const rejectRequest = () => {
    rejectionForm.post(
        route("owner.extensionRequests.reject", selectedRequest.value.id),
        {
            onSuccess: () => {
                closeModals();
            },
        }
    );
};
</script>
