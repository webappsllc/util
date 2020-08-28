<?php declare(strict_types=1);

namespace Webapps\Util\Mixins;

use Illuminate\Support\Str;

class ArrMixin {

    public function camelKeys() : callable {
        return function (iterable $lookup) : array {
            $result = [];

            foreach($lookup as $key => $value) {
                $result[Str::camel($key)] = $value;
            }

            return $result;
        };
    }

}
