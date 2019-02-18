<table>
  <tr><th>Record ID</th><th>Status</th></tr>
  {foreach from=$resultList item=result}
  <tr>
    <td>{$result.id}</td>
    <td>{if $result.status}:){else}X{/if}</td>
  </tr>
  {/foreach}
</table>