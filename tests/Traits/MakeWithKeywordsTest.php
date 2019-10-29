<?php declare(strict_types=1);

namespace WebApps\Tests\Traits;

use WebApps\Tests\TestCase;

use InvalidArgumentException;

use WebApps\Util\Traits\MakeWithKeywords;

class MakeWithKeywordsTest extends TestCase
{

    /** @test */
    public function can_make_instances()
    {
        $obj = HostClass::make(['stringVar' => 'stringVar']);
        $this->assertEquals($obj->stringVar, 'stringVar');
        $this->assertEquals($obj->nullableVar, null);
        $this->assertEquals($obj->defaultVar, 'default');
        $this->assertEquals($obj->noTypeVar, null);
    }

    /** @test */
    public function throws_when_parameter_not_supplied()
    {
        $this->expectException(InvalidArgumentException::class);
        $obj = HostClass::make([]);
    }

    /** @test */
    public function can_make_instances_with_data()
    {
        $obj = HostClass::makeSafe(['string_var' => 'stringVar', 'no_type_var' => 420]);
        $this->assertEquals($obj->stringVar, 'stringVar');
        $this->assertEquals($obj->nullableVar, null);
        $this->assertEquals($obj->defaultVar, 'default');
        $this->assertEquals($obj->noTypeVar, 420);
    }

    /** @test */
    public function can_make_instances_with_keyword_splat()
    {
        $obj = HostClassSplat::makeSafe(['string_var' => 'stringVar', 'no_type_var' => 420, 'more_stuff' => 100, 'extra_option' => 100]);
        $this->assertEquals($obj->stringVar, 'stringVar');
        $this->assertEquals($obj->nullableVar, null);
        $this->assertEquals($obj->defaultVar, 'default');
        $this->assertEquals($obj->noTypeVar, 420);

        $this->assertArraySubset(['moreStuff' => 100, 'extraOption' => 100], $obj->kwSplat);
    }

}

class HostClass
{
    use MakeWithKeywords;

    public $stringVar;
    public $nullableVar;
    public $defaultVar;
    public $noTypeVar;

    public function __construct(string $stringVar, ?string $nullableVar = null, string $defaultVar = 'default', $noTypeVar)
    {
        $this->stringVar = $stringVar;
        $this->nullableVar = $nullableVar;
        $this->defaultVar = $defaultVar;
        $this->noTypeVar = $noTypeVar;
    }

}

class HostClassSplat
{
    use MakeWithKeywords;

    public $stringVar;
    public $nullableVar;
    public $defaultVar;
    public $noTypeVar;
    public $kwSplat;

    public function __construct(string $stringVar, ?string $nullableVar = null, array $kwSplat, string $defaultVar = 'default', $noTypeVar)
    {
        $this->stringVar = $stringVar;
        $this->nullableVar = $nullableVar;
        $this->defaultVar = $defaultVar;
        $this->noTypeVar = $noTypeVar;
        $this->kwSplat = $kwSplat;
    }

}
