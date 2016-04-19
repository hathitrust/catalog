{foreach from=$indexes item=cluster}
<dl class="narrowList navmenu narrow_begin" id="facet_begin_{$clusterName}">
    <dt>{$facetConfig.$cluster}</dt>
    {foreach from=$counts.$cluster item=facet name="facetLoop"}
      {if $smarty.foreach.facetLoop.iteration == 6}
        <dd id="more_{$cluster}"><a href="" onclick="showThese('{$cluster}'); return false;"><i>more...</i></a></dd>
        </dl>
        <dl class="narrowList navmenu narrow_end" id="facet_end_{$cluster}">
      {/if}
      <dd><a href="/Search/Home?{$facet.url}">{translate text=$facet.value}</a> ({$facet.count})</dd>
      {if ($smarty.foreach.facetLoop.iteration > 5) && $smarty.foreach.facetLoop.last}
          <dd><a href="#" onclick="hideThese('{$cluster}'); return false;"><i>less...</i></a></dd>
      {/if}
    {/foreach}
</dl>
{/foreach}
