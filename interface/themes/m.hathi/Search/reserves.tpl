<div id="bd">
  <div id="yui-main" class="content">
    <div class="yui-b first contentbox">
    	<div class="record">

<form method="GET" action="{$url}/Search/Reserves" name="searchForm" class="search">
  <h2>Search For</h2>
  <table class="citation">
    <tr>
      <th align="right">Course: </th>
      <td>
        <select name="course">
          <option></option>
          {foreach from=$courseList item=courseName key=courseId}
            <option value="{$courseId}">{$courseName}</option>
          {/foreach}
        </select>
      </td>
    </tr>
    <tr>
      <th align="right">Instructor: </th>
      <td>
        <select name="inst">
          <option></option>
          {foreach from=$instList item=instName key=instId}
            <option value="{$instId}">{$instName}</option>
          {/foreach}
        </select>
      </td>
    </tr>
    <tr>
      <th align="right">Department: </th>
      <td>
        <select name="dept">
          <option></option>
          {foreach from=$deptList item=deptName key=deptId}
            <option value="{$deptId}">{$deptName}</option>
          {/foreach}
        </select>
      </td>
    </tr>
  </table>
  <input type="submit" name="submit" value="Find"><br>
</form>

</div></div></div></div>