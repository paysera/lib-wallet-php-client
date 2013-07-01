<?php

/**
 * Thrown on OAuth error
 */
class Paysera_WalletApi_Exception_OAuthException extends Paysera_WalletApi_Exception_ApiException
{
    /**
     * @var string
     */
    protected $errorCode;

    /**
     * Constructs object
     *
     * @param string     $message
     * @param string     $errorCode
     * @param Exception $exception
     */
    public function __construct($message, $errorCode, $exception = null)
    {
        $this->errorCode = $errorCode;
        parent::__construct($message, 0, $exception);
    }

    /**
     * Gets error code
     *
     * @return string
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }

}