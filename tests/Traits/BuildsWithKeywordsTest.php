<?php declare(strict_types=1);

namespace Webapps\Tests\Traits\MakeWithKeywords;

use Webapps\Tests\TestCase;

use InvalidArgumentException;

use Webapps\Util\Traits\MakeWithKeywords;
use Webapps\Util\Traits\BuildsWithKeywords;

class BuildWithKeywordsTest extends TestCase
{

    /** @test */
    public function can_build_instances()
    {
        $obj = FactoryClass::make(['stringVar' => 'str', 'booleanVar' => true]);

        $target = $obj->build(
            TargetClass::class,
            ['intVar' => 42],
            ['booleanVar' => 'boolVar']
        );

        $this->assertEquals(42, $target->intVar);
        $this->assertEquals(true, $target->boolVar);
        $this->assertEquals('str', $target->stringVar);
    }

    /** @test */
    public function can_build_instances_with_uninitialized_properties()
    {
        $obj = FactoryClass2::make(['stringVar' => 'str', 'booleanVar' => true]);

        $target = $obj->build(
            TargetClass::class,
            ['intVar' => 42],
            ['booleanVar' => 'boolVar']
        );

        $this->assertEquals(42, $target->intVar);
        $this->assertEquals(true, $target->boolVar);
        $this->assertEquals('str', $target->stringVar);
    }
}

class FactoryClass
{
    use MakeWithKeywords, BuildsWithKeywords;

    public $stringVar;
    public $booleanVar;
    public $extraVar = 100;

    public function __construct(string $stringVar, bool $booleanVar) {
        $this->stringVar = $stringVar;
        $this->booleanVar = $booleanVar;
    }

}

class FactoryClass2 extends FactoryClass {
    public ?int $someOtherVar;
}

class TargetClass
{
    use MakeWithKeywords;

    public $stringVar;
    public $boolVar;
    public $intVar;

    public function __construct(string $stringVar, int $intVar, bool $boolVar) {
        $this->stringVar = $stringVar;
        $this->boolVar = $boolVar;
        $this->intVar = $intVar;
    }
}
