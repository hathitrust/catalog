<div id="bd">
  <div class="yui-main content">
    <div class="yui-b first contentbox">
      <div class="record">
        <h2>Your Recent Searches</h2>
        <table class="datagrid" width="100%">
          <tr>
            <th width="40%">Search Phrase</th>
            <th width="20%">Type</th>
            <th width="40%">Limits</th>
          </tr>
          {foreach item=info key=location from=$links}
          <tr>
            <td><a href="{$url}/Search/Home?{$location}">{$info.phrase}</a></td>
            <td>{$info.type}</td>
            <td>{$info.format}</td>
          </tr>
          {/foreach}
        </table>
      </div>
    </div>
  </div>
</div>