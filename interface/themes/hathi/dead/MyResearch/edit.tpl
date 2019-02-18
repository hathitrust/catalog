<div id="bd">
  <div class="yui-main content">
    <div class="yui-b first contentbox">
      <div class="yui-ge">

        <div class="record">

          <h1>{$record.title}</h1>

          <form method="post">
          <table>
            <tr><td>Tags: </td><td><input type="text" name="tags" value="{$myTagList}" size="50"></td></tr>
            <tr><td>Notes: </td><td><textarea name="notes" rows="3" cols="50">{$savedData->notes}</textarea></td></tr>
            <tr><td></td><td><input type="submit" name="submit" value="Save"></td></tr>
          </table>
          </form>

        </div>

      </div>
    </div>
  </div>
</div>