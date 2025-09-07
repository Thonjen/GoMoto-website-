<script setup>
import { reactive, computed } from 'vue';
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link } from '@inertiajs/vue3';
import { useAuthStore } from '@/stores/auth';

const props = defineProps({
    canResetPassword: Boolean,
    status: String,
});

const auth = useAuthStore();

// Compute friendly status messages
const statusMessage = computed(() => {
    switch(props.status) {
        case 'email-already-verified':
            return 'Your email is already verified! Please log in to continue.';
        case 'verification-link-sent':
            return 'A new verification link has been sent to your email address.';
        case 'email-not-verified':
            return 'Please verify your email address before logging in.';
        case 'registration-successful':
            return 'Registration successful! Please check your email to verify your account.';
        default:
            return props.status;
    }
});

const form = reactive({
    email: '',
    password: '',
    remember: false,
    errors: {},
    processing: false,
});

const submit = async () => {
    form.processing = true;
    form.errors = {};
    try {
        await auth.login({
            email: form.email,
            password: form.password,
            remember: form.remember,
        });
    } catch (e) {
        form.errors = e?.response?.data?.errors || {};
    } finally {
        form.processing = false;
        form.password = '';
    }
};
</script>

<template>
    <GuestLayout>
        <Head title="Sign In - GoMOTO" />
        
        <div class="flex items-center justify-center min-h-[calc(100vh-120px)] px-4 py-8">
            <div class="w-full max-w-md">
                <!-- Header Section -->
                <div class="text-center mb-8">
                    <div class="mx-auto w-16 h-16 glass-card rounded-2xl flex items-center justify-center mb-6 shadow-glow">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                        </svg>
                    </div>
                    <h1 class="text-3xl font-bold text-white mb-2">Welcome Back</h1>
                    <p class="text-white/80 text-lg">Sign in to your GoMOTO account</p>
                </div>

                <!-- Login Card -->
                <div class="glass-card p-8 animate-slide-up">
                    <div v-if="statusMessage" class="mb-6 p-4 glass-card border border-green-400/30 rounded-lg">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-green-300 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <p class="text-green-300 text-sm font-medium">{{ statusMessage }}</p>
                        </div>
                    </div>

                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Email Field -->
                        <div>
                            <InputLabel for="email" value="Email Address" class="text-white/90 font-medium mb-2" />
                            <TextInput
                                id="email"
                                type="email"
                                class="glass-input w-full px-4 py-3 rounded-lg bg-white/10 border border-white/20 text-white placeholder-white/60 focus:border-white/40 focus:ring-2 focus:ring-white/20 transition-all"
                                v-model="form.email"
                                required
                                autofocus
                                autocomplete="username"
                                placeholder="Enter your email"
                            />
                            <InputError class="mt-2 text-red-300" :message="form.errors.email" />
                        </div>

                        <!-- Password Field -->
                        <div>
                            <InputLabel for="password" value="Password" class="text-white/90 font-medium mb-2" />
                            <TextInput
                                id="password"
                                type="password"
                                class="glass-input w-full px-4 py-3 rounded-lg bg-white/10 border border-white/20 text-white placeholder-white/60 focus:border-white/40 focus:ring-2 focus:ring-white/20 transition-all"
                                v-model="form.password"
                                required
                                autocomplete="current-password"
                                placeholder="Enter your password"
                            />
                            <InputError class="mt-2 text-red-300" :message="form.errors.password" />
                        </div>

                        <!-- Remember Me -->
                        <div class="flex items-center justify-between">
                            <label class="flex items-center">
                                <Checkbox
                                    name="remember"
                                    v-model:checked="form.remember"
                                    class="rounded border-white/30 text-white focus:ring-white/20"
                                />
                                <span class="ms-2 text-sm text-white/80">Remember me</span>
                            </label>

                            <Link
                                v-if="canResetPassword"
                                :href="route('password.request')"
                                class="text-sm text-white/80 hover:text-white transition-colors"
                            >
                                Forgot password?
                            </Link>
                        </div>

                        <!-- Submit Button -->
                        <PrimaryButton
                            class="btn-primary w-full py-3 text-lg font-semibold shadow-glow"
                            :class="{ 'opacity-75 cursor-not-allowed': form.processing }"
                            :disabled="form.processing"
                        >
                            <svg v-if="form.processing" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            {{ form.processing ? 'Signing In...' : 'Sign In' }}
                        </PrimaryButton>

                        <!-- Register Link -->
                        <div class="text-center pt-4 border-t border-white/10">
                            <p class="text-white/80">
                                Don't have an account?
                                <Link :href="route('register')" class="text-white font-medium hover:text-white/80 transition-colors ml-1">
                                    Create Account
                                </Link>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>
