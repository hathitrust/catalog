<script language="JavaScript" type="text/javascript" src="{$path}/services/Search/ajax.js"></script>
<script language="JavaScript" type="text/javascript" src="{$path}/js/googleLinks.js"></script>

<!-- Main Listing -->
<div id="bd">
  <div id="yui-main" class="content">
    <div class="yui-b first contentbox">

      <!-- Narrow Options for an Author Search-->
      {if $narrow}
      <div   class="yui-g resulthead" style="border: solid 1px #999999; background-color: #FFFFEE;">
        <div class="yui-u first">
        {foreach from=$narrow item=narrowItem name="narrowLoop"}
          {if $smarty.foreach.narrowLoop.iteration == 6}
            </div>
            <div class="yui-u">
          {/if}
          <a href="{$url}/Search/Home?{$narrowItem.authurl}">{$narrowItem.name}</a> ({$narrowItem.num})<br>
        {/foreach}
        </div>
        {if $narrowcount > $smarty.foreach.narrowLoop.iteration}
       <div style="clear:both; text-align: right;"> <a href="{$url}/Author/Search?{$searchcomps}&amp;lc=authseeall">see all ({$narrowcount})</a></div>
         {/if}
      </div>
      {/if $narrow}
      <!-- End Narrow Options -->

      <!-- Spelling suggestion -->
      {if $newPhrase}
      <p class="correction">{translate text='Did you mean'} <a href="{$url}/Search/{$action}?lookfor={$newPhrase}&amp;type={$type}">{$newPhrase}</a>?</p>
      {/if}


      <!-- Listing Options -->
      <div class="yui-ge resulthead">
        <div class="yui-u first">
        {if $recordCount}
          {translate text="Showing"}
          <b>{$recordStart}</b> - <b>{$recordEnd}</b>
          {translate text='of'} <b>{$recordCount}</b>
          {translate text='Results for'} <b>{$searchterms}</b>
        {/if}
        </div>

        <div class="yui-u toggle" style="width: auto">
          {translate text='Sort'}&nbsp;<select name="sort" onChange="document.location.href='/Search/Home?{$searchcomps}&amp;sort=' + this.options[this.selectedIndex].value;">
            <option value="">Relevance</option>
            <option value="year"{if $sort == "year"} selected{/if}>Date (newest first)</option>
            <option value="yearup"{if $sort == "yearup"} selected{/if}>Date (oldest first)</option>            
            <!-- <option value="callnumber"{if $sort == "callnumber"} selected{/if}>{translate text='Call Number'}</option> -->
            <option value="author"{if $sort == "author"} selected{/if}>{translate text='Author'}</option>
<!--            <option value="title"{if $sort == "title"} selected{/if}>{translate text='Title'}</option>  -->
          </select>
        </div>

      </div>
      <!-- End Listing Options -->
      {assign var=pageLinks value=$pager->getLinks()}
      <div class="pagination">{$pageLinks.all}</div>

      {if $subpage}
        {include file=$subpage}
      {else}
        {$pageContent}
      {/if}

      <!-- {assign var=pageLinks value=$pager->getLinks()} -->
      <div class="pagination">{$pageLinks.all}</div>
      <div class="searchtools">

        <a style="margin-left: 2em;" href="/Search/Home?{$searchcomps}&amp;view=atom" id="Feed">{translate text='Get Feed'}</a>

        <a href="{$url}/Search/Email" class="mail" onClick="getLightbox('Search', 'Email', null, null, '{translate text="Email this"}'); return false;">{translate text='Email this Search'}</a>
      </div>
    </div>
    <!-- End Main Listing -->
  </div>

  <!-- List of current filters with delete button -->
  <div id="listleftcol" class="yui-b"><div class="box submenu narrow">
    <h4>{translate text='Narrow Search'}</h4>

      <ul>
        {foreach from=$currentFacets item=facet}
          <li><a href="{$url}/Search/{$action}?{$facet.removalURL}"><img  src="{$path}/images/silk/delete.png" alt="Delete"></a>{$facet.indexDisplay} : {$facet.valueDisplay}</li>
        {/foreach}
      </ul>
      
      <!-- <dl class="narrowList navmenu" id="narrowList">
      </dl> -->
      <div class="narrowList navmenu" id="narrowList">
      <div id="narrowLoading">
        <img src="{$path}/images/loading.gif" alt="Loading"><br>
        Loading Narrow Options ...
      </div>
     </div>
      <script language="JavaScript" type="text/javascript">
         var url = '{$url}/Search/Snippet?{$searchcomps}';
{literal}
         jq('#narrowList').load(url, {'method' : 'getFacetCounts'});
{/literal}
       </script>
    </div>
  </div>
  <!-- End Narrow Search Options -->

</div>
