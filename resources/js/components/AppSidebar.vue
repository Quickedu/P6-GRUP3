<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { CalendarPlus, LayoutGrid } from 'lucide-vue-next';
import AppLogo from '@/components/AppLogo.vue';
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
} from '@/components/ui/sidebar';
import { dashboard, novaCita, patientDashboard, patientsList } from '@/routes';
import type { NavItem } from '@/types';
import { Microscope, Users, BookUser, History, Cross } from 'lucide-vue-next';
import { Settings } from 'lucide-vue-next';

const page = usePage();
const user = computed(() => page.props.auth?.user);

const isPatient = computed(() => !user.value?.role);
const isAdmin = computed(() => user.value?.role === 'admin');
const isDoctor = computed(() => user.value?.role === 'doctor');
const isSecretary = computed(() => user.value?.role === 'secretary');
const isWorker = computed(() => user.value?.role === 'doctor' || user.value?.role === 'secretary');

const AdminNavItems: NavItem[] = [
    {
        title: 'Inici',
        href: dashboard(),
        icon: LayoutGrid,
    },
    {
        title: 'Proves diagnostic',
        href: '/',
        icon: Microscope,
    },
    {
        title: 'Gestió de personal',
        href: '/',
        icon: BookUser,
    },
    {
        title: 'Nova Cita',
        href: novaCita(),
        icon: CalendarPlus,
    },
    {
        title: 'Pacients',
        href: patientsList(),
        icon: Users,
    },
    {
        title: 'Necessitats dels pacients',
        href: '/',
        icon: Cross,
    },
    {
        title: 'Historial de cites',
        href: '/',
        icon: History,
    },
];

const PatientNavItems: NavItem[] = [
    {
        title: 'Inici',
        href: patientDashboard(),
        icon: LayoutGrid,
    },
    {
        title: 'Historial de cites',
        href: '/',
        icon: History,
    },
];

const DoctorNavItems: NavItem[] = [
    {
        title: 'Inici',
        href: dashboard(),
        icon: LayoutGrid,
    },
    {
        title: 'Pacients',
        href: patientsList(),
        icon: Users,
    },
    {
        title: 'Necessitats dels pacients',
        href: '/',
        icon: Cross,
    },
    {
        title: 'Historial de cites',
        href: '/',
        icon: History,
    },
];
const SecretaryNavItems: NavItem[] = [
    {
        title: 'Inici',
        href: dashboard(),
        icon: LayoutGrid,
    },
    {
        title: 'Nova Cita',
        href: novaCita(),
        icon: CalendarPlus,
    },
    {
        title: 'Pacients',
        href: patientsList(),
        icon: Users,
    },
    {
        title: 'Necessitats dels pacients',
        href: '/',
        icon: Cross,
    },
]

const footerNavItems: NavItem[] = [
    {
        title: 'Configuració',
        href: '/settings',
        icon: Settings,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset" class="pmf-sidebar">
        <SidebarHeader class="pmf-sidebar-header">
            <Link :href="dashboard()" class="block w-full">
                <AppLogo />
            </Link>
        </SidebarHeader>

        <SidebarContent class="pmf-sidebar-content">
            <!-- <NavMain :items="mainNavItems" /> -->
            <NavMain label="Administració" v-if="isAdmin" :items="AdminNavItems" />
            <NavMain label="Doctor" v-if="isDoctor" :items="DoctorNavItems" />
            <NavMain label="Pacients" v-if="isPatient" :items="PatientNavItems" />
            <NavMain label="Secretary" v-if="isSecretary" :items="SecretaryNavItems" />
        </SidebarContent>

        <SidebarFooter class="pmf-sidebar-footer">
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
</template>