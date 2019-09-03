<?php

class Paysera_WalletApi_Listener_AppendHeadersListener implements Paysera_WalletApi_EventDispatcher_EventSubscriberInterface
{
    /**
     * @var array
     */
    protected $headers;

    /**
     * @param array $headers
     */
    public function __construct($headers)
    {
        $this->headers = $headers;
    }

    public function onBeforeRequest(Paysera_WalletApi_Event_RequestEvent $event)
    {
        foreach ($this->headers as $headerName => $headerValue) {
            $event->getRequest()->getHeaderBag()->setHeader($headerName, $headerValue);
        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            Paysera_WalletApi_Events::BEFORE_REQUEST => array('onBeforeRequest', 100),
        );
    }
}
