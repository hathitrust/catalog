<div id="bd">
  <div class="yui-main content">

    <div class="yui-b first contentbox">

      <div class="yui-ge">
        <div class="yui-u first">
          <h3 class="fav">{translate text='Your Favorites'}</h3>

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
                  <img src="{$path}/bookcover.php?isn={$resource.isbn|truncate:10:""}&size=small" class="alignleft">

                <div class="resultitem">
                  <a href="{$url}/Record/{$resource.id}" class="title">{$resource.title}</a><br>
                  {if $resource.author}
                  By: <a href="{$url}/Author/Home?author={$resource.author}">{$resource.author}</a><br>
                  {/if}
                  {if $resource.tags}
                  {translate text='Your Tags'}:
                  {foreach from=$resource.tags item=tag name=tagLoop}
                    <a href="{$url}/Search/Home?tag={$tag->tag}">{$tag->tag}</a>{if !$smarty.foreach.tagLoop.last},{/if}
                  {/foreach}
                  <br>
                  {/if}
                  {if $resource.notes}
                  Notes: {$resource.notes}<br>
                  {/if}
                  <img src="{$path}/images/{$resource.format|lower}-icon.gif" alt="{$resource.format}"> {$resource.format}

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
          You do not have any saved resources
          {/if}
        </div>

        <div class="yui-u">
          <h3 class="tag">{translate text='Your Tags'}</h3>

          {if $tags}
          <ul>
          {foreach from=$tags item=tag}
            <li>Tag: {$tag}</li>
            <a href="{$url}/MyResearch/Home?{foreach from=$tags item=mytag}{if $tag != $mytag}tag[]={$mytag}&{/if}{/foreach}">X</a>
          {/foreach}
          </ul>
          {/if}

          <ul>
          {foreach from=$tagList item=tag}
            <li>
              <a href="{$url}/MyResearch/Home?tag[]={$tag->tag}{foreach from=$tags item=mytag}&tag[]={$mytag}{/foreach}">{$tag->tag}</a> ({$tag->cnt})
            </li>
          {/foreach}
          </ul>
        </div>
      </div>
	</div>
    </div>

    <div class="yui-b">
      <div class="box submenu">
        {if $user->cat_username}
          <h4>{translate text='Your Checked Out Items'}</h4>
          {if $transList}
          <ul class="filters">
          {foreach from=$transList item=item}
            <li>
              <img src="{$path}/images/{$item.format}" alt="{$item.format}">
              <a href="{$url}/Record/{$item.id}">{$item.title}</a><br>
              <b>Due: {$item.duedate}</b>
            </li>
          {/foreach}
          </ul>
          {else}
          You do not have any items checked out.
          {/if}
        {else}
          <h4>Library Catalog Profile</h4>
          <p>In order to establish your account profile, please enter the following information:
          <form method="post">
            Library Catalog Username:<br>
            <input type="text" name="cat_username" value="" size="25"><br>
            Library Catalog Password:<br>
            <input type="text" name="cat_password" value="" size="25"><br>
            <input type="submit" name="submit" value="Save">
          </form>
        {/if}
      </div>
    </div>

</div>