<div id="bd">
  <div class="yui-main content">
    <div class="yui-b first contentbox">

      <div class="yui-gf resulthead">
        {include file="../../services/Admin/templates/menu.tpl"}
        <div class="yui-u">
          <h1>Edit Record</h1>

          <form method="post">
            <table class="citation">
              {foreach from=$record item=value key=field}
              <tr>
                <th>{$field}: </th>
                <td><input type="text" name="{$field}[]" value="{$value}" size="50"></td>
              </tr>
              {/foreach}
            </table>
            <input type="submit" name="submit" value="Save">
          </form>
        </div>
      </div>

    </div>
  </div>
</div>