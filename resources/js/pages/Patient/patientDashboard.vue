<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3'
import { patientDashboard } from '@/routes';
import { computed } from 'vue';
import { Calendar, Clock, AlertCircle } from 'lucide-vue-next';

interface DateRecord {
    id: number;
    date_time: string;
    time: number;
    estat: string;
    urgencia: string;
    patient_id: number;
    worker_id: number;
    test_id: number;
    created_at: string;
    updated_at: string;
}

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
const dates = page.props.dates as DateRecord[];

const now = new Date();

const futureDates = computed(() => {
    return dates
        .filter(date => new Date(date.date_time) > now)
        .sort((a, b) => new Date(a.date_time).getTime() - new Date(b.date_time).getTime());
});

const pastDates = computed(() => {
    return dates
        .filter(date => new Date(date.date_time) <= now)
        .sort((a, b) => new Date(b.date_time).getTime() - new Date(a.date_time).getTime());
});

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('ca-ES', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
};

const formatTime = (dateString: string) => {
    return new Date(dateString).toLocaleTimeString('ca-ES', {
        hour: '2-digit',
        minute: '2-digit',
    });
};

const getStatusColor = (estat: string) => {
    switch (estat) {
        case 'programada':
            return 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300';
        case 'realitzada':
            return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300';
        case 'cancel·lada':
            return 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300';
        default:
            return 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300';
    }
};

const getUrgencyIcon = (urgencia: string) => {
    return urgencia === 'urgent' ? 'text-red-500' : 'text-yellow-500';
};
</script>

<template>
    <Head title="Escriptori" />
    <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-4">
        <!-- Future dates section -->
        <div>
            <h2 class="mb-4 text-2xl font-bold text-foreground">Pròximes Cites</h2>
            <div v-if="futureDates.length > 0" class="space-y-3">
                <div 
                    v-for="date in futureDates" 
                    :key="date.id"
                    class="flex items-center gap-4 rounded-lg border border-sidebar-border/70 bg-muted/50 p-4 dark:border-sidebar-border"
                >
                    <div class="flex flex-shrink-0 items-center gap-2">
                        <Calendar class="h-5 w-5 text-pmf-primary" />
                        <div>
                            <p class="text-sm font-semibold text-foreground">
                                {{ formatDate(date.date_time) }}
                            </p>
                            <p class="flex items-center gap-1 text-xs text-muted-foreground">
                                <Clock class="h-3 w-3" />
                                {{ formatTime(date.date_time) }}
                            </p>
                        </div>
                    </div>
                    <div class="flex flex-grow items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span :class="['inline-block rounded-full px-3 py-1 text-xs font-medium', getStatusColor(date.estat)]">
                                {{ date.estat }}
                            </span>
                            <span v-if="date.urgencia === 'urgent'" class="flex items-center gap-1 text-xs font-medium text-red-600 dark:text-red-400">
                                <AlertCircle class="h-3 w-3" />
                                Urgent
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div v-else class="rounded-lg border border-dashed border-sidebar-border/70 p-8 text-center dark:border-sidebar-border">
                <p class="text-muted-foreground">No tens pròximes cites agendades</p>
            </div>
        </div>

        <!-- Past dates section -->
        <div>
            <h2 class="mb-4 text-2xl font-bold text-foreground">Cites Passades</h2>
            <div v-if="pastDates.length > 0" class="space-y-3">
                <div 
                    v-for="date in pastDates" 
                    :key="date.id"
                    class="flex items-center gap-4 rounded-lg border border-sidebar-border/70 bg-muted/30 p-4 opacity-75 dark:border-sidebar-border"
                >
                    <div class="flex flex-shrink-0 items-center gap-2">
                        <Calendar class="h-5 w-5 text-muted-foreground" />
                        <div>
                            <p class="text-sm font-semibold text-foreground">
                                {{ formatDate(date.date_time) }}
                            </p>
                            <p class="flex items-center gap-1 text-xs text-muted-foreground">
                                <Clock class="h-3 w-3" />
                                {{ formatTime(date.date_time) }}
                            </p>
                        </div>
                    </div>
                    <div class="flex flex-grow items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span :class="['inline-block rounded-full px-3 py-1 text-xs font-medium', getStatusColor(date.estat)]">
                                {{ date.estat }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div v-else class="rounded-lg border border-dashed border-sidebar-border/70 p-8 text-center dark:border-sidebar-border">
                <p class="text-muted-foreground">No tens cites passades</p>
            </div>
        </div>
    </div>
</template>
