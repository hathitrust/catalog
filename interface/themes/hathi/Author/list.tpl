<div id="bd">
  <div id="yui-main" class="content">
    <div class="yui-b first contentbox">

      <div class="record">
        {if $lastsearch}
          <p>  <a href="{$url}/Search/Home?{$lastsearch}" class="backtosearch">&laquo; Back to Search Results</a></p>
        {/if}

      <!-- Listing Options -->
      <div class="yui-ge resulthead">
        <div class="yui-u first">
        {if $total}
          {translate text="Showing"}
          <b>{$start}</b> - <b>{$end}</b>
          {translate text='of'} <b>{$total}</b>
          {translate text='Results for'} author or coauthor contains <b>{$lookfor[0]}</b>
        {/if}
        </div>

        <div class="yui-u toggle">
          {translate text='Sort'}
          <select name="bsort" onChange="document.location.href='/Author/Search?page=1&amp;{$searchcomps}&amp;bsort=' + this.options[this.selectedIndex].value;">
             <option value="count" {if $sort == "count"} selected{/if}>{translate text='Frequency'}</option>
             <option value="index"{if $sort == "index"} selected{/if}>{translate text='Alphabetical'}</option>
           </select>
        </div>
      </div>
      <!-- End Listing Options -->

       {foreach from=$values item=valcount name="recordLoop"}
          {if ($smarty.foreach.recordLoop.iteration % 2) == 0}
          <div class="result alt record{$smarty.foreach.recordLoop.iteration}">
          {else}
          <div class="result record{$smarty.foreach.recordLoop.iteration}">
          {/if}

            <div class="yui-ge">
              <div class="yui-u first">
                <a href="{$url}/Search/Home?lookfor=%22{$valcount[0]}%22&amp;type=realauth">{$valcount[0]}</a> 
              </div>
              <div class="yui-u">
                {$valcount[1]}
              </div>
            </div>
          </div>
        {/foreach}
        
        {assign var=pageLinks value=$pager->getLinks()}
        <div class="pagination">{$pageLinks.all}</div>

      </div>
    </div>
  </div>
</div>