<?php

/**
 * Represents request that was or should be made to some URI
 */
class Paysera_WalletApi_Http_Request
{
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_DELETE = 'DELETE';
    const METHOD_PUT = 'PUT';

    const CONTENT_TYPE_JSON = 'application/json';
    const CONTENT_TYPE_URLENCODED = 'application/x-www-form-urlencoded';

    /**
     * @var Paysera_WalletApi_Http_HeaderBag
     */
    protected $headers;

    /**
     * @var mixed
     */
    protected $content;

    /**
     * @var string
     */
    protected $method;

    /**
     * @var string
     */
    protected $uri;

    /**
     * @var Paysera_WalletApi_Http_ClientCertificate
     */
    protected $clientCertificate;

    /**
     * Constructs object
     *
     * @param string $fullUri
     * @param string $method
     * @param mixed  $content
     * @param array  $headers
     */
    public function __construct($fullUri, $method = self::METHOD_GET, $content = '', array $headers = array())
    {
        $this->setFullUri($fullUri);
        $this->setContent($content);
        $this->setMethod($method);

        $defaultHeaders = array(
            'User-Agent' => 'Paysera WalletApi PHP library',
        );
        $this->setHeaderBag(new Paysera_WalletApi_Http_HeaderBag($headers + $defaultHeaders));
    }

    /**
     * Sets header. Convenience method - you can get header bag and set headers to it
     *
     * @param string       $headerName
     * @param string|array $headerValues
     * @param boolean      $replace
     *
     * @return self        for fluent interface
     *
     * @see Paysera_WalletApi_Http_HeaderBag::setHeader
     */
    public function setHeader($headerName, $headerValues, $replace = true)
    {
        $this->headers->setHeader($headerName, $headerValues, $replace);
        return $this;
    }

    /**
     * Gets header bag object for manipulating headers
     *
     * @return Paysera_WalletApi_Http_HeaderBag
     */
    public function getHeaderBag()
    {
        return $this->headers;
    }

    /**
     * Sets header bag object
     *
     * @param Paysera_WalletApi_Http_HeaderBag $headerBag
     *
     * @return self        for fluent interface
     */
    public function setHeaderBag(Paysera_WalletApi_Http_HeaderBag $headerBag)
    {
        $this->headers = $headerBag;
        return $this;
    }

    /**
     * Gets formatted headers. Convenience function - you can get header bag and get them from there
     *
     * @return array
     */
    public function getFormattedHeaders()
    {
        return $this->headers->getFormattedHeaders();
    }

    /**
     * Sets request body as a string or array for POST parameters
     *
     * @param mixed $content
     *
     * @return self        for fluent interface
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * Returns request body or POST parameters
     *
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Sets request method. One of Paysera_WalletApi_Http_Request::METHOD_* contents
     *
     * @param string $method
     *
     * @return self        for fluent interface
     */
    public function setMethod($method)
    {
        $this->method = strtoupper($method);
        return $this;
    }

    /**
     * Returns request method
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Sets full request URI - with scheme, host, path and query string
     *
     * @param string $uri
     *
     * @return self        for fluent interface
     */
    public function setFullUri($uri)
    {
        $this->uri = $uri;
        return $this;
    }

    /**
     * Returns full request URI
     *
     * @return string
     */
    public function getFullUri()
    {
        return $this->uri;
    }

    /**
     * Returns path and query string parts of full request URI
     *
     * @return string
     */
    public function getUri()
    {
        return parse_url($this->uri, PHP_URL_PATH) . strstr($this->uri, '?');
    }

    /**
     * Gets host from full request URI
     *
     * @return string
     */
    public function getHost()
    {
        return parse_url($this->uri, PHP_URL_HOST);
    }

    /**
     * Gets port from full request URI. https protocol defaults to 433, all other - to 80
     *
     * @return integer
     */
    public function getPort()
    {
        $port = parse_url($this->uri, PHP_URL_PORT);
        if ($port === null) {
            $port = parse_url($this->uri, PHP_URL_SCHEME) === 'https' ? 443 : 80;
        }
        return $port;
    }

    /**
     * Sets clientCertificate
     *
     * @param Paysera_WalletApi_Http_ClientCertificate $clientCertificate
     *
     * @return $this
     */
    public function setClientCertificate($clientCertificate)
    {
        $this->clientCertificate = $clientCertificate;

        return $this;
    }

    /**
     * Gets clientCertificate
     *
     * @return Paysera_WalletApi_Http_ClientCertificate
     */
    public function getClientCertificate()
    {
        return $this->clientCertificate;
    }
}