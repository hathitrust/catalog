{$marc}

%0 {$format}
{foreach from=$record.100.a item=val}
%A {$val}
{/foreach}
{foreach from=$record.260.b item=val}
%C {$val}
{/foreach}
{foreach from=$record.260.c item=val}
%D {$val}
{/foreach}
{foreach from=$record.700.a item=val}
%E {$val}
{/foreach}
%G {$language}
{foreach from=$record.260.a item=val}
%I {$val}
{/foreach}
{foreach from=$record.440.a item=val}
%J {$val}
{/foreach}
{foreach from=$record.020.a item=val}
%@ {$val}
{/foreach}
{foreach from=$record.022.a item=val}
%@ {$val}
{/foreach}
%T {$record.245.a.0} {$record.245.b.0}
{foreach from=$record.856.u item=val}
%U {$val}
{/foreach}
{foreach from=$record.250.a item=val}
%7 {$val}
{/foreach}