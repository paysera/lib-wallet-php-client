<?php

/**
 * Represents web client response from some URI
 */
class Paysera_WalletApi_Http_Response
{
    /**
     * HTTP status code to message map
     *
     * @var array
     */
    static public $httpCodeMessageMap = array(
        100 => "Continue",
        101 => "Switching Protocols",

        200 => "OK",
        201 => "Created",
        202 => "Accepted",
        203 => "Non-Authoritative Information",
        204 => "No Content",
        205 => "Reset Content",
        206 => "Partial Content",

        300 => "Multiple Choices",
        301 => "Moved Permanently",
        302 => "Found",
        303 => "See Other",
        304 => "Not Modified",
        305 => "Use Proxy",
        306 => "(Unused)",
        307 => "Temporary Redirect",

        400 => "Bad Request",
        401 => "Unauthorized",
        402 => "Payment Required",
        403 => "Forbidden",
        404 => "Not Found",
        405 => "Method Not Allowed",
        406 => "Not Acceptable",
        407 => "Proxy Authentication Required",
        408 => "Request Timeout",
        409 => "Conflict",
        410 => "Gone",
        411 => "Length Required",
        412 => "Precondition Failed",
        413 => "Request Entity Too Large",
        414 => "Request-URI Too Long",
        415 => "Unsupported Media Type",
        416 => "Requested Range Not Satisfiable",
        417 => "Expectation Failed",

        500 => "Internal Server Error",
        501 => "Not Implemented",
        502 => "Bad Gateway",
        503 => "Service Unavailable",
        504 => "Gateway Timeout",
        505 => "HTTP Version Not Supported",
    );

    /**
     * @var integer
     */
    protected $statusCode;

    /**
     * @var Paysera_WalletApi_Http_HeaderBag
     */
    protected $headers;

    /**
     * @var string
     */
    protected $content;

    /**
     * @var Paysera_WalletApi_Http_Request
     */
    protected $request;

    /**
     * Constructs object
     *
     * @param integer $statusCode
     * @param array   $headers
     * @param string  $content
     */
    public function __construct($statusCode, array $headers, $content)
    {
        $this->setStatusCode($statusCode);
        $this->setContent($content);
        $this->setHeaderBag(new Paysera_WalletApi_Http_HeaderBag($headers));
    }

    /**
     * Sets status code
     *
     * @param integer $statusCode
     *
     * @return self        for fluent interface
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = intval($statusCode);
        return $this;
    }

    /**
     * Gets status code
     *
     * @return integer
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Gets status message by status code. Not the one returned from the server
     *
     * @return string|null
     */
    public function getStatusCodeMessage()
    {
        return isset(self::$httpCodeMessageMap[$this->statusCode]) ? self::$httpCodeMessageMap[$this->statusCode] : null;
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
     * Gets header bag object
     *
     * @return Paysera_WalletApi_Http_HeaderBag
     */
    public function getHeaderBag()
    {
        return $this->headers;
    }

    /**
     * Gets header value. Convenience method - you can get header bag and get header from there
     *
     * @param string       $header
     * @param string|array $default
     * @param boolean      $first
     *
     * @return string|array
     */
    public function getHeader($header, $default = null, $first = true)
    {
        return $this->headers->getHeader($header, $default, $first);
    }

    /**
     * Sets response body content
     *
     * @param string $content
     *
     * @return self        for fluent interface
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * Gets response body content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Sets request
     *
     * @param Paysera_WalletApi_Http_Request $request
     *
     * @return $this
     */
    public function setRequest($request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * Gets request
     *
     * @return Paysera_WalletApi_Http_Request
     */
    public function getRequest()
    {
        return $this->request;
    }
}