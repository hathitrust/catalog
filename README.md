# HathiTrust Catalog Front-End

## Initial Setup
```bash
git clone --recurse-submodules https://github.com/hathitrust/catalog
docker compose up
```

This will use a Solr index pre-loaded with a sample set of 2,000 records.

Wait for everything to come up, then go to http://localhost:8080.

## Indexing Records

Follow the README from https://github.com/hathitrust/hathitrust_catalog_indexer
and use the instructions for `traject_external` to add records to the solr you
just started with `docker compose`.

## Testing

PlayWright browser tests are done automatically by GitHub Actions on push,
and can be done locally with:

```bash
docker compose run --rm playwright
```

## How to run the playwright tests updating Firebird before

```bash
git clone git@github.com:hathitrust/firebird-common.git
docker compose run firebird npm i
docker compose run firebird 
docker compose build
docker compose run --rm playwright
```

## What Works

See all records with http://localhost:8080/Search/Home

Facets work as expected.

Records can be viewed in a variety of formats:

* http://localhost:8080/Record/NNNNNNNNN
* http://localhost:8080/Record/NNNNNNNNN.marc
* http://localhost:8080/Record/NNNNNNNNN.xml
* http://localhost:8080/Record/NNNNNNNNN.json

## Known Issues
  
* Search forms that use `ls` won't work

* Advanced search doesn't work (needs additional setup?)

* Links to items don't work (point to the nonexistent
  localhost-full.babel.hathitrust.org rather than e.g. babel.hathitrust.org)

* Some rewriterules are imperfectly translated from Apache and unexpectedly
  append the query string
