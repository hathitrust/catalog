<link rel="stylesheet" type="text/css" media="screen" href="/static/MTagger/mtagger.css">
<script language="JavaScript" type="text/javascript" src="{$path}/services/Record/ajax.js"></script>
<script language="JavaScript" type="text/javascript" src="{$path}/js/googleLinks.js"></script>
<script language="JavaScript" type="text/javascript" src="http://www.lib.umich.edu/mtagger/js/jquery.mtagger.js"></script>
<script language="Javascript" type="text/javascript"> jQuery.mtagger.base +=  '/static/MTagger';</script>


<div  id="login_to_text" style="display:none;" >
  <h3>Send record via text message</h3>
  <p>Texting is only available to logged-in users. Please <a id="login_link" href="">log in</a>.
  </p>
<script language="JavaScript" type="text/javascript">
  jq('#login_link').attr('href', loginLink());
</script>  
</div>


<div id="bd" style="width: 100%;">
  {include file="tempbox.tpl"}      
  
   <div id="recordleftcol" class='yui-b' style="margin: 0px; padding: 0px; width: 17em;">
 
     {if $lc_subjects}
       <div class="box submenu">
             <h4>{translate text="Subjects (LCSH)"}</h4>
             <ul class="similar">
                {foreach from=$lc_subjects item=subject name=lcsh}
                  {assign var=subject_display value=" -- "|implode:$subject}
                  {assign var=subject_search value=" "|implode:$subject}
                  <li><a class="clickpostlog" ref="recsubjectLeft|{$record.id}|{$subject_search|escape}|{$smarty.foreach.lcsh.iteration}" href="{$url}/Search/Home?lookfor={$subject_search|escape}&amp;type=subject2">{$subject_display}</a></li>
                {/foreach}
             </ul>
       </div>
     {/if}

     {if is_array($editions)}
       <div class="box submenu">
             <h4>{translate text="Other Editions"}</h4>
             <ul class="similar">
               {foreach from=$editions item=edition name=edition}
               <li>
                 <a class="clickpostlog" ref="|receditionLeft|{$record.id}|{$edition.id}|{$smarty.foreach.edition.iteration}" href="{$url}/Record/{$edition.id}">{$edition.title}</a>
                 <span style="font-size: .8em">
                 {$edition.edition}
                 {if $edition.publishDate}<br>Published: {$edition.publishDate}{/if}
                 {if $edition.format}<br>Format: {", "|implode:$edition.format}{/if}
                 </span>
               </li>
               {/foreach}
             </ul>
       </div>
     {/if}

       <div class="box submenu">
          <h4>{translate text="Similar Items"}</h4>
           {if is_array($similarRecords)}
           <ul class="similar">
             {foreach from=$similarRecords item=similar name=similar}
             {if is_array($similar.title)}{assign var=similarTitle value=$similar.title.0}
             {else}{assign var=similarTitle value=$similar.title}{/if}  
             <li>
               <a class="clickpostlog" ref="recsimilarLeft|{$record.id}|{$similar.id}|{$smarty.foreach.similar.iteration}" href="{$url}/Record/{$similar.id}">{$similarTitle}</a>
               <span style="font-size: .8em">
               {if $similar.author}<br>By: {"; "|implode:$similar.author}{/if}
               {if $similar.publishDate}<br>Published: {$similar.publishDate.0}{/if}
               {if $similar.format}<br>Format: {", "|implode:$similar.format}{/if}
               </span>
             </li>
             {/foreach}
           </ul>
           {else}
           Cannot find similar records
           {/if}
       </div>
          
       <div class="box submenu tagcloud">
       </div>

       {assign var=marcField value=$marc->getField('245')}
       {assign var=title value=$marcField|getvalue:'a'}
       {if $marcField|getvalue:'b'}
         {assign var=title_b value = $marcField|getvalue:'b'}
         {assign var=title value = "$title $title_b"}
       {/if}
         <script type="text/javascript">
           jQuery('.tagcloud').addCloud( {ldelim}
             url:'{$path}/Record/{$id}',
           title:'{$title|escape:"url"}',
       separator: ' ',
        tag_link: '<h4><img src="http://www.lib.umich.edu/mtagger/img/tag(45deg).gif" alt="MTag this Page" class="mtagger_mtag"/>Tag this Page</h4>' {rdelim});
         </script>
   </div> <!-- end of recordleftcol -->
   

  <div id="content" class="recordcontent" >
    <div class="record">
      <div id="recordheader">
        {if $message}{$message}<br>{/if}
                
          {if isset($lastsearch)}
            {if $lastsearch == 'tags'}
              <a ref="backtotags|{$lasttagsearch.url}" href="{$lasttagsearch.url}" class="clickpostlog backtosearch"><img style="vertical-align: middle;" alt="Back" src="/static/umichwebsite/images/return.png">Back to {$lasttagsearch.description}</a><br>
            {else}
              <a ref="backtosearch" href="{$lastsearch}" class="clickpostlog backtosearch"><img style="vertical-align: middle;" alt="Back to results" src="/static/umichwebsite/images/return.png">{translate text="Back to Results"}</a><br>
            
              <div class="recordnav">
                {if $previous}
                  <a ref="prevrec" href="{$url}{$previous}" class="clickpostlog backtosearch"><img style="vertical-align: middle;" alt="Previous result" src="/static/umichwebsite/images/arrow_left.png"> {translate text="Previous record"}</a>
                {/if}
                {if $current}
                  {translate text="$current"}
                {/if}
                {if $next}
                  <a ref="nextrec" href="{$url}{$next}" class="clickpostlog backtosearch">{translate text="Next record"} <img style="vertical-align: middle;" alt="Next result" src="/static/umichwebsite/images/arrow_right.png"></a>
                {/if}
              </div> <!-- end of recordnav -->
            {/if}
          {/if}  
        <br>
      </div> <!-- end of recordheader -->
     
      <div id="recordTools" style="margin-bottom: 1em;">
        <ul class="tools">
          <li><a href="{$url}/Record/{$id}/Cite" class="cite" onClick="getLightbox('Record', 'Cite', '{$id}', null, '{translate text="Cite this"}'); return false;">{translate text="Cite this"}</a></li>
          <!-- text message only if we have a user -->
          <li>
            {if $username}
              <a href="#smsSingle" class="sms dolightbox">{translate text="Text this"}</a>
            {else}
              <a href="#login_to_text" class="sms dolightbox">{translate text="Text this"}</a>
            {/if}
          </li>
          <li><a href="#emailSingle" class="mail dolightbox">Email</a></li>
          <li><a href="#exportRefworksSingle" class="refworkslink dolightbox" target="refworks">Export to refworks</a>
          <li><a class="endnotelink" href="{$url}/Search/SearchExport?handpicked={$id}&amp;method=ris">Export to Endnote</a></li>
          <li>
            {if $inTempItems}
              <input type="checkbox" id="inSelected_{$record.id}" onclick="selectedToggle(this)" class="selectedCheckbox" value="on" checked="checked">&nbsp;<label class="selectedCheckboxLabel" for="inSelected_{$record.id}">Selected</label>
            {else}
              <input type="checkbox" id="inSelected_{$record.id}" onclick="selectedToggle(this)" class="selectedCheckbox" value="on">&nbsp;<label class="unselectedCheckboxLabel" for="inSelected_{$record.id}">Select</label>
            {/if}
          </li>
          <li>
            {if $tagobj->isFavorite($record.id)}
              <input type="checkbox" id="inFavorites_{$record.id}" onclick="favoritesToggle(this)" class="favoritesCheckbox" value="on"  checked="checked">&nbsp;<label class="favoritedCheckboxLabel" for="inFavorites_{$record.id}"><span class="favorites">Favorite</label>                     
            {else}
              <input type="checkbox" id="inFavorites_{$record.id}" onclick="favoritesToggle(this)" class="favoritesCheckbox" value="on">&nbsp;<label class="unfavoritedCheckboxLabel" for="inFavorites_{$record.id}">Add to <span class="favorites">Favorites</span></label>
            {/if}
        </ul>
      </div> <!-- end of recordTools -->

      <br>

      {if $error}
      <p class="error">{$error}</p>
      {/if}

      
      
      <!-- begin actual metadata -->
 
        <!-- Display Title -->
        <div id="title_collection" style="margin-top: 2em; margin-bottom: 2em;">
          <!-- Display Book Cover -->
          <div class="googleCoverColumn">
            <div id="GoogleCover_{$record.id}" class="googleCover">
              <img src="/images/noCover2.gif">
            </div>
          </div>
          <!-- End Book Cover -->
          
          <div style="margin-left: 80px">
            {assign var=marcField value=$marc->getFields('245')}
            {foreach from=$marcField item=field name=loop}
            <h1><abbr class="unapi-id" title="urn:bibnum:{$record.id}">
              {foreach from=$field->getSubfields() item=subfield key=subcode  name=subloop}
                {if $subcode >= 'a' and $subcode <= 'z'}
                  {$subfield->getData()}
                {/if}
              {/foreach}
              </abbr>
           </h1> 
           {/foreach}
          </div>
        </div> <!-- End Title -->
            
                           
        <table class="citation" style="margin: 0px; margin-top: 2em; padding: 0px; *width=auto">
        {assign var=marcField value=$marc->getFields('785')}
        {if $marcField}
          <tr valign="top">
            <th>{translate text='New Title'}: </th>
            <td>
              {foreach from=$marcField item=field name=loop}
                <a class="clickpostlog" ref="recnewtitle" href="{$url}/Search/Home?lookfor=%22{$field|getvalue:'s'}{$field|getvalue:'t'}%22&amp;type=title">{$field|getvalue:'s'}{$field|getvalue:'t'}</a><br>
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
                <a class="clickpostlog" ref="recprevtitle" href="{$url}/Search/Home?lookfor=%22{$field|getvalue:'s'}{$field|getvalue:'t'}%22&amp;type=title">{$field|getvalue:'s'}{$field|getvalue:'t'}</a><br>
              {/foreach}
            </td>
          </tr>
        {/if}
  
        {if $record.author}
          <tr valign="top">
            <th>{translate text='Main Author'}: </th>
            <td>
            {foreach from=$record.author item=author}
              <a class="clickpostlog" ref="recmainauthor" href="{$url}/Search/Home?lookfor=%22{$author}%22&amp;type=author">{$author}</a>
              <br>
            {/foreach}
            </td>
          </tr>
        {/if}
 
        {assign var=marcField value=$marc->getFields('700|710|711',1)}
        {if $marcField}
          <tr valign="top">
            <th>{translate text='Contributors'}: </th>
            <td>
              {foreach from=$marcField item=field name=loop}
                {assign var=fieldText value=''}
                {assign var="cauthor" value=''}
                {assign var="cauthorDisp" value=''}
                
                {foreach from=$field->getSubfields() item=subfield key=subcode  name=subloop}
                  {assign var="lastcode" value='b'}
                  {if $field->getTag() == '700'}
                    {assign var="lastcode" value='d'}
                  {/if}
                  
                  {if $subcode >= 'a' and $subcode <= 'z'}
                    {assign var="new" value=$subfield->getData()}
                    {assign var="cauthorDisp" value="$cauthorDisp $new"}
                  {/if}
                  
                  {if $subcode >= 'a' and $subcode <= $lastcode}
                    {assign var="new" value=$subfield->getData()}
                    {assign var="cauthor" value="$cauthor $new"}
                  {/if}
                  
                {/foreach}
                <a href="/Search/Home?lookfor=%22{$cauthor|trim}%22&amp;type=author" >{$cauthorDisp|trim}</a>
                <br>
              {/foreach}
            </td>
          </tr>
        {/if}

        <tr valign="top">
          <th>{translate text='Format'}: </th>
          <td>
            {foreach from=$recordFormat item=format}
              <span class="iconlabel {$format|lower|regex_replace:"/[ ()-]/":""}">{$format}</span>
              {/foreach}
          </td>
        </tr>


        <tr valign="top">
          <th>{translate text='Language'}: </th>
          {if is_array($recordLanguage)}
            <td>
              {foreach from=$recordLanguage item=language name=langLoop}
                {if !$smarty.foreach.langLoop.last}
                  {$language},
                {else}
                  {$language}
                {/if}
              {/foreach}
            </td>
          {else}
            <td>{$recordLanguage}</td>
          {/if}
        </tr>

  {assign var=marcField value=$marc->getFields('260')}
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

  {assign var=marcField value=$marc->getFields('400|410|411|440|800|810|811|830',1)}
  {if $marcField}
  <tr valign="top">
    <th>{translate text='Series'}: </th>
    <td>
      {foreach from=$marcField item=field name=loop}
        {assign var=seriesSearch value=""}
        {assign var=seriesDisplay value=""}
        {foreach from=$field->getSubfields() item=subfield key=subcode  name=subloop}
          {assign var=subdata value=$subfield->getData()}
          {if $subcode == 'a'}
            {assign var=seriesSearch value="$seriesSearch $subdata"}
          {else}       
            {assign var=seriesDisplay value="$seriesDisplay $subdata"}
          {/if}
        {/foreach}
        <a class="clickpostlog" ref="recseries" href="{$url}/Search/Home?lookfor=%22{$seriesSearch}%22&amp;type=series">{$seriesSearch}</a> {$seriesDisplay}<br>
      {/foreach}
    </td>
  </tr>
  {/if}

  {assign var=marcField value=$marc->getFields('490')}
  {if $marcField}
  <tr valign="top">
    <th>{translate text='Series Statement'}: </th>
    <td>
      {foreach from=$marcField item=field name=loop}
        {assign var=series value=""}
        {foreach from=$field->getSubfields() item=subfield key=subcode  name=subloop}
          {assign var=subdata value=$subfield->getData()}
          {assign var=series value="$series $subdata"}
        {/foreach}
        {$series}
      {/foreach}
    </td>
  </tr>
  {/if}

  {assign var=marcField value=$marc->getFields('520')}
  {if $marcField}
  {assign var=num_520 value=$marcField|@count}
  <a name="summary"></a>
  <tr valign="top">
    <th>{translate text='Summary'}: </th>
    <td>
    {$marcField[0]|getvalue:'a'}
    {if $num_520 > 1}
     .... <a class="clickpostlog" ref="recfullsummary" href="{$url}/Record/{$id}/Description#summary"> (full summary)</a>
    {/if}
    <p>
    </td>
  </tr>
  {/if}

  <!-- url to record in legacy system -->
  {if $recordURL}
  <tr valign="top">
    <th>{translate text='URL'}: </th>
    <td>
      <a href="{$recordURL}{$id}" target="Mirlyn">Display record in Mirlyn</a> 
    </td>
  </tr>
  {/if}

</table>


<a name="tabs"></a>

          <!-- Display Tab Navigation -->
          <div id="tabnav">
            <ul>
              <li{if $tab == 'Holdings' || $tab == 'Home'} class="active"{/if}>
                <a class="clickpostlog" ref="recholdingstab" href="{$url}/Record/{$id}/Holdings#tabs" class="first"><span></span>{translate text='Holdings'}</a>
              </li>
              <li{if $tab == 'Description'} class="active"{/if}>
                <a class="clickpostlog" ref="recdescriptiontab" href="{$url}/Record/{$id}/Description#tabs" class="first"><span></span>{translate text='Description'}</a>
              </li>
              {if $marc->getFields('975') || $lc_subjects or $other_subjects}
              <li{if $tab == 'Subjects'} class="active"{/if}>
                <a class="clickpostlog" ref="recsubjectstab" href="{$url}/Record/{$id}/Subjects#tabs" class="first"><span></span>{translate text='Subjects'}</a>
              </li>
              {/if}
              {if $marc->getFields('505')}
              <li{if $tab == 'TOC'} class="active"{/if}>
                <a class="clickpostlog" ref="rectoctab" href="{$url}/Record/{$id}/TOC#tabs" class="first"><span></span>{translate text='Table of Contents'}</a>
              </li>
              {/if}
<!--              <li{if $tab == 'UserComments'} class="active"{/if}>
                <a href="{$url}/Record/{$id}/UserComments" class="first"><span></span>{translate text='Comments'}</a>
              </li>
-->
              <!-- {if $hasReviews}
              <li{if $tab == 'Reviews'} class="active"{/if}>
                <a href="{$url}/Record/{$id}/Reviews" class="first"><span></span>{translate text='Reviews'}</a>
              </li>
              {/if} -->
              {if $hasExcerpt}
              <li{if $tab == 'Excerpt'} class="active"{/if}>
                <a class="clickpostlog" ref="recexcerpttab" href="{$url}/Record/{$id}/Excerpt#tabs" class="first"><span></span>{translate text='Excerpt'}</a>
              </li>
              {/if}
              <li{if $tab == 'Details'} class="active"{/if}>
                <a class="clickpostlog" ref="recmarctab" href="{$url}/Record/{$id}/Details#tabs" class="first"><span></span>{translate text='MARC View'}</a>
              </li>
            </ul>
          </div> <!-- End id=tabnav -->
          
          <div class="details">
            {include file="Record/$subTemplate"}
          </div>
<script>
 {if $googleLinks}
    //getGoogleBookInfo('{$googleLinks}');
    getGoogleBookInfo('{$googleLinks}', '{$id}');
  {/if}
</script>  

          {assign var=titleField value=$marc->getField('245')}
          {assign var=seriesField value=$marc->getField('440')}
          {assign var=authorField value=$marc->getField('100')}
          {assign var=publishField value=$marc->getField('260')}
          {assign var=editionField value=$marc->getField('250')}
          {assign var=isbnField value=$marc->getField('020')}
          {assign var=issnField value=$marc->getField('022')}

{*        
          <span class="Z3988"
              {if $recordFormat == "Book"}
              title="ctx_ver=Z39.88-2004&amp;rft_val_fmt=info%3Aofi%2Ffmt%3Akev%3Amtx%3Abook&amp;rfr_id=info%3Asid%2F{$coinsID}%3Agenerator&amp;rft.genre=book&amp;rft.btitle={$titleField|getvalue:'a'|escape:"url"} {$titleField|getvalue:'b'|escape:"url"}&amp;rft.title={$titleField|getvalue:'a'|escape:"url"} {$titleField|getvalue:'b'|escape:"url"}&amp;rft.series={$seriesField|getvalue:'a'|escape:"url"}&amp;rft.au={$authorField|getvalue:'a'|escape:"url"}&amp;rft.date={$publishField|getvalue:'c'}&amp;rft.pub={$publishField|getvalue:'a'|escape:"url"}&amp;rft.edition={$editionField|getvalue:'a'}&amp;rft.isbn={$isbnField|getvalue:'a'}">
              {elseif $recordFormat == "Journal"}
              title="ctx_ver=Z39.88-2004&amp;rft_val_fmt=info%3Aofi%2Ffmt%3Akev%3Amtx%3Ajournal&amp;rfr_id=info%3Asid%2F{$coinsID}%3Agenerator&amp;rft.genre=article&amp;rft.title={$titleField|getvalue:'a'|escape:"url"} {$titleField|getvalue:'b'|escape:"url"}&amp;rft.date={$publishField|getvalue:'c'}&amp;rft.issn={$issnField|getvalue:'a'}">
              {else}
              title="ctx_ver=Z39.88-2004&amp;rft_val_fmt=info%3Aofi%2Ffmt%3Akev%3Amtx%3Adc&amp;rfr_id=info%3Asid%2F{$coinsID}%3Agenerator&amp;rft.title={$titleField|getvalue:'a'|escape:"url"} {$titleField|getvalue:'b'|escape:"url"}&amp;rft.creator={$authorField|getvalue:'a'|escape:"url"}&amp;rft.date={$publishField|getvalue:'c'}&amp;rft.pub={$publishField|getvalue:'a'|escape:"url"}&amp;rft.format={$recordFormat}&amp;rft.language={$recordLanguage}">
              {/if}
          </span>
*}

      </div> <!-- end record -->
   </div> <!-- end of content -->
</div>
