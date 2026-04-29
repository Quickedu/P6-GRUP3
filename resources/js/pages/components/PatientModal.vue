<!-- <script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import { X, Mail, Phone } from 'lucide-vue-next';
import { watch, onUnmounted, ref} from 'vue';
import { patientsNeeds } from '@/routes';

interface Patient {
    id: number;
    name: string;
    nts: string;
    address: string;
    dni: string;
    phone: number;
    email: string;
}

interface Need {
    id: number;
    name: string;
}

const props = defineProps<{
    modelValue: boolean;
    patient: Patient | null;
}>();

const emit = defineEmits<{
    (e: 'update:modelValue', value: boolean): void;
}>();

const form = useForm<Patient>({
    id: 0,
    name: '',
    address: '',
    dni: '',
    nts: '',
    phone: 0,
    email: '',
});

watch(() => props.modelValue, (isOpen) => {
    if (isOpen && props.patient) {
        form.defaults(props.patient);
        form.reset();
    }
});

const closeModal = () => {
    emit('update:modelValue', false);
    form.reset();
    form.clearErrors();
};

const submitForm = () => {
    form.post(`/patients/${form.id}`, {
        onSuccess: () => closeModal(),
    });
};

const handleKeyPress = (event: KeyboardEvent) => {
    if (event.key === 'Escape') closeModal();
};

const activeTab = ref<'info' | 'needs'>('info');
const needs = ref<Need[]>([]);

watch(() => props.modelValue, (isOpen) => {
    isOpen
        ? document.addEventListener('keydown', handleKeyPress)
        : document.removeEventListener('keydown', handleKeyPress);
});
onUnmounted(() => document.removeEventListener('keydown', handleKeyPress));

//Ajax patient needs
async function patientNeedsList() {
  try {
    const response = await fetch(`/patients/needs/{$id}`);
    const need = await response.json();
    //need.name

  } catch (error) {
    console.error("Error:", error);
  }
}
</script>

<template>
    <div
        v-if="modelValue"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm"
        @click.self="closeModal"
    >
        <div class="relative mx-4 w-full max-w-2xl overflow-hidden rounded-2xl border border-[#b0ceca] bg-white shadow-xl">


            <div class="flex items-center justify-between border-b border-[#deecea] bg-[#f0f7f6] px-6 py-3">
                <div class="flex items-center gap-3">
                    <div>
                        <h3 class="text-[19px] font-medium text-pmf-green-dark">Dades del pacient</h3>
                    </div>
                </div>
                <button @click="closeModal"
                    aria-label="Tancar"
                    class="rounded-lg p-1.5 text-pmf-grey-light transition-colors hover:bg-pmf-secondary cursor-pointer"
                >
                    <X class="h-4 w-4" aria-hidden="true" />
                </button>
            </div>

            <form @submit.prevent="submitForm">
                <div class="max-h-[65vh] overflow-y-auto px-6 py-5">

                    <p class="mb-3 flex items-center gap-2 text-[11px] font-medium uppercase tracking-wider text-pmf-grey-light">
                        Informació personal
                    </p>

                    <div class="grid grid-cols-6 gap-x-4 gap-y-4">

                        <div class="col-span-3">
                            <label for="name" class="mb-1 block text-[11px] font-medium uppercase tracking-wider text-pmf-green">Nom i cognoms</label>
                            <input
                                id="name"
                                v-model="form.name"
                                type="text"
                                disabled
                                class="block w-full cursor-not-allowed rounded-lg border border-[#c5d8d5] bg-[#D9D9D9] px-3 py-2 text-sm text-pmf-green-dark outline-none"
                            />
                            <InputError :message="form.errors.name" class="mt-1" />
                        </div>

                        <div class="col-span-3">
                            <label for="address" class="mb-1 block text-[11px] font-medium uppercase tracking-wider text-pmf-green">Adreça</label>
                            <input
                                id="address"
                                v-model="form.address"
                                type="text"
                                class="block w-full rounded-lg border border-[#c5d8d5] px-3 py-2 text-sm text-pmf-green-dark outline-none focus:border-pmf-turquoise"
                            />
                            <InputError :message="form.errors.address" class="mt-1" />
                        </div>

                        <div class="col-span-3">
                            <label for="email" class="mb-1 block text-[11px] font-medium uppercase tracking-wider text-pmf-green">Correu electrònic</label>
                            <div class="relative">
                                <Mail class="absolute left-2.5 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-[#7aa09c]" />
                                <input
                                    id="email"
                                    v-model="form.email"
                                    type="email"
                                    autocomplete="email"
                                    class="block w-full rounded-lg border border-[#c5d8d5] py-2 pl-8 pr-3 text-sm text-pmf-green-dark outline-none focus:border-pmf-turquoise"
                                />
                            </div>
                            <InputError :message="form.errors.email" class="mt-1" />
                        </div>

                        <div class="col-span-3">
                            <label for="dni" class="mb-1 block text-[11px] font-medium uppercase tracking-wider text-pmf-green">DNI</label>
                            <input
                                id="dni"
                                v-model="form.dni"
                                type="text"
                                disabled
                                class="block w-full cursor-not-allowed rounded-lg border border-[#c5d8d5] bg-[#D9D9D9] px-3 py-2 text-sm text-pmf-green-dark outline-none"
                            />
                            <InputError :message="form.errors.dni" class="mt-1" />
                        </div>

                        <div class="col-span-3">
                            <label for="nts" class="mb-1 block text-[11px] font-medium uppercase tracking-wider text-pmf-green">NTS</label>
                            <input
                                id="nts"
                                v-model="form.nts"
                                type="text"
                                disabled
                                class="block w-full cursor-not-allowed rounded-lg border border-[#c5d8d5] bg-[#D9D9D9] px-3 py-2 text-sm text-pmf-green-dark outline-none"
                            />
                            <InputError :message="form.errors.nts" class="mt-1" />
                        </div>

                        <div class="col-span-3">
                            <label for="phone" class="mb-1 block text-[11px] font-medium uppercase tracking-wider text-pmf-green">Número de telèfon</label>
                            <div class="relative">
                                <Phone class="absolute left-2.5 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-[#7aa09c]" />
                                <input
                                    id="phone"
                                    v-model="form.phone"
                                    type="tel"
                                    autocomplete="tel"
                                    class="block w-full rounded-lg border border-[#c5d8d5] py-2 pl-8 pr-3 text-sm text-pmf-green-dark outline-none focus:border-pmf-turquoise"
                                />
                            </div>
                            <InputError :message="form.errors.phone" class="mt-1" />
                        </div>
                    </div>
                </div>


                <div class="flex items-center justify-end gap-3 border-t border-[#deecea] bg-[#f9fcfc] px-6 py-4">
                    <button
                        type="button"
                        @click="closeModal"
                        class="rounded-lg border border-[#c5d8d5] bg-white px-4 py-2 text-sm font-medium text-pmf-grey-light transition-colors hover:bg-pmf-secondary cursor-pointer"
                    >
                        Cancel·lar
                    </button>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="rounded-lg bg-pmf-primary px-5 py-2 text-sm font-medium text-white transition-colors hover:bg-pmf-green disabled:opacity-50 cursor-pointer"
                    >
                        {{ form.processing ? 'Desant...' : 'Desar canvis' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template> -->


<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import { X, Mail, Phone, Plus, Trash2, Pencil } from 'lucide-vue-next';
import { watch, onUnmounted, ref } from 'vue';

interface Patient {
    id: number;
    name: string;
    nts: string;
    address: string;
    dni: string;
    phone: number;
    email: string;
}

interface Need {
    id: number;
    name: string;
}

const props = defineProps<{
    modelValue: boolean;
    patient: Patient | null;
}>();

const emit = defineEmits<{
    (e: 'update:modelValue', value: boolean): void;
}>();

const activeTab = ref<'info' | 'needs'>('info');

const form = useForm<Patient>({
    id: 0,
    name: '',
    address: '',
    dni: '',
    nts: '',
    phone: 0,
    email: '',
});

const needs = ref<Need[]>([]);
const loadingNeeds = ref(false);
const editingNeed = ref<Need | null>(null);
const newNeedName = ref('');
const showAddForm = ref(false);

watch(() => props.modelValue, (isOpen) => {
    if (isOpen && props.patient) {
        form.defaults(props.patient);
        form.reset();
        activeTab.value = 'info';
        loadNeeds();
    }
});

watch(() => props.modelValue, (isOpen) => {
    isOpen
        ? document.addEventListener('keydown', handleKeyPress)
        : document.removeEventListener('keydown', handleKeyPress);
});

onUnmounted(() => document.removeEventListener('keydown', handleKeyPress));

// --- Mètodes generals ---
const closeModal = () => {
    emit('update:modelValue', false);
    form.reset();
    form.clearErrors();
    needs.value = [];
    editingNeed.value = null;
    showAddForm.value = false;
};

const handleKeyPress = (e: KeyboardEvent) => {
    if (e.key === 'Escape') closeModal();
};

const submitForm = () => {
    form.post(`/patients/${form.id}`, {
        onSuccess: () => closeModal(),
    });
};

// --- Mètodes necessitats ---
async function loadNeeds() {
    if (!props.patient) return;
    loadingNeeds.value = true;
    try {
        const response = await fetch(`/patients/${props.patient.id}/needs`);
        needs.value = await response.json();
    } catch (error) {
        console.error('Error carregant necessitats:', error);
    } finally {
        loadingNeeds.value = false;
    }
}

async function addNeed() {
    if (!newNeedName.value.trim() || !props.patient) return;
    try {
        const response = await fetch(`/patients/${props.patient.id}/needs`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getCsrf() },
            body: JSON.stringify({ name: newNeedName.value }),
        });
        const created: Need = await response.json();
        needs.value.push(created);
        newNeedName.value = '';
        showAddForm.value = false;
    } catch (error) {
        console.error('Error afegint necessitat:', error);
    }
}

async function saveNeed(need: Need) {
    if (!props.patient) return;
    try {
        const response = await fetch(`/patients/${props.patient.id}/needs/${need.id}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getCsrf() },
            body: JSON.stringify({ name: need.name }),
        });
        const updated: Need = await response.json();
        const index = needs.value.findIndex(n => n.id === updated.id);
        if (index !== -1) needs.value[index] = updated;
        editingNeed.value = null;
    } catch (error) {
        console.error('Error actualitzant necessitat:', error);
    }
}

async function deleteNeed(needId: number) {
    if (!props.patient) return;
    try {
        await fetch(`/patients/${props.patient.id}/needs/${needId}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': getCsrf() },
        });
        needs.value = needs.value.filter(n => n.id !== needId);
    } catch (error) {
        console.error('Error eliminant necessitat:', error);
    }
}

function getCsrf(): string {
    return (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content ?? '';
}
</script>

<template>
    <div
        v-if="modelValue"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm"
        @click.self="closeModal"
    >
        <div class="relative mx-4 w-full max-w-2xl overflow-hidden rounded-2xl border border-[#b0ceca] bg-white shadow-xl">

            <!-- Header -->
            <div class="flex items-center justify-between border-b border-[#deecea] bg-[#f0f7f6] px-6 py-3">
                <h3 class="text-[19px] font-medium text-pmf-green-dark">Dades del pacient</h3>
                <button @click="closeModal" aria-label="Tancar"
                    class="rounded-lg p-1.5 text-pmf-grey-light transition-colors hover:bg-pmf-secondary cursor-pointer">
                    <X class="h-4 w-4" />
                </button>
            </div>

            <!-- Tabs -->
            <div class="flex border-b border-[#deecea] bg-[#f0f7f6] px-6">
                <button
                    @click="activeTab = 'info'"
                    :class="[
                        'py-2.5 px-4 text-sm font-medium border-b-2 -mb-px transition-colors',
                        activeTab === 'info'
                            ? 'border-pmf-turquoise text-pmf-green-dark'
                            : 'border-transparent text-pmf-grey-light hover:text-pmf-green'
                    ]"
                >
                    Informació personal
                </button>
                <button
                    @click="activeTab = 'needs'; loadNeeds()"
                    :class="[
                        'py-2.5 px-4 text-sm font-medium border-b-2 -mb-px transition-colors',
                        activeTab === 'needs'
                            ? 'border-pmf-turquoise text-pmf-green-dark'
                            : 'border-transparent text-pmf-grey-light hover:text-pmf-green'
                    ]"
                >
                    Necessitats
                </button>
            </div>

            <!-- TAB 1: Informació personal -->
            <form v-if="activeTab === 'info'" @submit.prevent="submitForm">
                <div class="max-h-[65vh] overflow-y-auto px-6 py-5">
                    <p class="mb-3 flex items-center gap-2 text-[11px] font-medium uppercase tracking-wider text-pmf-grey-light">
                        Informació personal
                    </p>
                    <div class="grid grid-cols-6 gap-x-4 gap-y-4">
                        <div class="col-span-3">
                            <label for="name" class="mb-1 block text-[11px] font-medium uppercase tracking-wider text-pmf-green">Nom i cognoms</label>
                            <input id="name" v-model="form.name" type="text" disabled
                                class="block w-full cursor-not-allowed rounded-lg border border-[#c5d8d5] bg-[#D9D9D9] px-3 py-2 text-sm text-pmf-green-dark outline-none" />
                            <InputError :message="form.errors.name" class="mt-1" />
                        </div>
                        <div class="col-span-3">
                            <label for="address" class="mb-1 block text-[11px] font-medium uppercase tracking-wider text-pmf-green">Adreça</label>
                            <input id="address" v-model="form.address" type="text"
                                class="block w-full rounded-lg border border-[#c5d8d5] px-3 py-2 text-sm text-pmf-green-dark outline-none focus:border-pmf-turquoise" />
                            <InputError :message="form.errors.address" class="mt-1" />
                        </div>
                        <div class="col-span-3">
                            <label for="email" class="mb-1 block text-[11px] font-medium uppercase tracking-wider text-pmf-green">Correu electrònic</label>
                            <div class="relative">
                                <Mail class="absolute left-2.5 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-[#7aa09c]" />
                                <input id="email" v-model="form.email" type="email" autocomplete="email"
                                    class="block w-full rounded-lg border border-[#c5d8d5] py-2 pl-8 pr-3 text-sm text-pmf-green-dark outline-none focus:border-pmf-turquoise" />
                            </div>
                            <InputError :message="form.errors.email" class="mt-1" />
                        </div>
                        <div class="col-span-3">
                            <label for="dni" class="mb-1 block text-[11px] font-medium uppercase tracking-wider text-pmf-green">DNI</label>
                            <input id="dni" v-model="form.dni" type="text" disabled
                                class="block w-full cursor-not-allowed rounded-lg border border-[#c5d8d5] bg-[#D9D9D9] px-3 py-2 text-sm text-pmf-green-dark outline-none" />
                            <InputError :message="form.errors.dni" class="mt-1" />
                        </div>
                        <div class="col-span-3">
                            <label for="nts" class="mb-1 block text-[11px] font-medium uppercase tracking-wider text-pmf-green">NTS</label>
                            <input id="nts" v-model="form.nts" type="text" disabled
                                class="block w-full cursor-not-allowed rounded-lg border border-[#c5d8d5] bg-[#D9D9D9] px-3 py-2 text-sm text-pmf-green-dark outline-none" />
                            <InputError :message="form.errors.nts" class="mt-1" />
                        </div>
                        <div class="col-span-3">
                            <label for="phone" class="mb-1 block text-[11px] font-medium uppercase tracking-wider text-pmf-green">Número de telèfon</label>
                            <div class="relative">
                                <Phone class="absolute left-2.5 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-[#7aa09c]" />
                                <input id="phone" v-model="form.phone" type="tel" autocomplete="tel"
                                    class="block w-full rounded-lg border border-[#c5d8d5] py-2 pl-8 pr-3 text-sm text-pmf-green-dark outline-none focus:border-pmf-turquoise" />
                            </div>
                            <InputError :message="form.errors.phone" class="mt-1" />
                        </div>
                    </div>
                </div>

                <!-- Footer tab 1 -->
                <div class="flex items-center justify-end gap-3 border-t border-[#deecea] bg-[#f9fcfc] px-6 py-4">
                    <button type="button" @click="closeModal"
                        class="rounded-lg border border-[#c5d8d5] bg-white px-4 py-2 text-sm font-medium text-pmf-grey-light transition-colors hover:bg-pmf-secondary cursor-pointer">
                        Cancel·lar
                    </button>
                    <button type="submit" :disabled="form.processing"
                        class="rounded-lg bg-pmf-primary px-5 py-2 text-sm font-medium text-white transition-colors hover:bg-pmf-green disabled:opacity-50 cursor-pointer">
                        {{ form.processing ? 'Desant...' : 'Desar canvis' }}
                    </button>
                </div>
            </form>

            <!-- TAB 2: Necessitats -->
            <div v-else class="flex flex-col">
                <div class="max-h-[65vh] overflow-y-auto px-6 py-5 space-y-2">

                    <!-- Loading -->
                    <div v-if="loadingNeeds" class="flex items-center justify-center py-10">
                        <span class="text-sm text-pmf-grey-light">Carregant necessitats...</span>
                    </div>

                    <!-- Llista de necessitats -->
                    <template v-else>
                        <div v-if="needs.length === 0 && !showAddForm"
                            class="py-10 text-center text-sm text-pmf-grey-light">
                            Aquest pacient no té necessitats registrades.
                        </div>

                        <div
                            v-for="need in needs"
                            :key="need.id"
                            class="flex items-center gap-3 rounded-lg border border-[#deecea] bg-[#f9fcfc] px-4 py-3"
                        >
                            <!-- Mode edició -->
                            <template v-if="editingNeed?.id === need.id">
                                <input
                                    v-model="editingNeed.name"
                                    type="text"
                                    class="flex-1 rounded-lg border border-[#c5d8d5] px-3 py-1.5 text-sm text-pmf-green-dark outline-none focus:border-pmf-turquoise"
                                    @keyup.enter="saveNeed(editingNeed)"
                                    @keyup.escape="editingNeed = null"
                                />
                                <button @click="saveNeed(editingNeed)"
                                    class="rounded-lg bg-pmf-primary px-3 py-1.5 text-xs font-medium text-white hover:bg-pmf-green cursor-pointer">
                                    Desar
                                </button>
                                <button @click="editingNeed = null"
                                    class="rounded-lg border border-[#c5d8d5] px-3 py-1.5 text-xs font-medium text-pmf-grey-light hover:bg-pmf-secondary cursor-pointer">
                                    Cancel·lar
                                </button>
                            </template>

                            <!-- Mode lectura -->
                            <template v-else>
                                <span class="flex-1 text-sm text-pmf-green-dark">{{ need.name }}</span>
                                <button @click="editingNeed = { ...need }" title="Editar"
                                    class="rounded-lg p-1.5 text-pmf-grey-light hover:bg-pmf-secondary hover:text-pmf-green cursor-pointer transition-colors">
                                    <Pencil class="h-4 w-4" />
                                </button>
                                <button @click="deleteNeed(need.id)" title="Eliminar"
                                    class="rounded-lg p-1.5 text-pmf-grey-light hover:bg-red-50 hover:text-red-500 cursor-pointer transition-colors">
                                    <Trash2 class="h-4 w-4" />
                                </button>
                            </template>
                        </div>

                        <!-- Formulari afegir nova necessitat -->
                        <div v-if="showAddForm"
                            class="flex items-center gap-3 rounded-lg border border-dashed border-[#b0ceca] bg-[#f0f7f6] px-4 py-3">
                            <input
                                v-model="newNeedName"
                                type="text"
                                placeholder="Nova necessitat..."
                                class="flex-1 rounded-lg border border-[#c5d8d5] px-3 py-1.5 text-sm text-pmf-green-dark outline-none focus:border-pmf-turquoise"
                                @keyup.enter="addNeed"
                                @keyup.escape="showAddForm = false; newNeedName = ''"
                                autofocus
                            />
                            <button @click="addNeed"
                                class="rounded-lg bg-pmf-primary px-3 py-1.5 text-xs font-medium text-white hover:bg-pmf-green cursor-pointer">
                                Afegir
                            </button>
                            <button @click="showAddForm = false; newNeedName = ''"
                                class="rounded-lg border border-[#c5d8d5] px-3 py-1.5 text-xs font-medium text-pmf-grey-light hover:bg-pmf-secondary cursor-pointer">
                                Cancel·lar
                            </button>
                        </div>
                    </template>
                </div>

                <!-- Footer tab 2 -->
                <div class="flex items-center justify-between border-t border-[#deecea] bg-[#f9fcfc] px-6 py-4">
                    <button type="button" @click="showAddForm = true; newNeedName = ''"
                        :disabled="showAddForm"
                        class="flex items-center gap-2 rounded-lg border border-[#c5d8d5] bg-white px-4 py-2 text-sm font-medium text-pmf-green transition-colors hover:bg-pmf-secondary cursor-pointer disabled:opacity-40">
                        <Plus class="h-4 w-4" />
                        Nova necessitat
                    </button>
                    <button type="button" @click="closeModal"
                        class="rounded-lg border border-[#c5d8d5] bg-white px-4 py-2 text-sm font-medium text-pmf-grey-light transition-colors hover:bg-pmf-secondary cursor-pointer">
                        Tancar
                    </button>
                </div>
            </div>

        </div>
    </div>
</template>