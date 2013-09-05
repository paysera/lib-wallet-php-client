<?php


/**
 * BaseClient. Used as a base for specific clients
 *
 * @author Marius Balčytis <m.balcytis@evp.lt>
 */
abstract class Paysera_WalletApi_Client_BaseClient implements Paysera_WalletApi_Client_BasicClientInterface
{
    /**
     * @var Paysera_WalletApi_Client_BasicClient
     */
    protected $client;

    /**
     * @var Paysera_WalletApi_Mapper
     */
    protected $mapper;

    /**
     * Constructs object
     *
     * @param Paysera_WalletApi_Client_BasicClient $client
     * @param Paysera_WalletApi_Mapper $mapper
     */
    public function __construct(Paysera_WalletApi_Client_BasicClient $client, Paysera_WalletApi_Mapper $mapper)
    {
        $this->client = $client;
        $this->mapper = $mapper;
    }

    /**
     * Makes specified request.
     * URI in request object can be relative to current context (without endpoint and API path).
     * Content of request is not encoded or otherwise modified by the client
     *
     * @param Paysera_WalletApi_Http_Request $request
     * @param array                          $options
     *
     * @throws Paysera_WalletApi_Exception_ApiException
     * @return mixed|null
     */
    public function makeRequest(Paysera_WalletApi_Http_Request $request, $options = array())
    {
        return $this->client->makeRequest($request, $options);
    }

    /**
     * Makes GET request, uri can be relative to current context (without endpoint and API path)
     *
     * @param string $uri
     * @param array  $options
     *
     * @return mixed|null
     */
    public function get($uri, $options = array())
    {
        return $this->client->get($uri, $options);
    }

    /**
     * Makes DELETE request, uri can be relative to current context (without endpoint and API path)
     *
     * @param string $uri
     * @param array  $options
     *
     * @return mixed|null
     */
    public function delete($uri, $options = array())
    {
        return $this->client->delete($uri, $options);
    }

    /**
     * Makes POST request, uri can be relative to current context (without endpoint and API path)
     * Content is encoded to JSON or some other supported format
     *
     * @param string $uri
     * @param mixed  $content
     * @param array  $options
     *
     * @return mixed|null
     */
    public function post($uri, $content = null, $options = array())
    {
        return $this->client->post($uri, $content, $options);
    }

    /**
     * Makes PUT request, uri can be relative to current context (without endpoint and API path)
     * Content is encoded to JSON or some other supported format
     *
     * @param string $uri
     * @param mixed  $content
     * @param array  $options
     *
     * @return mixed|null
     */
    public function put($uri, $content = null, $options = array())
    {
        return $this->client->put($uri, $content, $options);
    }


} 