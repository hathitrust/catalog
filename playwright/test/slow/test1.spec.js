const { test, expect } = require('@playwright/test');

test('check catalog2', async ({ page, baseURL }) => {
    await page.goto('/Record/000292128');
    await expect(page).toHaveTitle(/a Norfolk man/);
});
