<!DOCTYPE html>
<html class="search-target-catalog" lang="{$userLang}" xml:lang="{$userLang}">
<!-- Machine is {$machine} -->
  <head>
    <title>{$pageTitle|truncate:64:"..."} | Hathi Trust Digital Library</title>
    <link rel="search" type="application/opensearchdescription+xml" title="Library Catalog Search" href="{$url}/Search/OpenSearch?method=describe">
    
{literal}
<!--[if lt IE 9]>
<script src="//ie7-js.googlecode.com/svn/version/2.1(beta4)/IE9.js"></script>
<script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<script src="/common/unicorn/vendors/js/selectivizr.js" type="text/javascript"></script>
<![endif]-->
{/literal}


  <script type="text/javascript" src="//code.jquery.com/jquery-1.9.1.min.js"></script>
  <script type="text/javascript" src="//code.jquery.com/jquery-migrate-1.1.0.min.js"></script>

  <!-- Project Unicorn -->
  <link rel="stylesheet" type="text/css" href="/common/unicorn/css/common.css">
  <script type="text/javascript" src="/common/unicorn/js/head.min.js"></script>
  <script type="text/javascript" src="/common/unicorn/js/common.js"></script>
  <link rel="stylesheet" type="text/css" media="screen" href="/static/unicorn/css/hathi.css" />
  
  {literal}
  <!--[if lt IE 8]>
  <link rel="stylesheet" type="text/css" href="/common/unicorn/css/ie7.css" />
  <![endif]-->
  {/literal}
  
    
{if $id}
  <link rel="canonical" href="/Record/{$id}">
  <link rel="alternate" type="application/marc" href="/Record/{$id}.mrc" >
  <link rel="alternate" type="application/marc+xml" href="/Record/{$id}.xml" >
  <link rel="alternate" type="application/x-Research-Info-Systems" href="/Record/{$id}.ris" >
  <link rel="alternate" type="application/x-endnote-refer" href="/Record/{$id}.endnote" >
  <link rel="alternate" href="/Record/{$id}.rdf" type="application/rdf+xml" >
{/if}



    <meta http-equiv="Content-Type" content="text/html;charset=utf-8">

    <script language="JavaScript" type="text/javascript">
      path = '{$url}';
      var jq = $;
    </script>

    <script type="text/javascript" src="/js/ht-layout.js"></script>


  </head>

  <body>
    <h1 class="offscreen">{$pageTitle} | Hathi Trust Digital Library</h1>
    {include file="htdl_header.tpl"}

    <div id="doc3" class="yui-t8">

      <h2 class="SkipLink">Main Content</h2>

      <div id="contentContainer" class="container page centered">

      {include file="$module/$pageTemplate"}

      </div>

    </div>

  <!-- FOOTER -->
  <div class="navbar navbar-static-bottom navbar-inverse footer" role="contentinfo">
    <div class="navbar-inner">
      <!-- IF LOGGED IN 
      <ul id="nav" class="nav">
        <li>
          <span>University of Michigan<br />Member, HathiTrust
          </span>
        </li>
      </ul>
      -->
      <ul class="nav pull-right">
        <li><a href="http://www.hathitrust.org/">Home</a></li>
        <li><a href="http://www.hathitrust.org/about">About</a></li>
        <li><a href="http://babel.hathitrust.org/cgi/mb">Collections</a></li>
        <li><a href="http://www.hathitrust.org/help">Help</a></li>
        <li><a href="#" data-m="ht" data-id="HathiTrust Catalog Feedback" data-toggle="feedback">Feedback</a></li>
        <li><a href="http://m.hathitrust.org">Mobile</a></li>
        <li><a href="http://www.hathitrust.org/take_down_policy">Take-Down Policy</a></li>
        <li><a href="http://www.hathitrust.org/privacy">Privacy</a></li>
        <li><a href="http://www.hathitrust.org/contact">Contact</a></li>
      </ul>
    </div>
  </div>

    <div id="popupbox">
    </div>
      {include file="popout_help.html"}

      <script type="text/javascript">
      {literal}
      var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
      document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
      </script>
      <script type="text/javascript">
      try {
      var pageTracker = _gat._getTracker("UA-954893-23");
      pageTracker._setDomainName(".hathitrust.org");
      pageTracker._trackPageview();
      } catch(err) {}
      {/literal}
      </script>
  </body>
</html>
