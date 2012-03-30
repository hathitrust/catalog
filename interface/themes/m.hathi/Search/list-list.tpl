<ul id="mResultsListList">

  <form name="addForm">
  
  {foreach from=$recordSet item=record name="recordLoop"}
    {if ($smarty.foreach.recordLoop.iteration % 2) == 0}
    <li class="result alt record{$smarty.foreach.recordLoop.iteration}">
    {else}
    <li class="result record{$smarty.foreach.recordLoop.iteration}">
    {/if}
  
      
              <!--fixme:suz why isn't this working? 
                <div id=GoogleCover_{$record.id} style="display:none; position: relative; float: left; border: 2px solid #ccc"></div>
                -->
                
              <div class="itemMetaData"> 
                  <div id="resultItemLine1" class="results_title">
                    {if $showscores}
                    (<span class="score">{$record.score}</span>)
                    {/if}
                  {if is_array($record.title)}
                  
                  <!-- title array -->
                    {foreach from=$record.title item=title}
                      <a href="/Record/{$record.id}" class="title">{$title|truncate:180:"..."|highlight:$lookfor|default:'Title not available'}</a><br>
                    {/foreach}
                  {else}
                  
                  <!-- title non-array -->
                  <a href="{$url}/Record/{$record.id}" class="title">{$record.title|truncate:180:"..."|highlight:$lookfor|default:'Title not aavailable'}</a>
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
                   {$author|highlight:$lookfor}
                    {/foreach}
                  {else}
                  {$record.author|highlight:$lookfor}
                  {/if}
                  {/if}
                  </div>
                  
                  <div id="resultItemLine3" class="results_published">
                  	{if $record.publishDate}{translate text='Published'} {$record.publishDate.0}{/if}
                  </div>
                   
									<!-- Level of access status -->

				          <div class="AccessStatus">

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
				                    <!-- removed links and reworded-->Multiple volumes
				                {php}break;{/php} 
				                {else}
				{*                 {if $field|getvalue:'z'}{$field|getvalue:'z'}{else}{/if}
				*}  <span {if $field|getvalue:'r' eq 'pd'}class="fulltext">Full-text{elseif $field|getvalue:'r' eq 'pdus'}class="fulltext">Full-text{else}class="searchonly">Search-only (no full-text){/if}
				                {/if}
				                {/foreach}
				            {/if}    

				          </div>
         
              </div>   
     
             </a>
            </li>
      

    <script type="text/javascript">
     {if $record.googleLinks}
        getGoogleBookInfo('{$record.googleLinks}', '{$record.id}');
      {/if}
    </script>


  {/foreach}
  </li>

{*
<script type="text/javascript">
  //doGetStatuses();
  doGetSaveStatuses();
</script>
*}
</ul>
