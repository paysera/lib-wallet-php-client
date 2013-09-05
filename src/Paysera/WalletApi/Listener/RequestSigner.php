<?php


/**
 * RequestSigner
 *
 * @author Marius Balčytis <m.balcytis@evp.lt>
 */
class Paysera_WalletApi_Listener_RequestSigner implements Paysera_WalletApi_EventDispatcher_EventSubscriberInterface
{
    /**
     * @var Paysera_WalletApi_Auth_SignerInterface
     */
    protected $signer;

    /**
     * @param Paysera_WalletApi_Auth_SignerInterface $signer
     */
    public function __construct(Paysera_WalletApi_Auth_SignerInterface $signer)
    {
        $this->signer = $signer;
    }

    /**
     * @param Paysera_WalletApi_Event_RequestEvent $event
     */
    public function onBeforeRequest(Paysera_WalletApi_Event_RequestEvent $event)
    {
        $options = $event->getOptions();
        $parameters = isset($options['parameters']) ? $options['parameters'] : array();
        $this->signer->signRequest($event->getRequest(), $parameters);
    }

    public static function getSubscribedEvents()
    {
        return array(
            Paysera_WalletApi_Events::BEFORE_REQUEST => array('onBeforeRequest', -100),
        );
    }

}