<?xml version="1.0" encoding="UTF-8" ?>

<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">

<!--
# Change Log
# 06 Oct 2011  sethip  Changed 'Regular Site' link to hathitrust.org
#
-->
<html lang="{$userLang}" xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="HandheldFriendly" content="true" />
		<link rel="alternate" media="handheld" href="" />
		<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" />
		<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1; minimum-scale=1; user-scalable=0;" />
		<meta name="format-detection" content="telephone=no" />

		{if $smarty.server.REQUEST_URI=="/"}
			<title>HathiTrust Mobile Digital Library</title>
		{else}
			<title>{$pageTitle|truncate:64:"..."} | HathiTrust Mobile Digital Library</title>
		{/if}


		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js" charset="utf-8"></script>
		{literal}
		<script type="text/javascript" charset="utf-8">

    		var HT = HT || {};
    		HT.update_status = function(status) {
	      		$(document).ready(function() {
        			if ( status.message ) {
          			//	$("#message").html(status.message + "<br />" + "Authenticated via: " + status.authType);
          				console.log(status.message + "  \n" + "Authenticated via: " + status.authType);
        			}

        			if ( status.logged_in ) {
          				if ( status.affiliation ) {
            			//	$("#affiliation").html(status.affiliation + "<br />" + "Member, HathiTrust");
            				console.log(status.affiliation + "  \n" + "Member, HathiTrust");
          				}
        			}
      			})
    		}
  		</script>
  		{/literal}

		<!--<script type="text/javascript" src="{$ht_url}/cgi/ping?debug=local;callback=HT.update_status"></script>-->
		<script type="text/javascript" src="{$ht_url}/cgi/ping?callback=HT.update_status"></script>

		<script type="text/javascript" charset="utf-8">
			var jq = jQuery.noConflict();
		</script>

		<script src="/js/htm-concat-min.js" type="text/javascript" charset="utf-8"></script>

		<script language="JavaScript" type="text/javascript" charset="utf-8">
			path = '{$url}';
			{literal}
			function fixform(f) {
				if (jq('#searchtype').val() == 'journaltitle') {
					jq(f).append('<input type="hidden" name="filter[]" value=\'format:Serial\'>');
				}
			}
			{/literal}
		</script>

		<link rel="stylesheet" type="text/css" charset="utf-8" href="{$path}/interface/themes/{$configArray.Site.theme}/css/htm-concat-min.css" />

	</head>

	<body onload="scrollTo(0,1);">

		{* this is where the page content is invoked... *}
		{include file="$module/$pageTemplate"}

		<div class="footer" id="footerDiv">
		    <div id="footerlogin">
		    {literal}<script>
    			document.write(HT.login_link());
    			if(!HT.login_status.logged_in){
    				jq("#footerlogin a").attr('href',jq("#footerlogin a").attr('href') + "&skin=mobilewayf");
    			}
    		</script>{/literal}
    		</div>
			<span style="color:black">Mobile</span> | <a href="http://www.hathitrust.org/?mdetect=no">Regular Site</a>
			<br />
			<a href="/Search/Feedback?currenturl={$smarty.server.REQUEST_URI|escape:'url'}">Feedback</a> | <a href="http://www.hathitrust.org/help_mobile">Help</a> | <a href="http://www.hathitrust.org/take_down_policy">Takedown</a>
			<br />

			{if $smarty.server.REMOTE_ADDR eq "141.211.43.161" || $smarty.server.REMOTE_ADDR eq "141.211.43.160"}
				<div>Remote Address: {$smarty.server.REMOTE_ADDR}</div>
				<div>Server Address: {$smarty.server.SERVER_ADDR}</div>
				<div>Last Search: {$lastsearch}</div>
				<div>Current: {$current}</div>
				<div>Search Comps: {$searchcomps}</div>
				<div>Inst: {$inst}</div>
			{/if}

		</div>

		<script type="text/javascript">
		{literal}
		var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
		document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
		</script>
		<script type="text/javascript">
		try {
		var pageTracker = _gat._getTracker("UA-954893-23");
		pageTracker._trackPageview();
		} catch(err) {}
		{/literal}
		</script>

	</body>
</html>
