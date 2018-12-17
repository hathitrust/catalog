{*<div class="headergrad"><!----></div>*}



<ul {*class="list"*} id="searchResults">

{foreach from=$recordSet item=record name="recordLoop"}
	<li>
		<a ref="recview|{$record.id}||{$recordCounter}|showrecordnav" href="/Record/{$record.id}" id="item{$smarty.foreach.recordLoop.iteration}" name="item{$smarty.foreach.recordLoop.iteration}">
				<abbr class="unapi-id" title="urn:bibnum:{$record.id}"></abbr>
				{assign var="recordCounter" value=$smarty.foreach.recordLoop.iteration+$recordStart-1}

				{*<div class="resultrightcol">*}
				{* display title *}
				{if $showscores}
            		{assign var=score value=$record.score*1000}
            		(<span class="score">{$score}</span>)
            	{/if}
            	
            	          
            	{assign var="disptitle" value=$record.title[0]}
            	{foreach from=$record.title item=title name=titleloop}
              		{if ($smarty.foreach.titleloop.iteration == 1) || $title != ''}
						<span class="resultstitle">{$title|truncate:120:"..."|default:'Title not available'}</span>
              		{/if}
            	{/foreach}

				{* display author  - handle multiple authors... *}
				<div class="resultssubheader">
				{if $record.author}
					{foreach from=$record.author item=author name=authorLoop}{$author}{if $smarty.foreach.authorLoop.last==FALSE},{/if}{/foreach};
					{*<br />*}
            	{/if}

				{* display published date *}
				{if $record.publishDate}
					{translate text='Published'} {$record.publishDate.0}
				{/if}
				</div>
				
        {assign var="dfields" value=$ru->displayable_ht_fields($record.marc)}
        
        {* If we have more than one good 974, just put in the link
           to the catalog record *}
        {if $dfields|@count gt 1}
           <span class="viewrights">(view record to see multiple volumes)</span>
        {else}

                  {assign var="htjson" value=$ru->items_from_json($record)}
		  {assign var="ht_vals_from_json" value=$ru->ht_link_data_from_json($htjson[0])}
                  {assign var=ld value=$ht_vals_from_json}

          {if $ld.is_fullview}
            <div class="viewrights fullview rights-{$ld.rights_code}">Full view</div>
          {else}
            <div class="viewrights limitedview rights-{$ld.rights_code}">Limited (search-only)</div>
          {/if}
        {/if}
          

		</a>
	</li>
{/foreach} 

</ul>
{if $pager->isLastPage() == FALSE}
	<div id="moresearchresults">
	{assign var=pageLinks value=$pager->getLinks()}
	{$pageLinks.next}
	</div>
{/if}
		

