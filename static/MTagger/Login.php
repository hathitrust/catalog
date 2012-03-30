<?php
  $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
  $filename =  '/Agree.php?url='; //isset($_SERVER['HTTPS']) ? 'Agrees.php?url=' : 'Agree.php?url=';
  $name = $_SERVER['SERVER_NAME'];
  $path = dirname($_SERVER['SCRIPT_NAME']);
  //$port = $_SERVER['SERVER_PORT'];
  $agree = $protocol . $name . $path . $filename . rawurlencode($_GET['url']) .
           '&action='. rawurlencode(isset($_GET['action'])?$_GET['action']:'') .
           '&title='. rawurlencode(isset($_GET['title'])?$_GET['title']:'') .
           '&tag='. rawurlencode(isset($_GET['tag'])?$_GET['tag']:'');

  //header('Location: https://beta.lib.umich.edu/Login.php?'.$agree);
  //if(!isset($_SERVER['REMOTE_USER'])) {
  //  header('Location: https://login.umdl.umich.edu/cgi/cosign/proxy?'. rawurlencode($agree));
  //} else {
  //echo $_SERVER['HTTPS'] ;
  header('Location: https://www.lib.umich.edu/Login?'. $agree);
  //}
?>
