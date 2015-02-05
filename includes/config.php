<?php

// Production Mode
$accounts_base = "https://accounts.payvio.com";
$wp_base = get_bloginfo('url');
$api_base = "https://api.payvio.com";

// Dev Mode
/*
$accounts_base = "https://accounts.dev.payvio.com";
$wp_base = get_bloginfo('url');
$api_base = "https://api.dev.payvio.com";
*/

// Local Mode
/*
$accounts_base = "https://localhost:44302";
$wp_base = "https://localhost:44305";
$api_base = "https://localhost:44303";
*/

define('PAYVIO_ACCOUNTS_URL', $accounts_base);
define('PAYVIO_OAUTH_AUTH_URL', $accounts_base + '/oauth2/auth');
define('PAYVIO_OAUTH_TOKEN_URL', $accounts_base + '/oauth2/token');
define('PAYVIO_REDIRECT_URL', $wp_base + '/oauth2callback/payvio');
define('PAYVIO_API_USERINFO_URL', $api_base + '/oauth2/v1/userinfo?access_token=[0]');
define('PAYVIO_API_CHARGE_URL', $api_base + '/oauth2/v1/accountcharge?access_token=[0]');
define('PAYVIO_API_SUBSCRIBE_URL', $api_base + '/oauth2/v1/subscription?access_token=[0]');
define('PAYVIO_AUTHZ_CHARGE_PERMISSION', 'user.charge');
define('PAYVIO_AUTHZ_USERINFO_PERMISSION', 'user.info');
define('PAYVIO_AUTHZ_SUBSCRIBE_PERMISSION', 'user.subscribe');

?>