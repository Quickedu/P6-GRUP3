<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { BookOpen, FolderGit2, LayoutGrid, CalendarPlus } from 'lucide-vue-next';
import AppLogo from '@/components/AppLogo.vue';
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import { novaCita } from '@/routes';
import type { NavItem } from '@/types';
import { Microscope, Users, BookUser, History, Cross } from 'lucide-vue-next';

const page = usePage();

const user = computed(() => page.props.auth?.user);

const isPatient = computed(() => !user.value?.role);
const isAdmin = computed(() => user.value?.role === 'admin');
const isWorker = computed(() => user.value?.role === 'doctor' || user.value?.role === 'secretary');

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },
];

const AdminNavItems: NavItem[] = [
    {
        title: 'Proves diagnostic',
        href: '/',
        icon: Microscope,
    },
    {
        title: 'Gestió de personal',
        href: '/',
        icon: BookUser,
    }
]

// const mainSecretaryNavItems: NavItem[] = [

// ]

// const mainDoctorNavItems: NavItem[] = [
    
// ]

const PatientNavItems: NavItem[] = [
    {
        title: 'Historial de cites',
        href: '/',
        icon: History,
    }
]

const WorkerNavItems: NavItem[] = [
    {
        title: 'Nova Cita',
        href: novaCita(),
        icon: CalendarPlus,
    },
    {
        title: 'Pacients',
        href: '/',
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
    }
]


const footerNavItems: NavItem[] = [
    {
        title: 'Repository',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: FolderGit2,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#vue',
        icon: BookOpen,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
            <NavMain label="Administració" v-if="isAdmin" :items="AdminNavItems"/>
            <NavMain label="Treballadors" v-if="isWorker" :items="WorkerNavItems"/>
            <NavMain label="Pacients" v-if="isPatient" :items="PatientNavItems"/>
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
