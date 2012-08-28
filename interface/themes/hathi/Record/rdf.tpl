<?xml version="1.0" encoding="utf-8"?>
<rdf:RDF
  xmlns:bibo="http://purl.org/ontology/bibo/"
  xmlns:dcterms="http://purl.org/dc/terms/"
  xmlns:foaf="http://xmlns.com/foaf/0.1/"
  xmlns:owl="http://www.w3.org/2002/07/owl#"
  xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
  xmlns:rdfs="http://www.w3.org/2000/01/rdf-schema#"
  xmlns:xhv="http://www.w3.org/1999/xhtml/vocab#"
  xmlns:xml="http://www.w3.org/XML/1998/namespace"
>

<rdf:Description rdf:about="{$url}/Record/{$record.id}#record">
  {if $record.title && $record.title[0]}
  <dcterms:title>{$record.title[0]}</dcterms:title>
  {if $record.title[1]}
   <dcterms:alternative>{$record.title[1]}</dcterms:alternative>
  {/if}
  {/if}

  {if $record.publishDate[0]}
  <dcterms:date>{$record.publishDate[0]}</dcterms:date>
  {/if}

  {if $record.author}
  {foreach from=$record.author item=author}
    <dcterms:creator>{$author}</dcterms:creator>
  {/foreach}
  {/if}


  {if $record.lccn}
    {foreach from=$record.lccn item=lccn}

  <owl:sameAs rdf:resource="http://purl.org/NET/lccn/{$lccn}#i">
  <bibo:lccn>{$lccn}</bibo:lccn>

    {/foreach}
  {/if}

  {if $record.isbn}
    {foreach from=$record.isbn item=isbn}

  <owl:sameAs rdf:resource="http://purl.org/NET/book/isbn/{$isbn}#book">
  <bibo:isbn>{$isbn}</bibo:isbn>
    {/foreach}
  {/if}
  {if $record.oclc}
    {foreach from=$record.oclc item=oclc}

  <bibo:oclc>{$oclc}</bibo:oclc>

    {/foreach}
  {/if}
  {if $record.issn}
    {foreach from=$record.issn item=issn}

  <bibo:issn>{$issn}</bibo:issn>
    {/foreach}
  {/if}



  </rdf:Description>
</rdf:RDF>
