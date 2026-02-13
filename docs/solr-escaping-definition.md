# Solr Escaping Definition

## Purpose

This document defines where, when, and how escaping must occur when building Solr/Lucene queries. 
Follow this document to avoid producing invalid queries, broken boolean logic, or double-escaping bugs.

## Core Rule (Read This First)

Escaping is context-dependent and MUST happen at the final embedding step only.

❌ Never escape raw user input
❌ Never escape tokens
❌ Never escape before semantics are known
❌ Escaping early destroys intent.

✅ Escape only when inserting a value into field:( … )

## Query Construction Pipeline

The query builder has four distinct layers. Each layer has a strict responsibility.

1. Raw input
   ↓
2. Validation
   ↓
3. Tokenization 
   ↓
4. Semantic Classification
   ↓
5. Escaping (character level only) 
   ↓
6. Rendering (syntax construction)

### Layer Responsibilities

1. Raw Input

Example: `nature, and history`

    - Allowed:
        - Trimming
        - Unicode normalization
        - Validation (balanced quotes, no junk)
    - Forbidden:
        - Escaping
        - Quoting
        - Backslashes

2. Tokenization & Normalization

**Input**: `nature, and history`
**Tokens**: `["nature,", "history"]`

    - Allowed:
        - Splitting into tokens
        - Lowercasing (optional)
        - Removing stop words
    - Forbidden:
        - Escaping tokens
        - Detecting boolean meaning
        - Adding quotes

Lowercase boolean words are stopwords; uppercase boolean operators are syntax. 

* Tokenizer removes lowercase boolean words; e.g `Poetry and nature` -> `["Poetry", "nature"]`
* Uppercase boolean operators are syntax so thet are preserved. `Poetry AND nature` -> `["Poetry AND nature"]`
* Tokenizer vener touch quoted phrases. `"nature and history"` -> `["nature and history"]`

3. Semantic Classification (NO ESCAPING)

**Input**: "table chair"~2 "wood table"~1
**Outputs**: Tokens : [{"type":"phrase","value":{"text":"table chair","slop":"2"}},{"type":"phrase","value":{"text":"wood table","slop":"1"}}]

* Determine is the input string is a Phrase or a Term
* Classifies syntax
* Runs before escaping
* Does not modify input

**Token    isPhrase    Purpose	**
"machine learning"   Yes  Quoted phrase
"machine learning"~3 Yes Proximity search/ Phrase with slop -> Search by machine and learning with a distant of 3
table~2 Yes Fuzzy term
"foo"~3    Yes  Proximity search
"table"*    Yes    
"table*"    Yes
machine learning~3 No
table*  No
"broken No


The semantic structure defines intent, not syntax.

``` $values = [
  'onephrase'    => '"nature, and history"',
  'and'          => 'nature AND and AND history',
  'or'           => 'nature OR and OR history',
  'asis'         => 'nature, and history', // raw input as tge yser typed it.
  'compressed'   => 'nature,andhistory',
  'exactmatcher' => 'natureandhistory',
  'emstartswith' => 'natureandhistory*'
]; 
```

    - Allowed:
        - Adding AND / OR
        - Adding quotes for phrases
        - Adding * for prefixes
    - Forbidden:
        - Escaping special characters
        - Adding backslashes
        - Lucene syntax manipulation

4. Context-Aware Escaping (MANDATORY)

Escaping happens only here, inside `__buildQueryString()` in `Solr.php`.

Each semantic value MUST use the correct escaper.

**Semantic Type    Escaper	Purpose**
onephrase   escapePhrase()  Quoted phrase
and, or escapeBoolean() Boolean expression
emstartswith    escapePrefix()  Prefix query
everything else escapeTerm()    Literal term

## Escaper Contracts

`escapeTerm(string $input)`

- Escapes Lucene special characters 
- DOES NOT add quotes 
- DOES NOT interpret boolean logic

✅ Correct for: exact terms, IDs, compressed strings
❌ Wrong for: phrases see `escapePhrase()`

`escapePhrase(string $input)`

- Removes outer quotes if present 
- Escapes inner content
- Re-adds exactly one pair of quotes

**Input**: `"nature, and history"`
**Output**: `"nature\, and history"`

❌ Must never double-quote
❌ Must never accept pre-escaped strings

`escapeBoolean(string $expr)`

- Preserves AND/OR operators
- Escapes only operands 
- MUST NOT quote operands automatically

**Input**: `nature AND and AND history`
**Output**: `nature AND and AND history`

(operators preserved, literals escaped as needed)

❌ Must not introduce "and"
❌ Must not escape operators

`escapePrefix(string $prefix)`

- Removes trailing *
- Escapes base term 
- Re-adds *

**Input**: `natureandhistory*`
**Output**: `natureandhistory*`

❌ Must never allow leading wildcards

## Explicit Anti-Patterns (DO NOT DO THESE)

❌ Escaping inside tokenization
❌ Escaping $lookfor directly
❌ Escaping tokens before implode()
❌ Quoting inside escapeBoolean()
❌ Passing pre-escaped strings into escapers
❌ Escaping twice “just to be safe”

## Maintenance Checklist (Before Any Change)

Before modifying escaping logic, confirm:

- Am I escaping only at embedding time? 
- Does this escaper handle exactly one semantic role? 
- Could this cause double-quoting? 
- Could this break boolean operators? 
- Do existing tests still pass? 
- If unsure → STOP and re-read this document.

## Summary
Semantics first. Escaping last. Always.


## What “phrase” means in the pipeline

A token should be considered a phrase if:

- It is quoted → `"foo bar"`
- It may optionally have a Lucene proximity / slop suffix → `"foo bar"~5`
- We should not classify:
   - machine~3 ❌ 
   - learning~3 ❌ 
   - machine learning~3 ❌ (not quoted, so not a phrase)
- We must preserve safety:
   - No partial quotes 
   - No malformed slop 
   - No boolean inference