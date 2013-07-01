<?php

/**
 * Encapsulates all headers in request or response
 */
class Paysera_WalletApi_Http_HeaderBag
{
    /**
     * @var array
     */
    protected $headers;

    /**
     * Constructs object
     *
     * @param array $headers
     */
    public function __construct(array $headers = array())
    {
        $this->setHeaders($headers);
    }

    /**
     * Sets provided headers. All headers that was set earlier are lost
     * Headers can be:
     *     * list of full header values ("Header-name: header value")
     *     * associative array in form "header-name" => "header value"
     *     * associative array in form "header-name" => array("header1 value", "header2 value")
     *
     * @param array $headers
     *
     * @return self        for fluent interface
     */
    public function setHeaders(array $headers)
    {
        $this->headers = array();
        foreach ($headers as $headerName => $headerValue) {
            if (is_numeric($headerName) && is_string($headerValue) && strpos($headerValue, ': ') !== false) {
                list($headerName, $headerValue) = explode(': ', $headerValue, 2);
            }
            $this->setHeader($headerName, $headerValue, false);
        }
        return $this;
    }

    /**
     * Sets header value(s)
     *
     * @param string       $headerName
     * @param string|array $headerValues
     * @param boolean      $replace       true means that old one(s) with the same name will be deleted
     *
     * @return self        for fluent interface
     */
    public function setHeader($headerName, $headerValues, $replace = true)
    {
        $headerValues = (array) $headerValues;
        $headerName = $this->normalizeHeaderName($headerName);

        if ($replace || !isset($this->headers[$headerName])) {
            $this->headers[$headerName] = $headerValues;
        } else {
            $this->headers[$headerName] = array_merge($this->headers[$headerName], $headerValues);
        }

        return $this;
    }

    /**
     * Gets header value or all header values, depending on the parameter $first
     *
     * @param string       $header
     * @param string|array $default    value to be returned if header was not found
     * @param boolean      $first      if set to true, string will be returned (first header); else - an array
     *
     * @return string|array
     */
    public function getHeader($header, $default = null, $first = true)
    {
        $header = $this->normalizeHeaderName($header);
        if (isset($this->headers[$header])) {
            if ($first) {
                return reset($this->headers[$header]);
            } else {
                return $this->headers[$header];
            }
        } else {
            return $default;
        }
    }

    /**
     * Gets formatted headers
     *
     * @return array        list of headers in format "Header-name: header value"
     */
    public function getFormattedHeaders()
    {
        $headers = array();
        foreach ($this->headers as $name => $values) {
            foreach ($values as $value) {
                $headers[] = $name . ': ' . $value;
            }
        }
        return $headers;
    }

    /**
     * Normalizes header name
     *
     * @param string $header
     *
     * @return string
     */
    protected function normalizeHeaderName($header)
    {
        $header = strtr(strtolower($header), '_', '-');
        return preg_replace_callback('/\-(.)/', array($this, 'headerNormalizerCallback'), ucfirst($header));
    }

    /**
     * Callback function for preg_replace_callback when normalizing header name
     *
     * @param array $match
     *
     * @return string
     */
    private function headerNormalizerCallback($match)
    {
        return '-' . strtoupper($match[1]);
    }
}