<script setup lang="ts">
/**
 * Doctor page for composing and downloading a patient report PDF.
 * Uses the backend to generate the PDF from the entered report data.
 */
import { Head } from '@inertiajs/vue3';
import { CircleCheck, UserRoundSearch } from 'lucide-vue-next';
import { onBeforeUnmount, ref } from 'vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { dashboard, home, downloadReport as store } from '@/routes';
import '@vueup/vue-quill/dist/vue-quill.snow.css';

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

const props = defineProps<{
    patient: Patient;
    currentUser: {
        id: number;
        name: string;
    };
    workerId: number;
}>();

const localUser = ref({ ...props.currentUser });

interface Patient {
    id: number;
    nts: string;
    name: string;
    address: string;
    birth_date: string;
}

const patientForm = ref({
    id: props.patient?.id ?? null,
    nts: props.patient?.nts ?? '',
    name: props.patient?.name ?? '',
    address: props.patient?.address ?? '',
    birth_date: props.patient?.birth_date ?? '',
});

const centerRequested = ref('PMF');

/**
 * Clears patient fields when the patient lookup fails or the NTS is empty.
 */
function clearPatientFields() {
    patientForm.value.id = null;
    patientForm.value.name = '';
    patientForm.value.address = '';
    patientForm.value.birth_date = '';
}

/**
 * Loads a patient by NTS and fills in the read-only patient fields.
 * If the request fails, we clear the fields to avoid showing stale data.
 */
function loadPatient() {
    const nts = patientForm.value.nts.trim();

    if (!nts) {
        clearPatientFields();

        return;
    }

    fetch(`/formReport/patient/${encodeURIComponent(nts)}`)
        .then((response) => response.json())
        .then((data) => {
            const info = data as {
                id?: number;
                name?: string;
                address?: string;
                birth_date?: string;
            };

            patientForm.value.id = info.id ?? null;
            patientForm.value.name = info.name ?? '';
            patientForm.value.address = info.address ?? '';
            patientForm.value.birth_date = info.birth_date ?? '';
        })
        .catch(() => {
            clearPatientFields();
        });
}

const imageInput = ref<HTMLInputElement | null>(null);
const imagePreviews = ref<string[]>([]);

/**
 * Creates object-URL previews for the selected images and revokes any
 * previously created URLs to avoid memory leaks.
 */
function onImageChange(event: Event) {
    const files = Array.from((event.target as HTMLInputElement).files ?? []);

    for (const url of imagePreviews.value) {
        URL.revokeObjectURL(url);
    }

    imagePreviews.value = files.map((file) => URL.createObjectURL(file));
}

onBeforeUnmount(() => {
    for (const url of imagePreviews.value) {
        URL.revokeObjectURL(url);
    }
});
</script>

<template>
    <Head title="Nova cita" />

    <div class="min-h-svh overflow-y-hidden">
        <div
            class="mt-6 flex h-full flex-1 flex-col gap-4 px-4 pb-8 sm:mt-8 sm:px-6 lg:mt-10 lg:px-8"
        >
            <form
                v-bind="store.form()"
                enctype="multipart/form-data"
                class="flex flex-col gap-6"
                aria-describedby="appointment-form-help"
                target="_blank"
                :reset-on-success="true"
            >
                <input
                    type="hidden"
                    name="patient_id"
                    :value="patientForm.id ?? ''"
                />
                <input
                    type="hidden"
                    name="worker_id"
                    :value="props.workerId ?? ''"
                />

                <div
                    class="mx-auto grid w-full max-w-7xl grid-cols-1 gap-6 lg:grid-cols-12"
                >
                    <div class="flex flex-col lg:col-span-12">
                        <div class="gap-3">
                            <h1
                                class="text-3xl font-extrabold tracking-tight text-pmf-primary sm:text-4xl"
                            >
                                Informe del pacient
                            </h1>
                            <p class="mt-2 text-muted-foreground">
                                Empleu aquest formulari per generar un informe
                                en PDF del pacient. Aquest informe inclourà les
                                dades del pacient.
                            </p>
                        </div>
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
                                            Dades del pacient
                                        </CardTitle>
                                    </div>
                                </div>
                            </CardHeader>
                            <CardContent class="pt-0">
                                <div
                                    class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2"
                                >
                                    <div>
                                        <Label
                                            for="patient_id"
                                            class="mb-2 text-xs font-semibold tracking-widest text-gray-600"
                                        >
                                            NTS
                                        </Label>
                                        <div
                                            class="flex flex-col items-stretch gap-2 md:flex-row md:items-center"
                                        >
                                            <Input
                                                id="patient_id"
                                                v-model="patientForm.nts"
                                                type="text"
                                                name="nts"
                                                class="h-9 flex-1 bg-background"
                                                placeholder="Ex: NTSS1234567890"
                                                autocomplete="off"
                                                pattern="^[A-Za-z]{4}\d{10}$"
                                                title="El NTS ha de tenir el format: 4 lletres seguides de 10 dígits (Ex: NTSS1234567890)"
                                                aria-describedby="patient-check-help patient-check-status"
                                            />

                                            <button
                                                type="button"
                                                class="inline-flex h-9 w-full shrink-0 cursor-pointer items-center justify-center rounded-md bg-pmf-primary px-5 py-3 text-pmf-secondary hover:bg-pmf-green focus-visible:ring-2 focus-visible:ring-pmf-primary focus-visible:ring-offset-2 focus-visible:outline-none md:h-9 md:w-auto"
                                                @click="loadPatient"
                                                title="Comprovar pacient"
                                                aria-label="Comprovar pacient"
                                            >
                                                <UserRoundSearch
                                                    class="size-4"
                                                />
                                                Comprovar
                                            </button>
                                        </div>
                                    </div>

                                    <div>
                                        <Label
                                            for="names"
                                            class="mb-2 text-xs font-semibold tracking-widest text-gray-600"
                                        >
                                            Nom i cognoms
                                        </Label>
                                        <Input
                                            id="names"
                                            v-model="patientForm.name"
                                            type="text"
                                            name="name"
                                            class="block h-9 flex-1 cursor-not-allowed bg-background"
                                            placeholder="Ex: John Doe"
                                            readonly
                                            autocomplete="off"
                                            aria-describedby="patient-check-help patient-check-status"
                                        />
                                    </div>

                                    <div>
                                        <Label
                                            for="address"
                                            class="mb-2 text-xs font-semibold tracking-widest text-gray-600"
                                        >
                                            Adreça
                                        </Label>
                                        <Input
                                            id="address"
                                            v-model="patientForm.address"
                                            type="text"
                                            name="address"
                                            readonly
                                            class="block h-9 flex-1 cursor-not-allowed bg-background"
                                            placeholder="Ex: Carrer de l'Exemple, 123"
                                            autocomplete="off"
                                            aria-describedby="patient-check-help patient-check-status"
                                        />
                                    </div>

                                    <div>
                                        <Label
                                            for="birth_date"
                                            class="mb-2 text-xs font-semibold tracking-widest text-gray-600"
                                        >
                                            Data de naixement
                                        </Label>
                                        <Input
                                            id="birth_date"
                                            v-model="patientForm.birth_date"
                                            type="date"
                                            name="birth_date"
                                            readonly
                                            class="block h-9 flex-1 cursor-not-allowed bg-background"
                                            placeholder="Ex: 01/01/1990"
                                            autocomplete="off"
                                            aria-describedby="patient-check-help patient-check-status"
                                        />
                                    </div>
                                </div>
                            </CardContent>
                        </Card>

                        <div>
                            <Card class="mt-6 gap-4 border-0 shadow-none">
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
                                                Detalls de la sol·licitud
                                            </CardTitle>
                                        </div>
                                    </div>
                                </CardHeader>
                                <CardContent class="pt-0">
                                    <div
                                        class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2"
                                    >
                                        <div>
                                            <Label
                                                for="center_requested"
                                                class="mb-2 text-xs font-semibold tracking-widest text-gray-600"
                                            >
                                                Centre sol·licitant
                                            </Label>
                                            <Input
                                                id="center_requested"
                                                type="text"
                                                name="center_requested"
                                                class="h-9 flex-1 bg-background"
                                                placeholder="PMF el centre"
                                                autocomplete="off"
                                                required
                                                aria-required="true"
                                                aria-describedby="patient-check-help patient-check-status"
                                                v-model="centerRequested"
                                            />
                                        </div>

                                        <div>
                                            <Label
                                                for="center_destination"
                                                class="mb-2 text-xs font-semibold tracking-widest text-muted-foreground"
                                            >
                                                Centre de Destinació
                                            </Label>
                                            <Input
                                                id="center_destination"
                                                type="text"
                                                name="center_destination"
                                                class="h-9 flex-1 bg-background"
                                                placeholder="Ex: Hospital de l'Exemple"
                                                autocomplete="off"
                                                required
                                                aria-required="true"
                                                aria-describedby="patient-check-help patient-check-status"
                                            />
                                        </div>

                                        <div>
                                            <Label
                                                for="doctor_name"
                                                class="mb-2 text-xs font-semibold tracking-widest text-muted-foreground"
                                            >
                                                Metge Sol·licitant
                                            </Label>
                                            <Input
                                                id="doctor_name"
                                                type="text"
                                                name="doctor_name"
                                                class="h-9 flex-1 bg-background"
                                                placeholder="Ex: Dr. Elena García"
                                                autocomplete="off"
                                                required
                                                aria-required="true"
                                                aria-describedby="patient-check-help patient-check-status"
                                                v-model="localUser.name"
                                            />
                                        </div>

                                        <div
                                            class="grid grid-cols-1 gap-4 md:grid-cols-2"
                                        >
                                            <div class="flex flex-col gap-2">
                                                <Label
                                                    for="request_date"
                                                    class="text-xs font-semibold tracking-widest text-muted-foreground"
                                                >
                                                    Data Sol·licitud
                                                </Label>

                                                <Input
                                                    id="request_date"
                                                    type="date"
                                                    name="data_request"
                                                    class="h-9 w-full bg-background"
                                                    autocomplete="off"
                                                    required
                                                    aria-required="true"
                                                    aria-describedby="patient-check-help patient-check-status"
                                                />
                                            </div>

                                            <div class="flex flex-col gap-2">
                                                <Label
                                                    for="exploration_date"
                                                    class="text-xs font-semibold tracking-widest text-muted-foreground"
                                                >
                                                    Data Exploració
                                                </Label>
                                                <Input
                                                    id="exploration_date"
                                                    type="date"
                                                    name="data_exploration"
                                                    class="h-9 w-full bg-background"
                                                    autocomplete="off"
                                                    required
                                                    aria-required="true"
                                                    aria-describedby="patient-check-help patient-check-status"
                                                />
                                            </div>
                                        </div>
                                    </div>
                                </CardContent>
                            </Card>
                        </div>

                        <div>
                            <Card class="mt-6 gap-4 border-0 shadow-none">
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
                                                Contingut clínic
                                            </CardTitle>
                                        </div>
                                    </div>
                                </CardHeader>
                                <CardContent class="pt-0">
                                    <div class="mt-4 gap-4">
                                        <div>
                                            <Label
                                                for="reason"
                                                class="mb-2 text-xs font-semibold tracking-widest text-muted-foreground"
                                            >
                                                Motiu de la sol·licitud
                                            </Label>
                                            <textarea
                                                name="reason"
                                                id="reason"
                                                aria-describedby="Escriu el motiu de la sol·licitud"
                                                placeholder="Escriu el motiu de la sol·licitud"
                                                required
                                                class="min-h-24 w-full min-w-0 overflow-hidden rounded-md border border-pmf-primary/30 bg-transparent p-3 text-base shadow-xs transition-[color,box-shadow] outline-none focus-within:border-pmf-primary focus-within:ring-2 focus-within:ring-pmf-primary/30 md:text-sm"
                                            ></textarea>
                                        </div>
                                    </div>

                                    <div
                                        class="grid grid-cols-1 gap-4 md:grid-cols-2"
                                    >
                                        <div class="flex flex-col gap-2">
                                            <Label
                                                for="report"
                                                class="text-xs font-semibold tracking-widest text-muted-foreground"
                                            >
                                                Informe clínic
                                            </Label>

                                            <Input
                                                id="report"
                                                type="text"
                                                name="report"
                                                class="h-9 w-full bg-background"
                                                autocomplete="off"
                                                required
                                                aria-describedby="patient-check-help patient-check-status"
                                                placeholder="Escriu un resum de l'informe clínic"
                                            />
                                        </div>

                                        <div class="flex flex-col gap-2">
                                            <Label
                                                for="exploration_clinical"
                                                class="text-xs font-semibold tracking-widest text-muted-foreground"
                                            >
                                                Exploració clínica
                                            </Label>
                                            <Input
                                                id="exploration_clinical"
                                                type="text"
                                                name="exploration"
                                                class="h-9 w-full bg-background"
                                                autocomplete="off"
                                                required
                                                aria-required="true"
                                                aria-describedby="patient-check-help patient-check-status"
                                                placeholder="Escriu un resum de l'exploració clínica"
                                            />
                                        </div>
                                    </div>

                                    <div class="mt-6">
                                        <div
                                            class="flex items-end justify-between gap-4"
                                        >
                                            <div>
                                                <Label
                                                    for="images"
                                                    class="mb-2 block text-xs font-semibold tracking-widest text-muted-foreground"
                                                >
                                                    Imatges (opcional)
                                                </Label>
                                            </div>

                                            <button
                                                type="button"
                                                class="shrink-0 rounded-md bg-pmf-primary px-3 py-2 text-sm font-semibold text-pmf-secondary hover:bg-pmf-green focus-visible:ring-2 focus-visible:ring-pmf-primary focus-visible:ring-offset-2 focus-visible:outline-none"
                                                @click="imageInput?.click()"
                                            >
                                                Afegir imatges
                                            </button>
                                        </div>

                                        <input
                                            ref="imageInput"
                                            type="file"
                                            name="images[]"
                                            id="images"
                                            multiple
                                            accept="image/*"
                                            class="hidden"
                                            @change="onImageChange"
                                        />

                                        <div
                                            class="mt-4 rounded-lg border border-dashed border-pmf-primary/30 bg-muted/40 p-4"
                                        >
                                            <div
                                                v-if="
                                                    imagePreviews.length === 0
                                                "
                                                class="text-sm text-muted-foreground"
                                            >
                                                No hi ha cap imatge
                                                seleccionada.
                                            </div>

                                            <div
                                                v-else
                                                class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4"
                                            >
                                                <div
                                                    v-for="(
                                                        src, index
                                                    ) in imagePreviews"
                                                    :key="`${src}-${index}`"
                                                    class="overflow-hidden rounded-md border bg-background"
                                                >
                                                    <div
                                                        class="aspect-video w-full bg-muted"
                                                    >
                                                        <img
                                                            :src="src"
                                                            :alt="`Imatge ${index + 1}`"
                                                            class="h-full w-full object-cover"
                                                        />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </CardContent>
                            </Card>
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-center sm:justify-end">
                    <button
                        class="inline-flex w-full cursor-pointer items-center justify-center gap-2 rounded-full bg-pmf-primary px-6 py-3 font-semibold text-pmf-secondary shadow-md shadow-pmf-primary/30 transition-all duration-200 hover:-translate-y-0.5 hover:bg-pmf-green hover:shadow-lg hover:shadow-pmf-green/30 focus-visible:ring-2 focus-visible:ring-pmf-primary focus-visible:ring-offset-2 focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-60 disabled:shadow-none sm:w-auto"
                        type="submit"
                        :disabled="isSubmitting"
                    >
                        <CircleCheck class="size-5" aria-hidden="true" />
                        Crear informe
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
