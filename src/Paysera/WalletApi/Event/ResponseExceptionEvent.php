<?php


/**
 * ResponseExceptionEvent
 *
 * @author Marius Balčytis <m.balcytis@evp.lt>
 */
class Paysera_WalletApi_Event_ResponseExceptionEvent extends Paysera_WalletApi_EventDispatcher_Event
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
     * @var mixed
     */
    protected $result;

    /**
     * @var bool
     */
    protected $repeatRequest = false;

    /**
     * @var array
     */
    protected $options;

    /**
     * @param Paysera_WalletApi_Exception_ResponseException $exception
     * @param Paysera_WalletApi_Http_Response               $response
     * @param array                                         $options
     */
    public function __construct(
        Paysera_WalletApi_Exception_ResponseException $exception,
        Paysera_WalletApi_Http_Response $response,
        $options
    ) {
        $this->exception = $exception;
        $this->response = $response;
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
     * Gets response
     *
     * @return Paysera_WalletApi_Http_Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Sets result
     *
     * @param mixed $result
     *
     * @return $this
     */
    public function setResult($result)
    {
        $this->result = $result;

        return $this;
    }

    /**
     * Gets result
     *
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * Sets repeatRequest
     *
     * @param boolean $repeatRequest
     *
     * @return $this
     */
    public function setRepeatRequest($repeatRequest)
    {
        $this->repeatRequest = $repeatRequest;

        return $this;
    }

    /**
     * Gets repeatRequest
     *
     * @return boolean
     */
    public function isRepeatRequest()
    {
        return $this->repeatRequest;
    }

    /**
     * Sets options
     *
     * @param array $options
     *
     * @return $this
     */
    public function setOptions($options)
    {
        $this->options = $options;

        return $this;
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