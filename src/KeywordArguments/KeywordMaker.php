<?php declare(strict_types=1);

namespace Webapps\Util\KeywordArguments;

use ReflectionClass;

use Illuminate\Support\Str;

/**
 * Allows for the construction of objects with keywords.
 */
class KeywordMaker {

    protected $className;
    protected $parameters;

    public function __construct(string $className) {
        $this->className = $className;
        $this->parameters = KeywordParameters::forReflectionFunction((new ReflectionClass($className))->getConstructor());
    }

    /**
     * Creates an instance of this object from an array with string keys.
     *
     * Default implementation just maps array values to ctor params.
     *
     * @param array|ArrayAccess|iterable $fields
     */
    public function make(iterable $fields) {
        return new $this->className(...$this->parameters->makeList($fields));
    }


    /**
     * Creates an instance of this object from an array with string keys.
     *
     * Keys can be amiguously cased and will result in camel case.
     *
     * @param array|ArrayAccess $fields
     */
    public function makeSafe(iterable $fields, callable $keyFunc = null)
    {
        if (is_null($keyFunc)) {
            $keyFunc = 'Illuminate\\Support\\Str::camel';
        }

        $args = [];
        foreach ($fields as $key => $value) {
            $args[$keyFunc($key)] = $value;
        }
        return static::make($args);
    }

}
