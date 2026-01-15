const { test, expect } = require('@playwright/test');
const { open } = require('node:fs/promises');

const mix_nobody_ic_cid = '000123565'; // in nobody_records_test_docs.json
const only_nobody_cid = '000115150';

test.describe('Deleted records', () => {

  test.beforeAll(async ({ request }) => {
    const solrDocsFd = await open('test/slow/nobody_record_test_docs.json');
    const solrDocs = await solrDocsFd.readFile();
    solrDocsFd.close();

    const solrResponse = await(request.post('http://solr-sdr-catalog:9033/solr/catalog/update/json/docs?commit=true', { data: solrDocs, headers: { 'Content-Type': 'application/json' } } ));
    expect(solrResponse.status()).toEqual(200);
  });

  test('on a record with some ic and nobody items, does not show nobody items', async ({ request }) => {

    const recordResponse = await(request.get(`/Record/${mix_nobody_ic_cid}`, { maxRedirects: 0 }));
    const responseBody = await recordResponse.text();
    // shouldn't have htid with rights nobody
    expect(responseBody).not.toMatch('/cgi/pt?id=mdp.39015000037161');
    // should have htid with rights ic
    expect(responseBody).toMatch('/cgi/pt?id=mdp.39015005935898');
  });

  test('on a record with only nobody items, shows the item is not available', async ({ request }) => {
    // no redirect, just not found
    const recordResponse = await(request.get(`/Record/${only_nobody_cid}`));
    const responseBody = await recordResponse.text();
    expect(responseBody).toMatch("/cgi/pt?id=mdp.39015047762235");
    expect(responseBody).toMatch("no longer available");
  });

});
