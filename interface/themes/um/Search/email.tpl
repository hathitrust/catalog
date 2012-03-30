<div align="left">
  {if $message}<div class="error">{$message}</div>{/if}

  <form action="{$url}/Search/Email" method="post" onSubmit="SendEmail(this); return false;">
    <input type="hidden" name="url" value="">
    <b>To:</b><br>
    <input type="text" name="to" size="40"><br>
    <b>From:</b><br>
    <input type="text" name="from" size="40"><br>
    <b>Message:</b><br>
    <textarea name="message" rows="3" cols="40"></textarea><br>
    <input type="submit" name="submit" value="Send">
  </form>
</div>