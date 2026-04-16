<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3'
import { patientDashboard } from '@/routes';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Escriptori',
                href: patientDashboard(),
            },
        ],
    },
});

const page = usePage();

const dates = page.props.dates

console.log(dates)
</script>

<template>
    <Head title="Escriptori" />
    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
      Escriptori Pacient
    </div>
</template>
