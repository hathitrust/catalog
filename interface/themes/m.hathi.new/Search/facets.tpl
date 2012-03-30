<![CDATA[
{foreach from=$facets item=cluster}
{assign var=clusterName value=$cluster.name}
<dl class="narrowList navmenu narrow_begin" id="facet_begin_{$clusterName}">
  <dt>{$facetConfig.$clusterName}</dt>
  {if isset($cluster.item.count)}
  <dd><a href="{$fullUrl}&filter[]={$clusterName|escape:"url"}:%22{$cluster.item._content|escape:"url"}%22">{translate text=$cluster.item._content}</a> ({$cluster.item.count})</dd>
  {else}
    {foreach from=$cluster.item item=facet name="facetLoop"}
      {if $smarty.foreach.facetLoop.iteration == 6}
  <dd id="more_{$clusterName}"><a href="" onclick="showThese('{$clusterName}'); return false;"><i>more...</i></a></dd>
</dl>
<dl class="narrowList navmenu narrow_end" id="facet_end_{$clusterName}">
      {/if}
  <dd><a href="{$fullUrl}&filter[]={$clusterName|escape:"url"}:%22{$facet._content|escape:'url'}%22">{translate text=$facet._content}</a> ({$facet.count})</dd>
      {if ($smarty.foreach.facetLoop.iteration > 5) && $smarty.foreach.facetLoop.last}
  <dd><a href="" onclick="hideThese('{$clusterName}'); return false;"><i>less...</i></a></dd>
      {/if}
    {/foreach}
  {/if}
</dl>
{/foreach}
]]>
