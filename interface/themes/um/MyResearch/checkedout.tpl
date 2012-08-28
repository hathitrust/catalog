{literal}
 <script type="text/javascript">
function SetAllCheckBoxes(FormName, FieldName, CheckValue)
{
  form = jq('form[name=' + FormName + ']');
  if (form) {
     if (CheckValue) {
       jq('input[name=' + FieldName + ']', form).attr('checked', 'checked')
     } else {
       jq('input[name=' + FieldName + ']', form).removeAttr('checked');
    }
  }
}



</script>
{/literal}

<div id="bd">
  <div class="yui-main content">
    <div class="yui-b first contentbox">

        {if $user->patron}
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

          {if $transList}
          <ul class="filters">
          <form name=renewItems method="post">
          <div class="yui-u first">
            <input type="submit" name="submit" value="Renew checked items">
            <input type="button" onclick="SetAllCheckBoxes('renewItems', 'item_barcodes[]', true);" value="Select all">
            <input type="button" onclick="SetAllCheckBoxes('renewItems', 'item_barcodes[]', false);" value="Unselect all">
          </div>

          <div class="yui-u toggle" style="width: auto">
            {translate text='Sort'}&nbsp;<select name="sort" onChange="document.location.href='{$fullPath}&amp;sort=' + this.options[this.selectedIndex].value;">
              <option value="duedate_sort_a"{if $sort == "duedate_sort_a"} selected{/if}>{translate text='Due date (ascending)'}</option>
              <option value="duedate_sort_d"{if $sort == "duedate_sort_d"} selected{/if}>{translate text='Due date (descending)'}</option>
              <option value="title_sort"{if $sort == "title_sort"} selected{/if}>{translate text='Title'}</option>
              <option value="author"{if $sort == "author"} selected{/if}>{translate text='Author'}</option>
            </select>
          </div>

          {foreach from=$transList item=resource name="recordLoop"}
            {if ($smarty.foreach.recordLoop.iteration % 2) == 0}
            <li class="result alt">
            {else}
            <li class="result">
            {/if}
              <div class="yui-ge">
                <div class="yui-u first">
                  <!-- img src="{$path}/bookcover.php?isn={$resource.isbn|truncate:10:""}&size=small" class="alignleft" -->

                  <div class="resultitem">
                    <input type="checkbox" name="item_barcodes[]" value="{$resource.barcode}">
                    {if $resource.id}
                      <a href="{$url}/Record/{$resource.id}" class="title">{$resource.title[0]}</a><br>
                    {else}
                      {$resource.title[0]}<br>
                    {/if}
                    {if $resource.author}
                    {translate text='by'}: {$resource.author[0]}<br>
                    {/if}
                    {if $resource.tags}
                    {translate text='Your Tags'}:
{*                   {foreach from=$resource.tags item=tag name=tagLoop}
                       <a href="{$url}/Search/Home?tag={$tag->tag}">{$tag->tag}</a>{if !$smarty.foreach.tagLoop.last},{/if}
                     {/foreach}
*}
                    <br>
                    {/if}
                    {if $resource.notes}
                    {translate text='Notes'}: {$resource.notes}<br>
                    {/if}

                    {foreach from=$resource.format item=fmt}
                      <span class="iconlabel {$fmt|lower|replace:" ":""}">{translate text=$fmt}</span>
                    {/foreach}
                    <br>
                    {if $resource.location}
                      {translate text='Library'}: {$resource.location} |
                    {/if}
                    {if $resource.call_num}
                      {translate text='Call number'}: {$resource.call_num}
                      {if $resource.description} {$resource.description}{/if}
                      <br>
                    {/if}

                    <b>Due: {$resource.duedate}</b>
                    {if $resource.status} ({$resource.status}){/if}
                    <br>
                    {if $resource.renew_message}{$resource.renew_message}<br>{/if}

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
          {translate text="Can't get patron information from Mirlyn for $username"}.
        {/if}


    </div>
  </div>

  {include file="MyResearch/menu.tpl"}

</div>
