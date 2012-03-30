<?php


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>MTagger: <?php print htmlentities($_GET['title']); ?></title>
<script type="text/javascript" src="http://www.lib.umich.edu/mtagger/js/jquery.js"></script>
<script type="text/javascript" src="http://www.lib.umich.edu/mtagger/js/jquery.mtagger.js"></script>
<script type="text/javascript" src="http://www.lib.umich.edu/mtagger/js/mtagger.js"></script>
<script type="text/javascript" src="http://www.lib.umich.edu/mtagger/js/jquery.jTagging.min.js"></script>
<link rel="stylesheet" type="text/css" href="http://www.lib.umich.edu/mtagger/css/tagger.css"></link>
<script type="text/javascript">
function checkData() {
    form = document.getElementById('submitForm');

    if(false) {
        alert("To save this item please enter a title, a description, tags, or untag one or more tags.");
        return false;
    } else {
        disableAddButton(form.id);
    }
    url = 'http://www.lib.umich.edu/mtagger/items/add_api?callback=?&' +
                 'ItemTitle='+ encodeURIComponent($('#ItemTitle').attr('value')) + '&' +
                 'ItemUrl='+ encodeURIComponent($('#ItemUrl').attr('value')) + '&' +
                 'mtags='+ encodeURIComponent($('#mtags').attr('value'));
    $.getJSON( url,
               function(data) {
                 if(data && data.status == 0) {
                   self.close();
                 } else {
                   self.close();
                   //alert('fail');
                 }
               }
    
    );
}

</script>
</head>
<body>

<table border="0" cellspacing="0" cellpadding="0" align="center" style="width:90%">
  <tr>
    <td style="text-align:center;">
      <div id="header" class="header_bar"><img src="http://www.lib.umich.edu/mtagger/img/mtag_logo_mini.gif" title="University of Michigan Library's MTagger" border="0" /><br/> </div>
    </td>
  </tr>
  <tr style="background:#fff url('http://www.lib.umich.edu/mtagger/img/tagging_item_bg.jpg') no-repeat fixed bottom right;height:100%;">
    <td>
      <div id="content" style="padding:0px 1em 0px 0px;">
        <div id="tagging_container">
          <form id="submitForm" name="submitForm" action="http://www.lib.umich.edu/mtagger/items/add/?URL=<?php print htmlentities(rawurlencode($_GET['url'])); ?>&title=<?php print htmlentities(rawurlencode($_GET['title'])); ?>" method="post" onsubmit="return checkData(this);">
          <!-- <input type="hidden" name="data[Item][collection]" class="readonly" readonly="readonly" value="1" id="ItemCollection"/> -->
          <table width = "100%" border = "0">
            <tr>
              <td align = "left" valign = "top" colspan = "2" class = "tagging_container_1">
                <div class="required" style="text-align: center">
                   Add to Favorites:
                </div>
              </td>
            </tr>
            <tr>
            <tr>
              <td align = "left" valign = "top" width = "50" class = "tagging_container_1">
                <div class = "required">
                  <label for="ItemTitle">Official Title</label>
                </div>
              </td>
              <td align = "left" valign = "top" class = "tagging_container_1">
                <div class="required">
                    <input type="text" id="ItemTitle" name="data[Item][title]" value="<?php print htmlentities($_GET['title']); ?>" readonly="readonly" title="The official title for this item" class="readonly" />
                  <input name="data[Tag][name]"  id="mtags" title="Use commas between tags" value="<?php print htmlentities($_GET['tag']) ?>" type="hidden" /><br/>
                  <input name="data[Item][url]"  type="hidden" value="<?php print htmlentities($_GET['url']); ?>" readonly="readonly" class="readonly" id="ItemUrl" />
                </div>
              </td>
            </tr>
            <tr>
              <td align = "left" valign = "top" width = "50" class = "tagging_container_1">
                <div class="required">
                </div>
              </td>
              <td align="left" valign = "top" class = "tagging_container_1">
                <div class="required">
                  <div class="submit">
                    <input type="button" style="width:50px;float:left;" name="saveButton" value="Done" onclick="javascript:checkData();" />
                    <a href="javascript:closeWindow();"  style="float:right;" title="Close this window">[close x]</a>
                    &nbsp;&nbsp; <img src="http://www.lib.umich.edu/mtagger/img/working.gif" id="workingImage" style="display:none;" />
                  </div>
                </div>
              </td>
            </tr>
            <tr>
              <td align = "left" valign = "top" width = "50" class = "tagging_container_1"><div class="optional" style = "padding-top: 2em;">Help</div></td>
              <td align="left" valign = "top" class = "tagging_container_1">
                <div class="optional" style = "padding-top: 2em;">
                  <a href = "http://www.lib.umich.edu/mtagger/tags/faq" target = "main">About MTagger</a>
                </div>
              </td>
            </tr>
          </table>
          </form>
        </div>
      </div>
    </td>
  </tr>
  <tr style="height:48px;"><td></td></tr>
</table>

<script type="text/javascript">
window.focus();
</script>
</body>

</html>
