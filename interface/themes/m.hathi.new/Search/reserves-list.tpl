<script language="JavaScript" type="text/javascript" src="/services/Search/ajax.js"></script>

<!-- Main Listing -->
<div id="bd">
  <div class="yui-main content">
    <div class="yui-b first contentbox">

      <!-- Listing Options -->
      <div class="yui-ge resulthead">
        <div class="yui-u first">
        {if $recordCount}
          {translate text="Showing"}
          <b>{$recordStart}</b> - <b>{$recordEnd}</b>
          {translate text='of'} <b>{$recordCount}</b>
          {translate text='Results for'} <b>{$lookfor}</b>
        {/if}
        </div>

        <div class="yui-u toggle">
          {translate text='Sort'}
          <select name="sort" onChange="document.location.href='{$fullPath}&sort=' + this.options[this.selectedIndex].value;">
            <option value="">Relevance</option>
            <option value="year"{if $sort == "year"} selected{/if}>{translate text='Date'}</option>
            <option value="callnumber"{if $sort == "callnumber"} selected{/if}>{translate text='Call Number'}</option>
            <option value="author"{if $sort == "author"} selected{/if}>{translate text='Author'}</option>
            <option value="title"{if $sort == "title"} selected{/if}>{translate text='Title'}</option>
          </select>
        </div>

      </div>
      <!-- End Listing Options -->

      {if $subpage}
        {include file=$subpage}
      {else}
        {$pageContent}
      {/if}

      {assign var=pageLinks value=$pager->getLinks()}
      <div class="pagination">{$pageLinks.all}</div>
      <div class="searchtools">
        <strong>{translate text='Search Tools'}:</strong>
        <a href="/Search/{$action}?lookfor={$lookfor|escape}&type={$type}&view=rss" class="feed">{translate text='Get RSS Feed'}</a>
        <a href="/Search/Email" class="mail" onClick="getLightbox('Search', 'Email', null, null, '{translate text="Email this"}'); return false;">{translate text='Email this Search'}</a>
      </div>
    </div>
    <!-- End Main Listing -->
  </div>


</div>