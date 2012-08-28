{foreach from=$indexes item=cluster}
<div class="narrowList navmenu narrow_begin" id="facet_begin_{$clusterName}">
    <h3>{$facetConfig.$cluster}</h3>
    <ul>
    {foreach from=$counts.$cluster item=facet name="facetLoop"}
      {if $smarty.foreach.facetLoop.iteration == 6}
        <li id="more_{$cluster}"><a href="" onclick="showThese('{$cluster}'); return false;"><em>more...</em></a></li>
        </ul></div>
        <div class="narrowList navmenu narrow_end" id="facet_end_{$cluster}">
      {/if}
      <li><a href="{$url}/Search/Home?{$facet.url}">{translate text=$facet.value}</a> <span dir="ltr">({$facet.count})</span></li>
      {if ($smarty.foreach.facetLoop.iteration > 5) && $smarty.foreach.facetLoop.last}
          <li><a href="#" onclick="hideThese('{$cluster}'); return false;"><i>less...</i></a></li>
      {/if}
    {/foreach}
</div>
{/foreach}
