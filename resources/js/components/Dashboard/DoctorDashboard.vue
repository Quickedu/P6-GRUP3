<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import { Calendar, Clock, AlertCircle } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import EventPopover from '@/pages/components/EventPopover.vue';
import FullCalendar from '@/pages/components/FullCalendar.vue';
import { patientDashboard } from '@/routes';

const page = usePage();

const user = computed(() => page.props.auth?.user);

const nts = ref(page.props.searchedNts || '');

const isDoctor = computed(() => user.value?.role === 'doctor');

interface DateRecord {
    id: number;
    date_time: string;
    estat: string;
    urgencia: string;
    test: { name: string };
}

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Inici', href: patientDashboard() }],
    },
});

const props = defineProps<{
    doctorDates?: DateRecord[];
}>();

const dates = computed(() => props.doctorDates ?? []);

const popover = ref<{
    visible: boolean;
    x: number;
    y: number;
    event: any;
}>({
    visible: false,
    x: 0,
    y: 0,
    event: null,
});

const handleEventClick = (info: any) => {
    const rect = info.el.getBoundingClientRect();

    // Calcula posició: apareix a sota de l'event, centrat
    let x = rect.left + rect.width / 2;
    let y = rect.bottom + 8;

    // Si surt per la dreta, desplaça a l'esquerra
    if (x + 120 > window.innerWidth) {
        x = window.innerWidth - 130;
    }

    // Si surt per baix, apareix a dalt de l'event
    if (y + 220 > window.innerHeight) {
        y = rect.top - 228;
    }

    popover.value = {
        visible: true,
        x,
        y,
        event: info.event,
    };
};

const closePopover = () => {
    popover.value.visible = false;
};

const calendarEvents = computed(() => {
    return dates.value.map((date: DateRecord) => ({
        start: date.date_time,
        title: date.test.name,
        extendedProps: {
            urgency: date.urgencia,
            status: date.estat,
            test: date.test.name,
            id: date.id,
        },
        backgroundColor: getStatusBg(date.urgencia),
        borderColor: getStatusBorder(date.urgencia),
        textColor: getStatusText(date.urgencia),
    }));
});

const getStatusBg = (urgencia: string) => {
    switch (urgencia) {
        case 'urgent':
            return '#fef2f2';
        case 'preferent':
            return '#fff7ed';
        default:
            return '#f0f7f6';
    }
};
const getStatusBorder = (urgencia: string) => {
    switch (urgencia) {
        case 'urgent':
            return '#ef4444';
        case 'preferent':
            return '#f97316';
        default:
            return '#006557';
    }
};
const getStatusText = (urgencia: string) => {
    switch (urgencia) {
        case 'urgent':
            return '#991b1b';
        case 'preferent':
            return '#9a3412';
        default:
            return '#003029';
    }
};

const now = new Date();
const todayDates = computed(() =>
    dates.value
        .filter((d: DateRecord) => {
            const dt = new Date(d.date_time);

            return dt.toDateString() === now.toDateString();
        })
        .sort(
            (a: DateRecord, b: DateRecord) =>
                new Date(a.date_time).getTime() -
                new Date(b.date_time).getTime(),
        ),
);

const totalCount = computed(() => dates.value.length);
const programadesCount = computed(
    () =>
        dates.value.filter((d: DateRecord) => d.estat === 'programada').length,
);
const urgentCount = computed(
    () => dates.value.filter((d: DateRecord) => d.urgencia === 'urgent').length,
);

const formatTime = (date: string) =>
    new Date(date).toLocaleTimeString('ca-ES', {
        hour: '2-digit',
        minute: '2-digit',
    });

const formatTodayLabel = () => {
    return now
        .toLocaleDateString('ca-ES', { day: 'numeric', month: 'short' })
        .replace('.', '')
        .toUpperCase();
};

const getUrgencyStyle = (urgencia: string) => {
    switch (urgencia) {
        case 'urgent':
            return { bg: 'bg-red-100 text-red-700', label: 'URGENT' };
        case 'preferent':
            return { bg: 'bg-[#e6f7f5] text-pmf-primary', label: 'PREFERENT' };
        default:
            return { bg: 'bg-gray-100 text-gray-600', label: 'NO URGENT' };
    }
};
</script>

<template>
    <Head title="Gestió de cites" />

    <h1 class="mx-6 mt-4 text-2xl font-semibold text-pmf-primary">
        Cites programades
    </h1>

    <div class="space-y-6 px-4 py-4 sm:px-6">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div
                class="rounded-xl border border-red-200 bg-red-200/30 px-6 py-5"
            >
                <p class="text-xs font-medium tracking-wider text-pmf-grey-light uppercase">
                    Urgent
                </p>
                <div class="mt-2 flex items-center justify-between">
                    <span class="text-4xl font-medium text-red-600">{{
                        urgentCount
                    }}</span>
                    <div class="rounded-lg bg-red-50 p-2">
                        <AlertCircle
                            class="h-5 w-5 text-red-500"
                            aria-hidden="true"
                        />
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-[#c5d8d5] bg-[#CBE2E5]/70 px-6 py-5">
                <p
                    class="text-xs font-medium tracking-wider text-pmf-grey-light uppercase"
                >
                    Programades
                </p>
                <div class="mt-2 flex items-center justify-between">
                    <span class="text-4xl font-medium text-pmf-green-dark">{{
                        programadesCount
                    }}</span>
                    <div class="rounded-lg bg-[#f0f7f6] p-2">
                        <Clock
                            class="h-5 w-5 text-pmf-primary"
                            aria-hidden="true"
                        />
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-[#c5d8d5] bg-white px-6 py-5">
                <p
                    class="text-xs font-medium tracking-wider text-pmf-grey-light uppercase"
                >
                    Total cites
                </p>
                <div class="mt-2 flex items-center justify-between">
                    <span class="text-4xl font-medium text-pmf-green-dark">{{
                        totalCount
                    }}</span>
                    <div class="rounded-lg bg-[#f0f7f6] p-2">
                        <Calendar
                            class="h-5 w-5 text-pmf-primary"
                            aria-hidden="true"
                        />
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 lg:grid-cols-[1fr_300px]">
            <!-- Calendar -->
            <div class="rounded-xl border border-[#c5d8d5] bg-white p-5">
                <FullCalendar
                    :events="calendarEvents"
                    :on-event-click="handleEventClick"
                />
            </div>

            <div class="rounded-xl border border-[#c5d8d5] bg-white p-5">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-base font-medium text-pmf-green-dark">
                        Visites d'avui
                    </h2>
                    <span
                        class="rounded-md bg-pmf-secondary px-2 py-0.5 text-xs font-medium text-pmf-green-dark"
                    >
                        {{ formatTodayLabel() }}
                    </span>
                </div>

                <div v-if="todayDates.length" class="mt-4 space-y-4">
                    <div
                        v-for="date in todayDates"
                        :key="date.id"
                        class="border-b border-[#eaf2f1] pb-4 last:border-0 last:pb-0"
                    >
                        <div class="flex items-start justify-between gap-2">
                            <p
                                class="text-sm font-medium text-pmf-green-dark capitalize"
                            >
                                {{ date.estat }}
                            </p>
                            <span
                                :class="[
                                    'rounded-full px-2 py-0.5 text-[10px] font-medium',
                                    getUrgencyStyle(date.urgencia).bg,
                                ]"
                            >
                                {{ getUrgencyStyle(date.urgencia).label }}
                            </span>
                        </div>
                        <div class="mt-1 flex items-center gap-1 text-xs text-pmf-grey-light">
                            <Clock class="h-3 w-3" aria-hidden="true" />
                            {{ formatTime(date.date_time) }}
                        </div>
                    </div>
                </div>

                <p v-else class="text-sm text-pmf-grey-light">
                    Avui no tens cap cita agendada.
                </p>
            </div>
        </div>
    </div>

    <EventPopover
        :visible="popover.visible"
        :event="popover.event"
        :x="popover.x"
        :y="popover.y"
        :can-cancel="false"
        @close="closePopover"
    />
</template>
