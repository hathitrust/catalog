<form method="post" action="/Record/{$id}/SMS" name="popupForm"
      onSubmit="SendSMS('{$id}', this.elements['to'].value, this.elements['provider'][this.elements['provider'].selectedIndex].value); return false;">
  <table>
  {literal}
  <tr>
    <td>Number: </td>
    <td><input type="text" name="to" value="10-Digit Phone Number" onfocus="if(this.value=='10-Digit Phone Number'){this.value=''}" onblur = "if(this.value==''){this.value='10-Digit Phone Number'}"></td>
  </tr>
  {/literal}
  <tr>
    <td>Provider: </td>
    <td>
      <select name="provider">
        <option selected=true value="">Select your carrier</option>
        <option value="att">AT&amp;T</option>
        <option value="verizon">Verizon</option>
        <option value="tmobile">T Mobile</option>
        <option value="sprint">Sprint</option>
        <option value="nextel">Nextel</option>
        <option value="vmobile">Virgin Mobile</option>
        <option value="alltel">Alltel</option>
        <option value="cricket">Cricket</option>
      </select>
    </td>
  </tr>
  <tr>
    <td></td>
    <td><input type="submit" name="submit" value="Send"></td>
  </tr>
  </table>
</form>