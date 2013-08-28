<?php

class Paysera_WalletApi
{
    /**
     * @var Paysera_WalletApi_Container
     */
    protected $container;

    /**
     * Constructs object
     *
     * @param Paysera_WalletApi_Container $container custom container for services and parameters
     */
    public function __construct(Paysera_WalletApi_Container $container)
    {
        $this->container = $container;
    }

    /**
     * Makes signed request to API and formats the result. Used by other methods, which only maps arrays or other
     * structures used in API to and from entities
     *
     * @param string $uri        Wallet API uri from version field; ie: "payment/123"
     * @param string $method     One of Paysera_WalletApi_Http_Request::METHOD_* constants
     * @param mixed  $content    Content to send in request body. If it's not string, it is encoded in JSON
     *
     * @return mixed             Decoded response body; usually an array
     *
     * @throws Paysera_WalletApi_Exception_ResponseException    if status code of response is not 200
     */
    public function makeRequest($uri, $method = Paysera_WalletApi_Http_Request::METHOD_GET, $content = null)
    {
        $content = $content === null ? null : json_encode($content);
        return $this->container->getRequestMaker()->makeRequest($uri, $method, $content);
    }

    /**
     * Handles callback.
     *
     * @param array                             $post            POST data from callback
     * @param Paysera_WalletApi_Event_EventDispatcher $eventDispatcher handles callback events
     *
     * @throws Paysera_WalletApi_Exception_CallbackException
     * @throws Paysera_WalletApi_Exception_ConfigurationException
     */
    public function handleCallback($post, Paysera_WalletApi_Event_EventDispatcher $eventDispatcher)
    {
        $this->container->getCallbackHandler()->handle($post, $eventDispatcher);
    }

    /**
     * Exchanges authorization code for access token. Use this method only if you make custom "code" parameter handling.
     * Use getOAuthAccessToken method instead for usual uses.
     *
     * @param string $authorizationCode
     * @param string $redirectUri
     *
     * @return Paysera_WalletApi_Entity_MacAccessToken
     *
     * @throws Paysera_WalletApi_Exception_ApiException
     *
     * @see Paysera_WalletApi::getOAuthAccessToken
     */
    public function exchangeCodeForAccessToken($authorizationCode, $redirectUri = null)
    {
        $parameters = array(
            'grant_type' => 'authorization_code',
            'code' => $authorizationCode,
        );
        if ($redirectUri !== null) {
            $parameters['redirect_uri'] = $redirectUri;
        }

        $responseData = $this->container->getOAuthRequestMaker()->makeRequest(
            'token',
            Paysera_WalletApi_Http_Request::METHOD_POST,
            $parameters
        );
        return $this->container->getMapper()->decodeAccessToken($responseData);
    }

    /**
     * Exchanges resource owner password credentials for access token.
     * This method is only for Resource Owner Password Credentials Grant, which is disabled for most clients by default.
     * Use Authorization Code Grant by getAuthorizationUri and getOAuthAccessToken methods if available.
     *
     * @param string $username
     * @param string $password
     * @param array  $scopes   can contain Paysera_WalletApi_OAuth_Consumer::SCOPE_* constants
     *
     * @return Paysera_WalletApi_Entity_MacAccessToken
     *
     * @throws Paysera_WalletApi_Exception_ApiException
     */
    public function exchangePasswordForAccessToken($username, $password, array $scopes = array())
    {
        Paysera_WalletApi_Util_Assert::isScalar($username);
        Paysera_WalletApi_Util_Assert::isScalar($password);
        $parameters = array(
            'grant_type' => 'password',
            'username' => $username,
            'password' => $password,
            'scope' => implode(' ', $scopes),
        );

        $responseData = $this->container->getOAuthRequestMaker()->makeRequest(
            'token',
            Paysera_WalletApi_Http_Request::METHOD_POST,
            $parameters
        );
        return $this->container->getMapper()->decodeAccessToken($responseData);
    }

    /**
     * Exchanges resource owner password credentials for access token.
     * This method is only for Resource Owner Password Credentials Grant, which is disabled for most clients by default.
     * Use Authorization Code Grant by getAuthorizationUri and getOAuthAccessToken methods if available.
     *
     * @param string     $refreshToken
     * @param array|null $scopes       can contain Paysera_WalletApi_OAuth_Consumer::SCOPE_* constants
     *
     * @return Paysera_WalletApi_Entity_MacAccessToken
     *
     * @throws Paysera_WalletApi_Exception_ApiException
     */
    public function refreshAccessToken($refreshToken, $scopes = null)
    {
        Paysera_WalletApi_Util_Assert::isScalar($refreshToken);
        $parameters = array(
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken,
        );
        if ($scopes !== null) {
            $parameters['scope'] = implode(' ', $scopes);
        }

        $responseData = $this->container->getOAuthRequestMaker()->makeRequest(
            'token',
            Paysera_WalletApi_Http_Request::METHOD_POST,
            $parameters
        );
        return $this->container->getMapper()->decodeAccessToken($responseData);
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
        $consumer = $this->container->getOAuthConsumer();
        if ($redirectUri === null) {
            $redirectUri = $consumer->getCurrentUri();
        }
        if ($userInformation === null) {
            return $consumer->getAuthorizationUri($scopes, $redirectUri);
        } else {
            $parameters = $consumer->getOAuthParameters($scopes, $redirectUri);
            $parameters['user'] = $this->container->getMapper()->encodeUserInformation($userInformation);

            $responseData = $this->container->getOAuthRequestMaker()->makeRequest(
                'session',
                Paysera_WalletApi_Http_Request::METHOD_POST,
                json_encode($parameters)
            );
            return $consumer->getAuthorisationUriForKey($responseData['key']);
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
        $consumer = $this->container->getOAuthConsumer();
        $authorizationCode = $consumer->getOAuthCode($params);
        if ($authorizationCode === null) {
            return null;
        }

        if ($redirectUri === null) {
            $redirectUri = $consumer->getCurrentUri();
        }
        return $this->exchangeCodeForAccessToken($authorizationCode, $redirectUri);
    }

    /**
     * Creates API object, related to current wallet (wallet of the user which granted access, giving the access token)
     *
     * @param Paysera_WalletApi_Entity_MacAccessToken $accessToken
     *
     * @return Paysera_WalletApi_WalletRelatedApi
     */
    public function createApiForToken(Paysera_WalletApi_Entity_MacAccessToken $accessToken)
    {
        return $this->container->createApiForToken($this, $accessToken);
    }

    /**
     * Creates payment using API
     *
     * @param Paysera_WalletApi_Entity_Payment $payment
     *
     * @return Paysera_WalletApi_Entity_Payment
     *
     * @throws Paysera_WalletApi_Exception_ApiException
     */
    public function createPayment(Paysera_WalletApi_Entity_Payment $payment)
    {
        $mapper = $this->container->getMapper();
        $requestData = $mapper->encodePayment($payment);
        $responseData = $this->makeRequest('payment', Paysera_WalletApi_Http_Request::METHOD_POST, $requestData);
        return $mapper->decodePayment($responseData);
    }

    /**
     * Gets payment by ID using API
     *
     * @param integer $paymentId
     *
     * @return Paysera_WalletApi_Entity_Payment
     *
     * @throws Paysera_WalletApi_Exception_ApiException
     */
    public function getPayment($paymentId)
    {
        Paysera_WalletApi_Util_Assert::isInt($paymentId);
        $responseData = $this->makeRequest('payment/' . $paymentId);
        return $this->container->getMapper()->decodePayment($responseData);
    }

    /**
     * Cancels payment by ID using API
     *
     * @param integer $paymentId
     *
     * @return Paysera_WalletApi_Entity_Payment
     *
     * @throws Paysera_WalletApi_Exception_ApiException
     */
    public function cancelPayment($paymentId)
    {
        Paysera_WalletApi_Util_Assert::isInt($paymentId);
        $responseData = $this->makeRequest('payment/' . $paymentId, Paysera_WalletApi_Http_Request::METHOD_DELETE);
        return $this->container->getMapper()->decodePayment($responseData);
    }

    /**
     * Removes freeze period for payment
     *
     * @param integer $paymentId
     *
     * @return Paysera_WalletApi_Entity_Payment
     *
     * @throws Paysera_WalletApi_Exception_ApiException
     */
    public function removeFreezePeriod($paymentId)
    {
        Paysera_WalletApi_Util_Assert::isInt($paymentId);
        $responseData = $this->makeRequest(
            'payment/' . $paymentId . '/freeze',
            Paysera_WalletApi_Http_Request::METHOD_PUT,
            array('freeze_until' => 0)
        );
        return $this->container->getMapper()->decodePayment($responseData);
    }

    /**
     * Extends freeze period for payment for specified amount of hours
     *
     * @param integer $paymentId
     * @param integer $periodInHours
     *
     * @return Paysera_WalletApi_Entity_Payment
     *
     * @throws Paysera_WalletApi_Exception_ApiException
     */
    public function extendFreezePeriod($paymentId, $periodInHours)
    {
        Paysera_WalletApi_Util_Assert::isInt($paymentId);
        Paysera_WalletApi_Util_Assert::isInt($periodInHours);
        $responseData = $this->makeRequest(
            'payment/' . $paymentId . '/freeze',
            Paysera_WalletApi_Http_Request::METHOD_PUT,
            array('freeze_for' => $periodInHours)
        );
        return $this->container->getMapper()->decodePayment($responseData);
    }

    /**
     * Extends freeze period for payment for specified amount of hours
     *
     * @param integer  $paymentId
     * @param DateTime $freezeUntil
     *
     * @return Paysera_WalletApi_Entity_Payment
     *
     * @throws Paysera_WalletApi_Exception_ApiException
     */
    public function changeFreezePeriod($paymentId, DateTime $freezeUntil)
    {
        Paysera_WalletApi_Util_Assert::isInt($paymentId);
        $responseData = $this->makeRequest(
            'payment/' . $paymentId . '/freeze',
            Paysera_WalletApi_Http_Request::METHOD_PUT,
            array('freeze_until' => $freezeUntil->getTimestamp())
        );
        return $this->container->getMapper()->decodePayment($responseData);
    }

    /**
     * Finalizes payment, optionally changing the final price
     *
     * @param integer                  $paymentId
     * @param Paysera_WalletApi_Entity_Money $finalPrice
     *
     * @return Paysera_WalletApi_Entity_Payment
     *
     * @throws Paysera_WalletApi_Exception_ApiException
     */
    public function finalizePayment($paymentId, Paysera_WalletApi_Entity_Money $finalPrice = null)
    {
        Paysera_WalletApi_Util_Assert::isInt($paymentId);
        $mapper = $this->container->getMapper();

        $responseData = $this->makeRequest(
            'payment/' . $paymentId . '/finalize',
            Paysera_WalletApi_Http_Request::METHOD_PUT,
            $finalPrice === null ? null : $mapper->encodePrice($finalPrice)
        );
        return $mapper->decodePayment($responseData);
    }


    /**
     * Finds payments by provided parameters
     *
     * @param string  $status
     * @param integer $walletId
     * @param integer $beneficiaryId
     *
     * @return integer[]        ID list of found payments
     *
     * @throws Paysera_WalletApi_Exception_ApiException
     */
    public function findPayments($status = null, $walletId = null, $beneficiaryId = null)
    {
        Paysera_WalletApi_Util_Assert::isIntOrNull($walletId);
        Paysera_WalletApi_Util_Assert::isIntOrNull($beneficiaryId);
        $params = array();
        if ($status !== null) {
            $params['status'] = $status;
        }
        if ($walletId !== null) {
            $params['wallet'] = $walletId;
        }
        if ($beneficiaryId !== null) {
            $params['beneficiary'] = $beneficiaryId;
        }
        return $this->makeRequest('payments/id' . (count($params) > 0 ? '?' . http_build_query($params) : ''));
    }

    /**
     * Creates allowance using API
     *
     * @param Paysera_WalletApi_Entity_Allowance $allowance
     *
     * @return Paysera_WalletApi_Entity_Allowance
     *
     * @throws Paysera_WalletApi_Exception_ApiException
     */
    public function createAllowance(Paysera_WalletApi_Entity_Allowance $allowance)
    {
        $mapper = $this->container->getMapper();
        $requestData = $mapper->encodeAllowance($allowance);
        $responseData = $this->makeRequest('allowance', Paysera_WalletApi_Http_Request::METHOD_POST, $requestData);
        return $mapper->decodeAllowance($responseData);
    }

    /**
     * Gets allowance by ID using API
     *
     * @param integer $allowanceId
     *
     * @return Paysera_WalletApi_Entity_Allowance
     *
     * @throws Paysera_WalletApi_Exception_ApiException
     */
    public function getAllowance($allowanceId)
    {
        Paysera_WalletApi_Util_Assert::isInt($allowanceId);
        $responseData = $this->makeRequest('allowance/' . $allowanceId);
        return $this->container->getMapper()->decodeAllowance($responseData);
    }

    /**
     * Gets active allowance for specified wallet using API
     *
     * @param integer $walletId
     *
     * @return Paysera_WalletApi_Entity_Allowance
     *
     * @throws Paysera_WalletApi_Exception_ApiException
     */
    public function getAllowanceForWallet($walletId)
    {
        Paysera_WalletApi_Util_Assert::isInt($walletId);
        $responseData = $this->makeRequest('allowance/active/' . $walletId);
        return $this->container->getMapper()->decodeAllowance($responseData);
    }

    /**
     * Gets current allowance limit for specified wallet using API
     *
     * @param integer $walletId
     * @param string  $currency
     *
     * @return Paysera_WalletApi_Entity_Money
     */
    public function getAllowanceLimit($walletId, $currency = 'EUR')
    {
        Paysera_WalletApi_Util_Assert::isInt($walletId);
        Paysera_WalletApi_Util_Assert::isScalar($currency);
        $responseData = $this->makeRequest('allowance/limit/' . $walletId . '?currency=' . urlencode($currency));
        return $this->container->getMapper()->decodeMoney($responseData);
    }

    /**
     * Cancels allowance by ID using API
     *
     * @param integer $allowanceId
     *
     * @return Paysera_WalletApi_Entity_Allowance
     *
     * @throws Paysera_WalletApi_Exception_ApiException
     */
    public function cancelAllowance($allowanceId)
    {
        Paysera_WalletApi_Util_Assert::isInt($allowanceId);
        $responseData = $this->makeRequest('allowance/' . $allowanceId, Paysera_WalletApi_Http_Request::METHOD_DELETE);
        return $this->container->getMapper()->decodeAllowance($responseData);
    }

    /**
     * Cancels allowance for specified wallet using API
     *
     * @param integer $walletId
     *
     * @return Paysera_WalletApi_Entity_Allowance
     *
     * @throws Paysera_WalletApi_Exception_ApiException
     */
    public function cancelAllowanceForWallet($walletId)
    {
        Paysera_WalletApi_Util_Assert::isInt($walletId);
        $responseData = $this->makeRequest('allowance/active/' . $walletId, Paysera_WalletApi_Http_Request::METHOD_DELETE);
        return $this->container->getMapper()->decodeAllowance($responseData);
    }

    /**
     * Creates transaction using API
     *
     * @param Paysera_WalletApi_Entity_Transaction $transaction
     *
     * @return Paysera_WalletApi_Entity_Transaction
     *
     * @throws Paysera_WalletApi_Exception_ApiException
     */
    public function createTransaction(Paysera_WalletApi_Entity_Transaction $transaction)
    {
        $mapper = $this->container->getMapper();
        $requestData = $mapper->encodeTransaction($transaction);
        $responseData = $this->makeRequest('transaction', Paysera_WalletApi_Http_Request::METHOD_POST, $requestData);
        return $mapper->decodeTransaction($responseData);
    }

    /**
     * Gets transaction by transaction key using API
     *
     * @param string $transactionKey
     *
     * @return Paysera_WalletApi_Entity_Transaction
     *
     * @throws Paysera_WalletApi_Exception_ApiException
     */
    public function getTransaction($transactionKey)
    {
        Paysera_WalletApi_Util_Assert::isScalar($transactionKey);
        $responseData = $this->makeRequest('transaction/' . $transactionKey);
        return $this->container->getMapper()->decodeTransaction($responseData);
    }

    /**
     * Revokes transaction by transaction key using API
     *
     * @param string $transactionKey
     *
     * @return Paysera_WalletApi_Entity_Transaction
     *
     * @throws Paysera_WalletApi_Exception_ApiException
     */
    public function revokeTransaction($transactionKey)
    {
        Paysera_WalletApi_Util_Assert::isScalar($transactionKey);
        $responseData = $this->makeRequest('transaction/' . $transactionKey, Paysera_WalletApi_Http_Request::METHOD_DELETE);
        return $this->container->getMapper()->decodeTransaction($responseData);
    }

    /**
     * Confirms transaction by transaction key using API
     *
     * @param string $transactionKey
     *
     * @return Paysera_WalletApi_Entity_Transaction
     *
     * @throws Paysera_WalletApi_Exception_ApiException
     */
    public function confirmTransaction($transactionKey)
    {
        Paysera_WalletApi_Util_Assert::isScalar($transactionKey);
        $responseData = $this->makeRequest(
            'transaction/' . $transactionKey . '/confirm',
            Paysera_WalletApi_Http_Request::METHOD_PUT
        );
        return $this->container->getMapper()->decodeTransaction($responseData);
    }

    /**
     * Tries to accept transaction by active allowance using API
     *
     * @param string  $transactionKey
     * @param int|Paysera_WalletApi_Entity_WalletIdentifier $payer
     *
     * @return Paysera_WalletApi_Entity_Transaction
     *
     * @throws Paysera_WalletApi_Exception_ApiException
     */
    public function acceptTransactionUsingAllowance($transactionKey, $payer)
    {
        Paysera_WalletApi_Util_Assert::isScalar($transactionKey);

        $content = null;
        if ($payer instanceof Paysera_WalletApi_Entity_WalletIdentifier) {
            $payer->validate();

            $mapper = $this->container->getMapper();

            $content = $mapper->encodePayer($payer);
            $uri = 'transaction/' . $transactionKey . '/reserve';
        } else {
            Paysera_WalletApi_Util_Assert::isInt($payer);

            $uri = 'transaction/' . $transactionKey . '/reserve/' . $payer;
        }

        $responseData = $this->makeRequest(
            $uri,
            Paysera_WalletApi_Http_Request::METHOD_PUT,
            $content
        );

        return $this->container->getMapper()->decodeTransaction($responseData);
    }

    /**
     * Tries to accept transaction by sending user's PIN code using API
     *
     * @param string  $transactionKey
     * @param int|Paysera_WalletApi_Entity_WalletIdentifier $payer
     * @param string  $pin
     *
     * @return Paysera_WalletApi_Entity_Transaction
     *
     * @throws Paysera_WalletApi_Exception_ApiException
     */
    public function acceptTransactionUsingPin($transactionKey, $payer, $pin)
    {
        Paysera_WalletApi_Util_Assert::isScalar($transactionKey);
        Paysera_WalletApi_Util_Assert::isScalar($pin);

        $mapper = $this->container->getMapper();
        $content = $mapper->encodePin($pin);

        if ($payer instanceof Paysera_WalletApi_Entity_WalletIdentifier) {
            $payer->validate();

            $content = array_merge($content, $mapper->encodePayer($payer));
            $uri = 'transaction/' . $transactionKey . '/reserve';
        } else {
            Paysera_WalletApi_Util_Assert::isInt($payer);

            $uri = 'transaction/' . $transactionKey . '/reserve/' . $payer;
        }

        $responseData = $this->makeRequest(
            $uri,
            Paysera_WalletApi_Http_Request::METHOD_PUT,
            $content
        );
        return $mapper->decodeTransaction($responseData);
    }

    /**
     * Sends FLASH SMS using API to the user to accept transaction
     *
     * @param string  $transactionKey
     * @param integer $walletId
     *
     * @return Paysera_WalletApi_Entity_Transaction
     *
     * @throws Paysera_WalletApi_Exception_ApiException
     */
    public function sendTransactionFlashSms($transactionKey, $walletId)
    {
        Paysera_WalletApi_Util_Assert::isScalar($transactionKey);
        Paysera_WalletApi_Util_Assert::isInt($walletId);
        $responseData = $this->makeRequest(
            'transaction/' . $transactionKey . '/flash/' . $walletId,
            Paysera_WalletApi_Http_Request::METHOD_PUT
        );
        return $this->container->getMapper()->decodeTransaction($responseData);
    }

    /**
     * Gets available types to accept transaction using API
     *
     * @param string  $transactionKey
     * @param integer $walletId
     *
     * @return string[]
     *
     * @throws Paysera_WalletApi_Exception_ApiException
     */
    public function getAvailableTransactionTypes($transactionKey, $walletId)
    {
        Paysera_WalletApi_Util_Assert::isScalar($transactionKey);
        Paysera_WalletApi_Util_Assert::isInt($walletId);
        return $this->makeRequest('transaction/' . $transactionKey . '/type/' . $walletId);
    }

    /**
     * Gets wallet by id using API
     *
     * @param integer $walletId
     *
     * @return Paysera_WalletApi_Entity_Wallet
     *
     * @throws Paysera_WalletApi_Exception_ApiException
     */
    public function getWallet($walletId)
    {
        Paysera_WalletApi_Util_Assert::isInt($walletId);
        $responseData = $this->makeRequest('wallet/' . $walletId);
        return $this->container->getMapper()->decodeWallet($responseData);
    }

    /**
     * Gets wallet balance by id using API
     *
     * @param integer $walletId
     *
     * @return Paysera_WalletApi_Entity_Wallet_Balance
     *
     * @throws Paysera_WalletApi_Exception_ApiException
     */
    public function getWalletBalance($walletId)
    {
        Paysera_WalletApi_Util_Assert::isInt($walletId);
        $responseData = $this->makeRequest('wallet/' . $walletId . '/balance');
        return $this->container->getMapper()->decodeWalletBalance($responseData);
    }

    /**
     * Gets statements for wallet by id using API
     *
     * @param integer                                   $walletId
     * @param Paysera_WalletApi_Entity_Statement_SearchFilter $filter
     *
     * @return Paysera_WalletApi_Entity_Statement_SearchResult|Paysera_WalletApi_Entity_Statement[]
     *
     * @throws Paysera_WalletApi_Exception_ApiException
     */
    public function getWalletStatements($walletId, Paysera_WalletApi_Entity_Statement_SearchFilter $filter = null)
    {
        Paysera_WalletApi_Util_Assert::isInt($walletId);
        $mapper = $this->container->getMapper();
        if ($filter !== null) {
            $query = '?' . http_build_query($mapper->encodeStatementFilter($filter), null, '&');
        } else {
            $query = '';
        }
        return $mapper->decodeStatementSearchResult(
            $this->makeRequest('wallet/' . $walletId . '/statements' . $query)
        );
    }

    /**
     * Gets wallet by search parameters
     *
     * @param array $parameters
     *
     * @return Paysera_WalletApi_Entity_Wallet
     *
     * @throws Paysera_WalletApi_Exception_ApiException
     */
    public function getWalletBy(array $parameters)
    {
        $responseData = $this->makeRequest('wallet?' . http_build_query($parameters, null, '&'));
        return $this->container->getMapper()->decodeWallet($responseData);
    }

    /**
     * Gets wallets by contact list (emails or phone numbers)
     *
     * @param array $contacts
     * @param bool  $private  whether to send hashes of contacts to avoid sending private information
     *
     * @return Paysera_WalletApi_Entity_Wallet[] array keys are provided contacts (only the found ones are provided)
     */
    public function getWalletsByContacts(array $contacts, $private = false)
    {
        if (count($contacts) === 0) {
            return array();
        }

        $map = array();
        $email = array();
        $phone = array();
        foreach ($contacts as $contact) {
            if (strpos($contact, '@') !== false) {
                $formatted = strtolower($contact);
                if ($private) {
                    $formatted = sha1($formatted);
                }
                $email[] = $formatted;
            } else {
                $formatted = preg_replace('/[^\d]/', '', $contact);
                if ($private) {
                    $formatted = sha1($formatted);
                }
                $phone[] = $formatted;
            }
            $map[$formatted] = $contact;
        }
        $parameters = array();
        if (count($email) > 0) {
            $parameters[$private ? 'email_hash' : 'email'] = implode(',', $email);
        }
        if (count($phone) > 0) {
            $parameters[$private ? 'phone_hash' : 'phone'] = implode(',', $phone);
        }
        $responseData = $this->makeRequest('wallets?' . http_build_query($parameters, null, '&'));

        $result = array();
        $mapper = $this->container->getMapper();
        foreach ($responseData as $key => $walletData) {
            $result[$map[$key]] = $mapper->decodeWallet($walletData);
        }
        return $result;
    }

    /**
     * Gets user by id using API
     *
     * @param integer $userId
     *
     * @return Paysera_WalletApi_Entity_User
     *
     * @throws Paysera_WalletApi_Exception_ApiException
     */
    public function getUser($userId)
    {
        Paysera_WalletApi_Util_Assert::isInt($userId);
        $responseData = $this->makeRequest('user/' . $userId);
        return $this->container->getMapper()->decodeUser($responseData);
    }

    /**
     * Gets user's email by id using API
     *
     * @param integer $userId
     *
     * @return string
     *
     * @throws Paysera_WalletApi_Exception_ApiException
     */
    public function getUserEmail($userId)
    {
        Paysera_WalletApi_Util_Assert::isInt($userId);
        return $this->makeRequest('user/' . $userId . '/email');
    }

    /**
     * Gets user's phone by id using API
     *
     * @param integer $userId
     *
     * @return string
     *
     * @throws Paysera_WalletApi_Exception_ApiException
     */
    public function getUserPhone($userId)
    {
        Paysera_WalletApi_Util_Assert::isInt($userId);
        return $this->makeRequest('user/' . $userId . '/phone');
    }

    /**
     * Gets user's address by id using API
     *
     * @param integer $userId
     *
     * @return Paysera_WalletApi_Entity_User_Address
     *
     * @throws Paysera_WalletApi_Exception_ApiException
     */
    public function getUserAddress($userId)
    {
        Paysera_WalletApi_Util_Assert::isInt($userId);
        $responseData = $this->makeRequest('user/' . $userId . '/address');
        return $this->container->getMapper()->decodeAddress($responseData);
    }

    /**
     * Gets user's identity by id using API
     *
     * @param integer $userId
     *
     * @return Paysera_WalletApi_Entity_User_Identity
     *
     * @throws Paysera_WalletApi_Exception_ApiException
     */
    public function getUserIdentity($userId)
    {
        Paysera_WalletApi_Util_Assert::isInt($userId);
        $responseData = $this->makeRequest('user/' . $userId . '/identity');
        return $this->container->getMapper()->decodeIdentity($responseData);
    }

    /**
     * Gets user's wallets by id using API
     *
     * @param integer $userId
     *
     * @return Paysera_WalletApi_Entity_Wallet[]
     *
     * @throws Paysera_WalletApi_Exception_ApiException
     */
    public function getUserWallets($userId)
    {
        Paysera_WalletApi_Util_Assert::isInt($userId);
        $responseData = $this->makeRequest('user/' . $userId . '/wallets');
        return $this->container->getMapper()->decodeWallets($responseData);
    }

    /**
     * Get URI for user redirection to confirm the transaction
     *
     * @param string $transactionKey
     * @param string $lang
     *
     * @return string
     */
    public function getConfirmationUri($transactionKey, $lang = 'en')
    {
        return $this->container->getConfirmPath($lang) . $transactionKey;
    }
}