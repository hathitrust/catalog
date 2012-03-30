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
				
				{assign var=marcField value=$record.marc->getFields('974')}
                {if $marcField}
					
					
                    {foreach from=$marcField item=field name=myLoop start=0 loop=2}
                    {/foreach}
                    
                    {assign var=count value=0}
                    
                    
                    {foreach from=$marcField item=field name=loop}
                    	{*
                        {assign var=url value=$field->getSubfield('u')}
                        {assign var=url value=$url->getData()}
                        {assign var=nmspace value=$url|regex_replace:"/\.\d+/":""}
						*}
						
                    	{if $smarty.foreach.myLoop.index gt 0}
                        	<span class="viewrights">(view record to see multiple volumes)</span>
                    		{php}break;{/php} 
                    	{else}
							{*<a href="http://hdl.handle.net/2027/{$url}" class="rights-{$field|getvalue:'r'}"*}
  							{if $session->get('inUSA')}
    							{if $field|getvalue:'r' eq 'pd'}<div class="viewrights fullview">Full view</div>
      							{elseif $field|getvalue:'r' eq 'pdus'}<div class="viewrights fullview">Full view</div>
      							{elseif $field|getvalue:'r' eq 'world'}<div class="viewrights fullview">Full view</div>
      							{else}<div class="viewrights limitedview">Limited (search-only)</div>
    							{/if}
  							{else}
    							{if $field|getvalue:'r' eq 'pd'}<div class="viewrights fullview">Full view</div>
      							{elseif $field|getvalue:'r' eq 'pdus'}<div class="viewrights limitedview">Limited (search-only)</div>
      							{elseif $field|getvalue:'r' eq 'world'}<div class="viewrights fullview">Full view</div>
      							{else}<div class="viewrights limitedview">Limited (search-only)</div>
    							{/if}
  							{/if}
  
							{*</a>*}

                    	{/if}
                	{/foreach}
                {/if}
				

				{*<div>*}
					{*{$record|@print_r}*}
					{*Availability: {$record.ht_availability|@print_r}*}
				{*</div>*}
			{*</div>*}
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
		

