<?php declare(strict_types=1);

namespace Webapps\Util\Contracts;

/**
 * Use with classes that can be converted to data where data is
 * the equivalent array with snake_case keys.
 */
interface ConvertsToData {
    public function toData() : array;
}
