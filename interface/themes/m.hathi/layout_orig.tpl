<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="{$userLang}">
  <head>
    <title>Hathi Trust Digital Library - {$pageTitle|truncate:64:"..."}</title>
    <link rel="search" type="application/opensearchdescription+xml" title="Library Catalog Search" href="{$url}/Search/OpenSearch?method=describe">
    <link rel="stylesheet" type="text/css" media="screen" href="{$path}/interface/themes/hathi/css/htdl_vf.css">
    <link rel="stylesheet" type="text/css" media="screen" href="{$path}/interface/themes/hathi/css/styles.css">
    <link rel="stylesheet" type="text/css" media="print" href="{$path}/interface/themes/hathi/css/print.css">

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

    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.1/jquery.min.js" type="text/javascript"></script>
    <script language="JavaScript" type="text/javascript">
    path = '{$url}';
    {literal}

    var jq = jQuery.noConflict();


  function SubmitFeedback(frm) {
    jq.post('/static/feedback.php',
            {'comment': jq("[name='comment']", frm).val(),
             'uniqname': jq("[name='uniqname']", frm).val(),
             'subject':  jq("[name='subject']", frm).val(),
             'url': jq("[name='url']:checked", frm).val()
            },
            function(data) {
              document.getElementById('popupbox').innerHTML = '<div style="padding: 4em;"><h3>Message Sent</h3><p>Thank you for your feedback!</p></div>';
              setTimeout("hideLightbox();", 3000);
              return false;
            }

          );
    return false;
  }

String.prototype.trim = function() {
	return this.replace(/^\s+|\s+$/g,"");
}

function trimForm(infrm) {
  infrm.value = infrm.value.trim()
  return true;
 }

function loginLink() {
  login = 'https://beta.lib.umich.edu/vf/vflogin?returnto=';
  loc = window.location.href;
  return login + escape(loc);
}

closeButton = ' <div style="float: right"><a href="#" onclick="hideLightbox(); return false">Close [X]</a></div>'

function fillLightbox(id) {
  lightbox();
  document.getElementById('popupbox').innerHTML = closeButton + document.getElementById(id).innerHTML;
  return false;
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


    </script>

  <script language="JavaScript" type="text/javascript">
  function changetop(A){
    // var B=document.getElementById(A);
    // var C=BrowserDetect.browser;
   // if(C=="Safari"){
    //      } else {
   	B.className="specialstyle";
    // }
  	return false;
  }

  function resumetop(A) {
  	var B=document.getElementById(A);
  	B.className="";
  	return false;
  }

  </script>

{/literal}

  </head>

  <body id="yahoo-com" class="yui-skin-sam">

    {include file="htdl_header.html"}

    <div id="doc3" class="yui-t8">

      <h2 class="SkipLink">Main Content</h2>
<!-- Searchbox -->
      <div id="bd">
        <div class="yui-main">
            {include file="searchbox.tpl"}
        </div>
      </div>

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
              <li><a href="http://babel.hathitrust.org/cgi/mb?a=page;page=help" title="Help page and faq">Help</a></li>
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
      var pageTracker = _gat._getTracker("UA-954893-17");
      pageTracker._trackPageview();
      } catch(err) {}
      {/literal}
      </script>
  </body>
</html>
