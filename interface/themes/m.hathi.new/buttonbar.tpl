<div id="buttonbar">
	{if $button1=='refine'}
		<span class="buttoncontainerodd"><a class="buttonOdd"  onclick="toggleFacetView('results_view','facets_view')" href="#">Refine Search</a></span>
	{elseif $button1=='toggleresults'}
		<span class="buttoncontainerodd"><a class="buttonOdd"  onclick="toggleFacetView('facets_view','results_view')" href="#">&lt;&nbsp;Results</a></span>	
	{elseif $button1=='results'}
		<span class="buttoncontainerodd"><a class="buttonOdd" href="{$lastsearch}">&lt;&nbsp;Results</a></span>
	{elseif $button1=='record'}
		<span class="buttoncontainerodd"><a class="buttonOdd" href="{$site.url}/Record/{$id}">&lt;&nbsp;Record</a></span>
    {elseif $button1=='checkedout'}
		<span class="buttoncontainerodd"><a class="buttonOdd" href="{$site.url}/MyResearch/CheckedOut">&lt;&nbsp;Back</a></span>		
	{else}
		<span class="buttoncontainerodd"><a class="buttonOdd" href="#">&nbsp;</a></span>
	{/if}
	
	{if $button2=='newsearch'}
		<span class="buttoncontainereven"><a class="buttonEven" href="{$site.url}">New Search</a></span>
	{else}
		<span class="buttoncontainereven"><a class="buttonEven" href="#">&nbsp;</a></span>
	{/if}
	{*
	{if $button3=='favorites'}
		<span class="buttoncontainerodd"><a class="buttonOdd" href="/MyResearch/Favorites"><span class="favorites">Favorites</span></a></span>
	{else}
		<span class="buttoncontainerodd"><a class="buttonOdd" href="#">&nbsp;</a></span>
	{/if}
	*}
	{*<span class="buttoncontainerodd"><a class="buttonOdd kiosk" href="#">&nbsp;</a><a class="buttonOdd nokiosk" href="/MyResearch/MyResearchMenu">My Account</a></span>*}
	<span class="buttoncontainerodd"><a class="buttonOdd" href="#">&nbsp;</a></span>
</div>

{*<br />*}
	