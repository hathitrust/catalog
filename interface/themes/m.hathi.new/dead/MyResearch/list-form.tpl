<form method="post" action="{$url}/MyResearch/EditList" name="listForm"
      onSubmit="addList(this.elements['title'].value, this.elements['desc'].value,
                        this.elements['public'].value, '{$recordId}',
                        '{translate text='Add to Favorites'}'); return false;">
  List:<br>
  <input type="text" name="title" value="{$list->title}" size="50"><br>
  Description:<br>
  <textarea name="desc" rows="3" cols="50">{$list->desc}</textarea><br>
  Access:<br>
  Public <input type="radio" name="public" value="true">
  Private <input type="radio" name="public" value="false" checked><br>
  <input type="submit" name="submit" value="Save">
</form>