const { test, expect } = require('@playwright/test');
const { open } = require('node:fs/promises');

const redirect_cid = '011398992'; // in deleted_records_test_docs.json
const redirect_target_cid = '103192851';
const deleted_cid = '9999'; // arbitrary choice

test.describe('Deleted records', () => {
  test.beforeAll(async ({ request }) => {
    const solrDocsFd = await open('test/slow/deleted_record_test_docs.json');
    const solrDocs = await solrDocsFd.readFile();
    solrDocsFd.close();

    const solrResponse = await request.post('http://solr-sdr-catalog:9033/solr/catalog/update/json/docs?commit=true', {
      data: solrDocs,
      headers: { 'Content-Type': 'application/json' },
    });
    expect(solrResponse.status()).toEqual(200);
  });

  test('redirects to another record with corresponding old_ids', async ({ request }) => {
    // get old record ID; should redirect to new record
    const recordResponse = await request.get(`/Record/${redirect_cid}`, { maxRedirects: 0 });
    expect(recordResponse.status()).toEqual(301);
    expect(recordResponse.headers().location).toEqual(`/Record/${redirect_target_cid}`);
  });

  test('with no corresponding old_ids says no such catalog record', async ({ request }) => {
    // no redirect, just not found
    const recordResponse = await request.get(`/Record/${deleted_cid}`, { maxRedirects: 0 });
    expect(recordResponse.status()).toEqual(404);
    const responseBody = await recordResponse.text();
    expect(responseBody).toContain('Catalog record not found');
  });
});
