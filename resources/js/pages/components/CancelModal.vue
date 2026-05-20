<script setup lang="ts">
import { Trash2 } from 'lucide-vue-next';
import { computed } from 'vue';

const props = defineProps({
    modelValue: { type: Boolean, required: false },
    show: {
        type: Boolean,
        required: false,
    },
    title: {
        type: String,
        default: 'Cancel·lar cita',
    },
    name: {
        type: String,
        default: '',
    },
    id: {
        type: [Number, String],
        required: false,
    },
});

const emit = defineEmits(['update:modelValue', 'confirm', 'close']);

const visible = computed(() => {
    if (typeof props.modelValue !== 'undefined') {
        return props.modelValue;
    }

    return Boolean(props.show);
});

const close = () => {
    if (props.modelValue !== undefined) {
        emit('update:modelValue', false);
    } else {
        emit('close');
    }
};

const confirmDelete = () => {
    emit('confirm', props.id);
    close();
};
</script>

<template>
    <div
        v-if="visible"
        role="dialog"
        aria-modal="true"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
        @click.self="close"
    >
        <div
            class="mx-4 w-full max-w-md overflow-hidden rounded-2xl border border-[#e8f1ef] bg-white shadow-2xl"
        >
            <div class="px-6 pt-6 pb-5">
                <div class="flex items-start gap-4">
                    <div
                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-red-50"
                    >
                        <Trash2 class="h-5 w-5 text-red-600" />
                    </div>

                    <div class="flex-1 pt-0.5">
                        <h3 class="text-base font-semibold text-pmf-green-dark">
                            {{ title }}
                        </h3>
                        <p
                            class="mt-1.5 text-sm leading-relaxed text-pmf-grey-light"
                        >
                            Estàs segur que vols cancel·lar la cita?
                        </p>
                    </div>
                </div>
            </div>

            <div
                class="flex items-center gap-3 border-t border-[#eef7f6] px-6 py-4"
            >
                <button
                    type="button"
                    class="flex-1 rounded-lg border border-[#cfe7e4] bg-white px-4 py-2.5 text-sm font-medium text-pmf-green-dark transition hover:bg-[#f4f9f8]"
                    @click="close"
                >
                    No, no vull
                </button>
                <button
                    type="button"
                    class="flex flex-1 items-center justify-center gap-2 rounded-lg bg-red-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-red-700 active:scale-95"
                    @click="confirmDelete"
                >
                    <Trash2 class="h-4 w-4" />
                    Sí, estic segur/a
                </button>
            </div>
        </div>
    </div>
</template>
