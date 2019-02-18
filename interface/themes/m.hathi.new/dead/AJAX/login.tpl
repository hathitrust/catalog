{if $message}<div class="error">{$message}</div>{/if}
<form method="post" action="{$url}/MyResearch/Home" name="loginForm"
      onSubmit="SaltedLogin(this, '{$followupModule}', '{$followupAction}', '{$recordId}', null, '{translate text='Add to Favorites'}'); {$followUp} return false;">
<input type="hidden" name="salt" value="">
<table class="citation">
  <tr>
    <td>{translate text='Username'}: </td>
    <td><input type="text" name="username" value="{$username}" size="15"></td>
  </tr>

  <tr>
    <td>{translate text='Password'}: </td>
    <td><input type="password" name="password" size="15"></td>
  </tr>

  <tr>
    <td></td>
    <td><input type="submit" name="submit" value="{translate text='Login'}"></td>
  </tr>
</table>
</form>
<script type="text/javascript">document.loginForm.username.focus();</script>

{if $authMethod != 'LDAP'}
<a href="{$url}/MyResearch/Account">Create New Account</a>
{/if}