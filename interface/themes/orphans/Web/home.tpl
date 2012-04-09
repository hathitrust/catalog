{literal}
  <style type="text/css">
    #searchControl .gsc-control {
      width : 100%;
    }
  </style>
   
    {/literal} 
    <script src="http://www.google.com/jsapi?key={$googleKey}" type="text/javascript"></script>
    {literal}
    <script language="Javascript" type="text/javascript">
    //<![CDATA[

    google.load("search", "1");

    function OnLoad() {
      // Create a search control
      var coreSearch = new GSearchControl();
      coreSearch.setLinkTarget(GSearch.LINK_TARGET_SELF);
      coreSearch.setResultSetSize(GSearch.LARGE_RESULTSET);

      // Define Web Search
      var siteSearch = new GwebSearch();
      siteSearch.setLinkTarget(GSearch.LINK_TARGET_SELF);
      siteSearch.setResultSetSize(GSearch.LARGE_RESULTSET);
      siteSearch.setUserDefinedLabel("Library Web");
      //siteSearch.setNoResultsString("Your search did not match any of the library web pages.");
      {/literal}
      siteSearch.setSiteRestriction("{$domain}");
      {literal}

      // Define Web Search Options
      var options = new GsearcherOptions();
      options.setExpandMode(GSearchControl.EXPAND_MODE_OPEN);

      // Add Web Search
      coreSearch.addSearcher(siteSearch, options);

      // Define Output Options
      var drawOptions = new GdrawOptions();
      //drawOptions.setSearchFormRoot(document.getElementById('searchbar'));
      drawOptions.setSearchFormRoot('empty');

      // Tell the searcher to draw itself and tell it where to attach
      coreSearch.draw(document.getElementById("searchcontrol"), drawOptions);

      {/literal}
      // Execute an inital search
      coreSearch.execute("{$lookfor}");
      {literal}
    }
    google.setOnLoadCallback(OnLoad);

    //]]>
    </script>
{/literal}

<div id="bd">
  <div id="yui-main" class="content">

    <div class="yui-b first contentbox">
      <div id="searchcontrol">Loading...</div>
    </div>
  </div>
   
  <div class="yui-b">
    <div class="box submenu catalogMini">
      <h4>Catalog Results</h4>
      
      <ul class="similar">
        {foreach from=$results item=record}
        <li>
          <span class="{$record.format}">
          <a href="{$url}/Record/{$record.id}">{$record.title}</a>
          </span>
          <span style="font-size: .8em">
          {if $record.author}
          <br>By: Ivler, J. M.
          {/if}
          <br>Published: ({$record.publishDate})
          </span>
        </li>
        {/foreach}
      </ul>
      <hr>
      <p><a href="{$url}/Search/Home?lookfor={$lookfor}">More catalog results...</a></p>
    </div>
    
  </div>
</div>

</div>
