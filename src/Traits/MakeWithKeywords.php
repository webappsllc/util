<?php declare(strict_types=1);

namespace Webapps\Util\Traits;

use ReflectionClass;
use InvalidArgumentException;

use Webapps\Util\KeywordArguments\KeywordMaker;

/**
 * Implements a static make function that will build the class from an array
 */
trait MakeWithKeywords {

    /**
     * Creates an instance of this object from an array with string keys.
     *
     * Default implementation just maps array values to ctor params.
     *
     * @param array|ArrayAccess $fields
     */
    public static function make($fields) {
        return KeywordMaker::forClass(static::class)->make($fields);
    }
}
