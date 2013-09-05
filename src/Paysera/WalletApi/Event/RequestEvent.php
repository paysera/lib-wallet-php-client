<?php


/**
 * RequestEvent
 *
 * @author Marius Balčytis <m.balcytis@evp.lt>
 */
class Paysera_WalletApi_Event_RequestEvent extends Paysera_WalletApi_EventDispatcher_Event
{
    /**
     * @var Paysera_WalletApi_Http_Request
     */
    protected $request;

    /**
     * @var array
     */
    protected $options;

    /**
     * @param Paysera_WalletApi_Http_Request $request
     * @param array                          $options
     */
    public function __construct(Paysera_WalletApi_Http_Request $request, $options)
    {
        $this->request = $request;
        $this->options = $options;
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

} 