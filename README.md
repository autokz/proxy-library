# OAuth_proxy

proxy actions:

- AccessAction::class
- LoginAction::class
- LogoutAction::class


# Example

```php
<?php

$converter = new JWTConverter();

$configStorage = new DotEnvConfigStorage(__DIR__ . '/../');
$configStorage->load();

$action = new LoginAction($converter, $configStorage);
$username = new UsernameType('username');
$password = new PasswordType('password');

$arrayFrontendAuthData = $action->login($username, $password);

### To check AuthData use AccessAction::class
$accessAction = new AccessAction($this->converter, $this->configStore);
$arrayFrontendAuthData = $accessAction->execute($arrayFrontendAuthData);

### To logout user from domains use LogoutAction::class
$logoutAction = new LogoutAction($this->converter, $this->configStore, $this->httpClient);
$result = $logoutAction->execute($arrayFrontendAuthData);

```

.env example

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
