<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    email: {
        type: String,
        required: true,
    },
    token: {
        type: String,
        required: true,
    },
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Reset Password - GoMOTO" />

        <div class="flex items-center justify-center min-h-[calc(100vh-120px)] px-4 py-8">
            <div class="w-full max-w-md">
                <!-- Header Section -->
                <div class="text-center mb-8">
                    <div class="mx-auto w-16 h-16 glass-card rounded-2xl flex items-center justify-center mb-6 shadow-glow">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                        </svg>
                    </div>
                    <h1 class="text-3xl font-bold text-white mb-2">Reset Password</h1>
                    <p class="text-white/80 text-lg">Create a new secure password</p>
                </div>

                <!-- Reset Form Card -->
                <div class="glass-card p-8 animate-slide-up">
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
                                placeholder="Your email address"
                                readonly
                            />
                            <InputError class="mt-2 text-red-300" :message="form.errors.email" />
                        </div>

                        <!-- New Password Field -->
                        <div>
                            <InputLabel for="password" value="New Password" class="text-white/90 font-medium mb-2" />
                            <TextInput
                                id="password"
                                type="password"
                                class="glass-input w-full px-4 py-3 rounded-lg bg-white/10 border border-white/20 text-white placeholder-white/60 focus:border-white/40 focus:ring-2 focus:ring-white/20 transition-all"
                                v-model="form.password"
                                required
                                autocomplete="new-password"
                                placeholder="Enter your new password"
                            />
                            <InputError class="mt-2 text-red-300" :message="form.errors.password" />
                        </div>

                        <!-- Confirm Password Field -->
                        <div>
                            <InputLabel for="password_confirmation" value="Confirm New Password" class="text-white/90 font-medium mb-2" />
                            <TextInput
                                id="password_confirmation"
                                type="password"
                                class="glass-input w-full px-4 py-3 rounded-lg bg-white/10 border border-white/20 text-white placeholder-white/60 focus:border-white/40 focus:ring-2 focus:ring-white/20 transition-all"
                                v-model="form.password_confirmation"
                                required
                                autocomplete="new-password"
                                placeholder="Confirm your new password"
                            />
                            <InputError class="mt-2 text-red-300" :message="form.errors.password_confirmation" />
                        </div>

                        <!-- Password Requirements -->
                        <div class="glass-card p-4 border border-white/20 rounded-lg">
                            <h4 class="text-white/90 font-medium mb-2 text-sm">Password Requirements:</h4>
                            <ul class="text-white/70 text-sm space-y-1">
                                <li class="flex items-center">
                                    <svg class="w-3 h-3 mr-2 text-white/60" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    At least 8 characters long
                                </li>
                                <li class="flex items-center">
                                    <svg class="w-3 h-3 mr-2 text-white/60" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Contains uppercase and lowercase letters
                                </li>
                                <li class="flex items-center">
                                    <svg class="w-3 h-3 mr-2 text-white/60" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Includes at least one number
                                </li>
                            </ul>
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
                            {{ form.processing ? 'Resetting Password...' : 'Reset Password' }}
                        </PrimaryButton>

                        <!-- Back to Login -->
                        <div class="text-center pt-4 border-t border-white/10">
                            <Link href="/login" class="inline-flex items-center text-sm text-white/80 hover:text-white transition-colors">
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
