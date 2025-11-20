<hathi-cookie-consent-banner></hathi-cookie-consent-banner>
<div id="skiplinks" class="visually-hidden-focusable" role="complementary" aria-label="Skip links">
  <ul>
    <li>
      <a href="#section">Skip to main</a>
    </li>
  </ul>
</div>
<div id="root">
<div role="status" aria-atomic="true" aria-live="polite" class="visually-hidden"></div>
  <hathi-website-header
    data-prop-search-state="none"> 
  </hathi-website-header>
  <hathi-alert-banner></hathi-alert-banner>
  <main class="main-container" id="main">
    <hathi-advanced-search-form
      data-prop-language-data="{$languageList|@json_encode|escape}" 
       data-prop-format-data='{$formatList|@json_encode|escape}'
       data-prop-location-data='{$locationsList|@json_encode|escape}'
    ></hathi-advanced-search-form>
  </main>

  {include file="footer.tpl"}
</div>
