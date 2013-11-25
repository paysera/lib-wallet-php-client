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
    const SCOPE_DOB = 'dob';
    const SCOPE_DOB_OFFLINE = 'dob_offline';
    const SCOPE_GENDER = 'gender';
    const SCOPE_GENDER_OFFLINE = 'gender_offline';

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
     * @var Paysera_WalletApi_Util_Router
     */
    protected $router;

    /**
     * @var string
     */
    protected $clientId;

    /**
     * @var Paysera_WalletApi_Util_RequestInfo
     */
    protected $requestInfo;

    /**
     * @var Paysera_WalletApi_Client_OAuthClient
     */
    protected $oauthClient;

    /**
     * Constructs object
     *
     * @param string                                          $clientId
     * @param Paysera_WalletApi_Client_OAuthClient            $oauthClient
     * @param Paysera_WalletApi_Util_Router                   $router
     * @param Paysera_WalletApi_State_StatePersisterInterface $statePersister
     * @param Paysera_WalletApi_Util_RequestInfo              $requestInfo
     */
    public function __construct(
        $clientId,
        Paysera_WalletApi_Client_OAuthClient $oauthClient,
        Paysera_WalletApi_Util_Router $router,
        Paysera_WalletApi_State_StatePersisterInterface $statePersister,
        Paysera_WalletApi_Util_RequestInfo $requestInfo
    ) {
        $this->clientId = $clientId;
        $this->oauthClient = $oauthClient;
        $this->router = $router;
        $this->statePersister = $statePersister;
        $this->requestInfo = $requestInfo;
    }

    /**
     * Gets redirect URI for OAuth authorization. After confirming or rejecting authorization request, user will
     * be redirected to redirect URI.
     *
     * @param array                              $scopes          can contain Paysera_WalletApi_OAuth_Consumer::SCOPE_* constants
     * @param string                             $redirectUri     takes current URI without authorization parameters if not passed
     * @param Paysera_WalletApi_Entity_UserInformation $userInformation if passed, creates OAuth session by API with confirmed user's information
     *
     * @return string
     *
     * @throws Paysera_WalletApi_Exception_OAuthException
     * @throws Paysera_WalletApi_Exception_ApiException
     */
    public function getAuthorizationUri(
        array $scopes = array(),
        $redirectUri = null,
        Paysera_WalletApi_Entity_UserInformation $userInformation = null
    ) {
        if ($redirectUri === null) {
            $redirectUri = $this->getCurrentUri();
        }
        if ($userInformation === null) {
            $query = http_build_query($this->getOAuthParameters($scopes, $redirectUri), null, '&');
            return $this->router->getOAuthEndpoint() . '?' . $query;
        } else {
            $parameters = $this->getOAuthParameters($scopes, $redirectUri);
            $responseData = $this->oauthClient->createSession($parameters, $userInformation);
            return $this->router->getOAuthEndpoint() . '/' . $responseData['key'];
        }
    }

    /**
     * Gets OAuth access token from query parameters. Redirect URI must be the same as passed when getting the
     * authorization URI, otherwise authorization will fail
     * If no authorization parameters are passed, returns null
     * If authorization error is passed or some data is invalid (like state parameter), exception is thrown
     *
     * @param array  $params      takes $_GET if not passed
     * @param string $redirectUri takes current URI without authorization parameters if not passed
     *
     * @return Paysera_WalletApi_Entity_MacAccessToken|null
     *
     * @throws Paysera_WalletApi_Exception_OAuthException
     * @throws Paysera_WalletApi_Exception_ApiException
     */
    public function getOAuthAccessToken($params = null, $redirectUri = null)
    {
        if ($params === null) {
            $params = $_GET;
        }
        $authorizationCode = $this->getOAuthCode($params);
        if ($authorizationCode === null) {
            return null;
        }

        if ($redirectUri === null) {
            $redirectUri = $this->getCurrentUri();
        }
        return $this->oauthClient->exchangeCodeForAccessToken($authorizationCode, $redirectUri);
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
     * Gets password change/reset URI for specific user
     *
     * @param int    $userId
     * @param string $lang
     * @param string $redirectUri
     *
     * @return string
     */
    public function getResetPasswordUri($userId, $lang = null, $redirectUri = null)
    {
        if ($redirectUri === null) {
            $redirectUri = $this->getCurrentUri();
        }
        $parameters = array(
            'client_id'    => $this->clientId,
            'redirect_uri' => $redirectUri,
        );
        $query = http_build_query($parameters, null, '&');
        return $this->router->getRemindPasswordUri($userId, $lang) . '?' . $query;
    }

    /**
     * Gets current URI without authentication parameters. Used for getting most probable redirect URI used in
     * authentication request
     *
     * @return string
     */
    protected function getCurrentUri()
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