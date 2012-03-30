
{* much code duplicated from view-holdings.tpl; can it be consolidated? *}

<div class="headerlight2darkgrad"><!----></div>
<div class="headergrad"><!----></div>

<div class="whitebox">
	{assign var=copy value=$holdings[$smarty.get.hid]}
	{assign var='loc' value=$copy_key|replace:" ":"_"}
	{assign var=summary_info value=''}

	{if $copy.public_note}{assign var=summary_info value=$summary_info|cat:"<span class='holdingsummary'>Note: "|cat:$copy.public_note|cat:"</span>"}{/if}
	{if $copy.summary_holdings}{assign var=summary_info value=$summary_info|cat:"<span class='holdingsummary'>Library has: "|cat:$copy.summary_holdings|cat:"</span>"}{/if}
	{if $copy.supplementary_material}{assign var=summary_info value=$summary_info|cat:"<span class='holdingsummary'>Supplementary material: "|cat:$copy.supplementary_material|cat:"</span>"}{/if}
	{if $copy.index}{assign var=summary_info value=$summary_info|cat:"<span class='holdingsummary'>Index: "|cat:$copy.index|cat:"</span>"}{/if}
	
	{if $summary_info}
		{$summary_info}
	{/if}
	
	<ul class="holdings" id="holdingsList">
		{assign var=hiddenCount value=0}
		{foreach from=$copy.item_info item=item name=itemLoop}
			{if $item.status=='Search only (no full text)' && $item.rights neq 'opb'}
				{assign var=hiddenCount value=$hiddenCount+1}
			{/if}		
			{include file="Record/view-list-holdings.tpl"}
		{/foreach}
		{if $hiddenCount>0}
			<li><a href="#" onclick="toggle_ht_so(1);return false"><span id="htso_showlabel" style="display:inline">Show</span><span id="htso_hidelabel" style="display:none">Hide</span> {$hiddenCount} Search Only Items</a></li>
		{/if}
	</ul>
</div>

<div class="footergrad"><!----></div>
