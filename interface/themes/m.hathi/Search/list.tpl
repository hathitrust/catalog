<script language="JavaScript" type="text/javascript" src="{$path}/services/Search/ajax.js"></script>
<script language="JavaScript" type="text/javascript" src="{$path}/js/googleLinks.js"></script>

<!-- Main Listing -->
<div id="mResultList">
        
  <!-- Spelling suggestion -->
  {if $newPhrase}
  <p>{translate text='Did you mean'} <a href="{$url}/Search/{$action}?lookfor={$newPhrase}&amp;type={$type}">{$newPhrase}</a>?</p>
  {/if}
  
  <!-- Email search not as useful here
  <div class="searchtools">
    <a href="#" id="emailSearch" class="mail">{translate text='Email this Search'}</a>
  </div>
  -->
  
  {include file="searchbox.tpl"}
  
  <!-- Listing Options -->
  
  <div class="resultsOptions">
  {if $recordCount}
    {translate text="Showing"}
    <span class="strong">{$recordStart} - {$recordEnd}</span>
    {translate text='of'} <span class="strong">{$recordCount}</span>
    <!-- {translate text='Results for'} <span class="strong">{$lookfor}</span>-->
  {/if}
  </div>
  <div>
    <div class="resultsOptions">
      <label for="sortOption">{translate text='Sort'}</label>
      <select id="sortOption" name="sort" onChange="document.location.href='{$fullPath}&amp;sort=' + this.options[this.selectedIndex].value;">
        <option value="">Relevance</option>
        <option value="year"{if $sort == "year"} selected{/if}>Date (newest first)</option>
        <option value="yearup"{if $sort == "yearup"} selected{/if}>Date (oldest first)</option>            
        <option value="author"{if $sort == "author"} selected{/if}>{translate text='Author'}</option>
        <!--<option value="title"{if $sort == "title"} selected{/if}>{translate text='Title'}</option>-->
      </select>
    </div>
    
  </div>
  <!-- End Listing Options -->
    
  <!-- Populates via list-list -->
	{if $subpage}
    {include file=$subpage}
  {else}
    {$pageContent}
  {/if}
  <!-- Pagination -->
  <div class="resultsOptions">
    {assign var=pageLinks value=$pager->getLinks()}
    <div class="pagination">{$pageLinks.all}</div>
  </div>

  <!-- Narrow Options (fixme: it can't find javascript, redo styles, etc. also need to put in other narrow options section)
  <div id="listleftcol" class="yui-b"><div class="box submenu narrow">
    <h3>{translate text='Narrow Search'}</h3>
      <ul class="filters">
        {foreach from=$currentFacets item=facet}
          <li>
            <a href="{$url}/Search/{$action}?{$facet.removalURL}"><img  src="{$path}/images/silk/delete.png" alt="Delete"></a>{$facet.indexDisplay} : {$facet.value}</li>
        {/foreach}
      </ul>        
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
-->
 <!-- End Main Listing -->

</div> 

