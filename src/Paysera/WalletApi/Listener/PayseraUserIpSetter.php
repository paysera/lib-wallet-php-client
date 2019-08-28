<?php

class Paysera_WalletApi_Listener_PayseraUserIpSetter implements Paysera_WalletApi_EventDispatcher_EventSubscriberInterface
{
    /**
     * @var array
     */
    protected $parameters;

    /**
     * @param array $parameters
     */
    public function __construct($parameters)
    {
        $this->parameters = $parameters;
    }

    public function onBeforeRequest(Paysera_WalletApi_Event_RequestEvent $event)
    {
        if (isset($this->parameters['Paysera-User-Ip'])) {
            $event->getRequest()->getHeaderBag()->setHeader(
                'Paysera-User-Ip',
                $this->parameters['Paysera-User-Ip']
            );
        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            Paysera_WalletApi_Events::BEFORE_REQUEST => array('onBeforeRequest', 100),
        );
    }
}
