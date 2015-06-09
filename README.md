# laravel-5-oauth-client
qq, weibo, weixin oauth for laravel 5

# QQ 调用示例
## 1.获取QQ验证的用户确认URL
```PHP
$oauth = new OAuthClient();
$qq = $oauth->get('qq');
echo $qq->getAuthorizationUrl();
```
## 2.QQ回调处理
```PHP
$oauth = new OAuthClient();
$provider = $oauth->get('qq');
// Try to get an access token (using the authorization code grant)
$token = $provider->getAccessToken('authorization_code', [
    'code' => Input::get('code')
]);

// Optional: Now you have a token you can look up a users profile data
try {

    // We got an access token, let's now get the user's details
    $userDetails = $provider->getUserDetails($token);
    // Use these details to create a new profile
    printf('Hello %s!', $userDetails->firstName);

} catch (Exception $e) {

    // Failed to get user details
    exit('Oh dear...');
}

echo '<pre>';
var_dump($userDetails);
echo "\n";

// Use this to interact with an API on the users behalf
echo $token->accessToken."\n";

// Use this to get a new access token if the old one expires
echo $token->refreshToken."\n";

// Number of seconds until the access token will expire, and need refreshing
echo $token->expires."\n";

var_dump($token);
echo '</pre>';
  ```
