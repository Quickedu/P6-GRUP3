<script setup lang="ts">
import { User } from 'lucide-vue-next';
import { patientDashboard } from '@/routes';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Inici', href: patientDashboard() },
            { title: 'Informació personal', href: '#' },
        ],
    },
});

interface PatientInfo {
    id: number;
    name: string;
    email: string;
    phone: string;
    address: string;
    birth_date: string;
    nts: string;
    dni: string;
}

const props = defineProps<{
    patient: PatientInfo;
}>();

const formatDate = (dateStr: string) => {
    if (!dateStr) {
        return '-';
    }

    const date = new Date(dateStr);

    return date.toLocaleDateString('ca-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
    });
};

const getAge = (birthDate: string) => {
    if (!birthDate) {
        return '-';
    }

    const today = new Date();
    const birth = new Date(birthDate);
    let age = today.getFullYear() - birth.getFullYear();
    const monthDiff = today.getMonth() - birth.getMonth();

    if (
        monthDiff < 0 ||
        (monthDiff === 0 && today.getDate() < birth.getDate())
    ) {
        age--;
    }

    return age;
};
</script>

<template>
    <div class="mx-5 mt-5 space-y-4">
        <!-- Títol -->
        <div class="flex items-center gap-3">
            <div class="rounded-lg bg-pmf-secondary p-2 text-pmf-primary">
                <User class="size-6" aria-hidden="true" />
            </div>
            <h2 class="text-2xl font-medium text-pmf-green-dark">
                Informació personal
            </h2>
        </div>

        <!-- Dades Personals -->
        <div
            class="overflow-hidden rounded-xl border border-[#c5d8d5] bg-white"
        >
            <div
                class="flex items-center border-b border-[#c5d8d5] bg-[#f0f7f6] px-5 py-3"
            >
                <h3
                    class="text-[11px] font-medium tracking-wider text-pmf-green uppercase"
                >
                    Dades del pacient
                </h3>
            </div>

            <div
                class="grid grid-cols-2 gap-x-12 gap-y-8 px-8 py-7 md:grid-cols-3"
            >
                <div>
                    <p
                        class="mb-1 text-[14px] font-medium tracking-wider text-pmf-green uppercase"
                    >
                        Nom i cognoms
                    </p>
                    <p class="text-sm text-pmf-green-dark">
                        {{ props.patient?.name || '-' }}
                    </p>
                </div>
                <div>
                    <p
                        class="mb-1 text-[14px] font-medium tracking-wider text-pmf-green uppercase"
                    >
                        DNI
                    </p>
                    <p class="text-sm text-pmf-green-dark">
                        {{ props.patient?.dni || '-' }}
                    </p>
                </div>
                <div>
                    <p
                        class="mb-1 text-[14px] font-medium tracking-wider text-pmf-green uppercase"
                    >
                        NTS
                    </p>
                    <p class="text-sm text-pmf-green-dark">
                        {{ props.patient?.nts || '-' }}
                    </p>
                </div>
                <div>
                    <p
                        class="mb-1 text-[14px] font-medium tracking-wider text-pmf-green uppercase"
                    >
                        Correu electrònic
                    </p>
                    <p class="text-sm text-pmf-green-dark">
                        {{ props.patient?.email || '-' }}
                    </p>
                </div>
                <div>
                    <p
                        class="mb-1 text-[14px] font-medium tracking-wider text-pmf-green uppercase"
                    >
                        Número de telèfon
                    </p>
                    <p class="text-sm text-pmf-green-dark">
                        {{ props.patient?.phone || '-' }}
                    </p>
                </div>
                <div>
                    <p
                        class="mb-1 text-[14px] font-medium tracking-wider text-pmf-green uppercase"
                    >
                        Data de naixement
                    </p>
                    <p class="text-sm text-pmf-green-dark">
                        {{ formatDate(props.patient?.birth_date) }}
                    </p>
                </div>
                <div>
                    <p
                        class="mb-1 text-[14px] font-medium tracking-wider text-pmf-green uppercase"
                    >
                        Edat
                    </p>
                    <p class="text-sm text-pmf-green-dark">
                        {{ getAge(props.patient?.birth_date) }} anys
                    </p>
                </div>
                <div class="col-span-2 md:col-span-3">
                    <p
                        class="mb-1 text-[14px] font-medium tracking-wider text-pmf-green uppercase"
                    >
                        Adreça
                    </p>
                    <p class="text-sm text-pmf-green-dark">
                        {{ props.patient?.address || '-' }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
