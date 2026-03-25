#!/usr/bin/env php
<?php
/**
 * Standalone helper that builds a SearchStructure from a single user query
 * string, runs it through Solr::simplesearch, and prints the generated Solr
 * search arguments.
 *
 * This script can be run from the project root.
 *
 * Usage: php PrintSolrQuery.php "search string" [type]
 *   - type defaults to "title"
 */

require_once __DIR__ . '/../sys/Solr.php';
require_once __DIR__ . '/../sys/SolrConnection.php';
require_once __DIR__ . '/../services/Search/SearchStructure.php';

function invokecollapseCompoundPhrases($solr, $tokens): array
    {
        $reflection = new ReflectionClass($solr);
        $method = $reflection->getMethod('collapseCompoundPhrases');

        return $method->invoke($solr, $tokens);
    }

function invokebuildEscapedParts($solr, $tokens): array
    {
        $reflection = new ReflectionClass($solr);
        $method = $reflection->getMethod('buildEscapedParts');

        return $method->invoke($solr, $tokens);
    }

$query = $argv[1] ?? '';
$type = $argv[2] ?? 'title';

if ($query === '') {
    fwrite(STDERR, "Usage: php PrintSolrQuery.php \"search string\" [type]\n");
    exit(1);
}

global $configArray;
$configArray = parse_ini_file(__DIR__ . '/../conf/config.ini', true);
$configArray['Site']['local'] = '/app';

$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_ADDR'] = 'localhost';
$_REQUEST['lookfor'][] = $query;
$_REQUEST['type'][] = $type;
$_REQUEST['action'] = 'standard';
$_REQUEST['pagesize'] = 1;

$ss = new SearchStructure();

$solr = new Solr('', '');

if ($ss->use_dismax) {
    $args = $solr->dismaxSearchArguments($ss);
} else {
    $args = $solr->searchArguments($ss);
}


$tokenized_query = $solr->tokenizeInput($query);
print_r("----- Tokenized Search ----- : " . json_encode($tokenized_query, JSON_UNESCAPED_UNICODE));
print("\n");

$classified_tokens = $solr->classifyTokens($tokenized_query);
print_r("----- Classified Tokens ----- : " . json_encode($classified_tokens, JSON_UNESCAPED_UNICODE));
print("\n");

$tokens = invokecollapseCompoundPhrases($solr, $classified_tokens);
print_r("----- Tokens after collapsing compound phrases ----- : " . json_encode($tokens, JSON_UNESCAPED_UNICODE));
print("\n");

$escapedParts = invokebuildEscapedParts($solr, $tokens);
print_r("----- Escaped Parts ----- : " . json_encode($escapedParts, JSON_UNESCAPED_UNICODE));
print("\n");

$semanticStructure = $solr->build_and_or_onephrase($query);
print_r("----- Semantic Structure ----- : " . json_encode($semanticStructure, JSON_UNESCAPED_UNICODE));
print("\n");
print_r("-----  Solr Search ----- : " . json_encode($args[0][1], JSON_UNESCAPED_UNICODE));
print("\n");