<!-- view-hathi-holdings.tpl -->

{if 'tombstone'|@in_array:$record.ht_rightscode}
  {assign var="fields" value=$ru->ht_fields($marc)}
{else}
  {assign var="fields" value=$ru->displayable_ht_fields($marc)}
{/if}

{foreach from=$fields item=field}
  {assign var=ld value=$ru->ht_link_data($field)}

  <li class="holding_container linkeditemrightarrow">

  <a href="http://babel.hathitrust.org/cgi/pt?id={$ld.handle};skin=mobile" class="rights-{$ld.rights_code}">
  <div class="gotopt">

  {if 'tombstone'|@in_array:$record.ht_rightscode}
    {assign var=viewclass value="noview"}
    {assign var=viewtext value="Unavailable"}
    This item is no longer available (why not?)
    {elseif $ld.is_fullview}
      {assign var=desc value=$ld.enumchron}
      {assign var=viewclass value="fullview"}
      {assign var=viewtext value="Full View"}
      
      {if !$desc}
        {assign var=desc value="Full View"}
      {/if}
      Go To {$desc}
    {else}
      {assign var=desc value=$ld.enumchron}
      {assign var=viewclass value="limitedview"}
      {assign var=viewtext value="Limited View"}
      
      
      {if !$desc}
        {assign var=desc value="Limited View"}
      {/if}
      Go To {$desc}
    {/if}
    <div class="originalsource">(original from {$ld.original_from})</div>
    <div class="{$viewclass}">{$viewtext}</div>
  </div>
</a>
{/foreach}




{*

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

*}