import { fileURLToPath } from 'node:url';
import { resolve } from 'node:path';
import vue from '@vitejs/plugin-vue';
import { defineConfig } from 'vitest/config';

export default defineConfig({
    plugins: [vue()],
    resolve: {
        alias: {
            '@': resolve(fileURLToPath(new URL('.', import.meta.url)), 'resources/js'),
        },
    },
    test: {
        environment: 'jsdom',
        include: ['tests/vitest/**/*.test.{js,ts}'],
    },
});
