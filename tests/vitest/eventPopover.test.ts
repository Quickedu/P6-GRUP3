// @vitest-environment jsdom
import { mount } from '@vue/test-utils';
import { afterEach, describe, expect, it, vi } from 'vitest';
import { defineComponent, nextTick } from 'vue';
import PatientDashboard from '@/pages/Patient/patientDashboard.vue';

type CalendarEvent = {
    title: string;
    start?: string;
    extendedProps?: {
        id: number;
        status: string;
        urgency: string;
        test: string;
    };
};

const FullCalendarStub = defineComponent({
    name: 'FullCalendar',
    props: {
        events: {
            type: Array,
            default: () => [],
        },
        onEventClick: {
            type: Function,
            default: undefined,
        },
    },
    setup(props) {
        const trigger = () => {
            if (!props.onEventClick) {
                return;
            }

            const firstEvent = (props.events as CalendarEvent[] | undefined)?.[0];
            const baseEvent = firstEvent ?? {
                title: 'Radiografia',
                start: '2026-05-20 09:00:00',
                extendedProps: {
                    id: 1,
                    status: 'programada',
                    urgency: 'urgent',
                    test: 'Radiografia',
                },
            };

            props.onEventClick({
                el: {
                    getBoundingClientRect: () => ({
                        left: 100,
                        width: 80,
                        bottom: 220,
                        top: 160,
                    }),
                },
                event: {
                    title: baseEvent.title,
                    startStr: baseEvent.start ?? '2026-05-20 09:00:00',
                    extendedProps: baseEvent.extendedProps ?? {
                        id: 1,
                        status: 'programada',
                        urgency: 'urgent',
                        test: 'Radiografia',
                    },
                },
            });
        };

        return { trigger };
    },
    template: '<button data-testid="calendar-event" @click="trigger">Event</button>',
});

vi.mock('@inertiajs/vue3', async () => {
    const actual = await vi.importActual<typeof import('@inertiajs/vue3')>(
        '@inertiajs/vue3',
    );

    return {
        ...actual,
        Head: defineComponent({
            name: 'Head',
            props: {
                title: {
                    type: String,
                    required: false,
                },
            },
            template: '<div />',
        }),
        usePage: () => ({
            props: {
                dates: [
                    {
                        id: 1,
                        date_time: '2026-05-20 09:00:00',
                        estat: 'programada',
                        urgencia: 'urgent',
                        test: { name: 'Radiografia' },
                    },
                ],
                flash: {},
            },
        }),
        router: {
            post: vi.fn(),
        },
    };
});

describe('Calendar EventPopover', () => {
    let wrapper: ReturnType<typeof mount> | undefined;

    afterEach(() => {
        if (wrapper) {
            wrapper.unmount();
            wrapper = undefined;
        }

        document.body.innerHTML = '';
    });

    it('opens and closes the event popover', async () => {
        wrapper = mount(PatientDashboard, {
            attachTo: document.body,
            global: {
                stubs: {
                    FullCalendar: FullCalendarStub,
                    DeleteModal: defineComponent({ template: '<div />' }),
                    textNotify: defineComponent({ template: '<div />' }),
                },
            },
        });

        await wrapper.get('[data-testid="calendar-event"]').trigger('click');
        await nextTick();

        expect(
            document.body.querySelector('[aria-label="Detall de la cita"]'),
        ).toBeTruthy();
        expect(document.body.textContent).toContain('Radiografia');

        const closeButton = document.body.querySelector(
            'button[aria-label="Tancar"]',
        ) as HTMLButtonElement | null;

        expect(closeButton).toBeTruthy();
        closeButton?.click();
        await nextTick();

        expect(
            document.body.querySelector('[aria-label="Detall de la cita"]'),
        ).toBeNull();
    });
});