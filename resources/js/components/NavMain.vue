<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';
import {
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { useCurrentUrl } from '@/composables/useCurrentUrl';
import type { NavItem } from '@/types';

const props = defineProps<{
    items: NavItem[];
    label?: string;
}>();

const { isCurrentUrl } = useCurrentUrl();

const activeIndex = computed(() =>
    props.items.findIndex(item => isCurrentUrl(item.href))
);

const direction = ref<'down' | 'up'>('down');

watch(activeIndex, (newIndex, oldIndex) => {
    if (newIndex === -1 || oldIndex === -1) {
return;
}

    direction.value = newIndex > oldIndex ? 'down' : 'up';
});
</script>

<template>
    <SidebarGroup class="px-0 py-0">
        <SidebarGroupLabel v-if="label" class="px-3 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm">{{ label }}</SidebarGroupLabel>
        <SidebarMenu class="gap-1 sm:gap-2">
            <SidebarMenuItem
                v-for="item in items"
                :key="item.title"
                class="relative"
            >
                <Transition :name="direction === 'down' ? 'bar-down' : 'bar-up'">
                    <span
                        v-if="isCurrentUrl(item.href)"
                        class="pointer-events-none absolute right-0 top-1/2 z-10 h-[80%] w-1 -translate-y-1/2 rounded-l-sm bg-[var(--pmf-primary)]"
                    />
                </Transition>

                <Link
                    :href="item.href"
                    class="flex w-full items-center gap-2 sm:gap-3 rounded px-2 sm:px-4 py-2 sm:py-3 text-xs sm:text-sm font-medium text-sidebar-foreground no-underline transition-colors duration-150 ease-in hover:bg-[color:rgba(0,163,142,0.08)] hover:text-[var(--pmf-primary)]"
                    :class="isCurrentUrl(item.href)
                        ? 'bg-[color:rgba(0,163,142,0.12)] font-semibold text-[var(--pmf-primary)]'
                        : ''"
                >
                    <component :is="item.icon" class="h-4 w-4 sm:h-5 sm:w-5 shrink-0" />
                    <span class="truncate">{{ item.title }}</span>
                </Link>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarGroup>
</template>