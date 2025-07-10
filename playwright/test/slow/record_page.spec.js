const { test, expect } = require('@playwright/test');

const test_cid = '100681548'; // "North Sea Pilot" arbitrarily chosen
const full_view_cid = '008374016'; // "Domesday Book" all volumes full view
const limited_view_cid = '008700748'; // "On free exhibition day and evening at the Lihou Art Galleries" limited view 
const summary_cid = '006153412'; // "Toby Tyler" one of our sample records that has a 520 summary

// Test for standard static items on record page.
test('Record page', async ({ page }) => {
  await page.goto(`/Record/${test_cid}`);
  await page.getByRole('button', { name: 'Allow all cookies' }).click();
  await expect(page.getByRole('link', { name: 'View HathiTrust MARC record' })).toBeVisible();
  // It *seems* like page.getByRole('term',{ name: 'Language(s)' }) should
  // work, but it doesn't appear to -- both Chrome and Firefox show this role
  // and name; the element role does match in playwright, but the name doesn't
  // appear to. Bug?
  await expect(page.getByRole('term').filter({ hasText: 'Language(s)'})).toBeVisible();
  await expect(page.getByRole('term').filter({ hasText: 'Published'})).toBeVisible();
  await expect(page.getByRole('term').filter({ hasText: 'Edition'})).toBeVisible();
  await expect(page.getByRole('term').filter({ hasText: 'Physical Description'})).toBeVisible();
  await expect(page.getByRole('term').filter({ hasText: 'Locate a Print Version'})).toBeVisible();
  // currently disabled
  //  await expect(page.getByRole('heading', { name: 'Similar Items' })).toBeVisible();
});

// Make sure author link works as a search.
test('Follow record page Author link', async ({ page }) => {
  await page.goto(`/Record/${test_cid}`);
  await page.getByRole('button', { name: 'Allow all cookies' }).click();
  await page.getByRole('link', { name: 'United States.' }).click();
  await expect(page).toHaveURL(/lookfor/);
});

// On full-view records, there will be no "Limited (search only)" volumes
// and at least one "Full View" volume.
test('Full-view Record Page', async ({ page }) => {
  await page.goto(`/Record/${full_view_cid}`);
  await page.getByRole('button', { name: 'Allow all cookies' }).click();
  expect(await page.getByRole('row', { name: /Limited \(search only\)/i }).count()).toEqual(0);
  expect(await page.getByRole('row', { name: /Full view/i }).count()).toBeGreaterThan(0);
});

// On limited-view records, there will be at least one "Limited (search only)" volume
// and no "Full View" volumes.
test('Limited-view Record Page', async ({ page }) => {
  await page.goto(`/Record/${limited_view_cid}`);
  await page.getByRole('button', { name: 'Allow all cookies' }).click();
  expect(await page.getByRole('row', { name: /Limited \(search only\)/i }).count()).toBeGreaterThan(0);
  expect(await page.getByRole('row', { name: /Full view/i }).count()).toEqual(0);
});

// Make sure citation file link leads to download of "references.ris"
test('Citation download', async ({ page }) => {
  await page.goto(`/Record/${test_cid}`);
  await page.getByRole('button', { name: 'Allow all cookies' }).click();
  await expect(page.getByRole('link', { name: 'Cite this' })).toBeVisible();
  const downloadPromise = page.waitForEvent('download');
  await page.getByRole('link', { name: 'Export citation file' }).click();
  const download = await downloadPromise;
  expect(download.suggestedFilename()).toEqual('references.ris');
});

// Make sure there is a Summary (not Content Advice) field.
test('Record page has Summary but not Content Advice', async ({ page }) => {
  await page.goto(`/Record/${summary_cid}`);
  await page.getByRole('button', { name: 'Allow all cookies' }).click();
  await expect(page.getByRole('term').filter({ hasText: 'Summary' })).toBeVisible();
  await expect(page.getByRole('term').filter({ hasText: 'Content Advice' })).not.toBeVisible();
});
