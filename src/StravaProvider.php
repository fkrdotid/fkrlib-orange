<?php


namespace FkrCode\Strava;

use GuzzleHttp\Client;

use Illuminate\Support\ServiceProvider;

class StravaProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/config/environment.php', 'fkr_strava'
        );

        $this->publishes([
            __DIR__ . '/config/environment.php' => config_path('fkr_strava.php')
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('Strava', function ($app) {
            $client = new Client();

            return new Strava(
                config('fkr_strava.client_id'),
                config('fkr_strava.client_secret'),
                config('fkr_strava.redirect_uri'),
                $client
            );

        });
    }
}