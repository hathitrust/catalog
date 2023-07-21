{*
FIREBIRD TODOS:
  1. when more than one option for viewing book, the "(view record to see multiple volumes)" text is not styled like storybook
    a. I had issues wit the layout every time I tried to move the span outside of the list-group
  2. I need to test out the limited/temporary access options but didn't want to mock HT.login or whatever, so I'll come back to this

*}
{foreach from=$recordSet item=record name="recordLoop"}
  {assign var="htjson" value=$ru->items_from_json($record)}
  {assign var="ht_vals_from_json" value=$ru->ht_link_data_from_json($htjson[0])}
  {assign var=ld value=$ht_vals_from_json}
  
  


 {* {$ld|@var_dump} 
 {$limitedSearchOnly|@var_dump}  *}
  {* {$record.id|@var_dump}  *}
<article class="record d-flex gap-3 p-3 mb-3 mt-3 shadow-sm">
  {* {$ld.handle|@var_dump}
  {$record.title|@var_dump} *}
 
  {* {$ld|@var_dump} *}
  <div class="cover d-none d-md-block" data-hdl="{$ld.handle}">
    {if $ld.handle}
    <img loading="lazy" class="bookCover border p-1 flex-grow-0 flex-shrink-0" aria-hidden="true" alt="" src="{$unicorn_root}/cgi/imgsrv/cover?id={$ld.handle};width=250" />
    {else}
    <img loading="lazy" class="bookCover border p-1 flex-grow-0 flex-shrink-0" aria-hidden="true" alt="" src="/images/nocover-thumbnail.png" />
    {/if}
  </div>

  <div class="flex-grow-1 d-flex flex-column justify-content-between">
    <div class="container-fluid p-1">
      {if is_array($record.title)}
        {foreach from=$record.title item=title}
          <h3 class="record-title">
            <span class="title">{$title|truncate:180:"..."|highlight:$lookfor|default:'Title not available'}</span>
          </h3>
        {/foreach}
      {else}
        <h3 class="record-title">{$record.title|truncate:180:"..."|highlight:$lookfor|default:'Title not available'}</h3>
      {/if}
      {if $record.title2}
      <blockquote>
        <p class="results_title2">{$record.title2|truncate:180:"..."|highlight:$lookfor}</p>
      </blockquote>
      {/if}

      {if $record.vtitle}
      <blockquote>
        <span class="results_title2">{$record.vtitle}</span>
      </blockquote>
      {/if}

      <dl class="metadata">
        {if $record.publishDate}
        <div class="grid">
        <dt class="g-col-lg-4 g-col-12">{translate text='Published'}</dt>
        <dd class="g-col-lg-4 g-col-12">{$record.publishDate.0}</dd>
        </div>
        {/if}

        {if $record.author}
        <div class="grid">
          <dt class="g-col-lg-4 g-col-12">Author</dt>
          {if is_array($record.author)}
            {foreach from=$record.author item=author}
            <dd class="g-col-lg-4 g-col-12">{$author|highlight:$lookfor}</dd>
            {/foreach}
          {else}
            <dd class="g-col-lg-4 g-col-12">{$record.author|highlight:$lookfor}</dd>
        </div>
          {/if}
        {/if}
      </dl>
    </div>
    {* need to come back and figure this out *}

    {assign var="dfields" value=$ru->displayable_ht_fields($record.marc)}
    {if false && $dfields|@count gt 1}
      <p class="fs-7 text-secondary mb-1">
        Use the Catalog Record to view multiple volumes
      </p>
    {/if}

    <div class="resource-access-container">
      <div class="list-group list-group-horizontal-sm align-items-center">
        <a href="{$ss->asRecordURL($record.id)}" class="list-group-item list-group-item-action w-sm-50"><i class="fa-solid fa-circle-info" aria-hidden="true"></i></i> Catalog Record</a>
        {if $dfields|@count eq 1}
          {if ( ! $ld.is_fullview && ( $ld.is_NFB || $ld.has_activated_role ) ) }
            {* need to figure out if data-activated-role="true" is still in use orrr if it's data-access-role="superuser" like in storybook *}
            <a data-activated-role="true" href="{$handle_prefix}{$ld.handle}" referrerpolicy="unsafe-url" class="rights-{$ld.rights_code} fulltext"><i aria-hidden="true" class="fa-solid fa-unlock"></i> Limited (Access Permitted)</a>
          {elseif ($ld.is_fullview || $ld.is_NFB) }
            <a href="{$handle_prefix}{$ld.handle}" referrerpolicy="unsafe-url" class="list-group-item list-group-item-action list-group-item w-sm-50 active"><i class="fa-regular fa-file-lines" aria-hidden="true"></i> Full view</a>
          {elseif $ld.is_emergency_access}
            <a href="{$handle_prefix}{$ld.handle}" referrerpolicy="unsafe-url" class="list-group-item list-group-item-action list-group-item w-sm-50"><i aria-hidden="true" class="fa-solid fa-unlock"></i> Temporary access</a>
          {else}
            <a href="{$handle_prefix}{$ld.handle}" referrerpolicy="unsafe-url" class="list-group-item list-group-item-action list-group-item w-sm-50"><i aria-hidden="true" class="fa-solid fa-lock"></i> Limited (search only)</a>
          {/if}
        {elseif $dfields|@count gt 1}
            <a href="{$ss->asRecordURL($record.id)}#viewability" class="list-group-item list-group-item-action w-sm-50"><i class="fa-solid fa-layer-group" aria-hidden="true"></i></i> Multiple Items</a>
        {/if}
           {* {if ( ! $ld.is_fullview && ( $ld.is_NFB || $ld.has_activated_role ) ) }
            <a data-activated-role="true" href="{$handle_prefix}{$ld.handle}" class="rights-{$ld.rights_code} fulltext"><i class="icomoon icomoon-unlocked" aria-hidden="true"></i> Limited (Access Permitted)</a>
          {elseif ($ld.is_fullview || $ld.is_NFB) }
            <a href="{$handle_prefix}{$ld.handle}" class="rights-{$ld.rights_code} fulltext"><i class="icomoon icomoon-document-2" aria-hidden="true"></i> Full view</a>
	        {elseif $ld.is_emergency_access}
	              <a href="{$handle_prefix}{$ld.handle}" class="rights-{$ld.rights_code} etas"><i class="icomoon icomoon-document-2" aria-hidden="true"></i> Temporary access</a>
          {else}
            <a href="{$handle_prefix}{$ld.handle}" class="rights-{$ld.rights_code} searchonly"><i class="icomoon icomoon-locked" aria-hidden="true"></i> Limited (search only)</a>
          {/if} *}
      </div>
    </div>
  </div>
</article>
{/foreach}
