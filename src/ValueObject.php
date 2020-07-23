<?php declare(strict_types=1);

namespace Webapps\Util;

use LogicException;
use ArrayAccess;
use IteratorAggregate;
use ReflectionClass;
use ReflectionProperty;
use Traversable;
use JsonSerializable;

use public_object_vars;
use Illuminate\Support\{
    Arr,
    Str,
    Collection
};
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

use Webapps\Util\KeywordMaker;
use Webapps\Util\Contracts\MakeWithKeywords as MakeWithKeywordsContract;
use Webapps\Util\Traits\MakeWithKeywords as MakeWithKeywordsTrait;
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
    JsonSerializable,
    Jsonable,
    MakeWithKeywordsContract
{
    use MakeWithKeywordsTrait;
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

    /**
     * Called when a poprety that does not exist is attempted to be set. Always throws.
     * @throws Exception
     */
    public function __set($name, $_value)
    {
        throw new LogicException("Cannot set new properties on ValueObjects.");
    }

    /**
     * Convert the object into something JSON serializable.
     *
     * @return array
     */
    public function jsonSerialize() {
        return array_map(function ($value) {
            if ($value instanceof JsonSerializable) {
                return $value->jsonSerialize();
            } elseif ($value instanceof Jsonable) {
                return json_decode($value->toJson(), true);
            } elseif ($value instanceof Arrayable) {
                return $value->toArray();
            } elseif (is_object($value)) {
                return public_object_vars($value);
            }

            return $value;
        }, $this->toArray());
    }

    /**
     *  Convert the object to a json string.
     * 
     *  @return string
     */
    public function toJson($options = 0) : string {
        return json_encode($this->jsonSerialize(), $options);
    }

    /**
     * String representation of this object as json.
     */
    public function __toString() : string {
        return $this->toJson();
    }
}
