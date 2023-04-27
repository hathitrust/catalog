
{* <div class="container container-medium flex-container container-header">
  <div class="logo">
    <a href="https://www.hathitrust.org">
      <span class="offscreen">HathiTrust Digital Library</span>
    </a>
  </div>
  <div id="search-modal-content" class="search-modal-content">
    <form id="ht-search-form" class="ht-search-form" method="GET" action="{$unicorn_root}/cgi/ls/one">
      <div style="display: flex; flex-direction: row">
        <div style="flex-grow: 1">
          <div style="display: flex">
            <div class="control control-q1">
              <label for="q1-input" class="offscreen">Search full-text index</label>
              <input id="q1-input" name="q1" type="text" class="search-input-text" placeholder="Search words about or within the items" required="required" pattern="^(?!\s*$).+" value="{$lookfor|escape:'html'}">
            </div>
            <div class="control control-searchtype">
              <label for="search-input-select" class="offscreen">Search Field List</label>
              <select id="search-input-select" size="1" class="search-input-select" name="searchtype" style="font-size: 1rem">
                <option value="all" {if $searchtype == 'all'}selected="selected"{/if}>All Fields</option>
                <option value="title" {if $searchtype == 'title'}selected="selected"{/if}>Title</option>
                <option value="author" {if $searchtype == 'author'}selected="selected"{/if}>Author</option>
                <option value="subject" {if $searchtype == 'subject'}selected="selected"{/if}>Subject</option>
                <option value="isbn" {if $searchtype == 'isbn'}selected="selected"{/if}>ISBN/ISSN</option>
                <option value="publisher" {if $searchtype == 'publisher'}selected="selected"{/if}>Publisher</option>
                <option value="seriestitle" {if $searchtype == 'seriestitle'}selected="selected"{/if}>Series Title</option>
              </select>
            </div>
          </div>
          <div class="global-search-options">
            <fieldset class="search-target">
              <legend class="offscreen">Available Indexes</legend>
              <input name="target" type="radio" id="option-full-text-search" value="ls">
              <label for="option-full-text-search" class="search-label-full-text">Full-text</label>
              <input name="target" type="radio" id="option-catalog-search" value="catalog" checked="checked">
              <label for="option-catalog-search" class="search-label-catalog">Catalog</label>
            </fieldset>
            <div class="global-search-ft">
              <input type="checkbox" name="ft" value="ft" id="global-search-ft" {if $check_ft_checkbox}checked="checked"{/if}/>
              <label for="global-search-ft">Full view only</label>
            </div>
          </div>
        </div>
        <div style="flex-grow: 0">
          <div class="control">
            <button class="btn btn-primary" id="action-search-hathitrust"><i class="icomoon icomoon-search" aria-hidden="true"></i> Search HathiTrust</button>
          </div>
        </div>
      </div>

      <div class="global-search-links" style="padding-top: 1rem; margin-top: -1rem">
        <ul class="search-links">
          <li class="search-advanced-link">
            <a href="{$unicorn_root}/cgi/ls?a=page;page=advanced">Advanced full-text search</a>
          </li>
          <li class="search-catalog-link">
            <a href="{$url}/Search/Advanced">Advanced catalog search</a>
          </li>
          <li>
            <a href="https://www.hathitrust.org/help_digital_library#SearchTips">Search tips</a>
          </li>
        </ul>
      </div>

      <!-- HIDDEN FACETS -->
      {if false and $currentFacets and $ss}
      {assign var=inputs value=$ss->filterURLComponents()}
      {foreach from=$inputs item=input}
      <input type="hidden" name="{$input[0]}" value="{$input[1]}" />
      {/foreach}
      {/if}

    </form>
  </div>
</div> *}
