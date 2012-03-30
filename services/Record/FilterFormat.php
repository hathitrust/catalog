<?php
class FilterFormat {
  public $FormatTable;
  public function __construct() {
    $this->FormatTable = array( array("Journal","Newspaper","Serial"),
                            array("Book"),
                            array("Available online", "Electronic Resource"),
                            //array("Available online", "HathiTrust", "Electronic Resource"),
                            array("Musical Score", "Audio (spoken word)"),
                            array("Audio CD", "Audio LP", "Audio"),
                            array("Video (Blu-ray)","Video (VHS)","Video (DVD)","Motion Picture", "Visual Material"),
                            array("Map"),
                            array("Archive","Manuscript","Mixed Material"),
                            array("Microform"),
                            array("Video Games"),
                          );
  }

    function filter($in)
    {
      $newFormats = array();
      if (!is_array($in)) $formats[] = $in;
      else $formats = $in;

      $selectList = array_pad(array(), count($this->FormatTable), 9);
      foreach ($formats as $fmt) {
        foreach ($this->FormatTable as $tableRow => $tableList) {
          foreach ($tableList as $listNum => $listFormat) {
            if ($fmt == $listFormat) {
              if ($listNum < $selectList[$tableRow]) {
                $selectList[$tableRow] = $listNum;
              }
            }
          }
        }
      }
      foreach ($selectList as $row => $listNum) {
        if ($listNum < 9) $newFormats[] = $this->FormatTable[$row][$listNum];
      }
      return($newFormats);

    }
}
?>
