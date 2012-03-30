{if $message}
  <div style="color: green">{$message}<br><br></div>
{/if}

<a href="{$url}/Record/{$id}" class="backtosearch"><img style="vertical-align: middle;" alt="Back to full record display" src="/static/umichwebsite/images/return.png">{translate text="Back to full record display"}</a><br><br>

<table>
  <tr><th>Title:</th><td>{"<br>"|implode:$record.title}</th></tr>
  <tr><th>Call number:</th><td>{$callnumber}</th></tr>
  <tr><th>Owning library:</th><td>{$location}</th></tr>
  <tr><th>Name:</th><td>{$patron_name}</th></tr>
</table>

