<div id="searchbox">

<!-- fixme:suzchap since search doesn't need to show on any page but the main, no need for an if/else to decide if it should show or not so I've simplified this template
to just be for the search box that gets called by the home.tpl-->

  <form method="GET" id="searchForm" action="{$path}/Search/Home" name="searchForm" class="search" onsubmit="trimForm(this.lookfor); return true;">
    <input class="search-field" type="hidden" name="checkspelling" value="true" autocapitalize="off" autocorrect="off"  />

    <div id="searchboxCont">

      <!-- Index selection -->
      <h3>Search Catalog</h3>
      <input class="search-field" type="text" name="lookfor" id="lookfor" size="30" value="{$lookfor|escape:"html"}"/>
      <select name="type" id="searchtype">
        <option value="all">{translate text="All Fields"}</option>
        <option value="title"{if $type == 'title'} selected{/if}>{translate text="Title"}</option>
        <option value="author"{if $type == 'author'} selected{/if}>{translate text="Author"}</option>
        <option value="subject"{if $type == 'subject'} selected{/if}>{translate text="Subject"}</option>
        <option value="isn"{if $type == 'isn'} selected{/if}>ISBN/ISSN</option>
        <option value="publisher" {if $type4 == 'publisher'} selected{/if}>{translate text="Publisher"}</option>
        <option value="series" {if $type4 == 'series'} selected{/if}>{translate text="Series Title"}</option>
        <option value="year" {if $type4 == 'year'} selected{/if}>{translate text="Year of Publication"}</option>
      </select>
      <input type="submit" name="submit" value="{translate text="Find"}"/>

    </div> 
  </form>
</div>
