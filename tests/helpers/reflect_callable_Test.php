<?php declare(strict_types=1);

namespace Webapps\Tests;

use Webapps\Tests\TestCase;

class ReflectCallableTest extends TestCase {

    /** @test */
    public function can_reflect_closure() {
        $meth = reflect_callable(function(string $a, string $b) { return $a . '-' . $b; });
        $params = $meth->getParameters();

        $this->assertCount(2,$params);
        $this->assertEquals($meth->invoke('foo','bar'), 'foo-bar');
    }

    /** @test */
    public function can_reflect_string_static_method() {
        $meth = reflect_callable('Webapps\Tests\DummyReflectCallable::staticOperation');
        $params = $meth->getParameters();

        $this->assertCount(2,$params);
        $this->assertEquals($meth->invoke(null, 'foo','bar'), 'foo-bar');
    }

    /** @test */
    public function can_reflect_array_static_method() {
        $meth = reflect_callable([DummyReflectCallable::class, 'staticOperation']);
        $params = $meth->getParameters();

        $this->assertCount(2,$params);
        $this->assertEquals($meth->invoke(null, 'foo','bar'), 'foo-bar');
    }

    /** @test */
    public function can_reflect_callable_object() {
        $obj = new DummyReflectCallable;
        $meth = reflect_callable($obj);
        $params = $meth->getParameters();

        $this->assertCount(2,$params);
        $this->assertEquals($meth->invoke($obj, 'foo','bar'), 'foo-bar');
    }

    /** @test */
    public function can_reflect_instance_method_object() {
        $obj = new DummyReflectCallable;
        $meth = reflect_callable([$obj, 'operation']);
        $params = $meth->getParameters();

        $this->assertCount(2,$params);
        $this->assertEquals($meth->invoke($obj, 'foo','bar'), 'foo-bar');
    }
}

class DummyReflectCallable {
    public function __invoke(string $a, string $b) { return "$a-$b"; }
    public function operation(string $a, string $b) { return "$a-$b"; }
    public static function staticOperation(string $a, string $b) { return "$a-$b"; }
}
