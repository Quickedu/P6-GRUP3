<script setup lang="ts">
import { X, Clock } from 'lucide-vue-next'


interface PopoverEvent {
    title: string
    startStr: string
    extendedProps: {
        id: number
        status: string
        urgency: string
        test: string
    }
}

const props = defineProps<{
    event: PopoverEvent | null
    x: number
    y: number
    visible: boolean
}>()

const emit = defineEmits<{
    (e: 'close'): void
    (e: 'cancel', id: number): void
}>()

const getUrgencyStyle = (urgencia: string) => {
    switch (urgencia) {
        case 'urgent': return { bg:'bg-red-100 text-red-700', label: 'Urgent'}
        case 'preferent': return { bg:'bg-orange-100 text-orange-700', label: 'Preferent'}
        default: return { bg:'bg-[#f0f7f6] text-pmf-green', label: 'No urgent'}
    }
}

const getStatusStyle = (estat: string) => {
    switch (estat) {
        case 'programada': return { bg: 'bg-blue-100 text-blue-700', label: 'Programada'}
        case 'realitzada': return { bg: 'bg-[#f0f7f6] text-pmf-green', label: 'Realitzada'}
        case 'cancel·lada': return { bg: 'bg-red-100 text-red-700', label: 'Cancel·lada'}
        default: return { bg: 'bg-gray-100 text-gray-600', label: estat }
    }
}

const formatDateTime = (dateStr: string) =>
    new Date(dateStr).toLocaleDateString('ca-ES', {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
        hour: '2-digit',
        minute: '2-digit',
    })
</script>

<template>
    <div v-if="visible && event" class="fixed inset-0 z-50 pointer-events-none">
        <!-- Close popover -->
        <div class="fixed inset-0 pointer-events-auto" @click="emit('close')"></div>

        <!-- Popover -->
        <div
            class="event-popover fixed pointer-events-auto z-50"
            :style="{ top: `${y}px`, left: `${x}px` }"
            role="dialog"
            aria-label="Detall de la cita"
        >
            <!-- Header -->
            <div class="flex items-start justify-between gap-3 mb-3">
                <div class="flex items-center gap-2">
                    <span
                        class="h-3 w-3 rounded-full flex-shrink-0"
                        :class="getUrgencyStyle(event.extendedProps.urgency).bg"
                    ></span>
                    <p class="text-sm font-medium text-pmf-green-dark">{{ event.title }}</p>
                </div>
                <button
                    @click="emit('close')"
                    aria-label="Tancar"
                    class="rounded p-0.5 text-pmf-grey-light hover:bg-pmf-secondary cursor-pointer flex-shrink-0"
                >
                    <X class="h-3.5 w-3.5" aria-hidden="true" />
                </button>
            </div>

            <!-- Date -->
            <div class="flex items-center gap-2 text-xs text-pmf-grey-light mb-3">
                <Clock class="h-3.5 w-3.5 flex-shrink-0" aria-hidden="true" />
                <span>{{ formatDateTime(event.startStr) }}</span>
            </div>

            <!-- Details -->
            <div class="space-y-2 text-xs">
                <div class="flex items-center justify-between">
                    <span class="text-pmf-grey-light">Prova</span>
                    <span class="font-medium text-pmf-green-dark">{{ event.extendedProps.test }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-pmf-grey-light">Estat</span>
                    <span :class="['rounded-full px-2 py-0.5 font-medium', getStatusStyle(event.extendedProps.status).bg]">
                        {{ getStatusStyle(event.extendedProps.status).label }}
                    </span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-pmf-grey-light">Urgència</span>
                    <span :class="['rounded-full px-2 py-0.5 font-medium', getUrgencyStyle(event.extendedProps.urgency).bg]">
                        {{ getUrgencyStyle(event.extendedProps.urgency).label }}
                    </span>
                </div>
            </div>
            <div v-if="event.extendedProps.status !== 'cancel·lada'" class="mt-4 pt-3 border-t border-gray-100">
                <button
                    @click="emit('cancel', event.extendedProps.id)"
                    class="w-full rounded-lg bg-red-50 px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-100 transition-colors cursor-pointer"
                >
                    Cancel·lar cita
                </button>
            </div>
        </div>
    </div>
</template>
