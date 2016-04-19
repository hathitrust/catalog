<script language="JavaScript" type="text/javascript" src="/services/Browse/ajax.js"></script>

<div id="bd">
  <div class="yui-main content">
    <div class="contentbox" style="margin-right: 15px;">

      <div class="yui-g">
        <div class="yui-g first">
          <div class="yui-u first">
            <div id="tabNav" style="margin: 0px;">
            <ul class="browse" id="list1">
              <li style="float: none;" class="active"><a href="/Browse/Tag">{translate text="Tag"}</a></li>
              <li style="float: none;"><a href="/Browse/LCC">{translate text="Subject Area"}</a></li>
              <li style="float: none;"><a href="/Browse/Author">{translate text="Author"}</a></li>
              <li style="float: none;"><a href="/Browse/Topic">{translate text="Topic"}</a></li>
              <li style="float: none;"><a href="/Browse/Genre">{translate text="Genre"}</a></li>
              <li style="float: none;"><a href="/Browse/Region">{translate text="Region"}</a></li>
              <li style="float: none;"><a href="/Browse/Era">{translate text="Era"}</a></li>
            </ul>
            </div>
          </div>
          <div class="yui-u" id="browse2">
            <div id="tabNav" style="margin: 0px;">
            <ul class="browse" id="list2">
              <li style="float: none;"{if $findby == "alphabetical"} class="active"{/if}><a href="/Browse/Tag?findby=alphabetical">{translate text="By Alphabetical"}</a></li>
              <li style="float: none;"{if $findby == "popularity"} class="active"{/if}><a href="/Browse/Tag?findby=popularity">{translate text="By Popularity"}</a></li>
              <li style="float: none;"{if $findby == "recent"} class="active"{/if}><a href="/Browse/Tag?findby=recent">{translate text="By Recent"}</a></li>
            </ul>
            </div>
          </div>
        </div>
        <div class="yui-g">
          <div class="yui-u first" id="browse3">
            <div id="tabNav" style="margin: 0px;">
            <ul class="browse" id="list3">
            {foreach from=$tagList item=tag}
              <li style="float: none;"><a href="">{$tag->tag} ({$tag->cnt})</a></li>
            {/foreach}
            </ul>
            </div>
          </div>
          <div class="yui-u" id="browse4">
          </div>
        </div>
      </div>

    </div>
  </div>
</div>