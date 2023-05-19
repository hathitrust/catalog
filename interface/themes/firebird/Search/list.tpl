{*
FIREBIRD TODOS:

1. when I set the results-toolbar currentSortOption prompt to "{$sort}", the initial dropdown is blank but works once clicked
  a. thanks to var_dump, the inital value of $sort is NULL but the value of the "Relevance" option in the sortOptions array is false
  b. in HTML in console, value="false"
  c. value needs to be false in order to send proper option to URL during on change/onSubmit
  d. tried many things but I must not understand smarty templates well enough to get the syntax right;
     either need to figure out how to set initial $sort variable to false (instead of null)
     or assign a variable to handle the conditional:
        if ($sort == '') {currentSortOption="false"} else currentSortOption={$sort}
*}
<div id="skiplinks" class="visually-hidden-focusable" role="complementary" aria-label="Skip links">
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
    {* {include file="search_form.tpl"} *}

    <div class="twocol mt-1">

      {include file="Search/list.sidebar.tpl"}

      <section class="twocol-main" id="section" data-record-count="{$recordCount}">
        <h1 class="listcs-intro">
            Search results
        </h1>
        <div class="results-container">
          <div class="results-summary-container">
             {if $recordCount}
            <hathi-results-toolbar
              data-prop-first-record-number='{$recordStart}'
              data-prop-last-record-number='{$recordEnd}'
              data-prop-total-records='{$recordCount|number_format:null:".":","}'
              data-prop-target='catalog'
              {* force 'Relevance' to appear in dropdown as default *}
              {if $sort == ''}
                data-prop-current-sort-option='false'
              {/if}
                data-prop-current-sort-option='{$sort}'
              ></hathi-results-toolbar>
             {/if}
            
          </div>

         
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
           
            <hathi-results-pagination
            data-prop-max-pages='{$numPages}'
            data-prop-next-href='{$pageLinks.linkTagsRaw.next.url}'
            data-prop-prev-href='{$pageLinks.linkTagsRaw.prev.url}'
            data-prop-value = '{$pager->_currentPage}'
            ></hathi-results-pagination>

          {/if}
        </div>
      </section>
    </div>

  </main>

  {include file="footer.tpl"}
</div>
