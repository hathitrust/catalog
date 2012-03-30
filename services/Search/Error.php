<?php
require_once 'Action.php';

class Error extends Action {
    
    function launch()
    {
        global $interface;
        $interface->setPageTitle('Search Syntax Error');
        $interface->assign('error', $_REQUEST['error']);
        $interface->setTemplate('searchError.tpl');
        $interface->display('layout.tpl');
    }
}

?>