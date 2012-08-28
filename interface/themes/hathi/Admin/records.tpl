<div id="bd">
  <div class="yui-main content">
    <div class="yui-b first contentbox">

      <div class="yui-gf resulthead">
        {include file="Admin/menu.tpl"}
        <div class="yui-u">
          <h1>Record Management</h1>

          <h2>Edit a Record</h2>
          <form method="get" name="recordEdit">
           <input type="hidden" name="util" value="">
           Record Id<br>
           <input type="text" name="id" size="50"><br>
           <input type="submit" name="submit" value="Edit" onClick="document.forms['recordEdit'].elements['util'].value='editRecord';">
           <input type="submit" name="submit" value="Delete" onClick="document.forms['recordEdit'].elements['util'].value='deleteRecord';">
          </form>

          <h2>Utilities</h2>
          <dl>
            <dt><a href="{$url}/Admin/Records?util=deleteSuppressed">Delete Suppressed Records</a></dt>
            <dd>This process will delete any suppressed records from the VuFind Index.</dd>

            <dt>Process Authority Records</dt>
            <dd>This process will update all records with the authority records to ensure that the authority data is included in the search index</dd>
          </dl>
        </div>
      </div>

    </div>
  </div>
</div>