<?php declare(strict_types=1);

namespace WebApps\Util\KeywordArguments;

use InvalidArgumentException;
use ReflectionFunctionAbstract;

use Illuminate\Support\Arr;

class KeywordParameters
{
    /** @var iterable[ReflectionParameter] */
    protected $parameters;

    public function __construct(iterable $parameters)
    {
        $this->parameters = $parameters;
    }

    public static function forReflectionFunction(ReflectionFunctionAbstract $reflectionFunction)
    {
        return new static($reflectionFunction->getParameters());
    }

    /**
     * Accepts a string keyed array and maps the values to parameter positions.
     *
     * Any parameters with the name kwSplat or keywordSplat will take the overflow arguments.
     *
     * @param array|ArrayAccess $fields
     * @return array
     */
    public function makeList($fields) : array
    {
        $args = [];
        $found = [];
        $keywordSplatIndex = null;
        $idx = 0;


        foreach ($this->parameters as $param) {
            $name = $param->name;
            if (Arr::exists($fields, $name)) {
                $args[] = $fields[$name];
                $found[] = $name;
            } elseif ($name === 'keywordSplat' || $name === 'kwSplat') {
                $args[] = null;
                $keywordSplatIndex = $idx;
            } elseif ($param->isDefaultValueAvailable()) {
                $args[] = $param->getDefaultValue();
            } elseif ($param->isOptional() || $param->allowsNull()) {
                $args[] = null;
            } else {
                throw new InvalidArgumentException("Cannot build object. Missing parameter {$param->name}.");
            }
            $idx++;
        }

        if (!is_null($keywordSplatIndex)) {
            $args[$keywordSplatIndex] = Arr::except($fields, $found);
        }

        return $args;
    }
}
