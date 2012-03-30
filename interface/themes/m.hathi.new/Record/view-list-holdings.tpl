{if $copy.sub_library == 'HATHI'}
<li 
{if $item.status=='Search only (no full text)' && $item.rights neq 'opb'}
 class="holding_container linkeditemrightarrow "
 {else}
 class="holding_container linkeditemrightarrow "
 {/if}
>
	<a href="{$ht_url}/cgi/pt?skin=mobile;id={$item.id};">
			{if $item.description}
				<div class="gotopt"><span>Go To {$item.description}</span></div>
			{else}
				{if $item.rights eq 'opb' || $item.status=='Search only (no full text)'}
					<div class="gotopt"><span>Go To Limited View</span></div>
				{else}
					<div class="gotopt"><span>Go To Full View</span></div>
				{/if}
			{/if}
			
			<div class='originalsource'>(original from {$item.source})&nbsp;&nbsp;</div>
			{if $item.rights eq 'opb'}
				<div class="limitedview">Limited (search-only)</div>
			{else}
				{if $item.status=='Search only (no full text)'}
					<div class="limitedview">Limited (search-only)</div>
				{else}
					<div class="fullview">Full View</div>
				{/if}
			{/if}			
	</a>
</li>
{/if}