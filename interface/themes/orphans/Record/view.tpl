<script language="JavaScript" type="text/javascript" src="/services/Record/ajax.js"></script>
<script language="JavaScript" type="text/javascript" src="/js/googleLinks.js"></script>

<!--<div  id="login_to_text" style="display:none;" >
  <h3>Send record via text message</h3>
  <p>Texting is only available to logged-in users. Please <a id="login_link" href="">log in</a>.
  </p>
<script language="JavaScript" type="text/javascript">
  jq('#login_link').attr('href', loginLink());
</script>  
</div>
-->


<div id="bd" style="width: 100%; border: 1pt solid #eee">

{*
   <div id="start of left column container" class='yui-b' style="margin: 0px; padding: 0px; float: left; width: 17em;">
   
       <div class="box submenu">
          <h3>{translate text="Similar Items"}</h3>
     <!-- {$similarRecords} -->
           {if is_array($similarRecords)}
           <ul class="similar">
             {foreach from=$similarRecords item=similar}
             {if is_array($similar.title)}{assign var=similarTitle value=$similar.title.0}
             {else}{assign var=similarTitle value=$similar.title}{/if}  
             <li>
               <span class="{$similar.format|lower|replace:" ":""}">
               <a href="/Record/{$similar.id}">{$similarTitle}</a>
               </span>
               <span style="font-size: .8em">

               {if $similar.author}<br>By: {$similar.author.0}{/if}
               {if $similar.publishDate}<br>Published: ({$similar.publishDate.0}){/if}
               </span>
             </li>
             {/foreach}
           </ul>
           {else}
           Cannot find similar records
           {/if}
         </div>
          
         {if is_array($editions)}
           <div class="box submenu">
             <h4>{translate text="Other Editions"}</h4>
             <ul class="similar">
               {foreach from=$editions item=edition}
               <li>
                 <span class="{$similar.format|lower|replace:" ":""}">
                 <a href="/Record/{$edition.id}">{$edition.title}</a>
                 </span>
                 {$edition.edition}
                 {if $edition.publishDate}({$edition.publishDate}){/if}
               </li>
               {/foreach}
             </ul>
          </div>

         {/if}

         {assign var=marcField value=$marc->getField('245')}
         {assign var=title value=$marcField|getvalue:'a'}
         {if $marcField|getvalue:'b'}
           {assign var=title_b value = $marcField|getvalue:'b'}
           {assign var=title value = "$title $title_b"}
         {/if}
      
   </div> <!-- end of left column -->
*}   
   <div id="content" style="margin: 0px; padding: 0px; margin-left: 18px;">
     <div class="record">
       {if $lastsearch}
        <a href="{$lastsearch}" class="backtosearch"><img style="vertical-align: middle;" alt="" src="/static/umichwebsite/images/return.png">{translate text="Back to Search Results"}</a><br>
       {/if}
       
{*       <h3 class="SkipLink">Tools</h3>
       <ul class="ToolLinks">
         <li><a href="#" id="emailRecord" class="mail" onClick="pageTracker._trackEvent('recordActions', 'click', 'Email this');">{translate text="Email this"}</a></li>
         <li><a href="/Record/{$id}/Cite" class="cite" onClick="getLightbox('Record', 'Cite', '{$id}', null, '{translate text="Cite this"}'); return false;">{translate text="Cite this"}</a></li>
         <li><a class="endnotelink" href="/Search/SearchExport?handpicked={$id}&amp;method=ris" onClick="pageTracker._trackEvent('recordActions', 'click', 'Endnote');">Export to Endnote</a></li>
       </ul>
*}       
       <div class="recordnav">
         {if $previous}
         <a href="{$url}{$previous}" class="backtosearch"><img style="vertical-align: middle;" alt="" src="/static/umichwebsite/images/arrow_left.png"> {translate text="Previous record"}</a>
         {/if}
         {if $current}
         {translate text="$current"}
         {/if}
         {if $next}
         <a href="{$url}{$next}" class="backtosearch">{translate text="Next record"} <img style="vertical-align: middle;" alt="" src="/static/umichwebsite/images/arrow_right.png"></a>
         {/if}
       </div> 
             

         
       <!--
       <div>
       <ul class="tools">
        <li>
          <a href="/Record/{$id}/Cite" class="cite" onClick="getLightbox('Record', 'Cite', '{$id}', null, '{translate text="Cite this"}'); return false;">{translate text="Cite this"}</a>
        </li>

         <li>
           {if $username}
           <a href="/Record/{$id}/SMS" class="sms" onClick="getLightbox('Record', 'SMS', '{$id}', null, '{translate text="Text this"}'); return false;">{translate text="Text this"}</a></li>
           {else}
           <a href="#" class="sms" onClick="fillLightbox('login_to_text'); return false;">{translate text="Text this"}</a>
           {/if}
         </li>
                   
        -->

          <!--<li><a href="/Record/{$id}/Email" class="mail" onClick="getLightbox('Record', 'Email', '{$id}', null, '{translate text="Email this"}'); return false;">{translate text="Email this"}</a></li>-->
          <!-- <li><a href="#" class="mail" onClick="fillLightbox('email_help'); return false;;return false;">{translate text="Email this"}</a></li>              -->

          <!--<li><a target="RefWorksMain" href="http://www.refworks.com.proxy.lib.umich.edu/express/expressimport.asp?vendor=Univeristy+of+Michigan+Mirlyn2+Beta&amp;filter=MARC+Format&amp;database=All+MARC+Formats&amp;encoding=65001&amp;url={$url|escape:'url'}/Record/{$id}/Export%3Fstyle%3DREF">Export to Refworks</a></li>-->
          <!--<li><a href="/Record/{$id}/Export?style=endnote" class="export" onClick="showMenu('exportMenu'); return false;">{translate text="Import Record"}</a>
           </li>-->
          <!-- <li><a href="#" onclick="hideMenu('exportMenu'); fillLightbox('export_help');return false;">Export Record</a></li>                      -->
          <!--<ul class="menu" id="exportMenu">-->
          <!-- <li><a href="/Record/{$id}/Export?style=refworks">{translate text="Import to"} RefWorks</a></li> -->
          <!-- <li><a onclick="hideMenu('exportMenu');return false;" href="/Record/{$id}/Export?style=endnote">{translate text="Import to"} EndNote</a></li> -->
          <!-- <li><a onclick="hideMenu('exportMenu');return false;" href="/Record/{$id}/Export?style=zotero">{translate text="Import to"} Zotero</a></li> -->
          <!--<li><a href="#" onclick="hideMenu('exportMenu'); fillLightbox('refworks_help');return false;">{translate text="Import to"} RefWorks</a></li>
          <li><a href="#" onclick="hideMenu('exportMenu'); fillLightbox('endnote_help');return false;">{translate text="Import to"} Endnote</a></li>-->
          <!-- <li><a href="#" onclick="hideMenu('exportMenu'); fillLightbox('zotero_help');return false;">{translate text="Import to"} Zotero</a></li> -->
          <!--</ul>-->
          <!--</li>-->
          <!--<li id="saveLink"><a href="/Record/{$id}/Save" class="fav" onClick="getLightbox('Record', 'Save', '{$id}', null, '{translate text="Add to Favorites"}'); return false;">{translate text="Add to favorites"}</a></li> 
           <li id="savelink"><a href="#" onClick="fillLightbox('favorite_help'); return false;;return false;" class="fav">{translate text='Add to favorites'}</a></li>
            <script language="JavaScript" type="text/javascript">
            getSaveStatus('{$id}', 'saveLink');
            </script>
        -->
       <!--</ul>
      </div>-->

     <br>

     {if $error}<p class="error">{$error}</p>{/if}

     <!-- Display Title -->
       <div id="title_collection">
         <!-- Display Book Cover -->
         <div id=GoogleCover_{$id} style="display:none; margin: 10px; position: relative; float: left; border: 2px solid #ccc">
         </div>
         
         <!-- End Book Cover -->
         <div style="margin-left: 70px">
         {assign var=marcField value=$marc->getFields('245')}
         {foreach from=$marcField item=field name=loop}
           <h2>
           {foreach from=$field->getSubfields() item=subfield key=subcode  name=subloop}
             {if $subcode >= 'a' and $subcode <= 'z'}
             {$subfield->getData()}
             {/if}
           {/foreach}
           </h2> 
         {/foreach}
         </div>
       </div>
     <!-- End Title -->
                           
<table summary="This table displays bibliographic information about this specific book or series" class="citation" style="margin: 0px; margin-top: 2em; padding: 0px; *width=auto">
  {assign var=marcField value=$marc->getFields('785')}
  {if $marcField}
  <tr valign="top">
    <th>{translate text='New Title'}: </th>
    <td>
      {foreach from=$marcField item=field name=loop}
        <a href="/Search/Home?lookfor=%22{$field|getvalue:'s'}{$field|getvalue:'t'}%22&type=title&amp;inst={$inst}">{$field|getvalue:'s'}{$field|getvalue:'t'}</a><br>
      {/foreach}
    </td>
  </tr>
  {/if}

  {assign var=marcField value=$marc->getFields('780')}
  {if $marcField}
  <tr valign="top">
    <th>{translate text='Previous Title'}: </th>
    <td>
      {foreach from=$marcField item=field name=loop}
        <a href="/Search/Home?lookfor=%22{$field|getvalue:'s'}{$field|getvalue:'t'}%22&type=title&amp;inst={$inst}">{$field|getvalue:'s'}{$field|getvalue:'t'}</a><br>
      {/foreach}
    </td>
  </tr>
  {/if}



  
  {assign var=marcField value=$marc->getField('100')}
  {if $marcField}
  <tr valign="top">
    <th>{translate text='Main Author'}: </th>
    <td><a href="/Search/Home?lookfor=%22{$marcField|getvalue:'a'}{if $marcField|getvalue:'b'} {$marcField|getvalue:'b'}{/if}{if $marcField|getvalue:'c'} {$marcField|getvalue:'c'}{/if}{if $marcField|getvalue:'d'} {$marcField|getvalue:'d'}{/if}%22&amp;type=author&amp;inst={$inst}">{$marcField|getvalue:'a'}{if $marcField|getvalue:'b'} {$marcField|getvalue:'b'}{/if}{if $marcField|getvalue:'c'} {$marcField|getvalue:'c'}{/if}{if $marcField|getvalue:'d'} {$marcField|getvalue:'d'}{/if}</a></td>
  </tr>
  {/if}

  {assign var=marcField value=$marc->getField('110')}
  {if $marcField}
  <tr valign="top">
    <th>{translate text='Corporate Author'}: </th>
    <td><a href="/Search/Home?lookfor=%22{$marcField|getvalue:'a'|escape:'uri'}%22&amp;type=author&amp;inst={$inst}">{$marcField|getvalue:'a'}</a></td>
  </tr>
  {/if}

  {assign var=marcField value=$marc->getFields('700')}
  {if $marcField}
  <tr valign="top">
    <th>{translate text='Other Authors'}: </th>
    <td>
      {foreach from=$marcField item=field name=loop}
        <a href="/Search/Home?lookfor=%22{$field|getvalue:'a'}{if $field|getvalue:'b'} {$field|getvalue:'b'}{/if}{if $field|getvalue:'c'} {$field|getvalue:'c'}{/if}{if $field|getvalue:'d'} {$field|getvalue:'d'}{/if}%22&amp;type=author&amp;inst={$inst}">{$field|getvalue:'a'} {$field|getvalue:'b'} {$field|getvalue:'c'} {$field|getvalue:'d'}</a>{if !$smarty.foreach.loop.last}, {/if}
      {/foreach}
    </td>
  </tr>
  {/if}

  <!-- <tr valign="top">
    <th>{translate text='Format'}: </th>
    <td><span class="{$recordFormat}">{$recordFormat}</span></td>
  </tr> -->
  {assign var=lang value=$recordLanguage}
  {if $recordLanguage}
  <tr valign="top">
    <th>{translate text='Language(s)'}: </th>
    <td>
    {foreach from=$lang item=field name=loop}
    {if $smarty.foreach.loop.first}{$field}{else}; {$field}{/if} 
    {/foreach}
   </td>
  </tr>
  {/if}

  {assign var=marcField value=$marc->getFields('26[04]', true)}
  {if $marcField}
  <tr valign="top">
    <th>{translate text='Published'}: </th>
    <td>
      {foreach from=$marcField item=field name=loop}
        {$field|getvalue:'a'} {$field|getvalue:'b'} {$field|getvalue:'c'}<br>
      {/foreach}
    </td>
  </tr>
  {/if}

  {assign var=marcField value=$marc->getFields('250')}
  {if $marcField}
  <tr valign="top">
    <th>{translate text='Edition'}: </th>
    <td>
      {foreach from=$marcField item=field name=loop}
        {$field|getvalue:'a'}<br>
      {/foreach}
    </td>
  </tr>
  {/if}

  {assign var=marcField value=$marc->getFields('440')}
  {if $marcField}
  <tr valign="top">
    <th>{translate text='Series'}: </th>
    <td>
      {foreach from=$marcField item=field name=loop}
        <a href="/Search/Home?lookfor=%22{$field|getvalue:'a'|escape}%22&amp;type=series&amp;inst={$inst}">{$field|getvalue:'a'}</a><br>
      {/foreach}
    </td>
  </tr>
  {/if}


{*
  {assign var=marcField value=$marc->getFields('975')}
  {if $marcField}
  <tr valign="top">
    <th>{translate text='Academic Discipline'}: </th>
    <td>
        {foreach from=$marcField item=field name=loop}
          {assign var=hlba value=$field->getSubfield('a')}
          {assign var=hlbb value=$field->getSubfield('b')}
          {assign var=hlba value=$hlba->getData()}
          {assign var=hlbb value=$hlbb->getData()}
          <a href="/Search/Home?lookfor=&amp;type=&amp;filter[]=hlb_both:%22{$hlba|escape:"url"}%22&amp;inst={$inst}">{$hlba|escape}</a>
          &gt;
          <a href="/Search/Home?lookfor=&amp;type=&amp;filter[]=hlb_both:%22{$hlbb|escape:"url"}%22&amp;inst={$inst}">{$hlbb|escape}</a>
          <br>
        {/foreach}

    </td>
  </tr>
  {/if}
*}

  {assign var=marcField value=$marc->getFields('600|610|630|650|651|655',1)}
  {if $marcField}
  <tr valign="top">
    <th>{translate text='Subjects'}: </th>
    <td>
        {foreach from=$marcField item=field name=loop}
          {assign var=subject value=""}
          {foreach from=$field->getSubfields() item=subfield key=subcode  name=subloop}
           {if $subcode >= 'a'}
            {if $subject} &gt; {/if}
            {assign var=subfield value=$subfield->getData()}
            {assign var=subject value="$subject $subfield"}
            <a href="/Search/Home?lookfor=%22{$subject|escape}%22&amp;type=subject&amp;inst={$inst}">{$subfield}</a>
           {/if}
          {/foreach}
          <br>
        {/foreach}
    </td>
  </tr>
  {/if}

  {assign var=marcField value=$marc->getFields('500|501|521|525|526|530|547|550|552|561|565|584|585',true)}
  {if $marcField}
  <tr valign="top">
    <th>{translate text='Note'}: </th>
    <td>
      {foreach from=$marcField item=field name=loop}
        {$field|getvalue:'a'}<br>
      {/foreach}
    </td>
  </tr>
  {/if}

  {assign var=marcField value=$marc->getFields('300')}
  {if $marcField}
  <tr valign="top">
    <th>{translate text='Physical Description'}: </th>
    <td>
      {foreach from=$marcField item=field name=loop}
        {foreach from=$field->getSubfields() item=subfield key=subcode}
          {if $subcode != '6'}
            {$subfield->getData()}
          {/if}
        {/foreach}
        <br>
      {/foreach}
    </td>
  </tr>
  {/if}

  {assign var=marcField value=$marc->getFields('970')}
  {if $marcField}
  <tr valign="top">
    <th>{translate text='Original Format'}: </th>
    <td>
      {foreach from=$marcField item=field name=loop}
       {if $smarty.foreach.loop.iteration eq 1}
       {if $field|getvalue:'b' neq 'Electronic Resource'}{$field|getvalue:'b'}{/if}
       {elseif $smarty.foreach.loop.iteration gt 1}
       {if $field|getvalue:'b' neq 'Electronic Resource'}<br>{$field|getvalue:'b'}{/if}
       {/if}
      {/foreach}
    </td>
  </tr>
  {/if}

  {assign var=852Field value=$marc->getFields('852')}
  {if isset($852Field)}
    {foreach from=$852Field item=field name=loop}
      {if $smarty.foreach.loop.iteration lt 2}
        {if $field|getvalue:'a' eq 'MiU'}
          {if $field|getvalue:'h'}
            <tr valign="top">
            <th>{translate text='Original Classification Number'}: </th>
            <td>
            {$field|getvalue:'h'}
            </td>
            </tr>
          {/if}
      {else}
        {assign var=050Field value=$marc->getFields('050')}
        {foreach from=$050Field item=field name=loop}
          {if $smarty.foreach.loop.iteration lt 2}
            {if $field|getvalue:'a'}
              <tr valign="top">
              <th>{translate text='Original Classification Number'}: </th>
              <td>
              {$field|getvalue:'a'} {$field|getvalue:'b'}
              </td>
              </tr>
            {/if}
          {/if}
        {/foreach}
      {/if}
      {/if}
    {/foreach}
  {/if}

  {assign var=marcField value=$marc->getFields('020')}
   {if $marcField}
   <tr valign="top">
     <th>{translate text='ISBN'}: </th>
     <td>
       {foreach from=$marcField item=field name=loop}
         {$field|getvalue:'a'}<br>
       {/foreach}
     </td>
   </tr>
   {/if}

  <tr valign="top">
    <th>{translate text='Locate a Print Version'}: </th>
    <td>
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
            {else} Find in a library service is not available from this catalog. <a href="http://www.worldcat.org/" onClick="pageTracker._trackEvent('outLinks', 'click', 'Search Worldcat');" target="_blank">Search Worldcat</a>
            {/if}
          {/if}
    </td>
  </tr>


{*
  {assign var=marcField value=$marc->getFields('856')}
  {if $marcField}
  <tr valign="top">
    <th>{translate text='Online Access'}: </th>
    <td>
      {foreach from=$marcField item=field name=loop}
        {if $proxy}
        <a href="{$proxy}/login?url={$field|getvalue:'u'}">{if $field|getvalue:'z'}{$field|getvalue:'z'}{else}{$field|getvalue:'u'}{/if}</a><br>
        {else}
        <a href="{$field|getvalue:'u'}">{if $field|getvalue:'z'}{$field|getvalue:'z'}{else}{$field|getvalue:'u'}{/if}</a><br>
        {/if}
      {/foreach}
    </td>
  </tr>
  {/if}
*}
  <!-- url to record in legacy system -->
<!-- commented by jjyork 3/31/09  {if $recordURL}
  <tr valign="top">
    <th>{translate text='URL'}: </th>
    <td>
      <a href="{$recordURL}{$id}" target="Mirlyn">Display record in Mirlyn</a> 
    </td>
  </tr>
  {/if} 
-->
  <!-- url to xserver holdings -->
<!-- commented by jjyork 3/31/09  {if $holdingsURL}
  <tr valign="top">
    <th>{translate text='xserver holdings'}: </th>
    <td>
      <a href="{$holdingsURL}{$id}" target="Mirlyn">Display holdings from Mirlyn xserver</a>       
    </td>
  </tr>
  {/if}
--> 
</table> 

<!-- Availability set apart from table-->

<div id="accessLinks">
  <h3>{translate text='Viewability'}: </h3>
  <ul>
    
  {if $mergedItems}
    {foreach from=$mergedItems item=item}
      <li><a href="{$item.itemURL}">{$item.usRightsString} {if $item.enumcron}<span class="IndItem">{$item.enumcron}</span>{/if}</a>
        <em>(original from {$item.orig})</em>
    {/foreach}
  {else}
    {assign var=ninetydays value="90 days"|DateInterval::createFromDateString}
    {assign var=marcField value=$marc->getFields('974')}
    {if $marcField}

        {foreach from=$marcField item=field name=loop}
            {assign var=url value=$field->getSubfield('u')}
            {assign var=url value=$url->getData()}
           <!-- {assign var=nmspace value=$url|regex_replace:"/\.\d+/":""} -->
            {assign var=nmspace value=$url|regex_replace:"/\..*/":""}
            <li class="{$field|getvalue:'r'}">
              <a target="ht_pageturner_{$url}" href="https://hdl.handle.net/2027/{$url}"
              {if $field|getvalue:'r' eq 'pd'}
                class="fulltext">Full view
              {elseif $field|getvalue:'r' eq 'pdus' && $session->get('inUSA')}
                class="fulltext">Full vie
              {elseif $field|getvalue:'r' eq 'world'}
                class="fulltext">Full view
              {elseif $field|getvalue:'r' eq 'ic-world'}
                class="fulltext">Full view
              {elseif $field|getvalue:'r' eq 'und-world'}
                class="fulltext">Full view
      {elseif $field|getvalue:'r' eq 'cc-by'}class="fulltext">Full view
      {elseif $field|getvalue:'r' eq 'cc-by-nd'}class="fulltext">Full view
      {elseif $field|getvalue:'r' eq 'cc-by-nc-nd'}class="fulltext">Full view
      {elseif $field|getvalue:'r' eq 'cc-by-nc'}class="fulltext">Full view
      {elseif $field|getvalue:'r' eq 'cc-by-nc-sa'}class="fulltext">Full view
      {elseif $field|getvalue:'r' eq 'cc-by-sa'}class="fulltext">Full view
      {elseif $field|getvalue:'r' eq 'orphcand'}class="searchonly orphcand">Orphan Works Candidate
      {elseif $field|getvalue:'r' eq 'orphan'}class="fulltext orphcand">Full view for valid users (orphan)
              {else}class="searchonly">Limited (search-only)
          {/if}
        
         <span class="IndItem">{if $field|getvalue:'z'}{$field|getvalue:'z'}{else}{/if}</span></a> 
         <em>
         {if $nmspace eq 'mdp'} (original from University of Michigan) 
           {elseif $nmspace eq 'miua'} (original from University of Michigan)
           {elseif $nmspace eq 'miun'} (original from University of Michigan)
           {elseif $nmspace eq 'wu'} (original from University of Wisconsin)
           {elseif $nmspace eq 'inu'} (original from Indiana University)
           {elseif $nmspace eq 'uc1'} (original from University of California)
           {elseif $nmspace eq 'uc2'} (original from University of California)
           {elseif $nmspace eq 'pst'} (original from Penn State University)
           {elseif $nmspace eq 'umn'} (original from University of Minnesota)
           {elseif $nmspace eq 'nnc1'} (original from Columbia University)
           {elseif $nmspace eq 'nnc2'} (original from Columbia University)
           {elseif $nmspace eq 'nyp'} (original from New York Public Library)
           {elseif $nmspace eq 'uiuo'} (original from University of Illinois)
           {elseif $nmspace eq 'njp'} (original from Princeton University)
           {elseif $nmspace eq 'yale'} (original from Yale University)
           {elseif $nmspace eq 'chi'} (original from University of Chicago)
           {elseif $nmspace eq 'coo'} (original from Cornell University)
           {elseif $nmspace eq 'ucm'} (original from Universidad Complutense de Madrid)
           {elseif $nmspace eq 'loc'} (original from Library of Congress)
           {elseif $nmspace eq 'ien'} (original from Northwestern University)
           {elseif $nmspace eq 'hvd'} (original from Harvard University)
           {elseif $nmspace eq 'uva'} (original from University of Virginia)
           {elseif $nmspace eq 'dul1'} (original from Duke University)
           {elseif $nmspace eq 'ncs1'} (original from North Carolina State University)
           {elseif $nmspace eq 'nc01'} (original from University of North Carolina)
           {else}
         {/if}
         </em>



     {*    {assign var=expdate value=$field|getvalue:'o'} *}
         {assign var=expdate value=$field|getvalue:'d'}
         {if $expdate}
           <div style="margin-left: 32px" class="IndItem">
           This volume will be identified as an orphan work on {$expdate|dateadd:90:'%B %e, %Y'}
           (using rights=opd and date=974$$d for testing only)
 
           </div>
         {/if}
         
         
         </li>
        {/foreach}
    {/if} {* $marcField *}
  {/if} {* $mergedItems  *}
  </ul>
</div>



          <!-- Display Tab Navigation -->
<!--          <div id="tabnav">
            <ul>
              <li{if $tab == 'Holdings' || $tab == 'Home'} class="active"{/if}>
                <a href="/Record/{$id}/Holdings" class="first"><span></span>{translate text='Holdings'}</a>
              </li>
              <li{if $tab == 'Description'} class="active"{/if}>
                <a href="/Record/{$id}/Description" class="first"><span></span>{translate text='Description'}</a>
              </li>
              {if $marc->getFields('505')}
              <li{if $tab == 'TOC'} class="active"{/if}>
                <a href="/Record/{$id}/TOC" class="first"><span></span>{translate text='Table of Contents'}</a>
              </li>
              {/if}
-->
<!--              <li{if $tab == 'UserComments'} class="active"{/if}>
                <a href="/Record/{$id}/UserComments" class="first"><span></span>{translate text='Comments'}</a>
              </li>
-->
              <!-- {if $hasReviews}
              <li{if $tab == 'Reviews'} class="active"{/if}>
                <a href="/Record/{$id}/Reviews" class="first"><span></span>{translate text='Reviews'}</a>
              </li>
              {/if} -->
<!--              {if $hasExcerpt}
              <li{if $tab == 'Excerpt'} class="active"{/if}>
                <a href="/Record/{$id}/Excerpt" class="first"><span></span>{translate text='Excerpt'}</a>
              </li>
              {/if}
              <li{if $tab == 'Details'} class="active"{/if}>
                <a href="/Record/{$id}/Details" class="first"><span></span>{translate text='MARC View'}</a>
              </li>
            </ul>
          </div>
-->
 <!-- End id=tabnav -->
          

 
             </div> <!-- end record -->
   </div> <!-- end of content -->

</div>
<script>
 {if $googleLinks}
    getGoogleBookInfo('{$googleLinks}', '{$id}');
  {/if}
</script>


