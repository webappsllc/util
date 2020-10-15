<?php declare(strict_types=1);

namespace Webapps\Util\Traits;

use ReflectionClass;

use Webapps\Util\KeywordArguments\KeywordMaker;

/**
 * Implements a static make function that will build the class from an array
 */
trait BuildsWithKeywords {

    /**
     * Factory method for creating objects with properties from this.
     *
     * @param string $klass - The class to build
     * @param array $override - Additional arguments for the target class constructor.
     * @param array $alias - Renamings of properties to
     *
     * @return object of the type given as the first argument
     */
    public function build(string $klass, array $override = [], array $alias = []) : object {
        $maker = KeywordMaker::forClass($klass);

        $refClass = new ReflectionClass($this);
        foreach($refClass->getProperties() as $prop) {
            $name = $prop->getName();

            if(isset($alias[$name])) {
                $name = $alias[$name];
            }

            if(!array_key_exists($name, $override) && $prop->isInitialized($this)) {
                $override[$name] = $prop->getValue($this);
            }

        }

        return $maker->make($override);
    }
}
