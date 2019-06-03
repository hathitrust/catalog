<div id="skiplinks" role="complementary" aria-label="Skip links">
  <ul>
    <li>
      <a href="#section">Skip to search results</a>
    </li>
    <li>
      <a href="#sidebar">Skip to results filters</a>
    </li>
  </ul>
</div>
<div id="root">
  {include file="header.tpl"}

  <main class="main-container" id="main">
    {include file="search_form.tpl"}

    <div class="container container-medium flex-container flex-container-expanded container-boxed" style="margin-top: 1.75rem; margin-bottom: 1.75rem">

      {include file="$module/list.sidebar.tpl"}

      <section class="section-container" id="section" data-record-count="{$recordCount}">
        <div class="results-container">
          <div class="results-summary-container">
            <h2 class="results-summary">
              {if $recordCount}
              {$recordStart} - {$recordEnd} of {$recordCount|number_format:null:".":","} Catalog results
              {/if}
            </h2>
            <div class="results-actions">
              <label for="sort-option">Sort by</label>
              <select id="sort-option" name="sort" data-toggle="select" data-href="{$fullPath_esc|remove_url_param:"sort"}&amp;sort=">
                <option value=""{if $sort == ""} selected{/if}>Relevance</option>
                <option value="year"{if $sort == "year"} selected{/if}>Date (newest first)</option>
                <option value="yearup"{if $sort == "yearup"} selected{/if}>Date (oldest first)</option>
                <option value="author"{if $sort == "author"} selected{/if}>{translate text='Author'}</option>
                <option value="title"{if $sort == "title"} selected{/if}>{translate text='Title'}</option>
              </select>

              <label for="pagesize-option">Items per page</label>
              <select id="pagesize-option" name="pagesize" data-toggle="select" data-href="{$fullPath_esc|remove_url_param:"pagesize"|remove_url_param:"page"}&amp;page=1&amp;pagesize=">
                <option value="20" {if $pagesize == "20"}selected{/if}>20</option>
                <option value="50" {if $pagesize == "50"}selected{/if}>50</option>
                <option value="100" {if $pagesize == "100"}selected{/if}>100</option>
              </select>
            </div>
          </div>

          {* <pre>{$ss->asFullURL()}</pre>
          <pre>{$ss->asWildcardURL()}</pre>
          <pre>{$ss->searchURLComponents()|@var_dump}</pre> *}

          <!-- results list -->
          {if $subpage}
          {include file="$subpage"}
          {else}
          <pre>HAVE PAGE CONTENT??</pre>
          {/if}

          <!-- pagination -->
          {assign var=numPages value=$pager->numPages()}
          {if $numPages gt 1}
            {assign var=pageLinks value=$pager->getLinks()}
            {assign var=pageLinksArray value=$pager->getPageLinksArray()}
          <nav class="pagination-container" aria-label="Pagination">
            <div class="page-back-link">
              {if $pageLinks.back}{$pageLinks.back}{/if}
            </div>

            <ul>
              {foreach from=$pageLinksArray item="page" name="pageLoop"}
              <li>{$page}</li>
              {/foreach}
            </ul>

            <div class="page-advance-link">
              {if $pageLinks.next}{$pageLinks.next}{/if}
            </div>
          </nav>
          {/if}
        </div>
      </section>
    </div>

  </main>

  {include file="footer.tpl"}
</div>
