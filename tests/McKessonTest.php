<?php

namespace Nickcheek\McKesson\Tests;

use Nickcheek\McKesson\McKesson;
use PHPUnit\Framework\TestCase;

class McKessonTests extends TestCase
{
    
    /** @test */
    public function can_call_mckesson_class()
    {
        $test = new McKesson('identity','secret',1234,'b2bkey');
        $this->assertIsObject($test);

    }

    /** @test */
    public function test_xml_converted_to_object()
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?><ItemLookupResponse xmlns="http://mms.mckesson.com/services/xml" itemType="detail availability"><ResponseStatus code="200"/><ItemOut valid="true"><ItemId>978841</ItemId><ItemAvailability><ItemStatus code="SH" available="true"/><ItemStock code="P"><Description>Stocked</Description></ItemStock><Contract>Individual</Contract><ItemAlternative available="true" code="S"><Message>Product Substitution</Message></ItemAlternative><AtomicUom units="UN"/><ItemUom units="BG" per="18" atomic="18.0"><Price currency="USD">9.11</Price><QuantityAvailable>956.0</QuantityAvailable></ItemUom><ItemUom units="CS" per="72" atomic="72.0"><Price currency="USD">36.44</Price><QuantityAvailable>239.0</QuantityAvailable></ItemUom></ItemAvailability><ItemDetail><Description>UNDERWEAR, TENA PROTECTIVE PLUS LG (18/BG 4 BG/CS)    SCAPER</Description><SupplierId>978841</SupplierId><ManufacturerId>72338</ManufacturerId><ManufacturerName>ESSITY HMS NORTH</ManufacturerName><Classification domain="UNSPSC">53102306</Classification><Category id="C076" type="Major">Disposable Underwear</Category><Category id="S02076" type="Minor">Underwear, Disposable Incontinence Brief</Category></ItemDetail></ItemOut></ItemLookupResponse>';
        
        $test = new McKesson('identity','secret',1234,'b2bkey');
        $this->assertIsObject($test->toObject($xml)); 
    }

    public function test_setup_item_returns_xml_string()
    {
        $test = new McKesson('identity','secret',1234,'b2bkey');
        $xml = '<?xml version="1.0"?><ItemLookupRequest><Credentials><Identity>identity</Identity><SharedSecret>secret</SharedSecret><Account>1234</Account><ShipTo>1234</ShipTo></Credentials><ItemId>123456</ItemId></ItemLookupRequest>';
        $this->assertXmlStringEqualsXmlString($xml,$test->setupItemXML('123456'));
    }
}
