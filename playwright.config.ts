import path from 'path';
import { defineConfig, devices } from '@playwright/test';
import dotenv from 'dotenv';

const envFile = process.env.PLAYWRIGHT_ENV_FILE ?? '.env.testing';

dotenv.config({
    path: path.resolve(process.cwd(), envFile),
});

const baseURL = process.env.URL_APP ?? process.env.APP_URL ?? '';

export default defineConfig({
    testDir: './tests/e2e',
    timeout: 60_000,
    fullyParallel: true,
    forbidOnly: !!process.env.CI,
    retries: process.env.CI ? 2 : 0,
    workers: process.env.CI ? 1 : undefined,
    use: {
        baseURL,
        trace: 'on-first-retry',
        headless: true,
    },
    projects: [
        {
            name: 'chromium',
            use: { ...devices['Desktop Chrome'] },
        },
        {
            name: 'firefox',
            use: { ...devices['Desktop Firefox'] },
        },
        {
            name: 'webkit',
            use: { ...devices['Desktop Safari'] },
        },
    ],
    reporter: 'html',
});
