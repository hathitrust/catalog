<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="{$userLang}">
  <head>
    <title>{$pageTitle|truncate:64:"..."}</title>
    <link rel="search" type="application/opensearchdescription+xml" title="Library Catalog Search" href="/Search/OpenSearch?method=describe">
    <link rel="stylesheet" type="text/css" media="screen" href="/interface/themes/umichwebsite/css/styles.css">
    <link rel="stylesheet" type="text/css" media="print" href="/interface/themes/umichwebsite/css/print.css">
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8">
    <script language="JavaScript" type="text/javascript">
      path = '{$url}';

{literal}

String.prototype.trim = function() {
	return this.replace(/^\s+|\s+$/g,"");
}

function trimForm(infrm) {
  infrm.value = infrm.value.trim()
  return true;
 }

{/literal}

    </script>


    <script language="JavaScript" type="text/javascript" src="/js/yui/yahoo-dom-event.js"></script>
    <script language="JavaScript" type="text/javascript" src="/js/yui/yahoo-min.js"></script>
    <script language="JavaScript" type="text/javascript" src="/js/yui/event-min.js"></script>
    <script language="JavaScript" type="text/javascript" src="/js/yui/connection-min.js"></script>
    <script language="JavaScript" type="text/javascript" src="/js/yui/dragdrop-min.js"></script>
    <script language="JavaScript" type="text/javascript" src="/js/scripts.js"></script>
    <script language="JavaScript" type="text/javascript" src="/js/rc4.js"></script>
    <script language="JavaScript" type="text/javascript" src="/js/ajax.yui.js"></script>
  </head>

  <body>
    <div id="wrapper">
    <div id="container">
    <!-- -->
    <!-- MLibrary Header -->
    <!-- -->

    <!-- header -->
    <div id="header">
      <div id="logo-wrapper">
        <a href="/"><img src="/static/umichwebsite/images/logo.png" alt="mlibrary" id="logo" ></a>
      </div>
      <div class="adjustable">
        <div id="alerts" class="floating-block">
      </div>
        <div id="nav">
          <ul class="links primary-links">
            <li class="menu-648 first"><a href="http://mirlyn.lib.umich.edu/" title="UM Mirlyn Library">Mirlyn Library Catalog</a></li>
            <li class="menu-649 active"><a href="/node/7" title="MLibrary Services" class="active">Services</a></li>
            <li class="menu-796"><a href="http://www.bipplebop.snagglewoof/" title="">About MLibrary</a></li>
            <li class="menu-795"><a href="/libraries" title="UM Libraries">Libraries</a></li>
          </ul>
        </div>
      </div>
    </div><!-- /header -->

    <!-- search-options -->
    <div id="search-options">
      <div id="logo-tag-wrapper">
        <a href="/">
        <img src="/static/umichwebsite/images/logo-tag.png" alt="university of michigan" id="logo-tag" ></a>
      </div>
      <div class="adjustable">
        <div id="block-mlibrary_blocks-0" class="clear-block block block-mlibrary_blocks">

          <h2>Search</h2>

          <div class="content">
            <div class="tabs">
              <a href="javascript:">MLibrary</a> <a href="javascript:">Journals</a> <a href="javascript:">Mirlyn</a>
            </div>
            <!--<form action="/"  accept-charset="UTF-8" method="post" id="mlibrary-blocks-search-form">
              <div>
                <div class="form-item" id="edit-search-wrapper">
                  <input type="text" maxlength="128" name="search" id="edit-search" size="23" value="" class="form-text" >
                </div>
                  <input type="submit" name="op" id="edit-submit" value="GO"  class="form-submit" >
                <div class="menu-drop">
                  <div class="form-item" id="edit-opt1-wrapper">
                    <label class="option"><input type="checkbox" name="opt1" id="edit-opt1" value="1"   class="form-checkbox" > opt 1</label>
                  </div>
                  <div class="form-item" id="edit-opt2-wrapper">
                    <label class="option"><input type="checkbox" name="opt2" id="edit-opt2" value="1"   class="form-checkbox" > opt 2</label>
                  </div>
                  <div class="form-item" id="edit-opt3-wrapper">
                    <label class="option"><input type="checkbox" name="opt3" id="edit-opt3" value="1"   class="form-checkbox" > opt 3</label>
                  </div>
                  <div class="form-item" id="edit-opt4-wrapper">
                    <label class="option"><input type="checkbox" name="opt4" id="edit-opt4" value="1"   class="form-checkbox" > opt 4</label>
                  </div>
                  <div class="form-item" id="edit-opt5-wrapper">
                    <label class="option"><input type="checkbox" name="opt5" id="edit-opt5" value="1"   class="form-checkbox" > opt 5</label>
                  </div>
                  <div class="form-item" id="edit-opt6-wrapper">
                    <label class="option"><input type="checkbox" name="opt6" id="edit-opt6" value="1"   class="form-checkbox" > opt 6</label>
                  </div>
                </div>
                <input type="hidden" name="form_build_id" id="form-138525087a249f31a3602dbc2b45738b" value="form-138525087a249f31a3602dbc2b45738b"  >
                <input type="hidden" name="form_token" id="edit-mlibrary-blocks-search-form-form-token" value="aba939d9194c7ce8fda0324ca6b922b4"  >
                <input type="hidden" name="form_id" id="edit-mlibrary-blocks-search-form" value="mlibrary_blocks_search_form"  >
              </div>
            </form>-->
          </div>
        </div>
        <div id="block-mlibrary_blocks-1" class="clear-block block block-mlibrary_blocks">

          <h2>Browse</h2>

          <div class="content"><!--
            <form action="/"  accept-charset="UTF-8" method="post" id="mlibrary-blocks-browse-form">
              <div>
                <div class="menu-drop">
                  <div class="form-item" id="edit-search-1-wrapper">
                    <input type="text" maxlength="128" name="search" id="edit-search-1" size="15" value="" class="form-text" >
                  </div>
                  <input type="submit" name="op" id="edit-submit-1" value="GO"  class="form-submit" >
                </div>
                <input type="hidden" name="form_build_id" id="form-0567a340403c45c80dfa9ab68631cdee" value="form-0567a340403c45c80dfa9ab68631cdee"  >
                <input type="hidden" name="form_token" id="edit-mlibrary-blocks-browse-form-form-token" value="73fdc753ff65f81421d3cd469ded1362"  >
                <input type="hidden" name="form_id" id="edit-mlibrary-blocks-browse-form" value="mlibrary_blocks_browse_form"  >
              </div>
            </form>-->
          </div>
        </div>
        <div id="block-mlibrary_blocks-2" class="clear-block block block-mlibrary_blocks">

          <h2>Get Help</h2>

          <div class="content">
            <!--<p>Get Help Links...</p>-->
          </div>
        </div>
      </div>
    </div><!-- /search-options -->

    <!-- -->
    <!-- /MLibrary Header -->
    <!-- -->

    <!-- LightBox -->
    <a onClick="hideLightbox(); return false;"><div id="lightbox"></div></a>
    <div id="popupbox" class="popupBox"></div>
    <!-- End LightBox -->

    <div id="doc3" class="yui-t5"> <!-- Change id for page width, class for menu layout. -->

      <!--<div id="hd">-->
        <!-- Your header. Could be an include. -->
        <!--<a href="/"><img src="/images/vufind.jpg" alt="vufinder"></a>-->
      <!--</div>-->



      <!-- Search box. This should really be coming from the include. -->
      <div id="bd">
        <div class="yui-main">
          <div class="searchbox">
            <div class="yui-b">
              <form method="GET" action="/Search/Home" name="searchForm" class="search" onsubmit="trimForm(this.lookfor); return true;">
                <input type="text" name="lookfor" size="30" value="{$lookfor|escape:"html"}">
                <select name="type">
                  <option value="all">{translate text="All Fields"}</option>
                  <option value="title"{if $type == 'title'} selected{/if}>{translate text="Title"}</option>
                  <option value="author"{if $type == 'author'} selected{/if}>{translate text="Author"}</option>
                  <option value="hlb"{if $type == 'hlb'} selected{/if}>{translate text="Categories"}</option>
                  <option value="subject"{if $type == 'subject'} selected{/if}>{translate text="Subject"}</option>
                  <option value="topic"{if $type == 'topic'} selected{/if}>{translate text="Topic"}</option>
                  <option value="callnumber"{if $type == 'callnumber'} selected{/if}>{translate text="Call Number"}</option>
                  <option value="isn"{if $type == 'isn'} selected{/if}>ISBN/ISSN</option>
                  <option value="tag"{if $type == 'tag'} selected{/if}>Tag</option>
                </select>
                <input type="submit" name="submit" value="{translate text="Find"}">
                <a href="/Search/Advanced" class="small">{translate text="Advanced"}</a>
                {if $lookfor }
                <br>
                <input type="radio" name="search" value="new" checked="on"> New Search
                <!--<input type="radio" name="search" value="within" onClick="document.forms['searchForm'].elements['lookfor'].value=''; document.forms['searchForm'].elements['lookfor'].focus();"> Search Within-->
                <input type="radio" name="search" value="within"> Search Within
                {/if}
              </form>
            </div>
          </div>
        </div>
          <div class="yui-b">
            {if $user}
              <a href="/MyResearch/Home">{translate text="Your Account"}</a> |
              <a href="/MyResearch/Logout">{translate text="Log Out"}</a>
            {else}
              <a href="/MyResearch/Home">{translate text="Login"}</a>
            {/if}
            <br>
            <form method="post" name="langForm">
            <select name="mylang" onChange="document.langForm.submit();">
              <option value="en">{translate text="English"}</option>
              <option value="de"{if $userLang == "de"} selected{/if}>{translate text="German"}</option>
              <option value="es"{if $userLang == "es"} selected{/if}>{translate text="Spanish"}</option>
              <option value="fr"{if $userLang == "fr"} selected{/if}>{translate text="French"}</option>
              <option value="ja"{if $userLang == "ja"} selected{/if}>{translate text="Japanese"}</option>
              <option value="nl"{if $userLang == "nl"} selected{/if}>{translate text="Dutch"}</option>
              <option value="pt-br"{if $userLang == "pt-br"} selected{/if}>{translate text="Brazilian Portugese"}</option>
              <option value="zh-cn"{if $userLang == "zh-cn"} selected{/if}>{translate text="Simplified Chinese"}</option>
              <option value="zh"{if $userLang == "zh"} selected{/if}>{translate text="Chinese"}</option>
            </select>
            </form>
          </div>
        </div>

      {include file="$module/$pageTemplate"}

      <div id="ft">
        <!-- Your footer. Could be an include. -->
          <div><p><strong>Search Options</strong></p>
            <ul>
              <li><a href="/Search/History">Search History</a></li>
              <li><a href="/Search/Advanced">Advanced Search</a></li>
              <li><a href="/Search/Advanced">Advanced Search</a></li>
            </ul>
          </div>
        <div><p><strong>Find More</strong></p>
          <ul>
              <li><a href="/Browse/Home">Browse the Catalog</a></li>
              <li><a href="/Search/Reserves">Course Reserves</a></li>
              <li><a href="/Search/NewItem">New Items</a></li>
            </ul>
        </div>
        <div><p><strong>Need Help?</strong></p>
          <ul>
              <li><a href="/Help/Home?topic=search">Search Tips</a></li>
              <li><a href="#">Ask a Librarian</a></li>
              <li><a href="#">FAQs</a></li>
            </ul>
        </div>

        <br clear="all">
      </div>
    </div>

    </div>
    <!-- -->
    <!-- MLibrary Footer -->
    <!-- -->

    <!-- footer-container -->
    <div id="footer-container">
      <!-- footer -->
      <div id="footer">
        <div id="block-mlibrary_footer-0" class="clear-block block block-mlibrary_footer">

          <h2>MLibrary Extras</h2>

          <div class="content">
            <a href="http://www.flickr.com/photos/mlibrary/" class="parent">Flickr photostream</a>
            <a href="http://www.flickr.com/photos/mlibrary/" class="child">Your photos from around the Library</a>
            <a href="/%2523" class="parent">Blogs</a>
            <a href="/%2523" class="child">Best books copyright, AuCourant, and other subjects</a><a href="/%2523" class="parent">Webcams</a>
            <a href="/%2523" class="child">from the UGLI, from North Campus</a>
            <a href="/%2523" class="parent">MLibrary Labs</a><a href="/%2523" class="child">Download new tools to connect with MLibrary</a>
          </div>
        </div>
        <div id="block-mlibrary_footer-1" class="clear-block block block-mlibrary_footer">

          <h2>MTagger</h2>

          <div class="content">
            <img src="http://www.lib.umich.edu/mtagger/img/tag(45deg).gif" >
          </div>
        </div>
        <div id="block-mlibrary_footer-2" class="clear-block block block-mlibrary_footer">

          <h2>Creative Commons</h2>

          <div class="content">
            <img src="/static/umichwebsite/images/creative-commons.gif" >
            <p class="cc">Except where otherwise noted, this work is subject to a Creative Commons license.<br ><a href="/%2523">Additional permissions are available</a></p>
            <p class="regents">&copy;2008, Regents of the University of Michigan</p>
            <p class="qc">Do you have <a href="/%2523">questions or comments</a> about this page?</p>
          </div>
        </div>
      </div><!-- /footer -->
    </div> <!-- /footer-container -->

    <!-- -->
    <!-- /MLibrary Footer -->
    <!-- -->
    </div>
  </body>
</html>
