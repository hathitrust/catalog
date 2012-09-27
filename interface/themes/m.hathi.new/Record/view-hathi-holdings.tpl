{assign var=marcField value=$marc->getFields('974')}

{foreach from=$marcField item=field name=loop}
{assign var=htid value=$field->getSubfield('u')}
{assign var=url value=$htid->getData()}
{assign var=nmspace value=$url|regex_replace:"/\..*/":""}

<li class="holding_container linkeditemrightarrow">
  <a href="http://hdl.handle.net/2027/{$htid}?skin=mobile">
    {if $field|getvalue:'r' eq 'pd'} class="fulltext">Full view
      {elseif $field|getvalue:'r' eq 'pdus' && $session->get('inUSA')} class="fulltext">Full view
      {elseif $field|getvalue:'r' eq 'world'}class="fulltext">Full view
      {elseif $field|getvalue:'r' eq 'ic-world'}class="fulltext">Full view
      {elseif $field|getvalue:'r' eq 'und-world'}class="fulltext">Full view
      {elseif $field|getvalue:'r' eq 'cc-by'}class="fulltext">Full view
      {elseif $field|getvalue:'r' eq 'cc-by-nd'}class="fulltext">Full view
      {elseif $field|getvalue:'r' eq 'cc-by-nc-nd'}class="fulltext">Full view
      {elseif $field|getvalue:'r' eq 'cc-by-nc'}class="fulltext">Full view
      {elseif $field|getvalue:'r' eq 'cc-by-nc-sa'}class="fulltext">Full view
      {elseif $field|getvalue:'r' eq 'cc-by-sa'}class="fulltext">Full view
      {elseif $field|getvalue:'r' eq 'cc-zero'}class="fulltext">Full view
      {else}class="searchonly">Limited (search-only)
    {/if}
    <span class="IndItem">{if $field|getvalue:'z'}{$field|getvalue:'z'}{else}{/if}</span>
  </a>
  <em>(original from {$ht_namespace_map[$nmspace]})</em>
</li>
{/foreach}

  {*<ul class="list" id="locationList">*}
  {*<ul class="holdings">*}
  {*{foreach from=$marcField item=field name=loop}*}
  {*{assign var=url value=$field->getSubfield('u')}*}
  {*{assign var=url value=$url->getData()}*}
  {*{assign var=nmspace value=$url|regex_replace:"/\..*/":""}*}
  {*<li><a href="http://hdl.handle.net/2027/{$url}" *}
  {*{if $field|getvalue:'r' eq 'pd'} class="fulltext">Full view*}
  {*{elseif $field|getvalue:'r' eq 'pdus' && $session->get('inUSA')} class="fulltext">Full view*}
  {*{elseif $field|getvalue:'r' eq 'world'}class="fulltext">Full view*}
  {*{elseif $field|getvalue:'r' eq 'ic-world'}class="fulltext">Full view*}
  {*{elseif $field|getvalue:'r' eq 'und-world'}class="fulltext">Full view*}
  {*{elseif $field|getvalue:'r' eq 'cc-by'}class="fulltext">Full view*}
  {*{elseif $field|getvalue:'r' eq 'cc-by-nd'}class="fulltext">Full view*}
  {*{elseif $field|getvalue:'r' eq 'cc-by-nc-nd'}class="fulltext">Full view*}
  {*{elseif $field|getvalue:'r' eq 'cc-by-nc'}class="fulltext">Full view*}
  {*{elseif $field|getvalue:'r' eq 'cc-by-nc-sa'}class="fulltext">Full view*}
  {*{elseif $field|getvalue:'r' eq 'cc-by-sa'}class="fulltext">Full view*}
  {*{elseif $field|getvalue:'r' eq 'cc-zero'}class="fulltext">Full view*}
  {*{else}class="searchonly">Limited (search-only)*}
  {*{/if}*}
  {*<span class="IndItem">{if $field|getvalue:'z'}{$field|getvalue:'z'}{else}{/if}</span></a> *}
  {*<em>(original from {$ht_namespace_map[$nmspace]})</em>*}
  {*</li>*}
  {*{/foreach}*}
  {*</ul>*}
{*</ul>*}