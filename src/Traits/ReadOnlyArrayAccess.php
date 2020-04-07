<?php declare(strict_types=1);

namespace Webapps\Util\Traits;

trait ReadOnlyArrayAccess {

    /**
    * Determine the existence of a property.
    */
    public function offsetExists ( $mixOffset ) : bool
    {
        return property_exists($this, $mixOffset);
    }

    /**
     * Get a property with array access.
     */
    public function offsetGet ( $offset )
    {
        return $this->{$offset};
    }

    /**
     * Called when attempting to set a property with array access. Always throws.
     * @throws LogicException
     */
    public function offsetSet ( $mixOffset, $mixValue )
    {
        throw new LogicException("Cannot mutate value.");
    }

    /**
     * Called when attempting to unset a property with array access. Always throws.
     * @throws LogicException
     */
    public function offsetUnset ( $mixOffset )
    {
        throw new LogicException("Cannot unset value.");
    }
}
