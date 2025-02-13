<?php

namespace Elfeffe\BladeCacheDirective;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Cache;
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

    public function packageBooted()
    {
        Blade::directive('cache', function ($expression) {
            return "<?php
                // Set default tags and prepare directive arguments
                \$__cache_directive_tags = ['blade_cache'];
                \$__cache_directive_arguments = [{$expression}];

                // Determine which arguments were passed
                if (count(\$__cache_directive_arguments) === 3) {
                    [\$__cache_directive_key, \$__cache_directive_ttl, \$__cache_directive_tags] = \$__cache_directive_arguments;
                } elseif (count(\$__cache_directive_arguments) === 2) {
                    [\$__cache_directive_key, \$__cache_directive_ttl] = \$__cache_directive_arguments;
                } else {
                    [\$__cache_directive_key] = \$__cache_directive_arguments;
                    \$__cache_directive_ttl = config('blade-cache-directive.ttl');
                }

                // Ensure the tags variable is always an array
                if (!is_array(\$__cache_directive_tags)) {
                    \$__cache_directive_tags = (array) \$__cache_directive_tags;
                }

                // If caching is enabled and the cache has this key, output the cached content
                if (config('blade-cache-directive.enabled') && Cache::tags(\$__cache_directive_tags)->has(\$__cache_directive_key)) {
                    echo Cache::tags(\$__cache_directive_tags)->get(\$__cache_directive_key);
                } else {
                    // Otherwise, start output buffering
                    \$__cache_directive_buffering = true;
                    ob_start();
                }
            ?>";
        });

        Blade::directive('endcache', function () {
            return "<?php
                    // Capture the buffered output
                    \$__cache_directive_buffer = ob_get_clean();

                    // If caching is enabled, store the output in cache using tags
                    if (config('blade-cache-directive.enabled')) {
                        Cache::tags(\$__cache_directive_tags)->put(\$__cache_directive_key, \$__cache_directive_buffer, \$__cache_directive_ttl);
                    }

                    // Echo the output regardless of caching
                    echo \$__cache_directive_buffer;

                    // Clean up directive variables
                    unset(\$__cache_directive_key, \$__cache_directive_ttl, \$__cache_directive_buffer, \$__cache_directive_buffering, \$__cache_directive_arguments, \$__cache_directive_tags);
            ?>";
        });
    }
}
