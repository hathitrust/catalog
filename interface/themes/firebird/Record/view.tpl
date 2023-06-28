<div id="skiplinks" class="visually-hidden-focusable" role="complementary" aria-label="Skip links">
  <ul>
    <li>
      <a href="#section">Skip to main</a>
    </li>
    <li>
      <a href="#similar-items">Skip to similar items</a>
    </li>
  </ul>
</div>
<div id="root" class="catalog-record">
  {include file="header.tpl"}

{if $mergeset}
  {assign var="mhtj" value=$mergeset->combined_ht_json() }
  {assign var="htjson" value=$mhtj}  
{else}
  {assign var="htjson" value=$ru->items_from_json($record)}  
{/if}

  {assign var="ht_vals_from_json" value=$ru->ht_link_data_from_json($htjson[0])}
  {assign var=ld value=$ht_vals_from_json}

  <main class="main-container" id="main">
    {* {include file="search_form.tpl"} *}

    {* <div class="container container-medium flex-container flex-container-expanded container-boxed"> *}
    <div class="twocol mt-1">

      <section class="twocol-main" id="section" data-record-count="{$recordCount}">
        <article class="record d-flex flex-column gap-3 p-3 mb-3 mt-3" data-hdl="{$hdl.handle}">
          <div class="article-heading d-flex gap-3">

            <div class="cover d-none d-md-block">
              {if $ld.handle}
                <img class="border p-1" aria-hidden="true" alt="" src="{$unicorn_root}/cgi/imgsrv/cover?id={$ld.handle};width=250" />
                {* <img aria-hidden="true" alt="" src="https://preview.babel.hathitrust.org/cgi/imgsrv/cover?id=mdp.35112104694155" /> *}
              {else}
              <img class="bookCover" aria-hidden="true" alt="" src="https://catalog.hathitrust.org/images/nocover-thumbnail.png" />
              {/if}
              </div>
            </xsl:if>
            {assign var=marcField value=$marc->getFields('245')}
            <h1>
              {foreach from=$marcField item=field name=loop}
                {foreach from=$field->getSubfields() item=subfield key=subcode  name=subloop}
                  {if $subcode >= 'a' and $subcode <= 'z'}
                  <span>{$subfield->getData()}</span>
                  {if ! $smarty.foreach.last}<br />{/if}
                  {/if}
                {/foreach}
              {/foreach}
              {if $record.vtitle}
               <br /><span>{$record.vtitle}</span>
              {/if}
            </h1>
          </div>
          <h2 class="mt-3">Description</h2>
          <div class="d-flex flex-row gap-3">
            <h3 class="mt-3">Tools</h3>
            <div class="list-group list-group-horizontal-sm align-items-center">
                <a href="/Record/{$id|escape:"url"}/Cite" class="list-group-item list-group-item-action">
                  <i class="fa-solid fa-bookmark" aria-hidden="true"></i>
                  <span>Cite this</span>
                </a>
                <a download href="/Search/SearchExport?handpicked={$id|escape:"url"}&amp;method=ris" class="list-group-item list-group-item-action">
                  <i class="fa-solid fa-file-export" aria-hidden="true"></i>
                  <span>Export citation file</span>
                </a>
            </div>
          </div>
          {* <div class="article-actions" style="display: flex; align-items: center">
            <h3 class="xx-offscreen" style="font-size: 1rem; margin-right: 1rem;">Tools</h3>
            <ul>
              <li><a href="/Record/{$id|escape:"url"}/Cite" class="cite"><i class="icomoon icomoon-bookmark" aria-hidden="true"></i> {translate text="Cite this"}</a></li>
              <li><a download class="endnotelink" href="/Search/SearchExport?handpicked={$id|escape:"url"}&amp;method=ris" data-toggle="tracking" data-tracking-category="recordActions" data-tracking-action="Catalog Export" data-tracking-label="Endnote"><i class="icomoon icomoon-upload" aria-hidden="true"></i> Export citation file</a></li>
            </ul>
          </div> *}

          {include file="$module/view.summary.tpl"}

          <h2 id="viewability" class="mt-3">Viewability</h2>
          <table class="table-branded viewability-table">
            <thead>
              <tr>
                <th>Item Link</th>
                <th>Original Source</th>
              </tr>
            </thead>
            <tbody>
              {foreach from=$htjson item=e}
                {assign var=ld value=$ru->ht_link_data_from_json($e)}
                {if $record_is_tombstone || !($ld.is_tombstone)}
                 <tr>
                  <td>
                   {if $record_is_tombstone}
                     This item is no longer available (<a href="//hdl.handle.net/2027/{$ld.handle}" class="rights-{$ld.rights_code}">why not?</a>)
         {elseif ( ! $ld.is_fullview && ( $ld.is_NFB || $ld.has_activated_role ) ) }
              <a data-activated-role="true" href="{$handle_prefix}{$ld.handle}" class="rights-{$ld.rights_code} fulltext"><i aria-hidden="true" class="fa-solid fa-unlock"></i> <span>Limited (Access Permitted)</span> &nbsp; <span class="IndItem">{$ld.enumchron}</span></a>
                     {elseif ($ld.is_fullview || $ld.is_NFB)}
            <a href="{$handle_prefix}{$ld.handle}" class="rights-{$ld.rights_code} fulltext"><i class="fa-regular fa-file-lines" aria-hidden="true"></i> <span>Full view</span> &nbsp; <span class="IndItem">{$ld.enumchron}</span></a>
	       {elseif $ld.is_emergency_access}
	              <a href="{$handle_prefix}{$ld.handle}" class="rights-{$ld.rights_code} etas"><i aria-hidden="true" class="fa-solid fa-unlock"></i> <span>Temporary access</span> &nbsp; <span class="IndItem">{$ld.enumchron}</span></a>
          {else}
            <a href="{$handle_prefix}{$ld.handle}" class="rights-{$ld.rights_code} searchonly"><i aria-hidden="true" class="fa-solid fa-lock"></i> <span>Limited (search only)</span> &nbsp; <span class="IndItem">{$ld.enumchron}</span></a>
          {/if}

                     </td>
                     <td>
                       {$ld.original_from}
                      </td>
                   </tr>
                   {/if}
                {/foreach}

            </tbody>
          </table>

          <div style="text-align: center">
            <a class="btn btn btn-outline-secondary mb-3" href="/Record/{$id|escape:"url"}.marc">View HathiTrust MARC record</a>
          </div>

        </article>




                
      </section>
      

	





      {if is_array($similarRecords) or $lastsearch}
      <div class="twocol-side" id="sidebar" tabindex="0">
        {if $lastsearch}
        <div class="back-to-results">
          <a class="btn btn-secondary" href="{$lastsearch|regex_replace:"/&/":"&amp;"}">
          <i class="fa-solid fa-arrow-left-long" aria-hidden="true"></i> Back to Catalog Search Results
          </a>

        </div>
        {/if}

        <h2 class="fs-3 mt-3" id="similar-items">Similar Items</h2>
        <div class="similar-items">
          {foreach from=$similarRecords item=similar}
          {if is_array($similar.title)}{assign var=similarTitle value=$similar.title.0}
          {else}{assign var=similarTitle value=$similar.title}{/if}
            <div class="d-flex gap-3 p-3 mb-3 mt-3 shadow-sm rounded">
            <div class="container-fluid p-1">
              <h3 class="record-title h4 mb-3 fw-normal"><a href="{$url}/Record/{$similar.id}">{$similarTitle}</a></h3>
              {if $similar.author or $similar.publishDate}
              <dl class="metadata mb-0">
              <div class="grid gap-2">
                {if $similar.author}
                <dt class="g-col-lg-3 g-col-12">Author</dt>
                <dd class="g-col-lg-9 g-col-12">{$similar.author.0}</dd>
                {/if}
                {if $similar.publishDate}
                <dt class="g-col-lg-3 g-col-12">Published</dt>
                <dd class="g-col-lg-9 g-col-12">{$similar.publishDate.0}</dd>
                {/if}
                </div>
              </dl>
              {/if}
            </div>
            </div>
          {/foreach}
        </div>
      </div>
      {/if}


    </div>
    

  </main>

  {include file="footer.tpl"}
</div>
