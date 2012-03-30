<div class="searchbox">
  <div class="yui-b" style="margin-left: 0em; *margin-left: 0em; min-width: 670px;">  
    {if $suppress_searchbox}
      <div style="margin: none; padding: none;">        
          <div style="margin-left: 2em; padding-bottom: 1em; padding-top: 15px">
            <a href="/">&lt; Back to basic search</a>
{*            <a style="color: green; position: absolute; right: 2em;" href="#" onclick="fillLightbox('feedback_form');return false;">Tell Us What You Think!</a>                     
*}
              <span style="position: absolute; right: 2em;">             
                 {if $username}
                     <a class="clickpostlog" ref="topaccount" href="{$path}/MyResearch/CheckedOut" title="Account information for {$username}">{$username}'s Account</a> |
                     <a href="{$path}/MyResearch/Logout">{translate text="Log Out"}</a>
                  {else}
                    <a class="clickpostlog" ref="topaccountNLI" href="{$path}/MyResearch/CheckedOut" title="Log in and view your  account information">My Account</a> |
                    <a href="{$loginURL}">{translate text="Login"}</a> 
                  {/if}
               </span>

          </div>
          <div>
            <span style="position: absolute; right: 2em;padding: 3px; border: 1pt solid black;">
              <a href="http://mirlyn-classic.lib.umich.edu/">Mirlyn Classic</a>
            </span>
          </div>

        </div>
      </div>
      {else}  
    
      <form method="GET" id="searchForm" action="{$path}/Search/Home" name="searchForm" onsubmit="fixform(this)" class="search">  
        <input type="hidden" name="checkspelling" value="true" />
        <div id="institute_select" style="padding-bottom: .75em; position: relative;">
          <label for="inst" style="margin-left: 0px; width: 6em;">Search in: </label><select name="inst" class="stickyform" >
            {foreach from=$instList item=instName key=instKey}
              <option value="{$instKey}"{if $inst == $instKey} selected{/if}>{$instName}</option>
            {/foreach}
          </select>
          
          <a style="padding-right: 2.5em; padding-left: 2.5em; position: relative; " href="{$path}/Search/Advanced" class="small">{translate text="Advanced"}</a>          
          <a href="#searchtips_text" class="dolightbox">Search Tips</a>
          
  {* <p>
      <a style="border: 1pt solid gray; padding: 3px;color: green; position: absolute; right: 2em;" href="#" onclick="fillLightbox('feedback_form');return false;">Tell Us What You Think!</a>
     </p>
  *}

          <span style="position: absolute; right: 2em; top: 0em;">             
            {if $username}
                <a class="clickpostlog" ref="topaccount" href="{$path}/MyResearch/CheckedOut" title="Account information for {$username}">{$username}'s Account</a> |
                <a class="clickpostlog" ref="topfavorites" href="/MyResearch/Favorites" title="Favorites"><span class="favorites">Favorites</span></a> |                
                <a href="{$path}/MyResearch/Logout">{translate text="Log Out"}</a>
             {else}
                <a class="clickpostlog" ref="topaccountNLI" href="{$path}/MyResearch/CheckedOut" title="Log in and view your  account information">My Account</a> |
                <a class="clickpostlog" ref="topfavoritesNLI" href="/MyResearch/Favorites" title="Favorites"><span class="favorites">Favorites</span></a> |                
                <a href="{$loginURL}">{translate text="Login"}</a> 
             {/if}           
          </span>

       </div>
      
      <div style="margin: none; padding: none; position: relative; width: 100%; min-width: 600px;">

          <!-- Clear button -->
            <span style="margin-left: 0em; width: 6em;">
              {literal}
              <input type="button" value="Clear" onclick="jq('#searchForm').clearForm(['inst']);">
              {/literal}
            </span>
                 
           <!-- Index selection -->
             <span style="margin-left: 0em;">
               <input type="text" name="lookfor" id="lookfor" size="30" value="{$lookfor|escape:"html"}">
               <select name="type" id="searchtype">
                 <option value="all">{translate text="All Fields"}</option>
                 <option value="title"{if $type == 'title'} selected{/if}>{translate text="Title"}</option>
                 <option value="author"{if $type == 'author'} selected{/if}>{translate text="Author"}</option>
                 <option value="subject"{if $type == 'subject'} selected{/if}>{translate text="Subject"}</option>
                 <option value="hlb"{if $type == 'hlb'} selected{/if}>{translate text="Categories"}</option>
                 <option value="callnumber"{if $type == 'callnumber'} selected{/if}>{translate text="Call Number"} starts with</option>
                 <option value="isn"{if $type == 'isn'} selected{/if}>ISBN / ISSN / etc.</option>
                 <option value="serialtitle"{if $type == 'serialtitle'} selected{/if}>Journal/Serial name</option>
               </select>
               <input  type="submit" name="submit" value="{translate text="Find"}">
            </span>
           <span style="position: absolute; right: 2em; top: 0em; padding: 3px;  border: 1pt solid black;">
              <a class="clickpostlog" ref="topclassic" href="http://mirlyn-classic.lib.umich.edu/">Mirlyn Classic</a>
            </span>       
           

       </div> <!-- End of the margin:none padding:none -->

       <input type="hidden" name="use_dismax" value="1">
       
      </form>
    {/if}
  </div>
</div>

