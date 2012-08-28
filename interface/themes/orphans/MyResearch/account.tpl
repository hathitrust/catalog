<div id="bd">
  <div class="yui-main content">
    <div class="yui-b first contentbox">

      <h2>User Account</h2><br>

      {if $message}<div class="error">{$message}</div>{/if}
      <div class="result">
      <table class="citation">
        <form method="post" action="{$url}/MyResearch/Account" name="loginForm">
        <tr>
          <td>First Name: </td>
          <td><input type="text" name="firstname" value="{$formVars.firstname}" size="30"></td>
        </tr>
        <tr>
          <td>Last Name: </td>
          <td><input type="text" name="lastname" value="{$formVars.lastname}" size="30"></td>
        </tr>
        <tr>
          <td>Email Address: </td>
          <td><input type="text" name="email" value="{$formVars.email}" size="30"></td>
        </tr>

        <tr>
          <td>Desired Username: </td>
          <td><input type="text" name="username" value="{$formVars.username}" size="30"></td>
        </tr>
        <tr>
          <td>Password: </td>
          <td><input type="password" name="password" size="15"></td>
        </tr>
        <tr>
          <td>Password Again: </td>
          <td><input type="password" name="password2" size="15"></td>
        </tr>

        <tr>
          <td></td>
          <td><input type="submit" name="submit" value="Submit"></td>
        </tr>
        </form>
      </table>
      </div>

    </div>
  </div>
</div>