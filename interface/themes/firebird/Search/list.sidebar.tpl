{capture name=reset_url}{$fullPath_esc|remove_url_param:"lookfor[^=]+"|remove_url_param:"type[^=]+"|remove_url_param:"searchtype[^=]+"|regex_replace:"/\/Home&amp;/":"/Home?"}{/capture}
<div class="sidebar-container" id="sidebar" tabindex="0">
  <button class="for-mobile sidebar-toggle-button filter-group-toggle-show-button" aria-expanded="false">
    <span class="flex-space-between flex-center">
      <span class="filter-group-heading">Options/Filters<span class="total-filter-count"></span></span>
      {* <svg xmlns="http://www.w3.org/2000/svg" class="icon"><use xlink:href="#panel-collapsed"></use></svg> *}
      <i class="icomoon icomoon-sidebar-toggle" aria-hidden="true"></i>
    </span>
  </button>

  <h2 class="filters-heading" style="font-size: 1.125rem; padding-bottom: 0">Filter your search</h2>

  {if $currentFacets or ( $searchterms and ($lookfor ne '*') ) }
  <h3 class="active-filters-heading">Current Filters</h3>
  <ul class="active-filters-list">
    {if ($searchterms) and ($lookfor ne '*') }
      {assign var=rurl value=$ss->asWildcardURL()|regex_replace:"/&/":"&amp;"}

    <li class="active-filter-item">
      <button class="active-filter-button" data-href="{$rurl}" data-xhref="{$smarty.capture.reset_url}&amp;lookfor%5B%5D=*&amp;type%5B%5D=all">
        <span class="flex-space-between flex-center">
          <span class="active-filter-button-text">{$searchterms|escape}</span>
          <svg viewBox="0 0 14 14" version="1.1" class="icon"><use xlink:href="#action-remove"></use></svg>
          <span class="offpage">Remove</span>
        </span>
      </button>
    </li>
    {/if}
    {foreach from=$currentFacets item=facet}
      {assign var=rurl value=$facet.removalURL|regex_replace:"/&/":"&amp;"}
      <li class="active-filter-item">
        <button class="active-filter-button" data-href="/Search/{$action}?{$rurl}">
          <span class="flex-space-between flex-center">
            <span class="active-filter-button-text">{$facet.indexDisplay}: {translate text=$facet.valueDisplay}</span>
            <svg viewBox="0 0 14 14" version="1.1" class="icon"><use xlink:href="#action-remove"></use></svg>
            <span class="offpage">Remove</span>
          </span>
        </button>
      </li>
    {/foreach}
    {if $adv}
    <li class="filter-action">
      <button class="button-link-light clear-all-filters" data-href="{$adv}">
        <span>Revise this advanced search</span>
      </button>
    </li>
    {/if}
    <li class="filter-action">
      <button class="button-link-light clear-all-filters" data-href="/Search/Home?lookfor=*&type=all">
        <span>Clear filters</span>
      </button>
    </li>
    </ul>
  {/if}

  <ul class="filter-group-list">
  {if $allitems_count gt 0}
    <li>
      <h3 class="active-filters-heading" id="filter-item-viewability-desc">Item Viewability</h3>
      <ul class="filter-list" role="radiogroup" aria-labelledby="filter-item-viewability-desc">
        <li class="filter-group filter-group-checkbox">
          <button role="radio" aria-checked="{if !$is_fullview}true{else}false{/if}" class="checkbox-label" {if !$is_fullview}tabindex="0"{/if} data-href="{$allitems_url}" aria-labelledby="view-all">
            {* <span id="view-all" class="offscreen">View</span> *}
            <div class="checkbox">{if !$is_fullview}<span class="filter-checkbox-checked">{/if}<svg class="icon"><use xlink:href="{if $is_fullview}#radio-empty{else}#radio-checked{/if}"></use></svg>{if !$is_fullview}</span>{/if}</div>
            <span class="flex-space-between flex-center" id="view-all">
              <span class="filter-name">All Items </span>
              {if $allitems_count gt 0}
              <span class="filter-count">{$allitems_count|number_format:null:".":","}</span>
              {/if}
            </span>
          </button>
        </li>
        <li class="filter-group filter-group-checkbox">
          <button role="radio" aria-checked="{if $is_fullview}true{else}false{/if}" class="checkbox-label" {if $is_fullview}tabindex="0"{/if} data-href="{$fullview_url}" aria-labelledby="view-full-view">
            {* <span class="offscreen" id="view-full-view">View</span> *}
            <div class="checkbox">{if $is_fullview}<span class="filter-checkbox-checked">{/if}<svg version="1.1" class="icon"><use xlink:href="{if $is_fullview}#radio-checked{else}#radio-empty{/if}"></use></svg>{if $is_fullview}</span>{/if}</div>
            <span class="flex-space-between flex-center" id="view-full-view">
              <span class="filter-name">Full View </span>
              {if $fullview_count gt 0}
              <span class="filter-count">{$fullview_count|number_format:null:".":","}</span>
              {/if}
            </span>
          </button>
        </li>
      </ul>
    </li>
  {/if}

  {foreach from=$indexes item=cluster}
  {if $cluster eq 'ht_availability'}
  {else}
    <li class="filter-group filter-group-multiselect">
      <button class="filter-group-toggle-show-button" aria-expanded="true">
        <span class="flex-space-between flex-center">
          <h3 class="filter-group-heading">{$facetConfig.$cluster}</h3>
          <svg class="icon"><use xlink:href="#panel-expanded"></use></svg>
        </span>
      </button>
      <div class="filter-list-container">
        <ul class="filter-list">
          {foreach from=$counts.$cluster item=facet name="facetLoop"}
            <li class="filter-item">
              <button class="filter-button" data-href="/Search/Home?{$facet.url|regex_replace:"/&/":"&amp;"}" aria-label="{translate text=$facet.value} - {$facet.count|number_format:null:".":","}">
                <span class="flex-space-between flex-center">
                  <span class="filter-value">{translate text=$facet.value}</span>
                  <span class="filter-count">{$facet.count|number_format:null:".":","}</span>
                </span>
              </button>
            </li>
          {/foreach}
          {if $counts.$cluster|@count gt 6}
          <li class="filter-action">
            <button class="button-link-light show-all-button" aria-expanded="false">
              <span class="show-all-button__text">Show all {$counts.$cluster|@count} {$facetConfig.$cluster} Filters</span>
              <span class="show-fewer-button__text">Show fewer {$facetConfig.$cluster} Filters</span>
            </button>
          </li>
          {/if}
        </ul>
      </div>
    </li>
  {/if}
  {/foreach}

</div>
