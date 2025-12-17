const { test, expect } = require('@playwright/test');

const test_cid = '002312286'; // "Kōkogaku zasshi" arbitrarily chosen
const test_truncated_cid = '2312286'; // Truncated version
const test_htid = 'mdp.39015048895836'; // One of the htids on test_cid


test('XML with full CID', async ({ page }) => {
  const response = await page.goto(`/Record/${test_cid}.xml`);
  await expect(response.ok()).toBeTruthy();
});

test('XML with truncated CID', async ({ page }) => {
  const response = await page.goto(`/Record/${test_truncated_cid}.xml`);
  await expect(response.ok()).toBeTruthy();
});
