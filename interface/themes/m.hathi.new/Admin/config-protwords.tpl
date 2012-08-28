<div id="bd">
  <div class="yui-main content">
    <div class="yui-b first contentbox">

      <div class="yui-gf resulthead">
        {include file="Admin/menu.tpl"}
        <div class="yui-u">
          <h1>Protected Words Configuration</h1>

          <p>
            The Protected Words are a list of words that will prevent VuFind from using word stemming on.
          </p>

          <form method="post">
            <textarea name="stopwords" rows="20" cols="20">{$protwords}</textarea><br>
            <input type="submit" name="submit" value="Save">
          </form>
        </div>
      </div>

    </div>
  </div>
</div>