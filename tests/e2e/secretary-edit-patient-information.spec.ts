import { expect, test } from '@playwright/test';
import type { Page } from '@playwright/test';

const secretaryBase = (process.env.SECRETARY_URL_APP ?? process.env.URL_APP ?? '').replace(/\/+$/u, '');
const urlFor = (p: string) => `${secretaryBase}${p.startsWith('/') ? p : `/${p}`}`;
const secretaryEmail = process.env.TEST_SECRETARY_USER;
const secretaryPassword = process.env.TEST_SECRETARY_PASSWORD;

const patientId = 3;
const patientName = 'Montserrat Cos Grau';
const originalPatientInfo = {
    address: 'Carrer del Pacient 3',
    email: 'patient3@gmail.com',
    phone: '610000003',
};

const openPatientDetail = async (page: Page) => {
    await page.goto(urlFor('/patientsList'));
    await expect(page.getByText('Llistat de pacients')).toBeVisible();

    const searchInput = page.locator('input[placeholder="Cercar pacient..."]');
    await searchInput.fill(patientName);
    await page.waitForLoadState('networkidle');

    await page
        .locator('tbody tr', { hasText: patientName })
        .getByRole('button', { name: 'Detall pacient' })
        .click();

    await expect(page.getByText('Detall pacient')).toBeVisible();
};

const updatePatientInfo = async (
    page: Page,
    patientInfo: typeof originalPatientInfo,
) => {
    await page.getByRole('button', { name: 'Editar' }).click();
    await expect(page.getByText('Editar dades personals')).toBeVisible();

    await page.locator('input#address').fill(patientInfo.address);
    await page.locator('input#email').fill(patientInfo.email);
    await page.locator('input#phone').fill(patientInfo.phone);

    await page.getByRole('button', { name: 'Desar canvis' }).click();

    await expect(page.getByText('Dades modificades correctament')).toBeVisible();
    await expect(page.getByText('Editar dades personals')).not.toBeVisible();
    await expect(page.getByText(patientInfo.address)).toBeVisible();
    await expect(page.getByText(patientInfo.email)).toBeVisible();
    await expect(page.getByText(patientInfo.phone)).toBeVisible();
};

test('secretary can edit patient information', async ({ page }) => {
    expect(secretaryEmail, 'TEST_SECRETARY_USER must be configured').toBeTruthy();
    expect(
        secretaryPassword,
        'TEST_SECRETARY_PASSWORD must be configured',
    ).toBeTruthy();

    const timestamp = Date.now();
    const updatedPatientInfo = {
        address: `Carrer E2E ${timestamp}`,
        email: `patient3+e2e-${timestamp}@example.com`,
        phone: `67${String(timestamp).slice(-7)}`,
    };

    await page.goto(urlFor('/loginWorker'));
    await page.locator('input#email').fill(secretaryEmail!);
    await page.locator('input#password').fill(secretaryPassword!);
    await page.locator('[data-test="login-button"]').click();
    await page.waitForURL('**/dashboard');

    try {
        await openPatientDetail(page);
        await updatePatientInfo(page, updatedPatientInfo);
    } finally {
        await page.goto(urlFor(`/patientDetail/${patientId}`));
        await updatePatientInfo(page, originalPatientInfo);
    }
});
