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

use Webapps\Util\KeywordMaker;
use Webapps\Util\Traits\MakeWithKeywords; 
use Webapps\Util\Traits\ReadOnlyArrayAccess; 
use Webapps\Util\Contracts\ConvertsToData; 

/**
 * A class designed to encapsulate a simple data object.
 *
 * Subclasses should define the properties they will use.
 *
 * A key feature of this class is that public properties are considered part of the value. Protected properties are not and are available to facilitate logic.
 */
abstract class ValueObject implements
    Arrayable,
    ArrayAccess,
    IteratorAggregate,
    ConvertsToData
{
    use MakeWithKeywords;
    use ReadOnlyArrayAccess;

    public function __construct(array $kwSplat = [])
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
        $lookup = $this->all();
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
        foreach ($this->all() as $key => $value) {
            yield $key => $value;
        }
    }

    /**
     * Convert the object to an assoc array.
     *
     * Only includes public properies.
     */
    public function toArray() : array {
        return collect($this->all())->toArray();
    }

    /**
     * Convert the object to a collection.
     *
     * Only includes public properties.
     */
    public function toCollection() : Collection
    {
        return new Collection($this->all());
    }

    /**
     * Return an array with only some values specified by keys.
     *
     * @param array|string $keys - The keys to find and return in assoc array.
     *
     * @return array
     */
    public function only($keys) : array {
        return Arr::only($this->all(), Arr::wrap($keys));
    }

    /**
     * Return an array with some values removed, specified by keys.
     *
     * @param array|string $keys - The keys to find and return in assoc array.
     *
     * @return array
     */
    public function except($keys) : array {
        return Arr::except($this->all(), Arr::wrap($keys));
    }

    /**
     * Get all the values in the object as an assoc array.
     */
    public function all() : array {
        return public_object_vars($this);
    }

    /**
     * Build an array from the assoc array of this object and another array.
     */
    public function merge(array $other) : array {
        return array_merge($this->all(), $other);
    }

    public function toData() : array {
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
