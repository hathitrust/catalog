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
var numtags     = 0;
var hasUntagged = false; // true if a user clicked '[untag]' for one of their tags

function updateAddTags() {
  var tags = trim($('#mtags').val());
  numtags = (tags == null || tags == "") ? 0 : tags.split(',').length;
  if(numtags) {
    $('#adding_tags').html('Adding '+numtags+' tags');
  } else {
    $('#adding_tags').html('');
  }
}

/* Don't have other window.onload's on this page, so shouldn't be overwriting any */
window.onload = function() {
  setInterval("updateAddTags()",500);
}


function checkData() {
    form = document.getElementById('submitForm');
    var title   = trim(form.SnippetTitle.value);
    var tags    = trim(form.mtags.value);
    var snippet = trim(form.SnippetSnippet.value);   
    var noData  = (snippet == null || snippet == "") &&  (numtags < 1) && (title == null || title == "");

    if(noData && !hasUntagged) {
        alert("To save this item please enter a title, a description, tags, or untag one or more tags.");
        return false;
    } else {
        disableAddButton(form.id);
    }
    url = 'https://www.lib.umich.edu/mtagger/items/add_api?callback=?&' +
                 'ItemCollection='+ encodeURIComponent($('#ItemCollection').attr('value')) + '&' +
                 'ItemTitle='+ encodeURIComponent($('#ItemTitle').attr('value')) + '&' +
                 'ItemUrl='+ encodeURIComponent($('#ItemUrl').attr('value')) + '&' +
                 'mtags='+ encodeURIComponent($('#mtags').attr('value') ? $('#mtags').attr('value') : '') + '&' +
                 'SnippetSnippet='+ encodeURIComponent($('#SnippetSnippet').attr('value')) + '&' +
                 'SnippetTitle='+ encodeURIComponent($('#SnippetTitle').attr('value'));
    $.getJSON( url,
               function(data) {
                 window.opener.jQuery.mtagger.refresh();
                 self.close();
               }
    
    );
}

$(function() {

    
    var normalClass = { color : "darkblue", textDecoration : "none", backgroundColor : ""};
    var selectedClass = { textDecoration : "underline", color : "#fff", backgroundColor : "#00f"};
    var normalHoverClass = { textDecoration : "none", color : "#fff", backgroundColor : "#14356A"};
    $("#mtags").jTagging($("#tag_links"), ",", normalClass, selectedClass, normalHoverClass);

});
</script>
</head>
<body>

<table border="0" cellspacing="0" cellpadding="0" align="center" style="width:90%">
  <tr>
    <td style="text-align:center;">
      <div id="header" class="header_bar"><img src="http://www.lib.umich.edu/mtagger/img/mtag_logo_mini.gif" title="University of Michigan Library's MTagger" border="0" /></div>
    </td>
  </tr>
  <tr style="background:#fff url('http://www.lib.umich.edu/mtagger/img/tagging_item_bg.jpg') no-repeat fixed bottom right;height:100%;">
    <td>
      <div id="content" style="padding:0px 1em 0px 0px;">
        <div id="tagging_container">
          <form id="submitForm" name="submitForm" action="https://www.lib.umich.edu/mtagger/items/add/?URL=<?php print htmlentities(rawurlencode($_GET['url'])); ?>&title=<?php print htmlentities(rawurlencode($_GET['title'])); ?>" method="post" onsubmit="return checkData(this);">
          <!-- <input type="hidden" name="data[Item][collection]" class="readonly" readonly="readonly" value="1" id="ItemCollection"/> -->
          <table width = "100%" border = "0">
            <tr>
              <td align = "left" valign = "top" width = "50" class = "tagging_container_1">
                <div class = "required">
                  <label for="ItemTitle">Official Title</label>
                </div>
              </td>
              <td align = "left" valign = "top" class = "tagging_container_1">
                <div class="required">
                    <input type="text" id="ItemTitle" name="data[Item][title]" value="<?php print htmlentities($_GET['title']); ?>" readonly="readonly" title="The official title for this item" class="readonly" />
                </div>
              </td>
            </tr>
            <tr>
              <td align = "left" valign = "top" width = "50" class = "tagging_container_1">
                <div class="required">
                  <label for="TagName">Tag(s)</label> &nbsp; &nbsp;
                </div>
              </td>
              <td align = "left" valign = "top" class = "tagging_container_1">
                <div class="required">
                  <input name="data[Tag][name]"  id="mtags" title="Use commas between tags" value="<?php print htmlentities($_GET['tag']) ?>" type="text" /><br/>
                  <small style="font-style:italic;float:left;">Use commas between tags.  Max 50 characters per tag. <a href="http://www.lib.umich.edu/mtagger/tags/help"  target="_blank">[Help me]</a></small>
                  <small id="adding_tags" style="float:right;"></small><br/>
                  <?php /* echo $html->tagErrorMsg('Tag/name', 'Please enter tag(s).'); */ ?>
  </p>
                  <div style="clear:both; padding-top: 7px;">
                    <label for="MyTags">You Previously Tagged This:</label>
                    <p id='my_tags' style="margin-top:0px;">
                      <?php if(isset($my_tags) && $my_tags): ?>

                      <?php foreach($my_tags as $tag): ?>
                        <span style="color:darkblue;margin-right:8px;"><?php echo $tag['name']; ?> <?php echo $html->link('[untag]', '/tags/untag/'.$tag['id'].'/'.$html->tagValue('Item/id').'/a=true', array('class' => 'ajaxLink', 'title' => 'Click here to delete this tag from the item'));?></span>
                      <?php endforeach; ?>

                      <?php else: ?>
                        <span class="noSaved">You haven't tagged this item</span>
                      <?php endif; ?>
                    </p>
                    <label for="TheirTags">Others Tagged This:</label> &nbsp; 
                    <p id="tag_links" style="margin-top:0px;">
                      <span class="noSaved">No other tags used</span>
                    </p>
                    <script type="text/javascript">
$( function () {

$.getJSON('https://www.lib.umich.edu/mtagger/items/get_my_tags_api/?callback=?&URL=<?php print rawurlencode($_GET['url']); ?>',
  function (json) {
    if(json && json.status == 0 && json.data && json.data.length > 0) {
      $('#my_tags').empty();
      for(i=0; i<json.data.length; i++) {
        $('#my_tags').append('<span style="color:darkblue;margin-right:8px;">'+json.data[i].Tag.name+'<a href="https://www.lib.umich.edu/mtagger/tags/untag/'+json.data[i].Tag.id+'/'+json.item+'/a=true" class="ajaxLink" title="Click here to delete this tag from the item">[untag]</a></span> ');
      }
      $(".ajaxLink").click( function() {
        var anchor = $(this);          // a tag jquery object that was click
        var span = $(this).parent();  // span jquery objec holding this tag
        
        if( confirm('Are you sure you want to untag this?') ) {
          anchor.parent().html('<span class="noSaved">Deleting...</span>');
          $("a.ajaxLink").parent().append('<span class="disabled">[untag]</span>');
          $("a.ajaxLink").hide();
          $.getJSON(this.href +'&callback=?',
            function (json) {
              span.remove();
              $("a.ajaxLink").show();
              $("span.disabled").remove();
              hasUntagged=true;
            }
          );
        }
        return false;
    });
    }
  });

$.getJSON('https://www.lib.umich.edu/mtagger/items/get_others_tags_api/?callback=?&URL=<?php print rawurlencode($_GET['url']); ?>', 
  function (json) {
    if(json && json.status == 0 && json.data.length > 0) {
      
      $('#tag_links').before('<small style="font-style: italic;">(Click a tag to add or remove it from your list)</small>')
        .empty();
      for(i=0;i<json.data.length;i++) {
        $('#tag_links').append('<span style="margin-right:8px;"><a href="#" title="Swap tag in or out" onclick="return false;">'+json.data[i]+'</a></span>');

      } 
      var normalClass = { color : "darkblue", textDecoration : "none", backgroundColor : ""};
      var selectedClass = { textDecoration : "underline", color : "#fff", backgroundColor : "#00f"};
      var normalHoverClass = { textDecoration : "none", color : "#fff", backgroundColor : "#14356A"};
      $("#mtags").jTagging($("#tag_links"), ",", normalClass, selectedClass, normalHoverClass);
    }
  });
});

                    </script>
                  </div>
                </div>
              </td>
            </tr>
            <tr>
              <td align = "left" valign = "top" width = "50" class = "tagging_container_1"><div class="optional"> 
                <label for="SnippetTitle">Alt Title</label><br/>
                <small style="font-style:italic;float:left;">Optional</small>
              </div>
              </td>
              <td align="left" valign = "top" class = "tagging_container_1"><div class="optional">
                  <input name="data[Snippet][title]"  title="Enter a meaningful title that makes sense to you" value="" type="text" id="SnippetTitle" />
                </div>
              </td>
            </tr>

            <tr>
              <td align = "left" valign = "top" width = "50" class = "tagging_container_1">
                <div class="optional">
                  <label for="SnippetSnippet">Description</label><br/>
                  <small style="font-style:italic;float:left;">Optional</small>
                </div> 
              </td>
              <td align="left" valign = "top" class = "tagging_container_1">
                <div class="optional">
                  <textarea name="data[Snippet][snippet]"  cols="50" rows="3" title="Enter a personal description to remember what this item is" id="SnippetSnippet"></textarea>
                  <input name="data[Item][url]"  type="hidden" value="<?php print htmlentities($_GET['url']); ?>" readonly="readonly" class="readonly" id="ItemUrl" />
                </div>
              </td>
            </tr>
            <tr>
              <td align = "left" valign = "top" width = "50" class = "tagging_container_1">
                <div class = "optional">
                  <label for="ItemCollection">Collection</label>
                </div>
              </td>
              <td align = "left" valign = "top" class = "tagging_container_1">
                <div class="optional">
                  MLibrary	<input name="data[Item][collection]"  value="0" readonly="readonly" class="readonly" type="hidden" id="ItemCollection" />
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
