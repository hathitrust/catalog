<?php
  //$file = file_get_contents('http://beta.lib.umich.edu/mtagger/users/registered/'. $_SERVER['REMOTE_USER']);
  $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
  $name = $_SERVER['SERVER_NAME'];
  $path = dirname($_SERVER['SCRIPT_NAME']);
  $port = isset($_SERVER['SERVER_PORT'])? ':'.$_SERVER['SERVER_PORT'] : '';
  $tagurl = $protocol . $name . $port . $path . '/Tag.php?url='. rawurlencode($_GET['url']) . 
            '&action=' . rawurlencode($_GET['action']) .
            '&title=' . rawurlencode($_GET['title']) .
            '&tag=' . rawurlencode($_GET['tag']);
  //$tagurl = $protocol . $name . ':' . $port . $path . '/Tag.php';
  $next =  $tagurl;
  if($_GET['action'] == 'favorites') {
    $next = $protocol . $name . $port . $path . '/Favorites.php?url='. rawurlencode($_GET['url']) .
            '&action=' . rawurlencode($_GET['action']) .
            '&title=' . rawurlencode($_GET['title']) .
            '&tag=' . rawurlencode($_GET['tag']);
  }
  $welcome = 'https://www.lib.umich.edu/mtagger/tags/welcome?referer='. rawurlencode($next);
?>
<html>
<head><title>Checking Your MTagger Account</title>
<script type="text/javascript" src="http://www.lib.umich.edu/mtagger/js/jquery.js"></script>
</head>
<body>
<script type="text/javascript">
$(function () {$.getJSON(
  'https://www.lib.umich.edu/mtagger/users/registered/jsonp?callback=?',
  function(data) {
    if(data && 'status' in data && (parseInt(data.status) == 1 )) {
        location = '<?php print $next ?>';
    }  else {
        location = '<?php print $welcome ?>';
    }
  }
);});
</script>
</body>
</html>
