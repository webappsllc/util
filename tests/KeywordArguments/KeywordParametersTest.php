<?php declare(strict_types=1);

namespace WebApps\Tests\KeywordArguments;

use WebApps\Tests\TestCase;

use ReflectionFunction;
use ReflectionMethod;
use WebApps\Util\KeywordArguments\KeywordParameters;

class KeywordParametersTest extends TestCase
{

    /** @test */
    public function keyword_parameters_works_with_closures() {
        $ref = new ReflectionFunction(function (?int $a, $b = 5) {
            return ($a ? $a : 20) * $b;
        });
        $params = KeywordParameters::forReflectionFunction($ref);

        $this->assertEquals($params->makeList(['a' => 1, 'b' => 2]), [1, 2]);
        $this->assertEquals($params->makeList(['b' => 2, 'a' => 1]), [1, 2]);
        $this->assertEquals($params->makeList(['a' => 1]), [1, 5]);
        $this->assertEquals($params->makeList(['b' => 2]), [null, 2]);
    }

    /** @test */
    public function keyword_parameters_works_with_strings() {
        $ref = new ReflectionMethod(static::class,'mockStringCall');
        $params = KeywordParameters::forReflectionFunction($ref);

        $this->assertEquals($params->makeList(['a' => 1, 'b' => 2]), [1, 2]);
        $this->assertEquals($params->makeList(['b' => 2, 'a' => 1]), [1, 2]);
        $this->assertEquals($params->makeList(['a' => 1]), [1, 5]);
        $this->assertEquals($params->makeList(['b' => 2]), [null, 2]);
    }

    /** @test */
    public function keyword_parameters_works_with_callable_objects() {
        $ref = new ReflectionMethod(new MockParamCallable,'__invoke');
        $params = KeywordParameters::forReflectionFunction($ref);

        $this->assertEquals($params->makeList(['a' => 1, 'b' => 2]), [1, 2]);
        $this->assertEquals($params->makeList(['b' => 2, 'a' => 1]), [1, 2]);
        $this->assertEquals($params->makeList(['a' => 1]), [1, 5]);
        $this->assertEquals($params->makeList(['b' => 2]), [null, 2]);
    }

    /** @test */
    public function keyword_callable_works_with_array_callables() {
        $ref = new ReflectionMethod($this, 'mockCall');
        $params = KeywordParameters::forReflectionFunction($ref);

        $this->assertEquals($params->makeList(['a' => 1, 'b' => 2]), [1, 2]);
        $this->assertEquals($params->makeList(['b' => 2, 'a' => 1]), [1, 2]);
        $this->assertEquals($params->makeList(['a' => 1]), [1, 5]);
        $this->assertEquals($params->makeList(['b' => 2]), [null, 2]);
    }

    public static function mockStringCall(?int $a, $b = 5) {
        return ($a ? $a : 20) * $b;
    }

    public function mockCall(?int $a, $b = 5) {
        return ($a ? $a : 20) * $b;
    }
}

class MockParamCallable {
    public function __invoke(?int $a, $b = 5) : int {
        return ($a ? $a : 20) * $b;
    }
}
