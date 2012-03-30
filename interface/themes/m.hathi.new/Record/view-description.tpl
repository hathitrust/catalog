<br>
<table cellpadding="2" cellspacing="0" border="0" class="citation" style="*width: auto">

  {assign var=marcField value=$marc->getFields('240|730',1)}
  {if $marcField}
  <tr valign="top">
    <th>{translate text='Uniform Title'}: </th>
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

  {assign var=marcField value=$marc->getFields('246|247|740',1)}
  {if $marcField}
  <tr valign="top">
    <th>{translate text='Other Titles'}: </th>
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

  {assign var=marcField value=$marc->getFields('773')}
  {if $marcField}
  <tr valign="top">
    <th>{translate text='In Collection'}: </th>
    <td>
      {foreach from=$marcField item=field name=loop}
        {foreach from=$field->getSubfields() item=subfield key=subcode}
          {if $subcode >= 'a' and $subcode <= 'z'}
            {$subfield->getData()}
          {/if}
        {/foreach}
        <br>
      {/foreach}
    </td>
  </tr>
  {/if}

  {assign var=marcField value=$marc->getFields('520')}
  {if $marcField}
  <a name="summary"></a>
  <tr valign="top">
    <th>{translate text='Summary'}: </th>
    <td>
      {foreach from=$marcField item=field name=loop}
        {assign var=data value=$field|getvalue:'a'|regex_replace:"/(\.$)/":"\\1<p>"}
        {$data}
      {/foreach}
    </td>
  </tr>
  {/if}

  {assign var=marcField value=$marc->getFields('506')}
  {if $marcField}
  <tr valign="top">
    <th>{translate text='Access'}: </th>
    <td>
      {foreach from=$marcField item=field name=loop}
        {$field|getvalue:'a'}<br>
      {/foreach}
    </td>
  </tr>
  {/if}

  {assign var=marcField value=$marc->getFields('511')}
  {if $marcField}
  <tr valign="top">
    <th>{translate text='Performers'}: </th>
    <td>
      {foreach from=$marcField item=field name=loop}
        {$field|getvalue:'a'}<br>
      {/foreach}
    </td>
  </tr>
  {/if}

  {assign var=marcField value=$marc->getFields('518')}
  {if $marcField}
  <tr valign="top">
    <th>{translate text='Date/Place of Event'}: </th>
    <td>
      {foreach from=$marcField item=field name=loop}
        {$field|getvalue:'a'}<br>
      {/foreach}
    </td>
  </tr>
  {/if}

  {assign var=marcField value=$marc->getFields('580')}
  {if $marcField}
  <tr valign="top">
    <th>{translate text='Related Items'}: </th>
    <td>
      {foreach from=$marcField item=field name=loop}
        {$field|getvalue:'a'}<br>
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
        {$field|getvalue:'s'}{$field|getvalue:'t'}:
        {$field|getvalue:'a'}
      {/foreach}
    </td>
  </tr>
  {/if}

  {assign var=marcField value=$marc->getFields('785')}
  {if $marcField}
  <tr valign="top">
    <th>{translate text='New Title'}: </th>
    <td>
      {foreach from=$marcField item=field name=loop}
        {$field|getvalue:'s'}{$field|getvalue:'t'}:
        {$field|getvalue:'a'}
      {/foreach}
    </td>
  </tr>
  {/if}

  {assign var=marcField value=$marc->getFields('362')}
  {if $marcField}
  <tr valign="top">
    <th>{translate text='Numbering'}: </th>
    <td>
      {foreach from=$marcField item=field name=loop}
        {$field|getvalue:'a'}<br>
      {/foreach}
    </td>
  </tr>
  {/if}

  {assign var=marcField value=$marc->getFields('590')}
  {if $marcField}
  <tr valign="top">
    <th>{translate text='Copy-Specific Note'}: </th>
    <td>
      {foreach from=$marcField item=field name=loop}
        {$field|getvalue:'a'}<p>
      {/foreach}
    </td>
  </tr>
  {/if}

  {assign var=marcField value=$marc->getFields('545')}
  {if $marcField}
  <tr valign="top">
    <th>{translate text='Biography/History'}: </th>
    <td>
      {foreach from=$marcField item=field name=loop}
        {$field|getvalue:'a'}<p>
      {/foreach}
    </td>
  </tr>
  {/if}

  {assign var=marcField value=$marc->getFields('510')}
  {if $marcField}
  <tr valign="top">
    <th>{translate text='References'}: </th>
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

  {assign var=marcField value=$marc->getFields('546')}
  {if $marcField}
  <tr valign="top">
    <th>{translate text='Language note'}: </th>
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

  {assign var=marcField value=$marc->getFields('542')}
  {if $marcField}
  <tr valign="top">
    <th>{translate text='Copyright status information'}: </th>
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

  {assign var=marcField value=$marc->getFields('500|501|502|521|525|526|530|547|550|552|561|565|584|585',true)}
  {if $marcField}
  <tr valign="top">
    <th>{translate text='Note'}: </th>
    <td>
      {foreach from=$marcField item=field name=loop}
        {$field|getvalue:'a'}<p>
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

  {assign var=marcField value=$marc->getFields('533')}
  {if $marcField}
  <tr valign="top">
    <th>{translate text='Reproduction Note'}: </th>
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

  {assign var=marcField value=$marc->getFields('310')}
  {if $marcField}
  <tr valign="top">
    <th>{translate text='Publication Frequency'}: </th>
    <td>
      {foreach from=$marcField item=field name=loop}
        {$field|getvalue:'a'}<br>
      {/foreach}
    </td>
  </tr>
  {/if}

  {assign var=marcField value=$marc->getFields('306')}
  {if $marcField}
  <tr valign="top">
    <th>{translate text='Playing Time'}: </th>
    <td>
      {foreach from=$marcField item=field name=loop}
        {$field|getvalue:'a'}<br>
      {/foreach}
    </td>
  </tr>
  {/if}

  {assign var=marcField value=$marc->getFields('538')}
  {if $marcField}
  <tr valign="top">
    <th>{translate text='Media Format'}: </th>
    <td>
      {foreach from=$marcField item=field name=loop}
        {$field|getvalue:'a'}<br>
      {/foreach}
    </td>
  </tr>
  {/if}

  {assign var=marcField value=$marc->getFields('521')}
  {if $marcField}
  <tr valign="top">
    <th>{translate text='Audience'}: </th>
    <td>
      {foreach from=$marcField item=field name=loop}
        {$field|getvalue:'a'}<br>
      {/foreach}
    </td>
  </tr>
  {/if}
  
  {assign var=marcField value=$marc->getFields('586')}
  {if $marcField}
  <tr valign="top">
    <th>{translate text='Awards'}: </th>
    <td>
      {foreach from=$marcField item=field name=loop}
        {$field|getvalue:'a'}<br>
      {/foreach}
    </td>
  </tr>
  {/if}

  {assign var=marcField value=$marc->getFields('508')}
  {if $marcField}
  <tr valign="top">
    <th>{translate text='Production Credits'}: </th>
    <td>
      {foreach from=$marcField item=field name=loop}
        {$field|getvalue:'a'}<br>
      {/foreach}
    </td>
  </tr>
  {/if}

  {assign var=marcField value=$marc->getFields('504')}
  {if $marcField}
  <tr valign="top">
    <th>{translate text='Bibliography'}: </th>
    <td>
      {foreach from=$marcField item=field name=loop}
        {$field|getvalue:'a'}<br>
      {/foreach}
    </td>
  </tr>
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
  
  {assign var=marcField value=$marc->getFields('022')}
  {if $marcField}
  <tr valign="top">
    <th>{translate text='ISSN'}: </th>
    <td>
      {foreach from=$marcField item=field name=loop}
        {$field|getvalue:'a'}<br>
      {/foreach}
    </td>
  </tr>
  {/if}
  
  {assign var=marcField value=$marc->getFields('028')}
  {if $marcField}
  <tr valign="top">
    <th>{translate text='Publisher Number'}: </th>
    <td>
      {foreach from=$marcField item=field name=loop}
        {$field|getvalue:'a'}
        {if $field|getvalue:'b'} ({$field|getvalue:'b'}){/if}
        <br>
      {/foreach}
    </td>
  </tr>
  {/if}
  
  {assign var=marcField value=$marc->getFields('088')}
  {if $marcField}
  <tr valign="top">
    <th>{translate text='Report number'}: </th>
    <td>
      {foreach from=$marcField item=field name=loop}
        {$field|getvalue:'a'}<br>
      {/foreach}
    </td>
  </tr>
  {/if}
  
  {assign var=marcField value=$marc->getFields('555')}
  {if $marcField}
  <tr valign="top">
    <th>{translate text='Indexes/Finding Aids'}: </th>
    <td>
      {foreach from=$marcField item=field name=loop}
        {if $field|getvalue:'a'}{$field|getvalue:'a'}{/if}
        {if $field|getvalue:'u'}
          {if $proxy}
            <a href="{$proxy}/login?url={$field|getvalue:'u'}">{$field|getvalue:'u'}</a>
          {else}
            <a href="{$field|getvalue:'u'}">{$field|getvalue:'u'}</a><br>
          {/if}
        {/if}
       <br>
      {/foreach}
    </td>
  </tr>
  {/if}

  {assign var=marcField value=$marc->getField('945')}
  {if $marcField}
    {assign var=chronology value=$marcField|getvalue:'a'}
    {if $chronology|count_characters:true != 9}
    

      <tr valign="top">
        <th>{translate text='Chronology'}: </th>
        <td>{$chronology}<br></td>
      </tr>
    {/if}
  {/if}
  
  {assign var=marcField value=$marc->getFields('946')}
  {if $marcField}
  <tr valign="top">
    <th>{translate text='Place'}: </th>
    <td>
      {foreach from=$marcField item=field name=loop}
        {$field|getvalue:'a'}<br>
      {/foreach}
    </td>
  </tr>
  {/if}
  
  {assign var=marcField value=$marc->getFields('947')}
  {if $marcField}
  <tr valign="top">
    <th>{translate text='Printer'}: </th>
    <td>
      {foreach from=$marcField item=field name=loop}
        {$field|getvalue:'a'}<br>
      {/foreach}
    </td>
  </tr>
  {/if}
  
  {assign var=marcField value=$marc->getFields('948')}
  {if $marcField}
  <tr valign="top">
    <th>{translate text='Association'}: </th>
    <td>
      {foreach from=$marcField item=field name=loop}
        {$field|getvalue:'a'}<br>
      {/foreach}
    </td>
  </tr>
  {/if}
  
  {assign var=marcField value=$marc->getFields('949')}
  {if $marcField}
  <tr valign="top">
    <th>{translate text='Bookplate'}: </th>
    <td>
      {foreach from=$marcField item=field name=loop}
        {$field|getvalue:'a'}<br>
      {/foreach}
    </td>
  </tr>
  {/if}
  
</table>
