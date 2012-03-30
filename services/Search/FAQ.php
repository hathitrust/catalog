<?php
require_once 'Action.php';

class FAQ extends Action {
    
    function launch()
    {
        global $interface;
        $interface->setPageTitle('Frequently Asked Questions');
        $interface->setTemplate('faq.tpl');
        $interface->display('layout.tpl');
    }
}

?>