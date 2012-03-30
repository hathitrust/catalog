<?php

require_once('services/Tags/TaggedItemsDisplay.php');

class SelectedItems extends TaggedItemsDisplay 
{
  function __construct() {
    global $interface;
    parent::__construct();
    $interface->assign("selectedItemsPage", true);
  }
}

?>