<script setup lang="ts">
import { ref, computed } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import { SquarePen } from 'lucide-vue-next';
import PatientModal from '@/pages/components/PatientModal.vue';
import searchInput from '@/pages/components/searchInput.vue';
import textNotify from '@/pages/components/textNotify.vue';
import Patients from '@/actions/App/Http/Controllers/Patients';

const search = ref('');
const page = usePage();
const user = computed(() => page.props.auth?.user)
const isSecretary = computed(() => user.value?.role === 'secretary');

interface Patient {
    id: number;
    name: string;
    nts: string;
    address: string;
    dni: string;
    phone: number;
    email: string;
}

const props = defineProps<{
    patients: Patient[];
}>();

const isPatientModalOpen = ref(false);
const selectedPatient = ref<Patient | null>(null);

const openEditModal = (patient: Patient) => {
    selectedPatient.value = patient;
    isPatientModalOpen.value = true;
};

const flashMessage = computed(() => (page.props.flash as any)?.message);
const flashStatus = computed(() => (page.props.flash as any)?.status);

const handleSearch = () => {
    router.get('/patientsList', { search: search.value }, {
        preserveState: true,
        replace: true,
    });
};  
console.log(props.patients);
</script>

<template>
    <div class="space-y-4 mx-5 mt-5">
        <textNotify
            class="mb-4"
            v-if="flashMessage"
            :message="flashMessage"
            :status="flashStatus"
        />
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <h2 class="text-2xl font-medium text-pmf-green-dark">Llistat de pacients</h2>
                <span class="rounded-full bg-pmf-secondary px-3 py-0.5 text-xs font-medium text-pmf-green-dark">
                    {{ props.patients.length }} {{ props.patients.length === 1 ? 'pacient' : 'pacients' }}
                </span>
            </div>
            <label for="search"></label>
            <searchInput
                v-model="search"
                placeholder="Cercar pacient..."
                @input="handleSearch"
            />
        </div>

        <!-- Table -->
        <div class="overflow-hidden rounded-xl border border-[#c5d8d5] bg-white">
            <table class="w-full text-sm">
                <thead class="bg-[#f0f7f6]">
                    <tr class="border-b border-[#c5d8d5] text-left text-[11px] font-medium uppercase tracking-wider text-pmf-green">
                        <th class="px-4 py-3">Núm.</th>
                        <th class="px-4 py-3">Nom i cognoms</th>
                        <th class="px-4 py-3">Adreça</th>
                        <th class="px-4 py-3">DNI</th>
                        <th class="px-4 py-3">NTS</th>
                        <th class="px-4 py-3">Telèfon</th>
                        <th class="px-4 py-3">Correu electrònic</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#eaf2f1]">
                    <tr
                        v-for="patient in props.patients"
                        :key="patient.id"
                        class="transition-colors hover:bg-[#f4f9f8]"
                    >
                        <td class="px-4 py-3">
                            <span class="rounded-full bg-pmf-secondary px-2 py-0.5 text-[11px] font-medium text-pmf-green">
                                {{ String(patient.id).padStart(3, '0') }}
                            </span>
                        </td>
                        <td class="px-4 py-3 font-medium text-pmf-green-dark">{{ patient.name }}</td>
                        <td class="px-4 py-3 text-pmf-grey-light">{{ patient.address }}</td>
                        <td class="px-4 py-3 text-pmf-grey-light">{{ patient.dni }}</td>
                        <td class="px-4 py-3 text-pmf-grey-light">{{ patient.nts }}</td>
                        <td class="px-4 py-3 text-pmf-grey-light">{{ patient.phone }}</td>
                        <td class="px-4 py-3 text-pmf-grey-light">{{ patient.email }}</td>
                        <td v-if="isSecretary" class="px-4 py-3">
                            <button
                                @click="openEditModal(patient)"
                                class="inline-flex items-center gap-1.5 rounded-lg border border-[#b0ceca] bg-pmf-primary px-2.5 py-1.5 text-md font-medium text-white transition-colors hover:bg-pmf-primary cursor-pointer"
                                title="Editar pacient"
                            >
                                <SquarePen class="h-3.5 w-3.5" />
                                Editar
                            </button>
                        </td>
                    </tr> 
                    <tr v-if="!props.patients || props.patients.length === 0">
                        <td colspan="8" class="px-5 py-12 text-center text-pmf-grey-light">
                            No hi ha cap pacient registrat.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <PatientModal v-model="isPatientModalOpen" :patient="selectedPatient" />
</template>