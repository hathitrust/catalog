<?php
/**
 * Table Definition for search
 */
require_once 'DB/DataObject.php';

class Search extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'search';                          // table name
    public $id;                              // int(11)  not_null primary_key auto_increment
    public $user_id;                         // int(11)  not_null multiple_key
    public $folder_id;                       // int(11)  multiple_key
    public $created;                         // date(10)  not_null binary
    public $title;                           // string(20)  
    public $lookfor;                         // string(200)  not_null
    public $type;                            // string(20)  not_null
    public $limitto;                         // string(100)  

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('Search',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
