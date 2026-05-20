import fs from 'node:fs';
import path from 'node:path';
import { afterAll, describe, expect, it } from 'vitest';

const defaultUrl = process.env.LARAVEL_SAIL
    ? 'http://localhost'
    : 'http://localhost:8090';

const baseUrl = (
    process.env.VITEST_BASE_URL
    ?? process.env.APP_URL
    ?? defaultUrl
).replace(/\/$/, '');

const hotFilePath = path.resolve('public/hot');
const hotFileUrl = process.env.VITE_DEV_SERVER_URL ?? 'http://localhost:5173';

let createdHotFile = false;

const ensureHotFile = (): void => {
    if (!fs.existsSync(hotFilePath)) {
        fs.mkdirSync(path.dirname(hotFilePath), { recursive: true });
        fs.writeFileSync(hotFilePath, hotFileUrl, 'utf8');
        createdHotFile = true;
    }
};

describe('ajaxPatientNeeds', () => {
    it('loads the worker login page', async () => {
        ensureHotFile();

        const response = await fetch(`${baseUrl}/loginWorker`, {
            redirect: 'manual',
        });

        const body = await response.text();

        if (!response.ok) {
            throw new Error(
                `Login page failed (${response.status}). ${body.slice(0, 400)}`,
            );
        }

        expect(response.status).toBe(200);
    }, { timeout: 20000 });
});

afterAll(() => {
    if (createdHotFile && fs.existsSync(hotFilePath)) {
        fs.unlinkSync(hotFilePath);
    }
});