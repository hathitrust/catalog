<div id="bd">
  <div class="yui-main content">
    <div class="yui-b first contentbox">
    {if $recordList}
      <h4>{translate text='Your holds and recalls'}</h4>
      {if $message}<p>{$message}</p>{/if}
      {if !is_array($recordList)}
        {translate text='You do not have any holds or recalls placed'}.
      {else}
        {assign var="numItems" value=$recordList|@count}
        {if $numItems eq 1}
          {assign var="numdisp" value="1 item"}
        {else}
          {assign var="numdisp" value="$numItems items"}
        {/if}
        <p>You have placed holds on {$numdisp}</p>

        <ul class="filters">
        <form method="post">
        <input type="submit" name="submit" value="Delete holds on checked items">
        {foreach from=$recordList item=record name="recordLoop"}
          {if ($smarty.foreach.recordLoop.iteration % 2) == 0}
            <li class="result alt">
          {else}
            <li class="result">
          {/if}
          <div class="yui-ge">
            <div class="yui-u first">
              <div class="resultitem">
                <input type="checkbox" name="hold_rec_key[]" value="{$record.hold_rec_key}">
                <a href="/Record/{$record.id}" class="title">{$record.title[0]}</a><br>
                {if $record.author}
                  {translate text='by'}: {$record.author[0]}<br>
                {/if}
                {if $record.format}
                  {foreach from=$record.format item=fmt}
                    <span class="iconlabel {$fmt|lower|replace:" ":""}">{$fmt}</span>
                  {/foreach}
                  <br>
                {/if}

                {if $record.call_num}
                  {translate text='Call number'}: {$record.call_num}
                  {if $record.description} {$record.description}{/if}
                  <br>
                {/if}
                <strong>{translate text='Created'}:</strong> {$record.createdate} |
                <strong>{translate text='Expires'}:</strong> {$record.expiredate}<br>
                <strong>{translate text='Status'}:</strong> {$record.status} |
                <strong>{translate text='Pickup location'}:</strong> {$record.pickup_loc}
                <br>

               </div>
            </div>
          </div>
        </li>
        {/foreach}
        </ul>
      </form>
      {/if}
    {else}
      {translate text="Can't get patron information from Mirlyn for $username"}.
    {/if}

    </div>
  </div>

  {include file="MyResearch/menu.tpl"}

</div>



