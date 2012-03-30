<div align="left">
  {if $message}<div class="error">{$message}</div>{/if}

  <form action="{$url}/Search/Email" method="post" onSubmit="SendEmail(this); return false;">
    <input type="hidden" name="url" value="">
    <label for="to"><strong>To:</strong></label><br>
    <input type="text" id="to" name="to" size="40"><br>
    <label for="from"><strong>From:</strong></label><br> 
    <input type="text" id="from" name="from" size="40"><br>
    <label for="message"><strong>Message:</strong></label><br>
    <textarea id="message" name="message" rows="3" cols="40"></textarea><br>
    <input type="submit" name="submit" value="Send">
  </form>
</div>
