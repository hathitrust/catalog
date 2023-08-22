<table summary="This table displays bibliographic information about this specific book or series" class="citation">
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
    <td>{record_author_display marc_field=$marcField inst=$inst url=$url}</td>
  </tr>
  {/if}

  {assign var=marcField value=$marc->getField('110')}
  {if $marcField}
  <tr valign="top">
    <th>{translate text='Corporate Author'}: </th>
    <td>{record_author_display marc_field=$marcField inst=$inst url=$url}</td>
  </tr>
  {/if}

  {assign var=marcField value=$marc->getField('111')}
  {if $marcField}
  <tr valign="top">
    <th>{translate text='Meeting Name'}: </th>
    <td>{record_author_display marc_field=$marcField inst=$inst url=$url}</td>
  </tr>
  {/if}

  {assign var=marcField value=$marc->getFields('700|710|711', 1)}
  {if $marcField}
  <tr valign="top">
    <th>{translate text='Related Names'}: </th>
    <td>
      {foreach from=$marcField item=field name=loop}
        {record_author_display marc_field=$field inst=$inst url=$url}<br/>
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
          {assign var=ancestors value=""}
          {foreach from=$field->getSubfields() item=subfield key=subcode  name=subloop}
           {if $subcode >= 'a'}
            {if $subject} &gt; {/if}
            {assign var=subfield value=$subfield->getData()}
            {assign var=subject value="$subject $subfield"}
            <a href="{$url}/Search/Home?lookfor=%22{$subject|escape}%22&amp;type=subject&amp;inst={$inst}">
              {if $ancestors != ""}
              <span class="offscreen">{$ancestors} / </span>
              {/if}
              {$subfield}
            </a>
           {/if}
           {if $ancestors == ""}
             {assign var=ancestors value="$subject"}
           {else}
             {assign var=ancestors value="$ancestors / $subfield"}
           {/if}
          {/foreach}
          <br>
        {/foreach}
    </td>
  </tr>
  {/if}

  {if $content_advice}
  <tr valign="top">
    <th>{translate text='Content Advice'}: </th>
    <td>
      {foreach from=$content_advice item=field name=loop}
        {$field}<br>
      {/foreach}
    </td>
  </tr>
  {/if}

  {if $summary}
  <tr valign="top">
    <th>{translate text='Summary'}: </th>
    <td>
      {foreach from=$summary item=field name=loop}
        {$field}<br>
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
              <a href="http://www.worldcat.org/oclc/{$title}" data-toggle="tracking" data-tracking-category="outLinks" data-tracking-action="Catalog Find in a Library" data-tracking-label="{$title}">Find in a library</a><br>
              {/if}
            {/foreach}
          {else}
<!-- title non-array -->
            {if $record.oclc}
             <a href="http://www.worldcat.org/oclc/{$record.oclc}" data-toggle="tracking" data-tracking-category="outLinks" data-tracking-action="Catalog Find in a Library" data-tracking-label="{$record.oclc}">Find in a library</a>
            {else} Find in a library service is not available from this catalog. <a href="http://www.worldcat.org/" data-toggle="tracking" data-tracking-category="outLinks" data-tracking-action="Catalog Search Worldcat" data-tracking-label="worldcat" target="_blank">Search Worldcat</a>
            {/if}
          {/if}
    </td>
  </tr>



</table>
