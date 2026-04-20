<script setup>
const props = defineProps({
  show: {
    type: Boolean,
    required: true
  }
});

const emit = defineEmits(['close']);
</script>

<template>
  <div v-if="show" class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center z-50 p-4" @click.self="emit('close')">
    <div class="backdrop-blur-md bg-black/40 border border-white/20 rounded-[10px] p-6 max-w-md w-full shadow-2xl">
      {{ message }}
      <slot></slot>
    </div>
  </div>
</template>