<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="{$userLang}"xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <title>HathiTrust-{$pageTitle|truncate:64:"..."}</title>
    <link rel="search" type="application/opensearchdescription+xml" title="Library Catalog Search" href="{$url}/Search/OpenSearch?method=describe">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="Cache-Control" content="max-age=600" />
    <meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=3" />
    <link rel="stylesheet" type="text/css" href="{$path}/interface/themes/hathi/css/mobile.css" />
    
  </head>

  <body>

    {include file="mobile_header.html"}
    
    <div id="container">
      {include file="$module/$pageTemplate"}
    </div>
    
    <div id="mFooter">
      <a href="http://catalog.hathitrust.org/">Go to full site</a> | <a href="http://m.catalog.hathitrust.org/">New Search</a>
      <!-- <ul>
          <li><a href="https://babel.hathitrust.org/cgi/mb?a=listcs;colltype=pub;sort=cn_a">Login</a></li> 
          <li><a href="http://www.hathitrust.org" title="About Hathi Trust">About</a></li>
          <li><a href="http://babel.hathitrust.org/cgi/mb?a=page;page=help" title="Help page and faq">Help</a></li>
          <li><a href=" " id="feedback_footer" title="Feedback form for problems or comments"><span>Feedback</span></a></li>
          <li><a href="http://mirlyn.lib.umich.edu/" title="New Search in Mirlyn">Mirlyn Library Catalog</a></li>
          <li> | <a href="http://www.hathitrust.org/take_down_policy" title="item removal policy">Take-Down Policy</a></li>v
      </ul>-->
    </div>


<!-- fixme:suzchap I removed analytics, popout_help.html... some of which may need to be added back.-->
  
  </body>
</html>
