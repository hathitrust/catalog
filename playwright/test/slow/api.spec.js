const { test, expect } = require('@playwright/test');

const test_cid = '002312286'; // "Kōkogaku zasshi" arbitrarily chosen
const test_cid2 = '100673017'; // "Kōkogaku zasshi" arbitrarily chosen
const test_truncated_cid = '2312286'; // Truncated version
const test_htid = 'mdp.39015048895836'; // One of the htids on test_cid
const test_htid2 = 'umn.31951d03005375z'; // Another htid, this one corresponding test_cid2


test('XML with full CID', async ({ page }) => {
  const response = await page.goto(`/Record/${test_cid}.xml`);
  expect(response.ok()).toBeTruthy();
  expect(response.headers()["content-type"]).toContain('text/xml');
});

test('XML with truncated CID', async ({ page }) => {
  const response = await page.goto(`/Record/${test_truncated_cid}.xml`);
  expect(response.ok()).toBeTruthy();
  expect(response.headers()["content-type"]).toContain('text/xml');
});

test('XML with HTID', async ({ page }) => {
  const response = await page.goto(`/MARCXML/${test_htid}`);
  expect(response.status()).toBe(200);
  expect(response.headers()["content-type"]).toContain('text/xml');
});

// See https://github.com/hathitrust/catalog/wiki/Volume-API
//   for an explanation of these inscrutable "b/qf/qv.t", "b/t/Q" codes.
// API JSON single-record responses are of the form
// {records: {cid: {...}, items: [...]}
// =========== single-id query, b/qf/qv.t endpoint

test('Bib API b/qf/qv.t brief recordnumber', async ({ page }) => {
  const response = await page.goto(`/api/volumes/brief/recordnumber/${test_cid}.json`);
  expect(response.status()).toBe(200);
  expect(response.headers()["content-type"]).toContain('application/json');
  const body = await response.json();
  expect(body.records).toHaveProperty(test_cid);
  // No marc-xml property
  expect(body.records[test_cid]).not.toHaveProperty("marc-xml");
  expect(body.items.length).toBeGreaterThan(0);
});

test('Bib API b/qf/qv.t full recordnumber', async ({ page }) => {
  const response = await page.goto(`/api/volumes/full/recordnumber/${test_cid}.json`);
  expect(response.status()).toBe(200);
  expect(response.headers()["content-type"]).toContain('application/json');
  const body = await response.json();
  expect(body.records).toHaveProperty(test_cid);
  // Has marc-xml property
  expect(body.records[test_cid]).toHaveProperty("marc-xml");
  expect(body.items.length).toBeGreaterThan(0);
});

test('Bib API b/qf/qv.t brief htid', async ({ page }) => {
  const response = await page.goto(`/api/volumes/brief/htid/${test_htid}.json`);
  expect(response.status()).toBe(200);
  expect(response.headers()["content-type"]).toContain('application/json');
  const body = await response.json();
  expect(body.records).toHaveProperty(test_cid);
  // No marc-xml property
  expect(body.records[test_cid]).not.toHaveProperty("marc-xml");
  expect(body.items.length).toBeGreaterThan(0);
});

test('Bib API b/qf/qv.t full htid', async ({ page }) => {
  const response = await page.goto(`/api/volumes/full/htid/${test_htid}.json`);
  expect(response.status()).toBe(200);
  expect(response.headers()["content-type"]).toContain('application/json');
  const body = await response.json();
  expect(body.records).toHaveProperty(test_cid);
  // Has marc-xml property
  expect(body.records[test_cid]).toHaveProperty("marc-xml");
  expect(body.items.length).toBeGreaterThan(0);
});

// =========== single-id query, b/t/Q endpoint
test('Bib API b/t/Q brief 1-htid', async ({ page }) => {
  const response = await page.goto(`/api/volumes/brief/json/htid:${test_htid}`);
  expect(response.status()).toBe(200);
  expect(response.headers()["content-type"]).toContain('application/json');
  const body = await response.json();
  expect(body[`htid:${test_htid}`]).toHaveProperty('records');
  expect(body[`htid:${test_htid}`]).toHaveProperty('items');
  // Has no marc-xml property
  expect(body[`htid:${test_htid}`].records[test_cid]).not.toHaveProperty("marc-xml");
  expect(Object.keys(body)).toHaveLength(1);
});

// =========== multi-id query, b/t/Q endpoint
test('Bib API b/t/Q brief 2-htid', async ({ page }) => {
  const response = await page.goto(`/api/volumes/brief/json/htid:${test_htid}|htid:${test_htid2}`);
  expect(response.status()).toBe(200);
  expect(response.headers()["content-type"]).toContain('application/json');
  const body = await response.json();
  expect(body[`htid:${test_htid}`]).toHaveProperty('records');
  expect(body[`htid:${test_htid}`]).toHaveProperty('items');
  expect(body[`htid:${test_htid2}`]).toHaveProperty('records');
  expect(body[`htid:${test_htid2}`]).toHaveProperty('items');
  // Has no marc-xml property
  expect(body[`htid:${test_htid}`].records[test_cid]).not.toHaveProperty("marc-xml");
  expect(body[`htid:${test_htid2}`].records[test_cid2]).not.toHaveProperty("marc-xml");
  expect(Object.keys(body)).toHaveLength(2);
});

// Inconsistencies and possible misfeatures
// b/t/Q allows any value for brevity and defaults to "brief"
// This is inconsistent with b/qf/qv.t which requires b to be in {brief,full}
test('Bib API b/t/Q b? 200', async ({ page }) => {
  const response = await page.goto(`/api/volumes/blah/json/htid:${test_htid}`);
  expect(response.status()).toBe(200);
});

// Error conditions (rewrite side)
// These will be caught by the rewrite rules and return 404
// These tests time out with firefox but succeed with all others
test('Bib API b/qf/qv.t b? 404', async ({ page, browserName }) => {
  test.skip(browserName === 'firefox', 'times out with firefox for unknown reason');
  const response = await page.goto(`/api/volumes/blah/htid/${test_htid}.json`);
  expect(response.status()).toBe(404);
});

test('Bib API b/qf/qv.t t? 404', async ({ page, browserName }) => {
  test.skip(browserName === 'firefox', 'times out with firefox for unknown reason');
  const response = await page.goto(`/api/volumes/brief/htid/${test_htid}.blah`);
  expect(response.status()).toBe(404);
});

test('Bib API b/t/Q t? 404', async ({ page, browserName }) => {
  test.skip(browserName === 'firefox', 'times out with firefox for unknown reason');
  const response = await page.goto(`/api/volumes/brief/blah/htid:${test_htid}`);
  expect(response.status()).toBe(404);
});

// Error conditions (volumes.php side)
// These will be caught by volumes.php and return 400
test('Bib API b/qf/qv.t qf? 400', async ({ page }) => {
  const response = await page.goto(`/api/volumes/brief/blah/${test_htid}.json`);
  expect(response.status()).toBe(400);
});

test('Bib API b/t/Q qf? 400', async ({ page }) => {
  const response = await page.goto(`/api/volumes/brief/json/blah:${test_htid}`);
  expect(response.status()).toBe(400);
});

// Empty results
test('Bib API b/qf/qv.t qv? 200', async ({ page }) => {
  const response = await page.goto('/api/volumes/brief/htid/blah.json');
  expect(response.status()).toBe(200);
});

test('Bib API b/t/Q qv? 200', async ({ page }) => {
  const response = await page.goto('/api/volumes/brief/json/htid:blah');
  expect(response.status()).toBe(200);
});

// Endpoint variants with implied brevity "brief"
test('Bib API t/Q htid', async ({ page }) => {
  const response = await page.goto(`/api/volumes/json/htid:${test_htid}`);
  expect(response.status()).toBe(200);
  const body = await response.json();
  expect(body[`htid:${test_htid}`]).toHaveProperty('records');
  expect(body[`htid:${test_htid}`]).toHaveProperty('items');
});

test('Bib API qf/qv.t htid', async ({ page }) => {
  const response = await page.goto(`/api/volumes/htid/${test_htid}.json`);
  expect(response.status()).toBe(200);
  const body = await response.json();
  expect(body.items.length).toBeGreaterThan(0);
});
