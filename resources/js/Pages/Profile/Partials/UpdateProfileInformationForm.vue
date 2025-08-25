<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import TextareaInput from '@/Components/TextareaInput.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const page = usePage();
// Use the enhanced user data from the ProfileController that includes profile_picture_url
const user = computed(() => page.props.user || page.props.auth.user);
const profilePicturePreview = ref(null);

const form = useForm({
    first_name: user.value.first_name,
    last_name: user.value.last_name,
    email: user.value.email,
    bio: user.value.bio || '',
    profile_picture: null,
});

const handleProfilePictureChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        form.profile_picture = file;
        
        // Create preview
        const reader = new FileReader();
        reader.onload = (e) => {
            profilePicturePreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};

const submitForm = () => {
    // Use POST route for file uploads, PATCH for regular updates
    if (form.profile_picture) {
        form.post(route('profile.update.post'), {
            forceFormData: true,
            preserveScroll: true,
            onSuccess: () => {
                profilePicturePreview.value = null;
            },
        });
    } else {
        form.patch(route('profile.update'), {
            preserveScroll: true,
        });
    }
};

const deleteProfilePicture = () => {
    if (profilePicturePreview.value) {
        // If there's only a preview (not yet saved), just clear it
        profilePicturePreview.value = null;
        form.profile_picture = null;
        // Reset the file input
        const fileInput = document.querySelector('input[type="file"]');
        if (fileInput) fileInput.value = '';
    } else if (user.value.profile_picture) {
        // If there's a saved profile picture, delete it from server
        if (confirm('Are you sure you want to delete your profile picture?')) {
            form.delete(route('profile.picture.delete'), {
                preserveScroll: true,
                onSuccess: () => {
                    profilePicturePreview.value = null;
                },
            });
        }
    }
};
</script>

<template>
    <section class="max-w-full">
        <!-- Header with better typography -->
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-2">
                Profile Information
            </h2>
            <p class="text-gray-600 leading-relaxed">
                Manage your personal information and customize your profile to help others get to know you better.
            </p>
        </div>

        <form
            @submit.prevent="submitForm"
            class="max-w-full grid grid-cols-1 lg:grid-cols-5 gap-8"
        >
            <!-- Left Column - Profile & Basic Info -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Profile Picture Section with enhanced design -->
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl p-6 border border-blue-100">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Profile Picture</h3>
                    
                    <div class="flex flex-col items-center space-y-4">
                        <!-- Enhanced Profile Image -->
                        <div class="relative">
                            <img 
                                :src="profilePicturePreview || user.profile_picture_url" 
                                :alt="user.name"
                                class="h-24 w-24 rounded-full object-cover border-4 border-white shadow-lg ring-2 ring-blue-100"
                            />
                            <div class="absolute -bottom-1 -right-1 bg-blue-500 rounded-full p-1.5 shadow-md">
                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path>
                                </svg>
                            </div>
                        </div>
                        
                        <!-- Upload Controls with modern styling -->
                        <div class="flex flex-col items-center space-y-3 w-full">
                            <label class="cursor-pointer group">
                                <input 
                                    type="file" 
                                    @change="handleProfilePictureChange"
                                    accept="image/*"
                                    class="sr-only"
                                />
                                <div class="inline-flex items-center px-6 py-3 bg-white border-2 border-blue-200 
                                           rounded-xl font-medium text-sm text-blue-700 hover:bg-blue-50 
                                           hover:border-blue-300 transition-all duration-200 shadow-sm
                                           group-hover:shadow-md group-focus:ring-2 group-focus:ring-blue-500 
                                           group-focus:ring-offset-2">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                    </svg>
                                    Choose Photo
                                </div>
                            </label>
                            
                            <button 
                                v-if="user.profile_picture || profilePicturePreview"
                                @click.prevent="deleteProfilePicture"
                                type="button"
                                class="text-sm text-red-600 hover:text-red-700 font-medium 
                                       hover:underline transition-colors duration-200"
                            >
                                Remove Photo
                            </button>
                        </div>
                        
                        <p class="text-center text-xs text-gray-500 leading-relaxed">
                            JPG, GIF or PNG. Max 2MB.<br>
                            Recommended: Square image, at least 200x200px
                        </p>
                        
                        <InputError class="mt-2 text-center" :message="form.errors.profile_picture" />
                    </div>
                </div>
                <!-- Basic Information Card -->
                <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">Basic Information</h3>
                    
                    <div class="space-y-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <InputLabel for="first_name" value="First Name" class="text-sm font-medium text-gray-700 mb-2" />
                                <TextInput
                                    id="first_name"
                                    type="text"
                                    class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm 
                                           focus:border-blue-500 focus:ring-blue-500 transition-colors duration-200"
                                    v-model="form.first_name"
                                    required
                                    autofocus
                                    autocomplete="given-name"
                                />
                                <InputError class="mt-2" :message="form.errors.first_name" />
                            </div>

                            <div>
                                <InputLabel for="last_name" value="Last Name" class="text-sm font-medium text-gray-700 mb-2" />
                                <TextInput
                                    id="last_name"
                                    type="text"
                                    class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm 
                                           focus:border-blue-500 focus:ring-blue-500 transition-colors duration-200"
                                    v-model="form.last_name"
                                    required
                                    autocomplete="family-name"
                                />
                                <InputError class="mt-2" :message="form.errors.last_name" />
                            </div>
                        </div>

                        <div>
                            <InputLabel for="email" value="Email Address" class="text-sm font-medium text-gray-700 mb-2" />
                            <TextInput
                                id="email"
                                type="email"
                                class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm 
                                       focus:border-blue-500 focus:ring-blue-500 transition-colors duration-200"
                                v-model="form.email"
                                required
                                autocomplete="username"
                            />
                            <InputError class="mt-2" :message="form.errors.email" />
                        </div>

                        <!-- Email Verification Notice -->
                        <div v-if="mustVerifyEmail && user.email_verified_at === null" 
                             class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-amber-500 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                <div>
                                    <p class="text-sm text-amber-800 font-medium">Email verification required</p>
                                    <p class="text-sm text-amber-700 mt-1">
                                        Please verify your email address to access all features.
                                        <Link
                                            :href="route('verification.send')"
                                            method="post"
                                            as="button"
                                            class="font-medium underline hover:no-underline transition-all duration-200"
                                        >
                                            Send verification email
                                        </Link>
                                    </p>
                                    
                                    <div
                                        v-show="status === 'verification-link-sent'"
                                        class="mt-2 text-sm font-medium text-green-700 bg-green-50 rounded-lg px-3 py-2"
                                    >
                                        ✓ Verification email sent successfully!
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Bio Section -->
            <div class="lg:col-span-3">
                <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm h-full">
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">About You</h3>
                        <p class="text-sm text-gray-600">
                            Share a bit about yourself. This helps others get to know you better.
                        </p>
                    </div>
                    
                    <div class="h-full flex flex-col">
                        <InputLabel for="bio" value="Bio" class="text-sm font-medium text-gray-700 mb-3" />
                        <TextareaInput
                            id="bio"
                            v-model="form.bio"
                            class="flex-1 min-h-64 rounded-xl border-gray-300 shadow-sm 
                                   focus:border-blue-500 focus:ring-blue-500 transition-colors duration-200
                                   resize-none"
                            placeholder="Tell others about yourself, your interests, profession, or anything you'd like to share..."
                        />
                        <InputError class="mt-2" :message="form.errors.bio" />
                        <div class="flex justify-between items-center mt-3 text-xs text-gray-500">
                            <span>Express yourself freely - this is your space to shine!</span>
                            <span>{{ form.bio ? form.bio.length : 0 }}/1000</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Full-width Actions -->
            <div class="lg:col-span-5 flex items-center justify-between pt-6 border-t border-gray-200">
                <div class="flex items-center gap-4">
                    <PrimaryButton 
                        :disabled="form.processing"
                        class="bg-blue-600 hover:bg-blue-700 focus:ring-blue-500 px-8 py-3 rounded-xl
                               font-semibold text-white shadow-sm hover:shadow-md transition-all duration-200
                               disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <svg v-if="form.processing" class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        {{ form.processing ? 'Saving...' : 'Save Changes' }}
                    </PrimaryButton>
                    
                    <Transition
                        enter-active-class="transition ease-out duration-300"
                        enter-from-class="opacity-0 transform scale-95"
                        enter-to-class="opacity-100 transform scale-100"
                        leave-active-class="transition ease-in duration-200"
                        leave-from-class="opacity-100 transform scale-100"
                        leave-to-class="opacity-0 transform scale-95"
                    >
                        <div v-if="form.recentlySuccessful" 
                             class="flex items-center text-green-700 bg-green-50 px-4 py-2 rounded-xl border border-green-200">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="font-medium text-sm">Profile updated successfully!</span>
                        </div>
                    </Transition>
                </div>
                
                <p class="text-xs text-gray-500">
                    Last updated: {{ new Date().toLocaleDateString() }}
                </p>
            </div>
        </form>
    </section>
</template>
