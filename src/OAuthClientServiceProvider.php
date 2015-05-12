<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/5/12 0012
 * Time: 下午 10:01
 */

namespace Jzyuchen\OAuthClient;


use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

class OAuthClientServiceProvider extends ServiceProvider {

    public function boot(){
        $config     = realpath(__DIR__.'/../config/config.php');

        $this->publishes([
            $config     => config_path('oauth-client.php'),
        ]);
    }
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app['oauthclient'] = $this->app->share(function($app){
            return new OAuthClient();
        });
    }
}