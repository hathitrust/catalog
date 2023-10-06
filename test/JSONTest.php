<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
require_once 'File/MARCXML.php';
require_once 'sys/JSON.php';

const MARC_XML_SKELETON = <<<END_XML
<?xml version="1.0" encoding="UTF-8"?>
  <collection xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xmlns="http://www.loc.gov/MARC21/slim"
    xsi:schemaLocation="http://www.loc.gov/MARC21/slim http://www.loc.gov/standards/marcxml/schema/MARC21slim.xsd">
    <record>
      <leader>02396cam a2200505 i 4500</leader>
      <controlfield tag="008">830910m19259999ru       b    000 0 rus  </controlfield>
    </record>
  </collection>
END_XML;

class JSONTest extends TestCase
{
  /**
  * @covers JSON::encode_marc
  */
  public function test_encode_marc_unicode(): void {
    $marc = new File_MARCXML(MARC_XML_SKELETON, File_MARC::SOURCE_STRING);
    $marc_record = $marc->next();
    $subfields[] = new File_MARC_Subfield('6', '6730-09/');
    $subfields[] = new File_MARC_Subfield('a', '考古');
    $new_field = new File_MARC_Data_Field('880', $subfields, 0, null);
    $marc_record->appendField($new_field);
    $json = new JSON();
    $encoded = $json->encode_marc($marc_record);
    $expected = "{\"a\":\"\\u8003\\u53e4\"}";
    $this->assertStringContainsString($expected, $encoded);
  }
}

?>
