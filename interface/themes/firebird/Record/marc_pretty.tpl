<html data-analytics-enabled="true">
<head>
  <title>MARC view: {$title}</title>
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

  addScript({ href: 'https://kit.fontawesome.com/1c6c3b2b35.js', crossOrigin: 'anonymous' });
  // addScript({ href: `//localhost:5173/js/main.js`, type: 'module' });
   

  let firebird_config = localStorage.getItem('firebird') || '';
  if ( firebird_config == 'proxy' ) {
    addScript({ href: `//${location.host}/js/main.js`, type: 'module' });
  } else if ( firebird_config.match('localhost') ) {
    addScript({ href: `//${firebird_config}/js/main.js`, type: 'module' });
  } else {
    // connect to netlify
    if ( firebird_config ) { firebird_config += '--'; }
    let hostname = `//${firebird_config}hathitrust-firebird-common.netlify.app`;
    addStylesheet({ href: `${hostname}/assets/main.css` });
    addScript({ href: `${hostname}/assets/main.js`, type: 'module' });
  }
</script>
<style type="text/css">
      body {
        padding: 1.5em;
      }
      .even {
        background-color: #eee
      }
      .code {
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
<body>
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
