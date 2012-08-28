<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="{$userLang}">
  <head>
    <title>Mirlyn: {$pageTitle|truncate:64:"..."}</title>

    <script type="text/javascript">
    //<![CDATA[
    var is_ie6 = ('ActiveXObject' in window && !('XMLHttpRequest' in window));
    //]]>
    </script>

{if $searchcomps && $atom}
<link rel="alternate" type="application/rss+xml" title="Mirlyn search for {$lookfor}" href="/Search/Home?{$searchcomps|escape:'html'}&amp;view=atom">
{/if}

{if $id}
  <link rel="canonical" href="/Record/{$id}">
  <link rel="alternate" type="application/marc" href="/Record/{$id}.mrc" >
  <link rel="alternate" type="application/x-Research-Info-Systems" href="/Record/{$id}.ris" >
  <link rel="alternate" type="application/x-endnote-refer" href="/Record/{$id}.endnote" >
  <link rel="alternate" type="application/marc+xml" href="/Record/{$id}.xml" >
  <link rel="alternate" href="/Record/{$id}.rdf" type="application/rdf+xml" />
{/if}

<link rel="unapi-server" type="application/xml" title="unAPI" href="/unapi">



    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">


    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.1/jquery.min.js"></script>


  <!-- Fancybox -->

  <script src="/static/jquery.fancybox/jquery.fancybox-1.2.1.js" type="text/javascript" charset="utf-8"></script>
  <link rel="stylesheet" href="/static/jquery.fancybox/jquery.fancybox.css" type="text/css" media="screen" title="no title" charset="utf-8">

  <script type="text/javascript" charset="utf-8">
   var jq = jQuery.noConflict();

   var tempAddLink = "{translate text="tempAddLink"}";
   var tempRemoveLink = "{translate text="tempRemoveLink"}";
   var tempset = "{translate text="tempset"}";
   var jssearchcomps = "{$searchcomps}";
   var username = "{$username}";
  </script>

  <script src="/js/favorites.js" type="text/javascript" charset="utf-8"></script>


    <script language="JavaScript" type="text/javascript">
    path = '{$url}';

    {literal}


String.prototype.trim = function() {
	return this.replace(/^\s+|\s+$/g,"");
}

function trimForm(infrm) {
  infrm.value = infrm.value.trim()
 }

function fixform(f) {
  // jq('#lookfor').value = jq('#lookfor').value.trim();

  if (jq('#searchtype').val() == 'journaltitle') {
    // jq('#searchtype').val('title');
    jq(f).append('<input type="hidden" name="filter[]" value=\'format:Serial\'>');
  }
}

function loginLink() {
  {/literal}
  login = '{$loginURLBase}';
  {literal}
  loc = window.location.href;
  return login + escape(loc);
}



jq.fn.clearForm = function(exceptions) {
  return this.each(function() {
    if (jq(this).hasClass('stickyform')) {
      return;
    }
    var type = this.type, tag = this.tagName.toLowerCase();
    if (tag == 'form')
      return jq(':input',this).clearForm();
    if (type == 'text' || type == 'password' || tag == 'textarea')
      this.value = '';
    else if (type == 'checkbox' || type == 'radio')
      this.checked = false;
    else if (tag == 'select')
      this.selectedIndex = 0;
  });
};

function pausecomp(millis)
{
  var date = new Date();
  var curDate = null;

  do { curDate = new Date(); }
  while(curDate-date < millis);
}

// function submitFeedback()
// {
//   document.getElementById('popupbox').innerHTML +=  document.getElementById('feedback_thanks').innerHTML;
//   setTimeout("hideLightbox()", 1500);
//   return true;
// }

function hideMenu(elemId)
{
    document.getElementById(elemId).style.display='none';
}


/* Set up fancybox for dolightbox class */

jq(function(){
  jq('.dolightbox').fancybox({hideOnContentClick: false, overlayShow: true, frameHeight: 410});

});



  </script>



{/literal}


<script type="text/javascript" charset="utf-8">
  var isSelectedItemsPage = {if $selectedItemsPage}true{else}false{/if};
  var isFavoritesPage     = {if $favoritesPage}true{else}false{/if};
</script>

<!-- Our CSS -->
    <!-- <link rel="search" type="application/opensearchdescription+xml" title="Library Catalog Search" href="{$url}/Search/OpenSearch?method=describe"> -->
    <link rel="stylesheet" type="text/css" media="screen" href="{$path}/interface/themes/{$configArray.Site.theme}/css/styles.css">
    <link rel="stylesheet" type="text/css" media="screen" href="{$path}/interface/themes/{$configArray.Site.theme}/css/wrapper.css">
    <link rel="stylesheet" type="text/css" media="print" href="{$path}/interface/themes/{$configArray.Site.theme}/css/print.css">


  </head>

  <body>
{include file="popout_help.html"}

{include file="mlibrary_header.html"}



<div id="doc3" class="yui-t8"> <!-- Change id for page width, class for menu layout. -->

  <!-- Searchbox -->
  <div id="bd">
    <div class="yui-main" style="*margin-left: 0em;">
        {include file="searchbox.tpl"}
    </div>
  </div>

  <!-- Content -->
  <div style="margin-top: 1em; width: 100%">
      {include file="$module/$pageTemplate"}
  </div>

  <!-- VUFind Footer -->
  <a name="footer"></a>
  <div id="ft">
      <div><p><strong>Search Options</strong></p>
        <ul>
          <!-- <li><a href="{$path}/Search/History">Search History</a></li> -->
          <li><a href="#searchhistory_help" class="dolightbox">Search History</a></li>
          <li><a href="{$path}/Search/Advanced">Advanced Search</a></li>
        </ul>
      </div>

      <div>
      <p><strong>Find More</strong></p>
      <ul>
          <!-- <li><a href="{$path}/Browse/Home">Browse the Catalog</a></li> -->
          <!-- <li><a href="#" onclick="fillLightbox('browse_help');return false;">Browse the Catalog</a></li> -->
          <!-- <li><a href="{$path}/Search/Reserves">Course Reserves</a></li> -->
          {if $inst eq 'flint'}
          <li><a href="http://libproxy.umflint.edu:2048/login?url=http://mirlyn-classic.lib.umich.edu/F?func=find-b-0&amp;local_base=miu30_pub">Course Reserves</a></li>
          {else}
          <li><a href="http://mirlyn-classic.lib.umich.edu/F/?local_base=miu30_pub">Course Reserves</a></li>
          {/if}
          <li><a href="http://www.lib.umich.edu/newbooks/">New Items</a></li>
          <li><a href="http://www.lib.umich.edu/library-catalogs">Find other library catalogs</a></li>
          {if $inst eq 'flint'}
          <li><a href="http://www.umflint.edu/library/circ/delivery.htm" target="ill">ILL Request</a></li>
          {else}
          <li><a href="http://www.lib.umich.edu/interlibrary-loan" target="ill">ILL Request</a></li>
          {/if}
        </ul>
    </div>

     <div><p><strong>Need Help?</strong></p>
        <ul>
	    <li><a href="#searchtips_text" class="dolightbox">Search Tips</a></li>
            <li><a class="clickpostlog" ref="footerfaq" href="/Search/FAQ">Frequently Asked Questions</a></li>
            <!-- <li><a href="{$path}/Help/Home?topic=search">Search Tips</a></li> -->
            {if $inst eq 'flint'}
            <li> <a class="clickpostlog" ref="footeraskflint" href="http://www.umflint.edu/library/askus.htm" target="ask">Ask a Librarian</a></li>
            {else}
            <li> <a class="clickpostlog" ref="footerask" href="http://www.lib.umich.edu/ask/" target="ask">Ask a Librarian</a></li>
            {/if}
          </ul>
      </div>

      <br clear="all">
</div>

<!-- MLibrary Footer     -->
<div id="footer-container">
  <p style = "color: #fff; padding-top: .5em; padding-left: 25%;">&copy;2009, Regents of the University of Michigan</p>
</div>

<script type="text/javascript">
{literal}
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try {
var pageTracker = _gat._getTracker("UA-1341620-9");
pageTracker._trackPageview();
} catch(err) {}
{/literal}
</script>

</div>
</body>
</html>
