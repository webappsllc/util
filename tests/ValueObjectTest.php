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
    public function value_objects_can_be_converted_to_data()
    {
        $this->assertEquals($this->value->toData(), ['foo_prop' => 'foo_value', 'bar_prop' => 'bar_value']);
    }

    /** @test */
    public function value_object_needs_no_props() {
      $val = new NoProps;
      $this->assertTrue(true);
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
