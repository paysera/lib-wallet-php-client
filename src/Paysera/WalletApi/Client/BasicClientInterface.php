<?php

interface Paysera_WalletApi_Client_BasicClientInterface
{
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
    public function makeRequest(Paysera_WalletApi_Http_Request $request, $options = array());

    /**
     * Makes GET request, uri can be relative to current context (without endpoint and API path)
     *
     * @param string $uri
     * @param array  $options
     *
     * @return mixed|null
     */
    public function get($uri, $options = array());

    /**
     * Makes DELETE request, uri can be relative to current context (without endpoint and API path)
     *
     * @param string $uri
     * @param array  $options
     *
     * @return mixed|null
     */
    public function delete($uri, $options = array());

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
    public function post($uri, $content = null, $options = array());

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
    public function put($uri, $content = null, $options = array());

}