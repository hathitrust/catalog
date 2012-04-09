<div id="bd">
  <div id="yui-main" class="content">
    <div class="yui-b first contentbox">
      <h4>{translate text='Your Holds and Recalls'}</h4>
      {if is_array($recordList)}
      {assign var="numItems" value=$recordList|@count}
        {if $numItems eq 1}
          {assign var="numdisp" value="1 item"}
        {else}
          {assign var="numdisp" value="$numItems items"}
        {/if}
        <p>You have placed holds on {$numdisp}</p>

      <ul class="filters">
      {foreach from=$recordList item=record name="recordLoop"}
        {if ($smarty.foreach.recordLoop.iteration % 2) == 0}
        <li class="result alt">
        {else}
        <li class="result">
        {/if}
          <div class="yui-ge">
            <div class="yui-u first">
              <img src="{$path}/bookcover.php?isn={$record.isbn}&size=small" class="alignleft">

              <div class="resultitem">
                <a href="{$url}/Record/{$record.id}" class="title">{$record.title}</a><br>
                {if $record.author}
                {translate text='by'}: <a href="{$url}/Author/Home?author={$record.author}">{$record.author}</a><br>
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

                {assign var=formatList value=$record.format}

                {if is_array($formatList)}
                  {foreach from=$formatList item=fmt}
                    {capture name="fmtTrans"}{translate text=$fmt}{/capture}
                <span class="{$fmt|lower|replace:" ":""|regex_replace:"/[()]/":"-"} iconlabel">{$smarty.capture.fmtTrans|strip|replace:' ':'&nbsp;'}</span>
                  {/foreach}
                {else}
                <span class="iconlabel {$formatList|lower|replace:" ":""}">{translate text=$formatList}</span>
                {/if}
               </div>
            </div>
          </div>
        </li>
      {/foreach}
      </ul>
      {else}
      {translate text='You do not have any holds or recalls placed'}.
      {/if}
    </div>
  </div>

  {include file="MyResearch/menu.tpl"}

</div>
