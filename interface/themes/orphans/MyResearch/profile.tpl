<div id="bd">
  <div id="yui-main" class="content">
    <div class="yui-b first contentbox">
    {if $user->cat_username}
      <h3>{translate text='Your Profile'}</h3>
      <table>
        <tr><th>First Name:</th><td>{$profile.firstname}</th></tr>
        <tr><th>Last Name:</th><td>{$profile.lastname}</th></tr>
        <tr><th>Address 1:</th><td>{$profile.address1}</th></tr>
        <tr><th>Address 2:</th><td>{$profile.address2}</th></tr>
        <tr><th>Zip:</th><td>{$profile.zip}</th></tr>
        <tr><th>Phone Number:</th><td>{$profile.phone}</th></tr>
        <tr><th>Group:</th><td>{$profile.group}</th></tr>
      </table>
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