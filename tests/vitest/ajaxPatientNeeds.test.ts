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

const secretaryEmail =
    process.env.VITEST_SECRETARY_EMAIL ?? 'secretary1@gmail.com';

const secretaryPassword =
    process.env.VITEST_SECRETARY_PASSWORD ?? 'password123';

const updateCookieJar = (
    response: Response,
    jar: Record<string, string>,
): void => {
    const setCookie = response.headers.getSetCookie?.()
        ?? (
            response.headers.get('set-cookie')
                ? [response.headers.get('set-cookie')]
                : []
        );

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

        fs.writeFileSync(
            hotFilePath,
            hotFileUrl,
            'utf8',
        );

        createdHotFile = true;
    }

    const jar: Record<string, string> = {};

    // GET login page
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

    const csrf = decodeURIComponent(
        jar['XSRF-TOKEN'] ?? '',
    );

    if (!csrf) {
        throw new Error(
            'Missing XSRF token cookie.',
        );
    }

    // LOGIN
    const loginResponse = await fetch(
        `${baseUrl}/work/login`,
        {
            method: 'POST',
            redirect: 'manual',
            headers: {
                'Cookie': buildCookieHeader(jar),
                'Accept': 'text/html,application/xhtml+xml',
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-XSRF-TOKEN': csrf,
            },
            body: new URLSearchParams({
                email: secretaryEmail,
                password: secretaryPassword,
            }).toString(),
        },
    );

    if (![302, 303].includes(loginResponse.status)) {
        const body = await loginResponse.text();

        throw new Error(
            `Login failed (${loginResponse.status}). ${body.slice(0, 400)}`,
        );
    }

    updateCookieJar(loginResponse, jar);

    const sessionCookie = Object.keys(jar).find(
        (name) =>
            name.endsWith('-session')
            || name === 'laravel_session',
    );

    if (!sessionCookie) {
        throw new Error(
            'Login did not set a session cookie.',
        );
    }

    return {
        jar,
        csrf,
    };
};

describe('ajaxPatientNeeds', () => {
    it('assigns a need to a patient', async () => {
        const { jar, csrf } = await loginSecretary();

        const patientId = 1;
        const needId = 1;

        const response = await fetch(
            `${baseUrl}/patients/${patientId}/needs`,
            {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'Cookie': buildCookieHeader(jar),
                    'X-XSRF-TOKEN': csrf,
                },
                body: new URLSearchParams({
                    need_id: String(needId),
                }).toString(),
            },
        );

        const bodyText = await response.text();

        let data: any;

        try {
            data = JSON.parse(bodyText);
        } catch {
            throw new Error(
                `Response is not valid JSON:\n${bodyText}`,
            );
        }

        // console.log('ASSIGN RESPONSE:', data);

        if (!response.ok) {
            throw new Error(
                `assignNeed failed (${response.status}): ${bodyText}`,
            );
        }

        // Ajusta esto según tu respuesta real
        expect(data).toHaveProperty('id');
        expect(data).toHaveProperty('name');
    }, { timeout: 20000 });

    it('removes a need from a patient', async () => {
        const { jar, csrf } = await loginSecretary();

        const patientId = 1;
        const needId = 1;

        // Primero asignamos el need
        const assignResponse = await fetch(
            `${baseUrl}/patients/${patientId}/needs`,
            {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'Cookie': buildCookieHeader(jar),
                    'X-XSRF-TOKEN': csrf,
                },
                body: new URLSearchParams({
                    need_id: String(needId),
                }).toString(),
            },
        );

        if (!assignResponse.ok) {
            const body = await assignResponse.text();

            throw new Error(
                `Failed assigning need before delete (${assignResponse.status}): ${body}`,
            );
        }

        // DELETE
        const deleteResponse = await fetch(
            `${baseUrl}/patients/${patientId}/needs/${needId}`,
            {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'Cookie': buildCookieHeader(jar),
                    'X-XSRF-TOKEN': csrf,
                },
            },
        );

        const bodyText = await deleteResponse.text();

        let data: any;

        try {
            data = JSON.parse(bodyText);
        } catch {
            throw new Error(
                `Delete response is not valid JSON:\n${bodyText}`,
            );
        }

        // console.log('DELETE RESPONSE:', data);

        if (!deleteResponse.ok) {
            throw new Error(
                `removeNeed failed (${deleteResponse.status}): ${bodyText}`,
            );
        }

        // Ajusta según tu respuesta real
        expect(deleteResponse.status).toBe(200);
    }, { timeout: 20000 });
});

afterAll(() => {
    if (
        createdHotFile
        && fs.existsSync(hotFilePath)
    ) {
        fs.unlinkSync(hotFilePath);
    }
});