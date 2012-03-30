<script language="JavaScript" type="text/javascript" src="{$path}/services/Search/ajax.js"></script>
<script language="JavaScript" type="text/javascript" src="{$path}/js/googleLinks.js"></script>


<!-- Main Listing -->
<div id="bd">
  <div id="yui-main" class="content">
    <div class="yui-b first contentbox">


      <!-- Listing Options -->
      <div class="yui-ge resulthead">
        <div class="yui-u first">
        <h2>{$tid->tagdisplay}</h2>
        {if $recordCount}
          {translate text="Showing"}
          <b>{$recordStart}</b> - <b><span id="currentEndOfPageCount">{$recordEnd}</span></b>
          {translate text='of'} <b><span id="favcount">{$recordCount}</span></b>
          items {$tid->searchDescription}
        {/if}
        </div>

        <div class="yui-u toggle" style="width: auto">
          {translate text='Sort'}&nbsp;<select name="sort" onChange="document.location.href='{$urlbase}sort=' + this.options[this.selectedIndex].value;">
            <option value="">Relevance</option>
            <option value="year"{if $sort == "year"} selected{/if}>Date (newest first)</option>
            <option value="yearup"{if $sort == "yearup"} selected{/if}>Date (oldest first)</option>            
            <option value="author"{if $sort == "author"} selected{/if}>{translate text='Author'}</option>
          </select>
        </div>
      </div>

        {include file="tempbox.tpl"}      
    
      <!-- End Listing Options -->
      {assign var=pageLinks value=$pager->getLinks()}
      <div class="pagination">{$pageLinks.all}</div>

      {if $subpage}
        {include file=$subpage}
      {else}
        {$pageContent}
      {/if}

      <div class="pagination">{$pageLinks.all}</div>
      
    </div>
    <!-- End Main Listing -->
  </div>

  <!-- List of current filters with delete button -->
 {include file="MyResearch/menu.tpl"}

  <!-- End Narrow Search Options -->


