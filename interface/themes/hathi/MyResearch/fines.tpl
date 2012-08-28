<div id="bd">
  <div class="yui-main content">
    <div class="yui-b first contentbox">
    {if $user->cat_username}
      <h4>{translate text='Your Fines'}</h4>
      {$finesData}
    {else}
      <h4>{translate text='Library Catalog Profile'}</h4>
      <p>{translate text='In order to establish your account profile, please enter the following information'}:
      <form method="post">
        Library Catalog Username:<br>
        <input type="text" name="cat_username" value="" size="25"><br>
        Library Catalog Password:<br>
        <input type="text" name="cat_password" value="" size="25"><br>
        <input type="submit" name="submit" value="Save">
      </form>
    {/if}
    </div>
  </div>

  {include file="MyResearch/menu.tpl"}

</div>