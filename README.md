PHP client for Paysera.com Wallet API
=========
This is repository for PHP client, used with [Paysera.com Wallet API](https://developers.paysera.com/en/wallet).

OAuth example
--------------
```php
<?php

// setup autoloader for Paysera Wallet API library
require_once '../../lib-wallet-php-client/src/Paysera/WalletApi/Autoloader.php';
Paysera_WalletApi_Autoloader::register();

// credentials for API
$clientId = 'CLIENT_ID';
// $secret - shared secret to use in MAC auth
$secret = 'SECRET';
// or information about certificate to use in SSL auth
$secret = Paysera_WalletApi_Http_ClientCertificate::create()
    ->setCertificatePath('/path/to/cert.crt')
    ->setPrivateKeyPath('/path/to/private.key');

// create main object to use for all functionality
$api = new Paysera_WalletApi(Paysera_WalletApi_Container::create($clientId, $secret));

// example how to get ask and get information about paysera.com user
session_start();
try {
    if (!isset($_SESSION['token'])) {           // no token in session - let's get the token
        $token = $api->getOAuthAccessToken();   // this gets code query parameter if available and exchanges for token
        if ($token === null) {                  // no code parameter - redirect user to authentication endpoint
            $redirectUri = null;                // URL of this file; it's optional parameter
            header('Location: ' . $api->getAuthorizationUri(array(              // scopes are optional, no scope allows to get user ID/wallet ID
                Paysera_WalletApi_OAuth_Consumer::SCOPE_EMAIL,                  // to get user's main email address
                // Paysera_WalletApi_OAuth_Consumer::SCOPE_IDENTITY,            // this scope allows to get personal code, name and surname
                // Paysera_WalletApi_OAuth_Consumer::SCOPE_FULL_NAME,           // use this scope if only name and surname is needed
                // Paysera_WalletApi_OAuth_Consumer::SCOPE_IDENTITY_OFFLINE,    // this allows to get identity by user ID, after token has expired, using API, not related to token
            ), $redirectUri));
        } else {
            $_SESSION['token'] = $token;
        }
    }

    if (isset($_SESSION['token'])) {
        $tokenRelatedApi = $api->createApiForToken($_SESSION['token']);
        echo '<pre>';
        $user = $tokenRelatedApi->getUser();
        var_dump($user);
        // $user->getId();                            // you can save user ID (on paysera.com), user's email etc.
        // var_dump($api->getUserIdentity($userId));  // if you have offline scope, you can get info by user ID later
        echo '</pre>';
        $_SESSION['token'] = $tokenRelatedApi->getCurrentAccessToken();     // this could be refreshed, re-save
    }

} catch (Exception $e) {
    echo '<pre>', $e, '</pre>';
}
```


##Contacts
If you have any further questions feel free to contact us:

"EVP International", UAB    
Mėnulio g. 7    
LT-04326 Vilnius    
Email: pagalba@mokejimai.lt    
Tel. +370 (5) 2 03 27 19    
Faksas +370 (5) 2 63 91 79    
