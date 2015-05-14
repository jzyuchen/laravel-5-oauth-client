<?php

namespace Jzyuchen\OAuthClient\Provider;

use League\OAuth2\Client\Entity\User;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Token\AccessToken;

class QQ extends AbstractProvider  {

    public $responseType = 'string';
    protected $apiDomain = 'https://graph.qq.com/user';
    protected $openid = ''; // only stupid tencent offers this..

    public function __construct($options = []){

        if (!array_has($options, 'redirectUri')){
            $options['redirectUri'] = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];
        }
        parent::__construct($options);
    }

    /**
     * Get the URL that this provider uses to begin authorization.
     *
     * @return string
     */
    public function urlAuthorize()
    {
        return 'https://graph.qq.com/oauth2.0/authorize';
    }

    /**
     * Get the URL that this provider users to request an access token.
     *
     * @return string
     */
    public function urlAccessToken()
    {
        return 'https://graph.qq.com/oauth2.0/token';
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
        return 'https://graph.qq.com/oauth2.0/me?access_token='.$token;
    }

    public function getUserDetails(\League\OAuth2\Client\Token\AccessToken $token)
    {
        // Fetching openid from '/me' with access_token
        $response = $this->fetchUserDetails($token);
        // pickup openid
        $first_open_brace_pos = strpos($response, '{');
        $last_close_brace_pos = strrpos($response, '}');
        $response = json_decode(substr(
            $response,
            $first_open_brace_pos,
            $last_close_brace_pos - $first_open_brace_pos + 1
        ));

        $this->openid = $response->openid;
        // fetch QQ user profile
        $params = [
            'access_token' => $token->accessToken,
            'oauth_consumer_key' => $this->clientId,
            'openid' => $this->openid
        ];
        $request = $this->httpClient->get($this->apiDomain . '/get_user_info?' . http_build_query($params));
        $response = json_decode($request->send()->getBody());
        // check response status
        if ($response->ret < 0) {
            // handle tencent's style exception.
            $result['code'] = $response->ret;
            $result['message'] = $response->msg;
            throw new \League\OAuth2\Client\Exception\IDPException($result);
        }
        return $this->userDetails($response, $token);
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
        $user = new User();
        $gender = (isset($response->gender)) ? $response->gender : null;
        $province = (isset($response->province)) ? $response->province : null;
        $imageUrl = (isset($response->figureurl)) ? $response->figureurl : null;
        $user->exchangeArray([
            'uid' => $this->openid,
            'nickname' => $response->nickname,
            'gender' => $gender,
            'province' => $province,
            'imageUrl' => $imageUrl,
            'urls'  => null,
        ]);
        return $user;
    }

    public function userUid($response, \League\OAuth2\Client\Token\AccessToken $token)
    {
        return $response->id;
    }
}