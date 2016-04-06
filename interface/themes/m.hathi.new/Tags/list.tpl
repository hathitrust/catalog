<script language="JavaScript" type="text/javascript" src="/services/Search/ajax.js"></script>
<script language="JavaScript" type="text/javascript" src="/js/googleLinks.js"></script>

{literal}
<script type="text/javascript" charset="utf-8">

</script>
{/literal}

<!-- Main Listing -->
<div id="bd">
  <div class="yui-main content">
    <div class="yui-b first contentbox">


      <!-- Listing Options -->
      <div class="yui-ge resulthead">
        <div class="yui-u first">
        <h2>{$tid->tagdisplay}</h2>
        {if $recordCount}
          {translate text="Showing"}
          <b>{$recordStart}</b> - <b><span id="currentEndOfPageCount">{$recordEnd}</span></b>
          {translate text='of'} <b><span class="tempcount">{$recordCount}</span></b>
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

      {if !$selectedItemsPage}
        {include file="tempbox.tpl"}
      {/if}

      <!-- End Listing Options -->
      {assign var=pageLinks value=$pager->getLinks()}

      {if strlen($pageLinks.all)}
      <div class="pagination">{$pageLinks.all}</div>
      {/if}

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
  <div id="listleftcol" class="yui-b"><div class="box submenu narrow">
    {if $selectedItemsPage}
    {include file="Tags/tempbox_dialogs.tpl"}
    <div id="selectedMenu">
    {include file="Tags/templinks.tpl"}
    </div>
    {/if}

  </div>
  <!-- End Narrow Search Options -->



<div id="emailSearch" style="background-color: #fff; display: none">
  <div style=" padding:3em; ">
    <p><strong>Email this search</strong></p>
    <p>By filling out the form below, you can email a link to this search (and the first few results) to yourself or someone else.</p>
    <p>Note that both the "To" and "From" addresses must be complete (e.g., user@umich.edu).</p>

    <form type="GET" action="/Search/SearchExport">
      <input type="hidden" name="method" value="emailRecords">
      <input type="hidden" name="tag" value="{$uuid}">
      <table style="margin:0; padding: 0; width: auto;">
        <tbody>
          <tr>
            <td>To:</td><td><input name="to" type="text" size="20"></td>
          </tr>
          <tr>
            <td>From:</td><td><input name="from" type="text" size="20"></td>
          </tr>
          <tr>
            <td>Message:</td><td><textarea  name="message"></textarea></td></tr>
          </tr>
        </tbody>
        </table>
        <input type="button" value="Send email" onclick="emailSearch(this); return false;">
    </form>

    <div class="erError"></div>

  </div>
</div>
