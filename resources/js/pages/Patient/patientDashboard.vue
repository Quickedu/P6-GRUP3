<script setup lang="ts">
import { Head, usePage, router } from '@inertiajs/vue3'
import { patientDashboard, cancelDate } from '@/routes'
import { computed, ref } from 'vue'
import { Calendar, Clock, AlertCircle, X } from 'lucide-vue-next'
import FullCalendar from '@/pages/components/FullCalendar.vue'
import EventPopover from '@/pages/components/EventPopover.vue'
import textNotify from '@/pages/components/textNotify.vue';
import DeleteModal from '@/pages/components/cancelModal.vue';

interface DateRecord {
    id: number;
    date_time: string;
    estat: string;
    urgencia: string;
    test: {name: string;}
}

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Inici', href: patientDashboard() },
        ],
    },
})

const page = usePage()
const dates = page.props.dates as DateRecord[]
const flashMessage = computed(() => (page.props.flash as any)?.message);
const flashStatus = computed(() => (page.props.flash as any)?.status);

const popover = ref<{
    visible: boolean
    x: number
    y: number
    event: any
}>({
    visible: false,
    x: 0,
    y: 0,
    event: null
})
const showCancelModal = ref(false)
const selectedDateId = ref<number | null>(null)

function openCancelModal(id: number) {
    selectedDateId.value = id
    showCancelModal.value = true
}

const handleEventClick = (info: any) => {
    const rect = info.el.getBoundingClientRect()
    
    // Calcula posició: apareix a sota de l'event, centrat
    let x = rect.left + rect.width / 2
    let y = rect.bottom + 8

    // Si surt per la dreta, desplaça a l'esquerra
    if (x + 120 > window.innerWidth) x = window.innerWidth - 130
    // Si surt per baix, apareix a dalt de l'event
    if (y + 220 > window.innerHeight) y = rect.top - 228

    popover.value = {
        visible: true,
        x,
        y,
        event: info.event
    }
}

const closePopover = () => {
    popover.value.visible = false
}

const calendarEvents = computed(() => {
    return dates.map(date => ({
        start: date.date_time,
        title: date.test.name,
        extendedProps: {
            urgency: date.urgencia,
            status: date.estat,
            test: date.test.name,
            id: date.id
        },
        backgroundColor: getStatusBg(date.urgencia),
        borderColor:     getStatusBorder(date.urgencia),
        textColor:       getStatusText(date.urgencia), 
    }))
})

const getStatusBg = (urgencia: string) => {
    switch (urgencia) {
        case 'urgent':     return '#fef2f2'
        case 'preferent':  return '#fff7ed'
        default:           return '#f0f7f6'
    }
}
const getStatusBorder = (urgencia: string) => {
    switch (urgencia) {
        case 'urgent':     return '#ef4444'
        case 'preferent':  return '#f97316'
        default:           return '#006557'
    }
}
const getStatusText = (urgencia: string) => {
    switch (urgencia) {
        case 'urgent':     return '#991b1b'
        case 'preferent':  return '#9a3412'
        default:           return '#003029' 
    }
}

const now = new Date()
const todayDates = computed(() =>
    dates
        .filter(d => {
            const dt = new Date(d.date_time)
            return dt.toDateString() === now.toDateString()
        })
        .sort((a, b) => new Date(a.date_time).getTime() - new Date(b.date_time).getTime())
)

const totalCount = computed(() => dates.length)
const programadesCount = computed(() => dates.filter(d => d.estat === 'programada').length)
const urgentCount = computed(() => dates.filter(d => d.urgencia === 'urgent').length)

const formatTime = (date: string) =>
    new Date(date).toLocaleTimeString('ca-ES', {
        hour: '2-digit',
        minute: '2-digit'
    })

const formatTodayLabel = () => {
    return now.toLocaleDateString('ca-ES', { day: 'numeric', month: 'short' })
        .replace('.', '')
        .toUpperCase()
}

const getUrgencyStyle = (urgencia: string) => {
    switch (urgencia) {
        case 'urgent':
            return { bg: 'bg-red-100 text-red-700', label: 'URGENT' }
        case 'preferent':
            return { bg: 'bg-[#e6f7f5] text-pmf-primary', label: 'PREFERENT' }
        default:
            return { bg: 'bg-gray-100 text-gray-600', label: 'NO URGENT' }
    }
}

function handleCancel(id: number) {
    closePopover()
    showCancelModal.value = false
    
    router.post(cancelDate.url(id), {}, {
        onSuccess: () => {
            router.reload()
        },
        onError: (errors) => {
            console.error(errors)
        }
    })
}
</script>

<template>
    <Head title="Gestió de cites" />
    <textNotify class="mb-4"
        v-if="flashMessage"
        :message="flashMessage"
        :status="flashStatus"/>

    <h1 class="text-pmf-primary font-semibold text-2xl mt-4 mx-6">Visites</h1>

    <div class="space-y-6 px-4 py-4 sm:px-6">

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">

            <div class="rounded-xl border border-red-200 bg-red-200/30 px-6 py-5">
                <p class="text-xs font-medium uppercase tracking-wider text-pmf-grey-light">Urgent</p>
                <div class="mt-2 flex items-center justify-between">
                    <span class="text-4xl font-medium text-red-600">{{ urgentCount }}</span>
                    <div class="rounded-lg bg-red-50 p-2">
                        <AlertCircle class="h-5 w-5 text-red-500" aria-hidden="true" />
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-[#c5d8d5] bg-[#CBE2E5]/70 px-6 py-5">
                <p class="text-xs font-medium uppercase tracking-wider text-pmf-grey-light">Programades</p>
                <div class="mt-2 flex items-center justify-between">
                    <span class="text-4xl font-medium text-pmf-green-dark">{{ programadesCount }}</span>
                    <div class="rounded-lg bg-[#f0f7f6] p-2">
                        <Clock class="h-5 w-5 text-pmf-primary" aria-hidden="true" />
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-[#c5d8d5] bg-white px-6 py-5">
                <p class="text-xs font-medium uppercase tracking-wider text-pmf-grey-light">Total cites</p>
                <div class="mt-2 flex items-center justify-between">
                    <span class="text-4xl font-medium text-pmf-green-dark">{{ totalCount }}</span>
                    <div class="rounded-lg bg-[#f0f7f6] p-2">
                        <Calendar class="h-5 w-5 text-pmf-primary" aria-hidden="true" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Calendar + Today's visits -->
        <div class="grid grid-cols-1 gap-4 lg:grid-cols-[1fr_300px]">

            <!-- Calendar -->
            <div class="rounded-xl border border-[#c5d8d5] bg-white p-5">
                <FullCalendar
                    :events="calendarEvents"
                    :on-event-click="handleEventClick"
                />
            </div>

            <!-- Today's visits -->
            <div class="rounded-xl border border-[#c5d8d5] bg-white p-5">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-base font-medium text-pmf-green-dark">Visites d'avui</h2>
                    <span class="rounded-md bg-pmf-secondary px-2 py-0.5 text-xs font-medium text-pmf-green-dark">
                        {{ formatTodayLabel() }}
                    </span>
                </div>

                <div v-if="todayDates.length" class="space-y-4 mt-4">
                    <div v-for="date in todayDates"
                        :key="date.id" class="border-b border-[#eaf2f1] pb-4 last:border-0 last:pb-0">
                        <div class="flex items-start justify-between gap-2">
                            <p class="text-sm font-medium text-pmf-green-dark capitalize">{{ date.estat }}</p>
                            <span
                                :class="['rounded-full px-2 py-0.5 text-[10px] font-medium', getUrgencyStyle(date.urgencia).bg]"
                            >
                                {{ getUrgencyStyle(date.urgencia).label }}
                            </span>
                        </div>
                        <div class="mt-1 flex items-center gap-1 text-xs text-pmf-grey-light">
                            <Clock class="h-3 w-3" aria-hidden="true" />
                            {{ formatTime(date.date_time) }}
                        </div>
                        <button v-if="date.estat !== 'cancel·lada'"
                            @click="openCancelModal(date.id)"
                            class="mt-2 w-full rounded-lg bg-red-50 px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-100 transition-colors cursor-pointer"
                        >
                            Cancel·lar cita
                        </button>
                    </div>
                </div>

                <p v-else class="text-sm text-pmf-grey-light">
                    Avui no tens cap cita agendada.
                </p>
            </div>
        </div>
    </div>

    <!-- Event modal -->
    <EventPopover
        :visible="popover.visible"
        :event="popover.event"
        :x="popover.x"
        :y="popover.y"
        @close="closePopover"
        @cancel="openCancelModal"
    />
    <DeleteModal
        v-model="showCancelModal"
        title="Cancel·lar cita"
        name="Aquesta cita mèdica"
        :id="selectedDateId"
        @confirm="handleCancel"
    />
</template>