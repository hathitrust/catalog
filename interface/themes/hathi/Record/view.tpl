<script language="JavaScript" type="text/javascript" src="{$path}/js/googleLinks.js"></script>


<div id="bd" style="width: 100%">
   <div id="start_of_left_column_container" class='yui-b' style="margin: 0px; padding: 0px; float: left; width: 17em;">
       <div class="box submenu">
          <h3>{translate text="Similar Items"}</h3>
           {if is_array($similarRecords)}
           <ul class="similar">
             {foreach from=$similarRecords item=similar}
             {if is_array($similar.title)}{assign var=similarTitle value=$similar.title.0}
             {else}{assign var=similarTitle value=$similar.title}{/if}
             <li>
               <span class="{$similar.format|lower|replace:" ":""}">
               <a href="{$url}/Record/{$similar.id}">{$similarTitle}</a>
               </span>
               <span style="font-size: .9em">

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
                 <a href="{$url}/Record/{$edition.id}">{$edition.title}</a>
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

   <div id="content" style="margin: 0px; padding: 0px; margin-left: 19em;">
     <div class="record">
       {if $lastsearch}
       <a href="{$lastsearch|regex_replace:"/&/":"&amp;"}" class="backtosearch">{translate text="Back to Search Results"}</a><br>
       {/if}

       <h3 class="SkipLink">Tools</h3>
       <ul class="ToolLinks">
         <li><a href="/Record/{$id|escape:"url"}/Cite" class="cite" onClick="getLightbox('Record', 'Cite', '{$id|escape:"url"}', null, '{translate text="Cite this"}'); return false;">{translate text="Cite this"}</a></li>
         <li><a class="endnotelink" href="/Search/SearchExport?handpicked={$id|escape:"url"}&amp;method=ris" onClick="pageTracker._trackEvent('recordActions', 'click', 'Endnote');">Export citation file</a></li>
       </ul>

       <div class="recordnav">
         {if $previous}
         <a href="{$url}{$previous|regex_replace:"/&/":"&amp;"}" class="goto-previous-record">{translate text="Previous record"}</a>
         {/if}
         {if $current}
         {translate text="$current"}
         {/if}
         {if $next}
         <a href="{$url}{$next|regex_replace:"/&/":"&amp;"}" class="goto-next-record">{translate text="Next record"}</a>
         {/if}
       </div>



       <!--
       <div>
       <ul class="tools">
        <li>
          <a href="{$url}/Record/{$id|escape:"url"}/Cite" class="cite" onClick="getLightbox('Record', 'Cite', '{$id}', null, '{translate text="Cite this"}'); return false;">{translate text="Cite this"}</a>
        </li>

         <li>
           {if $username}
           <a href="{$url}/Record/{$id|escape:"url"}/SMS" class="sms" onClick="getLightbox('Record', 'SMS', '{$id}', null, '{translate text="Text this"}'); return false;">{translate text="Text this"}</a></li>
           {else}
           <a href="#" class="sms" onClick="fillLightbox('login_to_text'); return false;">{translate text="Text this"}</a>
           {/if}
         </li>

        -->


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
         {if $record.vtitle}
          <h2>{$record.vtitle}</h2>
         {/if}

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
        <a href="{$url}/Search/Home?lookfor=%22{$field|getvalue:'s'}{$field|getvalue:'t'}%22&type=title&amp;inst={$inst}">{$field|getvalue:'s'}{$field|getvalue:'t'}</a><br>
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
        <a href="{$url}/Search/Home?lookfor=%22{$field|getvalue:'s'}{$field|getvalue:'t'}%22&type=title&amp;inst={$inst}">{$field|getvalue:'s'}{$field|getvalue:'t'}</a><br>
      {/foreach}
    </td>
  </tr>
  {/if}




  {assign var=marcField value=$marc->getField('100')}
  {if $marcField}
  <tr valign="top">
    <th>{translate text='Main Author'}: </th>
    <td><a href="{$url}/Search/Home?lookfor=%22{$marcField|getvalue:'a'}{if $marcField|getvalue:'b'} {$marcField|getvalue:'b'}{/if}{if $marcField|getvalue:'c'} {$marcField|getvalue:'c'}{/if}{if $marcField|getvalue:'d'} {$marcField|getvalue:'d'}{/if}%22&amp;type=author&amp;inst={$inst}">{$marcField|getvalue:'a'}{if $marcField|getvalue:'b'} {$marcField|getvalue:'b'}{/if}{if $marcField|getvalue:'c'} {$marcField|getvalue:'c'}{/if}{if $marcField|getvalue:'d'} {$marcField|getvalue:'d'}{/if}</a></td>
  </tr>
  {/if}

  {assign var=marcField value=$marc->getField('110')}
  {if $marcField}
  <tr valign="top">
    <th>{translate text='Corporate Author'}: </th>
    <td><a href="{$url}/Search/Home?lookfor=%22{$marcField|getvalue:'a'|escape:'uri'}%22&amp;type=author&amp;inst={$inst}">{$marcField|getvalue:'a'}</a></td>
  </tr>
  {/if}

  {assign var=marcField value=$marc->getFields('700')}
  {assign var=subfieldlist value=','|explode:'a,b,c,d,e'}
  {if $marcField}
  <tr valign="top">
    <th>Related Names: </th>
    <td>
      {foreach from=$marcField item=field name=loop}
        {foreach from=$subfieldlist item=subfield name=subfield_loop}
          {assign var=subval value=$field|getvalue:$subfield}	  
          {if !empty($subval)}
	    {assign var="subfield_$subfield" value=$subval|regex_replace:"/,\$/":""}
	  {else}
	    {assign var="subfield_$subfield" value=""}
          {/if}
         {/foreach}

	 
        <a href="{$url}/Search/Home?lookfor=%22{$subfield_a $subfield_b $subfield_c $subfield_d}%22&amp;type=author&amp;inst={$inst}">{$subfield_a} {$subfield_b} {$subfield_c} {$subfield_d}</a>{if !empty($subfield_e)}, {$subfield_e}{/if}{if (!$smarty.foreach.loop.last)}, {/if}


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
        <a href="{$url}/Search/Home?lookfor=%22{$field|getvalue:'a'|escape}%22&amp;type=series&amp;inst={$inst}">{$field|getvalue:'a'}</a><br>
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
          <a href="{$url}/Search/Home?lookfor=&amp;type=&amp;filter[]=hlb_both:%22{$hlba|escape:"url"}%22&amp;inst={$inst}">{$hlba|escape}</a>
          &gt;
          <a href="{$url}/Search/Home?lookfor=&amp;type=&amp;filter[]=hlb_both:%22{$hlbb|escape:"url"}%22&amp;inst={$inst}">{$hlbb|escape}</a>
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
            <a href="{$url}/Search/Home?lookfor=%22{$subject|escape}%22&amp;type=subject&amp;inst={$inst}">{$subfield}</a>
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

  {if $mdl}
    <!-- mdl -->
    {assign var=marcField value=$marc->getFields('540')}
    {if $marcField}
    <tr valign="top">
      <th>{translate text='Use and Reproduction Note'}: </th>
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



</table>

<!-- Availability set apart from table-->


<!--

{$record.ht_rightscode|@print_r:true}

-->

<div id="accessLinks">
  <h3>{translate text='Viewability'}: </h3>
  <ul>

  {if $mergedItems}
    {foreach from=$mergedItems item=item}
      <li><a href="{$item.itemURL}">{$item.usRightsString} {if $item.enumcron}<span class="IndItem">{$item.enumcron}</span>{/if}</a>
        <em>(original from {$item.orig})</em>
    {/foreach}
  {else}

  {assign var="htjson" value=$ru->items_from_json($record)}
  {assign var="record_is_tombstone" value=$ru->record_is_tombstone($record)}

   {foreach from=$htjson item=e}
     {assign var=ld value=$ru->ht_link_data_from_json($e)}
     {if $record_is_tombstone || !($ld.is_tombstone)}
      <li>
        {if $record_is_tombstone}
          This item is no longer available (<a href="//hdl.handle.net/2027/{$ld.handle}" class="rights-{$ld.rights_code}">why not?</a>)
           {elseif $ld.is_fullview}
            <a href="//hdl.handle.net/2027/{$ld.handle}" data-hdl="{$ld.handle}"  class="rights-{$ld.rights_code} fulltext">Full view<span class="IndItem">{$ld.enumchron}</span></a>
          {else}
            <a href="//hdl.handle.net/2027/{$ld.handle}" data-hdl="{$ld.handle}" class="rights-{$ld.rights_code} searchonly">Limited (search only)<span class="IndItem">{$ld.enumchron}</span></a>
          {/if}
          <em class="original_from">(original from {$ld.original_from})</em>
      </li>
      {/if}
    {/foreach}

  {/if} {* $mergedItems  *}
  </ul>
</div>





             </div> <!-- end record -->
   </div> <!-- end of content -->

</div>
 {if $googleLinks}
<script type="text/javascript">
    getGoogleBookInfo('{$googleLinks}', '{$id}');
</script>
  {/if}


