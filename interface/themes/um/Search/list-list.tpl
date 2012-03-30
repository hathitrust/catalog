
{assign var="lcaction" value="recview"}
{if $favoritesPage}
  {assign var="lcaction" value="favrecview"}
{/if}
{if $selectedItemsPage}
  {assign var="lcaction" value="selectedrecview"}
{/if}

<div style="margin-top: 1.5em">
{foreach from=$recordSet item=record name="recordLoop"}
  {if ($smarty.foreach.recordLoop.iteration % 2) == 0}
  <div id="record_{$record.id}"   class="result alt record{$smarty.foreach.recordLoop.iteration} {if $selectedItemsPage}selectedItemsPageRecord{/if} {if $favoritesPage}favoritesPageRecord{/if}">
  {else}
  <div id="record_{$record.id}"  class="result record{$smarty.foreach.recordLoop.iteration} {if $selectedItemsPage}selectedItemsPageRecord{/if} {if $favoritesPage}favoritesPageRecord{/if}">
  {/if}
  

    <div class="yui-ge">
      <div class="yui-u first">
      <div class="googleCoverColumn">
        <div id="GoogleCover_{$record.id}" class="googleCover">
          <img src="/images/noCover2.gif">
        </div>
        <div class="tempAndFavorites">
          <div class="temp" id="temp_{$record.id}">
          {if $tagobj->inTempItems($record.id)}
              <input type="checkbox" id="inSelected_{$record.id}" onclick="selectedToggle(this)" class="selectedCheckbox" value="on" checked="checked">&nbsp;<label class="selectedCheckboxLabel" for="inSelected_{$record.id}">Selected</label>
          {else}
              <input type="checkbox" id="inSelected_{$record.id}" onclick="selectedToggle(this)" class="selectedCheckbox" value="on">&nbsp;<label class="unselectedCheckboxLabel" for="inSelected_{$record.id}">Select</label>
          {/if}
          </div>
          
          {if $favoritesPage}
          <div>
            <p>
            
            {assign var="item" value=$tagobj->item($record.id)}
            {assign var="dtags" value=$item->displayTags()}
            {assign var="taglist" value=""}
            <p class="taglist">
              {foreach from=$dtags item="dtag" name="dtagloop"}
                {if $smarty.foreach.dtagloop.iteration != 1}
                  <br>
                  {assign var="taglist" value="$taglist,"}
               {/if}
                  <a href="/MyResearch/Favorites?tag={$dtag}">{$dtag}</a>
                  {assign var='taglist' value="$taglist $dtag"}
              {/foreach}
            </p>
            
            <form>
              <input type="hidden" name="tags" value="{$taglist}">
              <input type="hidden" name="title" value="{$record.title[0]}">
              <input type="hidden" name="id" value="{$record.id}">
              <button style="padding: 0px .25em; width: auto; overflow: visible" onclick="editFavoriteForm(this); return false;">Edit/Remove</button>
            </form>
              
          
            
            
          </div>
          {else}
          <div class="favorite" id="favorite_{$record.id}">
            {if $tagobj->isFavorite($record.id)}
              <span class="favorites">Favorite</span>
            {/if}
          </div>
          {/if}
        </div>
        
    </div>
        <div class="resultitem">
          <abbr class="unapi-id" title="urn:bibnum:{$record.id}"></abbr>

          <div id="resultItemLine1" class="results_title">
            {if $showscores}
            {assign var=score value=$record.score*1000}
            (<span class="score">{$score}</span>)
            {/if}
            {assign var="recordCounter" value=`$smarty.foreach.recordLoop.iteration+$recordStart-1`}          
            {assign var="disptitle" value=$record.title[0]}
<!-- title array -->
            {foreach from=$record.title item=title name=titleloop}
              {if ($smarty.foreach.titleloop.iteration == 1) || $title != '' }
              <a ref="recview|{$record.id}||{$recordCounter}|showrecordnav" href="/Record/{$record.id}" class="title clickpostlog">{$title|truncate:180:"..."|highlight:$lookfor|default:'Title not available'}</a><br>
              {/if}
            {/foreach}          
          </div>


  
          <div id="resultItemLine2" class="results_author">
          {if $record.author}
          {translate text='by'}
          {if is_array($record.author)}
            {foreach from=$record.author item=author}
           <a class="clickpostlog" ref="realauthlink" href="/Search/Home?lookfor=%22{$author|escape:'uri'}%22&amp;type=author">{$author|highlight:$lookfor}</a>
            {/foreach}
          {else}
          <a class="clickpostlog" ref="realauthlink" href="/Search/Home?lookfor=%22{$record.author|escape:'uri'}%22&amp;type=author">{$record.author|highlight:$lookfor}</a>
          {/if}
          {/if}
          </div>
    
          <div id="resultItemLine3" class="results_published">
          {if $record.publishDate}{translate text='Published'} {$record.publishDate.0}{/if}
          </div>

          <div id="resultItemLine4" class="results_format">
       
          {assign var=id value=$record.id}

          {if $record.format}
            {foreach from=$record.format item=fmt}
            <span class="iconlabel {$fmt|lower|regex_replace:"/[ ()-]/":""}">{$fmt}</span>
            {/foreach}
          {/if}
          </div>

           {assign var=holdings value=$resultHoldings.$id}
           <table class="holdings" width="100%" id="holdings{$record.id}">
            <tr>
              <th width="55%">{translate text='Location'}</th>
              <th width="20%">{translate text='Status'}</th>
              <th width="30%">{translate text='Call Number / Description'}</th>
            </tr>
            {assign var=prev_location value=''}
            {foreach from=$holdings item=holding key=copy_key}
              {assign var=copy_id value=$copy_key|replace:" ":"_"}
              {if $holding.location != $prev_location}
                <tr id="{$copy_id}_{$id}">
                  <td class="holdingLocation">
                    {$holding.location}
                    {if $holding.temp_loc_count > 0}
                       {if $holding.temp_loc_count == 1 and $holding.temp_loc}<br>(shelved at {$holding.temp_loc})
                       {else}<br>(some items shelved elsewhere, see holdings for details)
                       {/if}
                    {/if}
                </td>
                {if $holding.status eq 'See holdings'}
                  <td><a class="clickpostlog" ref="llholdings|{$record.id}|{$holding.location}|{$recordCounter}" href="/Record/{$record.id}/Holdings#{$copy_id}">{$holding.status}</a></td>
                {elseif $holding.sub_library eq 'ELEC'}
                  {assign var=holding_link value=$holding.link}
                  {assign var=pholding_link value=$holding_link}
                  <td><a class="clickpostlog" ref="elink|{$record.id}|{$holding_link}|{$recordCounter}" target="link" href="{$pholding_link}">{$holding.status}</a></td>
                {elseif $holding.sub_library eq 'HATHI'}
                  {if $holding.item_info.0.rights eq 'opb'}
                    <td><a class="clickpostlog" 
                           ref="hathilink|{$record.id}|hathi|{$holding.status}|{$recordCounter}" 
                           target="link" 
                           href="http://hdl.handle.net/2027/{$holding.id}">Limited Access</a>
                           [Full view available to authenticated UM users and in some UM Libraries ...  
                            <i><a class="dolightbox" href="#section108">more</a></i> ]                               
                    </td>
                  {else}
                    <td><a class="clickpostlog" ref="hathilink|{$record.id}|hathi|{$holding.status}|{$recordCounter}" target="link" href="http://hdl.handle.net/2027/{$holding.id}">{translate text=$holding.status}</a></td>
                  {/if}
                {else}
                  <td>{$holding.status}</td>
                {/if}
                <td>
                {if $holding.sub_library eq 'ELEC' or $holding.sub_library eq 'HATHI'}
                    {$holding.description} {$holding.note}
                {else}
                    {$holding.callnumber}
                {/if}
                </td>
                </tr>
              {/if}
              {assign var=prev_location value=$holding.location}
            {/foreach}
          </table>
        </div>
      </div>
    </div>
    
    

<!-- 
          {if $record.format=="Book"}
    <span class="Z3988"
          title="ctx_ver=Z39.88-2004&amp;rft_val_fmt=info%3Aofi%2Ffmt%3Akev%3Amtx%3Abook&amp;rfr_id=info%3Asid%2F{$coinsID}%3Agenerator&amp;rft.genre=book&amp;rft.btitle={$record.title|escape:"url"}&amp;rft.title={$record.title|escape:"url"}&amp;rft.series={$record.series}&amp;rft.au={$record.author|escape:"url"}&amp;rft.date={$record.publishDate}&amp;rft.pub={$record.publisher|escape:"url"}&amp;rft.edition={$record.edition|escape:"url"}&amp;rft.isbn={$record.isbn}">
          {elseif $record.format=="Journal"}
    <span class="Z3988"
          title="ctx_ver=Z39.88-2004&amp;rft_val_fmt=info%3Aofi%2Ffmt%3Akev%3Amtx%3Ajournal&amp;rfr_id=info%3Asid%2F{$coinsID}%3Agenerator&amp;rft.genre=article&amp;rft.title={$record.title|escape:"url"}&amp;rft.date={$record.publishDate}&amp;rft.issn={$record.issn}">
          {else}
    <span class="Z3988"
          title="ctx_ver=Z39.88-2004&amp;rft_val_fmt=info%3Aofi%2Ffmt%3Akev%3Amtx%3Adc&amp;rfr_id=info%3Asid%2F{$coinsID}%3Agenerator&amp;rft.title={$record.title|escape:"url"}&amp;rft.creator={$record.author|escape:"url"}&amp;rft.date={$record.publishDate}&amp;rft.pub={$record.publisher|escape:"url"}&amp;rft.format={$record.format}">
          {/if}
-->

  </div>

<!--   {if !$record.url} 
  <script type="text/javascript">
     getStatuses('{$record.id}');
  </script>
  {/if} -->
  <script type="text/javascript">
   {if $record.googleLinks}
      jq(document).ready(function() {literal}{{/literal}
        getGoogleBookInfo('{$record.googleLinks}', '{$record.id}', '{$recordCounter}');
      {literal}});{/literal}
    {/if}
  </script>


{/foreach}

</div>
