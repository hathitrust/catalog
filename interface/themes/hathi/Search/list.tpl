{* <script language="JavaScript" type="text/javascript" src="{$path}/services/Search/ajax.js"></script> *}
<script language="JavaScript" type="text/javascript" src="{$path}/js/googleLinks.js"></script>

<!-- Main Listing -->
<div id="bd" data-recordCount="{$recordCount}">
  <!-- Narrow Search Options -->
  <div id="listleftcol" class="yui-b"><div class="box submenu narrow">
  {if $currentFacets}
    <div id="applied_filters">
      <h2>{translate text='Results refined by:'}</h2>
        <ul class="filters">
          {foreach from=$currentFacets item=facet}
            {assign var=rurl value=$facet.removalURL|regex_replace:"/&/":"&amp;"}
            <li>
              <a href="/Search/{$action}?{$rurl}"><img  class="facetbutton" src="/images/silk/cancel.png" alt="Delete"></a>{$facet.indexDisplay} : {translate text=$facet.valueDisplay}</li>
          {/foreach}
        </ul>
    </div>
  {/if}

      <div class="narrowList navmenu" id="narrowList">
      <h2>{translate text='Refine Results'}</h2>
        {include file="Search/facet_snippet.tpl"}
      </div>

    </div>
  </div>
  <!-- End Narrow Search Options -->
  <div class="yui-main content">
    
    <div class="yui-b first contentbox">

      <!-- Narrow Options -->
      {if $narrow}
      <div class="yui-g resulthead" style="border: solid 1px #999999; background-color: #FFFFEE;">
        <div class="yui-u first">
        {foreach from=$narrow item=narrowItem name="narrowLoop"}
          {if $smarty.foreach.narrowLoop.iteration == 6}
            </div>
            <div class="yui-u">
          {/if}
          <a href="{$url}/Search/Home?{$narrowItem.authurl}">{translate text=$narrowItem.name}</a> ({$narrowItem.num})<br>
        {/foreach}
        </div>
        {if $narrowcount > $smarty.foreach.narrowLoop.iteration}
        <div style="clear:both; text-align: right;"> <a href="{$url}/Author/Search?{$searchcomps|escape:"url"}">see all ({$narrowcount})</a></div>
      {/if}
      </div>
      {/if $narrow}
      <!-- End Narrow Options -->

      <!-- Spelling suggestion -->
      {if $newPhrase}
      <p class="correction">{translate text='Did you mean'} <a href="{$url}/Search/{$action}?lookfor={$newPhrase}&amp;type={$type}">{$newPhrase}</a>?</p>
      {/if}

      <!-- Listing Options -->
      <h2 class="hidden">Search Results</h2>
      <div class="yui-ge resulthead">
        <div class="yui-u first">
        {if $recordCount}
          {translate text="Showing"}
          <span class="strong">{$recordStart} - {$recordEnd}</span>
          {translate text='of'} <span class="strong">{$recordCount}</span>
          {translate text='Results for'} <span class="strong">{$searchterms|escape}</span>
        {/if}
        </div>


      </div>


      <!-- Viewability Tabs -->
      <div class="viewability tabs" id="viewability-tabs">
        <ul>
          <li class="view-all {if !$is_fullview}active{/if}">
            <a href="{$allitems_url}">All items
            <span dir="ltr">(<span id="allitems_count">{$allitems_count|number_format:null:".":","}</span>)</span></a> 
          </li>
          <li class="view-full {if $is_fullview}active{/if}">
            {if $fullview_count > 0}
              <a href="{$fullview_url}">
            {/if}
            Only full view
            <span dir="ltr">(<span id="fullview_count">{$fullview_count|number_format:null:".":","}</span>)</span>
            {if $fullview_count > 0}
              </a>
            {/if}
          </li>
          
        </ul>
      </div>


      <!-- End Listing Options -->
      {assign var=pageLinks value=$pager->getLinks()}
      <div class="options PageInfo toolbar">

        <div class="sort">
          <label for="sortOption" class="hidden">{translate text='Sort'}</label>
          <select id="sortOption" name="sort" onChange="document.location.href='{$fullPath_esc|remove_url_param:"sort"}&amp;sort=' + this.options[this.selectedIndex].value;" style="margin-left: 8px">
            <option value="">Sort by Relevance</option>
            <option value="year"{if $sort == "year"} selected{/if}>Sort by Date (newest first)</option>
            <option value="yearup"{if $sort == "yearup"} selected{/if}>Sort by Date (oldest first)</option>
            <option value="author"{if $sort == "author"} selected{/if}>Sort by {translate text='Author'}</option>
            <option value="title"{if $sort == "title"} selected{/if}>Sort by {translate text='Title'}</option>
          </select>
	  </div>




<div class="toolbar" style="vertical-align: baseline">
 <div class="sort" style="display: inline-block">
  <select style="" id="pagesizeOption" name="pagesize" onChange="document.location.href='{$fullPath_esc|remove_url_param:"pagesize"|remove_url_param:"page"}&amp;page=1&amp;pagesize=' + this.options[this.selectedIndex].value">
   <option value="20" {if $pagesize == "20"}selected{/if}>20 per page</option>
    <option value="50" {if $pagesize == "50"}selected{/if}>50 per page</option>
   <option value="100" {if $pagesize == "100"}selected{/if}>100 per page</option>
  </select>
 </div>

<div class="pagination"  style="margin-top: 0.5em; display: inline-block">
{$pageLinks.all}
</div>
</div>


</div>

      {if $subpage}
        {include file=$subpage}
      {else}
        {$pageContent}
      {/if}

      <!-- {assign var=pageLinks value=$pager->getLinks()} -->
      <div class="options clearfix">
        <div class="pagination clearfix">{$pageLinks.all}</div>
      </div>
      <div class="searchtools">
      </div>
    </div>
    <!-- End Main Listing -->
  </div>


</div> <!-- ??? -->
