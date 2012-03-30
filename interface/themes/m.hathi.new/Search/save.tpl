<form name="saveForm" onSubmit="saveRecord(); this.reset(); return false;">
<input type="hidden" name="recordId" value="{$recordId}">
<table>
  <tr><td colspan="2" id="recordTitle"></td></tr>
  <tr><td>{translate text='Tags'}: </td><td><input type="text" name="tags" value="{$myTagList}" size="60"></td></tr>
  <tr><td>{translate text='Notes'}: </td><td><textarea name="notes" rows="3" cols="60">{$savedData.notes}</textarea></td></tr>
  <tr><td></td><td><input type="button" value="Save" onClick="saveRecord(); document.forms['saveForm'].reset();"></td></tr>
</table>
</form>

