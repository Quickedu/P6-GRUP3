<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { Download, FileText, Search, X, ChevronLeft, ChevronRight, Microscope } from 'lucide-vue-next';
import { useRelativeTime } from '@/composables/useRelativeTime';
import { patientDashboard } from '@/routes';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Inici', href: patientDashboard() },
            { title: 'Informes i resultats', href: '#' },
        ],
    },
});

const { getRelativeTime } = useRelativeTime();

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

const props = defineProps<{
    reports: Report[];
}>();

const today = new Date();
const twoYearsAgo = new Date();
twoYearsAgo.setFullYear(today.getFullYear() - 2);

const toInputDate = (d: Date) => d.toISOString().split('T')[0];

const dateFrom = ref(toInputDate(twoYearsAgo));
const dateTo = ref(toInputDate(today));

const formatDate = (dateStr: string) => {
    const date = new Date(dateStr);
    return date.toLocaleDateString('ca-ES', { day: '2-digit', month: '2-digit', year: 'numeric' });
};

const clearFilters = () => {
    dateFrom.value = toInputDate(twoYearsAgo);
    dateTo.value = toInputDate(today);
};

const filteredReports = computed(() => {
    return props.reports.filter(report => {
        const reportDate = new Date(report.created_at);
        const from = dateFrom.value ? new Date(dateFrom.value) : null;
        const to = dateTo.value ? new Date(dateTo.value + 'T23:59:59') : null;
        if (from && reportDate < from) return false;
        if (to && reportDate > to) return false;
        return true;
    });
});

const PAGE_SIZE = 5;
const currentPage = ref(1);

// Reset to page 1 whenever the filter changes
watch([dateFrom, dateTo], () => { currentPage.value = 1; });

const totalPages = computed(() => Math.max(1, Math.ceil(filteredReports.value.length / PAGE_SIZE)));

const pagedReports = computed(() => {
    const start = (currentPage.value - 1) * PAGE_SIZE;
    return filteredReports.value.slice(start, start + PAGE_SIZE);
});

const prevPage = () => { if (currentPage.value > 1) currentPage.value--; };
const nextPage = () => { if (currentPage.value < totalPages.value) currentPage.value++; };


// Group ORDER as defined in the composable
const GROUP_ORDER = [
    'Avui',
    'Ahir',
    'Últims 7 dies',
    'Últim mes',
    'Últim any',
    "Més d'un any",
    'Mai',
];

interface Group {
    label: string;
    reports: Report[];
}

const groupedReports = computed((): Group[] => {
    const map = new Map<string, Report[]>();

    for (const report of pagedReports.value) {
        const label = getRelativeTime(report.created_at);
        if (!map.has(label)) map.set(label, []);
        map.get(label)!.push(report);
    }

    return GROUP_ORDER
        .filter(label => map.has(label))
        .map(label => ({ label, reports: map.get(label)! }));
});
</script>

<template>
    <div class="space-y-6 px-6 py-6">
        <div>
            <div class="flex items-center gap-3">
                <div class="rounded-lg bg-pmf-secondary p-2 text-pmf-primary">
                    <Microscope
                        class="size-6"
                        aria-hidden="true"
                    />
                </div>
                <h1 class="text-3xl font-bold text-pmf-green-dark">Informes i resultats</h1>
            </div>
            <p class="text-sm text-pmf-grey-light mt-3">Consulta tots els teus informes clínics</p>
        </div>

        <!-- Data filters -->
        <div class="overflow-hidden rounded-xl border border-[#c5d8d5] bg-white">
            <div class="flex items-center justify-between px-5 py-3 bg-[#f0f7f6] border-b border-[#c5d8d5]">
                <h3 class="text-[11px] font-medium uppercase tracking-wider text-pmf-green">Filtres</h3>
                <button @click="clearFilters" 
                    class="inline-flex items-center gap-1 text-xs text-pmf-grey-light hover:text-pmf-green transition-colors border border-pmf-primary/40 p-2 rounded-lg">
                    <X class="h-3 w-3" />
                    Netejar filtres
                </button>
            </div>

            <div class="px-5 py-4 flex flex-wrap gap-4 items-end">
                <!-- Since -->
                <div class="flex flex-col gap-1">
                    <label class="text-[11px] font-medium uppercase tracking-wider text-pmf-green">Des de</label>
                    <div class="relative">
                        <input
                            type="date"
                            v-model="dateFrom"
                            aria-label="Data des de"
                            class="rounded-lg border border-[#c5d8d5] bg-white px-3 py-2 text-sm text-pmf-green-dark focus:outline-none focus:ring-2 focus:ring-pmf-green/30 focus:border-pmf-green transition-colors pr-8"
                        />
                    </div>
                </div>

                <!-- Up to -->
                <div class="flex flex-col gap-1">
                    <label class="text-[11px] font-medium uppercase tracking-wider text-pmf-green">Fins a</label>
                    <div class="relative">
                        <input
                            type="date"
                            aria-label="Data fins a"
                            v-model="dateTo"
                            class="rounded-lg border border-[#c5d8d5] bg-white px-3 py-2 text-sm text-pmf-green-dark focus:outline-none focus:ring-2 focus:ring-pmf-green/30 focus:border-pmf-green transition-colors pr-8"
                        />
                    </div>
                </div>
            </div>
        </div>

        <!-- Reports list -->
        <div class="overflow-hidden rounded-xl border border-[#c5d8d5] bg-white">
            <div class="flex items-center px-5 py-3 bg-[#f0f7f6] border-b border-[#c5d8d5]">
                <h3 class="text-[11px] font-medium uppercase tracking-wider text-pmf-green">Informes mèdics</h3>
            </div>

            <!-- Table with grouped results -->
            <template v-if="filteredReports.length > 0">
                <template v-for="group in groupedReports" :key="group.label">
                    <div class="px-5 py-2 bg-[#f8fcfb] border-b border-[#eaf2f1] flex items-center gap-2">
                        <span class="text-[11px] font-semibold uppercase tracking-wider text-pmf-green">{{ group.label }}</span>
                        <span class="rounded-full bg-pmf-secondary px-1.5 py-0 text-[10px] font-medium text-pmf-green-dark">{{ group.reports.length }}</span>
                    </div>

                    <table class="w-full text-sm">
                        <tbody class="divide-y divide-[#eaf2f1]">
                            <tr
                                v-for="report in group.reports"
                                :key="report.id"
                                class="transition-colors hover:bg-[#f4f9f8]"
                            >
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-2.5">
                                        <div class="flex-shrink-0 w-8 h-8 rounded-lg bg-pmf-secondary flex items-center justify-center">
                                            <FileText class="h-4 w-4 text-pmf-green" />
                                        </div>
                                        <span class="font-medium text-pmf-green-dark">Informe {{ report.id }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-3 text-pmf-grey-light hidden sm:table-cell">
                                    {{ report.worker?.user?.name || '—' }}
                                </td>
                                <td class="px-5 py-3 text-pmf-grey-light">
                                    {{ formatDate(report.created_at) }}
                                </td>
                                <td class="px-5 py-3 text-right">
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
                </template>

                <!-- Pagination -->
                <div class="flex items-center justify-between px-5 py-3 border-t border-[#eaf2f1]">
                    <p class="text-xs text-pmf-grey-light">
                        Pàgina <span class="font-medium text-pmf-green-dark">{{ currentPage }}</span> de <span class="font-medium text-pmf-green-dark">{{ totalPages }}</span>
                        &nbsp;·&nbsp; {{ filteredReports.length }} {{ filteredReports.length === 1 ? 'informe' : 'informes' }}
                    </p>
                    <div class="flex items-center gap-1.5">
                        <button
                            @click="prevPage"
                            :disabled="currentPage === 1"
                            aria-label="Pàgina anterior"
                            class="inline-flex items-center justify-center w-7 h-7 rounded-lg border border-[#c5d8d5] bg-white text-pmf-green transition-colors hover:bg-[#f0f7f6] disabled:opacity-40 disabled:cursor-not-allowed"
                        >
                            <ChevronLeft class="h-4 w-4" />
                        </button>
                        <button
                            @click="nextPage"
                            :disabled="currentPage === totalPages"
                            aria-label="Pàgina següent"
                            class="inline-flex items-center justify-center w-7 h-7 rounded-lg border border-[#c5d8d5] bg-white text-pmf-green transition-colors hover:bg-[#f0f7f6] disabled:opacity-40 disabled:cursor-not-allowed"
                        >
                            <ChevronRight class="h-4 w-4" />
                        </button>
                    </div>
                </div>
            </template>

            <!-- Empty status: no results with the current filter -->
            <div v-else-if="props.reports.length > 0" class="px-5 py-12 text-center">
                <div class="mx-auto mb-3 w-10 h-10 rounded-full bg-pmf-secondary flex items-center justify-center">
                    <Search class="h-5 w-5 text-pmf-green" />
                </div>
                <p class="text-sm font-medium text-pmf-green-dark mb-1">No s'ha trobat cap informe en aquest període</p>
            </div>

            <!-- Empty status: no report -->
            <div v-else class="px-5 py-12 text-center">
                <div class="mx-auto mb-3 w-10 h-10 rounded-full bg-pmf-secondary flex items-center justify-center">
                    <FileText class="h-5 w-5 text-pmf-green" />
                </div>
                <p class="text-sm font-medium text-pmf-green-dark mb-1">Actualment, no consta cap informe clínic</p>
            </div>
        </div>
    </div>
</template>