{literal}
<style type="text/css" media="screen">
  button.tempSelected {
    color: red;
  }
  
  button.tempUnselected {
    color: green;
  }
  
  .tools button.temptoggle {
    margin-top: -.5em;
  }
  
  .erError {
    padding-top: 1em;
  }
</style>
{/literal}



<div id="smsSingle"   style="background-color: #fff; display: none">
  <div class="fancyboxInner">
    <h2>Send record via SMS</h2>
    <form method="post" action="null"
          onSubmit="sendSMS('{$id}', jq(this)); return false;">
      <table>
      {literal}
      <tr>
        <td>Number: </td>
        <td><input type="text" name="to" value="10-Digit Phone Number" onfocus="if(this.value=='10-Digit Phone Number'){this.value=''}" onblur = "if(this.value==''){this.value='10-Digit Phone Number'}"></td>
      </tr>
      {/literal}
      <tr>
        <td>Provider: </td>
        <td>
          <select name="provider">
            <option selected=true value="">Select your carrier</option>
            <option value="att">AT&amp;T</option>
            <option value="verizon">Verizon</option>
            <option value="tmobile">T Mobile</option>
            <option value="sprint">Sprint</option>
            <option value="nextel">Nextel</option>
            <option value="vmobile">Virgin Mobile</option>
            <option value="alltel">Alltel</option>
            <option value="cricket">Cricket</option>
          </select>
        </td>
      </tr>
      <tr>
        <td></td>
        <td><button onclick="sendSMS('{$id}'); return false;">Send</button></td>
      </tr>
      </table>
    </form>
    <div class="erError"></div>
  </div>
</div>

<div id="emailSingle"   style="background-color: #fff; display: none">
  <div class="fancyboxInner">
    <p><strong>Email this item</strong></p>    
    <p>By filling out the form below, you can email this item to yourself or someone else.</p>
    <p>Note that both the "To" and "From" addresses must be complete (e.g., user@umich.edu).</p>
    
    <form action="GET" action="/Search/SearchExport">
      <input type="hidden" name="method" value="emailRecords">
      <input type="hidden" name="id" value="{$id}">
      <table style="margin:0; padding: 0; width: auto;">
        <tbody>
          <tr>
            <td>To:</td><td><input name="to" type="text" size="20"></td>
          </tr>
          <tr>
            <td>From:</td><td><input name="from" type="text" size="20"></td>
          </tr>
          <tr>
            <td>Message:</td><td><textarea  name="message"></textarea></td>
          </tr>
        </tbody>
        </table>
        <input type="button" value="Send email" onclick="emailSelectedRecords({literal}{{/literal}'handpicked': '{$id}'{literal}}{/literal}); return false;">
    </form>
    
    <div class="erError"></div>
  
  </div>
</div>


<div id="emailRecords" style="background-color: #fff; display: none">
  <div class="fancyboxInner">
    <p><strong>Email <span class="tempcount">{$tempcount}</span>  selected <span class="tempdenom">items</span></strong></p>    
    <p>Email the selected <span class="tempcount">{$tempcount}</span> <span class="tempdenom">items</span> to the address given below. Note that both the "To" and "From" addresses must be complete (e.g., user@umich.edu).</p>
    
    <form type="GET" action="/Search/SearchExport">
      <input type="hidden" name="method" value="emailRecords">
      <input type="hidden" name="tag" value="{$uuid}">
      <table style="margin:0; padding: 0; width: auto;">
        <tbody>
          <tr>
            <td>To:</td><td><input name="to" type="text" size="20"></td>
          </tr>
          <tr>
            <td>From:</td><td><input name="from" type="text" size="20"></td>
          </tr>
          <tr>
            <td>Message:</td><td><textarea  name="message"></textarea></td>
          </tr>
        </tbody>
        </table>
        <input type="button" value="Send email" onclick="emailSelectedRecords({literal}{{/literal}'tempset': 1{literal}}{/literal}); return false;">
    </form>
    <div class="erError" style="padding-top: 1em;"></div>
    
   <p style="text-align: right"><a href="#" onclick="replaceLightbox('exportMenu'); return false">(back)</a></p>
  </div>
</div>

<div id="exportRecords" style="background-color: #fff; display: none">
  <div style=" padding:3em;">
    <p><strong>Export or Save <span class="tempcount">{$tempcount}</span> 
               <span class="tempdenom">items</span>
       </strong></p>
    <p>Clicking on the link below will download the records in "RIS" format, which can be read by Endnote, Procite, Zotero, etc. </p>

    <p>You may either open the file in Endnote (or other program) directly, or save it and subsequently import it into your program.
      Saving and then importing will usually correct any foreign-language characters that import incorrectly with a direct import.]</p>

    <p style="text-align: center">
      <a href="/Search/SearchExport?method=ris&amp;tag={$uuid}" onclick="setTimeout('jq.fn.fancybox.close()', 2500); return true;">Export the selected 
          <span class="tempcount">{$tempcount}</span> <span class="tempdenom">items</span> in RIS Format</a>
    </p>
 
     <p style="text-align: right">
       <a href="#" onclick="replaceLightbox('exportMenu'); return false">(back)</a>
     </p>
  </div>
</div>

<div id="exportMenu" style="background-color: #fff; display: none">
  <div class="fancyboxInner">
    <p><strong>Export <span class="tempcount">{$tempcount}</span> {translate text="tempSelected"} <span class="tempdenom">items</span></strong></p>
    <p>You have selected <span class="tempcount">{$tempcount}</span> <span class="tempdenom">items</span>. You may:</p>
    <ul>
      <li><a href="#" onclick="replaceLightbox('exportRefworks'); return false;">send the selected <span class="tempdenom">items</span> to Refworks</a></li>
      <li><a href="#" onclick="replaceLightbox('exportRecords'); return false;">export the {translate text="tempSelected"} <span class="tempdenom">items</span> to a text file or a  program like Endnote, Procite, or Zotero</a></li>
    </ul>
  </div>
</div>

<div id="exportRefworks" style="background-color: #fff; display: none">
  <div class="fancyboxInner">
  <p><strong>Export to Refworks</strong></p>
  <p>Clicking on the button below will send the <span class="tempcount">{$tempcount}</span> {translate text="tempSelected"} 
    <span class="tempdenom">items</span> to Refworks.</p>
  <p>A new window will open, and you may be asked to log into Refworks to continue.[Refworks is allowed access for about ten minutes. If you wait longer than that, you'll need to do the export again.]</p>
    
  <p>Choose the campus through which you use RefWorks:</p>
  <form>
   <input type="radio" name="campus" value="aa"> Ann Arbor<br/>
   <input type="radio" name="campus" value="flint"> Flint<br/>

   <div  style="padding-top: 1em;"><button onclick="exportTempToRefworks('tempset=1'); return false">Export to RefWorks</button></div>
  </form>

  
  <p style="text-align: right">
      <a href="#" onclick="replaceLightbox('exportMenu'); return false">(back)</a>
  </p>  
  </div>
</div>

<div id="exportRefworksSingle" style="background-color: #fff; display: none">
  <div class="fancyboxInner">
  <p><strong>Export to Refworks</strong></p>
  <p>Clicking on the button below will send this item, <i>{$record.title.0|regex_replace:"/[\/\.\;\:\s]+$/":""}</i>, to Refworks.</p>
  <p>A new window will open, and you may be asked to log into Refworks to continue.[Refworks is allowed access for about ten minutes. If you wait longer than that, you'll need to do the export again.]</p>
    
  <p>Choose the campus through which you use RefWorks:</p>
  <form>
   <input type="radio" name="campus" value="aa"> Ann Arbor<br/>
   <input type="radio" name="campus" value="flint"> Flint<br/>

    <div  style="padding-top: 1em;"><button style="padding-top: 1em;" onclick="exportTempToRefworks('handpicked={$record.id}'); return false">Export to RefWorks</button></div>
  </form>

  </div>
</div>

<div id="clearSelected" style="background-color: #fff; display: none">
  <div class="fancyboxInner">
  <p><strong>Clear selected <span class="tempdenom">items</span> </strong></p>
  <p> By clicking the below button, you will remove the "selected" status from the  <span class="tempcount">{$tempcount}</span> 
    currently selected <span class="tempdenom">items</span>. Are you sure?</p>
    
    <p><button onclick="clearTempItems();">Clear selected items</button></p>
    <div class="erError" style="padding-top: 1em;"></div>
    
  </div>
</div>

<div id="deleteFavorite" style="background-color: #fff; display: none">
  <div class="fancyboxInner">
    <h2>Remove Favorite</h2>
    <p>Are you sure you want to remove:</p>
    
    <div style="padding-left: 1.5em;"><i><span class="favFormTitle"></span></i></div>
    
    <p>...from your list of favorites?</p>
    
    <p><button class="deleteButton">Remove</button>
       <button onclick="jq.fn.fancybox.close()">Cancel</button></p>
  </div>
</div>

<a id="openEditFavorite" class="dolightbox" href="#editFavorite" style="display:none"></a>
<div id="editFavorite" style="background-color: #fff; display: none">
  <div class="fancyboxInner">
    <h2><span class="favFormTitle"></span></h2>
    <input type="hidden" name="id">
    
    <div class="currentFavorites">
    <h3>Choose which tags to keep or remove</h3>
    <table class="currentFavorites">
      <thead><tr><th style="width: 10%">Keep?</th><th>Tag</th></tr></thead>
      <tbody class="currentFavoritesCheckboxes">
      </tbody>
    </table>
    </div>
    
    <p><label for="newtags">Add new tags (separate with commas)</label><br>
      <input id="newtags" name="newtags" size="45"></p>
    
    <div class="buttonlist">
      <!-- this.parent.parent to get to the whole div with all the data in it -->
      <button onclick="editFavorite(jq(this).parent().parent())">Submit</button>
      <button onclick="jq.fn.fancybox.close();return false">Cancel</button>
      <button onclick="openDeleteFavorite(jq(this).parent().parent());return false;" 
              href="#deleteFavorite" class="deleteButton">Remove</button>
    </div>
  </div>
</div>


<div id="saveToFavorites" style="background-color: #fff; display: none">
  <div class="fancyboxInner">
    
    {if $username}
      <p><strong class="fbTitle">Save to <span class="favorites">Favorites</span></strong></p>
      
      <p class="favoritesDialogStatus">Saving to Favorites...</p>
      
      <div class="undoForm" style="display:none">
        Second thoughts? <button name="undoList" ref="" onclick="favoritesUndo(this)">Undo</button>.
      </div>
      
      <div class="favoritesDialogFinished" style="display:none">
        <h3>Optional additional tags</h3>
        <p><label for="additional_tags">You may optionally add additional tags to these items.</label></p>
    
      
          <form >
            <p>Separate multiple tags with commas.</p>
            <p><input type="text" size="40" name="additional_tags" /></p>
    
            <div width="80%">
              <button ref="" class="addExtraTags" onclick="addExtraTags(this); return false;">Add these tags</button>
              <span style="float: right"><button onclick="lbclose(); return false;">Close</button></span>
           </div>
          </form>
          
          
        <div class="erError" style="padding-top: 1em;"></div>
          
      </div>
    {else}
      <p><strong>Saving to <span class="favorites">Favorites</span></strong></p>
      <p>...requires you to first <a href="{$loginURL}">{translate text="Login"}</a></p>
    {/if}
  </div>
</div>