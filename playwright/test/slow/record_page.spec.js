const { test, expect } = require('@playwright/test');

const test_cid = '100681548'; // "North Sea Pilot" arbitrarily chosen
const full_view_cid = '008374016'; // "Domesday Book" all volumes full view
const limited_view_cid = '008700748'; // "On free exhibition day and evening at the Lihou Art Galleries" limited view 

// Test for standard static items on record page.
test('Record page', async ({ page }) => {
  await page.goto(`/Record/${test_cid}`);
  await expect(page.getByRole('link', { name: 'View HathiTrust MARC record' })).toBeVisible();
  await expect(page.getByRole('cell', { name: 'Language(s):' })).toBeVisible();
  await expect(page.getByRole('cell', { name: 'Published:' })).toBeVisible();
  await expect(page.getByRole('cell', { name: 'Edition:' })).toBeVisible();
  await expect(page.getByRole('cell', { name: 'Physical Description:' })).toBeVisible();
  await expect(page.getByRole('cell', { name: 'Locate a Print Version:' })).toBeVisible();
  await expect(page.getByRole('heading', { name: 'Similar Items' })).toBeVisible();
});

// Make sure author link works as a search.
test('Follow record page Author link', async ({ page }) => {
  await page.goto(`/Record/${test_cid}`);
  await page.getByRole('link', { name: 'United States.' }).click();
  await expect(page).toHaveURL(/lookfor/);
});

// On full-view records, there will be no "Limited (search only)" volumes
// and at least one "Full View" volume.
test('Full-view Record Page', async ({ page }) => {
  await page.goto(`/Record/${full_view_cid}`);
  expect(await page.getByRole('row', { name: /Limited \(search only\)/i }).count()).toEqual(0);
  expect(await page.getByRole('row', { name: /Full view/i }).count()).toBeGreaterThan(0);
});

// On limited-view records, there will be at least one "Limited (search only)" volume
// and no "Full View" volumes.
test('Limited-view Record Page', async ({ page }) => {
  await page.goto(`/Record/${limited_view_cid}`);
  expect(await page.getByRole('row', { name: /Limited \(search only\)/i }).count()).toBeGreaterThan(0);
  expect(await page.getByRole('row', { name: /Full view/i }).count()).toEqual(0);
});

// Make sure citation file link leads to download of "references.ris"
test('Citation download', async ({ page }) => {
  await page.goto(`/Record/${test_cid}`);
  await expect(page.getByRole('link', { name: 'Cite this' })).toBeVisible();
  const downloadPromise = page.waitForEvent('download');
  await page.getByRole('link', { name: 'Export citation file' }).click();
  const download = await downloadPromise;
  expect(download.suggestedFilename()).toEqual('references.ris');
});
