<?php


/**
 * EndpointSetter
 *
 * @author Marius Balčytis <m.balcytis@evp.lt>
 */
class Paysera_WalletApi_Listener_EndpointSetter implements Paysera_WalletApi_EventDispatcher_EventSubscriberInterface
{
    /**
     * @var string
     */
    protected $endpoint;

    /**
     * @param string $endpoint
     */
    public function __construct($endpoint)
    {
        $this->endpoint = $endpoint;
    }

    /**
     * @param Paysera_WalletApi_Event_RequestEvent $event
     */
    public function onBeforeRequest(Paysera_WalletApi_Event_RequestEvent $event)
    {
        $uri = $event->getRequest()->getFullUri();
        if ($uri !== null && substr($uri, 0, 7) !== 'http://' && substr($uri, 0, 8) !== 'https://') {
            $event->getRequest()->setFullUri($this->endpoint . $uri);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return array(
            Paysera_WalletApi_Events::BEFORE_REQUEST => array('onBeforeRequest', 100),
        );
    }

}