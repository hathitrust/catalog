<br>
<table cellpadding="2" cellspacing="0" border="0" class="citation" style="*width: auto">

  {if $lc_subjects}
  <tr valign="top">
    <th>{translate text='Subjects (LCSH)'}: </th>
    <td>
    {foreach from=$lc_subjects item=subject}
      {assign var=subject_display value=" -- "|implode:$subject}
      {assign var=subject_search value=" "|implode:$subject}
      <a href="/Search/Home?lookfor={$subject_search|escape}&amp;type=subject2">{$subject_display}</a>
      <br>
    {/foreach}
    </td>
  </tr>
  {/if}

  {if $other_subjects}
  <tr valign="top">
    <th>{translate text='Subjects (other)'}: </th>
    <td>
    {foreach from=$other_subjects item=subject}
      {assign var=subject_display value=" -- "|implode:$subject}
      {assign var=subject_search value=" "|implode:$subject}
      <a href="/Search/Home?lookfor={$subject_search|escape}&amp;type=subject2">{$subject_display}</a>
      <br>
    {/foreach}
    </td>
  </tr>
  {/if}

  {assign var=marcField value=$marc->getFields('976')}
  {assign var="hlb3Delimited" value=$record.hlb3Delimited}
  {if $marcField || $hlb3Delimited}
  <tr valign="top">
    <th>{translate text='Academic Discipline'}: </th>
    <td>
        {foreach from=$marcField item=field name=loop}
          {if $field|getvalue:'a'}
            <a href="/Search/Home?lookfor=&amp;type=&amp;filter[]=hlb3Str:{$field|getvalue:'a'|escape:"url"}">{$field|getvalue:'a'|escape}</a>
          {/if}
          {if $field|getvalue:'b'}
            &gt;
            <a href="/Search/Home?lookfor=&amp;type=&amp;filter[]=hlb3Str:{$field|getvalue:'b'|escape:"url"}">{$field|getvalue:'b'|escape}</a>
          {/if}
          {if $field|getvalue:'c'}
            &gt;
            <a href="/Search/Home?lookfor=&amp;type=&amp;filter[]=hlb3Str:{$field|getvalue:'c'|escape:"url"}">{$field|getvalue:'c'|escape}</a>
          {/if}
          <br>
        {/foreach}
        
        {* Repeat for the stored values in hlb3Delimited *}
        {foreach from=$hlb3Delimited item=field name=loop}
        
          {assign var="hlb3array" value="|"|explode:$field}
          {if $hlb3array.0}
            <a href="/Search/Home?lookfor=&amp;type=&amp;filter[]=hlb3Str:{$hlb3array.0|trim|escape:"url"}">{$hlb3array.0|escape}</a>
          {/if}
          {if $hlb3array.1}
            &gt;
            <a href="/Search/Home?lookfor=&amp;type=&amp;filter[]=hlb3Str:{$hlb3array.1|trim|escape:"url"}">{$hlb3array.1|escape}</a>
          {/if}
          {if $hlb3array.2}
            &gt;
            <a href="/Search/Home?lookfor=&amp;type=&amp;filter[]=hlb3Str:{$hlb3array.2|trim|escape:"url"}">{$hlb3array.2|escape}</a>
          {/if}
          <br>
        {/foreach}
        

    </td>
  </tr>
  {/if}

</table>
