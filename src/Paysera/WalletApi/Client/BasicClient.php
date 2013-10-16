<?php

class Paysera_WalletApi_Client_BasicClient implements Paysera_WalletApi_Client_BasicClientInterface
{
    /**
     * @var Paysera_WalletApi_Http_ClientInterface
     */
    protected $webClient;

    /**
     * @var Paysera_WalletApi_EventDispatcher_EventDispatcher
     */
    protected $eventDispatcher;

    /**
     * Constructs object
     *
     * @param Paysera_WalletApi_Http_ClientInterface            $webClient
     * @param Paysera_WalletApi_EventDispatcher_EventDispatcher $eventDispatcher
     */
    public function __construct(
        Paysera_WalletApi_Http_ClientInterface $webClient,
        Paysera_WalletApi_EventDispatcher_EventDispatcher $eventDispatcher
    ) {
        $this->webClient = $webClient;
        $this->eventDispatcher = $eventDispatcher;
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
    public function makeRequest(
        Paysera_WalletApi_Http_Request $request,
        $options = array()
    ) {
        $originalRequest = clone $request;

        $event = new Paysera_WalletApi_Event_RequestEvent($request, $options);
        $this->eventDispatcher->dispatch(Paysera_WalletApi_Events::BEFORE_REQUEST, $event);
        $options = $event->getOptions();

        try {
            $response = $this->webClient->makeRequest($request);
        } catch (Paysera_WalletApi_Exception_HttpException $exception) {
            $event = new Paysera_WalletApi_Event_HttpExceptionEvent($exception, $request, $options);
            $this->eventDispatcher->dispatch(Paysera_WalletApi_Events::ON_HTTP_EXCEPTION, $event);
            if ($event->getResponse() !== null) {
                $response = $event->getResponse();
            } else {
                throw $event->getException();
            }
        }
        $response->setRequest($request);

        $event = new Paysera_WalletApi_Event_ResponseEvent($response, $options);
        $this->eventDispatcher->dispatch(Paysera_WalletApi_Events::AFTER_RESPONSE, $event);
        $options = $event->getOptions();

        try {
            $responseContent = $response->getContent();
            if ($responseContent === '') {
                throw new Paysera_WalletApi_Exception_ResponseException(
                    array(
                        'error' => 'internal_server_error',
                        'error_description' => 'Empty response from server',
                    ),
                    $response->getStatusCode(),
                    $response->getStatusCodeMessage()
                );
            }

            $result = json_decode($responseContent, true);

            if ($result === null && $response->getContent() !== 'null') {
                throw new Paysera_WalletApi_Exception_ResponseException(
                    array(
                        'error' => 'internal_server_error',
                        'error_description' => 'Invalid response from server: "' . $response->getContent() . '"',
                    ),
                    $response->getStatusCode(),
                    $response->getStatusCodeMessage()
                );
            } elseif ($response->getStatusCode() === 200) {
                return $result;
            } else {
                throw new Paysera_WalletApi_Exception_ResponseException(
                    $result,
                    $response->getStatusCode(),
                    $response->getStatusCodeMessage()
                );
            }
        } catch (Paysera_WalletApi_Exception_ResponseException $exception) {
            $event = new Paysera_WalletApi_Event_ResponseExceptionEvent($exception, $response, $options);
            $this->eventDispatcher->dispatch(Paysera_WalletApi_Events::ON_RESPONSE_EXCEPTION, $event);
            if ($event->getResult() !== null) {
                return $event->getResult();
            } elseif ($event->isRepeatRequest()) {
                return $this->makeRequest($originalRequest, $event->getOptions());
            } else {
                throw $event->getException();
            }
        }
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
        return $this->makeRequest(new Paysera_WalletApi_Http_Request(
            $uri,
            Paysera_WalletApi_Http_Request::METHOD_GET
        ), $options);
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
        return $this->makeRequest(new Paysera_WalletApi_Http_Request(
            $uri,
            Paysera_WalletApi_Http_Request::METHOD_DELETE
        ), $options);
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
    public function post(
        $uri,
        $content = null,
        $options = array()
    ) {
        return $this->makeRequest(new Paysera_WalletApi_Http_Request(
            $uri,
            Paysera_WalletApi_Http_Request::METHOD_POST,
            $content === null ? '' : json_encode($content),
            array('Content-Type' => Paysera_WalletApi_Http_Request::CONTENT_TYPE_JSON)
        ), $options);
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
    public function put(
        $uri,
        $content = null,
        $options = array()
    ) {
        return $this->makeRequest(new Paysera_WalletApi_Http_Request(
            $uri,
            Paysera_WalletApi_Http_Request::METHOD_PUT,
            $content === null ? '' : json_encode($content),
            array('Content-Type' => Paysera_WalletApi_Http_Request::CONTENT_TYPE_JSON)
        ), $options);
    }


}