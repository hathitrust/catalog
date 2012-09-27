<!-- view-hathi-holdings.tpl -->
{assign var=marcField value=$marc->getFields('974')}

{foreach from=$marcField item=field name=loop}
  {assign var=htid value=$field->getSubfield('u')}
  {assign var=htid value=$htid->getData()}
  {assign var=nmspace value=$htid|regex_replace:"/\..*/":""}
  {assign var=viewclass value="fullview"}
  {assign var=viewtext value="Full View"}
  {assign var=desc value=$field|getvalue:'z'}


<li class="holding_container linkeditemrightarrow">
  <a href="http://babel.hathitrust.org/cgi/pt?id={$htid};skin=mobile">
    <div
      {if $field|getvalue:'r' eq 'pd'} class="gotopt">Go To {if $desc}{$desc}{else}Full View{/if}
        {elseif $field|getvalue:'r' eq 'pdus' && $session->get('inUSA')} class="gotopt">Go To {if $desc}{$desc}{else}Full View{/if}
        {elseif $field|getvalue:'r' eq 'world'}class="gotopt">Go To {if $desc}{$desc}{else}Full View{/if}
        {elseif $field|getvalue:'r' eq 'ic-world'}class="gotopt">Go To {if $desc}{$desc}{else}Full View{/if}
        {elseif $field|getvalue:'r' eq 'und-world'}class="gotopt">Go To {if $desc}{$desc}{else}Full View{/if}
        {elseif $field|getvalue:'r' eq 'cc-by'}class="gotopt">Go To {if $desc}{$desc}{else}Full View{/if}
        {elseif $field|getvalue:'r' eq 'cc-by-nd'}class="gotopt">Go To {if $desc}{$desc}{else}Full View{/if}
        {elseif $field|getvalue:'r' eq 'cc-by-nc-nd'}class="gotopt">Go To {if $desc}{$desc}{else}Full View{/if}
        {elseif $field|getvalue:'r' eq 'cc-by-nc'}class="gotopt">Go To {if $desc}{$desc}{else}Full View{/if}
        {elseif $field|getvalue:'r' eq 'cc-by-nc-sa'}class="gotopt">Go To {if $desc}{$desc}{else}Full View{/if}
        {elseif $field|getvalue:'r' eq 'cc-by-sa'}class="gotopt">Go To {if $desc}{$desc}{else}Full View{/if}
        {elseif $field|getvalue:'r' eq 'cc-zero'}class="gotopt">Go To {if $desc}{$desc}{else}Full View{/if}
        {else}
        {assign var=viewclass value="limitedview"}
        {assign var=viewtext value="Limited View"}

        class="gotopt">Go To {if $desc}{$desc}{else}Limited View{/if}
      {/if}
      <div class="originalsource">(original from {$ht_namespace_map[$nmspace]})</div>
      <div class="{$viewclass}">{$viewtext}</div>
    </div>
  </a>

</li>
{/foreach}

{*<ul class="list" id="locationList">*}
{*<ul class="holdings">*}
{*{foreach from=$marcField item=field name=loop}*}
{*{assign var=url value=$field->getSubfield('u')}*}
{*{assign var=url value=$url->getData()}*}
{*{assign var=nmspace value=$url|regex_replace:"/\..*/":""}*}
{*<li><a href="http://hdl.handle.net/2027/{$url}" *}
{*{if $field|getvalue:'r' eq 'pd'} class="gotopt">{if $desc}{$desc}{else}Full View{/if}*}
{*{elseif $field|getvalue:'r' eq 'pdus' && $session->get('inUSA')} class="gotopt">{if $desc}{$desc}{else}Full View{/if}*}
{*{elseif $field|getvalue:'r' eq 'world'}class="gotopt">{if $desc}{$desc}{else}Full View{/if}*}
{*{elseif $field|getvalue:'r' eq 'ic-world'}class="gotopt">{if $desc}{$desc}{else}Full View{/if}*}
{*{elseif $field|getvalue:'r' eq 'und-world'}class="gotopt">{if $desc}{$desc}{else}Full View{/if}*}
{*{elseif $field|getvalue:'r' eq 'cc-by'}class="gotopt">{if $desc}{$desc}{else}Full View{/if}*}
{*{elseif $field|getvalue:'r' eq 'cc-by-nd'}class="gotopt">{if $desc}{$desc}{else}Full View{/if}*}
{*{elseif $field|getvalue:'r' eq 'cc-by-nc-nd'}class="gotopt">{if $desc}{$desc}{else}Full View{/if}*}
{*{elseif $field|getvalue:'r' eq 'cc-by-nc'}class="gotopt">{if $desc}{$desc}{else}Full View{/if}*}
{*{elseif $field|getvalue:'r' eq 'cc-by-nc-sa'}class="gotopt">{if $desc}{$desc}{else}Full View{/if}*}
{*{elseif $field|getvalue:'r' eq 'cc-by-sa'}class="gotopt">{if $desc}{$desc}{else}Full View{/if}*}
{*{elseif $field|getvalue:'r' eq 'cc-zero'}class="gotopt">{if $desc}{$desc}{else}Full View{/if}*}
{*{else}class="searchonly">Limited (search-only)*}
{*{/if}*}
{*<span class="IndItem">{if $field|getvalue:'z'}{$field|getvalue:'z'}{else}{/if}</span></a> *}
{*<em>(original from {$ht_namespace_map[$nmspace]})</em>*}
{*</li>*}
{*{/foreach}*}
{*</ul>*}
{*</ul>*}
