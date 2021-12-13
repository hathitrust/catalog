<ul id="skiplinks" role="complementary" aria-label="Skip links">
  <li>
    <a href="#section">Skip to main</a>
  </li>
</ul>
<div id="root">
  {include file="header.tpl"}

  <main class="main-container" id="main">
    {include file="search_form.tpl"}

    <div class="container container-medium flex-container flex-container-expanded container-boxed" style="margin-top: 1.75rem; margin-bottom: 1.75rem">

      {include file="$module/list.sidebar.tpl"}

      <section class="section-container" id="section" data-record-count="{$recordCount}">
        <h1 class="listcs-intro" style="margin-left: 0; font-weight: normal; margin-bottom: 1rem">
            Search results from the HathiTrust biblographic catalog.
        </h1>

        <div class="results-container">
          <div class="results-summary-container">
            <h2 class="results-summary"><b>No results</b> matched your search.</h2>
          </div>

          <div class="results-container-inner">

            {if $newPhrase}
            <div class="alert alert-block alert-info"><p class="correction">{translate text='Did you mean'} <a href="{$url}/Search/{$action|escape:"url"}?lookfor={$newPhrase|escape:"url"}&amp;type={$type}{$filterListStr}">{$newPhrase}</a>?</p></div>
            {/if}

            {* <div class="alert alert-error alert-info"><p class="error">Your
              <strong>{if $check_ft_checkbox}Full View only{/if}</strong>
               search &mdash; 
               <strong>{$searchterms|escape}</strong>
               &mdash; did not match any resources.</p>
             </div> *}

            <!-- <p>You may want to try to revise your search phrase by removing some words.</p> -->
            <h3>Suggestions</h3>
            <ul class="bullets">
              <li>Revise your search term</li>
              {if $check_ft_checkbox}
              <li>Filter by <strong>All Items</strong></li>
              {/if}
              <li>Remove some filters</li>
            </ul>

          </div>

        </div>
      </section>
    </div>

  </main>

  {include file="footer.tpl"}
</div>
