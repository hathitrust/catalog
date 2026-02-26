# Solr Search Module Documentation

## Scope
This document describes the current Solr query-building and escaping behavior implemented in `sys/Solr.php`, cross-checked with tests in `test/SolrQueryTest`.

It is implementation-first documentation: it reflects what the code does now, including edge-case behavior and current limitations.

## Main Entry Points
- Standard/dismax query assembly: `Solr::standardSearchComponents()` and `Solr::dismaxSearchArguments()`
- Semantic value builder: `Solr::build_and_or_onephrase()`
- Final field query composition: `Solr::__buildQueryString()`

## End-to-End Pipeline

### 1. Input normalization and recovery (`build_and_or_onephrase`)
`build_and_or_onephrase($lookfor)` performs:
1. Fancy quote normalization: `“`/`”` -> `"`
2. Quoted wildcard unwrap: `"foo bar"*` -> `foo bar*` via `unwrapQuotedWildcard()
`
   - That is because Lucene does not allow wildcards on quoted phrases
3. Validation + recovery loop (max 3 passes):
   - validate via `validateInput()`
   - if invalid, apply targeted transform based on error type
   - Example valid input: `title:"data science"` --> `['valid' => true]`
   - Example invalid input: `title:` --> `['valid' => false, 'error' => 'Malformed field:value syntax']`

Hard-fail errors return `false`:
- `Empty query`
- `Invalid garbage-only query`
- `Invalid single-character query`
- For these cases, the function `build_and_or_onephrase` returns `false`, as `$query` is by default empty, so `$searchComponents[] = array('q', '*:*');`. 

Recoverable errors are transformed, then revalidated:
- `Leading wildcard not allowed` -> drop first char
- `Unbalanced parentheses` -> remove all parentheses
- `Unbalanced quotes` -> remove wrapping quotes
- invalid boost forms -> remove `^`
- many fuzzy/field/colon syntax failures -> `sanitizeToTerms()` (letters/numbers only)

This behavior is covered by `BuildAndOrOnePhraseTest` and `ValidateQueryInputTest`.

### 2. Tokenization (`tokenizeInput`)
Tokenizer regex:
- captures quoted phrases with optional trailing fuzzy/slop suffix (`"..."~N`)
- captures quoted phrases (`"..."`)
- captures unquoted tokens split by spaces

Current behavior:
- Lowercase boolean words (`and`, `or`, `not`) are removed outside quotes
- Uppercase boolean operators (`AND`, `OR`, `NOT`) are preserved as standalone tokens
- Quoted phrases are never split

Validated by `TokenizeInputTest`, including:
- `table AND "chair leg"~2` -> `['table', 'AND', '"chair leg"~2']`
- `poetry and nature` -> `['poetry', 'nature']`
- `"poetry AND nature"` stays one token

### 3. Classification (`classifyTokens`)
Token categories currently emitted:
- `operator`:
  - matches `AND`, `OR`, `NOT` 
  - stored as uppercase in output (`['type' => 'operator', 'value' => 'AND']`)
- `phrase_slop`:
  - quoted phrase with slop suffix (`"..."~N`)
  - output value shape: `['text' => <phrase>, 'slop' => <digits-as-string>]`
- `phrase`:
  - quoted phrase (`"..."`) and quoted wildcard form (`"..."*`) by `isPhrase()`
  - output value shape: `['text' => <phrase>, 'slop' => null]`
- `term_wildcard`:
  - non-phrase token containing `*` or `?`
  - output value shape: raw token string
- `term_fuzzy`:
  - non-phrase token ending in `~<digits>` (for example `table~2`)
  - output value shape: raw token string
- `term`:
  - fallback category for all remaining tokens
  - output value shape: raw token string

Validated by `ClassifySyntaxQueryTest`.

### 4. Escaped query part construction (`buildEscapedParts`)
Escaping is type-specific:
- `phrase` / `phrase_slop`:
  - `buildPhraseToken()` -> `escapePhrase(text)` then wraps in quotes
  - appends `~N` if slop exists
- `term`:
  - `escapeTerm()`
- `term_wildcard`:
  - `escapeTermKeepWildcardOperators()` (keeps `*` and `?` unescaped)
- `term_fuzzy`:
  - splits into base + `~N`; escapes base with `escapeTerm()`; reattaches raw `~N`
- `operator`:
  - preserved as uppercase (`AND`/`OR`/`NOT`)

### 5. Raw flattening for semantic side fields (`flattenTokens`)
`flattenTokens()` produces raw semantic text (not Lucene-escaped), preserving:
- phrase quotes
- phrase slop suffix
- fuzzy suffix
- wildcard characters
- operators

Examples:
- `"table chair"~2` stays `"table chair"~2`
- `table*` stays `table*`

## Semantic Structure Output & Field definitions
`build_and_or_onephrase()` returns:
- `onephrase`
  - space-joined escaped parts
- `and`
  - if explicit operator tokens exist: same as `onephrase`
  - else: escaped parts joined by ` AND `
- `or`
  - if explicit operator tokens exist: same as `onephrase`
  - else: escaped parts joined by ` OR `
- `asis`
  - raw flattened token string
- `compressed`
  - `asis` with whitespace removed
- `exactmatcher`
  - `exactmatcherify(flattened)` -> lowercase, trim, remove all but letters/numbers/`*`/`?`
- `emstartswith`
  - `str_replace('*', '', exactmatcher) . '*'`
  - effectively ensures exactly one trailing `*`

### Tested examples
From `BuildAndOrOnePhraseTest`:
- `table` -> `onephrase=table`, `asis=table`
- `table~2` -> `onephrase=table~2`, `asis=table~2`
- `"table chair"~2` -> `onephrase="table chair"~2`, `asis="table chair"~2`
- `table*` -> `onephrase=table*`, `asis=table*`, `emstartswith=table*`
- `~` and `\\` -> returns `false`

## Escaping Functions and Where They Are Used

### `escapeTerm(string)`
Escapes Lucene specials for literal terms, including `*`, `?`, `~`, `:`.
Used for `term` and fuzzy base.

### `escapeTermKeepWildcardOperators(string)`
Escapes Lucene specials except wildcard operators `*` and `?`.
Used for `term_wildcard`.

### `escapePhrase(string)`
- removes outer quotes if present
- escapes only `"` and `\\` in phrase content
- does not add quotes itself (caller wraps)
**Input**: `"nature, and history"`
**Output**: `"nature\, and history"`

❌ Must never double-quote
❌ Must never accept pre-escaped strings

### `lucene_escape_fq(string)`
Strict escaper for filter/query fragment contexts (`fq`, quoted field values, MLT clauses):
- Unicode normalization
- control char removal
- backslash escaped first
- escapes full Lucene special set

Used in:
- `quoteFilterValue()` for filter values
- `getMoreLikeThis()` query fragments

## How semantic values are used in final query clauses
`__buildQueryString()` reads search specs (`conf/searchspecs.yaml`) and maps field boosts to semantic keys (`onephrase`, `and`, `or`, `exactmatcher`, `emstartswith`, etc.).

For each configured field mapping:
- builds `field:(<semantic-value>)`
- applies configured boost when present

`standardSearchComponents()` and `dismaxSearchArguments()` call this builder and assemble final `q`.

## Behavior validated by full pipeline tests
`SolrQueryFullPipelineTest` verifies that the generated query contains expected boosted clauses such as:
- exactmatcher: `title_ab:(smart)^25000`, `title_a:(smart)^15000`
- startswith: `titleProper:(smart*)^8000`
- onephrase-style fields: `titleProper:(smart)^1200`, etc.

## Current known limitations / implementation notes
- `tokenizeInput()` keeps leading boolean operators as tokens; this can still produce invalid Lucene if not normalized upstream.
- `validateInput()` currently accepts some fuzzy forms with decimals (e.g., `table~0.5`) while `term_fuzzy` classification/building is integer-suffix-oriented (`~\d+`), so `table~0.5` is classify as term and `~` is escaped.
- Classification and validation rules are partially overlapping and not yet unified under a single parser.
- `emstartswith` intentionally rewrites wildcard content to one trailing `*` using `str_replace('*', '', exactmatcher) . '*'`.

## Refactoring Guidance
For future refactoring, preserve these invariants unless intentionally changed with tests:
- token categories and precedence
- operator token preservation (`AND|OR|NOT`)
- wildcard/fuzzy semantics (`term_wildcard`, `term_fuzzy`, `phrase_slop`)
- raw `asis` generation from flattened tokens
- field-level semantic outputs required by search specs:
  `onephrase`, `and`, `or`, `asis`, `compressed`, `exactmatcher`, `emstartswith`
