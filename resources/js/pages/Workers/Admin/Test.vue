<script setup lang="ts">
import { usePage, useForm } from '@inertiajs/vue3';
import { SquarePen, Trash2 } from 'lucide-vue-next';
import { ChevronLeft, ChevronRight } from 'lucide-vue-next';
import { ref, computed } from 'vue';
import ModalDelete from '@/pages/components/modalDelete.vue';
import textNotify from '@/pages/components/textNotify.vue';
import { update, destroy, store } from '@/routes/tests';

const page = usePage();
const flashMessage = computed(() => (page.props.flash as any)?.message);
const flashStatus = computed(() => (page.props.flash as any)?.status);
const isTestModalOpen = ref(false);
const isTestUpdateModalOpen = ref(false);
const selectedTest = ref<Test>({
    id: 0,
    name: '',
    time: 0,
});
const showDeleteModal = ref(false);
const limit = ref(1);
const perPage = 10;

interface Test {
    id: number;
    name: string;
    time: number;
}

const props = defineProps<{
    tests: Test[];
}>();
const filteredTests = ref(props.tests);

const visibleItems = computed(() => {
    return filteredTests.value.slice(
        (limit.value - 1) * perPage,
        limit.value * perPage,
    );
});

const hasMore = computed(
    () => limit.value * perPage < filteredTests.value.length,
);


// Opens the "create test" modal and resets the form state.
const OpenCreateModal = () => {
    selectedTest.value = {
        id: 0,
        name: '',
        time: 0,
    };
    isTestModalOpen.value = true;
};


// Closes the "create test" modal and clears the selected test
const closeModal = () => {
    isTestModalOpen.value = false;
    selectedTest.value = { id: 0, name: '', time: 0 };
};


// Closes the "edit test" modal and clears the selected test
const closeModalUpdate = () => {
    isTestUpdateModalOpen.value = false;
    selectedTest.value = { id: 0, name: '', time: 0 };
};


// Submits the "create test" form to the backend
const createTest = () => {
    const form = useForm({
        name: selectedTest.value.name,
        time: selectedTest.value.time,
    });
    form.post(store().url, {
        preserveScroll: true,
        onSuccess: () => {
            isTestModalOpen.value = false;
            filteredTests.value = props.tests;
        },
    });
};

// Opens the "edit test" modal and loads the selected test into the form
const openEditModal = (test: Test) => {
    selectedTest.value = {
        id: test.id,
        name: test.name,
        time: test.time,
    };
    isTestUpdateModalOpen.value = true;
};

// Submits the "edit test" form to the backend
function updateTest() {
    const form = useForm({
        name: selectedTest.value.name,
        time: selectedTest.value.time,
    });
    const id = selectedTest.value.id;
    form.put(update(id).url, {
        preserveScroll: true,
        onSuccess: () => {
            isTestUpdateModalOpen.value = false;
            filteredTests.value = props.tests;
        },
    });
}

// Deletes the selected test after confirmation
function destroyTest() {
    const form = useForm({});
    const id = selectedTest.value.id;
    form.delete(destroy(id).url, {
        preserveScroll: true,
        onSuccess: () => {
            showDeleteModal.value = false;
            selectedTest.value = { id: 0, name: '', time: 0 };
            filteredTests.value = props.tests;
        },
        onFinish: () => {
            showDeleteModal.value = false;
            selectedTest.value = { id: 0, name: '', time: 0 };
        },
    });
}

// Opens the confirmation modal for deleting a test
function openDeleteModal(test: Test) {
    selectedTest.value = {
        id: test.id,
        name: test.name,
        time: test.time,
    };
    showDeleteModal.value = true;
}
</script>

<template>
    <div class="mx-5 mt-5 space-y-4">
        <textNotify
            class="mb-4"
            v-if="flashMessage"
            :message="flashMessage"
            :status="flashStatus"
        />
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <h2 class="text-2xl font-medium text-pmf-green-dark">
                    Llistat de proves
                </h2>
            </div>
            <div>
                <button
                    class="text-md inline-flex cursor-pointer items-center gap-1.5 rounded-lg border border-[#b0ceca] bg-pmf-primary px-2.5 py-1.5 font-medium text-white transition-colors hover:bg-pmf-primary"
                    title="Editar prova"
                    @click="OpenCreateModal()"
                >
                    <SquarePen class="h-3.5 w-3.5" />
                    Afegir prova
                </button>
            </div>
        </div>
        <!-- Tests table -->
        <div
            class="overflow-hidden rounded-xl border border-[#c5d8d5] bg-white"
        >
            <div class="overflow-x-auto">
                <table class="w-full min-w-160 text-sm">
                    <thead class="bg-[#f0f7f6]">
                        <tr
                            class="border-b border-[rgb(197,216,213)] text-left text-[11px] font-medium tracking-wider text-pmf-green uppercase"
                        >
                            <th class="px-4 py-3">Número</th>
                            <th class="px-4 py-3">Nom del test</th>
                            <th class="px-4 py-3">Duració</th>
                            <th class="px-4 py-3">Accions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#eaf2f1]">
                        <tr
                            class="transition-colors hover:bg-[#f4f9f8]"
                            v-for="test in visibleItems"
                            :key="test.id"
                        >
                            <td class="px-4 py-3">
                                <span
                                    class="rounded-full bg-pmf-secondary px-2 py-0.5 text-[11px] font-medium text-pmf-green"
                                >
                                    {{ test.id }}
                                </span>
                            </td>
                            <td
                                class="px-4 py-3 font-medium text-pmf-green-dark"
                            >
                                {{ test.name }}
                            </td>
                            <td class="px-4 py-3 text-pmf-grey-light">
                                {{ test.time }} min
                            </td>
                            <td class="flex max-w-27 gap-2 py-3">
                                <button
                                    @click="openEditModal(test)"
                                    class="inline-flex items-center gap-1.5 rounded-lg border border-pmf-primary bg-pmf-primary px-3 py-2 text-sm font-medium text-white transition-colors hover:bg-pmf-primary/90"
                                    title="Editar prova"
                                >
                                    <SquarePen class="h-4 w-4" />
                                    Editar
                                </button>
                                <button
                                    @click="openDeleteModal(test)"
                                    class="inline-flex items-center gap-1.5 rounded-lg border border-red-300 bg-red-50 px-3 py-2 text-sm font-medium text-red-700 transition-colors hover:bg-red-100"
                                    title="Eliminar prova"
                                >
                                    <Trash2 class="h-4 w-4" />
                                    Eliminar
                                </button>
                            </td>
                        </tr>
                        <tr v-if="!props.tests || props.tests.length === 0">
                            <td
                                colspan="8"
                                class="px-5 py-12 text-center text-pmf-grey-light"
                            >
                                No hi ha cap prova registrada.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div
                class="flex flex-col gap-3 border-t border-[#eaf2f1] bg-[#f8fcfb] px-4 py-4 sm:flex-row sm:items-center sm:justify-between"
            >
                <p class="text-sm text-pmf-grey-light">
                    Mostrant de {{ (limit - 1) * perPage + 1 }} a
                    {{ Math.min(limit * perPage, filteredTests.length) }} de
                    {{ filteredTests.length }} resultats
                </p>

                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        @click="limit--"
                        v-if="limit > 1"
                        class="inline-flex h-10 items-center gap-1.5 rounded-lg border border-[#d4e3e0] bg-white px-3 text-sm font-medium text-pmf-green-dark transition-colors hover:border-[#b0ceca] hover:bg-[#f4f9f8] disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        <ChevronLeft class="h-4 w-4" />
                        Anterior
                    </button>

                    <button
                        type="button"
                        @click="limit++"
                        v-if="hasMore"
                        class="inline-flex h-10 items-center gap-1.5 rounded-lg border border-[#d4e3e0] bg-white px-3 text-sm font-medium text-pmf-green-dark transition-colors hover:border-[#b0ceca] hover:bg-[#f4f9f8] disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        Següent
                        <ChevronRight class="h-4 w-4" />
                    </button>
                </div>
            </div>
        </div>
    </div>

    <ModalDelete
        v-model="showDeleteModal"
        :title="'Eliminar prova'"
        :name="selectedTest.name"
        :id="selectedTest.id"
        @confirm="destroyTest"
    />

    <!-- Create test modal -->

    <div
        v-if="isTestModalOpen"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm"
        @click.self="closeModal"
    >
        <div
            class="relative mx-4 w-full max-w-2xl overflow-hidden rounded-2xl border border-[#b0ceca] bg-white shadow-xl"
        >
            <!-- Modal header -->
            <div
                class="flex items-center justify-between border-b border-[#deecea] bg-[#f0f7f6] px-6 py-3"
            >
                <div class="flex items-center gap-3">
                    <div>
                        <h3 class="text-[19px] font-medium text-pmf-green-dark">
                            Dades de la prova
                        </h3>
                    </div>
                </div>
                <button
                    @click="closeModal"
                    aria-label="Tancar"
                    class="cursor-pointer rounded-lg p-1.5 text-pmf-grey-light transition-colors hover:bg-pmf-secondary"
                >
                    <X class="h-4 w-4" aria-hidden="true" />
                </button>
            </div>

            <form @submit.prevent="createTest()">
                <div class="max-h-[65vh] overflow-y-auto px-6 py-5">
                    <p
                        class="mb-3 flex items-center gap-2 text-[11px] font-medium tracking-wider text-pmf-grey-light uppercase"
                    >
                        Informació de la prova
                    </p>

                    <div class="grid grid-cols-6 gap-x-4 gap-y-4">
                        <div class="col-span-3">
                            <label
                                for="name"
                                class="mb-1 block text-[11px] font-medium tracking-wider text-pmf-green uppercase"
                                >Nom de la prova</label
                            >
                            <input
                                id="name"
                                v-model="selectedTest.name"
                                type="text"
                                name="name"
                                class="w-full rounded-lg border border-[#c5d8d5] px-3 py-2 text-sm text-pmf-green-dark outline-none"
                            />
                        </div>

                        <div class="col-span-3">
                            <label
                                for="time"
                                class="mb-1 block text-[11px] font-medium tracking-wider text-pmf-green uppercase"
                                >Temps (minuts)</label
                            >
                            <input
                                id="time"
                                v-model="selectedTest.time"
                                type="number"
                                name="time"
                                class="w-full rounded-lg border border-[#c5d8d5] px-3 py-2 text-sm text-pmf-green-dark outline-none"
                            />
                        </div>
                    </div>
                </div>

                <!-- Modal footer -->
                <div
                    class="flex items-center justify-end gap-3 border-t border-[#deecea] bg-[#f9fcfc] px-6 py-4"
                >
                    <button
                        type="button"
                        @click="closeModal"
                        class="cursor-pointer rounded-lg border border-[#c5d8d5] bg-white px-4 py-2 text-sm font-medium text-pmf-grey-light transition-colors hover:bg-pmf-secondary"
                    >
                        Cancel·lar
                    </button>
                    <button
                        type="submit"
                        class="cursor-pointer rounded-lg bg-pmf-primary px-5 py-2 text-sm font-medium text-white transition-colors hover:bg-pmf-green disabled:opacity-50"
                    >
                        Desar els canvis
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit test modal -->

    <div
        v-if="isTestUpdateModalOpen"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm"
        @click.self="closeModalUpdate"
    >
        <div
            class="relative mx-4 w-full max-w-2xl overflow-hidden rounded-2xl border border-[#b0ceca] bg-white shadow-xl"
        >
            <!-- Modal header -->
            <div
                class="flex items-center justify-between border-b border-[#deecea] bg-[#f0f7f6] px-6 py-3"
            >
                <div class="flex items-center gap-3">
                    <div>
                        <h3 class="text-[19px] font-medium text-pmf-green-dark">
                            Dades de la prova
                        </h3>
                    </div>
                </div>
                <button
                    @click="closeModalUpdate"
                    aria-label="Tancar"
                    class="cursor-pointer rounded-lg p-1.5 text-pmf-grey-light transition-colors hover:bg-pmf-secondary"
                >
                    <X class="h-4 w-4" aria-hidden="true" />
                </button>
            </div>

            <form @submit.prevent="updateTest()">
                <div class="max-h-[65vh] overflow-y-auto px-6 py-5">
                    <p
                        class="mb-3 flex items-center gap-2 text-[11px] font-medium tracking-wider text-pmf-grey-light uppercase"
                    >
                        Informació de la prova
                    </p>

                    <div class="grid grid-cols-6 gap-x-4 gap-y-4">
                        <div class="col-span-3">
                            <label
                                for="name"
                                class="mb-1 block text-[11px] font-medium tracking-wider text-pmf-green uppercase"
                                >Nom de la prova</label
                            >
                            <input
                                id="name"
                                v-model="selectedTest.name"
                                name="name"
                                type="text"
                                class="w-full rounded-lg border border-[#c5d8d5] px-3 py-2 text-sm text-pmf-green-dark outline-none"
                            />
                        </div>

                        <div class="col-span-3">
                            <label
                                for="time"
                                class="mb-1 block text-[11px] font-medium tracking-wider text-pmf-green uppercase"
                                >Temps (minuts)</label
                            >
                            <input
                                id="time"
                                v-model="selectedTest.time"
                                name="time"
                                type="number"
                                class="w-full rounded-lg border border-[#c5d8d5] px-3 py-2 text-sm text-pmf-green-dark outline-none"
                            />
                        </div>
                    </div>
                </div>

                <!-- Modal footer -->
                <div
                    class="flex items-center justify-end gap-3 border-t border-[#deecea] bg-[#f9fcfc] px-6 py-4"
                >
                    <button
                        type="button"
                        @click="closeModalUpdate"
                        class="cursor-pointer rounded-lg border border-[#c5d8d5] bg-white px-4 py-2 text-sm font-medium text-pmf-grey-light transition-colors hover:bg-pmf-secondary"
                    >
                        Cancel·lar
                    </button>
                    <button
                        type="submit"
                        class="cursor-pointer rounded-lg bg-pmf-primary px-5 py-2 text-sm font-medium text-white transition-colors hover:bg-pmf-green disabled:opacity-50"
                    >
                        Desar els canvis
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
