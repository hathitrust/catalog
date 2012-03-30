<ul class="list" id="locationList">

{assign var=prev_location value=''}
{assign var=show_available value='false'}

{foreach from=$holdings item=copy key=copy_key name=copyloop}

	{*<li>*}
	{if $copy.sub_library == 'HATHI'}
		{if $copy.location != $prev_location}
			<li {*class="linkeditem"*}>
			{*{assign var=copy_num value=1}*}
	    	{*<span class="locationname">{$copy.location}</span><br />*}
	
			{* brk -- link to library info....
		    {if $copy.info_link}
		      <a href={$copy.info_link} target="new"><img src="{$path}/images/info.gif" alt="Library information"></a>
		    {/if}
		    </h3>
		    *}
		{else}
			<li id="continuedlocation">
		{/if}
			
		{assign var=num_items value=$copy.item_info|@count}
		{assign var='loc' value=$copy_key|replace:" ":"_"}

		{assign var=summary_info value=''}

		{if $copy.public_note}{assign var=summary_info value=$summary_info|cat:"<span class='holdingsummary'>Note: "|cat:$copy.public_note|cat:"</span>"}{/if}
		{if $copy.summary_holdings}{assign var=summary_info value=$summary_info|cat:"<span class='holdingsummary'>Library has: "|cat:$copy.summary_holdings|cat:"</span>"}{/if}
		{if $copy.supplementary_material}{assign var=summary_info value=$summary_info|cat:"<span class='holdingsummary'>Supplementary material: "|cat:$copy.supplementary_material|cat:"</span>"}{/if}
		{if $copy.index}{assign var=summary_info value=$summary_info|cat:"<span class='holdingsummary'>Index: "|cat:$copy.index|cat:"</span>"}{/if}
	
		{if $num_items eq 0}
			{if $copy.callnumber}{assign var=summary_info value=$summary_info|cat:"<span class='callnumber'>"|cat:$copy.callnumber|cat:"&nbsp</span>"}{/if}
			{if $copy.status}{assign var=summary_info value=$summary_info|cat:"<span class='status'>"|cat:$copy.status|cat:"</span>"}{/if}  
		{/if}

		{if $summary_info}{$summary_info}{/if}
	
		<ul class="holdings" {*id="holdingsList"*}>
			{assign var=displayedCount value=0}
			{foreach from=$copy.item_info item=item name=itemLoop}	
				
				{*{if $displayedCount <= 5}*}
					{*{$item|@print_r}<br />*}

					{include file="Record/view-list-holdings.tpl"}
				
				{*{elseif $displayedCount == 6}
					{assign var=displayedCount value=$displayedCount+1}
					<li>
						<a href="{$url}{$smarty.server.REQUEST_URI}/LocationHoldings?hid={$smarty.foreach.copyloop.index}">More...</a>
					</li>
				{/if}*}

			{/foreach}
		</ul>
  		{assign var=prev_location value=$copy.location}
	</li>
	{/if}
{/foreach}

</ul>

