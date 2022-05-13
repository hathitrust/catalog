<?php
require_once 'Action.php';

class Error extends Action {
    
    function launch()
    {
        global $interface;
        $interface->setPageTitle('Search Syntax Error');
        $interface->assign('error', $_REQUEST['error']);
        $interface->setTemplate('Search/error.tpl');
        $interface->display('layout.tpl');
    }
}

?>
