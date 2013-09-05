<?php


/**
 * Router
 *
 * @author Marius Balčytis <m.balcytis@evp.lt>
 */
class Paysera_WalletApi_Util_Router
{
    const ENDPOINT_API_PROD = 'https://wallet.paysera.com';
    const ENDPOINT_AUTH_PROD = 'https://www.paysera.com/frontend';

    const OAUTH_API_PATH = '/oauth/v1/';
    const WALLET_API_PATH = '/rest/v1/';
    const PUBLIC_KEY_PATH = '/publickey';
    const OAUTH_PATH = '/oauth';
    const TRANSACTION_PATH = '/wallet/confirm';

    protected $apiEndpoint;
    protected $authEndpoint;

    public function __construct($apiEndpoint = self::ENDPOINT_API_PROD, $authEndpoint = self::ENDPOINT_AUTH_PROD)
    {
        $this->apiEndpoint = $apiEndpoint;
        $this->authEndpoint = $authEndpoint;
    }

    /**
     * Gets URI to redirect user to confirm transaction
     *
     * @param string $transactionKey
     * @param string $lang
     *
     * @return string
     */
    public function getTransactionConfirmationUri($transactionKey, $lang = 'en')
    {
        return $this->authEndpoint . '/' . $lang . self::TRANSACTION_PATH . '/' . $transactionKey;
    }

    public function getOAuthEndpoint()
    {
        return $this->authEndpoint . self::OAUTH_PATH;
    }

    public function getApiEndpoint($path = null)
    {
        if (substr($path, 0, 7) !== 'http://' && substr($path, 0, 8) !== 'https://') {
            return $this->apiEndpoint . $path;
        } else {
            return $path;
        }
    }

    public function getWalletApiEndpoint()
    {
        return $this->getApiEndpoint(self::WALLET_API_PATH);
    }

    public function getOAuthApiEndpoint()
    {
        return $this->getApiEndpoint(self::OAUTH_API_PATH);
    }

    public function getPublicKeyUri()
    {
        return $this->getApiEndpoint(self::PUBLIC_KEY_PATH);
    }
} 