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
const doctorEmail = process.env.VITEST_DOCTOR_EMAIL ?? 'doctor1@gmail.com';
const doctorPassword = process.env.VITEST_DOCTOR_PASSWORD ?? 'password123';

const updateCookieJar = (response, jar) => {
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

const buildCookieHeader = (jar) =>
    Object.entries(jar)
        .map(([name, value]) => `${name}=${value}`)
        .join('; ');

const loginDoctor = async () => {
    if (!fs.existsSync(hotFilePath)) {
        fs.mkdirSync(path.dirname(hotFilePath), { recursive: true });
        fs.writeFileSync(hotFilePath, hotFileUrl, 'utf8');
        createdHotFile = true;
    }

    const jar = {};

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
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-XSRF-TOKEN': csrf,
            Cookie: buildCookieHeader(jar),
            Accept: 'text/html,application/xhtml+xml',
        },
        body: new URLSearchParams({
            email: doctorEmail,
            password: doctorPassword,
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

describe('ajaxReportPatient', () => {
    it('returns patient data for a patient', async () => {
        const jar = await loginDoctor();

        const response = await fetch(`${baseUrl}/formReport/patient/NTSS0000000001`, {
            headers: {
                Accept: 'application/json',
                Cookie: buildCookieHeader(jar),
            },
        });

        if (!response.ok) {
            const body = await response.text();
            throw new Error(`ajaxReportPatient failed (${response.status}). ${body.slice(0, 400)}`);
        }

        const contentType = response.headers.get('content-type') ?? '';
        if (!contentType.includes('application/json')) {
            const body = await response.text();
            throw new Error(`Expected JSON but got ${contentType}. ${body.slice(0, 400)}`);
        }

        const data = await response.json();

        expect(data).toMatchObject({
            nts: 'NTSS0000000001',
            id: 1,
            birth_date: '1988-01-15',
            name: 'Sílvia Torrents Pla',
            address: 'Carrer del Pacient 1',
        });
    });
});

afterAll(() => {
    if (createdHotFile && fs.existsSync(hotFilePath)) {
        fs.unlinkSync(hotFilePath);
    }
});
