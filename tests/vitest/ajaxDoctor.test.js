import fs from 'node:fs';
import path from 'node:path';
import { afterAll, describe, expect, it } from 'vitest';

const defaultUrl = process.env.LARAVEL_SAIL
    ? 'http://localhost'
    : 'http://localhost:8080';
const baseUrl = (process.env.VITEST_BASE_URL
    ?? process.env.APP_URL
    ?? defaultUrl).replace(/\/$/, '');
const hotFilePath = path.resolve('public/hot');
const hotFileUrl = process.env.VITE_DEV_SERVER_URL ?? 'http://localhost:5173';
let createdHotFile = false;
const workerEmail = process.env.VITEST_WORKER_EMAIL ?? 'secretary1@gmail.com';
const workerPassword = process.env.VITEST_WORKER_PASSWORD ?? 'password123';

const formatDateOffset = (days) => {
    const date = new Date();
    date.setDate(date.getDate() + days);
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
};

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

const buildQuarterHourTimes = (startHour, endHour, requiredMinutes) => {
    const times = [];
    const startMinutes = startHour * 60;
    const endMinutes = endHour * 60;
    const lastStartMinutes = endMinutes - requiredMinutes;

    for (let minutes = startMinutes; minutes <= lastStartMinutes; minutes += 15) {
        const hours = String(Math.floor(minutes / 60));
        const mins = String(minutes % 60).padStart(2, '0');
        times.push(`${hours}:${mins}`);
    }

    return times;
};

describe('ajaxDoctor', () => {
    it('returns availability for a seeded doctor', async () => {
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
                email: workerEmail,
                password: workerPassword,
            }).toString(),
        });
        if (![302, 303].includes(loginResponse.status)) {
            const body = await loginResponse.text();
            throw new Error(`Login failed (${loginResponse.status}). ${body.slice(0, 400)}`);
        }
        const loginLocation = loginResponse.headers.get('location') ?? '';
        if (loginLocation && !loginLocation.includes('/dashboard')) {
            throw new Error(`Login did not redirect to dashboard. Location: ${loginLocation}`);
        }
        updateCookieJar(loginResponse, jar);
        const sessionCookie = Object.keys(jar).find(
            (name) => name.endsWith('-session') || name === 'laravel_session',
        );
        if (!sessionCookie) {
            throw new Error('Login did not set a session cookie. Check credentials or CSRF handling.');
        }

        const doctorId = 4;
        const requestedMinutes = 30;
        const url = new URL(`/doctorConsult/${doctorId}`, baseUrl);
        url.searchParams.set('date', formatDateOffset(30));
        url.searchParams.set('time', String(requestedMinutes));

        const response = await fetch(url, {
            headers: {
                Accept: 'application/json',
                Cookie: buildCookieHeader(jar),
            },
        });

        if (!response.ok) {
            const body = await response.text();
            throw new Error(`ajaxDoctor failed (${response.status}). ${body.slice(0, 400)}`);
        }

        const contentType = response.headers.get('content-type') ?? '';
        if (!contentType.includes('application/json')) {
            const body = await response.text();
            throw new Error(`Expected JSON but got ${contentType}. ${body.slice(0, 400)}`);
        }

        const data = await response.json();

        expect(data).toHaveProperty('status');
        expect(data).toHaveProperty('message');
        expect(data).toHaveProperty('data');
        expect(data.data).toHaveProperty('start_times');
        expect(data.data.start_times).toEqual(
            buildQuarterHourTimes(8, 15, requestedMinutes + 10),
        );
    });
});

afterAll(() => {
    if (createdHotFile && fs.existsSync(hotFilePath)) {
        fs.unlinkSync(hotFilePath);
    }
});
