import { expect, test } from '@playwright/test';

test('admin can create a worker', async ({ page }) => {
    const adminEmail = process.env.TEST_USER;
    const adminPassword = process.env.TEST_PASSWORD;

    const timestamp = Date.now();
    const workerName = `E2E Worker ${timestamp}`;
    const workerEmail = `e2e-worker-${timestamp}@example.com`;
    const workerNss = `NSS-${timestamp}`;
    const workerDni = `DNI-${timestamp}`;
    const workerLicense = `LIC-${timestamp}`;

    await page.goto('/loginWorker');
    await page.locator('input#email').fill(adminEmail);
    await page.locator('input#password').fill(adminPassword);
    await page.locator('[data-test="login-button"]').click();

    await page.waitForURL('**/dashboard');

    await page.goto('/workers');
    await expect(page.getByText('Llistat de treballadors')).toBeVisible();

    await page.getByTitle('Afegir treballador').click();
    await page.locator('input#name').fill(workerName);
    await page.locator('input#email').fill(workerEmail);
    await page.locator('select#role').selectOption('doctor');
    await page.locator('input#password').fill('password123');
    await page.locator('input#nss').fill(workerNss);
    await page.locator('input#dni').fill(workerDni);
    await page.locator('input#address').fill('Carrer E2E 1');
    await page.locator('input#license_number').fill(workerLicense); 
    await page.locator('input#phone').fill('612345678');
    await page.getByRole('button', { name: 'Desar els canvis' }).click();

    const workerRow = page.locator('tbody tr', { hasText: workerName });

    await page.getByRole('button', { name: 'Següent' }).click();

    await expect(workerRow).toHaveCount(1);

    await workerRow.getByTitle('Eliminar treballador').click();
    await page.getByRole('dialog').getByRole('button', { name: 'Eliminar' }).click();

    await expect(page.locator('tbody tr', { hasText: workerName })).toHaveCount(0);
});