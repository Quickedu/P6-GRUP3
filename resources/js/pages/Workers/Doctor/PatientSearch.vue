<script setup lang="ts">
import { ref, computed } from 'vue';
import { router, usePage, Link } from '@inertiajs/vue3';
import { ArrowLeft, Download, Plus } from 'lucide-vue-next';

import textNotify from '@/pages/components/textNotify.vue';
import { formReport, patientSearch } from '@/routes';

const page = usePage();

const nts = ref('');

const user = computed(() => page.props.auth?.user);

const isSecretary = computed(() => user.value?.role === 'secretary');
const isDoctor = computed(() => user.value?.role === 'doctor');

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Inici', href: patientSearch() },
        ],
    },
});

interface Need {
    id: number;
    name: string;
}

interface Report {
    id: number;
    patient_id: string;
    worker_id: string;
    created_at: string;
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
    patient?: Patient | null;
    needs: Need[];
    reports: Report[];
    searchedNts?: string | null;
}>();

const flashMessage = computed(() => (page.props.flash as any)?.message);

const flashStatus = computed(() => (page.props.flash as any)?.status);

const goBack = () => {
    if (window.history.length > 1) {
        window.history.back();
    } else {
        router.get('/patientsList');
    }
};

function searchPatient() {
    router.get(('patientSearch'), {
        nts: nts.value
    });
}
</script>

<template>
    <div class="mx-5 mt-5 space-y-4">

        <textNotify
            v-if="flashMessage"
            class="mb-4"
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

            <span class="text-pmf-green-dark">
                Informe pacient
            </span>
        </div>

        <!-- Header -->
        <div class="flex items-center gap-3">

            <button
                @click="goBack"
                class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-[#c5d8d5] bg-white transition-colors hover:bg-[#f4f9f8]"
            >
                <ArrowLeft class="h-4 w-4 text-pmf-green" />
            </button>

            <h2 class="text-2xl font-medium text-pmf-green-dark">
                Detall pacient
            </h2>
        </div>

        <!-- Search -->
        <div class="rounded-xl border border-[#c5d8d5] bg-white p-6">

            <h2 class="mb-4 text-xl font-medium text-pmf-green-dark">
                Buscar pacient
            </h2>

            <div class="flex gap-3">

                <input
                    v-model="nts"
                    type="text"
                    placeholder="Número targeta sanitària"
                    class="w-full rounded-lg border border-[#c5d8d5]"
                    @keyup.enter="searchPatient"
                />

                <button
                    @click="searchPatient"
                    class="rounded-lg bg-pmf-primary px-4 py-2 text-white"
                >
                    Buscar
                </button>
            </div>
        </div>

        <!-- Patient Content -->
        <div v-if="props.patient" class="space-y-4">

            <!-- Dades Personals -->
            <div class="overflow-hidden rounded-xl border border-[#c5d8d5] bg-white">

                <div class="flex items-center justify-between border-b border-[#c5d8d5] bg-[#f0f7f6] px-5 py-3">
                    <h3 class="text-[11px] font-medium uppercase tracking-wider text-pmf-green">
                        Dades personals
                    </h3>
                </div>

                <div class="grid grid-cols-2 gap-x-8 gap-y-4 px-5 py-4 md:grid-cols-3">

                    <div>
                        <p class="mb-1 text-[11px] font-medium uppercase tracking-wider text-pmf-green">
                            Nom i cognoms
                        </p>

                        <p class="text-sm text-pmf-green-dark">
                            {{ props.patient.name }}
                        </p>
                    </div>

                    <div>
                        <p class="mb-1 text-[11px] font-medium uppercase tracking-wider text-pmf-green">
                            Número de telèfon
                        </p>

                        <p class="text-sm text-pmf-green-dark">
                            {{ props.patient.phone }}
                        </p>
                    </div>

                    <div>
                        <p class="mb-1 text-[11px] font-medium uppercase tracking-wider text-pmf-green">
                            Data de naixement
                        </p>

                        <p class="text-sm text-pmf-green-dark">
                            {{
                                new Date(props.patient.birth_date)
                                    .toLocaleDateString('es-ES', {
                                        day: '2-digit',
                                        month: '2-digit',
                                        year: 'numeric'
                                    })
                            }}
                        </p>
                    </div>

                    <div>
                        <p class="mb-1 text-[11px] font-medium uppercase tracking-wider text-pmf-green">
                            DNI
                        </p>

                        <p class="text-sm text-pmf-green-dark">
                            {{ props.patient.dni }}
                        </p>
                    </div>

                    <div>
                        <p class="mb-1 text-[11px] font-medium uppercase tracking-wider text-pmf-green">
                            NTS
                        </p>

                        <p class="text-sm text-pmf-green-dark">
                            {{ props.patient.nts }}
                        </p>
                    </div>

                    <div>
                        <p class="mb-1 text-[11px] font-medium uppercase tracking-wider text-pmf-green">
                            Correu electrònic
                        </p>

                        <p class="text-sm text-pmf-green-dark">
                            {{ props.patient.email }}
                        </p>
                    </div>

                    <div class="col-span-2 md:col-span-3">
                        <p class="mb-1 text-[11px] font-medium uppercase tracking-wider text-pmf-green">
                            Adreça
                        </p>

                        <p class="text-sm text-pmf-green-dark">
                            {{ props.patient.address }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Necessitats -->
            <div class="overflow-hidden rounded-xl border border-[#c5d8d5] bg-white">

                <div class="border-b border-[#c5d8d5] bg-[#f0f7f6] px-5 py-3">
                    <h3 class="text-[11px] font-medium uppercase tracking-wider text-pmf-green">
                        Necessitats
                    </h3>
                </div>

                <div class="px-5 py-4">

                    <div
                        v-if="props.needs.length > 0"
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

                    <p
                        v-else
                        class="py-2 text-sm text-pmf-grey-light"
                    >
                        No hi ha cap necessitat registrada.
                    </p>
                </div>
            </div>

            <!-- Informes -->
            <div class="overflow-hidden rounded-xl border border-[#c5d8d5] bg-white">

                <div class="flex items-center justify-between border-b border-[#c5d8d5] bg-[#f0f7f6] px-5 py-3">

                    <h3 class="text-[11px] font-medium uppercase tracking-wider text-pmf-green">
                        Informes mèdics
                    </h3>

                    <Link
                        v-if="isDoctor"
                        :href="formReport()"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-[#b0ceca] bg-pmf-primary px-2.5 py-1.5 text-xs font-medium text-white transition-colors hover:opacity-90"
                    >
                        <Plus class="h-3.5 w-3.5" />
                        Afegir informe
                    </Link>
                </div>

                <table
                    v-if="props.reports.length > 0"
                    class="w-full text-sm"
                >
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
                            <td class="px-5 py-3 font-medium text-pmf-green-dark">
                                Informe {{ report.id }}
                            </td>

                            <td class="px-5 py-3 text-pmf-grey-light">
                                {{ report.worker?.user?.name || '-' }}
                            </td>

                            <td class="px-5 py-3 text-pmf-grey-light">
                                {{
                                    new Date(report.created_at)
                                        .toLocaleDateString('es-ES')
                                }}
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
    </div>
</template>