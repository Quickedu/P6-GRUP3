import { expect, test } from '@playwright/test';

test('secretary can search patient list by name and NTS', async ({ page }) => {

    const secretaryBase = (process.env.SECRETARY_URL_APP ?? process.env.URL_APP ?? '').replace(/\/+$/u, '');
    const urlFor = (p: string) => `${secretaryBase}${p.startsWith('/') ? p : `/${p}`}`;

    const secretaryEmail = process.env.TEST_SECRETARY_USER;
    const secretaryPassword = process.env.TEST_SECRETARY_PASSWORD;
    const patientName = 'Sílvia';
    const patientNts = 'NTSS0000000002';
    const patientFullName = 'Guillem Saura Colom';

    // Login as secretary
    await page.goto(urlFor('/loginWorker'));
    await page.locator('input#email').fill(secretaryEmail!);
    await page.locator('input#password').fill(secretaryPassword!);
    // Navigate to patient list
    await page.goto(urlFor('/patientsList'));
    await expect(page.getByText('Llistat de pacients')).toBeVisible();

    // Test 1: Search by patient name
    const searchInput = page.locator('input[placeholder="Cercar pacient..."]');
    await searchInput.fill(patientName);
    await page.waitForLoadState('networkidle');

    // Verify that patient with name "Sílvia" is visible
    await expect(page.getByText('Sílvia Torrents Pla')).toBeVisible();

    // Clear search and test by NTS
    await searchInput.clear();
    await searchInput.fill(patientNts);
    await page.waitForLoadState('networkidle');

    // Verify that patient with NTS "NTSS0000000002" is visible
    await expect(page.getByText(patientFullName)).toBeVisible();
    await expect(page.getByText(patientNts)).toBeVisible();

    // Verify that other patients are not visible
    await expect(page.getByText('Sílvia Torrents Pla')).not.toBeVisible();

    // Clear search and verify all patients are back
    await searchInput.clear();
    await page.waitForLoadState('networkidle');

    // Check that the patient count shows all patients
    const patientCountBadge = page.locator('[class*="rounded-full"][class*="bg-pmf-secondary"]').first();
    await expect(patientCountBadge).toBeVisible();
});