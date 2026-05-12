<script setup lang="ts">
import { computed } from 'vue';
import VueApexCharts from 'vue3-apexcharts';

const apexchart = VueApexCharts;

interface Stats {
    totalPatients?: number;
    totalWorkers?: number;
    totalReports?: number;
    totalNeeds?: number;
}

interface Props {
    stats: Stats;
    chartDays: string[];
    chartReports: number[];
    chartPatients: number[];
}

const props = defineProps<Props>();

const lineChartOptions = computed(() => ({
    chart: {
        type: 'line',
        toolbar: { show: true },
        animations: { enabled: true },
    },
    stroke: { curve: 'smooth', width: 2 },
    colors: ['#3B82F6'],
    xaxis: { categories: props.chartDays },
    yaxis: { title: { text: 'Informes' } },
    tooltip: { theme: 'light' },
    grid: { borderColor: '#e5e7eb' },
}));

const lineChartSeries = computed(() => [
    { name: 'Informes', data: props.chartReports },
]);

const barChartOptions = computed(() => ({
    chart: { type: 'bar', toolbar: { show: true } },
    colors: ['#10B981'],
    xaxis: { categories: props.chartDays },
    yaxis: { title: { text: 'Treballadors' } },
    tooltip: { theme: 'light' },
    grid: { borderColor: '#e5e7eb' },
}));

const barChartSeries = computed(() => [
    { name: 'Treballadors', data: props.chartPatients },
]);
</script>

<template>
    <div class="space-y-6 p-6">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                Admin Dashboard
            </h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">
                Benvingut! Aquí trobaràs un resum general del sistema
            </p>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
            <!-- Pacients -->
            <div
                class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm transition-all hover:shadow-md dark:border-gray-700 dark:bg-gray-800"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p
                            class="text-sm font-medium text-gray-600 dark:text-gray-400"
                        >
                            Pacients
                        </p>
                        <p
                            class="mt-2 text-3xl font-bold text-gray-900 dark:text-white"
                        >
                            {{ stats?.totalPatients ?? 0 }}
                        </p>
                    </div>
                    <div class="rounded-lg bg-blue-500 p-3">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="24"
                            height="24"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            class="lucide lucide-users-icon lucide-users text-white"
                        >
                            <path
                                d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"
                            />
                            <path d="M16 3.128a4 4 0 0 1 0 7.744" />
                            <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                            <circle cx="9" cy="7" r="4" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Treballadors -->
            <div
                class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm transition-all hover:shadow-md dark:border-gray-700 dark:bg-gray-800"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p
                            class="text-sm font-medium text-gray-600 dark:text-gray-400"
                        >
                            Treballadors
                        </p>
                        <p
                            class="mt-2 text-3xl font-bold text-gray-900 dark:text-white"
                        >
                            {{ stats?.totalWorkers ?? 0 }}
                        </p>
                    </div>
                    <div class="rounded-lg bg-green-500 p-3">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="24"
                            height="24"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            class="lucide lucide-users-round-icon lucide-users-round text-white"
                        >
                            <path d="M18 21a8 8 0 0 0-16 0" />
                            <circle cx="10" cy="8" r="5" />
                            <path
                                d="M22 20c0-3.37-2-6.5-4-8a5 5 0 0 0-.45-8.3"
                            />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Informes -->
            <div
                class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm transition-all hover:shadow-md dark:border-gray-700 dark:bg-gray-800"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p
                            class="text-sm font-medium text-gray-600 dark:text-gray-400"
                        >
                            Informes
                        </p>
                        <p
                            class="mt-2 text-3xl font-bold text-gray-900 dark:text-white"
                        >
                            {{ stats?.totalReports ?? 0 }}
                        </p>
                    </div>
                    <div class="rounded-lg bg-purple-500 p-3">
                        <svg
                            class="h-8 w-8 text-white"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                            />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Necessitats -->
            <div
                class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm transition-all hover:shadow-md dark:border-gray-700 dark:bg-gray-800"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p
                            class="text-sm font-medium text-gray-600 dark:text-gray-400"
                        >
                            Necessitats
                        </p>
                        <p
                            class="mt-2 text-3xl font-bold text-gray-900 dark:text-white"
                        >
                            {{ stats?.totalNeeds ?? 0 }}
                        </p>
                    </div>
                    <div class="rounded-lg bg-red-500 p-3">
                        <svg
                            class="h-8 w-8 text-white"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"
                            />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <!-- Line Chart - Informes -->
            <div
                class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800"
            >
                <h2
                    class="mb-4 text-lg font-semibold text-gray-900 dark:text-white"
                >
                    Informes dels últims 7 dies
                </h2>
                <apexchart
                    type="line"
                    :options="lineChartOptions"
                    :series="lineChartSeries"
                    height="300"
                />
            </div>

            <!-- Bar Chart - Pacients -->
            <div
                class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800"
            >
                <h2
                    class="mb-4 text-lg font-semibold text-gray-900 dark:text-white"
                >
                    Treballadors dels últims 7 dies
                </h2>
                <apexchart
                    type="bar"
                    :options="barChartOptions"
                    :series="barChartSeries"
                    height="300"
                />
            </div>
        </div>
    </div>
</template>
