import { expect, test } from '@playwright/test';

test('doctor can search patient list by NTS', async ({ page }) => {
    const doctorEmail = process.env.TEST_DOCTOR_USER;
    const doctorPassword = process.env.TEST_DOCTOR_PASSWORD;
    const patientNts = 'NTSS0000000001';

    const doctorBase = (process.env.DOCTOR_URL_APP ?? process.env.URL_APP ?? '').replace(/\/+$/u, '');
    const urlFor = (p: string) => `${doctorBase}${p.startsWith('/') ? p : `/${p}`}`;

    await page.goto(urlFor('/loginWorker'));
    await page.locator('input#email').fill(doctorEmail);
    await page.locator('input#password').fill(doctorPassword);
    await page.locator('[data-test="login-button"]').click();

    await page.waitForURL('**/dashboard');

    // Go to doctor's patient search page
    await page.goto(urlFor('/doctor/patient-search'));
    await expect(page.getByText('Buscar pacient')).toBeVisible();

    // Search by NTS 
    const ntsInput = page.locator('input#nts');
    await ntsInput.fill(patientNts);
    await page.locator('button:has-text("Buscar")').click();

    // Wait for the page to show the searched NTS in the patient details
    await page.waitForSelector(`text=${patientNts}`);
    await expect(page.getByText(patientNts)).toBeVisible();
});
