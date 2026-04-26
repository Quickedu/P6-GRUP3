<script setup lang="ts">
/**
 * Reusable admin search input.
 *
 * Purpose:
 * - Provide a shared search UI for Admin pages.
 * - Work with v-model from parent pages.
 * - Forward native input/keyup events so each page can keep its own search logic.
 */

// Props consumed by parent pages.
// `modelValue` is required for v-model.
// `placeholder` is optional and defaults to "Cerca...".
const props = withDefaults(
    defineProps<{
        modelValue: string;
        placeholder?: string;
    }>(),
    {
        placeholder: 'Cerca...',
    },
);

// Events emitted to parent pages:
// - update:modelValue => updates the v-model value in parent state.
// - input => lets parent run logic on input (example: debounce).
// - keyup => lets parent run logic on key release.
const emit = defineEmits<{
    (e: 'update:modelValue', value: string): void;
    (e: 'input', event: Event): void;
    (e: 'keyup', event: KeyboardEvent): void;
}>();

// Triggered on each input change.
// 1) Sends new value for v-model.
// 2) Re-emits the original event for custom parent behavior.
function handleInput(event: Event) {
    const target = event.target as HTMLInputElement;
    emit('update:modelValue', target.value);
    emit('input', event);
}

// Re-emits keyup so parent pages can attach their current filters/search handlers.
function handleKeyup(event: KeyboardEvent) {
    emit('keyup', event);
}
</script>

<template>
    <div class="relative w-full sm:max-w-md">
        <svg
            class="absolute top-1/2 left-4 -translate-y-1/2 text-gray-400"
            xmlns="http://www.w3.org/2000/svg"
            width="18"
            height="18"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
        >
            <circle cx="11" cy="11" r="8" />
            <path d="m21 21-4.34-4.34" />
        </svg>

        <input
            :value="props.modelValue"
            type="text"
            :placeholder="props.placeholder"
            :aria-label="props.placeholder"
            class="w-full rounded-2xl bg-[#f3f6f7] py-1.5 pr-4 pl-11 text-[#023e8a] placeholder-gray-400 border border-pmf-green/40 focus:ring-2 focus:ring-pmf-green focus:outline-none"
            @input="handleInput"
            @keyup="handleKeyup"
        />
    </div>
</template>
