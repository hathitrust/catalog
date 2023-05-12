<div id="skiplinks" class="visually-hidden-focusable" role="complementary" aria-label="Skip links">
  <ul>
    <li>
      <a href="#section">Skip to main</a>
    </li>
  </ul>
</div>
<div id="root" class="catalog-record">
  {include file="header.tpl"}

  <main class="main-container" id="main">

    {* <div class="container container-narrow flex-container flex-container-expanded container-boxed"> *}
    <div class="twocol mt-1">

      {assign var="htjson" value=$ru->items_from_json($record)}
      {assign var="ht_vals_from_json" value=$ru->ht_link_data_from_json($htjson[0])}
      {assign var=ld value=$ht_vals_from_json}

      <section class="twocol-main" id="section">
        <article class="record d-flex flex-column gap-3 p-3 mb-3 mt-3">
          {* <p style="display: none; margin-bottom: 1rem; padding-left: 7rem"><a href="{$url}/Record/{$id}/Home" class="backtosearch"><i class="icomoon icomoon-enter" aria-hidden="true"></i> {translate text="Back to Record"}</a></p> *}

          <div class="article-heading d-flex gap-3">

            <div class="cover d-none d-mb-block">
              {if $ld.handle}
                <img class="border p-1" aria-hidden="true" alt="" src="https://babel.hathitrust.org/cgi/imgsrv/cover?id={$ld.handle}" />
              {else}
              <img class="bookCover" aria-hidden="true" alt="" src="https://catalog.hathitrust.org/images/nocover-thumbnail.png" />
              {/if}
              </div>

             {assign var=marcField value=$marc->getFields('245')}
             <div>
              <p style="margin-bottom: 1rem; margin-top: -2rem"><a href="{$url}/Record/{$id}/Home" class="backtosearch"><i class="icomoon icomoon-enter" aria-hidden="true"></i> {translate text="Back to Record"}</a></p>
              <h2>
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
             </h2>
             </div>
            </xsl:if>
          </div>

          {include file="Record/$subTemplate"}

        </article>
      </section>

    </div>

  </main>

  {include file="footer.tpl"}
</div>
