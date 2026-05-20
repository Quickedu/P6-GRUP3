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
        default: 'Eliminar element',
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
            class="mx-4 w-full max-w-md transform overflow-hidden rounded-xl border border-[#e8f1ef] bg-white shadow-2xl transition-all"
        >
            <div class="px-6 py-5">
                <div class="flex items-start gap-4">
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-pmf-green-dark">
                            {{ title }}
                        </h3>
                        <p class="mt-1 text-sm text-pmf-grey-light">
                            Confirma que vols eliminar permanentment aquesta
                            entrada. Aquesta acció no es podrà desfer.
                        </p>
                    </div>
                </div>
                <div class="mt-4 px-1">
                    <div
                        class="rounded-md border border-[#f0f7f6] bg-[#fbfffe] p-3"
                    >
                        <p class="text-sm">
                            <span class="font-medium text-pmf-green-dark">{{
                                name
                            }}</span>
                        </p>
                    </div>
                </div>
            </div>
            <div
                class="flex items-center justify-end gap-3 border-t border-[#eef7f6] bg-[#f9fcfc] px-6 py-4"
            >
                <button
                    type="button"
                    @click="close"
                    class="rounded-md border border-[#cfe7e4] bg-white px-4 py-2 text-sm font-medium text-pmf-green-dark hover:bg-[#f4f9f8]"
                >
                    Cancel·lar
                </button>
                <button
                    type="button"
                    @click="confirmDelete"
                    class="inline-flex items-center gap-2 rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700"
                >
                    <Trash2 class="h-4 w-4" />
                    Eliminar
                </button>
            </div>
        </div>
    </div>
</template>
