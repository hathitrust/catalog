<div id="bd">
  <div id="yui-main" class="content">
    <div class="yui-b first contentbox">
    {if $transList}
      <h4>{translate text='Your Fines'}</h4>
      {if !is_array($transList)}
        {translate text='You do not have any fines'}.
      {else}
        {assign var="numItems" value=$transList|@count}
        {if $numItems eq 1}
          {assign var="numdisp" value="1 fine"}
        {else}
          {assign var="numdisp" value="$numItems fines"}
        {/if}
        <p>You have {$numdisp}</p>
      {/if}
          
      {if $numItems ge 1}
        <table class="filters">
          <tr>
            <th>Bibliographic Info</th>
            <th>Description</th>
            <th>Amount</th>
            <th>Date</th>
            <th>Status</th>
          </tr>
          {foreach from=$transList item=resource name="recordLoop"}
            {if ($smarty.foreach.recordLoop.iteration % 2) == 0}
            <tr class="result alt">
            {else}
            <tr class="result">
            {/if}
            {if $resource.title}<td><a href="{$url}/Record/{$resource.id}" class="title">{$resource.title[0]}</a></td>
            {else}<td></td>{/if}
            {if $resource.fine_description}<td>{$resource.fine_description}</a></td>
            {else}<td></td>{/if}
            {if $resource.fine}<td>{$resource.fine}</a></td>
            {else}<td></td>{/if}
            {if $resource.date}<td>{$resource.date}</a></td>
            {else}<td></td>{/if}
            {if $resource.status}<td>{$resource.status}</a></td>
            {else}<td></td>{/if}
            </tr>
          {/foreach}
          </table>
      {/if}

    {else}
      {translate text="Can't get patron information from Mirlyn for $username"}.
    {/if}
    </div>
  </div>

  {include file="MyResearch/menu.tpl"}

</div>
