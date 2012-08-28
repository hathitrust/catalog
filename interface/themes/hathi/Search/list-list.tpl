<form name="addForm">
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
      <div id=GoogleCover_{$record.id} style="display:none;position: relative; float: left; border: 2px solid #ccc">
      </div>

        <div class="resultitem">
          <div id="resultItemLine1" class="results_title">
            {if $showscores}
            (<span class="score">{$record.score}</span>)
            {/if}
          {if is_array($record.title)}
<!-- title array -->
            {foreach from=$record.title item=title}
            <!-- <a href="/Record/{$record.id}" class="title">{$title|truncate:180:"..."|highlight:$lookfor|default:'Title not available'}</a><br> -->
            <h3 class="title"><span class="title">{$title|truncate:180:"..."|highlight:$lookfor|default:'Title not available'}</span></h3><br>            {/foreach}
          {else}
<!-- title non-array -->
          <!-- <a href="{$url}/Record/{$record.id}" class="title">{$record.title|truncate:180:"..."|highlight:$lookfor|default:'Title not aavailable'}</a> -->
          <h3 class="title"><span class="title">{$record.title|truncate:180:"..."|highlight:$lookfor|default:'Title not aavailable'}</span></h3>

          {/if}
          {if $record.title2}
          <br>
          <span class="results_title2">{$record.title2|truncate:180:"..."|highlight:$lookfor}</span>
          {/if}

          </div>

          <div id="resultItemLine2" class="results_author">
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

          <div id="resultItemLine3" class="results_published">
          {if $record.publishDate}{translate text='Published'} {$record.publishDate.0}{/if}
          </div>


          <div>

          </div>

          <!-- Viewability Link -->

          <div class="AccessLink">
            <ul>
              <li>
                <a href="/Record/{$record.id}" class="cataloglinkhref">Catalog Record</a>
              </li>

              <li>
               {assign var=marcField value=$record.marc->getFields('974')}
                {if $marcField}

                    {foreach from=$marcField item=field name=myLoop start=0 loop=2}
                    {/foreach}
                    {assign var=count value=0}
                    {foreach from=$marcField item=field name=loop}
                        {assign var=url value=$field->getSubfield('u')}
                        {assign var=url value=$url->getData()}
                        {assign var=nmspace value=$url|regex_replace:"/\.\d+/":""}

                    {if $smarty.foreach.myLoop.index gt 0}
                        <!-- <a href="/Record/{$record.id}" class="multivolLink">Multiple volumes</a> -->
                        <span class="multivolLink">(view record to see multiple volumes)</span>

                    {php}break;{/php}
                    {else}
<a href="http://hdl.handle.net/2027/{$url}" class="rights-{$field|getvalue:'r'}
  {if $session->get('inUSA')}
    {if $field|getvalue:'r' eq 'pd'}fulltext">Full view
      {elseif $field|getvalue:'r' eq 'pdus'}fulltext">Full view
      {elseif $field|getvalue:'r' eq 'cc-by'}fulltext">Full view
      {elseif $field|getvalue:'r' eq 'cc-by-nd'}fulltext">Full view
      {elseif $field|getvalue:'r' eq 'cc-by-nc-nd'}fulltext">Full view
      {elseif $field|getvalue:'r' eq 'cc-by-nc'}fulltext">Full view
      {elseif $field|getvalue:'r' eq 'cc-by-nc-sa'}fulltext">Full view
      {elseif $field|getvalue:'r' eq 'cc-by-sa'}fulltext">Full view
      {elseif $field|getvalue:'r' eq 'world'}fulltext">Full view
      {elseif $field|getvalue:'r' eq 'ic-world'}fulltext">Full view
      {elseif $field|getvalue:'r' eq 'und-world'}fulltext">Full view
      {else}searchonly">Limited (search-only)
    {/if}
  {else}
    {if $field|getvalue:'r' eq 'pd'}fulltext">Full view
      {elseif $field|getvalue:'r' eq 'pdus'}searchonly">Limited (search-only)
      {elseif $field|getvalue:'r' eq 'cc-by'}fulltext">Full view
      {elseif $field|getvalue:'r' eq 'cc-by-nd'}fulltext">Full view
      {elseif $field|getvalue:'r' eq 'cc-by-nc-nd'}fulltext">Full view
      {elseif $field|getvalue:'r' eq 'cc-by-nc'}fulltext">Full view
      {elseif $field|getvalue:'r' eq 'cc-by-nc-sa'}fulltext">Full view
      {elseif $field|getvalue:'r' eq 'cc-by-sa'}fulltext">Full view
      {elseif $field|getvalue:'r' eq 'world'}fulltext">Full view
      {elseif $field|getvalue:'r' eq 'ic-world'}fulltext">Full view
      {elseif $field|getvalue:'r' eq 'und-world'}fulltext">Full view

      {else}searchonly">Limited (search-only)
    {/if}
  {/if}

</a>

                    {/if}
                    {/foreach}
                {/if}
              </li>
            </ul>



          </div>

{*        <div id="resultItemLine4" class="results_format">

          {if $record.format}
          {assign var=id value=$record.id}
          {assign var=formatList value=$format.$id}
          {if is_array($formatList)}
            {foreach from=$formatList item=fmt}
              {capture name="fmtTrans"}{translate text=$fmt}{/capture}
          <span class="{$fmt|lower|replace:" ":""|regex_replace:"/[()]/":"-"} iconlabel">{$smarty.capture.fmtTrans|strip|replace:' ':'&nbsp;'}</span>
            {/foreach}
          {else}
          <span class="iconlabel {$formatList|lower|replace:" ":""}">{translate text=$formatList}</span>
          {/if}
          {/if}
          </div>
*}

{*           {assign var=holdings value=$resultHoldings.$id}
           <table class="holdings" width="100%" id="holdings{$record.id}">
            <tr>
              <th width="55%">{translate text='Location'}</th>
              <th width="20%">{translate text='Status'}</th>
              <th width="30%">{translate text='Call Number / Description'}</th>
            </tr>
            {foreach from=$holdings item=holding key=location}
            <tr>
              <td class="holdingLocation">{$holding.location}</td>
            {if $holding.status eq 'See holdings'}
              <td><a href="{$url}/Record/{$record.id}/Holdings#holdings">{$holding.status}</a></td>
            {elseif $location eq 'ELEC'}
              <td><a target=link href="{$holding.link}">{$holding.status}</a></td>
            {elseif $location eq 'HATHI'}
              <td><a target=link href=http://hdl.handle.net/2027/{$holding.id}>{$holding.status}</a></td>
            {else}
              <td>{$holding.status}</td>
            {/if}
              <td>
              {if $location eq 'ELEC' or $location eq 'HATHI'}
                  {$holding.description} {$holding.note}
              {else}
                  {$holding.callnumber}
              {/if}
              </td>
            </tr>
            {/foreach}
          </table>
*}
       </div>
      </div>


      <!--<div class="yui-u">
        <div id="saveLink{$record.id}"> -->
<!--          <a href="{$url}/Record/{$record.id}/Save" onClick="getLightbox('Record', 'Save', '{$record.id}', null, '{translate text="Add to Favorites"}'); return false;" class="fav tool">{translate text='Add to favorites'}</a> -->
       <!--  <a href="#" onClick="fillLightbox('favorite_help'); return false;;return false;" class="fav tool">{translate text='Add to favorites'}</a>

        </div>
        {if $user}
        <script language="JavaScript" type="text/javascript">
          getSaveStatuses('{$record.id}');
        </script>
        {/if}
      </div>
    -->

    </div>

<!--
          {if $record.format=="Book"}
    <span class="Z3988"
          title="ctx_ver=Z39.88-2004&amp;rft_val_fmt=info%3Aofi%2Ffmt%3Akev%3Amtx%3Abook&amp;rfr_id=info%3Asid%2F{$coinsID}%3Agenerator&amp;rft.genre=book&amp;rft.btitle={$record.title|escape:"url"}&amp;rft.title={$record.title|escape:"url"}&amp;rft.series={$record.series}&amp;rft.au={$record.author|escape:"url"}&amp;rft.date={$record.publishDate}&amp;rft.pub={$record.publisher|escape:"url"}&amp;rft.edition={$record.edition|escape:"url"}&amp;rft.isbn={$record.isbn}">
          {elseif $record.format=="Journal"}
    <span class="Z3988"
          title="ctx_ver=Z39.88-2004&amp;rft_val_fmt=info%3Aofi%2Ffmt%3Akev%3Amtx%3Ajournal&amp;rfr_id=info%3Asid%2F{$coinsID}%3Agenerator&amp;rft.genre=article&amp;rft.title={$record.title|escape:"url"}&amp;rft.date={$record.publishDate}&amp;rft.issn={$record.issn}">
          {else}
    <span class="Z3988"
          title="ctx_ver=Z39.88-2004&amp;rft_val_fmt=info%3Aofi%2Ffmt%3Akev%3Amtx%3Adc&amp;rfr_id=info%3Asid%2F{$coinsID}%3Agenerator&amp;rft.title={$record.title|escape:"url"}&amp;rft.creator={$record.author|escape:"url"}&amp;rft.date={$record.publishDate}&amp;rft.pub={$record.publisher|escape:"url"}&amp;rft.format={$record.format}">
          {/if}
-->

  </div>

<!--   {if !$record.url}
  <script type="text/javascript">
     getStatuses('{$record.id}');
  </script>
  {/if} -->
  <script type="text/javascript">
   {if $record.googleLinks}
{literal}      jq(document).ready(function() { {/literal}
        getGoogleBookInfo('{$record.googleLinks}', '{$record.id}')
{literal}        }); {/literal}
    {/if}
  </script>


{/foreach}
</form>

{*
<script type="text/javascript">
  //doGetStatuses();
  doGetSaveStatuses();
</script>
*}
