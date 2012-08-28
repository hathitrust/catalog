<div id="bd">
  <div class="yui-main content">
    <div class="yui-b first contentbox">
    {if $profile}
      <h4>{translate text='Your Profile'}</h4>
      <a target="new" class="clickpostlog" ref="mrToWolverine" href="http://wolverineaccess.umich.edu"> Update Address via Wolverine Access</a>
      <table style="width: auto">
        <tr><th>First Name:</th><td>{$profile.firstname}</th></tr>
        <tr><th>Last Name:</th><td>{$profile.lastname}</th></tr>
        <tr><th>Address 1:</th><td>{$profile.address1}</th></tr>
        <tr><th>Address 2:</th><td>{$profile.address2}</th></tr>
        <tr><th>Zip:</th><td>{$profile.zip}</th></tr>
        <tr><th>Phone Number:</th><td>{$profile.phone}</th></tr>
        <tr><th>Email:</th><td>{$profile.email}</th></tr>
      </table>
    {else}
      {translate text="Can't get patron information from Mirlyn for $username"}.
    {/if}
    </div>
  </div>

  {include file="MyResearch/menu.tpl"}

</div>
