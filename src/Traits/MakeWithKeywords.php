<?php declare(strict_types=1);

namespace Webapps\Util\Traits;

use ReflectionClass;
use InvalidArgumentException;

use Webapps\Util\KeywordArguments\KeywordMaker;

/**
 * Implements a static make function that will build the class from an array
 */
trait MakeWithKeywords {

    protected static $_maker = [];

    /**
     * Creates an instance of this object from an array with string keys.
     *
     * Default implementation just maps array values to ctor params.
     *
     * @param array|ArrayAccess $fields
     */
    public static function make($fields) {
        return static::_getMaker()->make($fields);
    }


    /**
     * Creates an instance of this object from an array with string keys.
     *
     * Keys can be amiguously cased and will result in camel case.
     *
     * @param array|ArrayAccess $fields
     */
    public static function makeSafe(iterable $fields, callable $keyFunc = null)
    {
        return static::_getMaker()->makeSafe($fields, $keyFunc);
    }

    protected static function _getMaker() : KeywordMaker {
        if (!isset(static::$_maker[static::class])) {
            static::$_maker[static::class] = new KeywordMaker(static::class);
        }

        return static::$_maker[static::class];
    }
}
