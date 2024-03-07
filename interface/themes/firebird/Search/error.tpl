  <hathi-cookie-consent-banner></hathi-cookie-consent-banner>
<ul id="skiplinks" class="visually-hidden-focusable" role="complementary" aria-label="Skip links">
  <li>
    <a href="#section">Skip to main</a>
  </li>
</ul>
<div id="root">
  {include file="header.tpl"}

  <main class="main" id="main">
    {* {include file="search_form.tpl"} *}

    <div class="twocol mt-1">

      {include file="Search/list.sidebar.tpl"}

      <section class="twocol-main" id="section" data-record-count="{$recordCount}">
        <div class="mainplain w-auto position-relative">
        <h1>
            Search Results
        </h1>

        <div class="results-container">
          <div class="alert alert-info alert-block">
            <strong>No results</strong> matched your search.
          </div>

          <div class="results-container-inner">

            {if $newPhrase}
            <div class="alert alert-block alert-info"><p class="correction">{translate text='Did you mean'} <a href="{$url}/Search/{$action|escape:"url"}?lookfor={$newPhrase|escape:"url"}&amp;type={$type}{$filterListStr}">{$newPhrase}</a>?</p></div>
            {/if}

            <h2 class="fs-3">Suggestions</h2>
            <ul class="bullets">
              <li>Revise your search term</li>
              {if $check_ft_checkbox}
              <li>Filter by <strong>All Items</strong></li>
              {/if}
              <li>Remove some filters</li>
            </ul>

          </div>

        </div>
        </div>
      </section>
    </div>

  </main>

  {include file="footer.tpl"}
</div>
