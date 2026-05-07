<script setup lang="ts">
import { ref, computed } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import { ArrowLeft, Download, Plus } from 'lucide-vue-next';
import textNotify from '@/pages/components/textNotify.vue';
import PatientInfoModal from '@/pages/components/PatientInfoModal.vue';
import PatientNeedsModal from '@/pages/components/PatientNeedsModal.vue';

const page = usePage();
const user = computed(() => page.props.auth?.user);
const isSecretary = computed(() => user.value?.role === 'secretary');
const isDoctor = computed(() => user.value?.role === 'doctor');

interface Need {
    id: number;
    name: string;
}

interface Worker {
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

const goBack = () => router.get('/patientsList');

</script>

<template>
    <div class="space-y-4 mx-5 mt-5">
        <textNotify
            class="mb-4"
            v-if="flashMessage"
            :message="flashMessage"
            :status="flashStatus"
        />

        <!-- Breadcrumb -->
        <div class="flex items-center gap-1.5 text-sm text-pmf-grey-light">
            <button @click="goBack" class="hover:text-pmf-green transition-colors">Inici</button>
            <span>/</span>
            <span class="text-pmf-green-dark">Informe pacient</span>
        </div>

        <!-- Títol -->
        <div class="flex items-center gap-3">
            <button
                @click="goBack"
                class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-[#c5d8d5] bg-white hover:bg-[#f4f9f8] transition-colors"
            >
                <ArrowLeft class="h-4 w-4 text-pmf-green" />
            </button>
            <h2 class="text-2xl font-medium text-pmf-green-dark">Detall pacient</h2>
        </div>

        <!-- Dades Personals -->
        <div class="overflow-hidden rounded-xl border border-[#c5d8d5] bg-white">
            <div class="flex items-center justify-between px-5 py-3 bg-[#f0f7f6] border-b border-[#c5d8d5]">
                <h3 class="text-[11px] font-medium uppercase tracking-wider text-pmf-green">Dades personals</h3>
                <button
                    v-if="isSecretary"
                    @click="isInfoModalOpen = true"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-[#b0ceca] bg-pmf-primary px-2.5 py-1.5 text-xs font-medium text-white transition-colors hover:opacity-90 cursor-pointer"
                >
                    Editar
                </button>
            </div>

            <div class="px-5 py-4 grid grid-cols-2 md:grid-cols-3 gap-x-8 gap-y-4">
                <div>
                    <p class="text-[11px] font-medium uppercase tracking-wider text-pmf-green mb-1">Nom i cognoms</p>
                    <p class="text-sm text-pmf-green-dark">{{ props.patient.name }}</p>
                </div>
                <div>
                    <p class="text-[11px] font-medium uppercase tracking-wider text-pmf-green mb-1">Número de telèfon</p>
                    <p class="text-sm text-pmf-green-dark">{{ props.patient.phone}}</p>
                </div>
                <div>
                    <p class="text-[11px] font-medium uppercase tracking-wider text-pmf-green mb-1">Data de naixement</p>
                    <p class="text-sm text-pmf-green-dark">{{ new Date(props.patient.birth_date).toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric' }) }}</p>
                </div>
                <div>
                    <p class="text-[11px] font-medium uppercase tracking-wider text-pmf-green mb-1">DNI</p>
                    <p class="text-sm text-pmf-green-dark">{{ props.patient.dni }}</p>
                </div>
                <div>
                    <p class="text-[11px] font-medium uppercase tracking-wider text-pmf-green mb-1">NTS</p>
                    <p class="text-sm text-pmf-green-dark">{{ props.patient.nts }}</p>
                </div>
                <div>
                    <p class="text-[11px] font-medium uppercase tracking-wider text-pmf-green mb-1">Correu electrònic</p>
                    <p class="text-sm text-pmf-green-dark">{{ props.patient.email }}</p>
                </div>
                <div class="col-span-2 md:col-span-3">
                    <p class="text-[11px] font-medium uppercase tracking-wider text-pmf-green mb-1">Adreça</p>
                    <p class="text-sm text-pmf-green-dark">{{ props.patient.address }}</p>
                </div>
            </div>
        </div>

        <!-- Necessitats -->
        <div class="overflow-hidden rounded-xl border border-[#c5d8d5] bg-white">
            <div class="flex items-center justify-between px-5 py-3 bg-[#f0f7f6] border-b border-[#c5d8d5]">
                <h3 class="text-[11px] font-medium uppercase tracking-wider text-pmf-green">Necessitats</h3>
                <button
                    v-if="isSecretary"
                    @click="isNeedsModalOpen = true"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-[#b0ceca] bg-pmf-primary px-2.5 py-1.5 text-xs font-medium text-white transition-colors hover:opacity-90 cursor-pointer"
                >
                    Gestionar
                </button>
            </div>

            <div class="px-5 py-4">
                <div v-if="props.needs && props.needs.length > 0" class="flex flex-wrap gap-2">
                    <span
                        v-for="need in props.needs"
                        :key="need.id"
                        class="rounded-full bg-pmf-secondary px-3 py-1 text-xs font-medium text-pmf-green-dark"
                    >
                        {{ need.name }}
                    </span>
                </div>
                <p v-else class="text-sm text-pmf-grey-light py-2">
                    No hi ha cap necessitat registrada.
                </p>
            </div>
        </div>

        <!-- Informes Mèdics -->
        <div class="overflow-hidden rounded-xl border border-[#c5d8d5] bg-white">
            <div class="flex items-center justify-between px-5 py-3 bg-[#f0f7f6] border-b border-[#c5d8d5]">
                <h3 class="text-[11px] font-medium uppercase tracking-wider text-pmf-green">Informes mèdics</h3>
                <button
                    v-if="isDoctor"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-[#b0ceca] bg-pmf-primary px-2.5 py-1.5 text-xs font-medium text-white transition-colors hover:opacity-90 cursor-pointer"
                >
                    <Plus class="h-3.5 w-3.5" />
                    Afegir informe
                </button>
            </div>

            <table class="w-full text-sm" v-if="props.reports && props.reports.length > 0">
                <thead class="border-b border-[#c5d8d5] text-left text-[11px] font-medium uppercase tracking-wider text-pmf-green">
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
                        <td class="px-5 py-3 font-medium text-pmf-green-dark">Informe {{ report.id }}</td>
                        <td class="px-5 py-3 text-pmf-grey-light">{{ report.worker?.user?.name || '' }}</td>
                        <td class="px-5 py-3 text-pmf-grey-light">{{ report.created_at }}</td>
                        <td class="px-5 py-3">
                            <a :href="`/storage/${report.pdf_path}`"
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

            <div v-else class="px-5 py-12 text-center text-pmf-grey-light text-sm">
                No hi ha cap informe mèdic registrat.
            </div>
        </div> 
    </div>

    <!-- Modals -->
    <PatientInfoModal
        v-model="isInfoModalOpen"
        :patient="props.patient"
    />

    <PatientNeedsModal
        v-model="isNeedsModalOpen"
        :patient="props.patient"
        :patient-needs="props.needs"
        :available-needs="props.availableNeeds"
    />
</template>