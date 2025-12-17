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

// API JSON resonses are of the form
// {records: {cid: {...}, items: [...]}

test('Bib API catalog record brief', async ({ page }) => {
  const response = await page.goto(`/api/volumes/brief/recordnumber/${test_cid}.json`);
  expect(response.status()).toBe(200);
  expect(response.headers()["content-type"]).toContain('application/json');
  const body = await response.json();
  expect(body.records).toHaveProperty(test_cid);
  // No marc-xml property
  expect(body.records[test_cid]).not.toHaveProperty("marc-xml");
  expect(body.items.length).toBeGreaterThan(0);
});

test('Bib API catalog record full', async ({ page }) => {
  const response = await page.goto(`/api/volumes/full/recordnumber/${test_cid}.json`);
  expect(response.status()).toBe(200);
  expect(response.headers()["content-type"]).toContain('application/json');
  const body = await response.json();
  expect(body.records).toHaveProperty(test_cid);
  // Has marc-xml property
  expect(body.records[test_cid]).toHaveProperty("marc-xml");
  expect(body.items.length).toBeGreaterThan(0);
});

test('Bib API HTID brief', async ({ page }) => {
  const response = await page.goto(`/api/volumes/brief/htid/${test_htid}.json`);
  expect(response.status()).toBe(200);
  expect(response.headers()["content-type"]).toContain('application/json');
  const body = await response.json();
  expect(body.records).toHaveProperty(test_cid);
  // No marc-xml property
  expect(body.records[test_cid]).not.toHaveProperty("marc-xml");
  expect(body.items.length).toBeGreaterThan(0);
});

test('Bib API HTID full', async ({ page }) => {
  const response = await page.goto(`/api/volumes/full/htid/${test_htid}.json`);
  expect(response.status()).toBe(200);
  expect(response.headers()["content-type"]).toContain('application/json');
  const body = await response.json();
  expect(body.records).toHaveProperty(test_cid);
  // Has marc-xml property
  expect(body.records[test_cid]).toHaveProperty("marc-xml");
  expect(body.items.length).toBeGreaterThan(0);
});
