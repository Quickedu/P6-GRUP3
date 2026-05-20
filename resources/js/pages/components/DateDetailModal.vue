<!-- DateDetailModal.vue -->
<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    Calendar,
    Stethoscope,
    User,
    FileText,
    Clock,
    X,
} from 'lucide-vue-next';
import { patientDetail } from '@/routes';

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

const { isOpen, date } = defineProps<{
    isOpen: boolean;
    date: ScheduledDate | null;
}>();

const emit = defineEmits<{
    'update:isOpen': [value: boolean];
    close: [];
}>();

const getUrgencyColor = (urgency: string) => {
    const colors: Record<string, string> = {
        urgent: 'bg-red-100 text-red-800',
        preferent: 'bg-yellow-100 text-yellow-800',
        'no urgent': 'bg-green-100 text-green-800',
    };

    return colors[urgency] || 'bg-gray-100 text-gray-800';
};

const closeModal = () => {
    emit('update:isOpen', false);
    emit('close');
};

const handleBackdropClick = (e: MouseEvent) => {
    if (e.target === e.currentTarget) {
        closeModal();
    }
};
</script>

<template>
    <Teleport to="body">
        <div
            v-if="isOpen && date"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 transition-opacity"
            @click="handleBackdropClick"
        >
            <div
                class="relative max-h-[90vh] w-full max-w-3xl transform overflow-y-auto rounded-lg bg-white shadow-xl transition-all"
            >
                <!--header -->
                <div
                    class="sticky top-0 flex items-center justify-between rounded-t-lg border-b border-[#c5d8d5] bg-[#f0f7f6] px-6 py-4"
                >
                    <div>
                        <h2 class="text-xl font-bold text-pmf-primary">
                            Detalls de la cita
                        </h2>
                    </div>
                    <button
                        @click="closeModal"
                        class="text-gray-400 transition-colors hover:text-gray-600"
                        aria-label="Tancar modal"
                    >
                        <X class="h-6 w-6" />
                    </button>
                </div>

                <!--content -->
                <div class="space-y-6 p-6">
                    <!--test and urgency -->
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">
                                Prova: {{ date.test?.name }}
                            </h3>
                        </div>
                        <span
                            :class="`inline-block rounded-lg px-3 py-1 text-sm font-medium ${getUrgencyColor(date.urgencia)}`"
                        >
                            {{ date.urgencia }}
                        </span>
                    </div>

                    <!--description section -->
                    <div class="rounded-lg bg-gray-50 p-4">
                        <div class="mb-2 flex items-start gap-2">
                            <FileText
                                class="mt-0.5 h-5 w-5 shrink-0 text-pmf-primary"
                            />
                            <h4 class="font-semibold text-gray-900">
                                Descripció completa
                            </h4>
                        </div>
                        <p class="m-5 text-gray-700">
                            {{
                                date.description ||
                                'No hi ha descripció disponible'
                            }}
                        </p>
                    </div>

                    <!-- patient information -->
                    <div class="border-t border-gray-200 pt-4">
                        <h4
                            class="mb-3 flex items-center gap-2 font-semibold text-gray-900"
                        >
                            <User class="h-5 w-5 text-pmf-primary" />
                            Informació del pacient
                        </h4>
                        <div class="ml-7 grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <p class="text-xs text-gray-500">Nom complet</p>
                                <Link
                                    :href="patientDetail(date.patient.id)"
                                    @click.stop
                                    class="font-medium text-pmf-primary hover:underline"
                                >
                                    {{ date.patient?.name }}
                                </Link>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">NTS</p>
                                <p class="font-medium text-gray-900">
                                    {{ date.patient?.nts }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!--professional information -->
                    <div class="border-t border-gray-200 pt-4">
                        <h4
                            class="mb-3 flex items-center gap-2 font-semibold text-gray-900"
                        >
                            <Stethoscope class="h-5 w-5 text-pmf-primary" />
                            Informació del professional
                        </h4>
                        <div class="ml-7">
                            <p class="text-xs text-gray-500">Doctor/Tècnic</p>
                            <p class="font-medium text-gray-900">
                                {{ date.worker?.user?.name || 'No assignat' }}
                            </p>
                        </div>
                    </div>

                    <!-- date and time information -->
                    <div class="border-t border-gray-200 pt-4">
                        <h4
                            class="mb-3 flex items-center gap-2 font-semibold text-gray-900"
                        >
                            <Calendar class="h-5 w-5 text-pmf-primary" />
                            Data i hora
                        </h4>
                        <div class="ml-7 grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <p class="text-xs text-gray-500">Data</p>
                                <p class="font-medium text-gray-900">
                                    {{
                                        new Date(
                                            date.date_time,
                                        ).toLocaleDateString('ca-ES', {
                                            weekday: 'long',
                                            year: 'numeric',
                                            month: 'long',
                                            day: 'numeric',
                                        })
                                    }}
                                </p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Hora</p>
                                <p class="font-medium text-gray-900">
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
                        </div>
                    </div>

                    <!--duration -->
                    <div class="border-t border-gray-200 pt-4">
                        <h4
                            class="mb-2 flex items-center gap-2 font-semibold text-gray-900"
                        >
                            <Clock class="h-5 w-5 text-pmf-primary" />
                            Durada
                        </h4>
                        <div class="ml-7">
                            <p class="font-medium text-gray-900">
                                {{ date.time }} minuts
                            </p>
                        </div>
                    </div>
                </div>

                <!--footer -->
                <div
                    class="sticky bottom-0 flex justify-end rounded-b-lg border-t border-gray-200 bg-gray-50 px-6 py-4"
                >
                    <button
                        @click="closeModal"
                        class="rounded-lg bg-pmf-primary px-4 py-2 text-white transition-colors hover:bg-pmf-green"
                    >
                        Tancar
                    </button>
                </div>
            </div>
        </div>
    </Teleport>
</template>
