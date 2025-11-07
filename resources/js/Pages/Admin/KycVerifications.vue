<template>
    <AdminLayout>
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <!-- Success/Error Toast Notification -->
            <transition
                enter-active-class="transition ease-out duration-300"
                enter-from-class="transform translate-x-full opacity-0"
                enter-to-class="transform translate-x-0 opacity-100"
                leave-active-class="transition ease-in duration-200"
                leave-from-class="transform translate-x-0 opacity-100"
                leave-to-class="transform translate-x-full opacity-0"
            >
                <div
                    v-if="showNotification"
                    :class="[
                        'fixed top-4 right-4 z-50 max-w-md p-4 rounded-lg shadow-2xl border backdrop-blur-sm mx-4',
                        notificationType === 'success'
                            ? 'bg-green-400/20 border-green-400/30 text-green-300'
                            : 'bg-red-400/20 border-red-400/30 text-red-300',
                    ]"
                >
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg
                                v-if="notificationType === 'success'"
                                class="h-6 w-6"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                />
                            </svg>
                            <svg
                                v-else
                                class="h-6 w-6"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"
                                />
                            </svg>
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-sm font-medium">
                                {{ notificationMessage }}
                            </p>
                        </div>
                        <button
                            @click="showNotification = false"
                            class="ml-4 flex-shrink-0 inline-flex text-white/70 hover:text-white focus:outline-none"
                        >
                            <svg
                                class="h-5 w-5"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"
                                />
                            </svg>
                        </button>
                    </div>
                </div>
            </transition>

            <!-- Header -->
            <div class="mb-8">
                <div class="flex flex-col space-y-4 sm:flex-row sm:justify-between sm:items-center sm:space-y-0">
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-bold text-white">
                            Driver's License
                        </h1>
                        <p class="mt-2 text-white/70">
                            Review and manage user verification requests
                        </p>
                    </div>
                    <div class="flex flex-col space-y-3 sm:flex-row sm:space-y-0 sm:space-x-4">
                        <Link
                            :href="route('admin.kyc.logs')"
                            class="bg-blue-400/20 hover:bg-blue-400/30 text-blue-300 font-medium py-2 px-4 rounded-md border border-blue-400/30 transition-all duration-200 backdrop-blur-sm shadow-glow text-center"
                        >
                            View Logs
                        </Link>
                        <Link
                            :href="route('admin.dashboard')"
                            class="bg-white/10 hover:bg-white/20 text-white font-medium py-2 px-4 rounded-md border border-white/20 transition-all duration-200 backdrop-blur-sm shadow-glow text-center"
                        >
                            Back to Dashboard
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8">
                <div class="glass-card-dark">
                    <div class="bg-yellow-400/20 p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg
                                    class="h-6 w-6 text-yellow-400"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                    />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt
                                        class="text-sm font-medium text-white/70 truncate"
                                    >
                                        Pending Review
                                    </dt>
                                    <dd class="text-lg font-medium text-white">
                                        {{ stats.pending_review }}
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="glass-card-dark">
                    <div class="bg-green-400/20 p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg
                                    class="h-6 w-6 text-green-400"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                    />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt
                                        class="text-sm font-medium text-white/70 truncate"
                                    >
                                        Approved
                                    </dt>
                                    <dd class="text-lg font-medium text-white">
                                        {{ stats.approved }}
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="glass-card-dark">
                    <div class="bg-red-400/20 p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg
                                    class="h-6 w-6 text-red-400"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"
                                    />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt
                                        class="text-sm font-medium text-white/70 truncate"
                                    >
                                        Rejected
                                    </dt>
                                    <dd class="text-lg font-medium text-white">
                                        {{ stats.rejected }}
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="glass-card-dark">
                    <div class="bg-blue-400/20 p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg
                                    class="h-6 w-6 text-blue-400"
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
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt
                                        class="text-sm font-medium text-white/70 truncate"
                                    >
                                        Total Submissions
                                    </dt>
                                    <dd class="text-lg font-medium text-white">
                                        {{ stats.total_submissions }}
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="glass-card-dark mb-6">
                <div class="px-4 py-5 sm:p-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-white"
                                >Search Users</label
                            >
                            <input
                                v-model="searchForm.search"
                                @input="debouncedSearch"
                                type="text"
                                placeholder="Name or email..."
                                class="mt-1 block w-full bg-white/10 border border-white/20 rounded-md px-3 py-2 text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 backdrop-blur-sm"
                            />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-white"
                                >Status</label
                            >
                            <select
                                v-model="searchForm.status"
                                @change="performSearch"
                                class="mt-1 block w-full bg-white/10 border border-white/20 rounded-md px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 backdrop-blur-sm"
                            >
                                <option value="" class="bg-gray-800 text-white">
                                    All Statuses
                                </option>
                                <option
                                    value="under_review"
                                    class="bg-gray-800 text-white"
                                >
                                    Under Review
                                </option>
                                <option
                                    value="approved"
                                    class="bg-gray-800 text-white"
                                >
                                    Approved
                                </option>
                                <option
                                    value="rejected"
                                    class="bg-gray-800 text-white"
                                >
                                    Rejected
                                </option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-white"
                                >Role</label
                            >
                            <select
                                v-model="searchForm.role"
                                @change="performSearch"
                                class="mt-1 block w-full bg-white/10 border border-white/20 rounded-md px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 backdrop-blur-sm"
                            >
                                <option value="" class="bg-gray-800 text-white">
                                    All Roles
                                </option>
                                <option
                                    v-for="role in roles"
                                    :key="role.id"
                                    :value="role.name"
                                    class="bg-gray-800 text-white"
                                >
                                    {{ role.name.charAt(0).toUpperCase() + role.name.slice(1) }}
                                </option>
                            </select>
                        </div>

                        <div class="flex items-end">
                            <button
                                @click="clearFilters"
                                class="bg-white/10 hover:bg-white/20 text-white font-medium py-2 px-4 rounded-md border border-white/20 transition-all duration-200 backdrop-blur-sm shadow-glow"
                            >
                                Clear Filters
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Users Table -->
            <div class="glass-card-dark">
                <div class="px-4 py-5 sm:p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-white/20">
                            <thead class="bg-white/5">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-white/70 uppercase tracking-wider"
                                    >
                                        User
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-white/70 uppercase tracking-wider"
                                    >
                                        Role
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-white/70 uppercase tracking-wider"
                                    >
                                        Status
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-white/70 uppercase tracking-wider"
                                    >
                                        Submitted
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-white/70 uppercase tracking-wider"
                                    >
                                        Documents
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-white/70 uppercase tracking-wider"
                                    >
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody
                                class="bg-transparent divide-y divide-white/10"
                            >
                                <tr
                                    v-for="user in users.data"
                                    :key="user.id"
                                    class="hover:bg-white/5 transition-colors duration-150"
                                >
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div
                                                class="flex-shrink-0 h-10 w-10"
                                            >
                                                <div
                                                    class="h-10 w-10 rounded-full bg-white/20 flex items-center justify-center border border-white/20 backdrop-blur-sm"
                                                >
                                                    <span
                                                        class="text-sm font-medium text-white"
                                                    >
                                                        {{ user.first_name[0]
                                                        }}{{
                                                            user.last_name[0]
                                                        }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div
                                                    class="text-sm font-medium text-white"
                                                >
                                                    {{ user.first_name }}
                                                    {{ user.last_name }}
                                                </div>
                                                <div
                                                    class="text-sm text-white/70"
                                                >
                                                    {{ user.email }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-400/20 text-blue-300 border border-blue-400/30 backdrop-blur-sm"
                                        >
                                            {{ user.role?.name || "N/A" }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            :class="[
                                                'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border backdrop-blur-sm',
                                                user.kyc_status === 'approved'
                                                    ? 'bg-green-400/20 text-green-300 border-green-400/30'
                                                    : user.kyc_status ===
                                                      'under_review'
                                                    ? 'bg-blue-400/20 text-blue-300 border-blue-400/30'
                                                    : user.kyc_status ===
                                                      'rejected'
                                                    ? 'bg-red-400/20 text-red-300 border-red-400/30'
                                                    : 'bg-yellow-400/20 text-yellow-300 border-yellow-400/30',
                                            ]"
                                        >
                                            {{
                                                user.kyc_status
                                                    .replace("_", " ")
                                                    .toUpperCase()
                                            }}
                                        </span>
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm text-white/70"
                                    >
                                        {{
                                            user.license_submitted_at
                                                ? formatDate(
                                                      user.license_submitted_at
                                                  )
                                                : "Not submitted"
                                        }}
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm text-white/70"
                                    >
                                        <div class="flex space-x-2">
                                            <span
                                                v-if="
                                                    user.drivers_license_front
                                                "
                                                class="text-green-400"
                                                >✓ Front</span
                                            >
                                            <span v-else class="text-red-400"
                                                >✗ Front</span
                                            >
                                            <span
                                                v-if="user.drivers_license_back"
                                                class="text-green-400"
                                                >✓ Back</span
                                            >
                                            <span v-else class="text-red-400"
                                                >✗ Back</span
                                            >
                                            <span
                                                v-if="user.bio"
                                                class="text-green-400"
                                                >✓ Bio</span
                                            >
                                        </div>
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm font-medium"
                                    >
                                        <div class="flex space-x-2">
                                            <button
                                                @click="viewUserDetails(user)"
                                                class="text-blue-400 hover:text-blue-300 transition-colors duration-150"
                                            >
                                                View
                                            </button>
                                            <button
                                                v-if="
                                                    user.kyc_status ===
                                                    'under_review'
                                                "
                                                @click="approveUser(user)"
                                                class="text-green-400 hover:text-green-300 transition-colors duration-150"
                                            >
                                                Approve
                                            </button>
                                            <button
                                                v-if="
                                                    user.kyc_status ===
                                                    'under_review'
                                                "
                                                @click="rejectUser(user)"
                                                class="text-red-400 hover:text-red-300 transition-colors duration-150"
                                            >
                                                Reject
                                            </button>
                                            <button
                                                v-if="
                                                    user.kyc_status ===
                                                        'approved' ||
                                                    user.kyc_status ===
                                                        'rejected'
                                                "
                                                @click="
                                                    sendKycNotification(
                                                        user,
                                                        'sms'
                                                    )
                                                "
                                                :disabled="
                                                    sendingNotification[
                                                        `${user.id}_sms`
                                                    ] ||
                                                    (notificationSent[
                                                        `${user.id}_sms`
                                                    ] &&
                                                        Date.now() -
                                                            notificationSent[
                                                                `${user.id}_sms`
                                                            ] <
                                                            30000)
                                                "
                                                :class="[
                                                    'transition-all duration-150 flex items-center space-x-1',
                                                    sendingNotification[
                                                        `${user.id}_sms`
                                                    ]
                                                        ? 'text-indigo-300 cursor-wait'
                                                        : notificationSent[
                                                              `${user.id}_sms`
                                                          ] &&
                                                          Date.now() -
                                                              notificationSent[
                                                                  `${user.id}_sms`
                                                              ] <
                                                              30000
                                                        ? 'text-green-400 cursor-not-allowed'
                                                        : 'text-indigo-400 hover:text-indigo-300 cursor-pointer',
                                                ]"
                                                :title="
                                                    sendingNotification[
                                                        `${user.id}_sms`
                                                    ]
                                                        ? 'Sending SMS...'
                                                        : notificationSent[
                                                              `${user.id}_sms`
                                                          ] &&
                                                          Date.now() -
                                                              notificationSent[
                                                                  `${user.id}_sms`
                                                              ] <
                                                              30000
                                                        ? 'SMS sent recently'
                                                        : 'Send SMS Notification'
                                                "
                                            >
                                                <span
                                                    v-if="
                                                        sendingNotification[
                                                            `${user.id}_sms`
                                                        ]
                                                    "
                                                    class="inline-block animate-spin"
                                                    >⏳</span
                                                >
                                                <span
                                                    v-else-if="
                                                        notificationSent[
                                                            `${user.id}_sms`
                                                        ] &&
                                                        Date.now() -
                                                            notificationSent[
                                                                `${user.id}_sms`
                                                            ] <
                                                            30000
                                                    "
                                                    >✓</span
                                                >
                                                <span v-else>📱</span>
                                                <span class="text-xs">SMS</span>
                                            </button>
                                            <button
                                                v-if="
                                                    user.kyc_status ===
                                                        'approved' ||
                                                    user.kyc_status ===
                                                        'rejected'
                                                "
                                                @click="
                                                    sendKycNotification(
                                                        user,
                                                        'email'
                                                    )
                                                "
                                                :disabled="
                                                    sendingNotification[
                                                        `${user.id}_email`
                                                    ] ||
                                                    (notificationSent[
                                                        `${user.id}_email`
                                                    ] &&
                                                        Date.now() -
                                                            notificationSent[
                                                                `${user.id}_email`
                                                            ] <
                                                            30000)
                                                "
                                                :class="[
                                                    'transition-all duration-150 flex items-center space-x-1',
                                                    sendingNotification[
                                                        `${user.id}_email`
                                                    ]
                                                        ? 'text-purple-300 cursor-wait'
                                                        : notificationSent[
                                                              `${user.id}_email`
                                                          ] &&
                                                          Date.now() -
                                                              notificationSent[
                                                                  `${user.id}_email`
                                                              ] <
                                                              30000
                                                        ? 'text-green-400 cursor-not-allowed'
                                                        : 'text-purple-400 hover:text-purple-300 cursor-pointer',
                                                ]"
                                                :title="
                                                    sendingNotification[
                                                        `${user.id}_email`
                                                    ]
                                                        ? 'Sending Email...'
                                                        : notificationSent[
                                                              `${user.id}_email`
                                                          ] &&
                                                          Date.now() -
                                                              notificationSent[
                                                                  `${user.id}_email`
                                                              ] <
                                                              30000
                                                        ? 'Email sent recently'
                                                        : 'Send Email Notification'
                                                "
                                            >
                                                <span
                                                    v-if="
                                                        sendingNotification[
                                                            `${user.id}_email`
                                                        ]
                                                    "
                                                    class="inline-block animate-spin"
                                                    >⏳</span
                                                >
                                                <span
                                                    v-else-if="
                                                        notificationSent[
                                                            `${user.id}_email`
                                                        ] &&
                                                        Date.now() -
                                                            notificationSent[
                                                                `${user.id}_email`
                                                            ] <
                                                            30000
                                                    "
                                                    >✓</span
                                                >
                                                <span v-else>✉️</span>
                                                <span class="text-xs"
                                                    >Email</span
                                                >
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6">
                        <nav class="flex items-center justify-between">
                            <div class="flex-1 flex justify-between sm:hidden">
                                <Link
                                    v-if="users.prev_page_url"
                                    :href="users.prev_page_url"
                                    class="relative inline-flex items-center px-4 py-2 border border-white/20 text-sm font-medium rounded-md text-white bg-white/10 hover:bg-white/20 backdrop-blur-sm transition-all duration-200"
                                >
                                    Previous
                                </Link>
                                <Link
                                    v-if="users.next_page_url"
                                    :href="users.next_page_url"
                                    class="ml-3 relative inline-flex items-center px-4 py-2 border border-white/20 text-sm font-medium rounded-md text-white bg-white/10 hover:bg-white/20 backdrop-blur-sm transition-all duration-200"
                                >
                                    Next
                                </Link>
                            </div>
                            <div
                                class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between"
                            >
                                <div>
                                    <p class="text-sm text-white/70">
                                        Showing {{ users.from || 0 }} to
                                        {{ users.to || 0 }} of
                                        {{ users.total }} results
                                    </p>
                                </div>
                                <div class="flex space-x-1">
                                    <template
                                        v-for="link in users.links"
                                        :key="link.label"
                                    >
                                        <Link
                                            v-if="link.url"
                                            :href="link.url"
                                            v-html="link.label"
                                            :class="[
                                                'relative inline-flex items-center px-4 py-2 border text-sm font-medium rounded backdrop-blur-sm transition-all duration-200',
                                                link.active
                                                    ? 'z-10 bg-blue-400/20 border-blue-400/30 text-blue-300'
                                                    : 'bg-white/10 border-white/20 text-white/70 hover:bg-white/20 hover:text-white',
                                            ]"
                                        />
                                        <span
                                            v-else
                                            v-html="link.label"
                                            class="relative inline-flex items-center px-4 py-2 border text-sm font-medium cursor-not-allowed opacity-50 bg-white/5 border-white/10 text-white/50 rounded backdrop-blur-sm"
                                        />
                                    </template>
                                </div>
                            </div>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- View Details Modal -->
            <div
                v-if="selectedUser"
                class="fixed inset-0 bg-black/50 overflow-y-auto h-full w-full z-50 backdrop-blur-sm"
            >
                <div
                    class="relative top-20 mx-4 sm:mx-auto p-5 border border-white/20 w-11/12 max-w-4xl shadow-glow rounded-xl glass-card-dark"
                >
                    <div class="mt-3">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-white">
                                KYC Details - {{ selectedUser.first_name }}
                                {{ selectedUser.last_name }}
                            </h3>
                            <button
                                @click="selectedUser = null"
                                class="text-white/70 hover:text-white transition-colors duration-150"
                            >
                                <svg
                                    class="h-6 w-6"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
  <!-- User Information -->
  <div class="p-4 rounded-lg max-w-2xl">
    <h4 class="text-xl font-bold text-white mb-4">
      User Information
    </h4>

    <!-- Name -->
    <div class="mb-4">
      <div class="text-sm font-medium text-white/70 mb-1">Name</div>
      <div class="text-xl font-bold text-white leading-snug">
        {{ selectedUser.first_name }} {{ selectedUser.last_name }}
      </div>
    </div>

    <!-- Email -->
    <div class="mb-4">
      <div class="text-sm font-medium text-white/70 mb-1">Email</div>
      <div class="text-lg font-semibold text-white leading-snug">
        {{ selectedUser.email }}
      </div>
    </div>

    <!-- Phone -->
    <div class="mb-4">
      <div class="text-sm font-medium text-white/70 mb-1">Phone</div>
      <div class="text-lg font-semibold text-white leading-snug">
        {{ selectedUser.phone || "Not provided" }}
      </div>
    </div>

    <!-- Role -->
    <div class="mb-4">
      <div class="text-sm font-medium text-white/70 mb-1">Role</div>
      <div class="text-lg font-semibold text-white leading-snug">
        {{ selectedUser.role?.name || "N/A" }}
      </div>
    </div>

    <!-- Status -->
    <div class="mb-4">
      <div class="text-sm font-medium text-white/70 mb-1">Status</div>
      <div>
        <span
          :class="[
            'inline-flex items-center px-3 py-0.5 rounded-full text-base font-bold border backdrop-blur-sm',
            selectedUser.kyc_status === 'approved'
              ? 'bg-green-400/20 text-green-300 border-green-400/30'
              : selectedUser.kyc_status === 'under_review'
              ? 'bg-blue-400/20 text-blue-300 border-blue-400/30'
              : selectedUser.kyc_status === 'rejected'
              ? 'bg-red-400/20 text-red-300 border-red-400/30'
              : 'bg-yellow-400/20 text-yellow-300 border-yellow-400/30',
          ]"
        >
          {{ selectedUser.kyc_status.replace("_", " ").toUpperCase() }}
        </span>
      </div>
    </div>

    <!-- Bio -->
    <div v-if="selectedUser.bio" class="mb-4">
      <div class="text-sm font-medium text-white/70 mb-1">Bio</div>
      <div class="text-base font-semibold text-white leading-snug">
        {{ selectedUser.bio }}
      </div>
    </div>

    <!-- Rejection Reason -->
    <div v-if="selectedUser.kyc_rejection_reason" class="mb-4">
      <div class="text-sm font-medium text-red-400 mb-1">Rejection Reason</div>
      <div class="text-base font-semibold text-red-400 leading-snug">
        {{ selectedUser.kyc_rejection_reason }}
      </div>
    </div>
  </div>

  <!-- Documents -->
  <div class="p-4 rounded-lg max-w-2xl">
    <h4 class="text-xl font-bold text-white mb-4">
      Uploaded Documents
    </h4>
    <div class="space-y-3">
      <div v-if="selectedUser.drivers_license_front">
        <div class="text-sm font-medium text-white/70 mb-1">Driver's License (Front)</div>
        <div 
          @click="openImageModal(selectedUser.drivers_license_front)"
          class="cursor-pointer hover:opacity-80 transition-opacity duration-200 relative group"
        >
          <img
            :src="`/storage/${selectedUser.drivers_license_front}`"
            alt="Driver's License Front"
            class="max-w-full h-32 object-contain border border-white/20 rounded backdrop-blur-sm"
          />
          <div class="absolute inset-0 flex items-center justify-center bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity duration-200 rounded">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
            </svg>
          </div>
        </div>
      </div>

      <div v-if="selectedUser.drivers_license_back">
        <div class="text-sm font-medium text-white/70 mb-1">Driver's License (Back)</div>
        <div 
          @click="openImageModal(selectedUser.drivers_license_back)"
          class="cursor-pointer hover:opacity-80 transition-opacity duration-200 relative group"
        >
          <img
            :src="`/storage/${selectedUser.drivers_license_back}`"
            alt="Driver's License Back"
            class="max-w-full h-32 object-contain border border-white/20 rounded backdrop-blur-sm"
          />
          <div class="absolute inset-0 flex items-center justify-center bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity duration-200 rounded">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
            </svg>
          </div>
        </div>
      </div>

      <div v-if="!selectedUser.drivers_license_front && !selectedUser.drivers_license_back">
        <p class="text-white/50">No documents uploaded</p>
      </div>
    </div>
  </div>
</div>


                        <!-- Action Buttons -->
                        <div class="mt-6 flex justify-end space-x-3">
                            <button
                                @click="selectedUser = null"
                                class="bg-white/10 hover:bg-white/20 text-white font-medium py-2 px-4 rounded-md border border-white/20 transition-all duration-200 backdrop-blur-sm shadow-glow"
                            >
                                Close
                            </button>
                            <button
                                v-if="
                                    selectedUser.kyc_status === 'under_review'
                                "
                                @click="rejectUser(selectedUser)"
                                class="bg-red-400/20 hover:bg-red-400/30 text-red-300 font-medium py-2 px-4 rounded-md border border-red-400/30 transition-all duration-200 backdrop-blur-sm shadow-glow"
                            >
                                Reject
                            </button>
                            <button
                                v-if="
                                    selectedUser.kyc_status === 'under_review'
                                "
                                @click="approveUser(selectedUser)"
                                class="bg-green-400/20 hover:bg-green-400/30 text-green-300 font-medium py-2 px-4 rounded-md border border-green-400/30 transition-all duration-200 backdrop-blur-sm shadow-glow"
                            >
                                Approve
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Approve Modal -->
            <div
                v-if="showApproveModal"
                class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center backdrop-blur-sm"
            >
                <div
                    class="p-5 border border-white/20 w-11/12 max-w-md mx-4 shadow-glow rounded-xl glass-card-dark"
                >
                    <h3 class="text-lg font-medium text-white mb-4">
                        Approve KYC
                    </h3>
                    <form @submit.prevent="confirmApproval">
                        <div class="mb-4">
                            <label
                                class="block text-sm font-medium text-white/70 mb-2"
                            >
                                Approval Notes (Optional)
                            </label>
                            <textarea
                                v-model="approvalForm.reason"
                                rows="3"
                                class="w-full bg-white/10 border border-white/20 rounded-md px-3 py-2 text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-green-400 backdrop-blur-sm"
                                placeholder="Add any notes about the approval..."
                            ></textarea>
                        </div>
                        <div class="flex justify-end space-x-3">
                            <button
                                type="button"
                                @click="showApproveModal = false"
                                class="bg-white/10 hover:bg-white/20 text-white font-medium py-2 px-4 rounded-md border border-white/20 transition-all duration-200 backdrop-blur-sm shadow-glow"
                            >
                                Cancel
                            </button>
                            <button
                                type="submit"
                                class="bg-green-400/20 hover:bg-green-400/30 text-green-300 font-medium py-2 px-4 rounded-md border border-green-400/30 transition-all duration-200 backdrop-blur-sm shadow-glow"
                            >
                                Approve
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Reject Modal -->
            <div
                v-if="showRejectModal"
                class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center backdrop-blur-sm"
            >
                <div
                    class="p-5 border border-white/20 w-11/12 max-w-md mx-4 shadow-glow rounded-xl glass-card-dark"
                >
                    <h3 class="text-lg font-medium text-white mb-4">
                        Reject KYC
                    </h3>
                    <form @submit.prevent="confirmRejection">
                        <div class="mb-4">
                            <label
                                class="block text-sm font-medium text-white/70 mb-2"
                            >
                                Rejection Reason *
                            </label>
                            <textarea
                                v-model="rejectionForm.reason"
                                rows="3"
                                required
                                class="w-full bg-white/10 border border-white/20 rounded-md px-3 py-2 text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-red-400 focus:border-red-400 backdrop-blur-sm"
                                placeholder="Please provide a clear reason for rejection..."
                            ></textarea>
                        </div>
                        <div class="flex justify-end space-x-3">
                            <button
                                type="button"
                                @click="showRejectModal = false"
                                class="bg-white/10 hover:bg-white/20 text-white font-medium py-2 px-4 rounded-md border border-white/20 transition-all duration-200 backdrop-blur-sm shadow-glow"
                            >
                                Cancel
                            </button>
                            <button
                                type="submit"
                                class="bg-red-400/20 hover:bg-red-400/30 text-red-300 font-medium py-2 px-4 rounded-md border border-red-400/30 transition-all duration-200 backdrop-blur-sm shadow-glow"
                            >
                                Reject
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Image Viewer Modal -->
            <div
                v-if="showImageModal"
                @click.self="closeImageModal"
                class="fixed inset-0 bg-black/90 z-[60] flex items-center justify-center p-4"
            >
                <div class="relative max-w-5xl w-full bg-transparent rounded-lg shadow-2xl border border-white/20">
                    <!-- Header with controls -->
                    <div class="flex justify-between items-center p-4 border-b border-white/20 bg-black/80 backdrop-blur-sm">
                        <div class="flex items-center space-x-3">
                            <button
                                @click="zoomOut"
                                class="bg-white/10 hover:bg-white/20 text-white p-2 rounded border border-white/20 transition-all"
                                title="Zoom Out (Scroll Down)"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM13 10H7"></path>
                                </svg>
                            </button>
                            <span class="text-white font-medium text-sm">{{ Math.round(imageScale * 100) }}%</span>
                            <button
                                @click="zoomIn"
                                class="bg-white/10 hover:bg-white/20 text-white p-2 rounded border border-white/20 transition-all"
                                title="Zoom In (Scroll Up)"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                                </svg>
                            </button>
                            <button
                                @click="resetZoom"
                                class="bg-white/10 hover:bg-white/20 text-white px-3 py-2 rounded border border-white/20 transition-all font-medium text-sm"
                                title="Reset View"
                            >
                                Reset
                            </button>
                        </div>
                        <button
                            @click="closeImageModal"
                            class="bg-red-400/20 hover:bg-red-400/30 text-red-300 p-2 rounded border border-red-400/30 transition-all"
                            title="Close (ESC)"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Image container -->
                    <div 
                        class="relative bg-transparent overflow-hidden"
                        style="height: 70vh;"
                        @mousedown="handleMouseDown"
                        @mousemove="handleMouseMove"
                        @mouseup="handleMouseUp"
                        @mouseleave="handleMouseUp"
                        @wheel.prevent="handleWheel"
                    >
                        <div class="absolute inset-0 flex items-center justify-center p-8">
                            <img
                                :src="`/storage/${selectedImage}`"
                                alt="License Document"
                                :style="{
                                    transform: `scale(${imageScale}) translate(${imagePosition.x / imageScale}px, ${imagePosition.y / imageScale}px)`,
                                    cursor: imageScale > 1 ? (isDragging ? 'grabbing' : 'grab') : 'default',
                                    transition: isDragging ? 'none' : 'transform 0.1s ease-out',
                                }"
                                class="max-w-full max-h-full object-contain select-none"
                                draggable="false"
                            />
                        </div>
                    </div>

                    <!-- Instructions -->
                    <div class="p-3 border-t border-white/20 bg-black/80 backdrop-blur-sm">
                        <p class="text-center text-white/60 text-xs">
                            Scroll to zoom • Click and drag to pan when zoomed • Press ESC or click X to close
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref, reactive, onMounted, onUnmounted } from "vue";
import { Head, Link, router, usePage } from "@inertiajs/vue3";
import AdminLayout from "@/Layouts/AdminLayout.vue";

const props = defineProps({
    users: Object,
    stats: Object,
    filters: Object,
    roles: Object,
});

const page = usePage();
const selectedUser = ref(null);
const showApproveModal = ref(false);
const showRejectModal = ref(false);
const currentUser = ref(null);
const showNotification = ref(false);
const notificationMessage = ref("");
const notificationType = ref("success");
const sendingNotification = ref({});
const notificationSent = ref({});
const showImageModal = ref(false);
const selectedImage = ref(null);
const imageScale = ref(1);
const imagePosition = ref({ x: 0, y: 0 });
const isDragging = ref(false);
const dragStart = ref({ x: 0, y: 0 });

const searchForm = reactive({
    search: props.filters.search || "",
    status: props.filters.status || "",
    role: props.filters.role || "",
});

const approvalForm = reactive({
    reason: "",
});

const rejectionForm = reactive({
    reason: "",
});

let searchTimeout = null;

// Show notification helper
const displayNotification = (message, type = "success") => {
    notificationMessage.value = message;
    notificationType.value = type;
    showNotification.value = true;

    // Auto-hide after 5 seconds
    setTimeout(() => {
        showNotification.value = false;
    }, 5000);
};

const debouncedSearch = () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        performSearch();
    }, 300);
};

const performSearch = () => {
    router.get(route("admin.kyc.verifications"), searchForm, {
        preserveState: true,
        replace: true,
    });
};

const clearFilters = () => {
    searchForm.search = "";
    searchForm.status = "";
    searchForm.role = "";
    performSearch();
};

const viewUserDetails = (user) => {
    selectedUser.value = user;
};

const approveUser = (user) => {
    currentUser.value = user;
    approvalForm.reason = "";
    showApproveModal.value = true;
};

const rejectUser = (user) => {
    currentUser.value = user;
    rejectionForm.reason = "";
    showRejectModal.value = true;
};

const confirmApproval = () => {
    router.post(
        route("admin.kyc.approve", currentUser.value.id),
        approvalForm,
        {
            onSuccess: () => {
                showApproveModal.value = false;
                selectedUser.value = null;
                currentUser.value = null;

                // Check for flash message
                if (page.props.flash?.success) {
                    displayNotification(page.props.flash.success, "success");
                }
            },
        }
    );
};

const confirmRejection = () => {
    router.post(
        route("admin.kyc.reject", currentUser.value.id),
        rejectionForm,
        {
            onSuccess: () => {
                showRejectModal.value = false;
                selectedUser.value = null;
                currentUser.value = null;

                // Check for flash message
                if (page.props.flash?.success) {
                    displayNotification(page.props.flash.success, "success");
                }
            },
        }
    );
};

const sendKycNotification = (user, notificationType) => {
    // Create unique key for this user + notification type
    const key = `${user.id}_${notificationType}`;

    // Prevent sending if already in progress
    if (sendingNotification.value[key]) {
        displayNotification(
            "Please wait, notification is being sent...",
            "error"
        );
        return;
    }

    // Prevent sending if recently sent (within 30 seconds)
    if (notificationSent.value[key]) {
        const timeSinceSent = Date.now() - notificationSent.value[key];
        const cooldownPeriod = 30000; // 30 seconds in milliseconds

        if (timeSinceSent < cooldownPeriod) {
            const remainingSeconds = Math.ceil(
                (cooldownPeriod - timeSinceSent) / 1000
            );
            displayNotification(
                `Please wait ${remainingSeconds} seconds before sending again`,
                "error"
            );
            return;
        }
    }

    // Set loading state
    sendingNotification.value[key] = true;

    // Post to admin notify route with notification type
    router.post(
        route("admin.kyc.notify", user.id),
        {
            notification_type: notificationType,
        },
        {
            preserveState: true,
            onSuccess: () => {
                // Mark as sent with timestamp
                notificationSent.value[key] = Date.now();

                // Check for flash messages
                if (page.props.flash?.success) {
                    displayNotification(page.props.flash.success, "success");
                } else if (page.props.flash?.error) {
                    displayNotification(page.props.flash.error, "error");
                }

                // Clear loading state
                sendingNotification.value[key] = false;

                // Refresh the listing to pick up any flashes or status
                performSearch();
            },
            onError: () => {
                displayNotification("Failed to send notification", "error");
                // Clear loading state
                sendingNotification.value[key] = false;
            },
        }
    );
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString("en-US", {
        year: "numeric",
        month: "short",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
};

const openImageModal = (imagePath) => {
    selectedImage.value = imagePath;
    showImageModal.value = true;
    imageScale.value = 1;
    imagePosition.value = { x: 0, y: 0 };
};

const closeImageModal = () => {
    showImageModal.value = false;
    selectedImage.value = null;
    imageScale.value = 1;
    imagePosition.value = { x: 0, y: 0 };
    isDragging.value = false;
};

const zoomIn = () => {
    imageScale.value = Math.min(imageScale.value + 0.25, 5);
};

const zoomOut = () => {
    imageScale.value = Math.max(imageScale.value - 0.25, 0.5);
};

const resetZoom = () => {
    imageScale.value = 1;
    imagePosition.value = { x: 0, y: 0 };
};

const handleMouseDown = (e) => {
    if (imageScale.value > 1) {
        isDragging.value = true;
        dragStart.value = {
            x: e.clientX - imagePosition.value.x,
            y: e.clientY - imagePosition.value.y,
        };
    }
};

const handleMouseMove = (e) => {
    if (isDragging.value && imageScale.value > 1) {
        imagePosition.value = {
            x: e.clientX - dragStart.value.x,
            y: e.clientY - dragStart.value.y,
        };
    }
};

const handleMouseUp = () => {
    isDragging.value = false;
};

const handleWheel = (e) => {
    e.preventDefault();
    if (e.deltaY < 0) {
        zoomIn();
    } else {
        zoomOut();
    }
};

const handleKeyDown = (e) => {
    if (e.key === 'Escape' && showImageModal.value) {
        closeImageModal();
    }
};

onMounted(() => {
    window.addEventListener('keydown', handleKeyDown);
});

onUnmounted(() => {
    window.removeEventListener('keydown', handleKeyDown);
});
</script>
