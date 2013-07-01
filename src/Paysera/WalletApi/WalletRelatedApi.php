<?php

class Paysera_WalletApi_WalletRelatedApi
{
    /**
     * @var Paysera_WalletApi
     */
    protected $walletApi;

    /**
     * @var Paysera_WalletApi_RequestMaker
     */
    protected $requestMaker;

    /**
     * @var Paysera_WalletApi_Mapper
     */
    protected $mapper;

    /**
     * @var Paysera_WalletApi_Entity_MacAccessToken
     */
    protected $currentAccessToken;

    /**
     * Constructs object
     *
     * @param Paysera_WalletApi                       $WalletApi
     * @param Paysera_WalletApi_RequestMaker          $requestMaker
     * @param Paysera_WalletApi_Mapper                $mapper
     * @param Paysera_WalletApi_Entity_MacAccessToken $currentAccessToken
     */
    public function __construct(
        Paysera_WalletApi $WalletApi,
        Paysera_WalletApi_RequestMaker $requestMaker,
        Paysera_WalletApi_Mapper $mapper,
        Paysera_WalletApi_Entity_MacAccessToken $currentAccessToken
    ) {
        $this->walletApi = $WalletApi;
        $this->requestMaker = $requestMaker;
        $this->mapper = $mapper;
        $this->currentAccessToken = $currentAccessToken;
    }

    /**
     * Makes signed request to API and formats the result. Used by other methods, which only maps arrays or other
     * structures used in API to and from entities
     *
     * @param string $uri        Wallet API uri from version field; ie: "wallet/me"
     * @param string $method     One of Paysera_WalletApi_Http_Request::METHOD_* constants
     * @param mixed  $content    Content to send in request body. If it's not string, it is encoded in JSON
     *
     * @throws Exception|Paysera_WalletApi_Exception_ResponseException
     * @return mixed             Decoded response body; usually an array
     */
    public function makeRequest($uri, $method = Paysera_WalletApi_Http_Request::METHOD_GET, $content = null)
    {
        $content = $content === null ? null : json_encode($content);
        try {
            return $this->requestMaker->makeRequest($uri, $method, $content);
        } catch (Paysera_WalletApi_Exception_ResponseException $exception) {
            if ($exception->getErrorCode() === 'invalid_grant') {
                $refreshToken = $this->currentAccessToken->getRefreshToken();
                if ($refreshToken !== null) {
                    $newToken = $this->walletApi->refreshAccessToken($refreshToken);
                    $this->currentAccessToken = $newToken;
                    $signer = new Paysera_WalletApi_Auth_Mac($newToken->getMacId(), $newToken->getMacKey());
                    $this->requestMaker->setSigner($signer);
                    return $this->requestMaker->makeRequest($uri, $method, $content);
                }
            }
            throw $exception;
        }
    }

    /**
     * Gets current access token. It may change if it was automatically refreshed using refresh token
     *
     * @return Paysera_WalletApi_Entity_MacAccessToken
     */
    public function getCurrentAccessToken()
    {
        return $this->currentAccessToken;
    }

    /**
     * Gets active allowance for current wallet
     *
     * @return Paysera_WalletApi_Entity_Allowance
     *
     * @throws Paysera_WalletApi_Exception_ApiException
     */
    public function getActiveAllowance()
    {
        return $this->mapper->decodeAllowance($this->makeRequest('allowance/active/me'));
    }

    /**
     * Tries to accept transaction by active allowance using API
     *
     * @param string  $transactionKey
     *
     * @return Paysera_WalletApi_Entity_Transaction
     *
     * @throws Paysera_WalletApi_Exception_ApiException
     */
    public function acceptTransactionUsingAllowance($transactionKey)
    {
        Paysera_WalletApi_Util_Assert::isScalar($transactionKey);
        $responseData = $this->makeRequest(
            'transaction/' . $transactionKey . '/reserve/me',
            Paysera_WalletApi_Http_Request::METHOD_PUT
        );
        return $this->mapper->decodeTransaction($responseData);
    }

    /**
     * Tries to accept transaction by sending user's PIN code using API
     *
     * @param string  $transactionKey
     * @param string  $pin
     *
     * @return Paysera_WalletApi_Entity_Transaction
     *
     * @throws Paysera_WalletApi_Exception_ApiException
     */
    public function acceptTransactionUsingPin($transactionKey, $pin)
    {
        Paysera_WalletApi_Util_Assert::isScalar($transactionKey);
        Paysera_WalletApi_Util_Assert::isScalar($pin);
        $responseData = $this->makeRequest(
            'transaction/' . $transactionKey . '/reserve/me',
            Paysera_WalletApi_Http_Request::METHOD_PUT,
            $this->mapper->encodePin($pin)
        );
        return $this->mapper->decodeTransaction($responseData);
    }

    /**
     * Sends FLASH SMS using API to the user to accept transaction
     *
     * @param string  $transactionKey
     *
     * @return Paysera_WalletApi_Entity_Transaction
     *
     * @throws Paysera_WalletApi_Exception_ApiException
     */
    public function sendTransactionFlashSms($transactionKey)
    {
        Paysera_WalletApi_Util_Assert::isScalar($transactionKey);
        $responseData = $this->makeRequest(
            'transaction/' . $transactionKey . '/flash/me',
            Paysera_WalletApi_Http_Request::METHOD_PUT
        );
        return $this->mapper->decodeTransaction($responseData);
    }

    /**
     * Gets available types to accept transaction using API
     *
     * @param string  $transactionKey
     *
     * @return string[]
     *
     * @throws Paysera_WalletApi_Exception_ApiException
     */
    public function getAvailableTransactionTypes($transactionKey)
    {
        Paysera_WalletApi_Util_Assert::isScalar($transactionKey);
        return $this->makeRequest('transaction/' . $transactionKey . '/type/me');
    }

    /**
     * Gets current wallet using API
     *
     * @param int $walletId
     *
     * @return Paysera_WalletApi_Entity_Wallet
     *
     * @throws Paysera_WalletApi_Exception_ApiException
     */
    public function getWallet($walletId = null)
    {
        Paysera_WalletApi_Util_Assert::isIntOrNull($walletId);
        return $this->mapper->decodeWallet($this->makeRequest('wallet/' . ($walletId === null ? 'me' : $walletId)));
    }

    /**
     * Gets current wallet balance using API
     *
     * @return Paysera_WalletApi_Entity_Wallet_Balance
     *
     * @throws Paysera_WalletApi_Exception_ApiException
     */
    public function getWalletBalance()
    {
        return $this->mapper->decodeWalletBalance($this->makeRequest('wallet/me/balance'));
    }

    /**
     * Gets statements for current wallet using API
     *
     * @param Paysera_WalletApi_Entity_Statement_SearchFilter $filter
     *
     * @return Paysera_WalletApi_Entity_Statement_SearchResult|Paysera_WalletApi_Entity_Statement[]
     */
    public function getWalletStatements(Paysera_WalletApi_Entity_Statement_SearchFilter $filter = null)
    {
        if ($filter !== null) {
            $query = '?' . http_build_query($this->mapper->encodeStatementFilter($filter), null, '&');
        } else {
            $query = '';
        }
        return $this->mapper->decodeStatementSearchResult($this->makeRequest('wallet/me/statements' . $query));
    }

    /**
     * Gets current user using API
     *
     * @return Paysera_WalletApi_Entity_User
     *
     * @throws Paysera_WalletApi_Exception_ApiException
     */
    public function getUser()
    {
        return $this->mapper->decodeUser($this->makeRequest('user/me'));
    }

    /**
     * Gets current user's email using API
     *
     * @return string
     *
     * @throws Paysera_WalletApi_Exception_ApiException
     */
    public function getUserEmail()
    {
        return $this->makeRequest('user/me/email');
    }

    /**
     * Gets current user's phone using API
     *
     * @return string
     *
     * @throws Paysera_WalletApi_Exception_ApiException
     */
    public function getUserPhone()
    {
        return $this->makeRequest('user/me/phone');
    }

    /**
     * Gets current user's address using API
     *
     * @return Paysera_WalletApi_Entity_User_Address
     *
     * @throws Paysera_WalletApi_Exception_ApiException
     */
    public function getUserAddress()
    {
        return $this->mapper->decodeAddress($this->makeRequest('user/me/address'));
    }

    /**
     * Gets current user's identity using API
     *
     * @return Paysera_WalletApi_Entity_User_Identity
     *
     * @throws Paysera_WalletApi_Exception_ApiException
     */
    public function getUserIdentity()
    {
        return $this->mapper->decodeIdentity($this->makeRequest('user/me/identity'));
    }

    /**
     * Gets current user's wallets using API
     *
     * @return Paysera_WalletApi_Entity_Wallet[]
     *
     * @throws Paysera_WalletApi_Exception_ApiException
     */
    public function getUserWallets()
    {
        return $this->mapper->decodeWallets($this->makeRequest('user/me/wallets'));
    }
}