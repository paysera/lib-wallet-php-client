<?php


/**
 * Router
 *
 * @author Marius Balčytis <m.balcytis@evp.lt>
 */
class Paysera_WalletApi_Util_Router
{
    const ENDPOINT_API_PROD = 'https://wallet.paysera.com';
    const ENDPOINT_AUTH_PROD = 'https://bank.paysera.com/frontend';

    const ENDPOINT_API_SANDBOX = 'https://wallet-sandbox.paysera.com';
    const ENDPOINT_AUTH_SANDBOX = 'https://sandbox.paysera.com/frontend';

    const OAUTH_API_PATH = '/oauth/v1/';
    const WALLET_API_PATH = '/rest/v1/';
    const PUBLIC_KEY_PATH = '/publickey';
    const OAUTH_PATH = '/oauth';
    const REMIND_PASSWORD_PATH = '/wallet/remind-password';
    const TRANSACTION_PATH = '/wallet/confirm';

    protected $apiEndpoint;
    protected $authEndpoint;

    public function __construct($apiEndpoint = self::ENDPOINT_API_PROD, $authEndpoint = self::ENDPOINT_AUTH_PROD)
    {
        $this->apiEndpoint = $apiEndpoint;
        $this->authEndpoint = $authEndpoint;
    }

    /**
     * Creates router instance configured for sandbox environment
     *
     * @return Paysera_WalletApi_Util_Router
     */
    public static function createForSandbox()
    {
        return new self(self::ENDPOINT_API_SANDBOX, self::ENDPOINT_AUTH_SANDBOX);
    }

    /**
     * Gets URI to redirect user to confirm transaction
     *
     * @param string $transactionKey
     * @param string $lang
     *
     * @return string
     */
    public function getTransactionConfirmationUri($transactionKey, $lang = null)
    {
        return $this->authEndpoint . $this->getLanguagePrefix($lang) . self::TRANSACTION_PATH . '/' . $transactionKey;
    }

    public function getOAuthEndpoint($lang = null)
    {
        return $this->authEndpoint . $this->getLanguagePrefix($lang) . self::OAUTH_PATH;
    }

    /**
     * Gets URI to redirect to change users password
     *
     * @param int    $userId
     * @param string $lang
     *
     * @return string
     */
    public function getRemindPasswordUri($userId, $lang = null)
    {
        return $this->authEndpoint . $this->getLanguagePrefix($lang) . self::REMIND_PASSWORD_PATH . '/' . $userId;
    }

    /**
     * @param null|string $path
     *
     * @return string
     */
    public function getApiEndpoint($path = null)
    {
        return $this->resolveEndpointPath($this->apiEndpoint, $path);
    }

    /**
     * @param null|string $path
     *
     * @return string
     */
    public function getAuthEndpoint($path = null)
    {
        return $this->resolveEndpointPath($this->authEndpoint, $path);
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

    /**
     * Gets language prefix for URI paths
     *
     * @param string $lang
     *
     * @return string
     */
    protected function getLanguagePrefix($lang = null)
    {
        if (!$lang) {
            return '';
        }
        return '/' . $lang;
    }

    /**
     * @param string $endpoint
     * @param string $path
     *
     * @return string
     */
    private function resolveEndpointPath($endpoint, $path)
    {
        if (substr($path, 0, 7) !== 'http://' && substr($path, 0, 8) !== 'https://') {
            return $endpoint . $path;
        } else {
            return $path;
        }
    }
}
