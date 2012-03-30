<?php
require_once 'Action.php';

require_once 'services/MyResearch/lib/User.php';
require_once 'services/MyResearch/lib/Resource.php';
require_once 'services/MyResearch/lib/Resource_tags.php';
require_once 'services/MyResearch/lib/Tags.php';

class SavePopup extends Action
{
    function launch()
    {
        global $interface;

        $interface->assign('recordId', $_GET['lookfor']);

        // Retrieve MyResearch information about user
        if(UserAccount::isLoggedIn()) {
            $user = unserialize($_COOKIE['userinfo']);
            $myTagList = $user->getTags($_GET['id']);
            if (count($myTagList)) {
                $newTagList = array();
                foreach($myTagList as $myTag) {
                    $tagStr .= "$myTag->tag ";
                }
                $interface->assign('myTagList', $tagStr);
            }
            $saved = $user->getSavedData($_GET['id']);
            $interface->assign('savedData', $saved);
        }

        $interface->setTemplate('save.tpl');
    }
}
    
?>
