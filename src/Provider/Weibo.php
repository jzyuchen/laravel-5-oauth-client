<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/5/12 0012
 * Time: 下午 10:17
 */

namespace Jzyuchen\OAuthClient\Provider;


use League\OAuth2\Client\Entity\User;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Token\AccessToken;

class Weibo extends AbstractProvider {

    protected $apiDomain = 'https://api.weibo.com/2/';
    protected $site = 'http://weibo.com/';

    public function __construct($options=[]){
        parent::__construct($options);
    }
    /**
     * Get the URL that this provider uses to begin authorization.
     *
     * @return string
     */
    public function urlAuthorize()
    {
        return 'https://api.weibo.com/oauth2/authorize';
    }

    /**
     * Get the URL that this provider users to request an access token.
     *
     * @return string
     */
    public function urlAccessToken()
    {
        return 'https://api.weibo.com/oauth2/access_token';
    }

    /**
     * Get the URL that this provider uses to request user details.
     *
     * Since this URL is typically an authorized route, most providers will require you to pass the access_token as
     * a parameter to the request. For example, the google url is:
     *
     * 'https://www.googleapis.com/oauth2/v1/userinfo?alt=json&access_token='.$token
     *
     * @param AccessToken $token
     * @return string
     */
    public function urlUserDetails(AccessToken $token)
    {
        return $this->apiDomain.'users/show.json?access_token='.$token->accessToken.'&uid='.$token->uid;
    }

    /**
     * Given an object response from the server, process the user details into a format expected by the user
     * of the client.
     *
     * @param object $response
     * @param AccessToken $token
     * @return mixed
     */
    public function userDetails($response, AccessToken $token)
    {
        //dd($response);
        $user = new User;
        $user->uid = $response->id;
        $user->nickname = isset($response->name) ? $response->name : $response->domain;
        $user->name = isset($response->screen_name) ? $response->screen_name : null;
        $user->location = isset($response->location) ? $response->location : null;
        $user->imageUrl = isset($response->avatar_large) ? $response->avatar_large : null;
        $user->description = isset($response->description) ? $response->description : null;
        $user->gender = isset($response->gender) ? ($response->gender == 'm' ? '男': '女') : null;
        $user->urls = [
            'profile' => $this->site . (isset($response->profile_url) ? $response->profile_url : $response->id),
            'site' => isset($response->url) && $response->url ? $response->url : null,
        ];
        dd($user);
        return $user;
    }
}