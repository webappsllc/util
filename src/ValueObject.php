<?php declare(strict_types=1);

namespace Webapps\Util;

use LogicException;
use ArrayAccess;
use IteratorAggregate;
use ReflectionClass;
use ReflectionProperty;

use public_object_vars;
use Illuminate\Support\{
    Arr,
    Str,
    Collection
};
use Illuminate\Contracts\Support\Arrayable;

use Webapps\Util\Traits\MakeWithKeywords; 
use Webapps\Util\Traits\ReadOnlyArrayAccess; 
use Webapps\Util\Contracts\ConvertsToData; 

/**
 * A class designed to encapsulate a simple data object.
 *
 * Subclasses should define the properties they will use.
 */
abstract class ValueObject implements
    Arrayable,
    ArrayAccess,
    IteratorAggregate,
    ConvertsToData
{
    use MakeWithKeywords;
    use ReadOnlyArrayAccess;

    public function __construct(array $kwSplat)
    {
        foreach ($kwSplat as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }

    /**
     * Returns a Closure to get values by string name
     *
     * @return Closure
     */
    public function toClosure() : callable
    {
        $lookup = $this->toArray();
        return function ($property) use ($lookup) {
            return $lookup[$property] ?? null;
        };
    }

    /**
     * Returns an iterator suitable for use with foreach.
     *
     * @return iterable
     */
    public function getIterator() : iterable
    {
        foreach ($this->toArray() as $key => $value) {
            yield $key => $value;
        }
    }

    /**
     * Convert the object to an array.
     *
     * Only includes public properies.
     */
    public function toArray() : array
    {
        return public_object_vars($this);
    }

    /**
     * Convert the object to a collection.
     *
     * Only includes public properties.
     */
    public function toCollection() : Collection
    {
        return new Collection($this->toArray());
    }

    public function toData() : array
    {
        $result = [];
        foreach($this->toArray() as $key => $value) {
            $result[Str::snake($key)] = $value;
        }
        return $result;
    }

    /**
     * Called when a poprety that does not exist is attempted to be set. Always throws.
     * @throws Exception
     */
    public function __set($name, $_value)
    {
        throw new LogicException("Cannot set new properties on ValueObjects.");
    }
}
