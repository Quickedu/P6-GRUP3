<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
</script>

<template>
    <div class="flex w-full items-center justify-start gap-4 bg-[#DCE7ED]">
        <div class="flex shrink-0 items-center justify-center">
            <AppLogoIcon class="h-20 w-20 text-white" />
        </div>
        <div class="grid flex-1 text-left">
            <span class="text-2xl font-bold leading-none" style="color: var(--pmf-primary)">PMF</span>
            <span class="text-xs font-medium leading-none" style="color: var(--pmf-grey-light)">Programa Millora flux</span>
        </div>
    </div>
</template>