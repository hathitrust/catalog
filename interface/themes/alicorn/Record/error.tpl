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

      <section class="section-container" id="section" data-record-count="{$recordCount}">
        <h1 class="listcs-intro" style="margin-left: 0; font-weight: normal; margin-bottom: 1rem">
            Search results from the HathiTrust biblographic catalog.
        </h1>

        <div class="results-container">
          <div class="results-summary-container">
            <h2 class="results-summary">No catalog record was found with that identifier. Please check your url or try searching HathiTrust.</h2>
          </div>

          <div class="results-container-inner">
          </div>

        </div>
      </section>
    </div>

  </main>

  {include file="footer.tpl"}
</div>
