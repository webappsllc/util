<?php declare(strict_types=1);

namespace Webapps\Util\Contracts;

interface BuildsWithKeywords {

    /**
     * Factory method for creating objects with properties from this.
     *
     * @param string $klass - The class to build
     * @param array $override - Additional arguments for the target class constructor.
     * @param array $alias - Renamings of properties to
     *
     * @return object of the type given as the first argument
     */
    public function build(string $klass, array $override = [], array $alias = []);
}
