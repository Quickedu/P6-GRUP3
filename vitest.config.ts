import path from 'node:path';
import vue from '@vitejs/plugin-vue';
import { defineConfig } from 'vitest/config';

export default defineConfig({
    plugins: [vue()],
    resolve: {
        alias: {
            '@': path.resolve(__dirname, 'resources/js'),
        },
    },
    test: {
        environment: 'node',
        environmentMatchGlobs: [
            ['tests/vitest/rescheduleModal.test.ts', 'jsdom'],
            ['tests/vitest/admin-workers.test.js', 'jsdom'],
        ],
        include: ['tests/vitest/**/*.test.{js,ts}'],
    },
});
