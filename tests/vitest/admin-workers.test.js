// @vitest-environment jsdom
import { App as InertiaApp } from '@inertiajs/vue3';
import { mount } from '@vue/test-utils';
import { describe, expect, it } from 'vitest';
import Worker from '@/pages/Workers/Admin/Worker.vue';

/**
 * Unit test for the admin "Workers" Inertia page
 */
const buildPage = (workers) => ({
    component: 'Workers/Admin/Worker',
    props: {
        workers,
        flash: {},
    },
    url: '/workers',
    version: null,
});

/**
 * Helper that mounts the page with the given workers list
 * Keeping this in one place makes the test easier to read
 */
const mountAdminWorkers = (workers) =>
    mount(InertiaApp, {
        props: {
            initialPage: buildPage(workers),
            initialComponent: Worker,
            resolveComponent: () => Worker,
        },
    });

describe('Admin workers page', () => {
    it('opens the edit modal with worker data', async () => {
        // Minimal worker object required for the edit modal fields we assert
        const worker = {
            id: 1,
            name: 'Anna Vila',
            email: 'anna@example.com',
            role: 'doctor',
            nss: '123456789',
            address: 'Carrer Major 1',
            dni: '00000000A',
            license_number: 'COL1234',
            phone: '600000000',
        };

        const wrapper = mountAdminWorkers([worker]);

        // Click the "Edit worker" action
        await wrapper.get('button[title="Editar treballador"]').trigger('click');

        // The modal should prefill the form with the selected worker details
        expect(wrapper.get('input#edit-email').element.value).toBe('anna@example.com');
        expect(wrapper.get('input#readonly-name').element.value).toBe('Anna Vila');
    });
});
