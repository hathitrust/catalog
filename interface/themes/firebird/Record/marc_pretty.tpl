<!DOCTYPE html>
<html data-analytics-enabled="true">
<head>
  <title>MARC view: {$title}</title>
  
  <script>
  let $$assets = {firebird_manifest};
  </script>
  {literal}
  <script type="text/javascript">
  let head = document.head;
  function addScript(options) {
    let scriptEl = document.createElement('script');
    if ( options.crossOrigin ) { scriptEl.crossOrigin = options.crossOrigin; }
    if ( options.type ) { scriptEl.type = options.type; }
    scriptEl.src = options.href;
    document.head.appendChild(scriptEl);
  }
  function addStylesheet(options) {
    let linkEl = document.createElement('link');
    linkEl.rel = 'stylesheet';
    linkEl.href = options.href;
    document.head.appendChild(linkEl);
  }

  addStylesheet({ href: $$assets.stylesheet});
  addScript({ href: $$assets.script, type: 'module'});
</script>
<script>
  // in case any of the links and scripts fail
  setTimeout(function() {
    document.body.style.visibility = 'visible';
    document.body.style.opacity = '1';
  }, 1500);
</script>
<style type="text/css">
      body {
        padding: 1.5em;
      }
      .even {
        background-color: #eee
      }
      .tag {
        font-weight: bold;
      }
      .vdata {
        padding-left: 1em;
      }
      td {
        padding-top: 0.0em;
        border-top: 1pt solid #444;
      }
      table {
        margin-bottom: 5em;
        border-bottom: 1pt solid #444;
        line-height: 125%;
      }
      .code {
        padding-left: 0.25em;
        font-weight: bold;
        color: #a00;
        padding-right: 0.15em;
      }
  </style>
  {/literal}
  {* {js_link href="/common/alicorn/js/utils.201910.js"} *}
</head>
<body style="opacity: 0; visibility: none;">
  <h1 class="mb-3">{$title}</h1>
  <table class="table border-top">
    <tr class="leader">
      <td>LDR</td><td class="ind" colspan="2"> </td>
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
      <td class="ind" colspan=2> </td>
      <td class="cdata">{$f->getData()}</td>
      {else}
        {assign var=ind1 value=$f->getIndicator(1)}
        {assign var=ind2 value=$f->getIndicator(2)}
        {if $ind1 == ' '}{assign var="ind1" value="⊔"}{/if}
        {if $ind2 == ' '}{assign var="ind2" value="⊔"}{/if}
          <td class="ind">{$ind1}</td>
          <td class="ind">{$ind2}</td>
          <td class="cdata">
            {assign var=sfs value=$f->getSubfields()}
            {foreach from=$sfs item=sf}
              <span class="code">‡{$sf->getCode()}</span><span class="ddata">{$sf->getData()}</span>
            {/foreach}
        </td>
      {/if}
    </tr>
  {/foreach}
</body>
</html>
