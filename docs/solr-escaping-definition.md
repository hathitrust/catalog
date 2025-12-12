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
2. Normalization & tokenization 
   ↓
3. Semantic expansion
   ↓
4. Context-aware escaping 

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
**Tokens**: `["nature,", "and", "history"]`

    - Allowed:
        - Splitting into tokens
        - Lowercasing (optional)
    - Forbidden:
        - Escaping tokens
        - Detecting boolean meaning
        - Adding quotes

3. Semantic Expansion (NO ESCAPING)

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