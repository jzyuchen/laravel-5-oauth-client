<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/5/12 0012
 * Time: 下午 10:01
 */

namespace Jzyuchen\OAuthClient;

class OAuthClient {

    public function __construct(){

    }

    public function get($provider){
        $clientId = \Config::get('oauth-client.consumers.'.$provider.'.clientId');
        $clientSecret = \Config::get('oauth-client.consumers.'.$provider.'.clientSecret');
        $className = \Config::get('oauth-client.consumers.'.$provider.'.className');
        $redirectUri  = \Config::get('oauth-client.consumers.'.$provider.'.redirectUri');
        $model = new $className(array(
            'clientId' => $clientId,
            'clientSecret' => $clientSecret,
            'redirectUri' => $redirectUri
        ));

        return $model;
    }
}
