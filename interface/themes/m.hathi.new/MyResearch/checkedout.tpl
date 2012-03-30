{literal}
 <script type="text/javascript">

function SetCheckBox(FormName, FieldName, Value)
{
  form = jq('form[name=' + FormName + ']');
  if (form) {
	//key='input[name=' + FieldName + ',value=' + Value + ']';
	key='input[value=' + Value + ']';
	checkbox=jq(key, form);
	if(checkbox){
      jq(checkbox, form).attr('checked', 'checked');
	}
  }
}

</script>
{/literal}

<div class="headerlight2darkgrad"><!----></div>

{assign var=button2 value='newsearch'}
{assign var=button3 value='favorites'}
{include file='buttonbar.tpl'}
{if $user->patron}
  <form name=renewItems method="post">
    {assign var="numItems" value=$transList|@count}
    {if $numItems gt 0} 
      <div id="subHeader">
        {if $numItems eq 1}
          {assign var="numdisp" value="1 item"}
        {else}
          {assign var="numdisp" value="$numItems items"}
        {/if}
        {*{translate text='Your Checked Out Items:'}*}
        <p {*class="sublabel"*}>You have {$numdisp} checked out</p>
       
        {if $transList}
          <div >
            {* <label>{translate text='Sort'}&nbsp;</label> *}
            <select id="checkedoutitemssort" name="sort" onChange="document.location.href='{$fullPath}&amp;sort=' + this.options[this.selectedIndex].value;">
              <option value="duedate_sort_a"{if $sort == "duedate_sort_a"} selected{/if}>{translate text='Due date (ascending)'}</option>
              <option value="duedate_sort_d"{if $sort == "duedate_sort_d"} selected{/if}>{translate text='Due date (descending)'}</option>
              <option value="title_sort"{if $sort == "title_sort"} selected{/if}>{translate text='Title'}</option> 
              <option value="author"{if $sort == "author"} selected{/if}>{translate text='Author'}</option>
            </select>
            {*<input id="renew" type="submit" name="submit" value="Renew All">*}
          </div>
        {/if}
        {foreach from=$transList item=resource name="recordLoop"}
          {if $resource.renew_message}<p><h4>{$resource.renew_message}</h4></p>{/if}
        {/foreach}
      </div>
          
      <div class="headergrad"><!----></div>
          
      <ul class="list" id="checkedOut">
        {foreach from=$transList item=resource name="recordLoop"}
          {if ($smarty.foreach.recordLoop.iteration % 2) == 0}
            <li class="result alt">
          {else}
            <li class="result">
          {/if}
          {assign var="recordCounter" value=$smarty.foreach.recordLoop.iteration}
          <span class="resultleftcol">
		    <span class="resultstitle">{$recordCounter}.</span>
		  </span>

          <div class="resultrightcol">
          
              <!-- img src="{$path}/bookcover.php?isn={$resource.isbn|truncate:10:""}&size=small" class="alignleft" -->
              <div class="resultitem">
                <input type="checkbox" name="item_barcodes[]" value="{$resource.barcode}" style="display:none;">
                  {if $resource.id}
                    <a href="{$url}/Record/{$resource.id}?returnpage=checkedout" class="title">{$resource.title[0]}</a><br>
                  {else}
                    {$resource.title[0]}<br>
                  {/if}
                  
                  {if $resource.author}
                    <div class="resultssubheader">{translate text='by'}: {$resource.author[0]}</div>
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
                      <span class="iconlabel2 value {$fmt|lower|replace:" ":""}">{translate text=$fmt}</span>
                    {/foreach}
                    <br>
                    {if $resource.location}
                      <span class="checkedoutlabel">{translate text='Library'}: </span><span class="value">{$resource.location}</span>
                      <br />
                    {/if}
                    {if $resource.call_num}
                      <span class="checkedoutlabel">{translate text='Call number'}:</span> <span class="value">{$resource.call_num}</span>
                      {if $resource.description} <span class="value">{$resource.description}</span>{/if}
                      <br>
                    {/if}
                  
                    <h4>Due:  {$resource.duedate}</h4>
                    {if $resource.status} <h4>({$resource.status})</h4>{/if}
                    <br>
                    {if $resource.renew_message}<h4>{$resource.renew_message}</h4><br>{/if}
                    <input type="submit" OnClick="SetCheckBox('renewItems', 'item_barcodes[]', '{$resource.barcode}');" name="submit" value="Renew this Item">                    
                  </div>
              </div>
            </li>
        {/foreach}
      </ul>
    {else}
      <div class="contentbox">{translate text='You do not have any items checked out'}.</div>
    {/if}
  </form>
{else}
  <div class="contentbox">{translate text="Can't get patron information from Mirlyn for $username"}.</div>
{/if}
	
<div class="footergrad"><!----></div>	
