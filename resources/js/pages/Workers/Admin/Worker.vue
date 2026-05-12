<script setup lang="ts">
import { ref, computed } from 'vue';
import { usePage, useForm } from '@inertiajs/vue3';
import { SquarePen, Trash2, X } from 'lucide-vue-next';
import { ChevronLeft, ChevronRight } from 'lucide-vue-next';
import textNotify from '@/pages/components/textNotify.vue';
import { update, destroy, store } from '@/routes/workers';
import ModalDelete from '@/pages/components/modalDelete.vue';
import InputError from '@/components/InputError.vue';

interface Worker {
    id: number;
    name: string;
    email: string;
    role: string;
    nss: string;
    address: string;
    dni: string;
    license_number: string;
    phone: string;
    password: string;
    user?: {
        name: string;
        email: string;
        role: string;
    };
}

const createEmptyWorker = (): Worker => ({
    id: 0,
    name: '',
    email: '',
    role: '',
    nss: '',
    address: '',
    dni: '',
    license_number: '',
    phone: '',
    password: '',
});

const page = usePage();
const flashMessage = computed(() => (page.props.flash as any)?.message);
const flashStatus = computed(() => (page.props.flash as any)?.status);
const isWorkerModalOpen = ref(false);
const isWorkerUpdateModalOpen = ref(false);
const showDeleteModal = ref(false);
const limit = ref(1);
const perPage = 10;
const selectedWorker = ref<Worker>(createEmptyWorker());

const props = defineProps<{
    workers: Worker[];
}>();
const filteredWorkers = ref(props.workers);
const createForm = useForm({
    name: '',
    email: '',
    role: '',
    password: '',
    nss: '',
    address: '',
    dni: '',
    license_number: '',
    phone: '',
});
const updateForm = useForm({
    email: '',
    role: '',
    address: '',
    phone: '',
});

const resetSelectedWorker = () => {
    selectedWorker.value = createEmptyWorker();
};

const resetCreateForm = () => {
    createForm.reset();
    createForm.clearErrors();
    resetSelectedWorker();
};

const resetUpdateForm = () => {
    updateForm.reset();
    updateForm.clearErrors();
    resetSelectedWorker();
};

const setSelectedWorker = (worker: Worker) => {
    selectedWorker.value = {
        ...worker,
        password: '',
    };
};

const visibleItems = computed(() => {
    return filteredWorkers.value.slice(
        (limit.value - 1) * perPage,
        limit.value * perPage,
    );
});

const hasMore = computed(
    () => limit.value * perPage < filteredWorkers.value.length,
);

const OpenCreateModal = () => {
    isWorkerModalOpen.value = true;
    resetCreateForm();
};

const closeModal = () => {
    isWorkerModalOpen.value = false;
    resetCreateForm();
};

const closeModalUpdate = () => {
    isWorkerUpdateModalOpen.value = false;
    resetUpdateForm();
};

const createWorker = () => {
    createForm.post(store().url, {
        preserveScroll: true,
        onSuccess: () => {
            isWorkerModalOpen.value = false;
            createForm.reset();
            createForm.clearErrors();
            filteredWorkers.value = props.workers;
        },
    });
};

const openEditModal = (worker: Worker) => {
    setSelectedWorker(worker);
    updateForm.email = worker.email;
    updateForm.role = worker.role;
    updateForm.address = worker.address;
    updateForm.phone = worker.phone;
    isWorkerUpdateModalOpen.value = true;
};

function updateWorker() {
    const id = selectedWorker.value.id;
    updateForm.put(update(id).url, {
        preserveScroll: true,
        onSuccess: () => {
            isWorkerUpdateModalOpen.value = false;
            updateForm.reset();
            updateForm.clearErrors();
            filteredWorkers.value = props.workers;
        },
    });
}

function destroyWorker() {
    const form = useForm({});
    const id = selectedWorker.value.id;
    form.delete(destroy(id).url, {
        preserveScroll: true,
        onSuccess: () => {
            showDeleteModal.value = false;
            resetSelectedWorker();
            filteredWorkers.value = props.workers;
        },
        onFinish: () => {
            showDeleteModal.value = false;
            resetSelectedWorker();
        },
    });
}

function openDeleteModal(worker: Worker) {
    setSelectedWorker(worker);
    showDeleteModal.value = true;
}

</script>

<template>
    <div class="mx-5 mt-5 space-y-4">
        <textNotify class="mb-4" v-if="flashMessage" :message="flashMessage" :status="flashStatus" />
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <h2 class="text-2xl font-medium text-pmf-green-dark">
                    Llistat de treballadors
                </h2>
            </div>
            <div>
                <button
                    class="text-md inline-flex cursor-pointer items-center gap-1.5 rounded-lg border border-[#b0ceca] bg-pmf-primary px-2.5 py-1.5 font-medium text-white transition-colors hover:bg-pmf-primary"
                    title="Afegir treballador" @click="OpenCreateModal()">
                    <SquarePen class="h-3.5 w-3.5" />
                    Afegir treballador
                </button>
            </div>
        </div>
        <!-- Table -->
        <div class="overflow-hidden rounded-xl border border-[#c5d8d5] bg-white">
            <div class="overflow-x-auto">
                <table class="w-full min-w-160 text-sm">
                <thead class="bg-[#f0f7f6]">
                    <tr
                        class="border-b border-[#c5d8d5] text-left text-[11px] font-medium tracking-wider text-pmf-green uppercase">
                        <th class="px-4 py-3">Nom</th>
                        <th class="px-4 py-3">Rol</th>
                        <th class="px-4 py-3">NSS</th>
                        <th class="px-4 py-3">DNI</th>
                        <th class="px-4 py-3">Número de llicència</th>
                        <th class="px-4 py-3">Telèfon</th>
                        <th class="px-4 py-3">Accions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#eaf2f1]">
                    <tr class="transition-colors hover:bg-[#f4f9f8]" v-for="worker in visibleItems" :key="worker.id">
                                <td class="px-4 py-3 font-medium text-pmf-green-dark bg-[#f8fcfb]">
                                    <span class="block max-w-40 truncate ">{{ worker.name }}</span>
                                </td>
                        <td class="px-4 py-3 text-pmf-grey-light">
                            <span v-if="worker.role === 'admin'">Admin</span>
                            <span v-else-if="worker.role === 'secretary'">Secretari</span>
                            <span v-else-if="worker.role === 'doctor'">Metge</span>
                            <span v-else-if="worker.role === 'technician'">Tècnic</span>
                        </td>
                        <td class="px-4 py-3 text-pmf-grey-light">
                            {{ worker.nss }}
                        </td>
                        <td class="px-4 py-3 text-pmf-grey-light">
                            {{ worker.dni }}
                        </td>
                        <td class="px-4 py-3 text-pmf-grey-light">
                            {{ worker.license_number }}
                        </td>
                        <td class="px-4 py-3 text-pmf-grey-light">
                            {{ worker.phone }}
                        </td>
                        <td class="flex gap-2 py-3">
                            <button @click="openEditModal(worker)"
                                class="inline-flex items-center gap-1.5 rounded-lg border border-pmf-primary bg-pmf-primary px-3 py-2 text-sm font-medium text-white transition-colors hover:bg-pmf-primary/90"
                                title="Editar treballador">
                                <SquarePen class="h-4 w-4" />
                                Editar
                            </button>
                            <button @click="openDeleteModal(worker)"
                                class="inline-flex items-center gap-1.5 rounded-lg border border-red-300 bg-red-50 px-3 py-2 text-sm font-medium text-red-700 transition-colors hover:bg-red-100"
                                title="Eliminar treballador">
                                <Trash2 class="h-4 w-4" />
                                Eliminar
                            </button>
                        </td>
                    </tr>
                    <tr v-if="!props.workers || props.workers.length === 0">
                        <td colspan="8" class="px-5 py-12 text-center text-pmf-grey-light">
                            No hi ha cap treballador registrat.
                        </td>
                    </tr>
                </tbody>
                </table>
            </div>

            <div
                class="flex flex-col gap-3 border-t border-[#eaf2f1] bg-[#f8fcfb] px-4 py-4 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-sm text-pmf-grey-light">
                    Mostrant de {{ (limit - 1) * perPage + 1 }} a
                    {{ Math.min(limit * perPage, filteredWorkers.length) }} de
                    {{ filteredWorkers.length }} resultats
                </p>

                <div class="flex items-center gap-2">
                    <button type="button" @click="limit--" v-if="limit > 1"
                        class="inline-flex h-10 items-center gap-1.5 rounded-lg border border-[#d4e3e0] bg-white px-3 text-sm font-medium text-pmf-green-dark transition-colors hover:border-[#b0ceca] hover:bg-[#f4f9f8] disabled:cursor-not-allowed disabled:opacity-50">
                        <ChevronLeft class="h-4 w-4" />
                        Anterior
                    </button>

                    <button type="button" @click="limit++" v-if="hasMore"
                        class="inline-flex h-10 items-center gap-1.5 rounded-lg border border-[#d4e3e0] bg-white px-3 text-sm font-medium text-pmf-green-dark transition-colors hover:border-[#b0ceca] hover:bg-[#f4f9f8] disabled:cursor-not-allowed disabled:opacity-50">
                        Següent
                        <ChevronRight class="h-4 w-4" />
                    </button>
                </div>
            </div>
        </div>
    </div>

    <ModalDelete v-model="showDeleteModal" :title="'Eliminar treballador'" :name="selectedWorker.name" :id="selectedWorker.id"
        @confirm="destroyWorker" />

    <!-- Modal create -->

    <div v-if="isWorkerModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm"
        @click.self="closeModal">
        <div
            class="relative mx-4 w-full max-w-2xl overflow-hidden rounded-2xl border border-[#b0ceca] bg-white shadow-xl">
            <!-- Header -->
            <div class="flex items-center justify-between border-b border-[#deecea] bg-[#f0f7f6] px-6 py-3">
                <div class="flex items-center gap-3">
                    <div>
                        <h3 class="text-[19px] font-medium text-pmf-green-dark">
                            Dades del treballador
                        </h3>
                    </div>
                </div>
                <button @click="closeModal" aria-label="Tancar"
                    class="cursor-pointer rounded-lg p-1.5 text-pmf-grey-light transition-colors hover:bg-pmf-secondary">
                    <X class="h-4 w-4" aria-hidden="true" />
                </button>
            </div>

            <form @submit.prevent="createWorker()">
                <div class="max-h-[65vh] overflow-y-auto px-6 py-5">
                    <p
                        class="mb-3 flex items-center gap-2 text-[11px] font-medium tracking-wider text-pmf-grey-light uppercase">
                        Credencials d'usuari
                    </p>

                    <div class="grid grid-cols-6 gap-x-4 gap-y-4">
                        <div class="col-span-3">
                            <label for="name"
                                class="mb-1 block text-[11px] font-medium tracking-wider text-pmf-green uppercase">Nom i cognoms</label>
                            <input id="name" v-model="createForm.name" type="text" name="name"
                                class="w-full rounded-lg border border-[#c5d8d5] px-3 py-2 text-sm text-pmf-green-dark outline-none" />
                            <InputError :message="createForm.errors.name" class="mt-1" />
                        </div>

                        <div class="col-span-3">
                            <label for="email"
                                class="mb-1 block text-[11px] font-medium tracking-wider text-pmf-green uppercase">Email</label>
                            <input id="email" v-model="createForm.email" type="email" name="email"
                                class="w-full rounded-lg border border-[#c5d8d5] px-3 py-2 text-sm text-pmf-green-dark outline-none" />
                            <InputError :message="createForm.errors.email" class="mt-1" />
                        </div>

                        <div class="col-span-3">
                            <label for="role"
                                class="mb-1 block text-[11px] font-medium tracking-wider text-pmf-green uppercase">Rol</label>
                            <select id="role" v-model="createForm.role" name="role"
                                class="w-full rounded-lg border border-[#c5d8d5] bg-white px-3 py-2 text-sm text-pmf-green-dark outline-none">
                                <option disabled value="">Selecciona un rol</option>
                                <option value="admin">Administrador</option>
                                <option value="secretary">Secretari</option>
                                <option value="doctor">Doctor</option>
                                <option value="technician">Tècnic</option>
                            </select>
                            <InputError :message="createForm.errors.role" class="mt-1" />
                        </div>

                        <div class="col-span-3">
                            <label for="password"
                                class="mb-1 block text-[11px] font-medium tracking-wider text-pmf-green uppercase">Contrasenya</label>
                            <input id="password" v-model="createForm.password" type="password" name="password"
                                class="w-full rounded-lg border border-[#c5d8d5] px-3 py-2 text-sm text-pmf-green-dark outline-none" />
                            <InputError :message="createForm.errors.password" class="mt-1" />
                        </div>
                    </div>

                    <p
                        class="mb-3 mt-6 flex items-center gap-2 text-[11px] font-medium tracking-wider text-pmf-grey-light uppercase">
                        Informació del treballador
                    </p>

                    <div class="grid grid-cols-6 gap-x-4 gap-y-4">
                        <div class="col-span-3">
                            <label for="nss"
                                class="mb-1 block text-[11px] font-medium tracking-wider text-pmf-green uppercase">NSS del treballador</label>
                            <input id="nss" v-model="createForm.nss" name="nss" type="text"
                                class="w-full rounded-lg border border-[#c5d8d5] px-3 py-2 text-sm text-pmf-green-dark outline-none" />
                            <InputError :message="createForm.errors.nss" class="mt-1" />
                        </div>

                        <div class="col-span-3">
                            <label for="dni"
                                class="mb-1 block text-[11px] font-medium tracking-wider text-pmf-green uppercase">DNI del treballador</label>
                            <input id="dni" v-model="createForm.dni" name="dni" type="text"
                                class="w-full rounded-lg border border-[#c5d8d5] px-3 py-2 text-sm text-pmf-green-dark outline-none" />
                            <InputError :message="createForm.errors.dni" class="mt-1" />
                        </div>

                        <div class="col-span-3">
                            <label for="address"
                                class="mb-1 block text-[11px] font-medium tracking-wider text-pmf-green uppercase">Adreça</label>
                            <input id="address" v-model="createForm.address" name="address" type="text"
                                class="w-full rounded-lg border border-[#c5d8d5] px-3 py-2 text-sm text-pmf-green-dark outline-none" />
                            <InputError :message="createForm.errors.address" class="mt-1" />
                        </div>

                        <div class="col-span-3">
                            <label for="license_number"
                                class="mb-1 block text-[11px] font-medium tracking-wider text-pmf-green uppercase">Número de llicència</label>
                            <input id="license_number" v-model="createForm.license_number" name="license_number" type="text"
                                class="w-full rounded-lg border border-[#c5d8d5] px-3 py-2 text-sm text-pmf-green-dark outline-none" />
                            <InputError :message="createForm.errors.license_number" class="mt-1" />
                        </div>

                        <div class="col-span-3">
                            <label for="phone"
                                class="mb-1 block text-[11px] font-medium tracking-wider text-pmf-green uppercase">Número de telèfon</label>
                            <input id="phone" v-model="createForm.phone" name="phone" type="tel" inputmode="numeric"
                                class="w-full rounded-lg border border-[#c5d8d5] px-3 py-2 text-sm text-pmf-green-dark outline-none" />
                            <InputError :message="createForm.errors.phone" class="mt-1" />
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="flex items-center justify-end gap-3 border-t border-[#deecea] bg-[#f9fcfc] px-6 py-4">
                    <button type="button" @click="closeModal"
                        class="cursor-pointer rounded-lg border border-[#c5d8d5] bg-white px-4 py-2 text-sm font-medium text-pmf-grey-light transition-colors hover:bg-pmf-secondary">
                        Cancel·lar
                    </button>
                    <button type="submit" :disabled="createForm.processing"
                        class="cursor-pointer rounded-lg bg-pmf-primary px-5 py-2 text-sm font-medium text-white transition-colors hover:bg-pmf-green disabled:opacity-50">
                        {{ createForm.processing ? 'Desant...' : 'Desar els canvis' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal update -->

    <div v-if="isWorkerUpdateModalOpen"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm"
        @click.self="closeModalUpdate">
        <div
            class="relative mx-4 w-full max-w-2xl overflow-hidden rounded-2xl border border-[#b0ceca] bg-white shadow-xl">
            <!-- Header -->
            <div class="flex items-center justify-between border-b border-[#deecea] bg-[#f0f7f6] px-6 py-3">
                <div class="flex items-center gap-3">
                    <div>
                        <h3 class="text-[19px] font-medium text-pmf-green-dark">
                            Dades del treballador
                        </h3>
                    </div>
                </div>
                <button @click="closeModalUpdate" aria-label="Tancar"
                    class="cursor-pointer rounded-lg p-1.5 text-pmf-grey-light transition-colors hover:bg-pmf-secondary">
                    <X class="h-4 w-4" aria-hidden="true" />
                </button>
            </div>

            <form @submit.prevent="updateWorker()">
                <div class="max-h-[65vh] overflow-y-auto px-6 py-5">
                    <p
                        class="mb-3 flex items-center gap-2 text-[11px] font-medium tracking-wider text-pmf-grey-light uppercase">
                        Credencials d'usuari
                    </p>

                    <div class="grid grid-cols-6 gap-x-4 gap-y-4">
                        <div class="col-span-3">
                            <label for="readonly-name"
                                class="mb-1 block text-[11px] font-medium tracking-wider text-pmf-green uppercase">Nom i cognoms</label>
                            <input id="readonly-name" v-model="selectedWorker.name" name="readonly-name" type="text" disabled
                                class="block w-full cursor-not-allowed rounded-lg border border-[#c5d8d5] bg-[#D9D9D9] px-3 py-2 text-sm text-pmf-green-dark outline-none" />
                        </div>

                        <div class="col-span-3">
                            <label for="edit-email"
                                class="mb-1 block text-[11px] font-medium tracking-wider text-pmf-green uppercase">Email</label>
                            <input id="edit-email" v-model="updateForm.email" name="email" type="email"
                                class="w-full rounded-lg border border-[#c5d8d5] px-3 py-2 text-sm text-pmf-green-dark outline-none" />
                            <InputError :message="updateForm.errors.email" class="mt-1" />
                        </div>

                        <div class="col-span-3">
                            <label for="edit-role"
                                class="mb-1 block text-[11px] font-medium tracking-wider text-pmf-green uppercase">Rol</label>
                            <select id="edit-role" v-model="updateForm.role" name="role"
                                class="w-full rounded-lg border border-[#c5d8d5] bg-white px-3 py-2 text-sm text-pmf-green-dark outline-none">
                                <option disabled value="">Selecciona un rol</option>
                                <option value="admin">Administrador</option>
                                <option value="secretary">Secretari</option>
                                <option value="doctor">Doctor</option>
                                <option value="technician">Tècnic</option>
                            </select>
                            <InputError :message="updateForm.errors.role" class="mt-1" />
                        </div>
                    </div>

                    <p
                        class="mb-3 mt-6 flex items-center gap-2 text-[11px] font-medium tracking-wider text-pmf-grey-light uppercase">
                        Informacio del treballador
                    </p>

                    <div class="grid grid-cols-6 gap-x-4 gap-y-4">
                        <div class="col-span-3">
                            <label for="address"
                                class="mb-1 block text-[11px] font-medium tracking-wider text-pmf-green uppercase">Adreça
                            </label>
                            <input id="address" v-model="updateForm.address" name="address" type="text"
                                class="w-full rounded-lg border border-[#c5d8d5] px-3 py-2 text-sm text-pmf-green-dark outline-none" 
                            />
                            <InputError :message="updateForm.errors.address" class="mt-1" />
                        </div>

                        <div class="col-span-3">
                            <label for="phone"
                                class="mb-1 block text-[11px] font-medium tracking-wider text-pmf-green uppercase">Número de telèfon
                            </label>
                            <input id="phone" v-model="updateForm.phone" name="phone" type="tel" inputmode="numeric"
                                class="w-full rounded-lg border border-[#c5d8d5] px-3 py-2 text-sm text-pmf-green-dark outline-none" 
                            />
                            <InputError :message="updateForm.errors.phone" class="mt-1" />
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="flex items-center justify-end gap-3 border-t border-[#deecea] bg-[#f9fcfc] px-6 py-4">
                    <button type="button" @click="closeModalUpdate"
                        class="cursor-pointer rounded-lg border border-[#c5d8d5] bg-white px-4 py-2 text-sm font-medium text-pmf-grey-light transition-colors hover:bg-pmf-secondary">
                        Cancel·lar
                    </button>
                    <button type="submit" :disabled="updateForm.processing"
                        class="cursor-pointer rounded-lg bg-pmf-primary px-5 py-2 text-sm font-medium text-white transition-colors hover:bg-pmf-green disabled:opacity-50">
                        {{ updateForm.processing ? 'Desant...' : 'Desar els canvis' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
