<?php 

namespace Webapps\Util\Contracts;

interface MakeWithKeywords {
    /**
     * Creates an instance of this object from an array with string keys.
     *
     * Default implementation just maps array values to ctor params.
     *
     * @param array|ArrayAccess $fields
     */
    public static function make($fields);

    /**
     * Creates an instance of this object from an array with string keys.
     *
     * Keys can be amiguously cased and will result in camel case.
     *
     * @param array|ArrayAccess $fields
     */
    public static function makeSafe(iterable $fields, callable $keyFunc = null);
}
