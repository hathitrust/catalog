<h3 class="fav">{$list->title}</h3>
{if $resourceList}
<ul>
  {foreach from=$resourceList item=resource name="recordLoop"}
    {if ($smarty.foreach.recordLoop.iteration % 2) == 0}
  <li class="result alt">
    {else}
  <li class="result">
    {/if}
    <div class="yui-ge">
      <div class="yui-u first">
        <img src="/bookcover.php?isn={$resource.isbn|truncate:10:""}&size=small" class="alignleft">
        <div class="resultitem">
          <a href="/Record/{$resource.id}" class="title">{$resource.title}</a><br>
          {if $resource.author}
          {translate text='by'}: <a href="/Author/Home?author={$resource.author}">{$resource.author}</a><br>
          {/if}
          {if $resource.tags}
          {translate text='Your Tags'}:
          {foreach from=$resource.tags item=tag name=tagLoop}
          <a href="/Search/Home?tag={$tag->tag}">{$tag->tag}</a>{if !$smarty.foreach.tagLoop.last},{/if}
          {/foreach}
          <br>
          {/if}
          {if $resource.notes}
          {translate text='Notes'}: {$resource.notes}<br>
          {/if}

          <span class="iconlabel {$resource.format|lower|replace:" ":""}">{translate text=$resource.format}</span>

        </div>
      </div>
    </div>
  </li>
  {/foreach}
</ul>
{else}
{translate text='You do not have any saved resources'}
{/if}
