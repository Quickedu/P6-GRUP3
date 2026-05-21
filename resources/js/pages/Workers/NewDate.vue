<script setup lang="ts">
import { Form, Head, usePage } from '@inertiajs/vue3';
import { QuillEditor } from '@vueup/vue-quill';
import {
    CalendarDays,
    IdCard,
    Stethoscope,
    UserRoundSearch,
    CalendarRange,
    CircleCheck,
    Clock,
    Microscope,
    FileText,
} from 'lucide-vue-next';
import { ref, computed } from 'vue';
import InputError from '@/components/InputError.vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { useNewDateAppointment } from '@/composables/useNewDateAppointment';
import textNotify from '@/pages/components/textNotify.vue';
import { dashboard, home, novaCitaStore as store } from '@/routes';
import '@vueup/vue-quill/dist/vue-quill.snow.css';

const page = usePage();

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
    worker_id: number;
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

const showAll = ref(false);
const description = ref('');
const {
    dataCita,
    professionalId,
    patientId,
    selectedTestId,
    isAvaible,
    cip,
    confirmedPatient,
    testMinutes,
    timeValidationMessage,
    estimatedMinutes,
    validatedClass,
    slotsMessage,
    startTimeOptions,
    selectedStartTime,
    extraTime,
    selectedDateTime,
    validatePatient,
    validateTimeTest,
    validateDoctorSlots,
} = useNewDateAppointment();

const flashMessage = computed(() => (page.props.flash as any)?.message);
const flashStatus = computed(() => (page.props.flash as any)?.status);

const resumPacient = computed(
    () => confirmedPatient.value || "Pendent d'identificació",
);

const resumProfessional = computed(() => {
    const selectedDoctor = props.doctors.find(
        (doctor) => doctor.id.toString() === professionalId.value,
    );

    return selectedDoctor?.name || 'Pendent de professional';
});

const selectedWorkerId = computed(() => {
    const selectedDoctor = props.doctors.find(
        (doctor) => doctor.id.toString() === professionalId.value,
    );

    return selectedDoctor ? selectedDoctor.worker_id.toString() : '';
});

const canSubmit = computed(() =>
    Boolean(
        patientId.value &&
        selectedTestId.value &&
        selectedDateTime.value &&
        selectedWorkerId.value &&
        estimatedMinutes.value !== null,
    ),
);

const submitHint = computed(() => {
    const missingSteps: string[] = [];

    if (!patientId.value) {
        missingSteps.push('validar pacient');
    }

    if (!selectedTestId.value) {
        missingSteps.push('seleccionar prova');
    }

    if (!professionalId.value) {
        missingSteps.push('seleccionar doctor');
    }

    if (!selectedDateTime.value) {
        missingSteps.push("escollir hora d'inici");
    }

    if (missingSteps.length === 0) {
        return '';
    }

    return `Per confirmar la cita falta: ${missingSteps.join(', ')}`;
});

const plainDescription = computed(() =>
    description.value
        .replace(/<[^>]*>/g, ' ')
        .replace(/&nbsp;/gi, ' ')
        .replace(/\s+/g, ' ')
        .trim(),
);

const descriptionForSubmit = computed(() =>
    plainDescription.value
);

const resumMinutsProva = computed(() =>
    testMinutes.value !== null ? `${testMinutes.value} min` : 'Pendent',
);
const resumMinutsNecessitats = computed(() => `${extraTime.value} min`);

const resumDataTime = computed(() => {
    if (selectedDateTime.value) {
        const date = new Date(selectedDateTime.value);

        return date.toLocaleString('ca-ES', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
        });
    }

    return 'Pendent de data i hora';
});

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
            <Form
                v-bind="store.form()"
                v-slot="{ errors, processing }"
                class="flex flex-col gap-6"
                aria-describedby="appointment-form-help"
                :reset-on-success="true"
            >
                <p id="appointment-form-help" class="sr-only">
                    Completa les dades del pacient, prova i franja horaria abans
                    de confirmar la cita.
                </p>
                <input
                    type="hidden"
                    name="date_time"
                    :value="selectedDateTime"
                />
                <input type="hidden" name="time" :value="estimatedMinutes" />
                <input type="hidden" name="estat" value="programada" />
                <input
                    type="hidden"
                    name="worker_id"
                    :value="selectedWorkerId"
                />
                <input
                    type="hidden"
                    name="description"
                    :value="descriptionForSubmit"
                />

                <div
                    class="mx-auto grid w-full max-w-7xl grid-cols-1 gap-6 lg:grid-cols-12"
                >
                    <div class="flex flex-col lg:col-span-8">
                        <div class="">
                            <h1
                                class="text-3xl font-extrabold tracking-tight text-pmf-primary sm:text-4xl"
                            >
                                Crear Nova Cita
                            </h1>
                            <p class="mt-2 text-muted-foreground">
                                Empleneu els detalls per programar una nova
                                citatext
                            </p>
                        </div>
                        <!-- Detall de la cita -->
                        <textNotify
                            class="mb-4"
                            v-if="flashMessage"
                            :message="flashMessage"
                            :status="flashStatus"
                        />
                        <Card
                            class="mt-6 gap-4 border-0 bg-muted py-6 shadow-none"
                        >
                            <CardHeader class="pb-0">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="rounded-lg bg-pmf-secondary p-2 text-pmf-primary"
                                    >
                                        <UserRoundSearch
                                            class="size-6"
                                            aria-hidden="true"
                                        />
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
                                        class="text-xs font-semibold tracking-widest text-gray-600"
                                    >
                                        TARGETA SANITÀRIA (CIP)
                                    </Label>
                                    <div
                                        class="mt-3 flex w-full flex-col gap-3 md:flex-row md:items-center"
                                    >
                                        <Input
                                            id="patient_id"
                                            v-model="cip"
                                            @input="confirmedPatient = ''"
                                            type="text"
                                            class="h-9 flex-1 bg-background"
                                            :class="validatedClass"
                                            placeholder="Ex: ABCD 0123456789"
                                            autocomplete="off"
                                            required
                                            aria-required="true"
                                            aria-describedby="patient-check-help patient-check-status"
                                        />
                                        <input
                                            type="hidden"
                                            name="patient_id"
                                            :value="patientId ?? ''"
                                        />
                                        <button
                                            class="inline-flex h-9 w-full shrink-0 cursor-pointer items-center justify-center rounded-md bg-pmf-primary px-5 py-3 text-pmf-secondary hover:bg-pmf-green focus-visible:ring-2 focus-visible:ring-pmf-primary focus-visible:ring-offset-2 focus-visible:outline-none md:w-auto"
                                            type="button"
                                            @click="validatePatient"
                                            aria-describedby="patient-check-help"
                                        >
                                            <CircleCheck
                                                class="mr-3 size-5"
                                                aria-hidden="true"
                                            />
                                            Comprovar usuari
                                        </button>
                                    </div>
                                    <InputError
                                        :message="errors.patient_id"
                                        class="mt-2"
                                    />
                                </div>
                            </CardContent>
                        </Card>

                        <div class="lg:col-span-4">
                            <Card
                                class="mt-6 gap-4 border-0 bg-muted py-6 shadow-none"
                            >
                                <CardHeader class="pb-0">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="rounded-lg bg-pmf-secondary p-2 text-pmf-primary"
                                        >
                                            <Microscope
                                                class="size-6"
                                                aria-hidden="true"
                                            />
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
                                    :aria-describedby="
                                        isAvaible
                                            ? 'test-selection-status'
                                            : 'test-selection-help'
                                    "
                                >
                                    <legend class="sr-only">
                                        Seleccio de proves diagnostiques
                                    </legend>
                                    <label
                                        v-for="test in visibleItems"
                                        :key="test.id"
                                        :class="
                                            isAvaible
                                                ? 'cursor-pointer'
                                                : 'cursor-not-allowed opacity-60'
                                        "
                                    >
                                        <input
                                            type="radio"
                                            max="1"
                                            name="test_id"
                                            :value="test.id"
                                            v-model="selectedTestId"
                                            class="peer sr-only"
                                            :aria-label="`Seleccionar prova ${test.name}`"
                                            :disabled="!isAvaible"
                                            :aria-disabled="!isAvaible"
                                            @change="validateTimeTest(test.id)"
                                        />

                                        <div
                                            class="flex min-h-29 flex-col items-center justify-center rounded-2xl border-2 bg-white p-5 text-center text-black transition peer-checked:border-pmf-turquoise peer-focus-visible:ring-2 peer-focus-visible:ring-pmf-primary peer-focus-visible:ring-offset-2 hover:bg-gray-50"
                                        >
                                            <span
                                                class="mt-2 text-sm font-bold"
                                                >{{ test.name }}</span
                                            >
                                        </div>
                                    </label>
                                    <p
                                        v-if="!isAvaible"
                                        id="test-selection-help"
                                        class="col-span-full text-center text-sm text-gray-600"
                                        role="status"
                                        aria-live="polite"
                                    >
                                        Primer valida el pacient per activar la
                                        selecció de proves.
                                    </p>
                                    <div
                                        class="col-span-full flex items-center justify-center"
                                    >
                                        <button
                                            v-if="!showAll"
                                            @click="showAll = true"
                                            class="mt-3 flex cursor-pointer items-center justify-center rounded-full bg-pmf-green px-5 py-3 text-pmf-secondary transition hover:bg-pmf-green/90 focus-visible:ring-2 focus-visible:ring-pmf-primary focus-visible:ring-offset-2 focus-visible:outline-none"
                                            type="button"
                                        >
                                            Veure totes les proves
                                        </button>
                                    </div>
                                    <p
                                        v-if="timeValidationMessage"
                                        id="test-selection-status"
                                        class="col-span-full text-center text-sm text-muted-foreground"
                                        role="status"
                                        aria-live="polite"
                                    >
                                        Temps estimat
                                        <span
                                            v-if="estimatedMinutes !== null"
                                            class="font-semibold text-pmf-primary"
                                        >
                                            {{ estimatedMinutes }} min
                                        </span>
                                    </p>
                                    <InputError
                                        :message="errors.test_id"
                                        class="col-span-full text-center"
                                    />
                                </div>
                            </Card>
                        </div>

                        <Card
                            class="mt-6 gap-4 border-0 bg-muted py-6 shadow-none"
                        >
                            <CardHeader class="pb-0">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="rounded-lg bg-pmf-secondary p-2 text-pmf-primary"
                                    >
                                        <FileText
                                            class="size-6"
                                            aria-hidden="true"
                                        />
                                    </div>
                                    <div class="min-w-0">
                                        <CardTitle
                                            class="text-xl font-semibold"
                                        >
                                            Observacions de la Cita
                                        </CardTitle>
                                    </div>
                                </div>
                            </CardHeader>

                            <CardContent class="pt-0">
                                <div class="mt-4 grid gap-4">
                                    <Label
                                        for="description-editor"
                                        class="text-xs font-semibold tracking-widest text-gray-600"
                                    >
                                        OBSERVACIONS DE LA CITA
                                    </Label>

                                    <QuillEditor
                                        id="description-editor"
                                        v-model:content="description"
                                        contentType="html"
                                        theme="snow"
                                        rows="4"
                                        class="min-h-24 w-full min-w-0 overflow-hidden rounded-md border border-pmf-primary/30 bg-transparent text-base shadow-xs transition-[color,box-shadow] outline-none focus-within:border-pmf-primary focus-within:ring-2 focus-within:ring-pmf-primary/30 md:text-sm [&_.ql-container.ql-snow]:border-0 [&_.ql-editor]:relative [&_.ql-editor]:min-h-32 [&_.ql-editor]:px-3 [&_.ql-editor]:py-2 [&_.ql-editor]:text-foreground [&_.ql-editor.ql-blank::before]:top-2 [&_.ql-editor.ql-blank::before]:right-3 [&_.ql-editor.ql-blank::before]:left-3 [&_.ql-editor.ql-blank::before]:text-muted-foreground [&_.ql-editor.ql-blank::before]:not-italic [&_.ql-fill]:fill-pmf-primary [&_.ql-picker]:text-pmf-primary [&_.ql-snow_.ql-picker-options]:border-pmf-primary/20 [&_.ql-stroke]:stroke-pmf-primary [&_.ql-toolbar.ql-snow]:border-0 [&_.ql-toolbar.ql-snow]:border-b [&_.ql-toolbar.ql-snow]:border-pmf-primary/20 [&_.ql-toolbar.ql-snow]:bg-pmf-secondary/40"
                                        placeholder="Afegeix observacions de la cita..."
                                        aria-label="Observacions de la cita"
                                        aria-describedby="description-help"
                                    />
                                    <p
                                        id="description-help"
                                        class="text-xs text-gray-600"
                                    >
                                        Aquest camp es opcional i permet afegir
                                        notes cliniques per al professional.
                                    </p>
                                    <InputError :message="errors.description" />
                                </div>
                            </CardContent>
                        </Card>

                        <Card
                            class="mt-6 gap-4 border-0 bg-muted py-6 shadow-none"
                        >
                            <CardHeader class="pb-0">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="rounded-lg bg-pmf-secondary p-2 text-pmf-primary"
                                    >
                                        <CalendarRange
                                            class="size-6"
                                            aria-hidden="true"
                                        />
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
                                <div class="mt-4 grid gap-4 md:grid-cols-3">
                                    <div class="w-full">
                                        <Label
                                            for="date-time"
                                            class="text-xs font-semibold tracking-widest text-gray-600"
                                        >
                                            DATA DE LA CITA
                                        </Label>
                                        <Input
                                            id="date-time"
                                            type="date"
                                            :min="
                                                new Date()
                                                    .toISOString()
                                                    .split('T')[0]
                                            "
                                            v-model="dataCita"
                                            class="mt-3 h-9 bg-background"
                                            placeholder="Ex: ABCD 0123456789"
                                            autocomplete="off"
                                            required
                                            aria-required="true"
                                        />
                                    </div>
                                    <div class="w-full">
                                        <Label
                                            id="worker_id_label"
                                            for="worker_id"
                                            class="mb-3 text-xs font-semibold tracking-widest text-gray-600"
                                        >
                                            DOCTOR
                                        </Label>
                                        <Select
                                            id="worker_id"
                                            v-model="professionalId"
                                        >
                                            <SelectTrigger
                                                id="worker_id_trigger"
                                                aria-labelledby="worker_id_label worker_id_trigger"
                                                aria-required="true"
                                                class="w-full bg-white"
                                            >
                                                <SelectValue
                                                    placeholder="Selecciona un professional"
                                                />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectItem
                                                    v-for="doctor in doctors"
                                                    :key="doctor.id"
                                                    :value="
                                                        doctor.id.toString()
                                                    "
                                                >
                                                    {{ doctor.name }}
                                                </SelectItem>
                                            </SelectContent>
                                        </Select>
                                    </div>
                                    <button
                                        class="mt-7 inline-flex h-9 shrink-0 cursor-pointer items-center justify-center rounded-md bg-pmf-primary px-5 py-3 text-sm text-pmf-secondary hover:bg-pmf-green focus-visible:ring-2 focus-visible:ring-pmf-primary focus-visible:ring-offset-2 focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-60"
                                        type="button"
                                        @click="validateDoctorSlots"
                                        :disabled="
                                            !professionalId ||
                                            !dataCita ||
                                            estimatedMinutes === null
                                        "
                                    >
                                        <CircleCheck
                                            class="mr-3 size-5"
                                            aria-hidden="true"
                                        />
                                        Veure dates disponibles
                                    </button>
                                </div>
                                <div
                                    class="mt-4 rounded-md border border-pmf-primary/20 bg-white p-4"
                                >
                                    <p
                                        id="slots-heading"
                                        class="text-sm font-medium text-pmf-primary"
                                    >
                                        Franges horaries del doctor
                                    </p>

                                    <p
                                        id="slots-message"
                                        class="mt-2 text-sm text-muted-foreground"
                                        role="status"
                                        aria-live="polite"
                                    >
                                        {{ slotsMessage }}
                                    </p>

                                    <div
                                        class="mt-3 flex flex-wrap gap-2"
                                        aria-labelledby="slots-heading"
                                        aria-describedby="slots-message"
                                    >
                                        <legend class="sr-only">
                                            Seleccio d'hora d'inici disponible
                                        </legend>
                                        <label
                                            v-for="startTime in startTimeOptions"
                                            :key="startTime"
                                            class="cursor-pointer"
                                        >
                                            <input
                                                v-model="selectedStartTime"
                                                type="radio"
                                                name="start_time"
                                                :value="startTime"
                                                class="peer sr-only"
                                                :aria-label="`Seleccionar hora ${startTime}`"
                                            />
                                            <span
                                                class="inline-flex items-center rounded-md border border-pmf-primary/25 px-3 py-1.5 text-sm text-pmf-primary transition peer-checked:border-pmf-primary peer-checked:bg-pmf-primary peer-checked:text-pmf-secondary peer-focus-visible:ring-2 peer-focus-visible:ring-pmf-primary peer-focus-visible:ring-offset-2 hover:bg-pmf-secondary/70"
                                            >
                                                {{ startTime }}
                                            </span>
                                        </label>
                                    </div>

                                    <p
                                        v-if="selectedDateTime"
                                        class="mt-3 text-sm font-medium text-pmf-primary"
                                        role="status"
                                        aria-live="polite"
                                    >
                                        Data i hora seleccionada:
                                        {{ selectedDateTime }}
                                    </p>
                                    <InputError
                                        :message="errors.date_time"
                                        class="mt-2"
                                    />
                                </div>

                                <div class="mt-4 w-full lg:mb-25">
                                    <Label
                                        id="urgencia_label"
                                        for="urgencia"
                                        class="mb-3 text-xs font-semibold tracking-widest text-gray-600"
                                    >
                                        PRIORITAT DE LA CITA
                                    </Label>
                                    <Select id="urgencia" name="urgencia">
                                        <SelectTrigger
                                            id="urgencia_trigger"
                                            aria-labelledby="urgencia_label urgencia_trigger"
                                            aria-required="true"
                                            class="w-full bg-white"
                                        >
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
                                            <SelectItem value="no urgent">
                                                No urgent
                                            </SelectItem>
                                        </SelectContent>
                                    </Select>
                                    <InputError
                                        :message="errors.urgencia"
                                        class="mt-2"
                                    />
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                    <div
                        class="lg:sticky lg:top-34 lg:col-span-4 lg:self-start"
                    >
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
                                            <IdCard
                                                class="size-5"
                                                aria-hidden="true"
                                            />
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
                                            <CalendarDays
                                                class="size-5"
                                                aria-hidden="true"
                                            />
                                        </div>
                                        <div class="min-w-0">
                                            <div class="text-sm text-pmf-green">
                                                Data
                                            </div>
                                            <div class="truncate font-semibold">
                                                {{ resumDataTime }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-3">
                                        <div
                                            class="rounded-full bg-pmf-secondary p-3 text-pmf-primary"
                                        >
                                            <Stethoscope
                                                class="size-5"
                                                aria-hidden="true"
                                            />
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
                                            <Clock
                                                class="size-5"
                                                aria-hidden="true"
                                            />
                                        </div>
                                        <div class="min-w-0">
                                            <div class="text-sm text-pmf-green">
                                                Temps Estimat de la Cita
                                            </div>
                                            <div class="truncate font-semibold">
                                                {{ resumMinutsProva }}
                                                <span class="text-pmf-green"
                                                    >(+{{
                                                        resumMinutsNecessitats
                                                    }})</span
                                                >
                                            </div>
                                        </div>
                                    </div>

                                    <div
                                        class="mt-6 flex w-full items-center justify-center"
                                    >
                                        <div class="w-full sm:w-auto">
                                            <p
                                                v-if="submitHint"
                                                class="mb-3 text-sm text-muted-foreground"
                                                role="status"
                                                aria-live="polite"
                                            >
                                                {{ submitHint }}
                                            </p>

                                            <button
                                                class="inline-flex w-full cursor-pointer items-center justify-center rounded-full bg-pmf-primary px-5 py-3 text-pmf-secondary hover:bg-pmf-green focus-visible:ring-2 focus-visible:ring-pmf-primary focus-visible:ring-offset-2 focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-60 sm:w-auto"
                                                type="submit"
                                                :disabled="
                                                    !canSubmit || processing
                                                "
                                            >
                                                <CircleCheck
                                                    class="mr-3 size-5"
                                                    aria-hidden="true"
                                                />
                                                {{
                                                    processing
                                                        ? 'Guardant...'
                                                        : 'Confirmar Nova Cita'
                                                }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                </div>
            </Form>
        </div>
    </div>
</template>
