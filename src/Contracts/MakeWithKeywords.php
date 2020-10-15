<?php declare(strict_types=1);

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
}
