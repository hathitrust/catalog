<script language="JavaScript" type="text/javascript" src="/services/Record/ajax.js"></script>
<script language="JavaScript" type="text/javascript" src="/js/googleLinks.js"></script>

<div class="header">
    <a href="{$site.home_url}" class="htlogobutton" ></a>
	<a class="backlink" href="{$lastsearch}">&lt;&lt;&nbsp;Results</a>
</div>

{if $current}
	<div class="recnav aligncenter">
          {if $previous}
            <a ref="prevrec" style="text-decoration:none" href="{$url}{$previous}" class="prevrec">
            <img alt="Previous Record" src="/images/hathimobile/record_leftarrow.png">
            </a>
          {/if}
          {if $current}
            <span class="aligncenter">{$current|replace:'Showing record ':''}</span>
          {/if}
          {if $next}
            <a ref="nextrec" href="{$url}{$next}" class="nextrec">
            <img alt="Next Record" src="/images/hathimobile/record_rightarrow.png">
           	</a>
          {/if}
	</div>
{/if}


{assign var=smsmessage value=''}
<div id="detailHeader" >
	{* todo -- how to test this? *}
	{if $error}
		<p class="error">{$error}</p>
	{/if}
        	    {*
	<div id="record_detail_left" class="googleCoverColumn">
		<div id="GoogleCover_{$record.id}" >
			<img src="/images/noCover2.gif">
	</div>
</div>
*}

{*<div id="record_detail_right">*}
	<h1>
		{assign var=marcField value=$marc->getFields('245')}
				{foreach from=$marcField item=field name=loop}
					{*<abbr  class="unapi-id" *} {* title="urn:bibnum:{$record.id}"  brk - this displayed an underline, not sure why... > *}
					{foreach from=$field->getSubfields() item=subfield key=subcode  name=subloop}
						{if $subcode >= 'a' and $subcode <= 'z'}
							{$subfield->getData()}
							{assign var=smsmessagetemp value=$smsmessage}
							{assign var=subfieldtemp value=$subfield->getData()}
							{assign var=smsmessage value="$smsmessagetemp$subfieldtemp "}
						{/if}
					{/foreach}
					{*</abbr>*}
				{/foreach}
	</h1>

	{* Display the author *}

	{if $record.author}
	        	<label>{translate text='Author'}:</label>
				<span class="value">
				{foreach from=$record.author item=author name=authorLoop}
	           		{if !$smarty.foreach.authorLoop.last}
	           			<a ref="recmainauthor" href="/Search/Home?lookfor=%22{$author}%22&amp;type=author">{$author}</a>,
						{*{$author},*}
						{assign var=smsmessage value="$smsmessage$author, "}
	           		{else}
	           			{*{$author}*}
	           			<a ref="recmainauthor" href="/Search/Home?lookfor=%22{$author}%22&amp;type=author">{$author}</a>
	           			{assign var=smsmessage value="$smsmessage$author "}
	           		{/if}
	           	{/foreach}
	           	</span>
	           	<br />
	{/if}



	{* Display publication information *}
	{assign var=marcField value=$marc->getFields('26[04]', true)}
	{if $marcField}
	    		<label>{translate text='Published'}:</label>
				<span class="value">
	      			{foreach from=$marcField item=field name=marcLoop}
	      				{if !$smarty.foreach.marcLoop.last}
	        				{$field|getvalue:'a'} {$field|getvalue:'b'} {$field|getvalue:'c'},
	        			{else}
	        				{$field|getvalue:'a'} {$field|getvalue:'b'} {$field|getvalue:'c'}
	        			{/if}
	      			{/foreach}
	      		</span>
	      		<br />
	{/if}
	<button id="moredetails" href=""  onclick="return moreDetails();"><span>More Details</span></button>

	<div id="addlinfo" style="display:none" >
			{* Display the language *}
			{if is_array($recordLanguage)}
				<label>{translate text='Language'}:</label>
				<span class="value">
	        	{foreach from=$recordLanguage item=language name=langLoop}
	           		{if !$smarty.foreach.langLoop.last}
	           			{$language},
	               	{else}
	               		{$language}
	               	{/if}
	           	{/foreach}
	           	</span>
	           	<br />
	        {else}
	           	{if $recordLanguage}
	           		<label>{translate text='Language'}:</label>
					<span class="value">
						{$recordLanguage}
					</span>
					<br />
	           	{/if}
	        {/if}

  			{assign var=marcField value=$marc->getFields('600|610|630|650|651|655',1)}
  			{if $marcField}
  				<label>{translate text='Subjects'}: </label>
  				{if $marcField|@count > 1}<br />{/if}
    			{foreach from=$marcField item=field name=loop}
          			<span class="value">
					{assign var=subject value=""}
          			{foreach from=$field->getSubfields() item=subfield key=subcode  name=subloop}
           				{if $subcode >= 'a'}
            				{if $subject} &gt; {/if}
            				{assign var=subfield value=$subfield->getData()}
            				{assign var=subject value="$subject $subfield"}
            				<a href="/Search/Home?lookfor=%22{$subject|escape}%22&amp;type=subject&amp;inst={$inst}">{$subfield}</a>
           				{/if}
          			{/foreach}
          			</span>
          			<br>
        		{/foreach}
 			{/if}

 		  	{assign var=marcField value=$marc->getFields('500|501|521|525|526|530|547|550|552|561|565|584|585',true)}
  			{if $marcField}
    			<label>{translate text='Note'}: </label>
    			{if $marcField|@count > 1}<br />{/if}
    			<span class="value">
      			{foreach from=$marcField item=field name=loop}
        			{$field|getvalue:'a'}<br>
      			{/foreach}
      			</span>
			{/if}

  			{assign var=marcField value=$marc->getFields('300')}
  			{if $marcField}
				<label>{translate text='Physical Description'}: </label>
				{if $marcField|@count > 1}<br />{/if}
				<span class="value">
      			{foreach from=$marcField item=field name=loop}
        			{foreach from=$field->getSubfields() item=subfield key=subcode}
          				{if $subcode != '6'}
            				{$subfield->getData()}
          				{/if}
        			{/foreach}
        			<br>
      			{/foreach}
      			</span>
			{/if}

  			{assign var=marcField value=$marc->getFields('970')}
  			{if $marcField}
    			<label>{translate text='Original Format'}: </label>
				{if $marcField|@count > 1}<br />{/if}
				<span class="value">
				{foreach from=$marcField item=field name=loop}
       				{if $smarty.foreach.loop.iteration eq 1}
       					{if $field|getvalue:'b' neq 'Electronic Resource'}{$field|getvalue:'b'}{/if}
       				{elseif $smarty.foreach.loop.iteration gt 1}
       					{if $field|getvalue:'b' neq 'Electronic Resource'}<br>{$field|getvalue:'b'}{/if}
       				{/if}
      			{/foreach}
    			</span>
    			<br />
  			{/if}

  			{assign var=852Field value=$marc->getFields('852')}
  			{if isset($852Field)}
    			{foreach from=$852Field item=field name=loop}
      				{if $smarty.foreach.loop.iteration lt 2}
        				{if $field|getvalue:'a' eq 'MiU'}
          					{if $field|getvalue:'h'}
            					<label>{translate text='Original Classification Number'}: </label>
            					<span class="value">{$field|getvalue:'h'}</span>
            					<br />
					        {/if}
      					{else}
        					{assign var=050Field value=$marc->getFields('050')}
        					{foreach from=$050Field item=field name=loop}
          						{if $smarty.foreach.loop.iteration lt 2}
            						{if $field|getvalue:'a'}
              							<label>{translate text='Original Classification Number'}: </label>
              							<span class="value">{$field|getvalue:'a'} {$field|getvalue:'b'}</span>
              							<br />
            						{/if}
          						{/if}
        					{/foreach}
      					{/if}
      				{/if}
    			{/foreach}
  			{/if}

  			{assign var=marcField value=$marc->getFields('020')}
   			{if $marcField}
     			<label>{translate text='ISBN'}: </label>
    	 		{if $marcField|@count > 1}<br />{/if}
	     		<span class="value">
				{foreach from=$marcField item=field name=loop}
        	 		{$field|getvalue:'a'}<br>
    	   		{/foreach}
	       		</span>
	       		<br />
   			{/if}

    		<label>{translate text='Locate a Print Version'}: </label>
    		<span class="value">
          	{if is_array($record.oclc)}
				<!-- title array -->
            	{foreach from=$record.oclc item=title loop=1 name=loop}
              		{if $smarty.foreach.loop.iteration lt 3}
              			<a href="http://www.worldcat.org/oclc/{$title}" onClick="pageTracker._trackEvent('outLinks', 'click', 'Find in a Library');">Find in a library</a><br>
              		{/if}
            	{/foreach}
          	{else}
				<!-- title non-array -->
            	{if $record.oclc}
             		<a href="http://www.worldcat.org/oclc/{$record.oclc}" onClick="pageTracker._trackEvent('outLinks', 'click', 'Find in a Library');">Find in a library</a>
            	{else}
            		Find in a library service is not available from this catalog. <a href="http://www.worldcat.org/" onClick="pageTracker._trackEvent('outLinks', 'click', 'Search Worldcat');" target="_blank">Search Worldcat</a>
            	{/if}
          	{/if}
          	</span>

			<button href="" id="lessdetails" onclick="return lessDetails();"><span>Less Details</span></button>
		</div>
	</div>
{*</div>*}

    {* <div class="headergrad"><!----></div> *}

	{* Display holdings *}

	{include file="Record/$subTemplate"}

	<ul id="recordTools" >


				{* email record link *}
				<li id="recordToolEmail">
					<a  class="linkeditem " onclick="ShowHideEmail('emailSingle','#recordToolEmail a'); return false;" href="#" class="mail dolightbox"  >Email this Record</a>
					{* <a href="javascript:ReverseDisplay('emailSingle')" >Email this record. Click to show/hide.</a> *}
					<div id="emailSingle" class="linkeditem" style="display: none">
    					<form class="emailtext" action="GET" action="/Search/SearchExport">
      						<input type="hidden" name="method" value="emailRecords">
      						<input type="hidden" name="id" value="{$id}">
      						<ul>
        						<li>
        							<label>To:(required)</label><input name="to" type="text">
        						</li>
        						<li>
          							<label>From:(required)</label><input name="from" type="text">
          						</li>
								<li>
            						<label>Message:</label><textarea  name="message"></textarea>
								</li>
        						<li>
									{*<input type="button" value="Send email" onclick="emailSelectedRecordsNoFancybox({literal}{{/literal}'handpicked': '{$id}'{literal}}{/literal}); return false;">*}
									<input type="button" value="Send email" onclick="emailSelectedRecordsMobile({literal}{{/literal}'handpicked': '{$id}'{literal}}{/literal}); return false;">
	    						</li>
	    					</ul>
						</form>
    					<div class="erErrorEmail"></div>
					</div>
				</li>
				<li class="recordToolLink">
					<a href="{$regular_url}{$smarty.server.REQUEST_URI}?mdetect=no" target="Mirlyn">{translate text='View Record in Regular Catalog'}</a>
				</li>
	</ul>
{*
	<script>{strip}
			{if $googleLinks}
		    	getGoogleBookInfoMobile('{$googleLinks}', '{$id}');
			{/if}
	{/strip}</script>
*}
	{*<div class="footergrad"><!----></div>*}

{* <a href="sms:number?body=This%20is%20the%20text">Text us</a> *}
{* format: author, title, first call number *}
