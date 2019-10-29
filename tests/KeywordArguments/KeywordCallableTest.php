<?php declare(strict_types=1);

namespace WebApps\Tests\KeywordArguments;

use WebApps\Tests\TestCase;

use WebApps\Util\KeywordArguments\KeywordCallable;

class KeywordCallableTest extends TestCase
{

    /** @test */
    public function keyword_callable_works_with_closures() {
        $func = new KeywordCallable(function (?int $a, $b = 5) {
            return ($a ? $a : 20) * $b;
        });

        $this->assertEquals($func(['a' => 10, 'b' => 10]), 100);
        $this->assertEquals($func(['a' => 10]), 50);
        $this->assertEquals($func(['b' => 10]), 200);
    }

    /** @test */
    public function keyword_callable_works_with_strings() {
        $func = new KeywordCallable(static::class . '::mockStringCall');

        $this->assertEquals($func(['a' => 10, 'b' => 10]), 100);
        $this->assertEquals($func(['a' => 10]), 50);
        $this->assertEquals($func(['b' => 10]), 200);
    }

    /** @test */
    public function keyword_callable_works_with_callable_objects() {
        $func = new KeywordCallable(new MockKeywordCallable);

        $this->assertEquals($func(['a' => 10, 'b' => 10]), 100);
        $this->assertEquals($func(['a' => 10]), 50);
        $this->assertEquals($func(['b' => 10]), 200);
    }

    /** @test */
    public function keyword_callable_works_with_array_callables() {
        $func = new KeywordCallable([$this, 'mockCall']);

        $this->assertEquals($func(['a' => 10, 'b' => 10]), 100);
        $this->assertEquals($func(['a' => 10]), 50);
        $this->assertEquals($func(['b' => 10]), 200);
    }

    public static function mockStringCall(?int $a, $b = 5) {
        return ($a ? $a : 20) * $b;
    }

    public function mockCall(?int $a, $b = 5) {
        return ($a ? $a : 20) * $b;
    }
}

class MockKeywordCallable {
    public function __invoke(?int $a, $b = 5) : int {
        return ($a ? $a : 20) * $b;
    }
}
