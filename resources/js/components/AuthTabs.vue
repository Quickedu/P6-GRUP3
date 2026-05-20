<script setup lang="ts">
import { computed } from 'vue';

type Option = {
    value: string;
    label: string;
    icon?: any;
};

const props = defineProps<{
    modelValue: string;
    options: Option[];
    label?: string;
}>();

const emit = defineEmits(['update:modelValue']);

const currentIndex = computed(() =>
    props.options.findIndex((o) => o.value === props.modelValue),
);

const select = (value: string) => {
    emit('update:modelValue', value);
};

const move = (direction: 'next' | 'prev') => {
    let index = currentIndex.value;

    if (direction === 'next') {
        index = (index + 1) % props.options.length;
    } else {
        index = (index - 1 + props.options.length) % props.options.length;
    }

    emit('update:modelValue', props.options[index].value);
};
</script>

<template>
    <div class="auth-tab-group" role="radiogroup" :aria-label="label">
        <div
            v-for="option in options"
            :key="option.value"
            class="auth-tab-option"
            :class="{ active: modelValue === option.value }"
            role="radio"
            :aria-checked="modelValue === option.value"
            :tabindex="modelValue === option.value ? 0 : -1"
            @click="select(option.value)"
            @keydown.enter="select(option.value)"
            @keydown.space.prevent="select(option.value)"
            @keydown.arrow-right.prevent="move('next')"
            @keydown.arrow-left.prevent="move('prev')"
        >
            <span v-if="option.icon" class="auth-tab-icon" aria-hidden="true">
                <component :is="option.icon" />
            </span>

            {{ option.label }}
        </div>
    </div>
</template>
