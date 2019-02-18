  <ul>
    <li><a href="/MyResearch/Favorites">(<span class="num">{$tagobj->numFavoriteItems()}</span>)  All Favorites</a></li>
  </ul>
  <ul>
    {foreach from=$tagobj->tagsAndCounts() item=tagcount}
      {if $tagcount.count > 0}
        <li id="tagCount_{$tagcount.tag}" class="favoriteLink"><a "clickpostlog" ref="favchoosetag|{$tagcount.tag}" href="/MyResearch/Favorites?tag={$tagcount.tag|escape:'url'}">( <span class="num">{$tagcount.count}</span>) {$tagcount.tag}</a></li>
      {/if}
    {/foreach}
  </ul>
