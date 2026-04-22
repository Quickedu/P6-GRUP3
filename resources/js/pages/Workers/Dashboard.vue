<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3'
import SecretaryDashboard from '@/components/Dashboard/SecretaryDashboard.vue';
import DoctorDashboard from '@/components/Dashboard/DoctorDashboard.vue';
import AdminDashboard from '@/components/Dashboard/AdminDashboard.vue';
import { dashboard } from '@/routes';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Escriptori',
                href: dashboard(),
            },
        ],
    },
});

const page = usePage();
const user = page.props.auth.user

const isSecretry = user.role === 'secretary'
const isDoctor = user.role === 'doctor'
const isAdmin = user.role === 'admin'

//check user role using session and make 3 v-if components depending on the role
</script>

<template>
    <Head title="Escriptori" />
    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
        <SecretaryDashboard v-if="isSecretry"/>
        <DoctorDashboard v-if="isDoctor"/>
        <AdminDashboard v-if="isAdmin"/>
    </div>
</template>
