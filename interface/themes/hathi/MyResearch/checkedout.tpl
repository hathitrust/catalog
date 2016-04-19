<div id="bd">
  <div class="yui-main content">
    <div class="yui-b first contentbox">

        {if $user->cat_username}
          <h4>{translate text='Your Checked Out Items'}</h4>
          {assign var="numItems" value=$transList|@count}
          {if $numItems gt 0}
            {if $numItems eq 1}
              {assign var="numdisp" value="1 item"}
            {else}
              {assign var="numdisp" value="$numItems items"}
            {/if}
            <p>You have {$numdisp} checked out</p>
          {/if}

          <div style="margin: 1em; font-style: italic">Note: The ability to renew items from this screen will be added soon.</div>
          {if $transList}
          <ul class="filters">
          {foreach from=$transList item=resource name="recordLoop"}
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

                    {assign var=formatList value=$resource.format}

                    {if is_array($formatList)}
                      {foreach from=$formatList item=fmt}
                        {capture name="fmtTrans"}{translate text=$fmt}{/capture}
                    <span class="{$fmt|lower|replace:" ":""|regex_replace:"/[()]/":"-"} iconlabel">{$smarty.capture.fmtTrans|strip|replace:' ':'&nbsp;'}</span>
                      {/foreach}
                    {else}
                    <span class="iconlabel {$formatList|lower|replace:" ":""}">{translate text=$formatList}</span>
                    {/if}
                    <br>
                    {if $resource.call_num}
                      {translate text='Call number'}: {$resource.call_num}
                      {if $resource.description} {$resource.description}{/if}
                      <br>
                    {/if}

                    <b>Due: {$resource.duedate}</b>

                  </div>
                </div>

              </div>
            </li>
          {/foreach}
          </ul>
          {else}
          {translate text='You do not have any items checked out'}.
          {/if}
        {else}
          <h4>{translate text='Library Catalog Profile'}</h4>
          <p>{translate text='In order to establish your account profile, please enter the following information'}:
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

  {include file="MyResearch/menu.tpl"}

</div>
