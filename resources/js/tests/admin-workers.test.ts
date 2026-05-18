import { describe, expect, it } from 'vitest';
import { mount } from '@vue/test-utils';
import { App as InertiaApp } from '@inertiajs/vue3';
import Worker from '../pages/Workers/Admin/Worker.vue';

const buildPage = (workers: unknown[]) => ({
    component: 'Workers/Admin/Worker',
    props: {
        workers,
        flash: {},
    },
    url: '/workers',
    version: null,
});

const mountAdminWorkers = (workers: unknown[]) =>
    mount(InertiaApp, {
        props: {
            initialPage: buildPage(workers),
            initialComponent: Worker,
            resolveComponent: () => Worker,
        },
    });

describe('Admin workers page', () => {
    it('opens the edit modal with worker data', async () => {
         const worker = {
            id: 1,
            name: 'Anna Vila',
            email: 'anna@example.com',
        };

        const wrapper = mountAdminWorkers([worker]);

        await wrapper
            .get('button[title="Editar treballador"]')
            .trigger('click');

        expect(wrapper.get('input#edit-email').element.value).toBe('anna@example.com');
        expect(wrapper.get('input#readonly-name').element.value).toBe('Anna Vila');
    });
});
