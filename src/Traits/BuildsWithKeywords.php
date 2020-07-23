<?php declare(strict_types=1);

namespace Webapps\Util\Traits;

use ReflectionClass;
use InvalidArgumentException;

use Webapps\Util\KeywordArguments\KeywordMaker;

/**
 * Implements a static make function that will build the class from an array
 */
trait BuildsWithKeywords {

    /**
     * Factory method for creating objects with properties from this.
     *
     * @param string $klass - The class to build
     */
    public function build(string $klass, array $override = [], array $alias = []) {
        $maker = KeywordMaker::forClass($klass);

        $refClass = new ReflectionClass($this);
        foreach($refClass->getProperties() as $prop) {
            $name = $prop->getName();

            if(isset($alias[$name])) {
                $name = $alias[$name];
            }

            if(!array_key_exists($name, $override)) {
                $override[$name] = $prop->getValue($this);
            }

        }

        return $maker->make($override);
    }
}
