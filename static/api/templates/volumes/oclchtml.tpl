<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html
        PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <title>{$doc.titles[0]} (OCLC {$doc.oclcs|join:', '})</title>
</head>

<body>

<table class="hathiLinks">
  {foreach from=$data.items item=link}
     <tr><td><a href="{$link.itemURL}">{$link.enumcron} {$link.usRightsString} Original from {$link.orig}.</a></td>
  {/foreach}
</table>



</body>
</html>
