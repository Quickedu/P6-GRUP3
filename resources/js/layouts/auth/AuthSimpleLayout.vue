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
        <div class="form-side flex flex-1 items-center justify-center px-6 py-10">
            <div class="w-full max-w-md flex flex-col gap-6">

                <!-- Logo — outside card-->
                <Link
                    :href="home()"
                    class="flex items-center justify-center gap-4 animate-fade-slide-up"
                >
                    <AppLogoIcon class="h-23 w-26 fill-current text-pmf-primary" />
                    <span class="text-5xl font-bold tracking-wide text-pmf-primary">PMF</span>
                </Link>

                <!-- Form Card -->
                <div class="bg-white rounded-2xl shadow-lg px-8 py-4 animate-fade-slide-up">
                    <div class="flex flex-col gap-6">

                        <!-- Title + description-->
                        <div class="flex flex-col gap-1.5 text-center">
                            <!-- <h1 class="text-2xl font-bold text-gray-900">{{ title || null }}</h1> -->
                            <!-- <p class="text-sm text-muted-foreground leading-relaxed">{{ description }}</p> -->
                        </div>

                        <slot />
                    </div>
                </div>

            </div>
        </div>
    </div>
</template>