<script language="JavaScript" type="text/javascript" src="{$path}/services/Browse/ajax.js"></script>

<div id="bd">
  <div id="yui-main" class="content">
    <div class="contentbox" style="margin-right: 15px;">

      <div class="yui-g">
        <div class="yui-g first">
          <div class="yui-u first">
            <div id="tabNav" style="margin: 0px;">
            <ul class="browse" id="list1">
              <li style="float: none;"><a href="{$url}/Browse/Tag">{translate text="Tag"}</a></li>
              <li style="float: none;"><a href="{$url}/Browse/LCC">{translate text="Call Number"}</a></li>
              <li style="float: none;"><a href="{$url}/Browse/Author">{translate text="Author"}</a></li>
              <li style="float: none;"><a href="{$url}/Browse/Topic">{translate text="Topic"}</a></li>
              <li style="float: none;" class="active"><a href="{$url}/Browse/Genre">{translate text="Genre"}</a></li>
              <li style="float: none;"><a href="{$url}/Browse/Region">{translate text="Region"}</a></li>
              <li style="float: none;"><a href="{$url}/Browse/Era">{translate text="Era"}</a></li>
            </ul>
            </div>
          </div>
          <div class="yui-u" id="browse2">
            <div id="tabNav" style="margin: 0px;">
            <ul class="browse" id="list2">
              <li style="float: none;"><a href="{$url}/Browse/Author" onClick="this.style.background='URL('+path+'/images/tab_active_bg.jpg)'; LoadAlphabet('genreStr', 'list3', 'genreStr'); return false">{translate text="By Alphabetical"}</a></li>
              <li style="float: none;"><a href="{$url}/Browse/Author" onClick="this.style.background='URL('+path+'/images/tab_active_bg.jpg)'; LoadSubject('topicStr', 'list3', 'genreStr'); return false">{translate text="By Topic"}</a></li>
              <li style="float: none;"><a href="{$url}/Browse/Author" onClick="this.style.background='URL('+path+'/images/tab_active_bg.jpg)'; LoadSubject('geographicStr', 'list3', 'genreStr'); return false">{translate text="By Region"}</a></li>
              <li style="float: none;"><a href="{$url}/Browse/Author" onClick="this.style.background='URL('+path+'/images/tab_active_bg.jpg)'; LoadSubject('era', 'list3', 'genreStr'); return false">{translate text="By Era"}</a></li>
            </ul>
            </div>
          </div>
        </div>
        <div class="yui-g">
          <div class="yui-u first" id="browse3">
            <div id="tabNav" style="margin: 0px;">
            <ul class="browse" id="list3">
            </ul>
            </div>
          </div>
          <div class="yui-u" id="browse4">
            <div id="tabNav" style="margin: 0px;">
            <ul class="browse" id="list4">
            </ul>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>