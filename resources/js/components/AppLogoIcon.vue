<script setup lang="ts">
import { useAttrs } from 'vue';

const attrs = useAttrs();
</script>

<template>
    <img src="/logo.png" alt="Logo de la web" v-bind="attrs" />
</template>
