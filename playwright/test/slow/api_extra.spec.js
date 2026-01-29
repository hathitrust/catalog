// Additional API-like endpoints

// ===== Record/HTID/htid =====
// Redirects via a 301 to Record/cid
const { test, expect } = require('@playwright/test');

const test_cid = '002312286'; // "Kōkogaku zasshi" arbitrarily chosen
const test_htid = 'uc1.$b385357'; // One of the many HTIDs on test_cid

test('/Record/HTID/ redirects to /Record/', async ({ page }) => {
  const response = await page.goto(`/Record/HTID/${test_htid}`);
  // A redirect happens in the middle, but `goto` follows it so look at final URL.
  expect(page.url().endsWith(`/Record/${test_cid}`)).toBeTruthy();
});

test('/Record/HTID/ not found', async ({ page }) => {
  // Let's throw in a reserved character for `lucene_escape` to chew on.
  const response = await page.goto('/Record/HTID/no*such*id');
  // We expect a 404, no redirect
  expect(response.status()).toBe(404);
});

// ===== Record/cid.xml =====
test('/Record/cid.xml returns XML', async ({ page }) => {
  const response = await page.goto(`/Record/${test_cid}.xml`);
  expect(response.status()).toBe(200);
  expect(response.headers()["content-type"]).toContain('text/xml');
});

test('/Record/cid.xml returns 404 if not found', async ({ page }) => {
  const response = await page.goto('/Record/000.xml');
  expect(response.status()).toBe(404);
  expect(response.headers()["content-type"]).toContain('text/html');
});

// ===== MARCXML/htid =====
test('/MARCXML/htid returns XML', async ({ page }) => {
  const response = await page.goto(`/MARCXML/${test_htid}`);
  expect(response.status()).toBe(200);
  expect(response.headers()["content-type"]).toContain('text/xml');
});

test('/MARCXML/htid returns 404 if not found', async ({ page }) => {
  const response = await page.goto('/MARCXML/no*such*id');
  expect(response.status()).toBe(404);
  expect(response.headers()["content-type"]).toContain('text/html');
});
