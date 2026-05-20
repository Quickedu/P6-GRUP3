<script setup lang="ts">
import { router, usePage } from '@inertiajs/vue3';
import { ArrowLeft, Download, Plus } from 'lucide-vue-next';
import { ref, computed } from 'vue';
import PatientInfoModal from '@/pages/components/PatientInfoModal.vue';
import PatientNeedsModal from '@/pages/components/PatientNeedsModal.vue';
import textNotify from '@/pages/components/textNotify.vue';

const page = usePage();
const user = computed(() => page.props.auth?.user);
const isSecretary = computed(() => user.value?.role === 'secretary');
const isDoctor = computed(() => user.value?.role === 'doctor');

interface Need {
    id: number;
    name: string;
}

interface Report {
    id: number;
    patient_id: string;
    worker_id: string;
    created_at: Date;
    pdf_path: string;
    worker?: {
        id: number;
        user?: {
            id: number;
            name: string;
        };
    };
}

interface Patient {
    id: number;
    name: string;
    birth_date: string;
    nts: string;
    address: string;
    dni: string;
    phone: string;
    email: string;
}

const props = defineProps<{
    patient: Patient;
    needs: Need[];
    reports: Report[];
    availableNeeds: Need[];
}>();

const flashMessage = computed(() => (page.props.flash as any)?.message);
const flashStatus = computed(() => (page.props.flash as any)?.status);

const isInfoModalOpen = ref(false);
const isNeedsModalOpen = ref(false);

const goBack = () => {
    if (window.history.length > 1) {
        window.history.back();
    } else {
        router.get('/patientsList');
    }
};
</script>

<template>
    <div class="mx-5 mt-5 space-y-4">
        <textNotify
            class="mb-4"
            v-if="flashMessage"
            :message="flashMessage"
            :status="flashStatus"
        />

        <!-- Breadcrumb -->
        <div class="flex items-center gap-1.5 text-sm text-pmf-grey-light">
            <button
                @click="goBack"
                class="transition-colors hover:text-pmf-green"
            >
                Inici
            </button>
            <span>/</span>
            <span class="text-pmf-green-dark">Informe pacient</span>
        </div>

        <!-- Títol -->
        <div class="flex items-center gap-3">
            <button
                @click="goBack"
                class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-[#c5d8d5] bg-white transition-colors hover:bg-[#f4f9f8]"
                aria-label="Tornar enrere"
            >
                <ArrowLeft class="h-4 w-4 text-pmf-green" />
            </button>
            <h2 class="text-2xl font-medium text-pmf-green-dark">
                Detall pacient
            </h2>
        </div>

        <!-- Dades Personals -->
        <div
            class="overflow-hidden rounded-xl border border-[#c5d8d5] bg-white"
        >
            <div
                class="flex items-center justify-between border-b border-[#c5d8d5] bg-[#f0f7f6] px-5 py-3"
            >
                <h3
                    class="text-[11px] font-medium tracking-wider text-pmf-green uppercase"
                >
                    Dades personals
                </h3>
                <button
                    v-if="isSecretary"
                    @click="isInfoModalOpen = true"
                    class="inline-flex cursor-pointer items-center gap-1.5 rounded-lg border border-[#b0ceca] bg-pmf-primary px-2.5 py-1.5 text-xs font-medium text-white transition-colors hover:opacity-90"
                >
                    Editar
                </button>
            </div>

            <div
                class="grid grid-cols-2 gap-x-8 gap-y-4 px-5 py-4 md:grid-cols-3"
            >
                <div>
                    <p
                        class="mb-1 text-[11px] font-medium tracking-wider text-pmf-green uppercase"
                    >
                        Nom i cognoms
                    </p>
                    <p class="text-sm text-pmf-green-dark">
                        {{ props.patient.name }}
                    </p>
                </div>
                <div>
                    <p
                        class="mb-1 text-[11px] font-medium tracking-wider text-pmf-green uppercase"
                    >
                        Número de telèfon
                    </p>
                    <p class="text-sm text-pmf-green-dark">
                        {{ props.patient.phone }}
                    </p>
                </div>
                <div>
                    <p
                        class="mb-1 text-[11px] font-medium tracking-wider text-pmf-green uppercase"
                    >
                        Data de naixement
                    </p>
                    <p class="text-sm text-pmf-green-dark">
                        {{
                            new Date(
                                props.patient.birth_date,
                            ).toLocaleDateString('es-ES', {
                                day: '2-digit',
                                month: '2-digit',
                                year: 'numeric',
                            })
                        }}
                    </p>
                </div>
                <div>
                    <p
                        class="mb-1 text-[11px] font-medium tracking-wider text-pmf-green uppercase"
                    >
                        DNI
                    </p>
                    <p class="text-sm text-pmf-green-dark">
                        {{ props.patient.dni }}
                    </p>
                </div>
                <div>
                    <p
                        class="mb-1 text-[11px] font-medium tracking-wider text-pmf-green uppercase"
                    >
                        NTS
                    </p>
                    <p class="text-sm text-pmf-green-dark">
                        {{ props.patient.nts }}
                    </p>
                </div>
                <div>
                    <p
                        class="mb-1 text-[11px] font-medium tracking-wider text-pmf-green uppercase"
                    >
                        Correu electrònic
                    </p>
                    <p class="text-sm text-pmf-green-dark">
                        {{ props.patient.email }}
                    </p>
                </div>
                <div class="col-span-2 md:col-span-3">
                    <p
                        class="mb-1 text-[11px] font-medium tracking-wider text-pmf-green uppercase"
                    >
                        Adreça
                    </p>
                    <p class="text-sm text-pmf-green-dark">
                        {{ props.patient.address }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Necessitats -->
        <div
            class="overflow-hidden rounded-xl border border-[#c5d8d5] bg-white"
        >
            <div
                class="flex items-center justify-between border-b border-[#c5d8d5] bg-[#f0f7f6] px-5 py-3"
            >
                <h3
                    class="text-[11px] font-medium tracking-wider text-pmf-green uppercase"
                >
                    Necessitats
                </h3>
                <button
                    v-if="isSecretary"
                    @click="isNeedsModalOpen = true"
                    class="inline-flex cursor-pointer items-center gap-1.5 rounded-lg border border-[#b0ceca] bg-pmf-primary px-2.5 py-1.5 text-xs font-medium text-white transition-colors hover:opacity-90"
                >
                    Gestionar
                </button>
            </div>

            <div class="px-5 py-4">
                <div
                    v-if="props.needs && props.needs.length > 0"
                    class="flex flex-wrap gap-2"
                >
                    <span
                        v-for="need in props.needs"
                        :key="need.id"
                        class="rounded-full bg-pmf-secondary px-3 py-1 text-xs font-medium text-pmf-green-dark"
                    >
                        {{ need.name }}
                    </span>
                </div>
                <p v-else class="py-2 text-sm text-pmf-grey-light">
                    No hi ha cap necessitat registrada.
                </p>
            </div>
        </div>

        <!-- Informes Mèdics -->
        <div
            class="overflow-hidden rounded-xl border border-[#c5d8d5] bg-white"
        >
            <div
                class="flex items-center justify-between border-b border-[#c5d8d5] bg-[#f0f7f6] px-5 py-3"
            >
                <h3
                    class="text-[11px] font-medium tracking-wider text-pmf-green uppercase"
                >
                    Informes mèdics
                </h3>
                <button
                    v-if="isDoctor"
                    class="inline-flex cursor-pointer items-center gap-1.5 rounded-lg border border-[#b0ceca] bg-pmf-primary px-2.5 py-1.5 text-xs font-medium text-white transition-colors hover:opacity-90"
                >
                    <Plus class="h-3.5 w-3.5" />
                    Afegir informe
                </button>
            </div>

            <table
                class="w-full text-sm"
                v-if="props.reports && props.reports.length > 0"
            >
                <thead
                    class="border-b border-[#c5d8d5] text-left text-[11px] font-medium tracking-wider text-pmf-green uppercase"
                >
                    <tr>
                        <th class="px-5 py-3">Informe</th>
                        <th class="px-5 py-3">Metge</th>
                        <th class="px-5 py-3">Data</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#eaf2f1]">
                    <tr
                        v-for="report in props.reports"
                        :key="report.id"
                        class="transition-colors hover:bg-[#f4f9f8]"
                    >
                        <td class="px-5 py-3 font-medium text-pmf-green-dark">
                            Informe {{ report.id }}
                        </td>
                        <td class="px-5 py-3 text-pmf-grey-light">
                            {{ report.worker?.user?.name || '' }}
                        </td>
                        <td class="px-5 py-3 text-pmf-grey-light">
                            {{ report.created_at }}
                        </td>
                        <td class="px-5 py-3">
                            <a
                                :href="`/storage/${report.pdf_path}`"
                                target="_blank"
                                class="inline-flex items-center gap-1.5 rounded-lg border border-[#b0ceca] px-2.5 py-1.5 text-xs font-medium text-pmf-green transition-colors hover:bg-[#f0f7f6]"
                            >
                                <Download class="h-3.5 w-3.5" />
                                Descarregar
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div
                v-else
                class="px-5 py-12 text-center text-sm text-pmf-grey-light"
            >
                No hi ha cap informe mèdic registrat.
            </div>
        </div>
    </div>

    <!-- Modals -->
    <PatientInfoModal v-model="isInfoModalOpen" :patient="props.patient" />

    <PatientNeedsModal
        v-model="isNeedsModalOpen"
        :patient="props.patient"
        :patient-needs="props.needs"
        :available-needs="props.availableNeeds"
    />
</template>
