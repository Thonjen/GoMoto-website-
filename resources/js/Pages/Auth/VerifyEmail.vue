<script setup>
import { computed, ref, onMounted } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';

const page = usePage();
const props = defineProps({
    status: {
        type: String,
    },
    email: {
        type: String,
        default: ''
    }
});

const form = useForm({
    email: props.email || ''
});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(
    () => props.status === 'verification-link-sent',
);

const registrationSuccessful = computed(
    () => props.status === 'registration-successful',
);

const emailNotVerified = computed(
    () => props.status === 'email-not-verified',
);

// Check if user is authenticated
const isAuthenticated = computed(() => {
    return page.props.auth && page.props.auth.user;
});

// Auto-send verification email if this is after registration and email is provided
onMounted(() => {
    if (registrationSuccessful.value && props.email && !verificationLinkSent.value) {
        // Automatically send verification email after registration
        form.post(route('verification.send'));
    }
});
</script>

<template>
    <GuestLayout>
        <Head title="Email Verification" />

        <div class="flex items-center justify-center min-h-[calc(100vh-120px)] px-4">
            <div class="w-full max-w-md">
                <!-- Header Section -->
                <div class="text-center mb-8">
                    <div class="mx-auto w-16 h-16 glass-card rounded-2xl flex items-center justify-center mb-6 shadow-glow">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 7.89a2 2 0 002.83 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    
                    <h1 class="text-3xl font-bold text-white mb-2">
                        <span v-if="registrationSuccessful">Account Created!</span>
                        <span v-else-if="emailNotVerified">Verification Required</span>
                        <span v-else>Verify Your Email</span>
                    </h1>
                    <p class="text-white/80 text-lg">Almost there! Just one more step</p>
                </div>

                <!-- Verification Card -->
                <div class="glass-card p-8 animate-slide-up">
                    <!-- Main Message -->
                    <div class="text-center mb-6">
                        <p class="text-white/90 leading-relaxed" v-if="registrationSuccessful">
                            Welcome to GoMOTO! We've sent a verification link to 
                            <span class="font-semibold text-white">{{ props.email }}</span>. 
                            Please check your inbox and click the link to activate your account.
                        </p>
                        <p class="text-white/90 leading-relaxed" v-else-if="emailNotVerified">
                            Please verify your email address before logging in. Check your inbox for the verification link.
                        </p>
                        <p class="text-white/90 leading-relaxed" v-else-if="props.email">
                            We need to verify your email address <span class="font-semibold text-white">{{ props.email }}</span>. 
                            Please check your inbox for the verification link, or click below to send a new one.
                        </p>
                        <p class="text-white/90 leading-relaxed" v-else>
                            Thanks for signing up! Before getting started, could you verify your
                            email address by clicking on the link we just emailed to you? If you
                            didn't receive the email, we will gladly send you another.
                        </p>
                    </div>

                    <!-- Success Message for Resent Email -->
                    <div
                        v-if="verificationLinkSent"
                        class="mb-6 p-4 glass-card border border-green-400/30 rounded-lg"
                    >
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-green-300 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <p class="text-sm font-medium text-green-300">
                                A new verification link has been sent to your email address.
                            </p>
                        </div>
                    </div>

                    <!-- Action Form -->
                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Email input for non-authenticated users who don't have email provided -->
                        <div v-if="!isAuthenticated && !props.email" class="space-y-2">
                            <InputLabel for="email" value="Email Address" class="text-white/90 font-medium" />
                            <TextInput
                                id="email"
                                type="email"
                                class="glass-input w-full px-4 py-3 rounded-lg bg-white/10 border border-white/20 text-white placeholder-white/60 focus:border-white/40 focus:ring-2 focus:ring-white/20 transition-all"
                                v-model="form.email"
                                placeholder="Enter your email address"
                                required
                            />
                            <InputError class="mt-2 text-red-300" :message="form.errors.email" />
                            <p class="text-sm text-white/70">
                                Enter the email address you used to register.
                            </p>
                        </div>

                        <!-- Show email when it's provided but not authenticated -->
                        <div v-if="!isAuthenticated && props.email" class="glass-card p-4 border border-white/20 rounded-lg mb-4">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-white/80 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-white/80">Verification email for:</p>
                                    <p class="text-sm text-white font-semibold">{{ props.email }}</p>
                                </div>
                            </div>
                        </div>

                        <PrimaryButton
                            :class="{ 'opacity-75 cursor-not-allowed': form.processing }"
                            :disabled="form.processing"
                            class="btn-primary w-full py-3 text-lg font-semibold shadow-glow"
                        >
                            <svg v-if="form.processing" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            {{ 
                                form.processing ? 'Sending...' :
                                isAuthenticated ? 'Resend Verification Email' : 
                                props.email ? 'Resend Verification Email' : 
                                'Send Verification Email' 
                            }}
                        </PrimaryButton>

                        <div class="text-center pt-4 border-t border-white/10">
                            <Link
                                :href="route('login')"
                                class="inline-flex items-center text-sm text-white/80 hover:text-white transition-colors"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Back to Login
                            </Link>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>
