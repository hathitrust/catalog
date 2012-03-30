<div id="bd">
  <div id="yui-main" class="content">
    <div class="yui-b first contentbox">
  
      <h2>{translate text='Login'}</h2><br>

      {if $message}<div class="error">{$message}</div>{/if}
      <div class="result">
      <table class="citation">
        <form method="post" action="{$url}/MyResearch/Home" name="loginForm">
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
        </form>
      </table>
      <script type="text/javascript">document.loginForm.username.focus();</script>

      {if $authMethod != 'LDAP'}
      <a href="{$url}/MyResearch/Account">Create New Account</a>
      {/if}
      </div>
  
  
    </div>
  </div>
</div>