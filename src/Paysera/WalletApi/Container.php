<?php

/**
 * Service and parameter container class.
 * Only creates custom services, used in this API. Contains default creation login inside.
 * Creates services only when needed, not on initialization
 */
class Paysera_WalletApi_Container
{
    /**
     * @var string
     */
    protected $clientId;

    /**
     * @var string
     */
    protected $macSecret;

    /**
     * @var Paysera_WalletApi_Http_ClientCertificate
     */
    protected $clientCertificate;

    /**
     * @var string
     */
    protected $apiEndpoint;

    /**
     * @var string
     */
    protected $authEndpoint;

    /**
     * @var Paysera_WalletApi_RequestMaker
     */
    protected $requestMaker;

    /**
     * @var Paysera_WalletApi_RequestMaker
     */
    protected $oAuthRequestMaker;

    /**
     * @var Paysera_WalletApi_CallbackHandler
     */
    protected $callbackHandler;

    /**
     * @var Paysera_WalletApi_Mapper
     */
    protected $mapper;

    /**
     * @var Paysera_WalletApi_OAuth_Consumer
     */
    protected $oAuthConsumer;

    /**
     * @var Paysera_WalletApi_WebClient_Interface
     */
    protected $webClient;

    /**
     * @var Paysera_WalletApi_Auth_SignerInterface
     */
    protected $authSigner;

    /**
     * Creates new instance of this object. Used for fluent interface
     *
     * @param string                                    $clientId
     * @param string|Paysera_WalletApi_Http_ClientCertificate $authentication string for MAC secret
     *
     * @return Paysera_WalletApi_Container
     */
    public static function create($clientId, $authentication)
    {
        return new self($clientId, $authentication);
    }

    /**
     * Constructs object
     *
     * @param string                                    $clientId
     * @param string|Paysera_WalletApi_Http_ClientCertificate $authentication string for MAC secret
     */
    public function __construct($clientId, $authentication)
    {
        $this->clientId = $clientId;
        if ($authentication instanceof Paysera_WalletApi_Http_ClientCertificate) {
            $this->clientCertificate = $authentication;
        } else {
            $this->macSecret = (string) $authentication;
        }
        $this->apiEndpoint = 'https://wallet.paysera.com';
        $this->authEndpoint = 'https://www.paysera.com/frontend';
    }


    /**
     * Sets MAC secret value
     *
     * @param string $macSecret
     *
     * @return self for fluent interface
     */
    public function setMacSecret($macSecret)
    {
        $this->macSecret = $macSecret;
        return $this;
    }

    /**
     * Sets MAC ID
     *
     * @param string $clientId
     *
     * @return self for fluent interface
     */
    public function setClientId($clientId)
    {
        $this->clientId = $clientId;
        return $this;
    }

    /**
     * Sets API endpoint. In normal cases it should not be changed
     *
     * @param string $apiEndpoint
     *
     * @return self for fluent interface
     */
    public function setApiEndpoint($apiEndpoint)
    {
        $this->apiEndpoint = $apiEndpoint;
        return $this;
    }

    /**
     * Sets authorization endpoint. In normal cases it should not be changed
     *
     * @param string $authEndpoint
     *
     * @return self for fluent interface
     */
    public function setAuthEndpoint($authEndpoint)
    {
        $this->authEndpoint = $authEndpoint;
        return $this;
    }

    /**
     * For customizing service. In normal cases this should not be called
     *
     * @param Paysera_WalletApi_RequestMaker $requestMaker
     *
     * @return self for fluent interface
     */
    public function setRequestMaker(Paysera_WalletApi_RequestMaker $requestMaker)
    {
        $this->requestMaker = $requestMaker;
        return $this;
    }

    /**
     * For customizing service. In normal cases this should not be called
     *
     * @param Paysera_WalletApi_RequestMaker $oAuthRequestMaker
     *
     * @return self for fluent interface
     */
    public function setOAuthRequestMaker(Paysera_WalletApi_RequestMaker $oAuthRequestMaker)
    {
        $this->oAuthRequestMaker = $oAuthRequestMaker;
        return $this;
    }

    /**
     * For customizing service. In normal cases this should not be called
     *
     * @param Paysera_WalletApi_CallbackHandler $callbackHandler
     *
     * @return self for fluent interface
     */
    public function setCallbackHandler(Paysera_WalletApi_CallbackHandler $callbackHandler)
    {
        $this->callbackHandler = $callbackHandler;
        return $this;
    }

    /**
     * For customizing service. In normal cases this should not be called
     *
     * @param Paysera_WalletApi_Mapper $mapper
     *
     * @return self for fluent interface
     */
    public function setMapper(Paysera_WalletApi_Mapper $mapper)
    {
        $this->mapper = $mapper;
        return $this;
    }

    /**
     * For customizing service. In normal cases this should not be called
     *
     * @param Paysera_WalletApi_OAuth_Consumer $oAuthConsumer
     *
     * @return self for fluent interface
     */
    public function setOAuthConsumer(Paysera_WalletApi_OAuth_Consumer $oAuthConsumer)
    {
        $this->oAuthConsumer = $oAuthConsumer;
        return $this;
    }

    /**
     * For customizing service. In normal cases this should not be called
     *
     * @param Paysera_WalletApi_WebClient_Interface $webClient
     *
     * @return self for fluent interface
     */
    public function setWebClient(Paysera_WalletApi_WebClient_Interface $webClient)
    {
        $this->webClient = $webClient;
        return $this;
    }

    /**
     * For customizing service. In normal cases this should not be called
     *
     * @param Paysera_WalletApi_Auth_SignerInterface $authSigner
     *
     * @return self for fluent interface
     */
    public function setAuthSigner(Paysera_WalletApi_Auth_SignerInterface $authSigner)
    {
        $this->authSigner = $authSigner;
        return $this;
    }


    /*
     * Getters - creates and returns services and parameters
     */

    /**
     * Creates API object, related to current wallet (wallet of the user which granted access, giving the access token)
     *
     * @param Paysera_WalletApi                       $WalletApi
     * @param Paysera_WalletApi_Entity_MacAccessToken $accessToken
     *
     * @return Paysera_WalletApi_WalletRelatedApi
     */
    public function createApiForToken(Paysera_WalletApi $WalletApi, Paysera_WalletApi_Entity_MacAccessToken $accessToken)
    {
        $authMac = new Paysera_WalletApi_Auth_Mac($accessToken->getMacId(), $accessToken->getMacKey());
        $requestMaker = new Paysera_WalletApi_RequestMaker($this->getApiPath(), $authMac, $this->getWebClient());
        return new Paysera_WalletApi_WalletRelatedApi($WalletApi, $requestMaker, $this->getMapper(), $accessToken);
    }

    /**
     * Gets service. Creates with default configuration if not yet available
     *
     * @return Paysera_WalletApi_CallbackHandler
     */
    public function getCallbackHandler()
    {
        if ($this->callbackHandler === null) {
            $this->callbackHandler = new Paysera_WalletApi_CallbackHandler(
                new Paysera_WalletApi_Auth_CallbackSignChecker($this->getPublicKeyUri(), $this->getWebClient()),
                $this->getMapper()
            );
        }
        return $this->callbackHandler;
    }

    /**
     * Gets service. Creates with default configuration if not yet available
     *
     * @return Paysera_WalletApi_RequestMaker
     */
    public function getOAuthRequestMaker()
    {
        if ($this->oAuthRequestMaker === null) {
            $this->oAuthRequestMaker = new Paysera_WalletApi_RequestMaker(
                $this->getOAuthApiPath(),
                $this->getAuthSigner(),
                $this->getWebClient()
            );
        }
        return $this->oAuthRequestMaker;
    }

    /**
     * Gets service. Creates with default configuration if not yet available
     *
     * @return Paysera_WalletApi_Mapper
     */
    public function getMapper()
    {
        if ($this->mapper === null) {
            $this->mapper = new Paysera_WalletApi_Mapper();
        }
        return $this->mapper;
    }

    /**
     * Gets service. Creates with default configuration if not yet available
     *
     * @return Paysera_WalletApi_OAuth_Consumer
     */
    public function getOAuthConsumer()
    {
        if ($this->oAuthConsumer === null) {
            $this->oAuthConsumer = new Paysera_WalletApi_OAuth_Consumer(
                $this->getClientId(),
                $this->getAuthPath(),
                new Paysera_WalletApi_State_SessionStatePersister('Paysera_WalletApi_' . $this->getClientId()),
                new Paysera_WalletApi_Util_RequestInfo($_SERVER)
            );
        }
        return $this->oAuthConsumer;
    }

    /**
     * Gets service. Creates with default configuration if not yet available
     *
     * @return Paysera_WalletApi_RequestMaker
     */
    public function getRequestMaker()
    {
        if ($this->requestMaker === null) {
            $this->requestMaker = new Paysera_WalletApi_RequestMaker(
                $this->getApiPath(),
                $this->getAuthSigner(),
                $this->getWebClient()
            );
        }
        return $this->requestMaker;
    }

    /**
     * Gets base path for transaction confirmation by page. Transaction key is needed at the end or this URI
     *
     * @param string $lang
     *
     * @return string
     */
    public function getConfirmPath($lang)
    {
        switch ($lang) {
            case 'lt':
            case 'ru':
                return $this->authEndpoint . '/' . $lang . '/wallet/confirm/';
                break;

            default:
                return $this->authEndpoint . '/en/wallet/confirm/';
        }
    }

    /**
     * Gets service. Creates with default configuration if not yet available
     *
     * @return Paysera_WalletApi_Auth_SignerInterface
     */
    protected function getAuthSigner()
    {
        if ($this->authSigner === null) {
            if ($this->getMacSecret() !== null) {
                $this->authSigner = new Paysera_WalletApi_Auth_Mac($this->getClientId(), $this->getMacSecret());
            } else {
                $this->authSigner = new Paysera_WalletApi_Auth_ClientCertificate($this->getClientCertificate());
            }
        }
        return $this->authSigner;
    }

    /**
     * Gets service. Creates with default configuration if not yet available
     *
     * @return Paysera_WalletApi_WebClient_Interface
     */
    protected function getWebClient()
    {
        if ($this->webClient === null) {
            $this->webClient = new Paysera_WalletApi_WebClient_Curl();
        }
        return $this->webClient;
    }

    /**
     * Gets MAC secret
     *
     * @return string
     */
    protected function getMacSecret()
    {
        return $this->macSecret;
    }

    /**
     * Gets clientCertificate
     *
     * @return Paysera_WalletApi_Http_ClientCertificate
     */
    protected function getClientCertificate()
    {
        return $this->clientCertificate;
    }

    /**
     * Gets client id (mac_id or serial number of client certificate). This works as client_id in OAuth requests
     *
     * @return string
     */
    protected function getClientId()
    {
        return $this->clientId;
    }

    /**
     * Gets base path for main API
     *
     * @return string
     */
    protected function getApiPath()
    {
        return $this->apiEndpoint . '/rest/v1/';
    }

    /**
     * Gets base path for OAuth-related API
     *
     * @return string
     */
    protected function getOAuthApiPath()
    {
        return $this->apiEndpoint . '/oauth/v1/';
    }

    /**
     * Gets base path for authentication pages
     *
     * @return string
     */
    protected function getAuthPath()
    {
        return $this->authEndpoint . '/oauth';
    }

    /**
     * Gets URI of public key. Used for verifying callbacks
     *
     * @return string
     */
    protected function getPublicKeyUri()
    {
        return $this->apiEndpoint . '/publickey';
    }

}