<?php

class Error404 {

  function launch() {

    $interface = new UInterface();
    header('HTTP/1.1 404 Not Found');
    $interface->assign('module', 'Error');
    $interface->setTemplate('Error404.tpl');
    $interface->display('layout.tpl');

    exit();
  }
}

?>
