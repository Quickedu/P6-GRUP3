<script setup lang="ts">
import caLocale from '@fullcalendar/core/locales/ca';
import dayGridPlugin from '@fullcalendar/daygrid';
import FullCalendar from '@fullcalendar/vue3';
import { computed } from 'vue';

const props = defineProps<{
    events?: any[];
    onEventClick?: (info: any) => void;
    initialView?: string;
}>();

// Can change the calendar view from the parent using the initial-view prop.
// Available views: 'dayGridMonth' (default), 'dayGridWeek', 'dayGridDay'.
const calendarOptions = computed(() => ({
    plugins: [dayGridPlugin],
    initialView: props.initialView || 'dayGridMonth',
    locale: caLocale,
    headerToolbar: {
        left: 'prev',
        center: 'title',
        right: 'next',
        // Other options: 'today', 'dayGridMonth,dayGridWeek,dayGridDay' (view switcher)
    },
    events: props.events,
    eventClick: props.onEventClick,
    height: 'auto',
    fixedWeekCount: false, // only show weeks that exist in the current month
    eventTimeFormat: {
        hour: '2-digit',
        minute: '2-digit',
        hour12: false, // can change to 24h format - set true for it
    },
}));
</script>

<template>
    <div role="region" aria-label="Calendari de cites">
        <FullCalendar :options="calendarOptions">
            <template #eventContent="arg">
                <div
                    class="fc-event-inner rounded"
                    :style="{
                        backgroundColor: arg.event.backgroundColor,
                        borderLeft: `1px solid ${arg.event.borderColor}`,
                        color: arg.event.textColor,
                    }"
                >
                    <span class="fc-event-time-label">{{ arg.timeText }}</span>
                    <span class="fc-event-title-label">{{
                        arg.event.title
                    }}</span>
                    <span class="fc-event-status-label">{{
                        arg.event.extendedProps.status
                    }}</span>
                </div>
            </template>
        </FullCalendar>
    </div>
</template>
