<html>
<head>
  <title>MARC view: {$title}</title>
  {literal}
  <style type="text/css">
      body {
        padding: 1.5em;
      }
      .even {
        background-color: #eee
      }
      .code {
        padding-left: 1em;
      }
      .tag {
        font-weight: bold;
      }
      .vdata {
        padding-left: 1em;
      }
      .firstsub td {
        padding-top: 0.25em;
        border-top: 1pt solid #444;
      }
      table {
        margin-bottom: 5em;
        border-bottom: 1pt solid #444;
      }
      .inner td {
        padding-top: 0em;
      }
  </style>
  {/literal}
</head>
<body>
  <h1>{$title}</h1>
  <table>
    <tr class="leader">
      <td>LDR</td><td class="ind" colspan="3"> </td>
      <td>{$marc->getLeader()}</td>
  {foreach from=$fields key=i item=f}
    {if $f->getPosition()%2 == 0}
      {assign var=eo value="even"}
    {else}
      {assign var=eo value="odd"}
    {/if}
    <tr class="{$eo} firstsub">
      <td class="tag">{$f->getTag()}</td>
      {if $f->isControlField()}
        <td class="ind" colspan=3> </td>
        <td class="cdata">{$f->getData()}</td>
      {else}
        {assign var=ind1 value=$f->getIndicator(1)}
        {assign var=ind2 value=$f->getIndicator(2)}
        {if $ind1 == ' '}{assign var="ind1" value="⊔"}{/if}
        {if $ind2 == ' '}{assign var="ind2" value="⊔"}{/if}
          <td class="ind">{$ind1}</td>
          <td class="ind">{$ind2}</td>
        {assign var=sfs value=$f->getSubfields()}
        {foreach from=$sfs item=sf}
          {if $sf->getPosition() != 0}
            <tr class="{$eo} inner">
              <td class="tag"></td>
              <td class="ind1"></td>
              <td class="ind2"></td>
          {/if}
          <td class="code">‡{$sf->getCode()}</td>
          <td class="vdata">{$sf->getData()}</td>
          </tr>
        {/foreach}
      {/if}
    </tr>
  {/foreach}
</body>
</html>
