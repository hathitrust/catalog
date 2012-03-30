{$message}

=========================================

{foreach name=emailrecords from=$records item=r}
RECORD {$smarty.foreach.emailrecords.iteration}
{$r.title}
{$r.url}
{foreach from=$r.othertitles item=ot}
  {$ot}
{/foreach}
{if $r.authors}
by {$r.authors}
{/if}
{if $r.publishDate}
Published {$r.publishDate.0}

{foreach from=$r.holdings item=h}
   - {$h.location}
        {$h.callnumber} ({$h.status})
        
{/foreach}

{/if}
__________________________________________________________


{/foreach}          
 