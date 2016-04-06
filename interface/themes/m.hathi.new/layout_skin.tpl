<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<!-- BEGIN PAGE CONFIGURATION -->
{php}

        $title = $pageTitle;
{/php}

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" dir="ltr">
<head>
<!-- No need to change either of these includes; the title (pgTitle) is taken
     from above, so leave the pgTitle alone -->

{php}
include("./static/phase2/php/newheader_top2.php");
{/php}

    <link rel="search" type="application/opensearchdescription+xml" title="Library Catalog Search" href="/Search/OpenSearch?method=describe">
    <link rel="stylesheet" type="text/css" media="screen" href="/interface/themes/umichwebsite_feedback/css/styles.css">
    <link rel="stylesheet" type="text/css" media="print" href="/interface/themes/umichwebsite_feedback/css/print.css">
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

closeButton = ' <div style="float: right"><a href="#" onclick="hideLightbox(); return false">Close [X]</a></div>'

function fillLightbox(id) {
  lightbox();
  document.getElementById('popupbox').innerHTML = closeButton + document.getElementById(id).innerHTML;
}

function pausecomp(millis)
{
  var date = new Date();
  var curDate = null;

  do { curDate = new Date(); }
  while(curDate-date < millis);
}

function submitFeedback()
{
  document.getElementById('popupbox').innerHTML +=  document.getElementById('feedback_thanks').innerHTML;
  setTimeout("hideLightbox()", 1500);
  return true;
}

function hideMenu(elemId)
{
    document.getElementById(elemId).style.display='none';
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


</head>

{php}
include("./static/phase2/php/newheader_bottom2.php");
{/php}


<!--
  ###################### BEGIN POPOUT DISPLAY TEXT########################
-->

  <div id="favorite_help" style="display: none">
    <h2>Add to favorites</h2>
    <p>Adding a "favorite" is not yet available.</p>

    <p>When functioning, adding a favorite will allow you to not only mark it as a private favorite, but also to tag the record with arbitrary tags for later use in sorting through your favorites.</p>
  </div>

  <div id="rss_help" style="display: none">
    <h2>Get RSS Feed</h2>
    <p>When functioning correctly, we will be able to provide an RSS feed of a search suitable for use in a feed reader such as Bloglines or Google Reader.</p>
  </div>


  <div id="login_help" style="display:none;" >
    <h2>Login</h2>
    <p>The login function will use library authentication / cosign, but it is not yet implemented
    In addition to being required to tag a record/search or mark it as a "favorite", logged-in patrons will also be able to:
     <ul>
        <li>See what they have checked out from Mirlyn and via ILL
        <li>View their profile information
        <li>Browse their favorites
       <li>See a listing of their holds and recalls
        <li>Initiate "Get This" requests for holds, recalls, library-to-library delivery, etc.
    </ul>
    </p>
  </div>

    <div id="searchhistory_help" style="display:none;" >
      <h2>Search History</h2>
      <p>Search History is not currently working correctly. When functional, it will keep track of your recent searches and allow
      you to revisit them within a browser session.</p>

    </div>

    <div  id="browse_help" style="display:none;" >
      <h2>Browse the catalog</h2>
      <p>Sequential browsing of the catalog is not currently functional, but you can try another type of browsing by searching for nothing at all (just click the search button with an empty box) and then narrowing the results.</p>

      <p>We have not yet determined if we'll be able to make browsing available when the initial public Mirlyn2 beta rolls out in January.</p>

    </div>


    <div  id="hold_help" style="display:none;" >
      <h2>Hold/Recall an item</h2>
      <p>The ability to hold or recall an item is not yet functional; it will be working by the time we go live.</p>

    </div>


    <div  id="feedback" style="display:none;" >
      <h2>Mirlyn2 Beta Feedback</h2>

      <form method="POST" id="feedbackform" action="mailto:dueberb@umich.edu"      onsubmit="submitFeedback();return true;"
      >
      <script language="JavaScript" type="text/javascript">
      {literal}
      document.write('<input type="hidden" name="url" value="' + document.URL + '">');
      {/literal}
      </script>
        <table style="border: none">
          <tr><td style="padding-right: 2em; text-align:left; vertical-align: top">Subject</td><td style="text-align:left; vertical-align: top"><input type="text" name="subject" size=40></td></tr>
          <tr><td style="text-align:left; vertical-align: top padding-right: 2em;">Comment or Question</td><td style="text-align:left; vertical-align: top"><textarea style="width: 250px; height: 100px;" name="comment"></textarea></td></tr>
        </table>

      <div id="feedback_thanks" style="display:none"><div style="padding: 1em; color: red;" >Thank you for taking the time to provide feedback.</div></div>
      <div id="submitbutton"><input type="submit" /></div>
      </form>
    </div>

    <div  id="reserves_help" style="display:none;" >
      <h2>Reserves</h2>
      <p>Currently, this application doesn't support an integrated reserves system with Aleph. We will likely
      send users to the <a href="http://mirlyn.lib.umich.edu/F/?local_base=miu30_pub">reserves system in Mirlyn</a>.</p>
    </div>

    <div  id="newbooks_help" style="display:none;" >
      <h2>New Items</h2>
      <p>It's not clear how to support "New Items" in this software; for the moment, we're just going to send people
      to  <a href="http://www.lib.umich.edu/newbooks/">the regular Newly Cataloged Items page</a>.</p>
    </div>

    <div  id="searchtips_help" style="display:none;" >
      <h2>Search Tips</h2>
      <p>Search tips are not functional at this point.  It has not yet been determined how searching guidance will be provided.</p>
    </div>

    <div  id="askus_help" style="display:none;" >
      <h2>Ask a Librarian</h2>
      <p>Any "Ask a Librarian" links will likely be turned into our normal AskUs link, e.g., <p style="padding-top: 1em; padding-left: 3em"><a href="http://www.lib.umich.edu/ask/"><img src="http://www.lib.umich.edu/graphics/phase1/askus-sidebar.jpg" alt="Ask a Librarian" title="Ask a Librarian" border="0" height="11" width="17">Ask a Librarian</a></p></p>
    </div>


<!-- Record view.tpl -->
<div  id="email_help" style="display: none">
  <h2>Email</h2>
  <p>Emailing a record is not yet working correctly and will be fixed before launch.</p>

  <p>Decisions about exactly what data to email (e.g., just the ALA-formatted citation as it appears under the "Cite This" link?)
     are ongoing.</p>
</div>

<div  id="refworks_help" style="display:none;" >
<h2>Export to RefWorks</h2>
<p>The export to RefWorks functionality is not yet working correctly; we hope to have it in place by launch.</p>
</div>

<div   id="endnote_help" style="display:none;" >
<h2>Export to Endnote</h2>
<p>The export to Endnote functionality is not yet working correctly; we hope to have it in place by launch.</p>
</div>

<div   id="zotero_help" style="display:none;" >
<h2>Export to Zotero</h2>
<p>The export to Zotero functionality is not yet working correctly; we hope to have it in place by launch.</p>
</div>

<!-- Home.tpl -->

<div style="display:none;margin: .75em;" id="m2beta_help">
  <h2>Welcome to the Mirlyn2 Staff Only Beta</h2>
  <p>We're hoping staff will take the time to provide us with feedback about the application -- what seems to work well,
    what doesn't, features you'd like to see, bugs you've found...any comments at all are appreciated!</p>

  <p>To submit a comment, just click on the big green "Provide Feedback" link at the top of any page, or send email
    directly to <a href="mailto:mirlyn2-beta-feedback@umich.edu">mirlyn2-beta-feedback@umich.edu</a>.
  </p>
</div>


<!--
  ###################### END POPOUT DISPLAY TEXT########################
-->

    <div id="wrapper">
    <div id="container">

    <!-- LightBox -->
    <a onClick="hideLightbox(); return false;"><div id="lightbox"></div></a>
    <div id="popupbox" class="popupBox"></div>
    <!-- End LightBox -->

    <div id="doc3" class="yui-t8"> <!-- Change id for page width, class for menu layout. -->

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
                <input type="hidden" name="checkspelling" value="true" />
                <input type="text" name="lookfor" size="30" value="{$lookfor|escape:"html"}">
                <select name="type">
                  <option value="all">{translate text="All Fields"}</option>
                  <option value="title"{if $type == 'title'} selected{/if}>{translate text="Title"}</option>
                  <option value="author"{if $type == 'author'} selected{/if}>{translate text="Author"}</option>
                  <option value="hlb"{if $type == 'hlb'} selected{/if}>{translate text="Categories"}</option>
                  <option value="subject"{if $type == 'subject'} selected{/if}>{translate text="Subject"}</option>
                  <!-- <option value="topic"{if $type == 'topic'} selected{/if}>{translate text="Topic"}</option> -->
                  <!-- <option value="callnumber"{if $type == 'callnumber'} selected{/if}>{translate text="Call Number"}</option> -->
                  <option value="isn"{if $type == 'isn'} selected{/if}>ISBN/ISSN</option>
                  <!-- <option value="tag"{if $type == 'tag'} selected{/if}>Tag</option> -->
                </select>
                <input type="submit" name="submit" value="{translate text="Find"}">
                <a href="/Search/Advanced" class="small">{translate text="Advanced"}</a>

                <a style="color: darkgreen; padding-left: 3em;" href="mailto:mirlyn2-beta-feedback@umich.edu?subject=Mirlyn2 Beta Feedback">Provide Feedback<img style="vertical-align: text-bottom" src="/static/umichwebsite/images/feedback_icon.jpg"></a>
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
          <div class="yui-b" style="padding-top: 20px">
            {if $user}
              <a href="/MyResearch/Home">{translate text="Your Account"}</a> |
              <a href="/MyResearch/Logout">{translate text="Log Out"}</a>
            {else}
              <!-- <a href="/MyResearch/Home">{translate text="Login"}</a> -->
              <a href="#" onclick="fillLightbox('login_help');return false;">{translate text="Login"}</a>
            {/if}
            <!-- <br>
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
            </form> -->
          </div>
        </div>

      {include file="$module/$pageTemplate"}

      <div id="ft">
        <!-- Your footer. Could be an include. -->
<a name="footer"></a>
          <div><p><strong>Search Options</strong></p>
            <ul>
<!--              <li><a href="/Search/History">Search History</a></li> -->
              <li><a href="#" onclick="fillLightbox('searchhistory_help');return false;">Search History</a></li>
              <li><a href="/Search/Advanced">Advanced Search</a></li>
            </ul>
          </div>
        <div><p><strong>Find More</strong></p>
          <ul>
              <!-- <li><a href="/Browse/Home">Browse the Catalog</a></li> -->
              <li><a href="#" onclick="fillLightbox('browse_help');return false;">Browse the Catalog</a></li>
              <!-- <li><a href="/Search/Reserves">Course Reserves</a></li> -->
              <li><a href="http://mirlyn.lib.umich.edu/F/?local_base=miu30_pub">Course Reserves</a></li>
              <li><a href="http://www.lib.umich.edu/newbooks/">New Items</a></li>


            </ul>
        </div>


        <div><p><strong>Need Help?</strong></p>
          <ul>
              <li><a href="#" onclick="fillLightbox('searchtips_help');return false;">Search Tips</a></li>
              <!-- <li><a href="/Help/Home?topic=search">Search Tips</a></li> -->
              <li> <a href="http://www.lib.umich.edu/ask/">Ask a Librarian</a></li>
            </ul>
        </div>

        <br clear="all">
      </div>
    </div>

    </div>
    <!-- -->
    <!-- MLibrary Footer -->
    <!-- -->

    {php}
    include("./static/phase2/php/newfooter2.php");
    {/php}

    <!-- -->
    <!-- /MLibrary Footer -->
    <!-- -->
