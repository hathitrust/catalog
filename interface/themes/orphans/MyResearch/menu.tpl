  <div class="yui-b">
    <div id="tabNav">
      <ul id="list1">
        <li{if $pageTemplate=="favorites.tpl"} class="active"{/if} style="float: none;"><a href="/MyResearch/Home&amp;inst={$inst}">{translate text='Favorites'}</a></li>
        <li{if $pageTemplate=="checkedout.tpl"} class="active"{/if} style="float: none;"><a href="/MyResearch/CheckedOut&amp;inst={$inst}">{translate text='Checked Out Items'}</a></li>
        <li{if $pageTemplate=="holds.tpl"} class="active"{/if} style="float: none;"><a href="/MyResearch/Holds&amp;inst={$inst}">{translate text='Holds and Recalls'}</a></li>
        <li{if $pageTemplate=="fines.tpl"} class="active"{/if} style="float: none;"><a href="/MyResearch/Fines&amp;inst={$inst}">{translate text='Fines'}</a></li>
        <li{if $pageTemplate=="profile.tpl"} class="active"{/if} style="float: none;"><a href="/MyResearch/Profile&amp;inst={$inst}">{translate text='Profile'}</a></li>
      </ul>
    </div>
  </div>
