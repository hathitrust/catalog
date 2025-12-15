{* <table summary="This table displays bibliographic information about this specific book or series" class="citation"> *}

<dl class="metadata">
  {assign var=marcField value=$marc->getFields('785')}
  {if $marcField}
    <div class="grid">
      <dt class="g-col-lg-4 g-col-12">{translate text='New Title'}</dt>
      <dd class="g-col-lg-8 g-col-12">
        {foreach from=$marcField item=field name=loop}
          <a href="{$url}/Search/Home?lookfor=%22{$field|getvalue:'s'}{$field|getvalue:'t'}%22&type=title&amp;inst={$inst}">{$field|getvalue:'s'}{$field|getvalue:'t'}</a><br>
        {/foreach}
      </dd>
    </div>
  {/if}

  {assign var=marcField value=$marc->getFields('780')}
  {if $marcField}
    <div class="grid">
      <dt class="g-col-lg-4 g-col-12">{translate text='Previous Title'}</dt>
      <dd class="g-col-lg-8 g-col-12">
        {foreach from=$marcField item=field name=loop}
          <a href="{$url}/Search/Home?lookfor=%22{$field|getvalue:'s'}{$field|getvalue:'t'}%22&type=title&amp;inst={$inst}">{$field|getvalue:'s'}{$field|getvalue:'t'}</a><br>
        {/foreach}
      </dd>
    </div>
  {/if}

  {assign var=marcField value=$marc->getField('100')}
  {if $marcField}
    <div class="grid">
      <dt class="g-col-lg-4 g-col-12">{translate text='Main Author'}</dt>
      <dd class="g-col-lg-8 g-col-12">
        {record_author_display marc_field=$marcField inst=$inst url=$url}
      </dd>
    </div>
  {/if}

  {assign var=marcField value=$marc->getField('110')}
  {if $marcField}
    <div class="grid">
      <dt class="g-col-lg-4 g-col-12">{translate text='Corporate Author'}</dt>
      <dd class="g-col-lg-8 g-col-12">
        {record_author_display marc_field=$marcField inst=$inst url=$url}
      </dd>
    </div>
  {/if}

  {assign var=marcField value=$marc->getField('111')}
  {if $marcField}
    <div class="grid">
      <dt class="g-col-lg-4 g-col-12">{translate text='Meeting Name'}</dt>
      <dd class="g-col-lg-8 g-col-12">
        {record_author_display marc_field=$marcField inst=$inst url=$url}
      </dd>
    </div>
  {/if}

  {assign var=marcField value=$marc->getFields('700|710|711', 1)}
  {if $marcField}
    <div class="grid">
      <dt class="g-col-lg-4 g-col-12">{translate text='Related Names'}</dt>
      <dd class="g-col-lg-8 g-col-12">
        {foreach from=$marcField item=field name=loop}
          {record_author_display marc_field=$field inst=$inst url=$url}<br/>
        {/foreach}
      </dd>
    </div>
  {/if}

  {if isset($recordLanguage) && $recordLanguage}
  <div class="grid">
    <dt class="g-col-lg-4 g-col-12">{translate text='Language(s)'}</dt>
    <dd class="g-col-lg-8 g-col-12">
      {foreach from=$recordLanguage item=field name=loop}
        {if $field@first}{$field}{else}; {$field}{/if}
      {/foreach}
   </dd>
  </div>
  {/if}

  {assign var=marcField value=$marc->getFields('26[04]', true)}
  {if $marcField}
  <div class="grid">
    <dt class="g-col-lg-4 g-col-12">{translate text='Published'}</dt>
    <dd class="g-col-lg-8 g-col-12">
      {foreach from=$marcField item=field name=loop}
        {$field|getvalue:'a'} {$field|getvalue:'b'} {$field|getvalue:'c'}<br>
      {/foreach}
    </dd>
  </div>
  {/if}

  {assign var=marcField value=$marc->getFields('250')}
  {if $marcField}
  <div class="grid">
    <dt class="g-col-lg-4 g-col-12">{translate text='Edition'}</dt>
    <dd class="g-col-lg-8 g-col-12">
      {foreach from=$marcField item=field name=loop}
        {$field|getvalue:'a'}<br>
      {/foreach}
    </dd>
  </div>
  {/if}

  {assign var=marcField value=$marc->getFields('440')}
  {if $marcField}
  <div class="grid">
    <dt class="g-col-lg-4 g-col-12">{translate text='Series'}</dt>
    <dd class="g-col-lg-8 g-col-12">
      {foreach from=$marcField item=field name=loop}
        <a href="{$url}/Search/Home?lookfor=%22{$field|getvalue:'a'|escape}%22&amp;type=series&amp;inst={$inst}">{$field|getvalue:'a'}</a><br>
      {/foreach}
    </dd>
  </div>
  {/if}

  {assign var=marcField value=$marc->getFields('600|610|630|650|651|655',1)}
  {if $marcField}
  <div class="grid">
    <dt class="g-col-lg-4 g-col-12">{translate text='Subjects'}</dt>
    <dd class="g-col-lg-8 g-col-12">
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
              <span class="visually-hidden">{$ancestors} / </span>
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
    </dd>
  </div>
  {/if}

  {if isset($content_advice) && $content_advice}
  <div class="grid">
    <dt class="g-col-lg-4 g-col-12">{translate text='Content Advice'}</dt>
    <dd class="g-col-lg-8 g-col-12">
      {foreach from=$content_advice item=field name=loop}
        {$field}<br>
      {/foreach}
    </dd>
  </div>
  {/if}

  {if isset($summary) && $summary}
  <div class="grid">
    <dt class="g-col-lg-4 g-col-12">{translate text='Summary'}</dt>
    <dd class="g-col-lg-8 g-col-12">
      {foreach from=$summary item=field name=loop}
        {$field}<br>
      {/foreach}
    </dd>
  </div>
  {/if}

  {assign var=marcField value=$marc->getFields('500|501|521|525|526|530|547|550|552|561|565|584|585',true)}
  {if $marcField}
  <div class="grid">
    <dt class="g-col-lg-4 g-col-12">{translate text='Note'}</dt>
    <dd class="g-col-lg-8 g-col-12">
      {foreach from=$marcField item=field name=loop}
        {$field|getvalue:'a'}<br>
      {/foreach}
    </dd>
  </div>
  {/if}

  {if isset($mdl) && $mdl}
    <!-- mdl -->
    {assign var=marcField value=$marc->getFields('540')}
    {if $marcField}
    <div class="grid">
      <dt class="g-col-lg-4 g-col-12">{translate text='Use and Reproduction Note'}</dt>
      <dd class="g-col-lg-8 g-col-12">
        {foreach from=$marcField item=field name=loop}
          {foreach from=$field->getSubfields() item=subfield key=subcode}
            {if $subcode != '6'}
              {$subfield->getData()}
            {/if}
          {/foreach}
          <br>
        {/foreach}
      </dd>
    </div>
    {/if}
  {/if}


  {assign var=marcField value=$marc->getFields('300')}
  {if $marcField}
  <div class="grid">
    <dt class="g-col-lg-4 g-col-12">{translate text='Physical Description'}</dt>
    <dd class="g-col-lg-8 g-col-12">
      {foreach from=$marcField item=field name=loop}
        {foreach from=$field->getSubfields() item=subfield key=subcode}
          {if $subcode != '6'}
            {$subfield->getData()}
          {/if}
        {/foreach}
        <br>
      {/foreach}
    </dd>
  </div>
  {/if}

  {assign var=marcField value=$marc->getFields('970')}
  {if $marcField}
  <div class="grid">
    <dt class="g-col-lg-4 g-col-12">{translate text='Original Format'}: </dt>
    <dd class="g-col-lg-8 g-col-12">
      {foreach from=$marcField item=field name=loop}
       {if $field@iteration eq 1}
         {if $field|getvalue:'b' neq 'Electronic Resource'}{$field|getvalue:'b'}{/if}
       {elseif $field@iteration gt 1}
         {if $field|getvalue:'b' neq 'Electronic Resource'}<br>{$field|getvalue:'b'}{/if}
       {/if}
      {/foreach}
    </dd>
  </div>
  {/if}

  {assign var=852Field value=$marc->getFields('852')}
  {if isset($852Field)}
    {foreach from=$852Field item=field name=loop}
      {if $field@iteration lt 2}
        {if $field|getvalue:'a' eq 'MiU'}
          {if $field|getvalue:'h'}
            <div class="grid">
            <dt class="g-col-lg-4 g-col-12">{translate text='Original Classification Number'}</dt>
            <dd class="g-col-lg-8 g-col-12">
            {$field|getvalue:'h'}
            </dd>
            </div>
          {/if}
      {else}
        {assign var=050Field value=$marc->getFields('050')}
        {foreach from=$050Field item=field name=loop}
          {if $field@iteration lt 2}
            {if $field|getvalue:'a'}
              <div class="grid">
              <dt class="g-col-lg-4 g-col-12">{translate text='Original Classification Number'}</dt>
              <dd class="g-col-lg-8 g-col-12">
              {$field|getvalue:'a'} {$field|getvalue:'b'}
              </dd>
              </div>
            {/if}
          {/if}
        {/foreach}
      {/if}
      {/if}
    {/foreach}
  {/if}

  {assign var=marcField value=$marc->getFields('020')}
   {if $marcField}
   <div class="grid">
     <dt class="g-col-lg-4 g-col-12">{translate text='ISBN'}</dt>
     <dd class="g-col-lg-8 g-col-12">
       {foreach from=$marcField item=field name=loop}
         {$field|getvalue:'a'}<br>
       {/foreach}
     </dd>
   </div>
   {/if}

  <div class="grid">
    <dt class="g-col-lg-4 g-col-12">{translate text='Locate a Print Version'}</dt>
    <dd class="g-col-lg-8 g-col-12">
          {if array_key_exists('oclc', $record)}
            {if is_array($record.oclc)}
  <!-- title array -->
              {foreach from=$record.oclc item=title name=loop}
                {if $title@iteration lt 3}
                <a href="http://www.worldcat.org/oclc/{$title}" data-toggle="tracking" data-tracking-category="outLinks" data-tracking-action="Catalog Find in a Library" data-tracking-label="{$title}">Find in a library</a><br>
                {/if}
              {/foreach}
            {else}
  <!-- title non-array -->
               <a href="http://www.worldcat.org/oclc/{$record.oclc}" data-toggle="tracking" data-tracking-category="outLinks" data-tracking-action="Catalog Find in a Library" data-tracking-label="{$record.oclc}">Find in a library</a>
            {/if}
          {else}
            Find in a library service is not available from this catalog. <a href="http://www.worldcat.org/" data-toggle="tracking" data-tracking-category="outLinks" data-tracking-action="Catalog Search Worldcat" data-tracking-label="worldcat" target="_blank">Search Worldcat</a>
          {/if}
    </dd>
  </div>



</dl>
{* </table> *}
