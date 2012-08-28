{foreach from=$indexes item=cluster}
	{if $counts.$cluster|@count >0 }
	<div class='facetheader'>{$facetConfig.$cluster}</div>
	<ul class="facetlist">
		{foreach from=$counts.$cluster item=facet name="facetLoop"}
			{if $smarty.foreach.facetLoop.iteration == 6}
				<li id="less_{$cluster}" class="moreless">
					{* <div id="less_{$cluster}" > *}
						<a  href="#" onclick="htToggleFacetView(true, '#less_{$cluster}','.more_{$cluster}', '#morelink_{$cluster}' ); return false;">Show More</a>
					{* </div> *}
				</li>
			{/if}

			{if $smarty.foreach.facetLoop.iteration > 5}
				<li class="linkeditemrightarrow more_{$cluster}" style="display:none">
			{else}
				<li class="linkeditemrightarrow">
			{/if}

			<a  ref="{$facet.logargs}" class="findme" href="{$url}/Search/Home?{$facet.url}">{translate text=$facet.value} ({$facet.count})</a>
			</li>

			{if ($smarty.foreach.facetLoop.iteration > 5) && $smarty.foreach.facetLoop.last}
				<li id='morelink_{$cluster}' style="display:none" class="moreless">
					<a  href="#" onclick="htToggleFacetView(false, '#less_{$cluster}','.more_{$cluster}', '#morelink_{$cluster}'); return false;">Show Less</a>
 				</li>
			{/if}
		{/foreach}
	</ul>
	{/if}
{/foreach}
