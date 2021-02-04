# OAuth_proxy

Description...

![alt text](https://github.com/Faridjan/images/blob/main/proxy_lib/proxy.png?raw=true)


## Init 
```php
<?php

$converter = new JWTConverter();

$configStorage = new DotEnvConfigStorage(__DIR__ . '/../'); // Path to .env file
$configStorage->load();

// Optional variable - Http client
$httpClient = new CurlHttpClient();
```
## Create Proxy 
```php
$proxy = new Proxy($converter, $configStore, $httpClient);
```

### Methods
```php
// Login
$username = new UsernameType('username');
$password = new PasswordType('password');
$oAuthData = $proxy->login($username, $password); // string|Exception 

// Logout
$oAuthData = 'crypted_and_converted_jwt_array_to_string';
$proxy->logout($oAuthData); // true|Exception

// Check
$oAuthData = 'crypted_and_converted_jwt_array_to_string';
$oAuthData = $proxy->check($oAuthData); // string|Exception
```


## .env example

```env
OAUTH_BASE_URL="http://0.0.0.0:8080"

OAUTH_TYPE="Bearer"

OAUTH_URL="oauth/auth"
OAUTH_CHECK_URL="oauth/user/check"
OAUTH_LOGOUT_URL="oauth/user/logout"

OAUTH_GRANT_TYPE="password_domain"
OAUTH_REFRESH_GRANT_TYPE="refresh_domain"
OAUTH_DOMAIN="test.com"

OAUTH_CLIENT_ID="app"
OAUTH_CLIENT_SECRET=""
OAUTH_ACCESS_TYPE="offline"
```
