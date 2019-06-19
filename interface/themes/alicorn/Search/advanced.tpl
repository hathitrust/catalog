<div id="skiplinks" role="complementary" aria-label="Skip links">
  <ul>
    <li>
      <a href="#section">Skip to main</a>
    </li>
  </ul>
</div>
<div id="root">
  {include file="header.tpl" logo=true}


  <main class="main-container" id="main">
    <div class="container container-narrow flex-container flex-container-expanded container-boxed">
      <section id="section" class="section-container" tabindex="-1">

        <form method="GET" action="{$url}/Search/Home" name="searchForm" class="advanced-search-form" data-ignore-empty-query="true">

          <h2>{translate text='Advanced Catalog Search'}</h2>
          <p>Search information <em>about</em> the item (<a target="_blank" href="http://www.hathitrust.org/help_digital_library#SearchTips">Search Tips</a>).</p>
          <p>Prefer to search <em>within</em> the item in an <a href="//babel.hathitrust.org/cgi/ls?a=page&page=advanced">Advanced Full-text search</a>?</p>

          <h3 class="offscreen">Search by field</h3>

          {include file="Search/advanced_search_field.tpl" index='1' type=$type1 lookfor=$lookfor1}
          {include file="Search/advanced_search_field.tpl" index='2' type=$type2 lookfor=$lookfor2 bool=$bool1}
          {include file="Search/advanced_search_field.tpl" index='3' type=$type3 lookfor=$lookfor3 bool=$bool2}
          {include file="Search/advanced_search_field.tpl" index='4' type=$type4 lookfor=$lookfor4 bool=$bool3}

          <button kind="primary" class="button btn btn-primary"><i class="icomoon icomoon-search" aria-hidden="true"></i> Advanced Search</button>

          <h3>Additional search options</h3>
          <div class="advanced-filters-inner-container">
            <div class="advanced-search-filter-container">
              <h4 class="advanced-filter-label-text">View Options</h4>
              <div class="advanced-filter-inner-container">
                <input type="hidden" name="setft" value="true" />
                <input type="checkbox" name="ft" value="ft" id="filter-full-view-only" {if $ft eq 'ft'}checked="checked"{/if} />
                <label for="filter-full-view-only">Full view only</label>
              </div>
            </div>

            <div class="advanced-search-filter-container">
              <h4 class="advanced-filter-label-text">Date of Publication</h4>
              <div class="advanced-filter-inner-container">
                <div class="alert alert-error alert-block" role="alert" aria-atomic="true"></div>
                <div class="date-range-input">
                  <fieldset class="no-margin choice-container">
                    <legend class="offscreen">Select the type of date range to search on</legend>
                    <div>
                      <input type="radio" id="date-range-input-radio-0" name="yop" value="before" {if $dateRangeInput eq 'before'}checked="checked"{/if} />
                      <label class="multiple-choice" for="date-range-input-radio-0"><span>Before</span></label>
                    </div>
                    <div>
                      <input type="radio" id="date-range-input-radio-1" name="yop" value="after" {if $dateRangeInput eq 'after' or ! $dateRangeInput}checked="checked"{/if} />
                      <label class="multiple-choice" for="date-range-input-radio-1"><span>After</span></label>
                    </div>
                    <div>
                      <input type="radio" id="date-range-input-radio-2" name="yop" value="between" {if $dateRangeInput eq 'between'}checked="checked"{/if} />
                      <label class="multiple-choice" for="date-range-input-radio-2"><span>Between</span></label>
                    </div>
                    <div>
                      <input type="radio" id="date-range-input-radio-3" name="yop" value="in" {if $dateRangeInput eq 'in'}checked="checked"{/if} />
                      <label class="multiple-choice" for="date-range-input-radio-3"><span>Only during</span></label>
                    </div>
                  </fieldset>
                  <div class="date-range-container">
                    <input name="fqrange-start-publishDateTrie-1" data-xxparam="fqor-publishDateTrie[]" class="date-range-input-text date-range--between date-range--after" type="text" aria-label="Start date" placeholder="Start date" value="{$startDate}">
                    <input name="fqrange-end-publishDateTrie-1" data-xxparam="fqor-publishDateTrie[]" class="date-range-input-text date-range--between date-range--before" type="text" aria-label="End date" placeholder="End date" value="{$endDate}">
                    <input name="fqor-publishDateTrie[]" data-xxparam="fqor-publishDateTrie[]" class="date-range-input-text date-range--in" type="text" aria-label="Date" placeholder="Date" value="{$date}">
                  </div>
                </div>
              </div>
            </div>

            <div class="advanced-search-filter-container">
              <h4 class="advanced-filter-label-text">Language</h4>
              <div class="advanced-filter-inner-container">
                <div class="multiselect">
                  <p>Select one or more checkboxes to narrow your results to items that match all of your language selections.</p>
                  <input name=".language-filter" type="text" class="multiselect-search" aria-label="Filter options" aria-describedby="language" placeholder="Filter" value="" />
                  <p id="language" class="offscreen">Below this edit box is a list of check boxes that allow you to filter down your options. As you type in this edit box, the list of check boxes is updated to reflect only those that match the query typed in this box.</p>
                  <fieldset class="multiselect-options">
                    <ul class="multiselect-options-list">
                      {foreach from=$languageList item="language" name=options}
                        {if $language}
                      <li class="multiselect-options-list-item">
                        <input type="checkbox" name="fqor-language[]" id="language-{$smarty.foreach.options.index}" value="{$language}" {if in_array($language, $fqor_language)}checked="checked" {/if}/>
                        <label for="language-{$smarty.foreach.options.index}">
                          <span class="filter-name">{$language|escape:"html"}</span>
                        </label>
                      </li>
                        {/if}
                      {/foreach}
                    </ul>
                  </fieldset>
                  <button style="display: none" type="button" class="button-link-light multiselect-show-checked-toggle"><span>Show only selected options (<span data-slot="count">0</span>)</span></button>
                </div>
              </div>
            </div>

            <div class="advanced-search-filter-container">
              <h4 class="advanced-filter-label-text">Original Format</h4>
              <div class="advanced-filter-inner-container">
                <div class="multiselect">
                  <p>Select one or more checkboxes to narrow your results to items that match all of your format selections.</p>
                  <input name=".format-filter" type="text" class="multiselect-search" aria-label="Filter options" aria-describedby="format" placeholder="Filter" value="" />
                  <p id="format" class="offscreen">Below this edit box is a list of check boxes that allow you to filter down your options. As you type in this edit box, the list of check boxes is updated to reflect only those that match the query typed in this box.</p>
                  <fieldset class="multiselect-options">
                    <ul class="multiselect-options-list">
                      {foreach from=$formatList item="format" name=options}
                        {if $format}
                      <li class="multiselect-options-list-item">
                        <input type="checkbox" name="fqor-format[]" id="format-{$smarty.foreach.options.index}" value="{$format}" {if in_array($format, $fqor_format)}checked="checked" {/if}/>
                        <label for="format-{$smarty.foreach.options.index}">
                          <span class="filter-name">{$format|escape:"html"}</span>
                        </label>
                      </li>
                        {/if}
                      {/foreach}
                    </ul>
                  </fieldset>
                  <button style="display: none" type="button" class="button-link-light multiselect-show-checked-toggle"><span>Show only selected options (<span data-slot="count">0</span>)</span></button>
                </div>
              </div>
            </div>
          </div>

          <div style="display: flex; justify-content: space-between; align-items: center; flex-direction: row">
            <button kind="primary" class="button btn btn-primary"><i class="icomoon icomoon-search" aria-hidden="true"></i> Advanced Search</button>
            <a class="button btn" href="/Search/Advanced">Reset Form</a>
          </div>

          <input type='hidden' name='adv' value='1'>
        </form>
      </section>
    </div>
  </main>

  {include file="footer.tpl"}
</div>
