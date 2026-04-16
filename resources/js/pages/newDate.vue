<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import {
    CalendarDays,
    IdCard,
    Stethoscope,
    UserRoundSearch,
    CalendarRange,
    CircleCheck,
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


console.log('Doctors:', props.doctors);
console.log('Test Types:', props.testTypes);

const cip = ref('');
const dataCita = ref<number | null>(null);
const professional = ref<string | null>(null);

const resumPacient = computed(() =>
    cip.value.trim() ? cip.value.trim() : "Pendent d'identificació",
);
const resumData = computed(() =>
    dataCita.value ? new Date(dataCita.value).toLocaleString() : 'Pendent de data',
);
const resumProfessional = computed(() => 
    professional.value ? professional.value : 'Pendent de professional',
);

</script>

<template>
    <Head title="Nova cita" />

    <div class="min-h-svh overflow-y-hidden">
        <div class="mt-6 flex h-full flex-1 flex-col gap-4 px-4 pb-8 sm:mt-8 sm:px-6 lg:mt-10 lg:px-8">
        <form action="" v-on:submit.prevent="" method="post">
            <div class="mx-auto grid w-full max-w-7xl grid-cols-1 gap-6 lg:grid-cols-12">
                <div class="lg:col-span-8">
                    <h1 class="text-3xl font-extrabold tracking-tight text-pmf-primary sm:text-4xl">
                        Crear Nova Cita
                    </h1>
                    <p class="mt-2 text-muted-foreground">
                        Empleneu els detalls per programar una nova cita al
                        sistema Vital Flux.
                    </p>
                    <Card class="mt-6 gap-4 border-0 bg-muted/50 py-6 shadow-none">
                        <CardHeader class="pb-0">
                            <div class="flex items-start gap-3">
                                <div
                                    class="rounded-lg bg-pmf-secondary p-2 text-pmf-primary"
                                >
                                    <UserRoundSearch class="size-6" />
                                </div>
                                <div class="min-w-0">
                                    <CardTitle class="text-xl font-semibold">
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
                                <Input
                                    id="patient_id"
                                    v-model="cip"
                                    name="patient_id"
                                    type="text"
                                    class="mt-3 h-9 bg-background"
                                    placeholder="Ex: ABCD 0123456789"
                                    autocomplete="off"
                                />
                            </div>
                        </CardContent>
                    </Card>

                    <Card class="mt-6 gap-4 border-0 bg-muted/50 py-6 shadow-none">
                        <CardHeader class="pb-0">
                            <div class="flex items-start gap-3">
                                <div
                                    class="rounded-lg bg-pmf-secondary p-2 text-pmf-primary"
                                >
                                    <CalendarRange class="size-6" />
                                </div>
                                <div class="min-w-0">
                                    <CardTitle class="text-xl font-semibold">
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
                                        type="datetime-local"
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
                                        <SelectTrigger class="w-full bg-white">
                                            <SelectValue
                                                placeholder="Selecciona un professional de la salut"
                                            />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem v-for="doctor in doctors" :key="doctor.id" :value="doctor.name">
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
                                <Select
                                    name="urgencia"
                                    id="urgencia"
                                >
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

                <div class="lg:col-span-4">
                        <Card
                            class="mt-6 gap-4 border-0 bg-muted/50 py-6 shadow-none"
                        >
                            <CardHeader class="pb-0">
                                <div class="flex items-start gap-3">
                                    <div
                                        class="rounded-lg bg-pmf-secondary p-2 text-pmf-primary"
                                    >
                                        <UserRoundSearch class="size-6" />
                                    </div>
                                    <div class="min-w-0">
                                        <CardTitle
                                            class="text-xl font-semibold"
                                        >
                                            Proves Diagnòstiques
                                        </CardTitle>
                                    </div>
                                </div>
                            </CardHeader>
                            <div class="grid grid-cols-1 flex-wrap gap-4 p-5 sm:grid-cols-2 lg:grid-cols-2 xl:grid-cols-4">
                                <label v-for="test in testTypes" :key="test.id" class="cursor-pointer">
                                    <input
                                        type="checkbox"
                                        name="test_id"
                                        class="peer sr-only"
                                        aria-label="Seleccionar categoria"
                                    />

                                    <div
                                        class="flex min-h-29 flex-col items-center justify-center text-center rounded-2xl border-2 border-black bg-white p-5 text-black transition hover:bg-gray-200 peer-checked:border-black peer-checked:bg-pmf-turquoise"
                                    >
                                        <span class="mt-2 text-sm font-bold"
                                            >{{ test.name }}</span
                                        >
                                    </div>
                                </label>
                            </div>
                        </Card>
                    </div>
                </div>
                <div class="lg:col-span-4 lg:sticky lg:top-6 lg:self-start">
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
                                    <div class="min-w-0">
                                        <button class="inline-flex w-full items-center justify-center rounded-full bg-pmf-primary px-5 py-3 text-pmf-secondary hover:bg-pmf-green sm:w-auto cursor-pointer"
                                        type="submit"
                                        >
                                            <CircleCheck class="size-5 mr-3" />
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
