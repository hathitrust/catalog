<div id="bd">
  <div class="yui-main content">
    <div class="yui-b first contentbox">

      <div class="yui-gf resulthead">
        {include file="Admin/menu.tpl"}
        <div class="yui-u">
          <h1>Statistics</h1><br>

          <h2>Executive Summary</h2>
          <table class="citation">
            <tr>
              <th>Total Searches: </th>
              <td>{$searchCount}</td>
            </tr>
            <tr>
              <th>0 Hit Searches: </th>
              <td>{$nohitCount}</td>
            </tr>
            <tr>
              <th>Total Record Views: </th>
              <td>{$recordViews}</td>
            </tr>
          </table>

          <h2>Average Usage</h2>
          <table class="citation">
            <tr>
              <th>Per Day: </th>
              <td>{$avgPerDay}</td>
            </tr>
            <tr>
              <th>Per Week: </th>
              <td>{$avgPerWeek}</td>
            </tr>
            <tr>
              <th>Per Month: </th>
              <td>{$avgPerMonth}</td>
            </tr>
          </table>

          <h2>Top Search Terms</h2>
          <ul>
            {foreach from=$termList item=term}
            <li>({$term.attributes.count}) {$term._content}</li>
            {foreachelse}
            <li>No Searches</li>
            {/foreach}
          </ul>

          <h2>Top Records</h2>
          <ul>
            {foreach from=$recordList item=term}
            <li>({$term.attributes.count}) {$term._content}</li>
            {foreachelse}
            <li>No Record Views</li>
            {/foreach}
          </ul>

          <h2>Usage Summary</h2>
          <table class="citation">
            <tr>
              <th>Top Browsers</th>
              <th>Top Users</th>
            </tr>
            <tr>
              <td>
                <ul>
                  {foreach from=$browserList item=term}
                  <li>({$term.attributes.count}) {$term._content}</li>
                  {/foreach}
                </ul>
              </td>
              <td>
                <ul>
                  {foreach from=$ipList item=term}
                  <li>({$term.attributes.count}) {$term._content}</li>
                  {/foreach}
                </ul>
              </td>
            </tr>
          </table>


        </div>
      </div>

    </div>
  </div>
</div>