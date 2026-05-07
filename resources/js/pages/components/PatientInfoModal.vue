<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import { X, Mail, Phone } from 'lucide-vue-next';
import { watch } from 'vue';

interface Patient {
    id: number;
    name: string;
    birth_date: string;
    nts: string;
    address: string;
    dni: string;
    phone: string;
    email: string;
}

const props = defineProps<{
    modelValue: boolean;
    patient: Patient;
}>();

const emit = defineEmits<{
    (e: 'update:modelValue', value: boolean): void;
}>();

const form = useForm({
    name: '',
    address: '',
    dni: '',
    nts: '',
    phone: '',
    email: '',
});

watch(() => props.modelValue, (isOpen) => {
    if (isOpen) {
        form.name    = props.patient.name;
        form.address = props.patient.address;
        form.dni     = props.patient.dni;
        form.nts     = props.patient.nts;
        form.phone   = props.patient.phone;
        form.email   = props.patient.email;
    }
});

const close = () => emit('update:modelValue', false);

const submit = () => {
    form.post(`/patients/${props.patient.id}`, {
        onSuccess: () => close(),
    });
};

</script>

<template>
    <div
        v-if="modelValue"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm"
        @click.self="close"
    >
        <div class="mx-4 w-full max-w-lg overflow-hidden rounded-2xl border border-[#b0ceca] bg-white shadow-xl">

            <!-- Header -->
            <div class="flex items-center justify-between border-b border-[#deecea] bg-[#f0f7f6] px-6 py-4">
                <div>
                    <h3 class="text-[15px] font-semibold text-pmf-green-dark">Editar dades personals</h3>
                    <p class="text-xs text-pmf-grey-light mt-0.5">{{ patient.name }}</p>
                </div>
                <button @click="close" class="rounded-lg p-1.5 text-pmf-grey-light hover:bg-pmf-secondary cursor-pointer transition-colors">
                    <X class="h-4 w-4" />
                </button>
            </div>
            

            <form @submit.prevent="submit">
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

                <!-- Footer -->
                <div class="flex items-center justify-end gap-3 border-t border-[#deecea] bg-[#f9fcfc] px-6 py-4">
                    <button
                        type="button"
                        @click="close"
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
</template>

<style scoped>
.field-label {
    display: block;
    margin-bottom: 0.25rem;
    font-size: 11px;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--pmf-green);
}
.field-input {
    display: block;
    width: 100%;
    border-radius: 0.5rem;
    border: 1px solid #c5d8d5;
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
    color: var(--pmf-green-dark);
    outline: none;
}
.field-input:focus { border-color: var(--pmf-turquoise); }
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