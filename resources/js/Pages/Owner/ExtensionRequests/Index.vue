<template>
    <Head title="Extension Requests" />

    <OwnerLayout>
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h1 class="text-3xl font-bold text-gray-800">
                    Extension Requests
                </h1>
                <p class="text-gray-600 mt-2">
                    Manage extension requests for your vehicles
                </p>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div
                            v-if="extensionRequests.data.length === 0"
                            class="text-center py-8"
                        >
                            <svg
                                class="mx-auto h-12 w-12 text-gray-400"
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
                            <p class="mt-2 text-lg font-medium text-gray-900">
                                No extension requests
                            </p>
                            <p class="mt-1 text-gray-500">
                                When renters request to extend their bookings,
                                they will appear here.
                            </p>
                        </div>

                        <div v-else class="space-y-6">
                            <div
                                v-for="request in extensionRequests.data"
                                :key="request.id"
                                class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow"
                            >
                                <!-- Header with Renter and Vehicle Info -->
                                <div
                                    class="flex items-start justify-between mb-4"
                                >
                                    <div class="flex items-center space-x-4">
                                        <div class="flex-shrink-0">
                                            <div
                                                class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center"
                                            >
                                                <svg
                                                    class="w-6 h-6 text-blue-600"
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
                                                class="text-lg font-semibold text-gray-900"
                                            >
                                                {{ request.booking.user.name }}
                                            </h3>
                                            <p class="text-sm text-gray-600">
                                                {{
                                                    request.booking.vehicle
                                                        .brand?.name
                                                }}
                                                {{
                                                    request.booking.vehicle
                                                        .vehicle_type?.name
                                                }}
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                Booking #{{
                                                    request.booking.id
                                                }}
                                            </p>
                                        </div>
                                    </div>
                                    <span
                                        :class="getStatusClass(request.status)"
                                        class="px-3 py-1 rounded-full text-sm font-medium"
                                    >
                                        {{ formatStatus(request.status) }}
                                    </span>
                                </div>

                                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                                    <div
                                        class="grid grid-cols-1 md:grid-cols-3 gap-4"
                                    >
                                        <div class="text-center">
                                            <p
                                                class="text-2xl font-bold text-blue-600"
                                            >
                                                {{ request.requested_hours }}
                                            </p>
                                            <p class="text-sm text-gray-500">
                                                Hours Extension
                                            </p>
                                        </div>
                                        <div class="text-center">
                                            <p
                                                class="text-2xl font-bold text-green-600"
                                            >
                                                ₱{{
                                                    formatCurrency(
                                                        request.calculated_cost
                                                    )
                                                }}
                                            </p>
                                            <p class="text-sm text-gray-500">
                                                Additional Cost
                                            </p>
                                        </div>
                                        <div class="text-center">
                                            <p class="text-sm text-gray-500">
                                                Requested
                                            </p>
                                            <p class="font-medium">
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
                                        class="text-sm font-medium text-gray-700 mb-1"
                                    >
                                        Reason:
                                    </p>
                                    <p
                                        class="text-gray-600 bg-white border rounded p-3"
                                    >
                                        {{ request.reason }}
                                    </p>
                                </div>

                                <div v-if="request.owner_notes" class="mb-4">
                                    <p
                                        class="text-sm font-medium text-gray-700 mb-1"
                                    >
                                        Your Notes:
                                    </p>
                                    <p
                                        class="text-gray-600 bg-white border rounded p-3"
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
                                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-red-500"
                                    >
                                        Reject
                                    </button>
                                    <button
                                        @click="showApprovalModal(request)"
                                        class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
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
                                        class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                                    >
                                        Previous
                                    </Link>
                                    <Link
                                        v-if="extensionRequests.next_page_url"
                                        :href="extensionRequests.next_page_url"
                                        class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                                    >
                                        Next
                                    </Link>
                                </div>
                                <div
                                    class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between"
                                >
                                    <div>
                                        <p class="text-sm text-gray-700">
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
                                                            ? 'bg-blue-50 border-blue-500 text-blue-600'
                                                            : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'
                                                    "
                                                    class="relative inline-flex items-center px-4 py-2 border text-sm font-medium"
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
            <div class="bg-white rounded-lg max-w-md w-full mx-4 p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    Approve Extension Request
                </h3>
                <p class="text-gray-600 mb-4">
                    Approve {{ selectedRequest?.requested_hours }} hours
                    extension for Booking #{{ selectedRequest?.booking.id }}?
                </p>
                <form @submit.prevent="approveRequest">
                    <div class="mb-4">
                        <label
                            class="block text-sm font-medium text-gray-700 mb-2"
                        >
                            Notes to renter (optional)
                        </label>
                        <textarea
                            v-model="approvalForm.owner_notes"
                            rows="3"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500"
                            placeholder="Any additional notes or instructions..."
                        ></textarea>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button
                            type="button"
                            @click="closeModals"
                            class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            :disabled="approvalForm.processing"
                            class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md disabled:opacity-50"
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
            <div class="bg-white rounded-lg max-w-md w-full mx-4 p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    Reject Extension Request
                </h3>
                <p class="text-gray-600 mb-4">
                    Reject {{ selectedRequest?.requested_hours }} hours
                    extension for Booking #{{ selectedRequest?.booking.id }}?
                </p>
                <form @submit.prevent="rejectRequest">
                    <div class="mb-4">
                        <label
                            class="block text-sm font-medium text-gray-700 mb-2"
                        >
                            Reason for rejection
                        </label>
                        <textarea
                            v-model="rejectionForm.owner_notes"
                            rows="3"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-red-500 focus:ring-red-500"
                            placeholder="Please explain why you're rejecting this request..."
                            required
                        ></textarea>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button
                            type="button"
                            @click="closeModals"
                            class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            :disabled="rejectionForm.processing"
                            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md disabled:opacity-50"
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
            return "bg-yellow-100 text-yellow-800";
        case "approved":
            return "bg-green-100 text-green-800";
        case "rejected":
            return "bg-red-100 text-red-800";
        default:
            return "bg-gray-100 text-gray-800";
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
