<div id="bd">
  <div id="yui-main" class="content">
    <div class="yui-b first contentbox">

      <!-- Internal Grid -->
      <div class="yui-ge">
        <div class="yui-u first">
          <h3 class="fav">{translate text='Your Favorites'}</h3>

<p>Display of Your Favorites and Your Tags Will Be Coming Soon</p>
          
{*       {if $resourceList}
          <ul>
          {foreach from=$resourceList item=resource name="recordLoop"}
            {if ($smarty.foreach.recordLoop.iteration % 2) == 0}
            <li class="result alt">
            {else}
            <li class="result">
            {/if}
              <div class="yui-ge">
                <div class="yui-u first">
                  <img src="{$path}/bookcover.php?isn={$resource.isbn|truncate:10:""}&size=small" class="alignleft">

                  <div class="resultitem">
                    <a href="{$url}/Record/{$resource.id}" class="title">{$resource.title}</a><br>
                    {if $resource.author}
                    {translate text='by'}: <a href="{$url}/Author/Home?author={$resource.author}">{$resource.author}</a><br>
                    {/if}
                    {if $resource.tags}
                    {translate text='Your Tags'}:
                    {foreach from=$resource.tags item=tag name=tagLoop}
                      <a href="{$url}/Search/Home?tag={$tag->tag}">{$tag->tag}</a>{if !$smarty.foreach.tagLoop.last},{/if}
                    {/foreach}
                    <br>
                    {/if}
                    {if $resource.notes}
                    {translate text='Notes'}: {$resource.notes}<br>
                    {/if}

                    <span class="iconlabel {$resource.format|lower|replace:" ":""}">{translate text=$resource.format}</span>

                  </div>
                </div>

                <div class="yui-u">
                  <a href="{$url}/MyResearch/Edit?id={$resource.id}" class="edit tool">{translate text='Edit'}</a>
                  <a href="{$url}/MyResearch/Home?delete={$resource.id}" class="delete tool" onClick="confirm('Are you sure you want to delete this?');">{translate text='Delete'}</a>
                </div>
              </div>
            </li>
          {/foreach}
          </ul>
          {else}
          {translate text='You do not have any saved resources'}
          {/if}
        </div>

        <div class="yui-u">
          <h3 class="tag">{translate text='Your Tags'}</h3>

          {if $tags}
          <ul>
          {foreach from=$tags item=tag}
            <li>{translate text='Tag'}: {$tag}</li>
            <a href="{$url}/MyResearch/Home?{foreach from=$tags item=mytag}{if $tag != $mytag}tag[]={$mytag}&amp;{/if}{/foreach}">X</a>
          {/foreach}
          </ul>
          {/if}

          <ul>
          {foreach from=$tagList item=tag}
            <li>
              <a href="{$url}/MyResearch/Home?tag[]={$tag->tag}{foreach from=$tags item=mytag}&amp;tag[]={$mytag}{/foreach}">{$tag->tag}</a> ({$tag->cnt})
            </li>
          {/foreach}
          </ul>
*}          
        </div>
      </div>
      <!-- End of Internal Grid -->
      
    </div>
    <!-- End of first Body -->
  </div>
  
  {include file="MyResearch/menu.tpl"}

</div>