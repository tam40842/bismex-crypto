<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\UrlGenerator;
use App\Http\Controllers\Vuta\Vuta;
use View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(UrlGenerator $url)
    {
        view()->composer('*', function ($view) {
            $options = Vuta::get_settings(['title_website', 'site_email', 'site_phone', 'site_address', 'site_logo', 'favicon', 'is_website_notice', 'website_notice', 'tawk_to_id', 'google_analytics', 'maintenance_content', 'maintenance_expired', 'seo_separator']);

            View::share('options', $options);
        });

        // if (env('APP_ENV') !== 'local') {
        //     $url->forceScheme('https');
        // }
    }
}
