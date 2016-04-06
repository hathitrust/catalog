<div class="searchbox">
  <h2 class="SkipLink">Search Catalog</h2>
  <div class="yui-b" style="margin-left: 0em; *margin-left: 0em;">

    {if $suppress_searchbox}
      <!--
        <div style="margin: none; padding: none;">
          <div style="margin-left: 5em; padding-bottom: 1em; padding-top: 15px">
            <a href="/?&amp;inst={$inst}">&lt; Back to basic search</a>
          </div>
          <div>
            <span style="position: absolute; right: 2em;">
                {if $username}
                    <a href="/MyResearch/Home" title="Account information for {$username}">My Account</a> |
                    <a href="/MyResearch/Logout">{translate text="Log Out"}</a></span>
                {else}
                  <a href="/MyResearch/Home" title="Log in and view your  account information">My Account</a> |
                  <a href="{$loginURL}">{translate text="Login"}</a>
                {/if}
            </span>
          </div>
        </div>-->

    {else}


      <form method="GET" id="searchForm"
            action="/Search/Home" name="searchForm" class="search"
            onsubmit="trimForm(this.lookfor); return true;">
        <div id="searchGraphic">
          <img src="/images/hathi/SearchArrow_Cat.png" alt="Catalog Search">
        </div>
        <input type="hidden" name="checkspelling" value="true" >

        <div id="searchboxCont">

           <!-- Index selection -->
               <label for="lookfor" class="skipLink">Search Catalog</label>
               <input type="text" name="lookfor" id="lookfor" size="30" value="{$lookfor|escape:"html"}">
               <label for="searchtype" class="skipLink">Select type of search</label>
               <select name="type" id="searchtype">
                 <option value="all">{translate text="All Fields"}</option>
                 <option value="title"{if $type == 'title'} selected{/if}>{translate text="Title"}</option>
                 <option value="author"{if $type == 'author'} selected{/if}>{translate text="Author"}</option>
                 <option value="subject"{if $type == 'subject'} selected{/if}>{translate text="Subject"}</option>
                 <!--<option value="hlb"{if $type == 'hlb'} selected{/if}>{translate text="Categories"}</option>-->
                 <!--<option value="callnumber"{if $type == 'callnumber'} selected{/if}>{translate text="Call Number"} / in progress</option>-->
                 <option value="isn"{if $type == 'isn'} selected{/if}>ISBN/ISSN</option>
                 <option value="publisher" {if $type4 == 'publisher'} selected{/if}>{translate text="Publisher"}</option>
                 <option value="series" {if $type4 == 'series'} selected{/if}>{translate text="Series Title"}</option>
                 <option value="year" {if $type4 == 'year'} selected{/if}>{translate text="Year of Publication"}</option>
                 <!-- <option value="tag"{if $type == 'tag'} selected{/if}>Tag</option> -->
               </select>
               <input type="hidden" name="sethtftonly" value="true">
               <input type="checkbox" name="htftonly" value="true" id="fullonly" {if $ht_fulltextonly}checked="checked"{/if}>&nbsp;<label for="fullonly">Full view only</label>
               <input  type="submit" name="submit" value="{translate text="Find"}">
        {*
               {if $lookfor }

                  <br>
                  <input type="radio" name="search" value="new" checked="on"> New Search
                  <!--<input type="radio" name="search" value="within" onClick="document.forms['searchForm'].elements['lookfor'].value=''; document.forms['searchForm'].elements['lookfor'].focus();"> Search Within-->
                  <input type="radio" name="search" value="within"> Search Within
              {/if}
        *}


          <!-- fixme:suz hidden until advanced search can work better -->
            <!-- <a style="padding-right: 2.5em; position: relative; " href="/Search/Advanced/{$inst}" class="small">{translate text="Advanced"}</a>           -->

            <span id="searchLinks">
              <a href="/Search/Advanced">{translate text="Advanced Catalog Search"}</a>
              <a href="#" id="searchTips">Search Tips</a>
            </span>

        <!--
            <span style="position: absolute; right: 2em;">
            {if $username}
                <a href="/MyResearch/Home&amp;inst={$inst}" title="Account information for {$username}">Your Account</a> |
                <a href="/MyResearch/Logout">{translate text="Log Out"}</a></span>
             {else}
                <a href="/MyResearch/Home" title="Log in and view your  account information">My Account</a> |
                 <a href="{$loginURL}&amp;inst={$inst}">{translate text="Login"}</a>
             {/if}
          </span>
      -->


        <!-- Login
{*         <div style="float:right;">
           <div style="margin-left: 5em; margin-right: 2em;">

           {if $username}
              <a href="/MyResearch/Home&amp;inst={$inst}" title="Account information for {$username}">Your Account</a> |
              <a href="/MyResearch/Logout">{translate text="Log Out"}</a></span>
           {else}
               <a href="{$loginURL}&amp;inst={$inst}">{translate text="Login"}</a>
           {/if}
           </div>
         </div>
*}-->

       </div> <!-- End of the margin:none padding:none -->
      </form>
    {/if}
  </div>
</div>
