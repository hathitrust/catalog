<!DOCTYPE html>
<html lang="{$userLang}" xml:lang="{$userLang}" data-analytics-code="UA-954893-23" data-analytics-enabled="true" data-use="search" data-app="catalog">
<!-- Machine is {$machine} -->
<head>
  <title>{$pageTitle|truncate:64:"..."} | HathiTrust Digital Library</title>
  <link rel="search" type="application/opensearchdescription+xml" title="Library Catalog Search" href="{$url}/Search/OpenSearch?method=describe">

<script>
let $$assets = {firebird_manifest};
</script>
{literal}
<script type="text/javascript">
  let head = document.head;
  function addScript(options) {
    let scriptEl = document.createElement('script');
    if ( options.crossOrigin ) { scriptEl.crossOrigin = options.crossOrigin; }
    if ( options.type ) { scriptEl.type = options.type; }
    scriptEl.src = options.href;
    document.head.appendChild(scriptEl);
  }
  function addStylesheet(options) {
    let linkEl = document.createElement('link');
    linkEl.rel = 'stylesheet';
    linkEl.href = options.href;
    document.head.appendChild(linkEl);
  }

  addStylesheet({ href: $$assets.stylesheet});
  addScript({ href: $$assets.script, type: 'module' });
 
</script>
<script>
  // in case any of the links and scripts fail
  setTimeout(function() {
    document.body.style.visibility = 'visible';
    document.body.style.opacity = '1';
  }, 1500);
</script>
{/literal}


  {if isset($id) and $id}
  <link rel="canonical" href="/Record/{$id|escape:"url"}">
  <link rel="alternate" type="application/marc" href="/Record/{$id|escape:"url"}.mrc" >
  <link rel="alternate" type="application/marc+xml" href="/Record/{$id|escape:"url"}.xml" >
  <link rel="alternate" type="application/x-Research-Info-Systems" href="/Record/{$id|escape:"url"}.ris" >
  <link rel="alternate" type="application/x-endnote-refer" href="/Record/{$id|escape:"url"}.endnote" >
  <link rel="alternate" href="/Record/{$id|escape:"url"}.rdf" type="application/rdf+xml" >
  {/if}

  <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
  <meta name="title" content="{$pageTitle|escape}" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />

</head>
<body class="apps" style="opacity: 0; visibility:hidden;">

  {include file="$module/$pageTemplate"}

</body>
</html>
