<hathi-cookie-consent-banner></hathi-cookie-consent-banner>
<div id="skiplinks" class="visually-hidden-focusable" role="complementary" aria-label="Skip links">
  <ul>
    <li>
      <a href="#section">Skip to main content</a>
    </li>
  </ul>
</div>
<div id="root">
  {include file="header.tpl"}

  <main class="main main-container error" id="main">

   <div class="container error-wrapper">
          
          <section id="section" class="d-flex flex-column">
            <div class="d-flex flex-column message-wrapper">
              <div class="d-flex flex-column-reverse gap-2">
                <h1>Page not found</h1>
                <h2 class="text-uppercase error-code">Error: 404</h2>
              </div>

                <p class="error-message">Sorry, we can't find the page you're looking for.</p>
              </div>

              <div class="d-flex flex-column gap-3 help-links">
              <p>Here are a few links that may be helpful:</p>
              <ul class="m-0 p-0 list-unstyled d-flex gap-3">
                <li><a href="https://www.hathitrust.org">Home</a></li>
                <li><a href="https://babel.hathitrust.org/cgi/ls?a=page&page=advanced">Advanced Search</a></li>
                <li><a href="https://hathitrust.atlassian.net/servicedesk/customer/portals">Help Center</a></li>
                <li>
                  <a href="#" data-hathi-trigger="hathi-feedback-form-modal" id="feedback-form">Report a Problem</a>
                </li>
              </ul>
            </div>
            <hathi-feedback-form-modal data-prop-form="error" data-prop-is-open="false"></hathi-feedback-form-modal>
          </section>
        </div>
  </main>

  {include file="footer.tpl"}
</div>
