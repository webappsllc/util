<?php declare(strict_types=1);

namespace Webapps\Util\Mixins;

class ArrMixin {
    public function camelKeys() : callable {
        return function (iterable $lookup) : array {
            $result = [];

            foreach($lookup as $key => $value) {
                $result[static::camel($key)] = $value;
            }

            return $result;
        }
    }
}
