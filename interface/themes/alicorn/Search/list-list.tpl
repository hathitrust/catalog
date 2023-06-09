{foreach from=$recordSet item=record name="recordLoop"}
  {assign var="htjson" value=$ru->items_from_json($record)}
  {assign var="ht_vals_from_json" value=$ru->ht_link_data_from_json($htjson[0])}
  {assign var=ld value=$ht_vals_from_json}

<article class="record">
  <div class="cover" data-hdl="{$ld.handle}">
    {if $ld.handle}
    <img class="bookCover" aria-hidden="true" alt="" src="{$unicorn_root}/cgi/imgsrv/cover?id={$ld.handle}" />
    {else}
    <img class="bookCover" aria-hidden="true" alt="" src="/images/nocover-thumbnail.png" />
    {/if}
  </div>
  <div class="record-container record-medium-container">
    <div class="record-title-and-actions-container">
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

      <dl>
        {if $record.publishDate}
        <dt>{translate text='Published'}</dt>
        <dd>{$record.publishDate.0}</dd>
        {/if}
        {if $record.author}
        <dt>Author</dt>
          {if is_array($record.author)}
            {foreach from=$record.author item=author}
            <dd>{$author|highlight:$lookfor}</dd>
            {/foreach}
          {else}
            <dd>{$record.author|highlight:$lookfor}</dd>
          {/if}
        {/if}
      </dl>
    </div>
    {if $record.content_advice|@count gt 0}
      {foreach from=$record.content_advice item=advice}
        <div class="alert alert-danger">{$advice}</div>
      {/foreach}
    {/if}
    <div class="resource-access-container">
      <ul>
        <li><a href="{$ss->asRecordURL($record.id)}" class="cataloglinkhref"><i class="icomoon icomoon-info-circle" aria-hidden="true"></i> Catalog Record</a></li>
        {assign var="dfields" value=$ru->displayable_ht_fields($record.marc)}
        {if $dfields|@count gt 1}
          <li><span>(view record to see multiple volumes)</span></li>
        {else}

          {if ( ! $ld.is_fullview && ( $ld.is_NFB || $ld.has_activated_role ) ) }
            <li><a data-activated-role="true" href="{$handle_prefix}{$ld.handle}" class="rights-{$ld.rights_code} fulltext"><i class="icomoon icomoon-unlocked" aria-hidden="true"></i> Limited (Access Permitted)</a></li>
          {elseif ($ld.is_fullview || $ld.is_NFB) }
            <li><a href="{$handle_prefix}{$ld.handle}" class="rights-{$ld.rights_code} fulltext"><i class="icomoon icomoon-document-2" aria-hidden="true"></i> Full view</a></li>
	        {elseif $ld.is_emergency_access}
	              <li><a href="{$handle_prefix}{$ld.handle}" class="rights-{$ld.rights_code} etas"><i class="icomoon icomoon-document-2" aria-hidden="true"></i> Temporary access</a></li>
          {else}
            <li><a href="{$handle_prefix}{$ld.handle}" class="rights-{$ld.rights_code} searchonly"><i class="icomoon icomoon-locked" aria-hidden="true"></i> Limited (search only)</a></li>
          {/if}
        {/if}
      </ul>
    </div>
  </div>
</article>
{/foreach}