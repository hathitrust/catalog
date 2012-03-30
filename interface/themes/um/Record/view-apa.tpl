{if $record.100}
  {$record.100.a.0}.
{/if}

{if !$record.700}
  {foreach from=$record.700.a item=author name="loop"}
    {$author}{if !$smarty.foreach.loop.last}, {/if}
  {/foreach}
  (Eds.).
{/if}

({$record.260.c.0}).

{$record.245.a.0} {$record.245.b.0}.

{$record.260.a.0}: {$record.260.b.0}.