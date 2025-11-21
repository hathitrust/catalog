{capture name=reset_url}{$fullPath_esc|remove_url_param:"lookfor[^=]+"|remove_url_param:"type[^=]+"|remove_url_param:"searchtype[^=]+"|regex_replace:"/\/Home&amp;/":"/Home?"}{/capture}
<div class="twocol-side" id="sidebar">
  
  <button id="action-toggle-filters" class="btn btn-outline-primary" aria-expanded="false">
    <span>
      <span class="not-expanded">Show</span>
      <span class="is-expanded">Hide</span>
      Search Filters
    </span>
  </button>

  <h2 class="filters-heading fs-3 mt-3">Filter your search</h2>

  <!-- current filters accordion -->
  {if (isset($currentFacets) and $currentFacets) or ( $searchterms and ($lookfor ne '*') ) }
    <div class="accordion mb-1">
      <div class="panel accordion-item">
        <h3 class="accordion-header" id="heading-current">
        <button class="accordion-button fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-current" aria-controls="collapse-current" aria-expanded="true">
        Current Filters
        </button></h3>
        <div id="collapse-current" class="accordion-collapse collapse show" aria-labelledby="heading-current">
          <div class="accordion-body">
            <ul class="list-group list-group-flush">
              {if (isset($searchterms) and $searchterms) and ($lookfor ne '*') }
                {assign var=rurl value=$ss->asWildcardURL()|regex_replace:"/&/":"&amp;"}

                <li class="list-group-item d-flex justify-content-between align-items-center gap-3">
              
                  <span>{$searchterms}</span>
                    <a class="btn btn-outline-dark btn-lg" href="{$rurl}">
                    <i class="fa-solid fa-xmark" aria-hidden="true"></i><span class="visually-hidden">Remove filter {$searchterms}</span>
                    </a>
                  
                </li>
              {/if}
              {foreach from=$currentFacets item=facet}
              {assign var=rurl value=$facet.removalURL|regex_replace:"/&/":"&amp;"}
                <li class="list-group-item d-flex justify-content-between align-items-center gap-3">
                  <span >{$facet.indexDisplay}: {translate text=$facet.valueDisplay}</span>
                  <a class="btn btn-outline-dark btn-lg" href="/Search/{$action}?{$rurl}">
                  <i class="fa-solid fa-xmark" aria-hidden="true"></i><span class="visually-hidden">Remove filter {$facet.indexDisplay}: {translate text=$facet.valueDisplay}</span>
                  </a>
                </li>
              {/foreach}
            </ul>
          </div>
        </div>
      </div>
    </div> <!-- end of current filters accordion -->
    <!-- clear filters -->
    <div class="d-flex flex-column gap-2 mb-3">
    {if isset($adv) and $adv}
      <a class="btn btn-outline-dark btn-sm clear-all-filters" href="{$adv}">
        <span>Revise this advanced search</span>
      {* </button> *}
      </a>
    {/if}
      <a class="btn btn-outline-dark btn-sm clear-all-filters" href="/Search/Home?lookfor=*&type=all">
        <span>Clear filters</span>
      </a>
    </div>
     <!-- end clear filters -->
  {/if} <!-- end current facets conditional -->

  <div class="accordion mb-3">
    {if $allitems_count gt 0}
    <div class="panel accordion-item">
      <h3 class="accordion-header" id="heading-viewability">
      <button class="accordion-button fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-viewability" aria-expanded="true" aria-controls="collapse-viewability">Item Viewability</button></h3>
      <div id="collapse-viewability" class="accordion-collapse collapse show"  aria-labelledby="heading-viewability">
        <div class="accordion-body">
          <div class="list-group list-group-flush">
            <a href="{$allitems_url}" class="list-group-item d-flex justify-content-between align-items-center {if !$is_fullview}active{/if}" aria-current="{if !$is_fullview}true{else}false{/if}">All Items 
              {if $allitems_count gt 0}
              <span class="badge bg-dark rounded-pill">{$allitems_count|number_format:null:".":","}</span>
              {/if}
              </a>
            <a href="{$fullview_url}" class="list-group-item d-flex justify-content-between align-items-center {if $is_fullview}active{/if}" aria-current="{if $is_fullview}true{else}false{/if}">Full View 
              {if $fullview_count gt 0}
              <span class="badge bg-dark rounded-pill">{$fullview_count|number_format:null:".":","}</span>
              {/if}
              </a>
          </div>
        </div>
      </div>
     </div>
    {/if}
  </div>

  <div class="accordion" id="accordion-filters">
  {foreach from=$indexes item=cluster}
  {if $cluster eq 'ht_availability'} 
  {else}
    <div class="panel accordion-item">
      <h3 class="accordion-header" id="heading-{$cluster}">
        <button class="accordion-button fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{$cluster}" aria-controls="collapse-{$cluster}" aria-expanded="false">
        {$facetConfig.$cluster}
        </button>
      </h3>
      <div id="collapse-{$cluster}" class="accordion-collapse collapse" aria-labelledby="heading-{$cluster}" data-bs-parent="#accordion-filters">
        <div class="accordion-body">
          <div class="filter-list" data-expanded="false">
            {foreach from=$counts.$cluster item=facet name="facetLoop"}
            <div class="filter-list-item d-flex flex-nowrap align-items-center justify-content-between gap-3 mb-2 px-3">
              <a href="/Search/Home?{$facet.url|regex_replace:"/&/":"&amp;"}">{translate text=$facet.value}</a>
              <span>{$facet.count|number_format:null:".":","}</span> 
            </div> 
            {/foreach}
          </div>
          <div class="mt-3">
            {if $counts.$cluster|@count gt 6}
            <button type="button" class="btn btn-sm btn-outline-dark" data-action="expand-filter" aria-expanded="false">
              Show 
                <span class="not-expanded">all {$counts.$cluster|@count} </span>
                <span class="is-expanded">fewer </span>
                {$facetConfig.$cluster} Filters
            </button> 
            {/if}
          </div>
        </div>
      </div>
    </div>
  {/if}
  
  {/foreach}
  </div>
</div>
