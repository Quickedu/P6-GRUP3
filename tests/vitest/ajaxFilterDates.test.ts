import fs from 'node:fs';
import path from 'node:path';
import { afterAll, beforeAll, describe, expect, it } from 'vitest';

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
const workerEmail = process.env.VITEST_WORKER_EMAIL ?? 'secretary1@gmail.com';
const workerPassword = process.env.VITEST_WORKER_PASSWORD ?? 'password123';
const validPatientNts = 'NTSS0000000001';

const jar: Record<string, string> = {};

const ensureHotFile = (): void => {
    if (!fs.existsSync(hotFilePath)) {
        fs.mkdirSync(path.dirname(hotFilePath), { recursive: true });
        fs.writeFileSync(hotFilePath, hotFileUrl, 'utf8');
        createdHotFile = true;
    }
};

const updateCookieJar = (
    response: Response,
    cookieJar: Record<string, string>,
): void => {
    const setCookie = response.headers.getSetCookie?.()
        ?? (response.headers.get('set-cookie')
            ? [response.headers.get('set-cookie')]
            : []);

    for (const cookie of setCookie) {
        const [pair] = cookie.split(';');

        if (!pair) {
            continue;
        }

        const index = pair.indexOf('=');

        if (index === -1) {
            continue;
        }

        const name = pair.slice(0, index).trim();
        const value = pair.slice(index + 1).trim();
        cookieJar[name] = value;
    }
};

const buildCookieHeader = (cookieJar: Record<string, string>): string =>
    Object.entries(cookieJar)
        .map(([name, value]) => `${name}=${value}`)
        .join('; ');

const loginAsSecretary = async (): Promise<void> => {
    ensureHotFile();

    const loginPage = await fetch(`${baseUrl}/loginWorker`, {
        redirect: 'manual',
    });

    if (!loginPage.ok) {
        const body = await loginPage.text();

        throw new Error(
            `Login page failed (${loginPage.status}). ${body.slice(0, 400)}`,
        );
    }

    updateCookieJar(loginPage, jar);

    const csrf = decodeURIComponent(jar['XSRF-TOKEN'] ?? '');

    if (!csrf) {
        throw new Error(
            'Missing XSRF token cookie. Is the app responding correctly?',
        );
    }

    const loginResponse = await fetch(`${baseUrl}/work/login`, {
        method: 'POST',
        redirect: 'manual',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-XSRF-TOKEN': csrf,
            Cookie: buildCookieHeader(jar),
            Accept: 'text/html,application/xhtml+xml',
        },
        body: new URLSearchParams({
            email: workerEmail,
            password: workerPassword,
        }).toString(),
    });

    if (![302, 303].includes(loginResponse.status)) {
        const body = await loginResponse.text();

        throw new Error(
            `Login failed (${loginResponse.status}). ${body.slice(0, 400)}`,
        );
    }

    updateCookieJar(loginResponse, jar);

    const sessionCookie = Object.keys(jar).find(
        (name) => name.endsWith('-session') || name === 'laravel_session',
    );

    if (!sessionCookie) {
        throw new Error(
            'Login did not set a session cookie. Check credentials or CSRF handling.',
        );
    }
};

describe('ajaxFilterDates', () => {
    beforeAll(async () => {
        await loginAsSecretary();
    }, 20000);

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

    it('returns filter dates response with proper structure', async () => {
        ensureHotFile();

        const response = await fetch(`${baseUrl}/filter-dates`, {
            headers: {
                Accept: 'application/json',
                Cookie: buildCookieHeader(jar),
            },
            redirect: 'manual',
        });

        if (!response.ok) {
            const body = await response.text();

            throw new Error(
                `filter-dates failed (${response.status}). ${body.slice(0, 400)}`,
            );
        }

        const contentType = response.headers.get('content-type') ?? '';

        expect(contentType).toContain('application/json');

        const data = await response.json();

        expect(data).toHaveProperty('status');
        expect(data).toHaveProperty('message');
        expect(data).toHaveProperty('data');
        expect(Array.isArray(data.data)).toBe(true);
    }, { timeout: 20000 });

    it('returns filter patient dates response with proper structure', async () => {
        ensureHotFile();

        const url = new URL('/filter-patient-dates', baseUrl);
        url.searchParams.set('nts', validPatientNts);

        const response = await fetch(url, {
            headers: {
                Accept: 'application/json',
                Cookie: buildCookieHeader(jar),
            },
            redirect: 'manual',
        });

        if (!response.ok) {
            const body = await response.text();

            throw new Error(
                `filter-patient-dates failed (${response.status}). ${body.slice(0, 400)}`,
            );
        }

        const contentType = response.headers.get('content-type') ?? '';

        expect(contentType).toContain('application/json');

        const data = await response.json();

        expect(data).toHaveProperty('status');
        expect(data).toHaveProperty('message');
        expect(data).toHaveProperty('data');
        expect(Array.isArray(data.data)).toBe(true);
    }, { timeout: 20000 });
});

afterAll(() => {
    if (createdHotFile && fs.existsSync(hotFilePath)) {
        fs.unlinkSync(hotFilePath);
    }
});
