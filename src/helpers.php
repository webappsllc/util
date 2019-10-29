<?php declare(strict_types=1);

/**
 * Allows the extraction of public object properties
 */
function public_object_vars(object $item) : array {
    return get_object_vars($item);
}
