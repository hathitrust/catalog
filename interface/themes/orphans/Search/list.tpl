<script language="JavaScript" type="text/javascript" src="/services/Search/ajax.js"></script>
<script language="JavaScript" type="text/javascript" src="/js/googleLinks.js"></script>


{literal}
<script language="JavaScript" type="text/javascript">
  function resetSort() {
    alert(document.location.href)
  }
</script>
{/literal}

<!-- Main Listing -->
<div id="bd">
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
          <a href="/Search/Home?{$narrowItem.authurl}">{translate text=$narrowItem.name}</a> ({$narrowItem.num})<br>
        {/foreach}
        </div>
	      {if $narrowcount > $smarty.foreach.narrowLoop.iteration}
        <div style="clear:both; text-align: right;"> <a href="/Author/Search?{$searchcomps}">see all ({$narrowcount})</a></div>
      {/if}
      </div>
      {/if $narrow}
      <!-- End Narrow Options -->
 {*
      <!-- Spelling suggestion -->
      {if $newPhrase}
      <p class="correction">{translate text='Did you mean'} <a href="/Search/{$action}?lookfor={$newPhrase}&amp;type={$type}">{$newPhrase}</a>?</p>
      {/if}

       <div class="searchtools">
        <!-- <a href="/Search/{$action}?lookfor={$lookfor|escape}&amp;type={$type}&amp;view=rss" class="feed">{translate text='Get RSS Feed'}</a> -->

        <!-- fixme:suz RSS doesn't work so well
        <a href="" id="RSSFeed">{translate text='Get RSS Feed'}</a>
        <script language="JavaScript" type="text/javascript">
          loc = window.location.href;
          loc.replace(/checkspelling=true/, '');
          loc = loc + '&view=rss';
          jq('#RSSFeed').attr('href', loc)
        </script>
        -->
        <!-- <a class="feed" href="/Search/SearchExport?{$searchcomps|escape:'html'}&amp;method=atom" id="Feed">{translate text='Get Feed'}</a> -->

      <a href="#" id="emailSearch" class="mail" onClick="pageTracker._trackEvent('resultsActions', 'click', 'Email this Search top');">{translate text='Email this Search'}</a>
    </div>
*}
      <!-- Listing Options -->
      <div class="yui-ge resulthead">
        <div class="yui-u first">
        {if $recordCount}
          {translate text="Showing"}
          <span class="strong">{$recordStart} - {$recordEnd}</span>
          {translate text='of'} <span class="strong">{$recordCount}</span>
{*          {if ($searchterms == '(no keywords)')}
            (browsing all records)
          {else}
            {translate text='Results for'} <span class="strong">{$searchterms}</span>
          {/if}
*}
        {/if}
        </div>

        <div class="yui-u toggle" style="width: auto">
          <label for="sortOption">{translate text='Sort'}</label>
          <select id="sortOption" name="sort" onChange="document.location.href='/Search/Home?{$searchcomps|escape:'html'}&amp;sort=' + this.options[this.selectedIndex].value;">
            <option value="">Relevance</option>
            <option value="year"{if $sort == "year"} selected{/if}>Date (newest first)</option>
            <option value="yearup"{if $sort == "yearup"} selected{/if}>Date (oldest first)</option>
           <option value="author"{if $sort == "author"} selected{/if}>{translate text='Author'}</option>
            <option value="title"{if $sort == "title"} selected{/if}>{translate text='Title'}</option>
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

    </div>
    <!-- End Main Listing -->
  </div>

  <!-- Narrow Search Options -->
  <div id="listleftcol" class="yui-b"><div class="box submenu narrow">
    <h3>{translate text='Narrow Search'}</h3>
      <ul class="filters">
        {foreach from=$currentFacets item=facet}
          <li>
            <a href="/Search/{$action}?{$facet.removalURL}"><img  src="/images/silk/delete.png" alt="Delete"></a>{$facet.indexDisplay} : {translate text=$facet.valueDisplay}</li>
        {/foreach}
      </ul>
      <div class="narrowList navmenu" id="narrowList">
        {include file="Search/facet_snippet.tpl"}
      </div>

    </div>
  </div>
  <!-- End Narrow Search Options -->
</div> <!-- ??? -->
</div>
