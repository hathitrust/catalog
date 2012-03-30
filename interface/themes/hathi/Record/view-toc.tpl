<b>{translate text="Table of Contents"}: </b>
{assign var=marcField value=$marc->getFields('505')}
{if $marcField}
<ul class="toc">
  {foreach from=$marcField item=field name=loop}
    {assign var=toc value=""}
    {foreach from=$field->getSubfields() item=subfield name=subloop}
      {assign var=line value=$subfield->getData()|replace:"--":"</li><li>"}
      {assign var=toc value="$toc $line"}
    {/foreach}
    <li>{$toc}</li>
  {/foreach}
</ul>
{/if}