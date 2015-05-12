<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/5/12 0012
 * Time: 下午 11:11
 */

namespace Jzyuchen\OAuthClient\Facade;


use Illuminate\Support\Facades\Facade;

class OAuthClient extends Facade {

    protected static function getFacadeAccessor() { return 'oauthclient'; }
}