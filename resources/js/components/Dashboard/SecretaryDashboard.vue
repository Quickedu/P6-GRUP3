<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import {
    Calendar,
    Stethoscope,
    User,
    FileText,
    Loader,
    Clock,
    Trash2,
} from 'lucide-vue-next';
import { ref } from 'vue';
import DateDetailModal from '@/pages/components/DateDetailModal.vue';
import RescheduleDateModal from '@/pages/components/RescheduleDateModal.vue';
import CancelModal from '@/pages/components/CancelModal.vue';
import { patientDetail } from '@/routes';
import DatesFilterComponent from './DatesFilterComponent.vue';
import PatientFilterComponent from './PatientFilterComponent.vue';

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

withDefaults(
    defineProps<{
        dates?: ScheduledDate[];
        doctors?: DoctorOption[];
    }>(),
    {
        dates: () => [],
        doctors: () => [],
    },
);

const displayedDates = ref<ScheduledDate[]>([]);
const resultError = ref('');
const isLoading = ref(false);
const hasSearched = ref(false);
type ActiveFilter = 'dates' | 'patient';

const activeFilter = ref<ActiveFilter>('dates');

// Modal state
const isDetailModalOpen = ref(false);
const isRescheduleModalOpen = ref(false);
const isCancelModalOpen = ref(false);
const selectedDate = ref<ScheduledDate | null>(null);

const resetResults = () => {
    hasSearched.value = false;
    displayedDates.value = [];
    resultError.value = '';
};

const showFilter = (filter: ActiveFilter) => {
    if (activeFilter.value === filter) {
        return;
    }

    activeFilter.value = filter;
    resetResults();
};

const getUrgencyColor = (urgency: string) => {
    const colors: Record<string, string> = {
        urgent: 'bg-red-100 text-red-800',
        preferent: 'bg-yellow-100 text-yellow-800',
        'no urgent': 'bg-green-100 text-green-800',
    };

    return colors[urgency] || 'bg-gray-100 text-gray-800';
};

const handlePatientResults = (dates: ScheduledDate[], error: string) => {
    hasSearched.value = true;
    displayedDates.value = dates;
    resultError.value = error;
};

const handlePatientCleared = () => {
    resetResults();
};

const handleDatesResults = (dates: ScheduledDate[], error: string) => {
    hasSearched.value = true;
    displayedDates.value = dates;
    resultError.value = error;
};

const handleDatesCleared = () => {
    resetResults();
};

//open detail modal with selected date
const openDateDetail = (date: ScheduledDate) => {
    selectedDate.value = date;
    isDetailModalOpen.value = true;
};

//open reschedule modal with selected date
const openRescheduleModal = (date: ScheduledDate) => {
    selectedDate.value = date;
    isRescheduleModalOpen.value = true;
};

//close modals
const closeDetailModal = () => {
    isDetailModalOpen.value = false;
    selectedDate.value = null;
};

const closeRescheduleModal = () => {
    isRescheduleModalOpen.value = false;
    selectedDate.value = null;
};

// Check if date is in the future
const isFutureDate = (dateTime: string): boolean => {
    return new Date(dateTime) > new Date();
};

// Open cancel modal with selected date
const openCancelModal = (date: ScheduledDate) => {
    if (!isFutureDate(date.date_time)) {
        resultError.value = 'No es pot cancel·lar una cita passada';
        setTimeout(() => {
            resultError.value = '';
        }, 3000);
        return;
    }
    selectedDate.value = date;
    isCancelModalOpen.value = true;
};

// Close cancel modal
const closeCancelModal = () => {
    isCancelModalOpen.value = false;
    selectedDate.value = null;
};

// Handle cancel confirmation
const handleCancelDate = (id: number) => {
    router.post(`/dateSchedule/${id}/cancel`, {}, {
        onSuccess: () => {
            // Refresh the results after successful cancellation
            if (activeFilter.value === 'dates') {
                // Re-trigger the dates filter to refresh results
                displayedDates.value = displayedDates.value.map(d =>
                    d.id === id ? { ...d, estat: 'cancel·lada' } : d
                );
            } else if (activeFilter.value === 'patient') {
                displayedDates.value = displayedDates.value.map(d =>
                    d.id === id ? { ...d, estat: 'cancel·lada' } : d
                );
            }
            closeCancelModal();
        },
        onError: (errors) => {
            console.error(errors);
            resultError.value = 'Error al cancel·lar la cita';
        },
    });
};
</script>

<template>
    <div class="w-full space-y-8">
        <div class="flex flex-wrap gap-2">
            <button
                type="button"
                :aria-pressed="activeFilter === 'dates'"
                @click="showFilter('dates')"
                :class="[
                    'rounded-lg px-3 py-2 text-sm font-medium transition',
                    activeFilter === 'dates'
                        ? 'bg-pmf-primary text-white'
                        : 'border border-gray-300 bg-white text-pmf-primary hover:bg-gray-50',
                ]"
            >
                Filtrar per dates i metge
            </button>

            <button
                type="button"
                :aria-pressed="activeFilter === 'patient'"
                @click="showFilter('patient')"
                :class="[
                    'rounded-lg px-3 py-2 text-sm font-medium transition',
                    activeFilter === 'patient'
                        ? 'bg-pmf-primary text-white'
                        : 'border border-gray-300 bg-white text-pmf-primary hover:bg-gray-50',
                ]"
            >
                Filtrar per pacient
            </button>
        </div>

        <div v-if="activeFilter === 'patient'">
            <PatientFilterComponent
                @results-found="handlePatientResults"
                @cleared="handlePatientCleared"
            />
        </div>
        <div v-else>
            <h2 class="mb-4 text-lg font-bold">
                Filtrar cites per data i metge
            </h2>
            <DatesFilterComponent
                :initial-dates="dates"
                :doctors="doctors"
                @results-found="handleDatesResults"
                @cleared="handleDatesCleared"
            />
        </div>

        <!--results section-->
        <div v-if="hasSearched">
            <!--loading state-->
            <div v-if="isLoading" class="flex items-center justify-center p-8">
                <Loader class="h-8 w-8 animate-spin text-pmf-primary" />
            </div>

            <!--error state-->
            <div
                v-else-if="resultError"
                class="rounded-lg bg-red-100 p-4 text-red-700"
            >
                {{ resultError }}
            </div>

            <!--results found-->
            <div v-else-if="displayedDates.length > 0" class="space-y-4">
                <h3 class="text-lg font-bold text-pmf-primary">
                    Cites trobades: {{ displayedDates.length }}
                </h3>
                <div class="space-y-4">
                    <div
                        v-for="date in displayedDates"
                        :key="date.id"
                        class="cursor-pointer rounded-lg border border-gray-200 bg-white px-4 pt-4 shadow-sm transition hover:shadow-md"
                        @click="openDateDetail(date)"
                    >
                        <div class="mb-4 flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center">
                                    <span class="mr-1 font-semibold"
                                        >Test:</span
                                    >
                                    {{ date.test?.name }}
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <button
                                    type="button"
                                    class="rounded-md border border-pmf-primary/30 bg-white px-3 py-1 text-xs font-semibold text-pmf-primary hover:bg-pmf-secondary"
                                    @click.stop="openRescheduleModal(date)"
                                >
                                    Reprogramar
                                </button>
                                <button
                                    v-if="date.estat !== 'cancel·lada' && isFutureDate(date.date_time)"
                                    type="button"
                                    class="rounded-md border border-red-300 bg-white px-3 py-1 text-xs font-semibold text-red-600 hover:bg-red-50"
                                    @click.stop="openCancelModal(date)"
                                >
                                    <Trash2 class="inline h-3 w-3 mr-1" aria-hidden="true" />
                                    Cancel·lar
                                </button>
                                <span
                                    :class="`inline-block rounded-lg px-2 py-1 text-xs font-medium ${getUrgencyColor(date.urgencia)}`"
                                >
                                    {{ date.urgencia }}
                                </span>
                            </div>
                        </div>

                        <div
                            v-if="date.description"
                            class="mb-4 flex items-start text-sm"
                        >
                            <FileText
                                class="mr-2 h-4 w-4 shrink-0 text-gray-500"
                            />
                            <p class="line-clamp-2 text-gray-600">
                                {{ date.description }}
                            </p>
                        </div>

                        <div class="mb-4 grid grid-cols-2 gap-4 lg:grid-cols-4">
                            <div class="flex min-w-0 flex-col">
                                <div class="inline-flex gap-2">
                                    <User
                                        class="h-6 w-6 rounded-md bg-pmf-secondary p-1 text-pmf-primary"
                                    />
                                    <p class="text-xs text-gray-600">Pacient</p>
                                </div>
                                <Link
                                    :href="patientDetail(date.patient.id)"
                                    @click.stop
                                    class="mt-1 text-sm font-semibold text-pmf-primary hover:underline"
                                    >{{ date.patient?.name }}</Link
                                >
                                <p class="text-xs text-gray-500">
                                    {{ date.patient?.nts }}
                                </p>
                            </div>

                            <div class="min-w-0 items-start">
                                <div class="inline-flex gap-2">
                                    <Stethoscope
                                        class="h-6 w-6 rounded-md bg-pmf-secondary p-1 text-pmf-primary"
                                    />
                                    <p class="text-xs text-gray-600">Doctor</p>
                                </div>
                                <p class="text-sm font-semibold">
                                    {{ date.worker?.user?.name || 'N/A' }}
                                </p>
                            </div>

                            <div class="min-w-0 items-start">
                                <div class="inline-flex gap-2">
                                    <Calendar
                                        class="h-6 w-6 rounded-md bg-pmf-secondary p-1 text-pmf-primary"
                                    />
                                    <p class="text-xs text-gray-600">
                                        Data i hora
                                    </p>
                                </div>
                                <p class="text-sm font-semibold">
                                    {{
                                        new Date(
                                            date.date_time,
                                        ).toLocaleDateString('ca-ES')
                                    }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    {{
                                        new Date(date.date_time).toLocaleString(
                                            'ca-ES',
                                            {
                                                hour: '2-digit',
                                                minute: '2-digit',
                                            },
                                        )
                                    }}
                                </p>
                            </div>

                            <div class="min-w-0 items-start">
                                <div class="inline-flex gap-2">
                                    <Clock
                                        class="h-6 w-6 rounded-md bg-pmf-secondary p-1 text-pmf-primary"
                                    />
                                    <p class="text-xs text-gray-600">Temps</p>
                                </div>
                                <p class="text-sm font-semibold">
                                    {{ date.time }} min
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!--no results-->
            <div
                v-else
                class="rounded-lg border border-gray-200 bg-gray-50 p-4 text-center"
            >
                <p class="text-gray-600">
                    No hi han cites amb els filtres seleccionats
                </p>
            </div>
        </div>

        <!--detail modal -->
        <DateDetailModal
            v-model:is-open="isDetailModalOpen"
            :date="selectedDate"
            @close="closeDetailModal"
        />

        <!--reschedule modal -->
        <RescheduleDateModal
            v-model:is-open="isRescheduleModalOpen"
            :date="selectedDate"
            @close="closeRescheduleModal"
        />

        <!--cancel modal -->
        <CancelModal
            v-model="isCancelModalOpen"
            title="Cancel·lar cita"
            @confirm="handleCancelDate"
            @close="closeCancelModal"
        />
    </div>
</template>
