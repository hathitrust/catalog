const { test, expect } = require('@playwright/test');

// Expect Chaucer to be a Norfolk man.
// We are getting baseURL from the config file,
// so URLs can be relative to that base.
test('check catalog', async ({ page, baseURL }) => {
    await page.goto('/Record/000292128');
    await expect(page).toHaveTitle(/a Norfolk man/);
});

test('no smarty debug console', async ({ page, baseURL }) => {
    await page.goto('/Record/000292128');
    const html = await page.content();
    expect(html).not.toMatch(/Smarty Debug Console/);
});
