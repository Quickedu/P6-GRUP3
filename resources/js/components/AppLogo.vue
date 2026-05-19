<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
</script>

<template>
    <div
        class="flex w-full min-w-0 items-center justify-start gap-3 bg-[#DCE7ED] group-has-data-[collapsible=icon]/sidebar-wrapper:justify-center group-has-data-[collapsible=icon]/sidebar-wrapper:gap-0"
    >
        <div class="flex shrink-0 items-center justify-center">
            <AppLogoIcon
                class="h-12 w-12 text-white transition-[height,width] duration-200 sm:h-14 sm:w-14 lg:h-20 lg:w-20 group-has-data-[collapsible=icon]/sidebar-wrapper:h-10 group-has-data-[collapsible=icon]/sidebar-wrapper:w-10"
            />
        </div>
        <div
            class="grid min-w-0 flex-1 text-left transition-opacity duration-200 group-has-data-[collapsible=icon]/sidebar-wrapper:invisible group-has-data-[collapsible=icon]/sidebar-wrapper:opacity-0"
        >
            <span
                class="truncate text-base font-bold leading-tight sm:text-lg lg:text-2xl"
                style="color: var(--pmf-primary)"
            >
                PMF
            </span>
            <span
                class="truncate text-[10px] font-medium leading-tight sm:text-xs"
                style="color: var(--pmf-grey-light)"
            >
                Programa Millora flux
            </span>
        </div>
    </div>
</template>
