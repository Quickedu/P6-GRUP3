<script lang="ts" setup>
import { usePage } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

const STATUS = ['correcte', 'error', 'avís', 'informació'] as const;
type Stats = (typeof STATUS)[number];

type Props = {
    message: string;
    status?: Stats | (string & {});
};

const props = withDefaults(defineProps<Props>(), {
    status: 'informació',
});

const visible = ref(true);
const page = usePage();

const isKnownStatus = (status: string): status is Stats => {
    return (STATUS as readonly string[]).includes(status);
};

const statusForClasses = computed<Stats>(() => {
    return isKnownStatus(props.status) ? props.status : 'correcte';
});

watch(
    () => [props.message, props.status, page.props.flash as any],
    (_, __, onCleanup) => {
        visible.value = true;

        const timeoutId = setTimeout(() => {
            visible.value = false;
        }, 5000);

        onCleanup(() => {
            clearTimeout(timeoutId);
        });
    },
    { immediate: true },
);

const statusWrapperClasses = computed(() => {
    const classes: Record<Stats, string> = {
        correcte: 'border-[#006557]/20 bg-[#006557]',
        error: 'border-red-200 bg-red-50',
        avís: 'border-amber-200 bg-amber-50',
        informació: 'border-blue-200 bg-blue-50',
    };

    return classes[statusForClasses.value];
});

const statusTextClasses = computed(() => {
    const classes: Record<Stats, string> = {
        correcte: 'text-white/90',
        error: 'text-red-700',
        avís: 'text-amber-700',
        informació: 'text-blue-700',
    };

    return classes[statusForClasses.value];
});

const statusBadgeClasses = computed(() => {
    const classes: Record<Stats, string> = {
        correcte: 'bg-white/15 text-white',
        error: 'bg-red-100 text-red-800',
        avís: 'bg-amber-100 text-amber-800',
        informació: 'bg-blue-100 text-blue-800',
    };

    return classes[statusForClasses.value];
});

const statusCloseClasses = computed(() => {
    const classes: Record<Stats, string> = {
        correcte: 'text-white/60 hover:text-white hover:bg-white/10',
        error: 'text-red-400 hover:text-red-600 hover:bg-red-100',
        avís: 'text-amber-400 hover:text-amber-600 hover:bg-amber-100',
        informació: 'text-blue-400 hover:text-blue-600 hover:bg-blue-100',
    };

    return classes[statusForClasses.value];
});
</script>

<template>
    <Transition
        enter-active-class="transition ease-out duration-300"
        enter-from-class="translate-x-full opacity-0"
        enter-to-class="translate-x-0 opacity-100"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="translate-x-0 opacity-100"
        leave-to-class="translate-x-full opacity-0"
    >
        <div
            v-if="visible"
            class="pointer-events-auto fixed top-4 right-4 z-[9999] w-96 max-w-[calc(100vw-2rem)]"
        >
            <div
                class="flex items-center gap-3 rounded-xl border px-4 py-2.5 shadow-lg"
                :class="statusWrapperClasses"
                role="status"
                aria-live="polite"
            >
                <span
                    class="flex-shrink-0 rounded px-2 py-0.5 text-xs font-semibold tracking-wide whitespace-nowrap uppercase"
                    :class="statusBadgeClasses"
                >
                    {{ status }}
                </span>

                <p
                    class="min-w-0 flex-1 text-sm leading-5"
                    :class="statusTextClasses"
                >
                    {{ message }}
                </p>

                <button
                    @click="visible = false"
                    class="flex-shrink-0 rounded p-1 transition"
                    :class="statusCloseClasses"
                    aria-label="tancar"
                >
                    <svg
                        class="h-4 w-4"
                        fill="currentColor"
                        viewBox="0 0 20 20"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                            clip-rule="evenodd"
                        />
                    </svg>
                </button>
            </div>
        </div>
    </Transition>
</template>
