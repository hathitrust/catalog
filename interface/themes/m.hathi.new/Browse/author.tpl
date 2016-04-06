<script language="JavaScript" type="text/javascript" src="/services/Browse/ajax.js"></script>

<div id="bd">
  <div class="yui-main content">
    <div class="contentbox" style="margin-right: 15px;">

      <div class="yui-g">
        <div class="yui-g first">
          <div class="yui-u first">
            <div id="tabNav" style="margin: 0px;">
            <ul class="browse" id="list1">
              <li style="float: none;"><a href="/Browse/Tag">{translate text="Tag"}</a></li>
              <li style="float: none;"><a href="/Browse/LCC">{translate text="Call Number"}</a></li>
              <li class="active" style="float: none;"><a href="/Browse/Author">{translate text="Author"}</a></li>
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
              <li style="float: none;"><a href="/Browse/Author" onClick="this.style.background='URL('+path+'/images/tab_active_bg.jpg)'; document.getElementById('list4').innerHTML=''; LoadAlphabet('author-letter', 'list3', 'authorStr'); return false">{translate text="By Alphabetical"}</a></li>
              <li style="float: none;"><a href="/Browse/Author" onClick="this.style.background='URL('+path+'/images/tab_active_bg.jpg)'; document.getElementById('list4').innerHTML=''; LoadSubject('callnumber-letter', 'list3', 'authorStr'); return false">{translate text="By Subject Area"}</a></li>
              <li style="float: none;"><a href="/Browse/Author" onClick="this.style.background='URL('+path+'/images/tab_active_bg.jpg)'; document.getElementById('list4').innerHTML=''; LoadSubject('topicStr', 'list3', 'authorStr'); return false">{translate text="By Topic"}</a></li>
              <li style="float: none;"><a href="/Browse/Author" onClick="this.style.background='URL('+path+'/images/tab_active_bg.jpg)'; document.getElementById('list4').innerHTML=''; LoadSubject('genreStr', 'list3', 'authorStr'); return false">{translate text="By Genre"}</a></li>
              <li style="float: none;"><a href="/Browse/Author" onClick="this.style.background='URL('+path+'/images/tab_active_bg.jpg)'; document.getElementById('list4').innerHTML=''; LoadSubject('geographicStr', 'list3', 'authorStr'); return false">{translate text="By Region"}</a></li>
              <li style="float: none;"><a href="/Browse/Author" onClick="this.style.background='URL('+path+'/images/tab_active_bg.jpg)'; document.getElementById('list4').innerHTML=''; LoadSubject('era', 'list3', 'authorStr'); return false">{translate text="By Era"}</a></li>
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