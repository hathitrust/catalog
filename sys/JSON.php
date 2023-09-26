<?php

# Based on https://github.com/pear/File_MARC/blob/master/File/MARC/Record.php#L508
# with fix for double encoding.
# No attempt to use JSON_FORCE_OBJECT for numeric subfields since it incorrectly
# hashifies parts of the strucvture that should be arrays.
class JSON
{
  function encode_marc($marc) {
    $json = new StdClass();
    $json->leader = $marc->getLeader();

    /* Start fields */
    $fields = array();
    foreach ($marc->getFields() as $field) {
      if (!$field->isEmpty()) {
        switch(get_class($field)) {
        case "File_MARC_Control_Field":
          $fields[] = array($field->getTag() => $field->getData());
          break;

        case "File_MARC_Data_Field":
          $subs = array();
          foreach ($field->getSubfields() as $sf) {
              $subs[] = array(strval($sf->getCode()) => $sf->getData());
          }
          $contents = new StdClass();
          $contents->ind1 = $field->getIndicator(1);
          $contents->ind2 = $field->getIndicator(2);
          $contents->subfields = $subs;
          $fields[] = array($field->getTag() => $contents);
          break;
        }
      }
    }
    $json->fields = $fields;
    return json_encode($json);
  }
}

?>