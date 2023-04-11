<div id="skiplinks" role="complementary" aria-label="Skip links">
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
    {include file="search_form.tpl"}

    <div class="container container-medium flex-container flex-container-expanded container-boxed">

      <section class="section-container" id="section" data-record-count="{$recordCount}">
        <article class="record" data-hdl="{$hdl.handle}">
          <div class="article-heading">

            <div class="cover">
              {if $ld.handle}
                <img aria-hidden="true" alt="" src="{$unicorn_root}/cgi/imgsrv/cover?id={$ld.handle}" />
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
          <h2 style="font-size: 1.1rem">Description</h2>
          <div class="article-actions" style="display: flex; align-items: center">
            <h3 class="xx-offscreen" style="font-size: 1rem; margin-right: 1rem;">Tools</h3>
            <ul>
              <li><a href="/Record/{$id|escape:"url"}/Cite" class="cite"><i class="icomoon icomoon-bookmark" aria-hidden="true"></i> {translate text="Cite this"}</a></li>
              <li><a download class="endnotelink" href="/Search/SearchExport?handpicked={$id|escape:"url"}&amp;method=ris" data-toggle="tracking" data-tracking-category="recordActions" data-tracking-action="Catalog Export" data-tracking-label="Endnote"><i class="icomoon icomoon-upload" aria-hidden="true"></i> Export citation file</a></li>
            </ul>
          </div>

          {include file="$module/view.summary.tpl"}

          <h2 style="font-size: 1.1rem">Viewability</h2>
          <table class="viewability-table">
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
              <a data-activated-role="true" href="{$handle_prefix}{$ld.handle}" class="rights-{$ld.rights_code} fulltext"><i class="icomoon icomoon-unlocked" aria-hidden="true"></i> Limited (Access Permitted) <span class="IndItem">{$ld.enumchron}</span></a>
                     {elseif ($ld.is_fullview || $ld.is_NFB)}
            <a href="{$handle_prefix}{$ld.handle}" class="rights-{$ld.rights_code} fulltext"><i class="icomoon icomoon-document-2" aria-hidden="true"></i> Full view <span class="IndItem">{$ld.enumchron}</span></a>
	       {elseif $ld.is_emergency_access}
	              <a href="{$handle_prefix}{$ld.handle}" class="rights-{$ld.rights_code} etas"><i class="icomoon icomoon-document-2" aria-hidden="true"></i> Temporary access <span class="IndItem">{$ld.enumchron}</span></a>
          {else}
            <a href="{$handle_prefix}{$ld.handle}" class="rights-{$ld.rights_code} searchonly"><i class="icomoon icomoon-locked" aria-hidden="true"></i> Limited (search only) <span class="IndItem">{$ld.enumchron}</span></a>
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

        </article>

<div style="text-align: center">
  <a class="button btn" href="/Record/{$id|escape:"url"}.marc">View HathiTrust MARC record</a>
</div>



      </section>
      

	





      {if is_array($similarRecords) or $lastsearch}
      <div class="sidebar-container sidebar-container--right" id="sidebar" tabindex="0">
        {if $lastsearch}
        <div class="back-to-results">
          <p><a href="{$lastsearch|regex_replace:"/&/":"&amp;"}"><i class="icomoon icomoon-enter" aria-hidden="true"></i> Back to Catalog Search Results</a></p>

        </div>
        {/if}

        <h3 id="similar-items">Similar Items</h3>
        <ul class="similar-items">
          {foreach from=$similarRecords item=similar}
          {if is_array($similar.title)}{assign var=similarTitle value=$similar.title.0}
          {else}{assign var=similarTitle value=$similar.title}{/if}
          <li>
            <div>
              <p><a href="{$url}/Record/{$similar.id}">{$similarTitle}</a></p>
              {if $similar.author or $similar.publishDate}
              <dl>
                {if $similar.author}
                <dt>Author</dt>
                <dd>{$similar.author.0}</dd>
                {/if}
                {if $similar.publishDate}
                <dt>Published</dt>
                <dd>{$similar.publishDate.0}</dd>
                {/if}
              </dl>
              {/if}
            </div>
          </li>
          {/foreach}
        </ul>
      </div>
      {/if}


    </div>
    

  </main>

  {include file="footer.tpl"}
</div>
