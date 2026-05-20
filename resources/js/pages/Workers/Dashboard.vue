<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import AdminDashboard from '@/components/Dashboard/AdminDashboard.vue';
import DoctorDashboard from '@/components/Dashboard/DoctorDashboard.vue';
import SecretaryDashboard from '@/components/Dashboard/SecretaryDashboard.vue';
import { dashboard } from '@/routes';

interface ScheduledDate {
    id: number;
    patient_id: number;
    worker_id: number;
    test_id: number;
    date_time: string;
    time: number;
    estat: string;
    urgencia: string;
    description: string;
    patient: {
        id: number;
        name: string;
        nts: string;
    };
    worker: {
        id: number;
        user: {
            id: number;
            name: string;
        };
    };
    test: {
        id: number;
        name: string;
    };
}

interface DoctorOption {
    id: number;
    name: string;
}

interface DateRecord {
    id: number;
    date_time: string;
    estat: string;
    urgencia: string;
    test: { name: string };
}

defineProps<{
    dates?: ScheduledDate[];
    doctors?: DoctorOption[];
    doctorDates?: DateRecord[];
    stats?: {
        totalPatients: number;
        totalWorkers: number;
        totalReports: number;
        totalNeeds: number;
    };
    chartDays?: string[];
    chartReports?: number[];
    chartPatients?: number[];
}>();

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
const user = page.props.auth.user;

const isSecretry = user.role === 'secretary';
const isDoctor = user.role === 'doctor' || user.role === 'technician';
const isAdmin = user.role === 'admin';

//check user role using session and make 3 v-if components depending on the role
</script>

<template>
    <Head title="Escriptori" />
    <div
        class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
    >
        <SecretaryDashboard
            v-if="isSecretry"
            :dates="dates"
            :doctors="doctors"
        />
        <DoctorDashboard v-if="isDoctor" :doctorDates="doctorDates" />
        <AdminDashboard
            v-if="isAdmin"
            :stats="stats || {}"
            :chartDays="chartDays || []"
            :chartReports="chartReports || []"
            :chartPatients="chartPatients || []"
        />
    </div>
</template>
