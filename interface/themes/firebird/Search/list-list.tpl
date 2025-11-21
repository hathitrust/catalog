{assign var="i" value=0}
{foreach from=$recordSet item=record name="recordLoop"}
  {assign var="htjson" value=$ru->items_from_json($record)}
  {assign var="ht_vals_from_json" value=$ru->ht_link_data_from_json($htjson[0])}
  {assign var=ld value=$ht_vals_from_json}
  {assign var="i" value=$i+1}

  {if is_array($record.title)}
    {assign var="first_title" value=$record.title[0]}
  {else}
    {assign var="first_title" value=$record.title}
  {/if}

<article class="record d-flex gap-3 p-3 mb-3 mt-3 shadow-sm">
  {* {$ld.handle|@var_dump}
  {$record.title|@var_dump} *}
  <div class="cover d-none d-md-block" data-hdl="{$ld.handle}">
    {if $ld.handle}
    <img loading="lazy" class="bookCover border p-1 flex-grow-0 flex-shrink-0" aria-hidden="true" alt="" src="{$unicorn_root}/cgi/imgsrv/cover?id={$ld.handle};width=250" />
    {else}
    <img loading="lazy" class="bookCover border p-1 flex-grow-0 flex-shrink-0" aria-hidden="true" alt="" src="/images/nocover-thumbnail.png" />
    {/if}
  </div>

  <div class="flex-grow-1 d-flex flex-column justify-content-between">
    <div class="container-fluid p-1">
      <div id="maintitle-{$i}">
        {if is_array($record.title)}
          {foreach from=$record.title item=title}
            <h3 class="record-title">
              <span class="title">{$title|truncate:180:"..."|default:'Title not available'}</span>
            </h3>
          {/foreach}
        {else}
          <h3 class="record-title">{$record.title|truncate:180:"..."|default:'Title not available'}</h3>
        {/if}
      </div>
      {if array_key_exists('title2', $record)}
      <blockquote>
        <p class="results_title2">{$record.title2|truncate:180:"..."}</p>
      </blockquote>
      {/if}

      {if array_key_exists('vtitle', $record)}
      <blockquote>
        <span class="results_title2">{$record.vtitle}</span>
      </blockquote>
      {/if}

      <dl class="metadata">
        {if array_key_exists('publishDate', $record)}
        <div class="grid">
        <dt class="g-col-lg-4 g-col-12">{translate text='Published'}</dt>
        <dd class="g-col-lg-4 g-col-12">{$record.publishDate.0}</dd>
        </div>
        {/if}

        {if array_key_exists('author', $record)}
        <div class="grid">
          <dt class="g-col-lg-4 g-col-12">Author</dt>
          {if is_array($record.author)}
            {foreach from=$record.author item=author}
            <dd class="g-col-lg-4 g-col-12">{$author}</dd>
            {/foreach}
          {else}
            <dd class="g-col-lg-4 g-col-12">{$record.author}</dd>
        </div>
          {/if}
        {/if}
      </dl>
    </div>
    {assign var="dfields" value=$ru->displayable_ht_fields($record.marc)}
   {* {$dfields|@var_dump}  *}
    {if false && $dfields|@count gt 1}
      <p class="fs-7 text-secondary mb-1">
        Use the Catalog Record to view multiple volumes
      </p>
    {/if}

    <div class="resource-access-container">
      <div class="list-group list-group-horizontal-sm align-items-center">
        <a href="{$ss->asRecordURL($record.id)}" class="list-group-item list-group-item-action w-sm-50" aria-describedby="maintitle-{$i}"><i class="fa-solid fa-circle-info" aria-hidden="true"></i></i>Catalog Record<i aria-hidden="true" class="visited-link fa-solid fa-check-double"></i></a>
        {if $dfields|@count eq 1}
          {if ( $ld.is_resource_sharing ) }
            <a data-activated-role="true" href="{$handle_prefix}{$ld.handle}" referrerpolicy="unsafe-url" class="list-group-item list-group-item-action list-group-item w-sm-50" aria-describedby="maintitle-{$i}"><i aria-hidden="true" class="fa-solid fa-lock-open"></i>Registered Access<i aria-hidden="true" class="visited-link fa-solid fa-check-double"></i></a>
          {elseif ( $ld.role_name !== 'resourceSharing' && ! $ld.is_fullview && ( $ld.has_activated_role ) ) }
            <a data-activated-role="true" href="{$handle_prefix}{$ld.handle}" referrerpolicy="unsafe-url" class="list-group-item list-group-item-action list-group-item w-sm-50" aria-describedby="maintitle-{$i}"><i aria-hidden="true" class="fa-solid fa-unlock"></i>Limited (Access Permitted)<i aria-hidden="true" class="visited-link fa-solid fa-check-double"></i></a>
          {elseif ($ld.is_fullview ) }
            <a href="{$handle_prefix}{$ld.handle}" referrerpolicy="unsafe-url" class="list-group-item list-group-item-action list-group-item w-sm-50 active" aria-describedby="maintitle-{$i}"><i class="fa-regular fa-file-lines" aria-hidden="true"></i>Full view<i aria-hidden="true" class="visited-link fa-solid fa-check-double"></i></a>
          {elseif $ld.is_emergency_access}
            <a href="{$handle_prefix}{$ld.handle}" referrerpolicy="unsafe-url" class="list-group-item list-group-item-action list-group-item w-sm-50" aria-describedby="maintitle-{$i}"><i aria-hidden="true" class="fa-solid fa-unlock"></i>Temporary access<i aria-hidden="true" class="visited-link fa-solid fa-check-double"></i></a>
          {else}
            <a href="{$handle_prefix}{$ld.handle}" referrerpolicy="unsafe-url" class="list-group-item list-group-item-action list-group-item w-sm-50" aria-describedby="maintitle-{$i}"><i aria-hidden="true" class="fa-solid fa-lock"></i>Limited (search only)<i aria-hidden="true" class="visited-link fa-solid fa-check-double"></i></a>
          {/if}
        {elseif $dfields|@count gt 1}
            <a href="{$ss->asRecordURL($record.id)}#viewability" class="list-group-item list-group-item-action w-sm-50" aria-describedby="maintitle-{$i}"><i class="fa-solid fa-layer-group" aria-hidden="true"></i></i>Multiple Items<i aria-hidden="true" class="visited-link fa-solid fa-check-double"></i></a>
        {/if}
      </div>
    </div>
  </div>
</article>
{/foreach}
