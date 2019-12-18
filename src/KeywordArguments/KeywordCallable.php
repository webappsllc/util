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
        $this->parameters = new KeywordParameters($this->getReflector()->getParameters());
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


    /**
     * Returns the appropriate class of the callable reflection.
     *
     * @return \ReflectionFunctionAbstract
     * @throws \LogicException
     */
    public function getReflector() : ReflectionFunctionAbstract
    {
        if ($this->callable instanceof Closure) {
            return new ReflectionFunction($this->callable);
        } elseif (is_object($this->callable)) {
            return new ReflectionMethod($this->callable, '__invoke');
        } else {
            $callable = $this->explodeCallable();
            if (count($callable) === 1) {
                return new ReflectionFunction($this->callable);
            } elseif (count($callable) === 2) {
                return new ReflectionMethod(...$callable);
            }
        }
        throw new InvalidArgumentException('Type not found for callable');
    }

    /**
     * explodes the reflected callback in an array and splits method/function from class/object
     *
     * @return array
     */
    private function explodeCallable()
    {
        if (is_string($this->callable)) {
            return explode('::', $this->callable);
        }
        if (is_array($this->callable)) {
            return $this->callable;
        }
        return array($this->callable);
    }
}

