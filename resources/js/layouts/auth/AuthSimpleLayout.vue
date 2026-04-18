<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { home } from '@/routes';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div class="flex min-h-svh w-full">

        <!-- LEFT: Photo (hidden on mobile) -->
        <div class="hidden md:block md:w-1/2 overflow-hidden">
            <img
                src="/images/grup2.jpeg"
                alt="Team photo"
                class="w-full h-full object-cover object-center"
            />
        </div>

        <!-- RIGHT: Form side -->
        <div class="flex flex-1 items-center justify-center bg-gray-50 px-6 py-10">
            <div class="w-full max-w-md flex flex-col gap-3">
                <!-- Logo and Title - Outside form -->
                <Link :href="home()" class="flex flex-col items-center gap-5">
                    <div class="flex flex-row h-50 w-50 items-center justify-center">
                        <AppLogoIcon class="h-30 w-40 fill-current text-pmf-primary" />
                        <span class="text-5xl font-bold tracking-wide text-pmf-primary">PMF</span>
                    </div>
                </Link>

                <!-- Form Card -->
                <div class="bg-white rounded-2xl shadow-lg px-8 py-12">
                    <div class="flex flex-col gap-6">
                        <slot />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>