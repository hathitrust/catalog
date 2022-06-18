# HathiTrust Catalog Front-End

## Initial Setup
```bash
git clone https://github.com/hathitrust/catalog
docker-compose up
```

This will use a Solr index pre-loaded with a sample set of 2,000 records.

## Indexing Records

Follow the README from https://github.com/hathitrust/hathitrust_catalog_indexer
and use the instructions for `traject_external` to add records to the solr you
just started with docker-compose.

## What Works

* http://localhost:8080/Search/Home

The search form doesn't work, but you can submit a query manually:
* http://localhost:8080/Search/Home?lookfor=stuff

Facets work as expected.

Records can be viewed in a variety of formats:

* http://localhost:8080/Record/NNNNNNNNN
* http://localhost:8080/Record/NNNNNNNNN.marc
* http://localhost:8080/Record/NNNNNNNNN.xml
* http://localhost:8080/Record/NNNNNNNNN.json

## Known Issues

* CSS and Javascript is external; you will need to supply the contents of
  `/common` from an existing production or development catalog instance, or
  clone and build it from `/htapps/repos/common.git` (it's not yet in GitHub)
  
* Search forms that use `ls` won't work

* Links to items don't work (point to the nonexistent
  localhost-full.babel.hathitrust.org rather than e.g. babel.hathitrust.org)

* Some rewriterules are imperfectly translated from Apache and unexpectedly
  append the query string

* Advanced search does not appear to work (appears to be a PHP 7.4 issue)
