<div id="bd">
  <div class="yui-main content">
    <div class="yui-b first contentbox">
      <div class="record">

        <form method="GET" action="{$url}/Search/NewItem" name="searchForm" class="search">
          <h2>Find New Items</h2>
          <br>
          <table>
            <tr><th>Range: </th></tr>
            <tr>
              <td>
                <input type="radio" name="range" value="1" checked="true"> Yesterday<br>
                <input type="radio" name="range" value="5"> Past 5 Days<br>
                <input type="radio" name="range" value="30"> Past 30 Days<br>
                <br>
              </td>
            </tr>
            <tr><th>Department: </th></tr>
            <tr>
              <td>
                <select name="department" multiple="true" size="10">
                {foreach from=$fundList item="fund"}
                  <option value="{$fund}">{$fund}</option>
                {/foreach}
                </select>
              </td>
            </tr>
          </table>
          <input type="submit" name="submit" value="Find">
        </form>

        <p align="center"><a href="/Search/NewItem/RSS">New Item Feed</a></p>

      </div>
    </div>
  </div>
</div>