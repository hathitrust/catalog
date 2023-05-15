# Catalog

This document is a list of (all?) the pages known to be testable by https://github.com/hathitrust/catalog.

Some page elements change when the viewport width goes down below a certain value.
This is called "width-shrink" henceforth, and currently happens at 62em.
Playwright can only set viewport in pixels. 1 em is roughly 16 pixels (ymmv).
So, for width-shrink, try setting the viewport to just below `(16 * 62 =) 992`.

## Home Page

```
http://localhost:8080/
```

Check (at minimum visibility and clickability):

* Header menu:
* - Home, About [+], Collections, Help, Feedback, LOG IN
* Footer links:
* - Home, About, Collections, Help, Feedback, Accessibility, Take-Down Policy, Privacy, Contact
* - Footer disapprears when viewport width goes down to some number or ratio.
* Search links:
* - Advanced full-text search, Advanced catalog search, Search tips
* Full-text / Catalog radiobuttons (changes input placeholder and field selector)
* Field selector (only when doing Catalog search): each field [All Fields, Title, Author, Subject, ISBN/ISSN, Publisher, Series Title] makes different query
* Width-shrink:
* - Menu compacts to a single "☰ Menu"
* - Logo and LOG IN button move into `<header/>`
* - Footer disappears

## Search Results Page

```
/Search/Home?lookfor=norfolk
```

Check (at minimum visibility and clickability):

* A search for something dumb like "zxcxz" should return no matches, and say so
* Each `<article/>` in the search results page should have a  Cover, a Title and possibly an Author
* Each result should link to a Catalog Record and one of:
* - "Limited (Access Permitted)", "Full View" link, "Temporary Access", "Limited (search only)" link, or "(view record to see multiple volumes)"
* Facets:
* - adding a facet should narrow results
* - removing a facet should widen results
* - page should list current facets in URL and in `active-filters-list`
* - browser back button and "clear filters" should both bring back to unfaceted results
* - "show all 30 Author Filters" links (or whatever) should expand to the full set of available facets
* Sort By should have the desired effect:
* - Relevance, Date, Author, Title
* Items Per Page should have the desired effect:
* - 20, 50, 100
* Pagination links should go where they say:
* - Next Page, Previous Page, "numbered page"
* Width-shrink
* - Options and filters are hidden but can be expanded from the "Options/Filters" container and they all work as under full-width viewport.


## Catalog Record Page

```
/Record/100681548
```

Check (at minimum visibility and clickability):

* "Cite this" link
* "Export citation file" link, downloadable
* "View HathiTrust MARC record" button
* `div.article-heading` should contain the title
* `table.citation` should exist with some info about Language(s), Published, Subjects, Physical Description etc.
* - Values for various Author and Subject fields should be clickable and take you to aother search results page
* - "Find in a library" link that goes out to worldcat OR "Find in a library service is not available from this catalog. Search Worldcat"
* `ul.similar-items` should exist and be populated with at least some other titles
* Width-shrink
* - The right sidebar with "Similar Items" moves to the bottom of the page.

Some further possible differences depending on access ("Limited (Access Permitted)", "Full View" link, "Temporary Access", "Limited (search only)" link, or "(view record to see multiple volumes)")

### Limited (Access Permitted) Record Page

TBD (Not sure if we can test this yet without figuring out how to authenticate)

### Limiteded (search only) Record Page

* Should include `table.viewability-table` with exactly 1 row of volume links, all have access "Limited (search only)"

### Full View Record Page

* Should include `table.viewability-table` with volume links, all have access "Full view"

### (view record to see multiple volumes) Record Page

* Should include `table.viewability-table` with 2+ rows of volume links, access should include 1 "Full view" and 1 "Limited (search only)"

### Temporary Access Record Page

TBD

## Cite This Page

```
/Record/100681548/Cite
```

Should include a title, an APA citation and MLA citation.

## View HathiTrust MARC Record Page

```
http://localhost:8080/Record/100681548.marc

North Sea pilot (eastern shores) : from Dunkerque to the Skaw : 1922
LDR		00773namZa22002291ZZ4500
001		100681548
003		MiAaHDL
...
```

There are probably more things to test on this page, but for now:

* Should have values for `LDR`, `001`,  and `008`.
* Value for `001` should also be found in the URL.
