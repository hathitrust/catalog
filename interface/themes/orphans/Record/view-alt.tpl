<div id="bd">
  <div class="yui-main content">
    <div class="yui-b first contentbox">
      <div class="yui-ge">

        <div class="record">
          <a href="{$url}/Record/{$id}/Home" class="backtosearch">&laquo; {translate text="Back to Record"}</a>

          <h1>{$record.245.a.0} {$record.245.b.0}</h1>
          {include file="Record/$subTemplate"}

        </div>

      </div>
    </div>
  </div>
</div>
