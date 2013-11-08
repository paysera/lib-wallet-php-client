<?php


/**
 * WalletApi
 *
 * @author Marius Balčytis <m.balcytis@evp.lt>
 */
class Paysera_WalletApi
{
    /**
     * @var string
     */
    protected $clientId;

    /**
     * @var Paysera_WalletApi_Auth_SignerInterface
     */
    protected $signer;

    /**
     * @var Paysera_WalletApi_Util_Router
     */
    protected $router;

    /**
     * @var Paysera_WalletApi_Container
     */
    protected $container;

    /**
     * Constructor for entry point to client library.
     *
     * Credentials are required.
     * Router can be configured and passed if some other endpoints are used, different from default ones.
     * Container can be passed if some services are overriden with custom ones or if some listeners are bound to
     * event dispatcher.
     *
     * @param string                                          $clientId       client ID
     * @param string|Paysera_WalletApi_Http_ClientCertificate $authentication mac secret or certificate information
     * @param Paysera_WalletApi_Util_Router                   $router         default is used if not passed
     * @param Paysera_WalletApi_Container                     $container      default is used if not passed
     */
    public function __construct(
        $clientId,
        $authentication,
        Paysera_WalletApi_Util_Router $router = null,
        Paysera_WalletApi_Container $container = null
    ) {
        if ($router === null) {
            $router = new Paysera_WalletApi_Util_Router();
        }
        if ($container === null) {
            $container = new Paysera_WalletApi_Container();
        }

        $this->clientId = $clientId;
        $this->signer = $container->createAuthSigner($clientId, $authentication);
        $this->router = $router;
        $this->container = $container;
    }

    /**
     * Creates wallet client, responsible for creating transactions, getting user information etc.
     *
     * @param array $parameters project_id, location_id or some other parameters, if needed
     *
     * @return Paysera_WalletApi_Client_WalletClient
     */
    public function walletClient(array $parameters = array())
    {
        return $this->container->createWalletClient(
            $this->basicClient($this->router->getWalletApiEndpoint(), null, $parameters)
        );
    }

    /**
     * Creates wallet client, related to specific access token. Requests are signed with given access token.
     * Some specific methods are also available, which are only available when using access token.
     *
     * @param Paysera_WalletApi_Entity_MacAccessToken $token
     * @param array                                   $parameters project_id, location_id or some other parameters
     *
     * @return Paysera_WalletApi_Client_TokenRelatedWalletClient
     */
    public function walletClientWithToken(
        Paysera_WalletApi_Entity_MacAccessToken $token,
        array $parameters = array()
    ) {
        $dispatcher = $this->dispatcher($this->router->getWalletApiEndpoint(), $token, $parameters);
        $client = $this->container->createWalletClientWithToken($this->container->createBasicClient($dispatcher));
        $client->setCurrentAccessToken($token);
        $dispatcher->addSubscriber(new Paysera_WalletApi_Listener_AccessTokenSetter($client));
        return $client;
    }

    /**
     * Creates OAuth consumer, responsible for exchanging code to access token, getting code from parameters, getting
     * redirect uri to OAuth endpoint etc. Basically used with "code" grant type
     *
     * @return Paysera_WalletApi_OAuth_Consumer
     */
    public function oauthConsumer()
    {
        return $this->container->createOAuthConsumer(
            $this->clientId,
            $this->oauthClient(),
            $this->router
        );
    }

    /**
     * Creates callback handler, used when handling callbacks from Wallet API server
     *
     * @param Paysera_WalletApi_EventDispatcher_EventDispatcher $dispatcher
     *
     * @return Paysera_WalletApi_Callback_Handler
     */
    public function callbackHandler(Paysera_WalletApi_EventDispatcher_EventDispatcher $dispatcher)
    {
        return $this->container->createCallbackHandler($dispatcher, $this->router->getPublicKeyUri());
    }

    /**
     * Creates OAuth client, responsible for getting access token.
     * If "code" grant type is used, usually OAuth consumer is enough for all purposes, client itself is not needed
     *
     * @return Paysera_WalletApi_Client_OAuthClient
     */
    public function oauthClient()
    {
        return $this->container->createOAuthClient(
            $this->basicClient($this->router->getOAuthApiEndpoint())
        );
    }

    /**
     * Creates basic client for making custom requests.
     * Returned client is responsible for signing the requests etc., but no specific methods are defined
     * and no mapping done.
     *
     * This could be used for functionality that is not yet implemented in specific client classes.
     *
     * @param string                                  $basePath
     * @param Paysera_WalletApi_Entity_MacAccessToken $token
     * @param array                                   $parameters project_id, location_id or some other parameters
     *
     * @return Paysera_WalletApi_Client_BasicClient
     */
    public function basicClient(
        $basePath = null,
        Paysera_WalletApi_Entity_MacAccessToken $token = null,
        array $parameters = array()
    ) {
        return $this->container->createBasicClient($this->dispatcher($basePath, $token, $parameters));
    }

    /**
     * Returns router, related to API. Can be used to get transaction confirmation URI
     *
     * @return Paysera_WalletApi_Util_Router
     */
    public function router()
    {
        return $this->router;
    }


    protected function dispatcher(
        $basePath = null,
        Paysera_WalletApi_Entity_MacAccessToken $token = null,
        array $parameters = array()
    ) {
        $basePath = $this->router->getApiEndpoint($basePath);
        if ($token === null) {
            $requestSigner = $this->container->createRequestSigner($this->signer);
        } else {
            $requestSigner = $this->container->createOAuthRequestSigner($this->oauthClient(), $token);
        }
        return $this->container->createDispatcherForClient($basePath, $requestSigner, $parameters);
    }
}