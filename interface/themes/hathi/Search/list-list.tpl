<form name="addForm" action="#" method="GET">
{foreach from=$recordSet item=record name="recordLoop"}
  {if ($smarty.foreach.recordLoop.iteration % 2) == 0}
  <div class="result alt record{$smarty.foreach.recordLoop.iteration}">
  {else}
  <div class="result record{$smarty.foreach.recordLoop.iteration}">
  {/if}

<!--
  <script type="text/javascript">
     getStatuses('{$record.id}');
  </script>
-->
    <div class="yui-ge">
      <div class="yui-u first">
      <div id=GoogleCover_{$record.id} style="position: relative; float: left">
       <img src="/images/nocover-thumbnail.png"/>
      </div>

        <div class="resultitem">
          <div class="resultItemLine1 results_title">
            {if $showscores}
            (<span class="score">{$record.score}</span>)
            {/if}
          {if is_array($record.title)}
<!-- title array -->
            {foreach from=$record.title item=title}
            <h3 class="title"><span class="title">{$title|truncate:180:"..."|highlight:$lookfor|default:'Title not available'}</span></h3><br>            {/foreach}
          {else}
<!-- title non-array -->
          <h3 class="title"><span class="title">{$record.title|truncate:180:"..."|highlight:$lookfor|default:'Title not aavailable'}</span></h3>

          {/if}
          {if $record.title2}
          <br>
          <span class="results_title2">{$record.title2|truncate:180:"..."|highlight:$lookfor}</span>
          {/if}

          </div>

          <div class="resultItemLine2 results_author">
          {if $record.author}
          {translate text='by'}
          {if is_array($record.author)}
            {foreach from=$record.author item=author}
           <!-- <a href="/Search/Home?lookfor=%22{$author|escape:'uri'}%22&amp;type=author&amp;inst={$inst}">{$author|highlight:$lookfor}</a> -->
           {$author|highlight:$lookfor}
            {/foreach}
          {else}
          <!-- <a href="/Search/Home?lookfor=%22{$record.author|escape:'uri'}%22&amp;type=author&amp;inst={$inst}">{$record.author|highlight:$lookfor}</a> -->
          {$record.author|highlight:$lookfor}

          {/if}
          {/if}
          </div>

          <div class="resultItemLine3 results_published">
          {if $record.publishDate}{translate text='Published'} {$record.publishDate.0}{/if}
          </div>


          <div>

          </div>

          <!-- Viewability Link -->
          

          <div class="AccessLink">
            <ul>
              <li>
              <!--                <a href="/Record/{$record.id}" class="cataloglinkhref">Catalog Record</a> -->
              <a href="{$ss->asRecordURL($record.id)}" class="cataloglinkhref">Catalog Record</a>

              </li>

              <li>
               {assign var="dfields" value=$ru->displayable_ht_fields($record.marc)}
               
                 
                {* If we have more than one good 974, just put in the link
                   to the catalog record *}
                {if $dfields|@count gt 1}
                  <span class="multivolLink">(view record to see multiple volumes)</span>
                {else}
                  {assign var=ld value=$ru->ht_link_data($dfields[0])}
                  {if $ld.is_fullview}
                    <a href="http://hdl.handle.net/2027/{$ld.handle}" class="rights-{$ld.rights_code} fulltext">Full view</a>
                  {else}
                    <a href="http://hdl.handle.net/2027/{$ld.handle}" class="rights-{$ld.rights_code} searchonly">Limited (search only)</a>
                  {/if}
                {/if}  
               </li>
            </ul>

          </div>

       </div>
      </div>

    </div>


  </div>

  <script type="text/javascript">
   {if $record.googleLinks}
{literal}      jq(document).ready(function() { {/literal}
        getGoogleBookInfo('{$record.googleLinks}', '{$record.id}')
{literal}        }); {/literal}
    {/if}
  </script>


{/foreach}
</form>

