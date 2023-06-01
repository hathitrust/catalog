const { test, expect } = require('@playwright/test');

test('Citation page', async ({ page }) => {
  await page.goto('/Record/100681548/Cite');
  // Should include a title, an APA citation and MLA citation.
  await expect(page.getByRole('heading', { name: /north sea pilot/i })).toBeVisible();
  await expect(page.getByRole('heading', { name: 'APA Citation' })).toBeVisible();
  await expect(page.getByRole('heading', { name: 'MLA Citation' })).toBeVisible();
});
