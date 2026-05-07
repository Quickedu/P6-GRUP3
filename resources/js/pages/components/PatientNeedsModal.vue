<script setup lang="ts">
import { X, Plus, Trash2 } from 'lucide-vue-next';
import { ref, computed, watch } from 'vue';

interface Need {
    id: number;
    name: string;
}

interface Patient {
    id: number;
    name: string;
}

const props = defineProps<{
    modelValue: boolean;
    patient: Patient;
    patientNeeds: Need[];
    availableNeeds: Need[];
}>();

const emit = defineEmits<{
    (e: 'update:modelValue', value: boolean): void;
}>();

const localNeeds = ref<Need[]>([]);
const showSelect = ref(false);
const selectedNeedId = ref<number | null>(null);
const loading = ref(false);

watch(() => props.modelValue, (isOpen) => {
    if (isOpen) {
        localNeeds.value = [...props.patientNeeds];
        showSelect.value = false;
        selectedNeedId.value = null;
    }
});

const unassignedNeeds = computed(() =>
    props.availableNeeds.filter(n => !localNeeds.value.some(ln => ln.id === n.id))
);

const close = () => emit('update:modelValue', false);

async function assignNeed() {
    if (!selectedNeedId.value) return;
    loading.value = true;
    try {
        const res = await fetch(`/patients/${props.patient.id}/needs`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({ need_id: selectedNeedId.value }),
        });
        const data = await res.json();
        if (data.status === 'success') {
            localNeeds.value.push(data.data);
            selectedNeedId.value = null;
            showSelect.value = false;
        }
    } catch { console.error('Error assignant necessitat'); }
    finally { loading.value = false; }
}

async function removeNeed(needId: number) {
    loading.value = true;
    try {
        const res = await fetch(`/patients/${props.patient.id}/needs/${needId}`, {
            method: 'DELETE',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
        });
        const data = await res.json();
        if (data.status === 'success') {
            localNeeds.value = localNeeds.value.filter(n => n.id !== needId);
        }
    } catch { console.error('Error eliminant necessitat'); }
    finally { loading.value = false; }
}
</script>

<template>
    <div
        v-if="modelValue"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm"
        @click.self="close"
    >
        <div class="mx-4 w-full max-w-md overflow-hidden rounded-2xl border border-[#b0ceca] bg-white shadow-xl">

            <!-- Header -->
            <div class="flex items-center justify-between border-b border-[#deecea] bg-[#f0f7f6] px-6 py-4">
                <div>
                    <h3 class="text-[15px] font-semibold text-pmf-green-dark">Gestionar necessitats</h3>
                    <p class="text-xs text-pmf-grey-light mt-0.5">{{ patient.name }}</p>
                </div>
                <button @click="close" class="rounded-lg p-1.5 text-pmf-grey-light hover:bg-pmf-secondary cursor-pointer transition-colors">
                    <X class="h-4 w-4" />
                </button>
            </div>

            <!-- Llista de necessitats -->
            <div class="max-h-[50vh] overflow-y-auto px-6 py-4 space-y-2">

                <div v-if="localNeeds.length === 0 && !showSelect"
                    class="py-8 text-center text-sm text-pmf-grey-light">
                    Sense necessitats assignades.
                </div>

                <div
                    v-for="need in localNeeds"
                    :key="need.id"
                    class="flex items-center gap-3 rounded-lg border border-[#deecea] bg-[#f9fcfc] px-4 py-2.5"
                >
                    <span class="h-1.5 w-1.5 rounded-full bg-pmf-primary flex-shrink-0"></span>
                    <span class="flex-1 text-sm text-pmf-green-dark">{{ need.name }}</span>
                    <button
                        @click="removeNeed(need.id)"
                        :disabled="loading"
                        class="rounded-lg p-1.5 text-pmf-grey-light hover:bg-red-50 hover:text-red-500 cursor-pointer transition-colors disabled:opacity-40"
                    >
                        <Trash2 class="h-3.5 w-3.5" />
                    </button>
                </div>

                <!-- Selector per afegir -->
                <div v-if="showSelect"
                    class="flex items-center gap-2 rounded-lg border border-dashed border-[#b0ceca] bg-[#f0f7f6] px-4 py-3">
                    <select
                        v-model="selectedNeedId"
                        class="flex-1 rounded-lg border border-[#c5d8d5] bg-white px-3 py-1.5 text-sm text-pmf-green-dark outline-none focus:border-pmf-turquoise"
                    >
                        <option :value="null" disabled>Selecciona una necessitat...</option>
                        <option v-for="n in unassignedNeeds" :key="n.id" :value="n.id">{{ n.name }}</option>
                    </select>
                    <button
                        @click="assignNeed"
                        :disabled="!selectedNeedId || loading"
                        class="btn-primary text-xs py-1.5 px-3"
                    >
                        Assignar
                    </button>
                    <button
                        @click="showSelect = false; selectedNeedId = null"
                        class="btn-secondary text-xs py-1.5 px-3"
                    >
                        Cancel·lar
                    </button>
                </div>
            </div>

            <!-- Footer -->
            <div class="flex items-center justify-between border-t border-[#deecea] bg-[#f9fcfc] px-6 py-3">
                <button
                    @click="showSelect = true; selectedNeedId = null"
                    :disabled="showSelect || unassignedNeeds.length === 0"
                    class="inline-flex items-center gap-1.5 text-xs font-medium text-pmf-green hover:text-pmf-primary transition-colors disabled:opacity-40 cursor-pointer"
                >
                    <Plus class="h-3.5 w-3.5" />
                    Assignar necessitat
                </button>
                <button @click="close" class="btn-secondary text-xs py-1.5 px-3">Tancar</button>
            </div>
        </div>
    </div>
</template>

<style scoped>
.btn-primary {
    border-radius: 0.5rem;
    background-color: var(--pmf-primary);
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
    font-weight: 500;
    color: #fff;
    cursor: pointer;
    transition: opacity 0.15s;
}
.btn-primary:hover { opacity: 0.9; }
.btn-primary:disabled { opacity: 0.5; cursor: not-allowed; }
.btn-secondary {
    border-radius: 0.5rem;
    border: 1px solid #c5d8d5;
    background-color: #fff;
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
    font-weight: 500;
    color: var(--pmf-grey-light);
    cursor: pointer;
    transition: background-color 0.15s;
}
.btn-secondary:hover { background-color: var(--pmf-secondary); }
</style>