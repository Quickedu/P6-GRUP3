import { expect, test } from '@playwright/test';

/**
 * E2E test for the admin workers delete flow. It covers the following steps:
 * 1. Logs in as an admin worker
 * 2. Creates a new worker (unique per run)
 * 3. Deletes that worker and verifies it disappears from the table
 */
test('admin can delete a worker', async ({ page }) => {
    const adminEmail = process.env.TEST_USER;
    const adminPassword = process.env.TEST_PASSWORD;

    // Make values unique so the test can run multiple times without collisions
    const timestamp = Date.now();
    const workerName = `E2E Worker ${timestamp}`;
    const workerEmail = `e2e-worker-${timestamp}@example.com`;
    const workerNss = `NSS-${timestamp}`;
    const workerDni = `DNI-${timestamp}`;
    const workerLicense = `LIC-${timestamp}`;

    // Login flow
    await page.goto('/loginWorker');
    await page.locator('input#email').fill(adminEmail!);
    await page.locator('input#password').fill(adminPassword!);
    await page.locator('[data-test="login-button"]').click();

    await page.waitForURL('**/dashboard');

    // Open workers listing
    await page.goto('/workers');
    await expect(page.getByText('Llistat de treballadors')).toBeVisible();

    // Create a worker
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

    // Find the newly created row
    const workerRow = page.locator('tbody tr', { hasText: workerName });

    // Pagination: the new worker may end up on the next page depending on sorting
    await page.getByRole('button', { name: 'Següent' }).click();

    await expect(workerRow).toHaveCount(1);

    // Delete the worker and confirm in the modal
    await workerRow.getByTitle('Eliminar treballador').click();
    await page.getByRole('dialog').getByRole('button', { name: 'Eliminar' }).click();

    // The worker row should be removed from the table
    await expect(page.locator('tbody tr', { hasText: workerName })).toHaveCount(0);
});
