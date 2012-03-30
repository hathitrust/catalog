<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="{$language}">
  <head>
    <title>VuFind Administration - {$pageTitle}</title>
    <link rel="stylesheet" type="text/css" media="screen" href="{$path}/interface/themes/ashsc/css/styles.css">
    <link rel="stylesheet" type="text/css" media="print" href="{$path}/interface/themes/ashsc/css/print.css">
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8">
  </head>

  <body>
  
    <div id="doc2" class="yui-t5"> <!-- Change id for page width, class for menu layout. -->

      <div id="hd">
        <!-- Your header. Could be an include. -->
        <a href="{$url}"><img src="{$path}/images/vufind.jpg" alt="vufinder"></a>
        Administration
      </div>
    
      {include file="$module/$pageTemplate"}

      <div id="ft">
      </div>
      
    </div>
  </body>
</html>