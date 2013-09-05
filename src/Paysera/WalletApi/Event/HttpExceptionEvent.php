<?php


/**
 * HttpExceptionEvent
 *
 * @author Marius Balčytis <m.balcytis@evp.lt>
 */
class Paysera_WalletApi_Event_HttpExceptionEvent extends Paysera_WalletApi_EventDispatcher_Event
{
    /**
     * @var Paysera_WalletApi_Exception_ResponseException
     */
    protected $exception;

    /**
     * @var Paysera_WalletApi_Http_Response
     */
    protected $response;

    /**
     * @var Paysera_WalletApi_Http_Request
     */
    protected $request;

    /**
     * @var array
     */
    protected $options;

    /**
     * @param Paysera_WalletApi_Exception_HttpException $exception
     * @param Paysera_WalletApi_Http_Request            $request
     * @param array                                     $options
     */
    public function __construct(
        Paysera_WalletApi_Exception_HttpException $exception,
        Paysera_WalletApi_Http_Request $request,
        $options
    ) {
        $this->exception = $exception;
        $this->request = $request;
        $this->options = $options;
    }

    /**
     * Sets exception
     *
     * @param Paysera_WalletApi_Exception_ResponseException $exception
     *
     * @return $this
     */
    public function setException($exception)
    {
        $this->exception = $exception;

        return $this;
    }

    /**
     * Gets exception
     *
     * @return Paysera_WalletApi_Exception_ResponseException
     */
    public function getException()
    {
        return $this->exception;
    }

    /**
     * Sets response
     *
     * @param Paysera_WalletApi_Http_Response $response
     *
     * @return $this
     */
    public function setResponse($response)
    {
        $this->response = $response;

        return $this;
    }

    /**
     * Gets response
     *
     * @return Paysera_WalletApi_Http_Response
     */
    public function getResponse()
    {
        return $this->response;
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

    /**
     * Gets options
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

} 