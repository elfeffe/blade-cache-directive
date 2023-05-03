<?php

namespace Elfeffe\BladeCacheDirective;

use Illuminate\Support\Facades\Blade;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class BladeCacheDirectiveServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('blade-cache-directive')
            ->hasConfigFile();
    }

    public function boot()
    {
        Blade::directive('cache', function ($expression) {
            return "<?php

        ray('dff');

                \$__cache_directive_tags = ['blade_cache'];
                \$__cache_directive_arguments = [{$expression}];
                \$__cache_directive_ttl = config('blade-cache-directive.ttl');

                if (count(\$__cache_directive_arguments) === 3) {
                    [\$__cache_directive_key, \$__cache_directive_tags, \$__cache_directive_ttl] = \$__cache_directive_arguments;
                } elseif (count(\$__cache_directive_arguments) === 2) {
                    [\$__cache_directive_key, \$__cache_directive_tags] = \$__cache_directive_arguments;
                } else {
                    [\$__cache_directive_key] = \$__cache_directive_arguments;
                }

                if (
                \Illuminate\Support\Facades\Cache::tags(\$__cache_directive_tags)->has(\$__cache_directive_key) &&
                 !App::environment('local')
                 ) {
                    echo \Illuminate\Support\Facades\Cache::tags(\$__cache_directive_tags)->get(\$__cache_directive_key);
                } else {
                    \$__cache_directive_buffering = true;
                    ob_start();
            ?>";
        });

        Blade::directive('endcache', function () {
            return "<?php
                    \$__cache_directive_buffer = ob_get_clean();

                    if(\$__cache_directive_tags)
                    {
                        \Illuminate\Support\Facades\Cache::tags(\$__cache_directive_tags)->put(\$__cache_directive_key, \$__cache_directive_buffer, \$__cache_directive_ttl);
                    } else {
                        \Illuminate\Support\Facades\Cache::put(\$__cache_directive_key, \$__cache_directive_buffer, \$__cache_directive_ttl);
                    }

                    echo \$__cache_directive_buffer;

                    unset(\$__cache_directive_key, \$__cache_directive_ttl, \$__cache_directive_buffer, \$__cache_directive_buffering, \$__cache_directive_arguments);
                }
            ?>";
        });

    }
}
