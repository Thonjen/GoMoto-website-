<script setup>
import { reactive } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link } from '@inertiajs/vue3';
import { useAuthStore } from '@/stores/auth';

const auth = useAuthStore();

const form = reactive({
    first_name: '',
    last_name: '',
    email: '',
    phone: '',
    role_id: '',
    password: '',
    password_confirmation: '',
    errors: {},
    processing: false,
});

const submit = async () => {
    form.processing = true;
    form.errors = {};
    try {
        await auth.register({
            first_name: form.first_name,
            last_name: form.last_name,
            email: form.email,
            phone: form.phone,
            role_id: form.role_id,
            password: form.password,
            password_confirmation: form.password_confirmation,
        });
    } catch (e) {
        form.errors = e?.response?.data?.errors || {};
    } finally {
        form.processing = false;
        form.password = '';
        form.password_confirmation = '';
    }
};

const handlePhoneKeypress = (event) => {
    if (!/[0-9]/.test(event.key)) {
        event.preventDefault();
    }
};
</script>

<template>
    <GuestLayout>
        <Head title="Create Account" />
        
        <div class="flex items-center justify-center min-h-[calc(100vh-120px)] px-4 py-8">
            <div class="w-full max-w-lg">
                <!-- Header Section -->
                <div class="text-center mb-8">
                    <div class="mx-auto w-16 h-16 glass-card rounded-2xl flex items-center justify-center mb-6 shadow-glow">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <h1 class="text-3xl font-bold text-white mb-2">Join GoMOTO</h1>
                    <p class="text-white/80 text-lg">Sign up and start your ride today.</p>
                </div>

                <!-- Registration Card -->
                <div class="glass-card p-8 animate-slide-up">
                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Name Fields -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <InputLabel for="first_name" value="First Name" class="text-white/90 font-medium" />
                                <TextInput
                                    id="first_name"
                                    type="text"
                                    class="glass-input w-full px-4 py-3 rounded-lg bg-white/10 border border-white/20 text-white placeholder-white/60 focus:border-white/40 focus:ring-2 focus:ring-white/20 transition-all"
                                    v-model="form.first_name"
                                    placeholder="Enter your first name"
                                    required
                                    autofocus
                                    autocomplete="given-name"
                                />
                                <InputError class="mt-2 text-red-300" :message="form.errors.first_name" />
                            </div>
                            <div class="space-y-2">
                                <InputLabel for="last_name" value="Last Name" class="text-white/90 font-medium" />
                                <TextInput
                                    id="last_name"
                                    type="text"
                                    class="glass-input w-full px-4 py-3 rounded-lg bg-white/10 border border-white/20 text-white placeholder-white/60 focus:border-white/40 focus:ring-2 focus:ring-white/20 transition-all"
                                    v-model="form.last_name"
                                    placeholder="Enter your last name"
                                    required
                                    autocomplete="family-name"
                                />
                                <InputError class="mt-2 text-red-300" :message="form.errors.last_name" />
                            </div>
                        </div>

                        <!-- Email Field -->
                        <div class="space-y-2">
                            <InputLabel for="email" value="Email Address" class="text-white/90 font-medium" />
                            <TextInput
                                id="email"
                                type="email"
                                class="glass-input w-full px-4 py-3 rounded-lg bg-white/10 border border-white/20 text-white placeholder-white/60 focus:border-white/40 focus:ring-2 focus:ring-white/20 transition-all"
                                v-model="form.email"
                                placeholder="Enter your email address"
                                required
                                autocomplete="username"
                            />
                            <InputError class="mt-2 text-red-300" :message="form.errors.email" />
                        </div>

                        <!-- Phone Field -->
                        <div class="space-y-2">
                            <InputLabel for="phone" value="Phone Number" class="text-white/90 font-medium" />
                            <TextInput
                                id="phone"
                                type="tel"
                                class="glass-input w-full px-4 py-3 rounded-lg bg-white/10 border border-white/20 text-white placeholder-white/60 focus:border-white/40 focus:ring-2 focus:ring-white/20 transition-all"
                                v-model="form.phone"
                                placeholder="Enter your phone number"
                                autocomplete="tel"
                                pattern="[0-9]*"
                                inputmode="numeric"
                                @input="form.phone = $event.target.value.replace(/[^0-9]/g, '')"
                                @keypress="handlePhoneKeypress"
                            />
                            <InputError class="mt-2 text-red-300" :message="form.errors.phone" />
                        </div>

                        <!-- Role Selection -->
                        <div class="space-y-3">
                            <InputLabel for="role_id" value="I want to..." class="text-white/90 font-medium" />
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <label class="relative group cursor-pointer">
                                    <input
                                        v-model="form.role_id"
                                        type="radio"
                                        value="2"
                                        class="sr-only"
                                    />
                                    <div :class="[
                                        'relative rounded-lg border-2 p-6 text-center transition-all duration-200 transform hover:scale-105 backdrop-blur-sm',
                                        form.role_id === '2' 
                                            ? 'border-white/60 bg-white/20 shadow-glow ring-2 ring-white/30' 
                                            : 'border-white/30 bg-white/10 hover:border-white/50 hover:bg-white/15'
                                    ]">
                                        <div class="flex flex-col items-center space-y-3">
                                            <div :class="[
                                                'w-12 h-12 rounded-lg flex items-center justify-center transition-colors',
                                                form.role_id === '2' ? 'bg-white/30' : 'bg-white/20'
                                            ]">
                                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                                                </svg>
                                            </div>
                                            <div>
                                                <h3 class="font-semibold text-white mb-1">Rent Vehicles</h3>
                                                <p class="text-sm text-white/80">Find and book amazing rides</p>
                                            </div>
                                        </div>
                                    </div>
                                </label>

                                <label class="relative group cursor-pointer">
                                    <input
                                        v-model="form.role_id"
                                        type="radio"
                                        value="3"
                                        class="sr-only"
                                    />
                                    <div :class="[
                                        'relative rounded-lg border-2 p-6 text-center transition-all duration-200 transform hover:scale-105 backdrop-blur-sm',
                                        form.role_id === '3' 
                                            ? 'border-white/60 bg-white/20 shadow-glow ring-2 ring-white/30' 
                                            : 'border-white/30 bg-white/10 hover:border-white/50 hover:bg-white/15'
                                    ]">
                                        <div class="flex flex-col items-center space-y-3">
                                            <div :class="[
                                                'w-12 h-12 rounded-lg flex items-center justify-center transition-colors',
                                                form.role_id === '3' ? 'bg-white/30' : 'bg-white/20'
                                            ]">
                                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                                </svg>
                                            </div>
                                            <div>
                                                <h3 class="font-semibold text-white mb-1">List My Vehicles</h3>
                                                <p class="text-sm text-white/80">Share and earn from your car</p>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                            <InputError class="mt-2 text-red-300" :message="form.errors.role_id" />
                        </div>

                        <!-- Password Fields -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <InputLabel for="password" value="Password" class="text-white/90 font-medium" />
                                <TextInput
                                    id="password"
                                    type="password"
                                    class="glass-input w-full px-4 py-3 rounded-lg bg-white/10 border border-white/20 text-white placeholder-white/60 focus:border-white/40 focus:ring-2 focus:ring-white/20 transition-all"
                                    v-model="form.password"
                                    placeholder="Create a strong password"
                                    required
                                    autocomplete="new-password"
                                />
                                <InputError class="mt-2 text-red-300" :message="form.errors.password" />
                            </div>
                            <div class="space-y-2">
                                <InputLabel for="password_confirmation" value="Confirm Password" class="text-white/90 font-medium" />
                                <TextInput
                                    id="password_confirmation"
                                    type="password"
                                    class="glass-input w-full px-4 py-3 rounded-lg bg-white/10 border border-white/20 text-white placeholder-white/60 focus:border-white/40 focus:ring-2 focus:ring-white/20 transition-all"
                                    v-model="form.password_confirmation"
                                    placeholder="Repeat your password"
                                    required
                                    autocomplete="new-password"
                                />
                                <InputError class="mt-2 text-red-300" :message="form.errors.password_confirmation" />
                            </div>
                        </div>

                        <!-- Terms Notice -->
                        <div class="glass-card p-4 border border-white/20">
                            <p class="text-sm text-white/80 text-center">
                                By creating an account, you agree to our 
                                <Link href="/terms" class="text-white hover:text-white/80 font-medium underline transition-colors">Terms of Service</Link> 
                                and 
                                <Link href="/privacy" class="text-white hover:text-white/80 font-medium underline transition-colors">Privacy Policy</Link>
                            </p>
                        </div>

                        <!-- Submit Button -->
                        <PrimaryButton
                            :class="{ 'opacity-75 cursor-not-allowed': form.processing }"
                            :disabled="form.processing"
                            class="btn-primary w-full py-3 text-lg font-semibold shadow-glow"
                        >
                            <svg v-if="form.processing" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            {{ form.processing ? 'Creating Account...' : 'Create Account' }}
                        </PrimaryButton>

                        <!-- Login Link -->
                        <div class="text-center pt-4 border-t border-white/10">
                            <p class="text-white/80">
                                Already have an account? 
                                <Link
                                    :href="route('login')"
                                    class="text-white hover:text-white/80 font-medium transition-colors ml-1"
                                >
                                    Sign in here
                                </Link>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>
