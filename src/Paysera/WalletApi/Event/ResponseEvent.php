<?php


/**
 * ResponseEvent
 *
 * @author Marius Balčytis <m.balcytis@evp.lt>
 */
class Paysera_WalletApi_Event_ResponseEvent extends Paysera_WalletApi_EventDispatcher_Event
{

    /**
     * @var Paysera_WalletApi_Http_Response
     */
    protected $response;

    /**
     * @var array
     */
    protected $options;

    /**
     * @param Paysera_WalletApi_Http_Response $response
     * @param array                           $options
     */
    public function __construct(Paysera_WalletApi_Http_Response $response, $options)
    {
        $this->response = $response;
        $this->options = $options;
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