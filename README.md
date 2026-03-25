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

## How to run a PHP script that shows the Solr query

```bash
docker compose run vufind php bin/PrintSolrQuery.php 'charles dickens OR "weekly"' title 
```

The output of this script loooks like:

```bash

----- Tokenized Search ----- : ["charles","dickens","OR","\"weekly\""]
 ----- Classified Tokens ----- : [{"type":"term","value":"charles"},{"type":"term","value":"dickens"},{"type":"operator","value":"OR"},{"type":"phrase","value":{"text":"weekly","slop":null}}]
 ----- Tokens after collapsing compound phrases ----- : [{"type":"term","value":"charles"},{"type":"compound_phrase","value":{"tokens":[{"type":"term","value":"dickens"},{"type":"operator","value":"OR"},{"type":"phrase","value":{"text":"weekly","slop":null}}]}}]
 ----- Escaped Parts ----- : ["charles","dickens OR \"weekly\""]
 ----- Semantic Structure ----- : {"onephrase":"\"charles dickens OR weekly\"","and":"charles AND dickens OR \"weekly\"","or":"charles OR dickens OR \"weekly\"","asis":"charles (dickens OR \"weekly\")","compressed":"charles\\(dickensOR\\\"weekly\\\"\\)","exactmatcher":"charlesdickensorweekly","emstartswith":"charlesdickensorweekly*"}
 -----  Solr Search ----- : "(title_ab:(charlesdickensorweekly)^25000 OR title_a:(charlesdickensorweekly)^15000 OR titleProper:(charlesdickensorweekly*)^8000 OR titleProper:(\"charles dickens OR weekly\")^1200 OR titleProper:(charles AND dickens OR \"weekly\")^120 OR title_topProper:(\"charles dickens OR weekly\")^600 OR title_topProper:(charles AND dickens OR \"weekly\")^60 OR title_restProper:(\"charles dickens OR weekly\")^400 OR title_restProper:(charles AND dickens OR \"weekly\")^40 OR series:(\"charles dickens OR weekly\")^500 OR series:(charles AND dickens OR \"weekly\")^50 OR series2:(\"charles dickens OR weekly\")^500 OR series2:(charles AND dickens OR \"weekly\")^50 OR title:(charles AND dickens OR \"weekly\")^30 OR title_top:(charles AND dickens OR \"weekly\")^20 OR title_rest:(charles AND dickens OR \"weekly\")^1)"

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
