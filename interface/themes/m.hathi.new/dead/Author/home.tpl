<script language="JavaScript" type="text/javascript" src="{$path}/services/Search/ajax.js"></script>

<div id="bd">
  <div class="yui-main content">
    <div class="yui-b first contentbox">

      <div class="record">
        {if $lastsearch}
          <p>  <a href="{$url}/Search/Home?{$lastsearch}" class="backtosearch">&laquo; Back to Search Results</a></p>
        {/if}

        {if $info}
        <h2>{$info.name}</h2><br>

        {if $info.image}
        <img src="{$info.image}" alt="{$info.altimage}" class="alignleft">
        {/if}
        {$info.description}
        <p>
          <br clear="All"><a href="http://en.wikipedia.org/wiki/{$info.name}" target="new"><span class="note">Provided by Wikipedia</span></a>
        </p>
        {/if}

        <div class="resulthead">
          {translate text="Showing"}
          <b>{$recordStart}</b> - <b>{$recordEnd}</b>
          {translate text='of'} <b>{$recordCount}</b>
          {translate text='Library items by'} <b>{$authorName}</b>:
        </div>

        {include file="Search/list-list.tpl"}

        <script language="JavaScript" type="text/javascript">DoAjaxQueue();</script>

        {assign var=pageLinks value=$pager->getLinks()}
        <div class="pagination">{$pageLinks.all}</div>

      </div>
    </div>
  </div>

  <!-- Subject Options -->
  <div class="yui-b">
    <div class="box submenu narrow">
      <h4>{translate text='Related Subjects'}</h4>
      <ul class="similar">
      {foreach from=$topics item="topic"}
        <li><a href="{$url}/Search/Home?lookfor=%22{$topic}%22">{$topic}</a></li>
      {/foreach}
    </div>
  </div>
  <!-- End Narrow Search Options -->

</div>