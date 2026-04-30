<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import { dashboard, home} from '@/routes';

const page = usePage();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Pàgina Inici',
                href: home(),
            },
            {
                title: 'Crear nova cita',
                href: dashboard(),
            },
        ],
    },
});

const props = defineProps<{
    
}>();

</script>

<template>
    <Head title="Nova cita" />

    <!-- <div class="min-h-svh overflow-y-hidden">
        <div
            class="mt-6 flex h-full flex-1 flex-col gap-4 px-4 pb-8 sm:mt-8 sm:px-6 lg:mt-10 lg:px-8"
        >
            fgsdfgsdfgsd
        </div>
    </div> -->
</template>
