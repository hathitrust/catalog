const { test, expect } = require('@playwright/test');

const test_cid = '100681548'; // "North Sea Pilot" arbitrarily chosen

// Verify that MARC display has values for `LDR`, `001`,  and `008`.
test('MARC display', async ({ page }) => {
  await page.goto(`/Record/${test_cid}.marc`);
  await expect(page.getByRole('cell', { name: 'LDR', exact: true })).toBeVisible();
  await expect(page.getByRole('cell', { name: '001', exact: true })).toBeVisible();
  await expect(page.getByRole('cell', { name: '008', exact: true })).toBeVisible();
  // Value for `001` should also be found in the URL.
  // (Equivalently, the catalog id from the URL should be found in the table (in this case as 001 value.)
  await expect(page.getByRole('cell', { name: test_cid, exact: true })).toBeVisible();
});
