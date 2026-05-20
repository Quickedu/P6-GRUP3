import fs from 'node:fs';
import path from 'node:path';
import { afterAll, describe, expect, it } from 'vitest';

const defaultUrl = process.env.LARAVEL_SAIL
    ? 'http://localhost'
    : 'http://localhost:8090';
const baseUrl = (process.env.VITEST_BASE_URL
    ?? process.env.APP_URL
    ?? defaultUrl).replace(/\/$/, '');
const hotFilePath = path.resolve('public/hot');
const hotFileUrl = process.env.VITE_DEV_SERVER_URL ?? 'http://localhost:5173';
let createdHotFile = false;
const secretaryEmail = process.env.VITEST_SECRETARY_EMAIL ?? 'secretary1@gmail.com';
const secretaryPassword = process.env.VITEST_SECRETARY_PASSWORD ?? 'password123';

const updateCookieJar = (response: Response, jar: Record<string, string>): void => {
    const setCookie = response.headers.getSetCookie?.()
        ?? (response.headers.get('set-cookie') ? [response.headers.get('set-cookie')] : []);

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
        jar[name] = value;
    }
};

const buildCookieHeader = (jar: Record<string, string>): string =>
    Object.entries(jar)
        .map(([name, value]) => `${name}=${value}`)
        .join('; ');

const loginSecretary = async () => {
    if (!fs.existsSync(hotFilePath)) {
        fs.mkdirSync(path.dirname(hotFilePath), { recursive: true });
        fs.writeFileSync(hotFilePath, hotFileUrl, 'utf8');
        createdHotFile = true;
    }

    const jar: Record<string, string> = {};

    const loginPage = await fetch(`${baseUrl}/loginWorker`, {
        redirect: 'manual',
    });
    if (!loginPage.ok) {
        const body = await loginPage.text();
        throw new Error(`Login page failed (${loginPage.status}). ${body.slice(0, 400)}`);
    }
    updateCookieJar(loginPage, jar);

    const csrf = decodeURIComponent(jar['XSRF-TOKEN'] ?? '');
    if (!csrf) {
        throw new Error('Missing XSRF token cookie. Is the app responding correctly?');
    }

    const loginResponse = await fetch(`${baseUrl}/work/login`, {
        method: 'POST',
        redirect: 'manual',
        headers: {
            Cookie: buildCookieHeader(jar),
            Accept: 'text/html,application/xhtml+xml',
        },
        body: new URLSearchParams({
            email: secretaryEmail,
            password: secretaryPassword,
        }).toString(),
    });

    if (![302, 303].includes(loginResponse.status)) {
        const body = await loginResponse.text();
        throw new Error(`Login failed (${loginResponse.status}). ${body.slice(0, 400)}`);
    }

    updateCookieJar(loginResponse, jar);
    const sessionCookie = Object.keys(jar).find(
        (name) => name.endsWith('-session') || name === 'laravel_session',
    );
    if (!sessionCookie) {
        throw new Error('Login did not set a session cookie. Check credentials');
    }

    return jar;
};

describe('ajaxPatientNeeds', () => {
    it('assigns a need to a patient', async () => {
        const jar = await loginSecretary();
        const patientId = 1;
        const needId = 1;

        const response = await fetch(
            `${baseUrl}/patients/${patientId}/needs`,
            {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Cookie': buildCookieHeader(jar),
                },
                body: JSON.stringify({ need_id: needId }),
            },
        );

        if (!response.ok) {
            const body = await response.text();
            throw new Error(`assignNeed failed (${response.status}). ${body.slice(0, 400)}`);
        }

        const contentType = response.headers.get('content-type') ?? '';
        if (!contentType.includes('application/json')) {
            const body = await response.text();
            throw new Error(`Expected JSON but got ${contentType}. ${body.slice(0, 400)}`);
        }

        const data = await response.json();

        expect(data).toHaveProperty('status');
        expect(data.status).toBe('success');
        expect(data).toHaveProperty('data');
        expect(data.data).toHaveProperty('id');
        expect(data.data).toHaveProperty('name');
    }, { timeout: 20000 });

    it('removes a need from a patient', async () => {
        const jar = await loginSecretary();
        const patientId = 1;
        const needId = 1;

        // First, assign a need
        const assignResponse = await fetch(
            `${baseUrl}/patients/${patientId}/needs`,
            {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Cookie': buildCookieHeader(jar),
                },
                body: JSON.stringify({ need_id: needId }),
            },
        );

        if (!assignResponse.ok) {
            throw new Error(`Failed to assign need before deletion test`);
        }

        // Then, delete the need
        const deleteResponse = await fetch(
            `${baseUrl}/patients/${patientId}/needs/${needId}`,
            {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'Cookie': buildCookieHeader(jar),
                },
            },
        );

        if (!deleteResponse.ok) {
            const body = await deleteResponse.text();
            throw new Error(`removeNeed failed (${deleteResponse.status}). ${body.slice(0, 400)}`);
        }

        const contentType = deleteResponse.headers.get('content-type') ?? '';
        if (!contentType.includes('application/json')) {
            const body = await deleteResponse.text();
            throw new Error(`Expected JSON but got ${contentType}. ${body.slice(0, 400)}`);
        }

        const data = await deleteResponse.json();

        expect(data).toHaveProperty('status');
        expect(data.status).toBe('success');
    }, { timeout: 20000 });
});

afterAll(() => {
    if (createdHotFile && fs.existsSync(hotFilePath)) {
        fs.unlinkSync(hotFilePath);
    }
});
