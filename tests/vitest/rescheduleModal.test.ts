// @vitest-environment jsdom
import { mount } from '@vue/test-utils';
import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest';
import { defineComponent, nextTick, type ComponentPublicInstance } from 'vue';
import SecretaryDashboard from '../../resources/js/components/Dashboard/SecretaryDashboard.vue';

const DatesFilterComponentStub = defineComponent({
    name: 'DatesFilterComponent',
    emits: ['results-found', 'cleared'],
    template: '<div />',
});

const PatientFilterComponentStub = defineComponent({
    name: 'PatientFilterComponent',
    emits: ['results-found', 'cleared'],
    template: '<div />',
});

const DateDetailModalStub = defineComponent({
    name: 'DateDetailModal',
    props: {
        isOpen: {
            type: Boolean,
            default: false,
        },
        date: {
            type: Object,
            default: null,
        },
    },
    emits: ['update:isOpen', 'close'],
    template: '<div />',
});

const LinkStub = defineComponent({
    name: 'Link',
    props: {
        href: {
            type: String,
            default: '',
        },
    },
    template: '<a><slot /></a>',
});

const buildDate = () => ({
    id: 1,
    patient_id: 1,
    worker_id: 1,
    test_id: 1,
    date_time: '2026-05-20 09:00:00',
    time: 30,
    estat: 'programada',
    urgencia: 'no urgent',
    description: '',
    patient: {
        id: 1,
        name: 'Pacient Prova',
        nts: 'NTS1234567890',
    },
    worker: {
        id: 1,
        user: {
            id: 0,
            name: 'Dr. Prova',
        },
    },
    test: {
        id: 1,
        name: 'Radiografia',
    },
});

describe('SecretaryDashboard reschedule modal', () => {
    let wrapper: ReturnType<typeof mount> | undefined;
    let originalFetch: typeof globalThis.fetch;

    beforeEach(() => {
        originalFetch = globalThis.fetch;
        globalThis.fetch = vi.fn(async () => ({
            ok: true,
            json: async () => ({ status: 'success', data: [], message: '' }),
        }));
    });

    afterEach(() => {
        if (wrapper) {
            wrapper.unmount();
            wrapper = undefined;
        }
        document.body.innerHTML = '';
        globalThis.fetch = originalFetch;
    });

    it('opens the reschedule modal and closes on cancel', async () => {
        wrapper = mount(SecretaryDashboard, {
            attachTo: document.body,
            global: {
                components: {
                    DatesFilterComponent: DatesFilterComponentStub,
                    PatientFilterComponent: PatientFilterComponentStub,
                    DateDetailModal: DateDetailModalStub,
                    Link: LinkStub,
                },
            },
        });

        wrapper.findComponent(DatesFilterComponentStub).vm.$emit(
            'results-found',
            [buildDate()],
            '',
        );
        await nextTick();

        if (!wrapper) {
            throw new Error('Wrapper was not created');
        }

        const rescheduleButton = wrapper
            .findAll('button')
            .find((button) => button.text().trim() === 'Reprogramar');

        expect(rescheduleButton).toBeTruthy();
        await rescheduleButton!.trigger('click');
        await nextTick();

        expect(document.body.textContent).toContain('Reagendar la cita');

        const cancelButton = Array.from(
            document.body.querySelectorAll('button'),
        ).find((button) => button.textContent?.trim() === 'Tancar');

        expect(cancelButton).toBeTruthy();
        cancelButton.click();
        await nextTick();

        expect(document.body.textContent).not.toContain('Reagendar la cita');
    });
});
