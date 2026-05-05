<script setup lang="ts">
import { ref, computed } from 'vue';
import { router, usePage, useForm } from '@inertiajs/vue3';
import { SquarePen, Trash2 } from 'lucide-vue-next';
import { ChevronLeft, ChevronRight } from 'lucide-vue-next';
import textNotify from '@/pages/components/textNotify.vue';
import { create, update, destroy } from '@/routes/tests';
import ModalDelete from '@/pages/components/modalDelete.vue';

const search = ref('');
const page = usePage();
const user = computed(() => page.props.auth?.user);
const isSecretary = computed(() => user.value?.role === 'secretary');
const flashMessage = computed(() => (page.props.flash as any)?.message);
const flashStatus = computed(() => (page.props.flash as any)?.status);
const isTestModalOpen = ref(false);
const selectedTest = ref<Test | null>(null);
const showDeleteModal = ref(false);
const testType = ref({
    id: 0,
    name: '',
    time: '',
});
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

const OpenCreateModal = () => {
    selectedTest.value = null;
    isTestModalOpen.value = true;
}

const closeModal = () => {
    isTestModalOpen.value = false;
    selectedTest.value = null;
}

const createTest = () => {
    const form = useForm({
        name: testType.value.name,
        time: testType.value.time,
    });
    form.post(create().url, {
        preserveScroll: true,
        onSuccess: () => {
            isTestModalOpen.value = false;
            filteredTests.value = props.tests;
        }
    })
}

const openEditModal = (test: Test) => {
    selectedTest.value = test;
    isTestModalOpen.value = true;
};

// function open Update Modal
function openUpdateModal(test: Test) {
    testType.value = {
        id: test.id,
        name: test.name,
        time: test.time,
    }
    showDeleteModal.value = true;
}

function updateTest() {
    const form = useForm({
        name: testType.value.name,
        time: testType.value.time,
    });
    const id = testType.value.id;
    form.put(update(id).url, {
        preserveScroll: true,
        onSuccess: () => {
            showDeleteModal.value = false;
            filteredTests.value = props.tests;
        }
    })
}

function destroyTest() {
    const form = useForm({});
    const id = testType.value.id;
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
    })
}

function openDeleteModal(test: Test) {
    testType.value = {
        id: test.id,
        name: test.name,
        time: test.time,
    }
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
                >
                    <SquarePen class="h-3.5 w-3.5" />
                    Afegir prova
                </button>
            </div>
        </div>
        <!-- Table -->
        <div
            class="overflow-hidden rounded-xl border border-[#c5d8d5] bg-white"
        >
            <table class="w-full text-sm">
                <thead class="bg-[#f0f7f6]">
                    <tr
                        class="border-b border-[#c5d8d5] text-left text-[11px] font-medium tracking-wider text-pmf-green uppercase"
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
                        <td class="px-4 py-3 font-medium text-pmf-green-dark">
                            {{ test.name }}
                        </td>
                        <td class="px-4 py-3 text-pmf-grey-light">
                            {{ test.time }} min
                        </td>
                        <td class="flex gap-2 py-3">
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
                    <!-- <tr>
                        <td colspan="8" class="px-5 py-12 text-center text-pmf-grey-light">
                            No hi ha cap prova registrada.
                        </td>
                    </tr> -->
                </tbody>
            </table>

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
        :name="testType.name"
        :id="testType.id"
        @confirm="destroyTest"
    />
</template>
