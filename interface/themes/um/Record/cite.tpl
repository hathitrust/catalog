<div align="left">

<b>APA Citation</b>
<p style="padding-left: 25px; text-indent: -25px;">
{if $apaAuthorList}{$apaAuthorList}{/if}

{assign var=marcField value=$marc->getField('260')}
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

<b>MLA Citation</b>
<p style="padding-left: 25px; text-indent: -25px;">
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
{assign var=marcField value=$marc->getField('260')}
{if $marcField && $marcField|getvalue:'c'}
{$marcField|getvalue:'c'|regex_replace:"/[^0-9]/":""}.
{/if}
</p>
</div>
<div class="note"><strong>Warning</strong>: These citations may not always be 100% accurate.
<p>APA and MLA are two commonly used citation formats.  Need help using another citation format? <a href="http://www.lib.umich.edu/ask/">Ask a Librarian</a></p>  
</div>