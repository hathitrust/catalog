{assign var=marcField value=$marc->getFields('245')}

<div style="width: 90%; margin: 0 auto">
<h3>APA Citation</h3>
<p class="record-citation">
{if $apaAuthorList}{$apaAuthorList}{/if}

{assign var=marcField value=$marc->getField('26[04]', true)}
{if $marcField && $marcField|getvalue:'c'}
({$marcField|getvalue:'c'|regex_replace:"/[^0-9]/":""}).
{/if}

<span style="font-style:italic;">{$apatitle}</span>
{assign var=marcField value=$marc->getFields('250')}
{if $marcField}
{if !is_array($marcField)}
  {$marcField|getvalue:'a'}
{else}
  {foreach from=$marcField item=mf}
   {if $mf|getvalue:'a' && $mf|getvalue:'a' != '1st ed.'}
     {$mf|getvalue:'a'}
   {/if}
  {/foreach}
{/if}
{/if}

{$publisher}.
</p>

<h3>MLA Citation</h3>
<p class="record-citation">
{if $mlaAuthorList}{$mlaAuthorList}.{/if}

<span style="text-decoration: underline;">{$mlatitle}</span>
{assign var=marcField value=$marc->getFields('250')}
{if $marcField}
{if !is_array($marcField)}
  {$marcField|getvalue:'a'}
{else}
  {foreach from=$marcField item=mf}
   {if $mf|getvalue:'a' && $mf|getvalue:'a' != '1st ed.'}
     {$mf|getvalue:'a'}
   {/if}
  {/foreach}
{/if}
{/if}

{$publisher},
{assign var=marcField value=$marc->getField('26[04]', true)}
{if $marcField && $marcField|getvalue:'c'}
{$marcField|getvalue:'c'|regex_replace:"/[^0-9]/":""}.
{/if}
</p>


<div class="alert alert-block alert-warning"><p><strong>Warning</strong>: These citations may not always be complete (especially for serials).</p>
</div>
</div>
