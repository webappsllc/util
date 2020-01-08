<?php declare(strict_types=1);

namespace Webapps\Tests;

use Webapps\Tests\TestCase;

use Illuminate\Support\Collection;

use Webapps\Util\BatchingBuffer;

class XmlToArrayTest extends TestCase {

    public function setUp() : void {
        $this->xmlString = "
        <Person type=\"Cool\">
            <Name>
                <First>Greg</First>
                <Last>Flanders</Last>
            </Name>
            <Lucky>
                <Duck>100</Duck>
                <Duck>200</Duck>
                <Duck>300</Duck>
            </Lucky>
        </Person>
        ";

        $this->xmlObject = simplexml_load_string($this->xmlString);
    }

    /** @test */
    public function can_turn_xml_string_into_array() {
        $result = xml_to_array($this->xmlString);
        $person = $result['Person'];
        $this->assertArraySubset(['Name' => ['First' => 'Greg', 'Last' => 'Flanders']], $person);
        $this->assertArraySubset(['Lucky' => ['Duck' => [100,200,300]]], $person);
    }

    /** @test */
    public function can_turn_xml_object_into_array() {
        $result = xml_to_array($this->xmlObject);
        $person = $result['Person'];
        $this->assertArraySubset(['Name' => ['First' => 'Greg', 'Last' => 'Flanders']], $person);
        $this->assertArraySubset(['Lucky' => ['Duck' => [100,200,300]]], $person);
    }
}
