<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="{$userLang}">
<!-- Machine is {$machine} -->
  <head>
    <title>{$pageTitle|truncate:64:"..."} | Hathi Trust Digital Library</title>
    <link rel="search" type="application/opensearchdescription+xml" title="Library Catalog Search" href="{$url}/Search/OpenSearch?method=describe">
    <link rel="stylesheet" type="text/css" media="screen" href="{$path}/interface/themes/hathi/css/htdl_vf.css">
    <link rel="stylesheet" type="text/css" media="screen" href="{$path}/interface/themes/hathi/css/styles.css">
    <link rel="stylesheet" type="text/css" media="print" href="{$path}/interface/themes/hathi/css/print.css">

{if $id}
  <link rel="canonical" href="/Record/{$id}">
  <link rel="alternate" type="application/marc" href="/Record/{$id}.mrc" >
  <link rel="alternate" type="application/marc+xml" href="/Record/{$id}.xml" >
  <link rel="alternate" type="application/x-Research-Info-Systems" href="/Record/{$id}.ris" >
  <link rel="alternate" type="application/x-endnote-refer" href="/Record/{$id}.endnote" >
  <link rel="alternate" href="/Record/{$id}.rdf" type="application/rdf+xml" >
{/if}

<!-- Jeremy's additions 3/27/09 -->

    <link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/2.7.0/build/container/assets/container-core.css">
    <link rel="stylesheet" type="text/css" media="screen" href="{$path}/interface/themes/hathi/css/yui.css">

    <script type="text/javascript" src="http://yui.yahooapis.com/2.7.0/build/yuiloader/yuiloader-min.js"></script>
    <script type="text/javascript" src="http://yui.yahooapis.com/2.7.0/build/dom/dom-min.js"></script>
    <script type="text/javascript" src="http://yui.yahooapis.com/2.7.0/build/event/event-min.js"></script>
    <script type="text/javascript" src="http://yui.yahooapis.com/2.7.0/build/connection/connection-min.js"></script>
    <script type="text/javascript" src="http://yui.yahooapis.com/2.7.0/build/dragdrop/dragdrop-min.js"></script>
    <script type="text/javascript" src="http://yui.yahooapis.com/2.7.0/build/container/container-min.js"></script>
    <script type="text/javascript" src="http://yui.yahooapis.com/2.7.0/build/element/element-min.js"></script>
    <script type="text/javascript" src="http://yui.yahooapis.com/2.7.0/build/button/button-min.js"></script>
    <script language="JavaScript" type="text/javascript" src="{$path}/js/yui/feedback.js"></script>
    <script language="JavaScript" type="text/javascript" src="{$path}/js/ajax.yui.js"></script>
    <script language="JavaScript" type="text/javascript" src="{$path}/js/scripts.js"></script>

<!-- End Jeremy's additions -->

    <meta http-equiv="Content-Type" content="text/html;charset=utf-8">


    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.1/jquery.min.js"
            type="text/javascript"></script>



    <script language="JavaScript" type="text/javascript">
      path = '{$url}';
      var jq = jQuery.noConflict();
    </script>

    <script type="text/javascript" src="/js/ht-layout.js"></script>


  </head>

  <body class="yui-skin-sam">

    {include file="htdl_header.tpl"}

    <div id="doc3" class="yui-t8">

      <h2 class="SkipLink">Main Content</h2>
<!-- Searchbox -->
      {if !$isTheHomePage}
      <div class="bd">
        <div class="yui-main">
            {include file="searchbox.tpl"}
        </div>
      </div>
      {/if}

<!-- Content -->
      <div id="contentContainer">
          {include file="$module/$pageTemplate"}
      </div>

    </div>

<!-- HTDL Footer     -->
    <div id="mbFooter">
      <div id="FooterCont">
        <div class="MBooksNav">
          <h2 class="SkipLink">Footer Links</h2>
          <ul>
              <!-- <li><a href="https://babel.hathitrust.org/cgi/mb?a=listcs;colltype=pub;sort=cn_a">Login</a></li> -->
              <li><a href="http://www.hathitrust.org" title="About Hathi Trust">About</a></li>
              <li><a href="http://www.hathitrust.org/faq" title="Help page and faq">Help</a></li>
              <li><a href=" " id="feedback_footer" title="Feedback form for problems or comments"><span>Feedback</span></a></li>
              <!-- <li><a href="http://mirlyn.lib.umich.edu/" title="New Search in Mirlyn">Mirlyn Library Catalog</a></li>-->
              <li> | <a href="http://www.hathitrust.org/take_down_policy" title="item removal policy">Take-Down Policy</a></li>
          </ul>
        </div>
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
