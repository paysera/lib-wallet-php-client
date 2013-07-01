<?php

class Paysera_WalletApi_OAuth_Consumer
{
    const SCOPE_BALANCE = 'balance';
    const SCOPE_BALANCE_OFFLINE = 'balance_offline';
    const SCOPE_STATEMENTS = 'statements';
    const SCOPE_STATEMENTS_OFFLINE = 'statements_offline';
    const SCOPE_PHONE_CONFIRMATION = 'phone_confirmation';
    const SCOPE_PHONE_CONFIRMATION_OFFLINE = 'phone_confirmation_offline';
    const SCOPE_PHONE = 'phone';
    const SCOPE_PHONE_OFFLINE = 'phone_offline';
    const SCOPE_EMAIL = 'email';
    const SCOPE_EMAIL_OFFLINE = 'email_offline';
    const SCOPE_ADDRESS = 'address';
    const SCOPE_ADDRESS_OFFLINE = 'address_offline';
    const SCOPE_IDENTITY = 'identity';
    const SCOPE_IDENTITY_OFFLINE = 'identity_offline';
    const SCOPE_FULL_NAME = 'full_name';
    const SCOPE_FULL_NAME_OFFLINE = 'full_name_offline';
    const SCOPE_WALLET_LIST = 'wallet_list';
    const SCOPE_WALLET_LIST_OFFLINE = 'wallet_list_offline';

    /**
     * @var array all query parameters which can be used in OAuth authentication
     */
    protected static $authenticationParameters = array(
        'error',
        'error_description',
        'state',
        'code',
    );

    /**
     * @var Paysera_WalletApi_State_StatePersisterInterface
     */
    protected $statePersister;

    /**
     * @var string
     */
    protected $authPath;

    /**
     * @var string
     */
    protected $clientId;

    /**
     * @var Paysera_WalletApi_Util_RequestInfo
     */
    protected $requestInfo;

    /**
     * Constructs object
     *
     * @param string                                    $clientId
     * @param string                                    $authPath
     * @param Paysera_WalletApi_State_StatePersisterInterface $statePersister
     * @param Paysera_WalletApi_Util_RequestInfo              $requestInfo
     */
    public function __construct(
        $clientId,
        $authPath,
        Paysera_WalletApi_State_StatePersisterInterface $statePersister,
        Paysera_WalletApi_Util_RequestInfo $requestInfo
    ) {
        $this->clientId = $clientId;
        $this->authPath = $authPath;
        $this->statePersister = $statePersister;
        $this->requestInfo = $requestInfo;
    }

    public function getOAuthParameters(array $scopes, $redirectUri)
    {
        return array(
            'response_type' => 'code',
            'client_id' => $this->clientId,
            'scope' => implode(' ', $scopes),
            'redirect_uri' => $redirectUri,
            'state' => $this->createState(),
        );
    }

    public function getAuthorizationUri(array $scopes = array(), $redirectUri)
    {
        $query = http_build_query($this->getOAuthParameters($scopes, $redirectUri), null, '&');
        return $this->authPath . '?' . $query;
    }

    public function getAuthorisationUriForKey($key)
    {
        return $this->authPath . '/' . $key;
    }

    public function getOAuthCode(array $params)
    {
        if (!empty($params['code']) || !empty($params['error'])) {
            $currentState = $this->getState();
            $givenState = !empty($params['state']) ? $params['state'] : '';
            if ($currentState !== $givenState) {
                throw new Paysera_WalletApi_Exception_OAuthException(
                    'Invalid state parameter passed in OAuth authentication',
                    'invalid_state'
                );
            }

            if (!empty($params['error'])) {
                $message = 'Error in authentication: ' . $params['error'];
                $message .= !empty($params['error_description']) ? '. ' . $params['error_description'] : '';

                throw new Paysera_WalletApi_Exception_OAuthException($message, $params['error']);
            } else {
                return $params['code'];
            }
        } else {
            return null;
        }
    }

    /**
     * Gets current URI without authentication parameters. Used for getting most probable redirect URI used in
     * authentication request
     *
     * @return string
     */
    public function getCurrentUri()
    {
        return $this->requestInfo->getCurrentUri(self::$authenticationParameters);
    }

    protected function getState()
    {
        return $this->statePersister->getParameter('oauth-state', null);
    }

    protected function createState()
    {
        $state = $this->generateRandomString();
        $this->statePersister->saveParameter('oauth-state', $state);
        return $state;
    }

    protected function generateRandomString()
    {
        $length = 16;
        $charset = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $str = '';
        $count = strlen($charset);
        while ($length--) {
            $str .= $charset[mt_rand(0, $count - 1)];
        }
        return $str;
    }

}