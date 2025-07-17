<script setup>
import { Head, Link } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
</script>

<template>
    <GuestLayout>
        <div class="flex flex-col min-h-screen items-center justify-center gap-4">
            <h1>Hello World</h1>
            <Link href="/privacy" class="btn btn-primary">Privacy Policy</Link>
            <Link href="/terms" class="btn btn-primary">Terms of Service</Link>
            <Link href="/search" class="btn btn-primary">Search Vehicles</Link>
        </div>
    </GuestLayout>
</template>