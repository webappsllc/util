<?php declare(strict_types=1);

namespace Webapps\Util\KeywordArguments;

use Closure;
use ReflectionMethod;
use ReflectionFunction;
use ReflectionFunctionAbstract;
use InvalidArgumentException;

use Illuminate\Support\Arr;


/**
 * Allows invocation of callables by parameter names.
 */
class KeywordCallable
{
    /** @var callable $callable */
    protected $callable;

    /** @var Parameters */
    protected $parameters;

    public function __construct(callable $callable)
    {
        $this->callable = $callable;
        $this->parameters = new KeywordParameters(reflect_callable($callable)->getParameters());
    }

    /**
     * invokes the reflected callback
     *
     * @param array|ArrayAccess $fields
     *
     * @return mixed
     */
    public function __invoke($fields)
    {
        return ($this->callable)(...$this->parameters->makeList($fields));
    }
}

