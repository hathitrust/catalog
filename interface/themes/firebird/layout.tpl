<!DOCTYPE html>
<html lang="{$userLang}" xml:lang="{$userLang}" data-analytics-code="UA-954893-23" data-analytics-enabled="true" data-use="search" data-app="catalog">
<!-- Machine is {$machine} -->
<head>
  <title>{$pageTitle|truncate:64:"..."} | HathiTrust Digital Library</title>
  <link rel="search" type="application/opensearchdescription+xml" title="Library Catalog Search" href="{$url}/Search/OpenSearch?method=describe">

  <script src="https://kit.fontawesome.com/1c6c3b2b35.js" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="https://hathitrust-firebird-common.netlify.app/assets/main.css" />
  <script type="module" src="https://hathitrust-firebird-common.netlify.app/assets/main.js"></script>

  {if $id}
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
<body>

  <svg xmlns="http://www.w3.org/2000/svg" style="display: none">
    <symbol id="checkbox-empty" viewBox="0 0 18 18">
      <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><g transform="translate(-61.000000, -957.000000)"><g transform="translate(60.000000, 918.000000)"><g transform="translate(0.000000, 38.000000)"><path d="M16.9994,0.99807 L2.99939,0.99807 C1.89439,0.99807 0.99939,1.89307 0.99939,2.99807 L0.99939,16.9981 C0.99939,18.1031 1.89439,18.9981 2.99939,18.9981 L16.9994,18.9981 C18.1034,18.9981 18.9994,18.1031 18.9994,16.9981 L18.9994,2.99807 C18.9994,1.89307 18.1034,0.99807 16.9994,0.99807 L16.9994,0.99807 Z M16.9994,2.99807 L16.9994,16.9981 L2.99939,16.9991 L2.99939,2.99807 L16.9994,2.99807 L16.9994,2.99807 Z"></path></g></g></g></g>
    </symbol>
    <symbol id="checkbox-checked" viewBox="0 0 18 18" version="1.1"><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><g transform="translate(-240.000000, -957.000000)"><g transform="translate(60.000000, 918.000000)"><g transform="translate(179.000000, 38.000000)"><path d="M7.9994,14.9981 L2.9994,9.9981 L4.4134,8.5841 L7.9994,12.1701 L15.5854,4.58407 L16.9994,5.99807 L7.9994,14.9981 Z M16.9994,0.99807 L2.9994,0.99807 C1.8934,0.99807 0.9994,1.89307 0.9994,2.99807 L0.9994,16.9981 C0.9994,18.1031 1.8934,18.9981 2.9994,18.9981 L16.9994,18.9981 C18.1044,18.9981 18.9994,18.1031 18.9994,16.9981 L18.9994,2.99807 C18.9994,1.89307 18.1044,0.99807 16.9994,0.99807 L16.9994,0.99807 Z"></path></g></g></g></g></symbol>
    <symbol id="panel-expanded" viewBox="0 0 14 2">
      <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
        <g transform="translate(-823.000000, -212.000000)">
          <g transform="translate(822.000000, 60.000000)">
            <g transform="translate(0.000000, 151.000000)">
              <polygon points="14.9994 2.998 0.99943 2.998 0.99995 1.0001 14.9994 0.998"></polygon>
            </g>
          </g>
        </g>
      </g>
    </symbol>
    <symbol id="panel-collapsed" viewBox="0 0 12 8"><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><g transform="translate(-353.000000, -585.000000)"><g transform="translate(60.000000, 477.000000)"><g transform="translate(292.000000, 108.000000)"><polygon points="2.41348 0.58407 6.9995 5.1701 11.5855 0.58407 12.9995 1.99807 6.9995 7.9981 0.99948 1.99807"></polygon></g></g></g></g></symbol>
    <symbol id="action-remove" viewBox="0 0 14 14" class="active-filter-symbol">
      <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
        <g transform="translate(-274.000000, -993.000000)">
          <g transform="translate(60.000000, 918.000000)">
            <g transform="translate(214.000000, 75.000000)">
              <polygon points="14 1.41 12.59 0 7 5.59 1.41 0 0 1.41 5.59 7 0 12.59 1.41 14 7 8.41 12.59 14 14 12.59 8.41 7"></polygon>
            </g>
          </g>
        </g>
      </g>
    </symbol>
    <symbol                               
       className="svg"                             
       fill="currentColor"                
       preserveAspectRatio="xMidYMid meet"                 
       height="16" 
       width="16"                               
       viewBox="0 0 16 16"   
       id="radio-empty"                          
    >                               
       <circle                                 
          className="radioOutline"                              
          cx="8"
          cy="8"                                 
          r="6.5"                                 
          fill="none"                                 
          stroke="black"                                 
          stroke-width="2.5"                               
       />                               
    </symbol>
    <symbol                               
       className="svg"                             
       fill="currentColor"                
       preserveAspectRatio="xMidYMid meet"                 
       height="16" 
       width="16"                               
       viewBox="0 0 16 16"   
       id="radio-checked"                          
    >                               
       <circle                                 
          className="radioOutline"                              
          cx="8"
          cy="8"                                 
          r="6.5"                                 
          fill="none"                                 
          stroke="black"                                 
          stroke-width="2.5"
       />                               
       <circle 
          className="radioDot" 
          cx="8" 
          cy="8" 
          r="3.5" 
          fill="black" 
       />                             
    </symbol>
  </svg>

  {include file="$module/$pageTemplate"}

</body>
</html>
