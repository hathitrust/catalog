
{if $patron.campus eq 'UMAA' and ($patron.bor_status eq '01') or ($patron.bor_status eq '03')}
  {assign var="show_7fast" value='Y'}
{/if}


<div class="headerlight2darkgrad"><!----></div>

{assign var=button1 value='record'}
{assign var=button2 value='newsearch'}
{assign var=button3 value='favorites'}
{include file='buttonbar.tpl'}
	
<div id="detailHeader" >
	{if $message}
  		<div style="color: red">{$message}</div>
	{/if}

	<h1>{"<br/>"|implode:$record.titles}</h1>
	{if $record.author}
		<label>{translate text='Author'}:</label>
		<span class="value">{"<br/>"|implode:$record.author}</span>
		<br />
	{/if}
	<label>{translate text='Call Number'}:</label>
	<span class="value">{$record.item.callnumber}</span>
	{if $record.item.description}
		<br />
		<label>{translate text='Volume or Issue'}:</label>
		<span class="value">{$record.item.description}</span>	
	{/if}
	<br />
	<label>{translate text='Owning library'}:</label>
	<span class="value">{$record.item.location}</span>
	<br />
	<label>{translate text='Item status'}:</label>
	<span class="value">{$record.item.status}</span>	
</div>
	
<div class="headergrad"><!----></div>

<div class="holdboxouter">					
	<p class="centerText">If this item is currently available (On shelf), estimated delivery time is 2-3 days.<p>
	<div class="holdbox">
		<p class="boldText">Hold for me at a library (free of charge)</p>
		<p>Have items held for you at your preferred library:</p>
		
		<form action="{$url}/Record/{$id}/Hold" method="post" onSubmit="PlaceHold({$id}, this); return false;">
			<input type="hidden" name="barcode" value="{$record.item.barcode}">
			<input type="hidden" name="patron_name" value="{$patron.firstname} {$patron.lastname}">
			<input type="hidden" name="patron_id" value="{$patron.id}">
		
			<p>
				<label>Pickup Location:</label>  
				<span><select name="pickup_loc" id="pickup_loc">
					<option value="">{translate text="Select a pickup location"}</option>
					{foreach from=$pickupLocList item=desc key=loc}
		  				<option value="{$loc}"{if $pickup_loc == $loc} selected{/if}>{$desc}</option>
					{/foreach}
				</select></span>
			</p>
	
				
			<p>
				<label>Cancel this hold if item is not available before:</label>
				
				<span><input id="datepicker" maxlength="10" type="text" name="not_needed_after" value="{$not_needed_after}" /></span>
			</p>
			
			<input type="submit" name="submit" value="Submit">
		
		</form>
	</div>
	
	{if $show_7fast}
	
		<p id="orLabel">OR</p>
		
		<div class="holdbox">
			<p>Deliver to me (free of charge for Ann Arbor faculty and grad students)</p>
			<p>7FAST (MLibrary Document Delivery)</p>
			<p>We currently offer 2 delivery methods:</p>
		
			<form target="ill" 
		      id="illForm" 
		      action="http://ill.lib.umich.edu/illiad/illiad.dll/OpenURL" 
		      method="get" 
		      onsubmit="args=['submittoill', jq('input[name=genre]:checked').val()];   clickpostlog(this, args);true">
		      
		  		<input type="hidden" name="sid" value="mirlyn">
				{foreach from=$ctx_object item=ctx_value key=ctx_param}
		  			<input type="hidden" name="{$ctx_param}" value="{$ctx_value}">
				{/foreach}
		
				<p>
					<input type="radio" name="genre" value="docdelbook" checked>
					<label>Delivery of items to a departmental mailbox/reception area.</label>
				</p>
		
				<p>
					<input type="radio" name="genre" value="docdeljournal">
					<label>Journal articles and book chapters can be scanned and posted to a secure website as a PDF (Copyright guidelines apply).</label>
				</p>
		
				<input type="submit" value="Next">
			</form>
		
			<p>If this item is not available:</p>
			<p>We will forward your request to Interlibrary Loan. See our website for 
				<a href="http://www.lib.umich.edu/interlibrary-loan-um-community/illiad-faq#HowSoon">delivery time estimates for ILL</a>.
			</p>
		</div>

	{/if}
	
</div>

<div class="footergrad"><!----></div>
