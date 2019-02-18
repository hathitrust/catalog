  <div class="yui-b">
    <div id="tabNav" style="margin-top: 0px">
      <ul id="list1">
        <li{if $pageTemplate=="checkedout.tpl"} class="active"{/if} style="float: none;"><a class="clickpostlog" ref="mrcheckedout" href="/MyResearch/CheckedOut">{translate text='Checked Out Items'}</a></li>
        <li{if $pageTemplate=="holds.tpl"} class="active"{/if} style="float: none;"><a class="clickpostlog" ref="mrholds" href="/MyResearch/Holds">{translate text='Holds and Recalls'}</a></li>
        <li{if $pageTemplate=="bookings.tpl"} class="active"{/if} style="float: none;"><a class="clickpostlog" ref="mrbookings" href="/MyResearch/Bookings">{translate text='Bookings'}</a></li>
        <li{if $pageTemplate=="fines.tpl"} class="active"{/if} style="float: none;"><a class="clickpostlog" ref="mrfines" href="/MyResearch/Fines">{translate text='Fines'}</a></li>
        <li{if $pageTemplate=="ill.tpl"} class="active"{/if} style="float: none;"><a class="clickpostlog" ref="mrill" href="/MyResearch/ILL">{translate text='ILL Transactions'}</a></li>
        <li{if $pageTemplate=="profile.tpl"} class="active"{/if} style="float: none;"><a class="clickpostlog" ref="mrprofile" href="/MyResearch/Profile">{translate text='Profile'}</a></li>
        <li{if $pageTemplate=="favorites.tpl"} class="active"{/if} style="float: none;"><a class="clickpostlog" ref="mrfavorites" href="/MyResearch/Favorites">{translate text='Favorites'}</a></li>
      </ul>
    </div>

      <div id="favoritesLinks">
        {if $pageTemplate=="favorites.tpl"}
         {include file="MyResearch/favorites_taglist.tpl"}
        {/if}   
      </div>
  </div>

