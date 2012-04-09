  <form action="{$url}/Record/{$id}/Hold" method="post" onSubmit="PlaceHold({$id}, this); return false;">
      <table>
        <tr><th>barcode:</th><td>{$barcode}</th></tr>
        <tr><th>Name:</th><td>{$patron_name}</th></tr>
        <tr><th>ID:</th><td>{$patron_id}</th></tr>
      </table>
      <input type="hidden" name="barcode" value="{$barcode}">
      <input type="hidden" name="patron_name" value="{$patron_name}">
      <input type="hidden" name="patron_id" value="{$patron_id}">
      <input type="submit" name="submit" value="Submit">
  </form>
