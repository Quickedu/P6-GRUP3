<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import {
    CalendarDays,
    IdCard,
    Stethoscope,
    UserRoundSearch,
    CalendarRange,
    CircleCheck,
    Clock,
} from 'lucide-vue-next';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { dashboard, home } from '@/routes';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { ref, computed } from 'vue';
import { novaCitaStore as store } from '@/routes';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Pàgina Inici',
                href: home(),
            },
            {
                title: 'Crear nova cita',
                href: dashboard(),
            },
        ],
    },
});

interface Doctor {
    id: number;
    name: string;
    email: string;
    role: string;
}

interface TestType {
    id: number;
    name: string;
    time: number;
}

const props = defineProps<{
    doctors: Doctor[];
    testTypes: TestType[];
}>();

const dataCita = ref('');
const professional = ref<string | null>(null);
const showAll = ref(false);


const isAvaible = ref(false);

const cip = ref('');
const patientAvailable = ref(true);
const confirmedPatient = ref('');
const testMinutes = ref<number | null>(null);
const timeValidationMessage = ref('');
const estimatedMinutes = ref<number | null>(null);
const validatedClass = ref(
    'border-gray-200 focus:border-gray-900 focus:ring-gray-900',
);
const extraTime = ref(0);
const classTrue = 'disabled:border-green-500 disabled:focus:border-green-500 disabled:focus:ring-green-500';


function validatePatient() {
    const currentCip = cip.value.trim();
    if (!currentCip) {
        confirmedPatient.value = '';
        extraTime.value = 0;
        estimatedMinutes.value = testMinutes.value;
        validatedClass.value =
            'border-gray-200 focus:border-gray-900 focus:ring-gray-900';
        return;
    }
    fetch(`/patientConsult/${currentCip}`)
        .then((response) => response.json())
        .then((data) => {
            patientAvailable.value = Boolean(data.available);
            validatedClass.value = patientAvailable.value
                ? '!border-green-500 !focus:border-green-500 !focus:ring-green-500'
                : '!border-red-500 !focus:border-red-500 !focus:ring-red-500';

            confirmedPatient.value = patientAvailable.value ? currentCip : '';
            extraTime.value = data.data.number;
            estimatedMinutes.value =
                testMinutes.value !== null
                    ? testMinutes.value + extraTime.value
                    : null;
            isAvaible.value = true;
            console.log(isAvaible.value);
        });
}

function validateTimeTest(testId: number) {
    fetch(`/testConsult/${testId}`)
        .then((response) => response.json())
        .then((data) => {
            const status = data?.status;
            const message = data?.message;
            const timeTest = data?.data?.number;

            if (status !== 'success') {
                testMinutes.value = null;
                estimatedMinutes.value = null;
                timeValidationMessage.value =
                    message || "No s'ha pogut validar el temps de la prova.";
                return;
            }

            const testDuration = Number(timeTest) || 0;

            testMinutes.value = testDuration;
            estimatedMinutes.value = testDuration + extraTime.value;
            timeValidationMessage.value =
                message || `Temps estimat ${estimatedMinutes.value} min`;
        })
        .catch(() => {
            testMinutes.value = null;
            estimatedMinutes.value = null;
            timeValidationMessage.value =
                'Error de connexió validant el temps de la prova.';
        });
}

const resumPacient = computed(
    () => confirmedPatient.value || "Pendent d'identificació",
);

const resumData = computed(() =>
    dataCita.value
        ? new Date(dataCita.value).toLocaleDateString('ca-ES', {
              day: '2-digit',
              month: '2-digit',
              year: 'numeric',
          }) : 'Pendent de data',
);
const resumProfessional = computed(() =>
    professional.value ? professional.value : 'Pendent de professional',
);
const resumMinutsProva = computed(() =>
    testMinutes.value !== null ? `${testMinutes.value} min` : 'Pendent',
);
const resumMinutsNecessitats = computed(() => `${extraTime.value} min`);
const resumMinutsTotals = computed(() =>
    estimatedMinutes.value !== null ? `${estimatedMinutes.value} min` : 'Pendent',
);

const visibleItems = computed(() => {
    if (showAll.value) {
        return props.testTypes;
    }

    return props.testTypes.slice(0, 6);
});
</script>

<template>
    <Head title="Nova cita" />

    <div class="min-h-svh overflow-y-hidden">
        <div
            class="mt-6 flex h-full flex-1 flex-col gap-4 px-4 pb-8 sm:mt-8 sm:px-6 lg:mt-10 lg:px-8"
        >
            <form action="" @submit.prevent method="post">

                <div
                    class="mx-auto grid w-full max-w-7xl grid-cols-1 gap-6 lg:grid-cols-12"
                >
                    <div class="lg:col-span-8">
                        <h1
                            class="text-3xl font-extrabold tracking-tight text-pmf-primary sm:text-4xl"
                        >
                            Crear Nova Cita
                        </h1>
                        <p class="mt-2 text-muted-foreground">
                            Empleneu els detalls per programar una nova cita
                        </p>
                        <Card
                            class="mt-6 gap-4 border-0 bg-muted/50 py-6 shadow-none"
                        >
                            <CardHeader class="pb-0">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="rounded-lg bg-pmf-secondary p-2 text-pmf-primary"
                                    >
                                        <UserRoundSearch class="size-6" />
                                    </div>
                                    <div class="min-w-0">
                                        <CardTitle
                                            class="text-xl font-semibold"
                                        >
                                            Identificació del Pacient
                                        </CardTitle>
                                    </div>
                                </div>
                            </CardHeader>
                            <CardContent class="pt-0">
                                <div class="mt-4">
                                    <Label
                                        for="patient_id"
                                        class="text-xs font-semibold tracking-widest text-muted-foreground"
                                    >
                                        TARGETA SANITÀRIA (CIP)
                                    </Label>
                                    <div
                                        class="mt-3 flex w-full items-center gap-3"
                                    >
                                        <Input
                                            id="patient_id"
                                            name="patient_id"
                                            v-model="cip"
                                            @input="confirmedPatient = ''"
                                            type="text"
                                            class="h-9 flex-1 bg-background"
                                            :class="validatedClass"
                                            placeholder="Ex: ABCD 0123456789"
                                            autocomplete="off"
                                        />
                                        <button
                                            class="inline-flex h-9 shrink-0 cursor-pointer items-center justify-center rounded-md bg-pmf-primary px-5 py-3 text-pmf-secondary hover:bg-pmf-green"
                                            type="button"
                                            @click="validatePatient"
                                        >
                                            <CircleCheck class="mr-3 size-5" />
                                            Comprovar usuari
                                        </button>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>

                        <div class="lg:col-span-4">
                            <Card
                                class="mt-6 gap-4 border-0 bg-muted/50 py-6 shadow-none"
                            >
                                <CardHeader class="pb-0">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="rounded-lg bg-pmf-secondary p-2 text-pmf-primary"
                                        >
                                            <UserRoundSearch class="size-6" />
                                        </div>
                                        <div class="min-w-0">
                                            <CardTitle
                                                class="flex text-xl font-semibold"
                                            >
                                                Proves Diagnòstiques
                                            </CardTitle>
                                        </div>
                                    </div>
                                </CardHeader>
                                <div
                                    class="grid grid-cols-1 flex-wrap items-center justify-center gap-4 p-5 sm:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3"
                                >
                                    <label
                                        v-for="test in visibleItems"
                                        :key="test.id"
                                        class="cursor-pointer"
                                    >
                                        <input
                                        v-if="isAvaible === true"
                                            :class
                                            type="radio"
                                            max="1"
                                            name="test_id"
                                            class="peer sr-only"
                                            aria-label="Seleccionar categoria"
                                            @click="validateTimeTest(test.id)"
                                        />

                                        <div
                                            class="flex min-h-29 flex-col items-center justify-center rounded-2xl border-2 bg-white p-5 text-center text-black transition peer-checked:border-pmf-turquoise hover:bg-gray-50"
                                        >
                                            <span
                                                class="mt-2 text-sm font-bold"
                                                >{{ test.name }}</span
                                            >
                                        </div>
                                    </label>
                                    <div
                                        class="col-span-full flex items-center justify-center"
                                    >
                                        <button
                                            v-if="!showAll"
                                            @click="showAll = true"
                                            class="mt-3 flex cursor-pointer items-center justify-center rounded-full bg-pmf-green px-5 py-3 text-pmf-secondary transition hover:bg-pmf-green/90"
                                            type="button"
                                        >
                                            Veure totes les proves
                                        </button>
                                    </div>
                                    <p
                                        v-if="timeValidationMessage"
                                        class="col-span-full text-center text-sm text-muted-foreground"
                                    >
                                        {{ timeValidationMessage }}
                                        <span
                                            v-if="estimatedMinutes !== null"
                                            class="font-semibold text-pmf-primary"
                                        >
                                            ({{ estimatedMinutes }} min)
                                        </span>
                                    </p>
                                </div>
                            </Card>
                        </div>

                        <Card
                            class="mt-6 gap-4 border-0 bg-muted/50 py-6 shadow-none"
                        >
                            <CardHeader class="pb-0">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="rounded-lg bg-pmf-secondary p-2 text-pmf-primary"
                                    >
                                        <CalendarRange class="size-6" />
                                    </div>
                                    <div class="min-w-0">
                                        <CardTitle
                                            class="text-xl font-semibold"
                                        >
                                            Detalls de la Cita
                                        </CardTitle>
                                    </div>
                                </div>
                            </CardHeader>

                            <CardContent class="pt-0">
                                <div class="mt-4 grid gap-4 md:grid-cols-2">
                                    <div class="w-full">
                                        <Label
                                            for="date-time"
                                            class="text-xs font-semibold tracking-widest text-muted-foreground"
                                        >
                                            DATA DE LA CITA
                                        </Label>
                                        <Input
                                            id="date-time"
                                            type="date"
                                            name="date_time"
                                            v-model="dataCita"
                                            class="mt-3 h-9 bg-background"
                                            placeholder="Ex: ABCD 0123456789"
                                            autocomplete="off"
                                        />
                                    </div>
                                    <div class="w-full">
                                        <Label
                                            for="worker_id"
                                            class="mb-3 text-xs font-semibold tracking-widest text-muted-foreground"
                                        >
                                            METGE / DOCTOR
                                        </Label>
                                        <Select
                                            name="worker_id"
                                            id="worker_id"
                                            v-model="professional"
                                        >
                                            <SelectTrigger
                                                class="w-full bg-white"
                                            >
                                                <SelectValue
                                                    placeholder="Selecciona un professional de la salut"
                                                />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectItem
                                                    v-for="doctor in doctors"
                                                    :key="doctor.id"
                                                    :value="doctor.name"
                                                >
                                                    {{ doctor.name }}
                                                </SelectItem>
                                            </SelectContent>
                                        </Select>
                                    </div>
                                </div>
                                <div class="mt-4 w-full">
                                    <Label
                                        for="urgencia"
                                        class="mb-3 text-xs font-semibold tracking-widest text-muted-foreground"
                                    >
                                        PRIORITAT DE LA CITA
                                    </Label>
                                    <Select name="urgencia" id="urgencia">
                                        <SelectTrigger class="w-full bg-white">
                                            <SelectValue
                                                placeholder="Selecciona la prioritat de la cita"
                                            />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="preferent">
                                                Preferent
                                            </SelectItem>
                                            <SelectItem value="urgent">
                                                Urgent
                                            </SelectItem>
                                            <SelectItem value="none">
                                                No urgent
                                            </SelectItem>
                                        </SelectContent>
                                    </Select>
                                </div>
                            </CardContent>
                        </Card>

                        
                    </div>
                    <div class="lg:sticky lg:top-6 lg:col-span-4 lg:self-start">
                        <Card
                            class="gap-4 border-0 bg-pmf-secondary/50 py-6 shadow-none"
                        >
                            <CardHeader class="pb-0">
                                <CardTitle
                                    class="text-xs font-semibold tracking-widest text-pmf-green"
                                >
                                    RESUM DE LA CITA
                                </CardTitle>
                            </CardHeader>
                            <CardContent class="pt-0">
                                <div class="mt-2 space-y-5">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="rounded-full bg-pmf-secondary p-3 text-pmf-primary"
                                        >
                                            <IdCard class="size-5" />
                                        </div>
                                        <div class="min-w-0">
                                            <div class="text-sm text-pmf-green">
                                                Pacient
                                            </div>
                                            <div class="truncate font-semibold">
                                                {{ resumPacient }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-3">
                                        <div
                                            class="rounded-full bg-pmf-secondary p-3 text-pmf-primary"
                                        >
                                            <CalendarDays class="size-5" />
                                        </div>
                                        <div class="min-w-0">
                                            <div class="text-sm text-pmf-green">
                                                Data
                                            </div>
                                            <div class="truncate font-semibold">
                                                {{ resumData }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-3">
                                        <div
                                            class="rounded-full bg-pmf-secondary p-3 text-pmf-primary"
                                        >
                                            <Stethoscope class="size-5" />
                                        </div>
                                        <div class="min-w-0">
                                            <div class="text-sm text-pmf-green">
                                                Professional
                                            </div>
                                            <div class="truncate font-semibold">
                                                {{ resumProfessional }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-3">
                                        <div
                                            class="rounded-full bg-pmf-secondary p-3 text-pmf-primary"
                                        >
                                            <Clock class="size-5" />
                                    </div>
                                        <div class="min-w-0">
                                            <div class="text-sm text-pmf-green">
                                                Temps Estimat (Prova + necessitats)
                                            </div>
                                            <div class="truncate font-semibold">
                                                {{ resumMinutsProva }} + {{ resumMinutsNecessitats }} = {{ resumMinutsTotals }}
                                            </div>
                                        </div>
                                    </div>

                                    <div
                                        class="mt-6 flex w-full items-center justify-center"
                                    >
                                        <div class="w-full sm:w-auto">
                                            <button
                                                class="inline-flex w-full cursor-pointer items-center justify-center rounded-full bg-pmf-primary px-5 py-3 text-pmf-secondary hover:bg-pmf-green sm:w-auto"
                                                type="submit"
                                            >
                                                <CircleCheck
                                                    class="mr-3 size-5"
                                                />
                                                Confirmar Nova Cita
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>
