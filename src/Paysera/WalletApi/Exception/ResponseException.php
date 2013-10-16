<?php

/**
 * Thrown if error is received from API backend
 */
class Paysera_WalletApi_Exception_ResponseException extends Paysera_WalletApi_Exception_ApiException
{
    /**
     * @var string
     */
    protected $errorCode;

    /**
     * @var string
     */
    protected $errorDescription;

    /**
     * @var string
     */
    protected $errorUri;

    /**
     * @var integer
     */
    protected $statusCode;

    /**
     * @var string
     */
    protected $statusCodeMessage;

    /**
     * Constructs object
     *
     * @param array     $error
     * @param integer   $statusCode
     * @param string    $statusCodeMessage
     * @param Exception $previous
     */
    public function __construct(array $error, $statusCode, $statusCodeMessage, $previous = null)
    {
        $this->setStatusCode($statusCode);
        $this->setStatusCodeMessage($statusCodeMessage);
        $message = 'Got error response from Wallet API.';
        if (isset($error['error'])) {
            $code = $error['error'];
            $message .= ' Error code: ' . $code . ', status code: ' . $statusCode;
            $this->setErrorCode($error['error']);
        } else {
            $message .= ' No error code, status code: ' . $statusCode;
        }
        if (isset($error['error_description'])) {
            $this->setErrorDescription($error['error_description']);
            $message .= '. ' . $error['error_description'];
        }
        if (isset($error['error_uri'])) {
            $this->setErrorUri($error['error_uri']);
            $message .= '. See more at ' . $error['error_uri'];
        }
        parent::__construct($message, 0, $previous);
    }

    /**
     * Gets errorCode
     *
     * @return string
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }

    /**
     * Sets errorCode
     *
     * @param string $errorCode

     * @return self
     */
    public function setErrorCode($errorCode)
    {
        $this->errorCode = $errorCode;
        return $this;
    }

    /**
     * Gets errorDescription
     *
     * @return string
     */
    public function getErrorDescription()
    {
        return $this->errorDescription;
    }

    /**
     * Sets errorDescription
     *
     * @param string $errorDescription

     * @return self
     */
    public function setErrorDescription($errorDescription)
    {
        $this->errorDescription = $errorDescription;
        return $this;
    }

    /**
     * Gets errorUri
     *
     * @return string
     */
    public function getErrorUri()
    {
        return $this->errorUri;
    }

    /**
     * Sets errorUri
     *
     * @param string $errorUri

     * @return self
     */
    public function setErrorUri($errorUri)
    {
        $this->errorUri = $errorUri;
        return $this;
    }

    /**
     * Gets statusCode
     *
     * @return integer
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Sets statusCode
     *
     * @param integer $statusCode

     * @return self
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    /**
     * Set statusCodeMessage
     *
     * @param string $statusCodeMessage
     *
     * @return Paysera_WalletApi_Exception_ResponseException
     */
    public function setStatusCodeMessage($statusCodeMessage)
    {
        $this->statusCodeMessage = $statusCodeMessage;

        return $this;
    }

    /**
     * Get statusCodeMessage
     *
     * @return string
     */
    public function getStatusCodeMessage()
    {
        return $this->statusCodeMessage;
    }
}