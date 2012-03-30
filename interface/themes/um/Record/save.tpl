<form onSubmit="saveRecord('{$id}', this); return false;">
<table>
  <tr><td>Tags: </td><td><input type="text" name="tags" value="{$tags}" size="50"></td></tr>
  <tr><td colspan="2">Spaces will separate tags.  Use quotes for multi-word tags.</td></tr>
  <tr><td>Notes: </td><td><textarea name="notes" rows="3" cols="50">{$notes}</textarea></td></tr>
  <tr><td></td><td><input type="submit" value="Save"></td></tr>
</table>
</form>