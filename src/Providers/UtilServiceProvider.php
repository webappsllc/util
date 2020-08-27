<?php declare(strict_types=1);

namespace Webapps\Util\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Arr;

use Webapps\Util\Mixins\ArrMixin;

class UtilServiceProvider extends ServiceProvider {

    /**
     * Register services.
     * @return void
     */
    public function register() {
    }

    public function boot() {
        Arr::mixin(new ArrMixin);
    }
}
