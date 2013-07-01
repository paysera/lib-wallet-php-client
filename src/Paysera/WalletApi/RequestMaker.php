<?php

class Paysera_WalletApi_RequestMaker
{
    /**
     * @var Paysera_WalletApi_WebClient_Interface
     */
    protected $webClient;

    /**
     * @var Paysera_WalletApi_Auth_SignerInterface
     */
    protected $signer;

    /**
     * @var string
     */
    protected $basePath;


    /**
     * Constructs object
     *
     * @param string                           $basePath
     * @param Paysera_WalletApi_Auth_SignerInterface $signer
     * @param Paysera_WalletApi_WebClient_Interface  $webClient
     */
    public function __construct(
        $basePath,
        Paysera_WalletApi_Auth_SignerInterface $signer,
        Paysera_WalletApi_WebClient_Interface $webClient
    ) {
        $this->basePath = $basePath;
        $this->signer = $signer;
        $this->webClient = $webClient;
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
     * @throws Paysera_WalletApi_Exception_ResponseException    if status core of response is not 200
     */
    public function makeRequest($uri, $method = Paysera_WalletApi_Http_Request::METHOD_GET, $content = null)
    {
        $contentType = 'application/x-www-form-urlencoded';
        if ($content !== null) {
            if (is_array($content)) {
                $content = http_build_query($content, null, '&');
            } else {
                $contentType = 'application/json';
            }
        }
        $request = new Paysera_WalletApi_Http_Request(
            $this->basePath . $uri,
            $method,
            $content,
            array('Content-Type' => $contentType)
        );
        $this->signer->signRequest($request);

        $response = $this->webClient->makeRequest($request);

        $content = json_decode($response->getContent(), true);

        if ($content === null && $response->getContent() !== 'null') {
            throw new Paysera_WalletApi_Exception_ResponseException(
                array(
                    'error' => 'internal_server_error',
                    'error_description' => 'Invalid response from server: "' . $response->getContent() . '"',
                ),
                $response->getStatusCode() . ' ' . $response->getStatusCodeMessage()
            );
        } elseif ($response->getStatusCode() === 200) {
            return $content;
        } else {
            throw new Paysera_WalletApi_Exception_ResponseException(
                $content,
                $response->getStatusCode() . ' ' . $response->getStatusCodeMessage()
            );
        }
    }

    /**
     * Sets signer
     *
     * @param Paysera_WalletApi_Auth_SignerInterface $signer
     */
    public function setSigner(Paysera_WalletApi_Auth_SignerInterface $signer)
    {
        $this->signer = $signer;
    }
}