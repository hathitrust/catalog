<link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.0/themes/base/jquery-ui.css" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.0/jquery-ui.min.js"></script> 

{literal}
 <script type="text/javascript">
  jq(document).ready(function(){
    jq("#datepicker").datepicker({ dateFormat: 'mm/dd/yy', minDate: '+7', defaultDate: '+2m' });
  });
  </script>

<style type="text/css" media="screen">
/*   h2 {margin-top: 2.5em; margin-bottom: .75em;} */
</style>

{/literal}

{if $patron.campus eq 'UMAA' and ($patron.bor_status eq '01') or ($patron.bor_status eq '03')}
  {assign var="show_7fast" value='Y'}
{/if}

{if $message}
  <div style="color: red">{$message}</div>
{/if}

<table>
  <tr><th style="width: 10em">Title:</th><td>{"<br/>"|implode:$record.titles}</td></tr>
{if $record.author}
  <tr><th style="width: 10em">Author:</th><td>{"<br/>"|implode:$record.author}</td></tr>
{/if}
  <tr><th>Call number:</th><td>{$record.item.callnumber}</td></tr>
{if $record.item.description}
  <tr><th>Volume or Issue:</th><td>{$record.item.description}</td></tr>
{/if}
  <tr><th>Owning library:</th><td>{$record.item.location}</td></tr>
  <tr><th>Item status:</th><td>{$record.item.status}</td></tr>
</table>

<div style="text-align: center">
<!-- <h2>The <strong>GET THIS</strong> service is provided free of charge.</h2> -->
<h3>If this item is currently available (On shelf), estimated delivery time is <strong>2-3 days</strong>.<h3>
</div>

<table style="width: auto; margin: 0 auto">
<tr valign="top">
<td style="width: 450px; background-color: #eaeaea; border: 1pt solid #a9a9a9; padding: 1em 1em">
<h3 style="font-weight:bold">Hold for me at a library (free of charge)</h3>
<div style="font-style: italic; margin-bottom: 2em">Library-to-Library Service</div>

<p><strong>Have items held for you at your preferred library:</strong></p>

<div style="background-color: #ffffff; border: 1pt solid #a9a9a9; padding: 6px; margin-right: 3em; margin-bottom: 1em;">
<form action="{$url}/Record/{$id}/Hold" method="post" onSubmit="PlaceHold({$id}, this); return false;">
<input type="hidden" name="barcode" value="{$record.item.barcode}">
<input type="hidden" name="patron_name" value="{$patron.firstname} {$patron.lastname}">
<input type="hidden" name="patron_id" value="{$patron.id}">

<div style="margin-bottom: 1em">
Pickup Location:  <select name="pickup_loc" id="pickup_loc">
<option value="">{translate text="Select a pickup location"}</option>
{foreach from=$pickupLocList item=desc key=loc}
  <option value="{$loc}"{if $pickup_loc == $loc} selected{/if}>{$desc}</option>
{/foreach}
</select>
</div>

<div style="margin-bottom: 1em">
Cancel this hold if item is not available before: 
<input id="datepicker" size="10" maxlength="10" type="text" name="not_needed_after" value="{$not_needed_after}">
</div>
<input type="submit" name="submit" value="Submit">

</form>
</div>

<div style="font-weight:bold;">If this item is not available ("Checked out" or "On order"):</div>
We will place a hold or recall on it.  When it becomes available,  we will hold it for you at your 
preferred library. You will be notified via email, and your item will be held for one week.  Delivery 
time estimated at two weeks or longer.

</td>

{if $show_7fast}
<td style="width: 5px; text-align: center; padding-top: 2em; font-size: 150%">OR</td>
<td style="width: 450px; background-color: #eaeaea; border: 1pt solid #a9a9a9; padding: 1em 1em">
<h3 style="font-weight:bold">Deliver to me (free of charge for Ann Arbor faculty and grad students)</h3>
<div style="font-style:italic;margin-bottom: 2em">7FAST (MLibrary Document Delivery)</div>

<p><strong>We currently offer 2 delivery methods:</strong></p>
<div style="background-color: #ffffff; border: 1pt solid #a9a9a9; padding: 6px; margin-right: 3em; margin-bottom: 1em;">
<!-- div style="margin-top: 1em; margin-bottom: 1em;" -->
<form target="ill" 
      id="illForm" 
      action="http://ill.lib.umich.edu/illiad/illiad.dll/OpenURL" 
      method="get" 
      onsubmit="args=['submittoill', jq('input[name=genre]:checked').val()];   clickpostlog(this, args);true">
      
  <input type="hidden" name="sid" value="mirlyn">
{foreach from=$ctx_object item=ctx_value key=ctx_param}
  <input type="hidden" name="{$ctx_param}" value="{$ctx_value}">
{/foreach}

<table style="margin-top: 0em;">
<tr>
<td><input type="radio" name="genre" value="docdelbook" checked></td>
<td>Delivery of items to a departmental mailbox/reception area.</td>
</tr>
<tr>
<td> <input type="radio" name="genre" value="docdeljournal"></td>
<td>Journal articles and book chapters can be scanned and posted to a secure website as a PDF (Copyright guidelines apply).</td>
</tr>
</table>

<input type="submit" value="Next">
</form>
</div>

<div style="font-weight:bold;">If this item is not available:</div>
We will forward your request to Interlibrary Loan.  See our website for 
<a href="http://www.lib.umich.edu/interlibrary-loan-um-community/illiad-faq#HowSoon">delivery time estimates for ILL</a>.
</td>
{/if}
</tr>
</table>

<br/>
<a href="{$url}/Record/{$id}" class="backtosearch"><img style="vertical-align: middle;" alt="Back to full record display" src="/static/umichwebsite/images/return.png">{translate text="Back to full record display"}</a>
