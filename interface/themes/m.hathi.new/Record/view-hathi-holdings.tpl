<!-- view-hathi-holdings.tpl -->

{if 'tombstone'|@in_array:$record.ht_rightscode}
  {assign var="fields" value=$ru->ht_fields($marc)}
{else}
  {assign var="fields" value=$ru->displayable_ht_fields($marc)}
{/if}

{foreach from=$fields item=field}
  {assign var=ld value=$ru->ht_link_data($field)}

  <li class="holding_container linkeditemrightarrow">

  <a href="https://babel.hathitrust.org/cgi/pt?id={$ld.handle};skin=mobile" class="rights-{$ld.rights_code}">
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


