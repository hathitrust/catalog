  <a id="saveToFavoritesLink" class="dolightbox" href="#saveToFavorites" style="display:none"></a>
  <div id="tempFolder"  style="display:none">
    <span class="templine" id="tempFolder"><strong>Selected  <span class="tempdenom">items</span></strong> (<span class="tempcount">{$tempcount}</span>)</span>:      
    <ul id="tempbox">
      <li class="first" ><a id="inFavorites_tempset" onclick="favoritesToggle(this)" title="Save all selected items to Favorites" href="#saveToFavorites">Save Selected to Favorites</a></li>
      <li class="list"><a title="List selected items for viewing or printing" href="/Tags/SelectedItems">List</a></li>
      <li><a class="dolightbox" title="Email selected items" href="#emailRecords">Email</a></li>
      <li><a class="dolightbox" title="Export selected items" href="#exportMenu">Export</a></li>
      <li><a class="dolightbox" title="Remove all items from Selected Items list" href="#clearSelected">Clear</a></li>
    </ul>
  </div>
  
  <div id="tempFolderEmpty">
    <span class="templine" id="tempFolder"><strong>Selected  <span class="tempdenom">items</span></strong> (<span class="tempcount">{$tempcount}</span>):</span>      
    <ul id="tempboxEmpty">
      <li class="first">Save Selected to <span class="favorites">Favorites</span></li>
      <li class="list">List</li>
      <li>Email</li>
      <li>Export</li>
      <li>Clear</li>
    </ul>
  </div>

