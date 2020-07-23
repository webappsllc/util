<?php declare(strict_types=1);

namespace Webapps\Tests;

use ErrorException;

use Illuminate\Support\Collection;

use Webapps\Tests\TestCase;
use Webapps\Util\ValueObject;

class ValueObjectTest extends TestCase
{
    public function setUp() : void
    {
        parent::setUp();
        $this->value = new MockValueObject('foo_value','bar_value');
        $this->expectedJson = '{"name":"Testing Event Name","sample":{"data":"goes here"},"list":[1]}';
    }

    /** @test */
    public function value_objects_can_be_converted_to_closure()
    {
        $func = $this->value->toClosure();
        $this->assertEquals($func('fooProp'), 'foo_value');
        $this->assertEquals($func('barProp'), 'bar_value');
        $this->assertNull($func('bazProp'));
    }

    /** @test */
    public function value_objects_can_be_converted_to_array()
    {
        $arr = $this->value->toArray();
        $this->assertEquals($arr, ['fooProp' => 'foo_value', 'barProp' => 'bar_value']);
    }

    /** @test */
    public function value_objects_can_be_converted_to_collection()
    {
        $collection = $this->value->toCollection();
        $this->assertEquals($collection, new Collection(['fooProp' => 'foo_value', 'barProp' => 'bar_value']));
    }

    /** @test */
    public function value_objects_can_be_converted_to_iterator()
    {
        $iter = $this->value->getIterator();
        $this->assertEquals(iterator_to_array($iter, true), ['fooProp' => 'foo_value', 'barProp' => 'bar_value']);
    }

    /** @test */
    public function value_objects_implement_arrayable()
    {
        $this->assertEquals($this->value->toArray(), ['fooProp' => 'foo_value', 'barProp' => 'bar_value']);
    }

    /** @test */
    public function value_objects_have_array_access()
    {
        $this->assertEquals($this->value['fooProp'], 'foo_value');
        $this->assertEquals($this->value['barProp'], 'bar_value');
    }

    /** @test */
    public function value_object_needs_no_props() {
      $val = new NoProps;
      $this->assertTrue(true);
    }

    /** @test */
    public function only_returns_assoc_array_of_values() {
        $val = new MockValueObject('foo', 'bar');
        $expected = ['fooProp' => 'foo'];

        $this->assertEquals($expected, $val->only('fooProp'));
    }

    /** @test */
    public function except_returns_assoc_array_of_values() {
        $val = new MockValueObject('foo', 'bar');
        $expected = ['fooProp' => 'foo'];

        $this->assertEquals($expected, $val->except('barProp'));
    }

    /** @test */
    public function only_all_values_as_assoc_array() {
        $val = new MockValueObject('foo', 'bar');
        $expected = [
            'fooProp' => 'foo',
            'barProp' => 'bar',
        ];

        $this->assertEquals($expected, $val->all());
    }

    /** @test */
    public function merge_builds_assoc_array_with_overrides() {
        $val = new MockValueObject('foo', 'bar');
        $expected = [
            'fooProp' => 'foo',
            'barProp' => 'bar',
            'fluff' => 'puff'
        ];

        $this->assertEqualsCanonicalizing($expected, $val->merge(['fluff' => 'puff']));
    }

    /** @test */
    public function all_make_array_with_properties_as_keys() {
        $event = new TestEvent;

        $all = $event->all();
        $this->assertEquals($event->name, $all['name']);
        $this->assertEquals($event->sample, $all['sample']);
        $this->assertInstanceOf(Collection::class, $all['list']);
        $this->assertArrayNotHasKey('notAvailable', $all);
    }

    /** @test */
    public function toArray_recursively_transforms_aggregates_to_arrays() {
        $event = new TestEvent;

        $toArray = $event->toArray();
        $this->assertEquals($event->name, $toArray['name']);
        $this->assertEquals($event->sample, $toArray['sample']);
        $this->assertIsArray($toArray['list']);
        $this->assertArrayNotHasKey('notAvailable', $toArray);
    }

    /** @test */
    public function toJson_recursively_transforms_aggregates_to_json_string() {
        $event = new TestEvent;

        $json = $event->toJson();
        $this->assertEquals($this->expectedJson, $json);
    }

    /** @test */
    public function jsaonSerialize_recursively_transforms_aggregates_to_json_string() {
        $event = new TestEvent;

        $json = $event->jsonSerialize();
        $this->assertEquals(json_decode($this->expectedJson, true), $json);
    }

    /** @test */
    public function __string_recursively_transforms_aggregates_to_json_string() {
        $event = new TestEvent;

        $this->assertEquals($this->expectedJson, (string)$event);
    }
}

class MockValueObject extends ValueObject
{
    public $fooProp;
    public $barProp;
    protected $bazProp = 'baz';

    public function __construct(string $fooProp, string $barProp)
    {
        $this->fooProp = $fooProp;
        $this->barProp = $barProp;
    }
}

class NoProps extends ValueObject {}


class TestEvent extends ValueObject {

    protected static string $eventTopic = 'dev-test-topic';

    public string $name = "Testing Event Name";

    public array $sample = [
        'data' => 'goes here'
    ];

    public Collection $list;

    private string $notAvailable = 'Not Available';

    public function __construct() {
        $this->list = new Collection([1]);
    }

}
