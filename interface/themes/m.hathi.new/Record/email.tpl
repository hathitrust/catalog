<div align="left">
  {if $message}<div class="error">{$message}</div>{/if}

  <form action="{$url}/Record/{$id}/Email" method="post" id="popupForm" name="popupForm"
        onSubmit="SendEmail('{$id}', this.elements['to'].value, this.elements['from'].value, this.elements['message'].value);
                  return false;">
    <b>To:</b><br>
    <input type="text" name="to" size="40" id="to"><br>
    <b>From:</b><br>
    <input type="text" name="from" size="40" id="from"><br>
    <b>Message:</b><br>
    <textarea name="message" rows="3" cols="40" id="message"></textarea><br>
    <input type="submit" name="submit" value="Send">
  </form>
</div>