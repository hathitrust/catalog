<fieldset class="clause">
  <legend class="offscreen">Search Field {$index}</legend>
  {if $index gt 1}
  <fieldset class="no-margin choice-container">
    <legend class="offscreen">Boolean Operator for search field {$index-1} and field {$index}</legend>
    <div>
      <input type="radio" id="search-field-{$index}-boolean-0" data-param="bool[]" class="advanced-boolean-clause" name="bool-{$index}" value="AND" {if ($bool == 'AND') or (! $bool) }checked="checked"{/if} />
      <label for="search-field-{$index}-boolean-0">{translate text="AND"}</label>
    </div>
    <div>
      <input type="radio" id="search-field-{$index}-boolean-1" data-param="bool[]" name="bool-{$index}" value="OR" {if $bool == 'OR'}checked="checked"{/if} class="advanced-boolean-clause" />
      <label for="search-field-{$index}-boolean-1">{translate text="OR"}</label>
    </div>
  </fieldset>
  <input type="hidden" name="bool[]" data-for="bool-{$index}" value="{if ($bool == 'AND') or (! $bool)}AND{else}OR{/if}" />
  {/if}
  <div class="advanced-input-container">
    <select name="type[]" aria-label="Selected field {$index}" class="advanced-field-select">
      <option value="all" {if $type == 'all'} selected{/if}>{translate text="All Fields"}</option>
      <option value="title" {if $type == 'title'} selected{/if}>{translate text="Title"}</option>
      <option value="author" {if $type == 'author'} selected{/if}>{translate text="Author"}</option>
      <option value="subject" {if $type == 'subject'} selected{/if}>{translate text="Subject"}</option>
      {* <option value="hlb" {if $type == 'hlb'} selected{/if}>{translate text="Categories"}</option>
      <option value="toc" {if $type == 'toc'} selected{/if}>{translate text="Table of Contents"}</option>
      <option value="callnumber" {if $type == 'callnumber'} selected{/if}>{translate text="Call Number"}</option> *}
      <option value="publisher" {if $type == 'publisher'} selected{/if}>{translate text="Publisher"}</option>
      <option value="series" {if $type == 'series'} selected{/if}>{translate text="Series Title"}</option>
      <option value="year" {if $type == 'year'} selected{/if}>{translate text="Year of Publication"}</option>
      <option value="isn" {if $type == 'isn'} selected{/if}>ISBN/ISSN</option>
    </select>
    <div class="advanced-input-container">
      <label for="field-search-text-input-{$index}" class="offscreen">Search Term {$index}</label>
      <input id="field-search-text-input-{$index}" placeholder="Search Term {$index}" type="text" value="{$lookfor|default:''}" name="lookfor[]" />
    </div>
  </div>
</fieldset>
