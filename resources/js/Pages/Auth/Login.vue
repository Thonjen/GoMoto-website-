<script setup>
import { reactive } from 'vue';
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link } from '@inertiajs/vue3';
import { useAuthStore } from '@/stores/auth';

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const auth = useAuthStore();

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
    <div class="flex items-center justify-center min-h-[calc(100vh-150px)] px-4">
      <div class="w-full max-w-md bg-white p-8 rounded-lg shadow-lg">
        <Head title="Log in" />

        <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="email" value="Email" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                />

            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Password" />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4 block">
                <label class="flex items-center">
                    <Checkbox name="remember" v-model:checked="form.remember" />
                    <span class="ms-2 text-sm text-gray-600"
                        >Remember me</span
                    >
                </label>
            </div>
                <InputError class="mt-2" :message="form.errors.email" />
                <InputError class="mt-2" :message="form.errors.password" />

            <div class="mt-4 flex items-center justify-end">
                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                >
                    Forgot your password?
                </Link>

                <PrimaryButton
                    class="ms-4"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Log in
                </PrimaryButton>
            </div>
        </form>
        </div>
        </div>
    </GuestLayout>
</template>
