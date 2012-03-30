<div id="facets_view" style="display:none">
	<div class="header">
    	<a href="{$site.home_url}" class="htlogobutton" ></a>
        <a class="backlink"  onclick="toggleFacetView('facets_view','results_view')" href="#">&lt;&lt;&nbsp;Results</a>
	</div>
	
	{* <!-- List of current filters with delete button --> *}
	<div class="whitebox">
		{if $currentFacets|@count > 0}
			<div id="facetbox">
			<div>{translate text='Results filtered by'}</div>
				{foreach from=$currentFacets item=facet}       
						{if $facet.index eq 'ht_availability' && $facet.value eq 'Full text'}
						{*{if $facet.logargs eq 'removefacet|ht_availability|Full text|'}*}
							{* ugh... adding the sethtftonly=false is a total hack *}
	          				<a  class="facetitem" ref="{$facet.logargs}" href="{$url}/Search/{$action}?{$facet.removalURL}&sethtftonly=false">
	          					<img  src="{$path}/images/silk/delete.png" alt="Delete">
								{$facet.indexDisplay} : {translate text=$facet.valueDisplay}
							</a>						
						{else}   				
	          				<a  class="facetitem" ref="{$facet.logargs}" href="{$url}/Search/{$action}?{$facet.removalURL}">
	          					<img  src="{$path}/images/silk/delete.png" alt="Delete">
								{$facet.indexDisplay} : {translate text=$facet.valueDisplay}
							</a>
						{/if}
						<br />
						<br />
        		{/foreach}
      		</div>
      	{else}
      		<div id="facetbox">No facets selected</div>
      	{/if}

      	<div class="navmenu" id="narrowList">
        	{include file="Search/facet_snippet.tpl"}
     	</div>    	
  	</div>
	<div class="footergrad"><!----></div>
</div>



<div id="results_view">
	<div class="header">
    	<a href="{$site.home_url}" class="htlogobutton" ></a>
        <a class="backlink"  href="{$site.url}">&lt;&lt;&nbsp;Home</a>
    </div>
 
	<div id="resultsearchbox" >
	<form method="get" action="{$path}/Search/Home" name="searchForm" onsubmit="fixform(this)">	
		<input type="hidden" name="checkspelling" value="true" />
		<input type="hidden" value="true" name="sethtftonly">
		
		<!--<div id="resultsearchrow1">-->
			<input id="resultsfind" type="text" {*class="forminput"*} name="lookfor" {*id="lookfor"*} value="{$lookfor|escape:"html"}" placeholder="Search Catalog">
		<!-- </div> -->
		<!-- <div id="resultsearchrow2"> -->
		    <select name="type">
	        <option {if $type eq "all"}selected="selected"{/if} value="all">All Fields</option>
	        <option {if $type eq "title"}selected="selected"{/if} value="title">Title</option>
	        <option {if $type eq "author"}selected="selected"{/if} value="author">Author</option>
	        <option {if $type eq "subject"}selected="selected"{/if} value="subject">Subject</option>
	        <!--<option value="hlb">Academic Discipline</option>-->
	        <!--<option value="callnumber">Call Number / in progress</option>-->
	        <option {if $type eq "isn"}selected="selected"{/if} value="isn">ISBN/ISSN</option>
	        <option {if $type eq "publisher"}selected="selected"{/if} value="publisher">Publisher</option>
	        <option {if $type eq "series"}selected="selected"{/if} value="series">Series Title</option>
	        <option {if $type eq "year"}selected="selected"{/if} value="year">Year of Publication</option>
	        <!-- <option value="tag">Tag</option> -->
			</select>
		<!-- </div> -->
			
		<div id="resultsearchrow3">			
			<input  class="autowidth" type="checkbox" id="fullonly" value="true" name="htftonly" {$ht_fulltextonly}>
			<span >  Full view only</span>
			<input id="findbutton" class="autowidth"  type="submit" name="submit" value={translate text="Find"}>
		</div>
	</form>
	</div>

	{* <!-- Narrow Options for an Author Search--> *}
	{if $narrow}
		{* todo -- how to test this, if it is even applicable *}
		{foreach from=$narrow item=narrowItem name="narrowLoop"}
	    	{if $smarty.foreach.narrowLoop.iteration == 6}
			{/if}
			<a href="{$url}/Search/Home?{$narrowItem.authurl}">{$narrowItem.name}</a> ({$narrowItem.num})<br>
		{/foreach}
	
			{if $narrowcount > $smarty.foreach.narrowLoop.iteration}
				<div style="clear:both; text-align: right;">
					<a class="clickpostlog" ref="authseeall|||" href="{$url}/Author/Search?{$searchcomps}">see all ({$narrowcount})</a>
				</div>
			{/if}
	{/if $narrow}
	{* <!-- End Narrow Options --> *}

	<!-- Spelling suggestion -->
	{* todo -- how to test this *}
	{if $newPhrase}
		<p class="correction">{translate text='Did you mean'} <a class="clickpostlog" ref="spellsuggest|||" href="{$url}/Search/{$action}?lookfor={$newPhrase}&amp;type={$type}">{$newPhrase}</a>?</p>
	{/if}
	
	<div id="resultsummary">	
		{if $recordCount}
			<span>{$recordCount}{translate text=' results'}</span>
			
			<a onclick="toggleFacetView('results_view','facets_view')" href="#"
			{* $currentFacets[0].logargs eq 'removefacet|ht_availability|Full text|' && *}
			{* {if $currentFacets|@count > 0} *}
			{if $currentFacets|@count > 0 && 
				!($currentFacets|@count==1 && 
				$currentFacets[0].index eq 'ht_availability' && $currentFacets[0].value eq 'Full text' &&
				$ht_fulltextonly eq "checked") }
				
				class="facetselected"
			
			{/if}
			><div>Filter Results</div></a>
		{/if}
	</div>
	
	{if $subpage}
		{include file=$subpage}
	{else}
		{$pageContent}
	{/if}
	            
	<ul id="recordTools" class="list">
		<li class="recordToolLink linkeditemrightarrow nokiosk">
			{* <a class="linkeditemlink" href="{$regular_url}{$smarty.server.REQUEST_URI}&mdetect=no" target="Mirlyn">{translate text='View Results in Regular Catalog'}</a> *}
			<a href="{$regular_url}{$smarty.server.REQUEST_URI}&mdetect=no" target="Mirlyn">{translate text='View Results in Regular Catalog'}</a> 
		</li>
	</ul>

</div>

