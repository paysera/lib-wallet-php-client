<?php

/**
 * Service and parameter container class.
 * Only creates custom services, used in this API. Contains default creation login inside.
 * Creates services only when needed, not on initialization
 */
class Paysera_WalletApi_Container
{
    /**
     * @var Paysera_WalletApi_Mapper
     */
    protected $mapper;

    /**
     * @var Paysera_WalletApi_Http_ClientInterface
     */
    protected $webClient;

    /**
     * @var Paysera_WalletApi_EventDispatcher_EventDispatcher
     */
    protected $eventDispatcher;

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
     * @param Paysera_WalletApi_Http_ClientInterface $webClient
     *
     * @return self for fluent interface
     */
    public function setWebClient(Paysera_WalletApi_Http_ClientInterface $webClient)
    {
        $this->webClient = $webClient;
        return $this;
    }

    /**
     * Sets eventDispatcher
     *
     * @param Paysera_WalletApi_EventDispatcher_EventDispatcher $eventDispatcher
     *
     * @return $this
     */
    public function setEventDispatcher($eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
        return $this;
    }

    /**
     * @param string                                                     $basePath
     * @param Paysera_WalletApi_EventDispatcher_EventSubscriberInterface $requestSigner
     * @param array                                                      $parameters
     *
     * @return Paysera_WalletApi_EventDispatcher_EventDispatcher
     */
    public function createDispatcherForClient(
        $basePath,
        Paysera_WalletApi_EventDispatcher_EventSubscriberInterface $requestSigner,
        array $parameters = array()
    ) {
        $dispatcher = new Paysera_WalletApi_EventDispatcher_EventDispatcher();
        $dispatcher->mergeDispatcher($this->getEventDispatcher());
        $dispatcher->addSubscriber(new Paysera_WalletApi_Listener_EndpointSetter($basePath));

        if (count($parameters) > 0) {
            $dispatcher->addSubscriber(new Paysera_WalletApi_Listener_ParameterSetter($parameters));
        }

        $dispatcher->addSubscriber($requestSigner);
        return $dispatcher;
    }

    /**
     * @param Paysera_WalletApi_EventDispatcher_EventDispatcher $dispatcher
     *
     * @return Paysera_WalletApi_Client_BasicClient
     */
    public function createBasicClient(Paysera_WalletApi_EventDispatcher_EventDispatcher $dispatcher)
    {
        return new Paysera_WalletApi_Client_BasicClient($this->getWebClient(), $dispatcher);
    }

    /**
     * @param Paysera_WalletApi_Client_BasicClient $basicClient
     *
     * @return Paysera_WalletApi_Client_OAuthClient
     */
    public function createOAuthClient(Paysera_WalletApi_Client_BasicClient $basicClient)
    {
        return new Paysera_WalletApi_Client_OAuthClient($basicClient, $this->getMapper());
    }

    /**
     * @param Paysera_WalletApi_Client_BasicClient $basicClient
     *
     * @return Paysera_WalletApi_Client_WalletClient
     */
    public function createWalletClient(Paysera_WalletApi_Client_BasicClient $basicClient)
    {
        return new Paysera_WalletApi_Client_WalletClient($basicClient, $this->getMapper());
    }

    /**
     * @param Paysera_WalletApi_Client_BasicClient $basicClient
     *
     * @return Paysera_WalletApi_Client_TokenRelatedWalletClient
     */
    public function createWalletClientWithToken(Paysera_WalletApi_Client_BasicClient $basicClient)
    {
        return new Paysera_WalletApi_Client_TokenRelatedWalletClient($basicClient, $this->getMapper());
    }

    /**
     * @param Paysera_WalletApi_Auth_SignerInterface $signer
     *
     * @return Paysera_WalletApi_Listener_RequestSigner
     */
    public function createRequestSigner(Paysera_WalletApi_Auth_SignerInterface $signer)
    {
        return new Paysera_WalletApi_Listener_RequestSigner($signer);
    }

    /**
     * @param Paysera_WalletApi_Client_OAuthClient          $client
     * @param Paysera_WalletApi_Entity_MacAccessToken $token
     *
     * @return Paysera_WalletApi_Listener_OAuthRequestSigner
     */
    public function createOAuthRequestSigner(
        Paysera_WalletApi_Client_OAuthClient $client,
        Paysera_WalletApi_Entity_MacAccessToken $token
    ) {
        return new Paysera_WalletApi_Listener_OAuthRequestSigner($client, $token);
    }

    /**
     * Creates OAuth consumer service
     *
     * @param string                               $clientId
     * @param Paysera_WalletApi_Client_OAuthClient $oauthClient
     * @param Paysera_WalletApi_Util_Router        $router
     *
     * @return Paysera_WalletApi_OAuth_Consumer
     */
    public function createOAuthConsumer($clientId, Paysera_WalletApi_Client_OAuthClient $oauthClient, Paysera_WalletApi_Util_Router $router)
    {
        return new Paysera_WalletApi_OAuth_Consumer(
            $clientId,
            $oauthClient,
            $router,
            new Paysera_WalletApi_State_SessionStatePersister('Paysera_WalletApi_' . $clientId),
            new Paysera_WalletApi_Util_RequestInfo($_SERVER)
        );
    }

    /**
     * Gets service. Creates with default configuration if not yet available
     *
     * @param string                                          $clientId
     * @param string|Paysera_WalletApi_Http_ClientCertificate $authentication string for MAC secret
     *
     * @return Paysera_WalletApi_Auth_SignerInterface
     */
    public function createAuthSigner($clientId, $authentication)
    {
        if ($authentication instanceof Paysera_WalletApi_Http_ClientCertificate) {
            return new Paysera_WalletApi_Auth_ClientCertificate($authentication);
        } else {
            return new Paysera_WalletApi_Auth_Mac($clientId, $authentication);
        }
    }

    /**
     * Gets service. Creates with default configuration if not yet available
     *
     * @param Paysera_WalletApi_EventDispatcher_EventDispatcher $dispatcher
     * @param string                                            $publicKeyUri
     *
     * @return Paysera_WalletApi_Callback_Handler
     */
    public function createCallbackHandler(Paysera_WalletApi_EventDispatcher_EventDispatcher $dispatcher, $publicKeyUri)
    {
        return new Paysera_WalletApi_Callback_Handler(
            $dispatcher,
            new Paysera_WalletApi_Callback_SignChecker($publicKeyUri, $this->getWebClient()),
            $this->getMapper()
        );
    }

    /**
     * Gets service. Creates with default configuration if not yet available
     *
     * @return Paysera_WalletApi_EventDispatcher_EventDispatcher
     */
    public function getEventDispatcher()
    {
        if ($this->eventDispatcher === null) {
            $this->eventDispatcher = new Paysera_WalletApi_EventDispatcher_EventDispatcher();
        }
        return $this->eventDispatcher;
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
     * @return Paysera_WalletApi_Http_ClientInterface
     */
    protected function getWebClient()
    {
        if ($this->webClient === null) {
            $this->webClient = new Paysera_WalletApi_Http_CurlClient();
        }
        return $this->webClient;
    }

}